/**
 * Universal calculator engine. Reads data-config on [data-calculator]
 * and renders live results as inputs change.
 */
(function () {
  'use strict';

  var M = window.BatteryMath;
  if (!M) return;

  function readConfig(el) {
    try {
      return JSON.parse(el.getAttribute('data-config') || '{}');
    } catch (e) {
      return {};
    }
  }

  function values(form) {
    var out = {};
    Array.prototype.forEach.call(form.elements, function (el) {
      if (!el.name) return;
      out[el.name] = el.type === 'checkbox' ? el.checked : el.value;
    });
    return out;
  }

  function setPrimary(box, title, value, sub) {
    box.innerHTML =
      '<span class="bs-result-label">' + title + '</span>' +
      '<strong>' + value + '</strong>' +
      (sub ? '<small>' + sub + '</small>' : '');
  }

  function setSecondary(list, rows) {
    list.innerHTML = rows
      .map(function (r) {
        return '<li><span>' + r[0] + '</span><strong>' + r[1] + '</strong></li>';
      })
      .join('');
  }

  function compute(engine, mode, v) {
    var voltage = M.n(v.voltage, 12);
    var cap = M.n(v.capacity, 100);
    var load = M.n(v.load, 100);
    var hours = M.n(v.hours, 4);
    var dod = M.n(v.dod, 80);
    var eff = M.n(v.efficiency, 85);

    if (engine === 'runtime' || (engine === 'runtime_capacity' && mode === 'runtime')) {
      var rt = M.runtimeHours(voltage, cap, load, dod, eff);
      var usable = M.usableWh(voltage, cap, dod);
      var delivered = usable * (eff / 100);
      return {
        primary: ['Runtime', M.hoursToHm(rt), M.fmt(rt, 2) + ' hours'],
        secondary: [
          ['Stored energy', M.fmtWh(voltage * cap)],
          ['Usable energy (DoD)', M.fmtWh(usable)],
          ['Delivered to load', M.fmtWh(delivered)],
          ['Current draw', M.fmt(load / voltage, 1) + ' A'],
        ],
      };
    }

    if (engine === 'capacity' || (engine === 'runtime_capacity' && mode === 'capacity')) {
      var ah = M.requiredAh(load, hours, voltage, dod, eff);
      var wh = M.requiredWh(load, hours, dod, eff);
      var rec = M.nextCommonAh(ah);
      return {
        primary: ['Required capacity', M.fmt(ah, 1) + ' Ah', '≈ ' + M.fmtWh(wh) + ' stored'],
        secondary: [
          ['Recommended size', rec + ' Ah'],
          ['Energy to store', M.fmtWh(wh)],
          ['Load × time', M.fmtWh(load * hours) + ' (before losses)'],
          ['At this voltage', voltage + ' V'],
        ],
      };
    }

    if (engine === 'bank') {
      var layout = M.bankLayout(v.target_voltage, v.target_capacity, v.battery_voltage, v.battery_capacity);
      return {
        primary: ['Batteries needed', String(layout.total), layout.series + 'S' + layout.parallel + 'P'],
        secondary: [
          ['Series (voltage)', layout.series + ' × ' + M.fmt(v.battery_voltage, 0) + ' V = ' + M.fmt(layout.actualV, 1) + ' V'],
          ['Parallel (capacity)', layout.parallel + ' × ' + M.fmt(v.battery_capacity, 0) + ' Ah = ' + M.fmt(layout.actualAh, 0) + ' Ah'],
          ['Bank energy', M.fmtWh(layout.wh)],
          ['Wiring', layout.series + ' in series, ' + layout.parallel + ' parallel string' + (layout.parallel > 1 ? 's' : '')],
        ],
      };
    }

    if (engine === 'solar') {
      var s = M.solarBank(v.daily_wh, v.autonomy, v.dod, v.voltage, v.sun_hours);
      return {
        primary: ['Battery bank', M.fmt(s.ah, 0) + ' Ah', M.fmtWh(s.bankWh) + ' at ' + M.fmt(v.voltage, 0) + ' V'],
        secondary: [
          ['Stored energy needed', M.fmtWh(s.bankWh)],
          ['Recommended Ah', M.nextCommonAh(s.ah) + ' Ah'],
          ['Solar array', M.fmt(s.arrayW, 0) + ' W'],
          ['Daily consumption', M.fmtWh(M.n(v.daily_wh))],
        ],
      };
    }

    if (engine === 'ups') {
      var pf = M.clamp(M.n(v.power_factor, 0.8), 0.5, 1);
      var ahNeed = M.requiredAh(v.load, v.hours, v.voltage, v.dod, v.efficiency);
      var battAh = M.n(v.battery_ah, 100);
      var count = Math.max(1, Math.ceil(ahNeed / battAh));
      var va = M.n(v.load) / pf;
      return {
        primary: ['Required capacity', M.fmt(ahNeed, 1) + ' Ah', count + ' × ' + battAh + ' Ah battery' + (count > 1 ? 's' : '')],
        secondary: [
          ['UPS rating', M.fmt(va, 0) + ' VA  (PF ' + M.fmt(pf, 2) + ')'],
          ['Backup target', M.fmt(v.hours, 2) + ' h at ' + M.fmt(v.load, 0) + ' W'],
          ['Battery count', String(count)],
          ['Delivered energy', M.fmtWh(M.n(v.load) * M.n(v.hours))],
        ],
      };
    }

    if (engine === 'ah_to_wh') {
      var whOut = M.ahToWh(v.capacity, v.voltage);
      return {
        primary: ['Energy', M.fmtWh(whOut), M.fmt(whOut / 1000, 3) + ' kWh'],
        secondary: [
          ['Formula', M.fmt(v.capacity, 2) + ' Ah × ' + M.fmt(v.voltage, 2) + ' V'],
          ['At 12 V', M.fmtWh(M.n(v.capacity) * 12)],
          ['At 24 V', M.fmtWh(M.n(v.capacity) * 24)],
          ['At 48 V', M.fmtWh(M.n(v.capacity) * 48)],
        ],
      };
    }

    if (engine === 'wh_to_ah') {
      var ahOut = M.whToAh(v.energy, v.voltage);
      return {
        primary: ['Capacity', M.fmt(ahOut, 2) + ' Ah', 'at ' + M.fmt(v.voltage, 2) + ' V'],
        secondary: [
          ['Formula', M.fmt(v.energy, 0) + ' Wh ÷ ' + M.fmt(v.voltage, 2) + ' V'],
          ['At 12 V', M.fmt(M.whToAh(v.energy, 12), 1) + ' Ah'],
          ['At 24 V', M.fmt(M.whToAh(v.energy, 24), 1) + ' Ah'],
          ['At 48 V', M.fmt(M.whToAh(v.energy, 48), 1) + ' Ah'],
        ],
      };
    }

    return { primary: ['Result', '—', ''], secondary: [] };
  }

  function bind(widget) {
    var form = widget.querySelector('[data-form]');
    var primary = widget.querySelector('[data-primary]');
    var secondary = widget.querySelector('[data-secondary]');
    var engine = widget.getAttribute('data-engine');
    var mode = 'runtime';

    function applyMode() {
      Array.prototype.forEach.call(widget.querySelectorAll('[data-modes]'), function (field) {
        var modes = (field.getAttribute('data-modes') || '').split(',');
        var show = modes.indexOf(mode) !== -1;
        field.classList.toggle('is-hidden', !show);
      });
    }

    function render() {
      var result = compute(engine, mode, values(form));
      setPrimary(primary, result.primary[0], result.primary[1], result.primary[2]);
      setSecondary(secondary, result.secondary);
    }

    form.addEventListener('input', function (e) {
      if (e.target && e.target.type === 'range') {
        var out = e.target.parentNode.querySelector('output');
        if (out) out.textContent = e.target.value + '%';
      }
      render();
    });
    form.addEventListener('change', render);

    Array.prototype.forEach.call(widget.querySelectorAll('[data-mode]'), function (tab) {
      tab.addEventListener('click', function () {
        mode = tab.getAttribute('data-mode');
        Array.prototype.forEach.call(widget.querySelectorAll('[data-mode]'), function (t) {
          var on = t === tab;
          t.classList.toggle('is-active', on);
          t.setAttribute('aria-selected', on ? 'true' : 'false');
        });
        applyMode();
        render();
      });
    });

    applyMode();
    render();
  }

  document.addEventListener('DOMContentLoaded', function () {
    Array.prototype.forEach.call(document.querySelectorAll('[data-calculator]'), bind);
  });
})();
