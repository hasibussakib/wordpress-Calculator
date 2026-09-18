/**
 * Appliance Load Estimator — editable rows, live totals, SVG donut.
 */
(function () {
  'use strict';

  var COLORS = ['#2563EB', '#0F1E33', '#4FA3F7', '#1D4ED8', '#152A44', '#64748B', '#1E40AF', '#334155'];

  var DEFAULTS = [
    { name: 'LED bulbs', watts: 10, hours: 5, qty: 6 },
    { name: 'Ceiling fan', watts: 75, hours: 8, qty: 2 },
    { name: 'WiFi router', watts: 10, hours: 24, qty: 1 },
    { name: 'LED TV', watts: 80, hours: 4, qty: 1 },
    { name: 'Refrigerator (avg)', watts: 150, hours: 8, qty: 1 },
  ];

  function el(tag, attrs, html) {
    var n = document.createElement(tag);
    if (attrs) Object.keys(attrs).forEach(function (k) { n.setAttribute(k, attrs[k]); });
    if (html != null) n.innerHTML = html;
    return n;
  }

  function num(v) {
    var x = parseFloat(v);
    return isFinite(x) ? x : 0;
  }

  function fmt(v) {
    if (!isFinite(v)) return '0';
    return Math.round(v).toLocaleString();
  }

  function bind(root) {
    var tbody = root.querySelector('[data-rows]');
    var totalW = root.querySelector('[data-total-w]');
    var totalWh = root.querySelector('[data-total-wh]');
    var peakW = root.querySelector('[data-peak-w]');
    var chart = root.querySelector('[data-chart]');
    var legend = root.querySelector('[data-legend]');
    var addBtn = root.querySelector('[data-add-row]');
    var preset = root.querySelector('[data-preset]');

    function rowValues(tr) {
      var inputs = tr.querySelectorAll('input');
      return {
        name: inputs[0].value,
        watts: num(inputs[1].value),
        hours: num(inputs[2].value),
        qty: num(inputs[3].value),
      };
    }

    function addRow(data) {
      data = data || { name: '', watts: 0, hours: 1, qty: 1 };
      var tr = el('tr');
      tr.innerHTML =
        '<td><input type="text" value="' + String(data.name).replace(/"/g, '&quot;') + '" placeholder="Appliance"></td>' +
        '<td><input type="number" min="0" step="1" value="' + data.watts + '"></td>' +
        '<td><input type="number" min="0" max="24" step="0.5" value="' + data.hours + '"></td>' +
        '<td><input type="number" min="1" step="1" value="' + data.qty + '"></td>' +
        '<td data-wh>0</td>' +
        '<td><button type="button" class="bs-icon-btn" data-remove aria-label="Remove">×</button></td>';
      tbody.appendChild(tr);
    }

    function collect() {
      return Array.prototype.map.call(tbody.querySelectorAll('tr'), function (tr) {
        var v = rowValues(tr);
        v.running = v.watts * v.qty;
        v.wh = v.watts * v.hours * v.qty;
        tr.querySelector('[data-wh]').textContent = fmt(v.wh);
        return v;
      });
    }

    function polar(cx, cy, r, angle) {
      var a = (angle - 90) * Math.PI / 180;
      return { x: cx + r * Math.cos(a), y: cy + r * Math.sin(a) };
    }

    function arc(cx, cy, r, start, end) {
      var s = polar(cx, cy, r, end);
      var e = polar(cx, cy, r, start);
      var large = end - start > 180 ? 1 : 0;
      return ['M', s.x, s.y, 'A', r, r, 0, large, 0, e.x, e.y].join(' ');
    }

    function draw(rows) {
      var sumWh = rows.reduce(function (a, r) { return a + r.wh; }, 0);
      var sumW = rows.reduce(function (a, r) { return a + r.running; }, 0);
      totalW.textContent = fmt(sumW) + ' W';
      totalWh.textContent = fmt(sumWh) + ' Wh';
      peakW.textContent = fmt(sumW) + ' W';

      var cx = 120, cy = 120, r = 84, inner = 54;
      var html = '<circle cx="' + cx + '" cy="' + cy + '" r="' + r + '" fill="#EFF6FF"></circle>';
      html += '<circle cx="' + cx + '" cy="' + cy + '" r="' + inner + '" fill="#fff"></circle>';
      html += '<text x="' + cx + '" y="' + (cy - 4) + '" text-anchor="middle" font-size="18" font-weight="700" fill="#0B1E33">' + fmt(sumWh) + '</text>';
      html += '<text x="' + cx + '" y="' + (cy + 16) + '" text-anchor="middle" font-size="11" fill="#64748B">Wh / day</text>';

      legend.innerHTML = '';
      if (sumWh <= 0) {
        chart.innerHTML = html;
        return;
      }

      var angle = 0;
      var slices = '';
      rows.forEach(function (row, i) {
        if (row.wh <= 0) return;
        var sweep = (row.wh / sumWh) * 360;
        var color = COLORS[i % COLORS.length];
        if (sweep >= 359.9) {
          slices += '<circle cx="' + cx + '" cy="' + cy + '" r="' + r + '" fill="' + color + '"></circle>';
        } else if (sweep > 0.4) {
          slices += '<path d="' + arc(cx, cy, r, angle, angle + sweep) + '" fill="none" stroke="' + color + '" stroke-width="30"></path>';
        }
        angle += sweep;
        var li = el('li');
        li.innerHTML = '<i style="background:' + color + '"></i><span>' + (row.name || 'Item') + '</span><strong>' + Math.round(row.wh / sumWh * 100) + '%</strong>';
        legend.appendChild(li);
      });
      chart.innerHTML = '<circle cx="' + cx + '" cy="' + cy + '" r="' + r + '" fill="#EFF6FF"></circle>' + slices +
        '<circle cx="' + cx + '" cy="' + cy + '" r="' + inner + '" fill="#fff"></circle>' +
        '<text x="' + cx + '" y="' + (cy - 4) + '" text-anchor="middle" font-size="18" font-weight="700" fill="#0B1E33">' + fmt(sumWh) + '</text>' +
        '<text x="' + cx + '" y="' + (cy + 16) + '" text-anchor="middle" font-size="11" fill="#64748B">Wh / day</text>';
    }

    function render() {
      draw(collect());
    }

    tbody.addEventListener('input', render);
    tbody.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-remove]');
      if (!btn) return;
      var tr = btn.closest('tr');
      if (tbody.querySelectorAll('tr').length <= 1) {
        tr.querySelectorAll('input')[0].value = '';
        tr.querySelectorAll('input')[1].value = 0;
        render();
        return;
      }
      tr.remove();
      render();
    });

    addBtn.addEventListener('click', function () {
      addRow({ name: '', watts: 50, hours: 2, qty: 1 });
      render();
    });

    if (preset) {
      preset.addEventListener('change', function () {
        if (!preset.value) return;
        var parts = preset.value.split('|');
        addRow({ name: parts[0], watts: num(parts[1]), hours: 4, qty: 1 });
        preset.value = '';
        render();
      });
    }

    DEFAULTS.forEach(addRow);
    render();
  }

  document.addEventListener('DOMContentLoaded', function () {
    Array.prototype.forEach.call(document.querySelectorAll('#bs-estimator'), bind);
  });
})();
