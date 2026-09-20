/**
 * Battery Runtime Calculator — decision tool (simple / target, what-if, show work).
 */
(function () {
  'use strict';

  var M = window.BatteryMath;
  if (!M) return;

  var DEFAULTS = {
    voltage: 12,
    capacity: 100,
    load: 100,
    chemistry: 'lifepo4',
    dod: 90,
    efficiency: 85,
    reserve: 0,
    targetHours: 8,
  };

  function $(root, sel) {
    return root.querySelector(sel);
  }

  function num(el, fallback) {
    return M.n(el && el.value, fallback);
  }

  function setText(el, text) {
    if (el) el.textContent = text;
  }

  function esc(s) {
    return String(s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function inputs(root) {
    return {
      voltage: num($(root, '[name="voltage"]'), 12),
      capacity: num($(root, '[name="capacity"]'), 100),
      load: num($(root, '[name="load"]'), 100),
      chemistry: ($(root, '[name="chemistry"]') || {}).value || 'lifepo4',
      dod: num($(root, '[name="dod"]'), 90),
      efficiency: num($(root, '[name="efficiency"]'), 85),
      reserve: num($(root, '[name="reserve"]'), 0),
      targetHours: num($(root, '[name="targetHours"]'), 8),
    };
  }

  function warnings(a, v, mode, need) {
    var out = [];
    if (a.invalid) {
      out.push({ level: 'err', text: 'Enter a voltage, capacity and load greater than zero to get a runtime.' });
      return out;
    }
    if (a.chemistry.family === 'lead' && a.dod > 60) {
      out.push({
        level: 'warn',
        text: 'Depth of discharge is set above 60% on a lead-acid chemistry. That extra runtime today usually costs a lot of cycle life. 50% is the usual safe figure — check the manufacturer’s limit.',
      });
    }
    if (a.chemistry.family === 'lead' && a.crate > 0.2) {
      out.push({
        level: 'warn',
        text: 'Estimated battery current is ' + M.fmt(a.dcCurrent, 1) + ' A, about a ' + M.fmt(a.crate, 2) + 'C discharge. Lead-acid delivers less than the label Ah at this rate (Peukert). The practical estimate already reduces runtime; confirm cable size and the battery’s max discharge current.',
      });
    }
    if (a.chemistry.family === 'lithium' && a.crate > 1) {
      out.push({
        level: 'warn',
        text: 'Estimated battery current is ' + M.fmt(a.dcCurrent, 1) + ' A — above 1C for this pack. Many LiFePO4 batteries allow it for short bursts, but continuous runtime may be limited by the BMS. Check the datasheet.',
      });
    }
    if (a.theoreticalHours > a.practicalHours * 1.25) {
      out.push({
        level: 'info',
        text: 'The label-style figure (' + M.hoursToHm(a.theoreticalHours) + ') overestimates practical runtime. Usable energy after DoD, reserve and inverter losses is what the load actually sees.',
      });
    }
    if (mode === 'runtime' && v.targetHours > 0 && a.practicalHours + 0.02 < v.targetHours) {
      var short = v.targetHours - a.practicalHours;
      out.push({
        level: 'warn',
        text: 'Your current battery is estimated to provide ' + M.hoursToHm(a.practicalHours) + ', which is ' + M.hoursToHm(short) + ' below your ' + M.fmt(v.targetHours, 1) + ' h target. You would need approximately ' + M.fmt(need.ah, 0) + ' Ah (' + need.recommended + ' Ah is the next common size) under the selected assumptions.',
      });
    }
    if (a.practicalHours > 0 && a.practicalHours < 0.25) {
      out.push({
        level: 'warn',
        text: 'Estimated runtime is under 15 minutes. Reduce the load, raise voltage (lower current) or use a much larger battery.',
      });
    }
    if (a.reserve > 0 && a.dod >= 100) {
      out.push({
        level: 'info',
        text: 'Reserve is applied on top of 100% DoD, so you are still leaving ' + M.fmt(a.reserve, 0) + '% unused. That is fine as a safety buffer.',
      });
    }
    return out;
  }

  function meaning(a, v, mode, need) {
    if (a.invalid) return 'Enter your battery and load to see what the result means for your setup.';
    var rt = M.hoursToHm(a.practicalHours);
    if (mode === 'target') {
      return 'To cover ' + M.fmt(v.targetHours, 1) + ' hours at ' + M.fmt(v.load, 0) + ' W on a ' + M.fmt(v.voltage, 0) + ' V system, you need about ' + M.fmt(need.ah, 0) + ' Ah after DoD, reserve and inverter losses. A ' + need.recommended + ' Ah battery is the next common size that meets that.';
    }
    var extra = '';
    if (v.targetHours > 0 && a.practicalHours + 0.02 < v.targetHours) {
      extra = ' That is short of your ' + M.fmt(v.targetHours, 1) + ' h target — increase capacity toward ' + M.fmt(need.ah, 0) + ' Ah, or cut the load.';
    } else if (v.targetHours > 0 && a.practicalHours >= v.targetHours) {
      extra = ' That covers your ' + M.fmt(v.targetHours, 1) + ' h target under these assumptions, with about ' + M.hoursToHm(a.practicalHours - v.targetHours) + ' of margin.';
    }
    return 'Your estimated runtime is ' + rt + ' based on a ' + M.fmt(v.voltage, 0) + ' V ' + M.fmt(v.capacity, 0) + ' Ah ' + a.chemistry.label + ' battery, a ' + M.fmt(v.load, 0) + ' W load, ' + M.fmt(v.dod, 0) + '% DoD and ' + M.fmt(v.efficiency, 0) + '% efficiency.' + extra;
  }

  function nextSteps(a, v, mode, need) {
    if (a.invalid) return [];
    var steps = [];
    if (mode === 'target' || (v.targetHours > 0 && a.practicalHours < v.targetHours)) {
      steps.push('Choose a battery around ' + need.recommended + ' Ah at ' + M.fmt(v.voltage, 0) + ' V, or put enough batteries in parallel to reach ~' + M.fmt(need.ah, 0) + ' Ah.');
      steps.push('Or reduce the connected load. Halving watts roughly doubles runtime.');
    } else {
      steps.push('If you need more hours, add parallel capacity or drop non-essential loads — use the What-if table to see the trade-off.');
    }
    if (a.efficiency < 100) {
      steps.push('Confirm the inverter continuous rating is above ' + M.fmt(v.load, 0) + ' W, with surge headroom for motors.');
    }
    if (a.chemistry.family === 'lead') {
      steps.push('Keep lead-acid near 50% DoD and avoid high C-rates. Manufacturer limits override this estimate.');
    } else {
      steps.push('Check the BMS max continuous current against the estimated ' + M.fmt(a.dcCurrent, 1) + ' A draw.');
    }
    return steps;
  }

  function workRows(a, v, mode, need) {
    var rows = [];
    rows.push(['Nominal energy', 'V × Ah', M.fmt(v.voltage, 2) + ' × ' + M.fmt(v.capacity, 2), M.fmtWh(a.nominalWh)]);
    rows.push(['Usable fraction', 'DoD × (1 − reserve)', M.fmt(v.dod, 0) + '% × (1 − ' + M.fmt(v.reserve, 0) + '%)', M.fmt(a.fraction * 100, 1) + '%']);
    rows.push(['Usable energy', 'Nominal × fraction', M.fmtWh(a.nominalWh) + ' × ' + M.fmt(a.fraction, 3), M.fmtWh(a.usableWh)]);
    rows.push(['After inverter', 'Usable × η', M.fmtWh(a.usableWh) + ' × ' + M.fmt(v.efficiency, 0) + '%', M.fmtWh(a.deliveredWh)]);
    rows.push(['Battery current', 'Load ÷ (V × η)', M.fmt(v.load, 1) + ' ÷ (' + M.fmt(v.voltage, 2) + ' × ' + M.fmt(v.efficiency, 0) + '%)', M.fmt(a.dcCurrent, 2) + ' A']);
    if (a.peukertApplied) {
      rows.push(['Peukert-adjusted Ah', 'C × (C / (I·H))^(k−1)', 'k = ' + M.fmt(a.chemistry.k, 2) + ', H = ' + a.chemistry.H + ' h', M.fmt(a.effectiveAh, 1) + ' Ah']);
    }
    if (mode === 'target') {
      rows.push(['Required Ah', '(W × hours) ÷ (V × fraction × η)', M.fmt(v.load, 1) + ' × ' + M.fmt(v.targetHours, 2) + ' ÷ …', M.fmt(need.ah, 1) + ' Ah']);
    } else {
      rows.push(['Estimated runtime', 'Delivered Wh ÷ load', M.fmtWh(a.peukertApplied ? a.practicalWh : a.deliveredWh) + ' ÷ ' + M.fmt(v.load, 1) + ' W', M.hoursToHm(a.practicalHours)]);
    }
    return rows;
  }

  function renderWhatIf(root, v) {
    var box = $(root, '[data-whatif]');
    if (!box) return;
    var loads = [v.load * 0.5, 100, v.load, 200, 300, v.load * 2].filter(function (w, i, arr) {
      return w > 0 && arr.indexOf(w) === i;
    });
    loads.sort(function (a, b) { return a - b; });
    var seen = {};
    var unique = [];
    loads.forEach(function (w) {
      var key = String(Math.round(w * 10) / 10);
      if (seen[key]) return;
      seen[key] = true;
      unique.push(w);
    });
    var rows = unique.map(function (w) {
      var a = M.analyzeRuntime({
        voltage: v.voltage, capacity: v.capacity, load: w,
        dod: v.dod, efficiency: v.efficiency, reserve: v.reserve, chemistry: v.chemistry,
      });
      return { w: w, hours: a.practicalHours, current: a === v.load };
    });
    var maxH = Math.max.apply(null, rows.map(function (r) { return r.hours; }).concat([0.01]));
    box.innerHTML = rows.map(function (r) {
      var pct = Math.max(4, (r.hours / maxH) * 100);
      var on = Math.abs(r.w - v.load) < 0.05;
      return (
        '<div class="bs-bar ' + (on ? 'is-li' : '') + '">' +
          '<div class="bs-bar-meta">' +
            '<strong>' + M.fmt(r.w, 0) + ' W' + (on ? ' (your load)' : '') + '</strong>' +
            '<span>' + M.hoursToHm(r.hours) + '</span>' +
          '</div>' +
          '<div class="bs-bar-track"><div class="bs-bar-fill" style="width:' + pct + '%"></div></div>' +
        '</div>'
      );
    }).join('');
  }

  function bind(root) {
    var mode = 'runtime';
    var chemSelect = $(root, '[name="chemistry"]');
    var dodInput = $(root, '[name="dod"]');
    var lastChem = chemSelect ? chemSelect.value : 'lifepo4';

    function applyMode() {
      Array.prototype.forEach.call(root.querySelectorAll('[data-for-mode]'), function (el) {
        var modes = (el.getAttribute('data-for-mode') || '').split(',');
        el.hidden = modes.indexOf(mode) === -1;
      });
    }

    function render() {
      var v = inputs(root);
      var a = M.analyzeRuntime(v);
      var need = M.requiredAhForRuntime({
        voltage: v.voltage, load: v.load, hours: v.targetHours,
        dod: v.dod, efficiency: v.efficiency, reserve: v.reserve, chemistry: v.chemistry,
      });

      var primary = $(root, '[data-primary]');
      if (mode === 'target') {
        primary.innerHTML =
          '<span class="bs-result-label">Required battery capacity</span>' +
          '<strong>' + (need.invalid ? '—' : M.fmt(need.ah, 0) + ' Ah') + '</strong>' +
          '<small>' + (need.invalid ? 'Enter valid values' : 'Next common size ' + need.recommended + ' Ah · ' + M.fmt(v.targetHours, 1) + ' h target') + '</small>';
      } else {
        primary.innerHTML =
          '<span class="bs-result-label">Estimated runtime</span>' +
          '<strong>' + (a.invalid ? '—' : M.hoursToHm(a.practicalHours)) + '</strong>' +
          '<small>' + (a.invalid ? 'Enter voltage, capacity and load' : M.fmt(a.practicalHours, 2) + ' hours · practical estimate') + '</small>';
      }

      $(root, '[data-secondary]').innerHTML = [
        ['Nominal battery energy', M.fmtWh(a.nominalWh)],
        ['Estimated usable energy', M.fmtWh(a.usableWh)],
        ['Energy delivered to load', M.fmtWh(a.deliveredWh)],
        ['Load power', M.fmt(v.load, 1) + ' W'],
        ['Estimated battery current', M.fmt(a.dcCurrent, 2) + ' A'],
        ['Theoretical (label) runtime', M.hoursToHm(a.theoreticalHours)],
      ].map(function (r) {
        return '<li><span>' + r[0] + '</span><strong>' + r[1] + '</strong></li>';
      }).join('');

      setText($(root, '[data-assumptions]'),
        a.chemistry.label + ' · ' + M.fmt(v.dod, 0) + '% DoD · ' +
        (v.reserve > 0 ? M.fmt(v.reserve, 0) + '% reserve · ' : '') +
        M.fmt(v.efficiency, 0) + '% efficiency' +
        (a.peukertApplied ? ' · Peukert k=' + M.fmt(a.chemistry.k, 2) : ' · no Peukert adjustment')
      );

      var warnBox = $(root, '[data-warnings]');
      var warns = warnings(a, v, mode, need);
      warnBox.innerHTML = warns.map(function (w) {
        return '<div class="bs-alert bs-alert-' + w.level + '">' + esc(w.text) + '</div>';
      }).join('');
      warnBox.hidden = warns.length === 0;

      setText($(root, '[data-meaning]'), meaning(a, v, mode, need));

      var steps = nextSteps(a, v, mode, need);
      $(root, '[data-next]').innerHTML = steps.map(function (s) { return '<li>' + esc(s) + '</li>'; }).join('');

      var work = $(root, '[data-work]');
      work.innerHTML = workRows(a, v, mode, need).map(function (r) {
        return '<tr><th>' + esc(r[0]) + '</th><td><code>' + esc(r[1]) + '</code></td><td>' + esc(r[2]) + '</td><td>' + esc(r[3]) + '</td></tr>';
      }).join('');

      setText($(root, '[data-nominal-vs]'), M.hoursToHm(a.theoreticalHours));
      setText($(root, '[data-practical-vs]'), M.hoursToHm(a.practicalHours));

      renderWhatIf(root, mode === 'target' && !need.invalid ? Object.assign({}, v, { capacity: need.ah }) : v);

      root._lastCopy = mode === 'target'
        ? 'Required capacity: ' + M.fmt(need.ah, 0) + ' Ah (' + need.recommended + ' Ah recommended) for ' + M.fmt(v.targetHours, 1) + ' h at ' + M.fmt(v.load, 0) + ' W, ' + M.fmt(v.voltage, 0) + ' V.'
        : 'Estimated runtime: ' + M.hoursToHm(a.practicalHours) + ' from a ' + M.fmt(v.voltage, 0) + ' V ' + M.fmt(v.capacity, 0) + ' Ah battery at ' + M.fmt(v.load, 0) + ' W.';
    }

    root.addEventListener('input', function (e) {
      if (e.target && e.target.type === 'range') {
        var out = e.target.parentNode.querySelector('output');
        if (out) {
          var unit = e.target.getAttribute('data-unit') || '%';
          out.textContent = e.target.value + ' ' + unit;
        }
      }
      render();
    });
    root.addEventListener('change', function (e) {
      if (e.target && e.target.name === 'chemistry' && e.target.value !== lastChem) {
        var c = M.chemistry(e.target.value);
        if (dodInput) {
          dodInput.value = c.dod;
          var dodOut = dodInput.parentNode.querySelector('output');
          if (dodOut) dodOut.textContent = c.dod + ' %';
        }
        lastChem = e.target.value;
      }
      render();
    });

    Array.prototype.forEach.call(root.querySelectorAll('[data-mode]'), function (tab) {
      tab.addEventListener('click', function () {
        mode = tab.getAttribute('data-mode');
        Array.prototype.forEach.call(root.querySelectorAll('[data-mode]'), function (t) {
          var on = t === tab;
          t.classList.toggle('is-active', on);
          t.setAttribute('aria-selected', on ? 'true' : 'false');
        });
        applyMode();
        render();
      });
    });

    var adv = $(root, '[data-advanced]');
    var advBtn = $(root, '[data-advanced-toggle]');
    if (advBtn && adv) {
      advBtn.addEventListener('click', function () {
        var open = adv.hasAttribute('hidden');
        if (open) adv.removeAttribute('hidden');
        else adv.setAttribute('hidden', '');
        advBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        advBtn.querySelector('[data-adv-label]').textContent = open ? 'Hide advanced options' : 'Advanced options';
      });
    }

    var resetBtn = $(root, '[data-reset]');
    if (resetBtn) {
      resetBtn.addEventListener('click', function () {
        Object.keys(DEFAULTS).forEach(function (k) {
          var el = root.querySelector('[name="' + k + '"]');
          if (el) el.value = DEFAULTS[k];
        });
        lastChem = DEFAULTS.chemistry;
        Array.prototype.forEach.call(root.querySelectorAll('input[type="range"]'), function (r) {
          var out = r.parentNode.querySelector('output');
          if (out) out.textContent = r.value + ' ' + (r.getAttribute('data-unit') || '%');
        });
        render();
      });
    }

    var exampleBtn = $(root, '[data-example]');
    if (exampleBtn) {
      exampleBtn.addEventListener('click', function () {
        var map = {
          voltage: 12, capacity: 100, load: 100,
          chemistry: 'flooded', dod: 50, efficiency: 85, reserve: 0, targetHours: 8,
        };
        Object.keys(map).forEach(function (k) {
          var el = root.querySelector('[name="' + k + '"]');
          if (el) el.value = map[k];
        });
        lastChem = 'flooded';
        Array.prototype.forEach.call(root.querySelectorAll('input[type="range"]'), function (r) {
          var out = r.parentNode.querySelector('output');
          if (out) out.textContent = r.value + ' ' + (r.getAttribute('data-unit') || '%');
        });
        render();
      });
    }

    var copyBtn = $(root, '[data-copy]');
    if (copyBtn) {
      copyBtn.addEventListener('click', function () {
        var text = root._lastCopy || '';
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(text).then(function () {
            copyBtn.textContent = 'Copied';
            setTimeout(function () { copyBtn.textContent = 'Copy result'; }, 1500);
          });
        }
      });
    }

    applyMode();
    render();
  }

  document.addEventListener('DOMContentLoaded', function () {
    Array.prototype.forEach.call(document.querySelectorAll('[data-runtime-calc]'), bind);
  });
})();
