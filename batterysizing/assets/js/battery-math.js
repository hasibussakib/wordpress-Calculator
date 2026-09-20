/**
 * Shared battery formulas — port of src/lib/batteryMath.ts
 */
(function (root) {
  'use strict';

  function n(v, fallback) {
    var x = parseFloat(v);
    return isFinite(x) ? x : (fallback || 0);
  }

  function clamp(v, min, max) {
    return Math.min(max, Math.max(min, v));
  }

  function hoursToHm(hours) {
    if (!isFinite(hours) || hours < 0) return '—';
    if (hours === 0) return '0m';
    if (hours > 1000) return Math.round(hours) + 'h';
    var h = Math.floor(hours);
    var m = Math.round((hours - h) * 60);
    if (m === 60) { h += 1; m = 0; }
    if (h <= 0) return m + 'm';
    if (m === 0) return h + 'h';
    return h + 'h ' + String(m).padStart(2, '0') + 'm';
  }

  function fmt(v, digits) {
    if (!isFinite(v)) return '—';
    var d = digits == null ? (Math.abs(v) >= 100 ? 0 : Math.abs(v) >= 10 ? 1 : 2) : digits;
    var n = Number(v).toFixed(d);
    return n.replace(/\.0+$/, '').replace(/(\.\d*?)0+$/, '$1');
  }

  function fmtWh(wh) {
    if (!isFinite(wh)) return '—';
    if (Math.abs(wh) >= 10000) return fmt(wh / 1000, 2) + ' kWh';
    return fmt(wh, 0) + ' Wh';
  }

  function usableWh(voltage, ah, dodPct) {
    return n(voltage) * n(ah) * (n(dodPct, 100) / 100);
  }

  function runtimeHours(voltage, ah, loadW, dodPct, effPct) {
    var usable = usableWh(voltage, ah, dodPct) * (n(effPct, 100) / 100);
    var load = n(loadW);
    return load > 0 ? usable / load : 0;
  }

  function requiredAh(loadW, hours, voltage, dodPct, effPct) {
    var denom = n(voltage) * (n(dodPct, 100) / 100) * (n(effPct, 100) / 100);
    return denom > 0 ? (n(loadW) * n(hours)) / denom : 0;
  }

  function requiredWh(loadW, hours, dodPct, effPct) {
    var denom = (n(dodPct, 100) / 100) * (n(effPct, 100) / 100);
    return denom > 0 ? (n(loadW) * n(hours)) / denom : 0;
  }

  function ahToWh(ah, voltage) {
    return n(ah) * n(voltage);
  }

  function whToAh(wh, voltage) {
    var v = n(voltage);
    return v > 0 ? n(wh) / v : 0;
  }

  function bankLayout(targetV, targetAh, battV, battAh) {
    var series = Math.max(1, Math.round(n(targetV) / n(battV)));
    var parallel = Math.max(1, Math.ceil(n(targetAh) / n(battAh)));
    var actualV = series * n(battV);
    var actualAh = parallel * n(battAh);
    return {
      series: series,
      parallel: parallel,
      total: series * parallel,
      actualV: actualV,
      actualAh: actualAh,
      wh: actualV * actualAh,
    };
  }

  function solarBank(dailyWh, autonomy, dodPct, voltage, sunHours) {
    var bankWh = n(dailyWh) * n(autonomy) / (n(dodPct, 80) / 100);
    var v = n(voltage, 24);
    var ah = v > 0 ? bankWh / v : 0;
    var sun = n(sunHours, 5);
    var arrayW = sun > 0 ? n(dailyWh) / (sun * 0.75) : 0;
    return { bankWh: bankWh, ah: ah, arrayW: arrayW };
  }

  function nextCommonAh(ah) {
    var sizes = [7, 9, 12, 18, 20, 26, 35, 40, 50, 55, 70, 75, 80, 90, 100, 105, 120, 135, 150, 180, 200, 220, 250, 280, 300, 400, 500, 600, 800, 1000];
    for (var i = 0; i < sizes.length; i++) {
      if (sizes[i] >= ah) return sizes[i];
    }
    return Math.ceil(ah / 100) * 100;
  }

  var CHEMISTRIES = {
    flooded: { id: 'flooded', label: 'Flooded lead-acid', dod: 50, k: 1.3, H: 20, family: 'lead' },
    agm: { id: 'agm', label: 'AGM', dod: 50, k: 1.15, H: 20, family: 'lead' },
    gel: { id: 'gel', label: 'Gel', dod: 50, k: 1.2, H: 20, family: 'lead' },
    lifepo4: { id: 'lifepo4', label: 'LiFePO4 (lithium)', dod: 90, k: 1.05, H: 1, family: 'lithium' },
    other: { id: 'other', label: 'Other / not sure', dod: 80, k: 1, H: 20, family: 'other' },
  };

  function chemistry(id) {
    return CHEMISTRIES[id] || CHEMISTRIES.other;
  }

  /**
   * Effective Ah at a given discharge current (Peukert). Lithium k≈1 → no change.
   * Rated capacity C at hour-rate H. Never invent extra capacity beyond a small slow-rate bump.
   */
  function peukertEffectiveAh(ah, currentA, k, H) {
    ah = n(ah);
    currentA = n(currentA);
    k = n(k, 1);
    H = n(H, 20);
    if (ah <= 0 || currentA <= 0 || k <= 1.001) return ah;
    var ratio = ah / (currentA * H);
    if (ratio <= 0) return ah;
    var ceff = ah * Math.pow(ratio, k - 1);
    return clamp(ceff, ah * 0.15, ah * 1.1);
  }

  function usableFraction(dodPct, reservePct) {
    var dod = clamp(n(dodPct, 100), 1, 100) / 100;
    var reserve = clamp(n(reservePct, 0), 0, 90) / 100;
    return Math.max(0.01, dod * (1 - reserve));
  }

  /**
   * Full runtime analysis used by the Battery Runtime Calculator.
   */
  function analyzeRuntime(opts) {
    var V = n(opts.voltage, 12);
    var Ah = n(opts.capacity, 100);
    var load = n(opts.load, 100);
    var dod = clamp(n(opts.dod, 80), 1, 100);
    var eff = clamp(n(opts.efficiency, 85), 50, 100);
    var reserve = clamp(n(opts.reserve, 0), 0, 90);
    var chem = chemistry(opts.chemistry);
    var frac = usableFraction(dod, reserve);
    var eta = eff / 100;

    var invalid = V <= 0 || Ah <= 0 || load <= 0;
    var nominalWh = V * Ah;
    var usableWhNoPeukert = nominalWh * frac;
    var deliveredWh = usableWhNoPeukert * eta;
    var theoreticalH = load > 0 ? nominalWh / load : 0;
    var estimatedH = load > 0 ? deliveredWh / load : 0;

    var dcCurrent = V > 0 && eta > 0 ? load / (V * eta) : 0;
    var ceff = peukertEffectiveAh(Ah, dcCurrent, chem.k, chem.H);
    var peukertApplied = chem.k > 1.02 && ceff < Ah * 0.98;
    var practicalWh = V * ceff * frac * eta;
    var practicalH = load > 0 ? practicalWh / load : 0;
    var crate = Ah > 0 ? dcCurrent / Ah : 0;

    return {
      invalid: invalid,
      voltage: V,
      capacity: Ah,
      load: load,
      dod: dod,
      efficiency: eff,
      reserve: reserve,
      chemistry: chem,
      fraction: frac,
      nominalWh: nominalWh,
      usableWh: usableWhNoPeukert,
      deliveredWh: deliveredWh,
      theoreticalHours: theoreticalH,
      estimatedHours: estimatedH,
      practicalWh: practicalWh,
      practicalHours: practicalH,
      dcCurrent: dcCurrent,
      effectiveAh: ceff,
      peukertApplied: peukertApplied,
      crate: crate,
    };
  }

  function requiredAhForRuntime(opts) {
    var V = n(opts.voltage, 12);
    var load = n(opts.load, 100);
    var hours = n(opts.hours, 8);
    var dod = clamp(n(opts.dod, 80), 1, 100);
    var eff = clamp(n(opts.efficiency, 85), 50, 100);
    var reserve = clamp(n(opts.reserve, 0), 0, 90);
    var chem = chemistry(opts.chemistry);
    var frac = usableFraction(dod, reserve);
    var eta = eff / 100;
    var denom = V * frac * eta;
    if (denom <= 0 || load <= 0 || hours <= 0) {
      return { invalid: true, ah: 0, recommended: 0 };
    }
    var naive = (load * hours) / denom;
    var ah = naive;
    var i;
    for (i = 0; i < 8; i++) {
      var current = V > 0 && eta > 0 ? load / (V * eta) : 0;
      var ceff = peukertEffectiveAh(ah, current, chem.k, chem.H);
      if (ceff <= 0) break;
      var next = naive * (ah / ceff);
      if (Math.abs(next - ah) < 0.05) {
        ah = next;
        break;
      }
      ah = next;
    }
    return {
      invalid: false,
      ah: ah,
      recommended: nextCommonAh(ah),
      naive: naive,
    };
  }

  root.BatteryMath = {
    n: n,
    clamp: clamp,
    hoursToHm: hoursToHm,
    fmt: fmt,
    fmtWh: fmtWh,
    usableWh: usableWh,
    runtimeHours: runtimeHours,
    requiredAh: requiredAh,
    requiredWh: requiredWh,
    ahToWh: ahToWh,
    whToAh: whToAh,
    bankLayout: bankLayout,
    solarBank: solarBank,
    nextCommonAh: nextCommonAh,
    CHEMISTRIES: CHEMISTRIES,
    chemistry: chemistry,
    peukertEffectiveAh: peukertEffectiveAh,
    usableFraction: usableFraction,
    analyzeRuntime: analyzeRuntime,
    requiredAhForRuntime: requiredAhForRuntime,
  };
})(typeof window !== 'undefined' ? window : globalThis);
