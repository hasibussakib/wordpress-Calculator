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
  };
})(typeof window !== 'undefined' ? window : globalThis);
