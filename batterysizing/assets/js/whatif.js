/**
 * What-If Runtime Comparison — horizontal bars for battery presets.
 */
(function () {
  'use strict';

  var M = window.BatteryMath;
  if (!M) return;

  function bind(root) {
    var presets;
    try {
      presets = JSON.parse(root.getAttribute('data-presets') || '[]');
    } catch (e) {
      presets = [];
    }
    var loadEl = root.querySelector('[data-load]');
    var effEl = root.querySelector('[data-eff]');
    var bars = root.querySelector('[data-bars]');

    function render() {
      var load = M.n(loadEl.value, 100);
      var eff = M.n(effEl.value, 85);
      var rows = presets.map(function (p) {
        var hours = M.runtimeHours(p.voltage, p.capacity, load, p.dod, eff);
        return { name: p.name, hours: hours, chemistry: p.chemistry, dod: p.dod };
      });
      var max = Math.max.apply(null, rows.map(function (r) { return r.hours; }).concat([0.01]));
      bars.innerHTML = rows.map(function (r) {
        var pct = Math.max(4, (r.hours / max) * 100);
        var klass = r.chemistry === 'lithium' ? 'is-li' : 'is-pb';
        return (
          '<div class="bs-bar ' + klass + '">' +
            '<div class="bs-bar-meta">' +
              '<strong>' + r.name + '</strong>' +
              '<span>' + M.hoursToHm(r.hours) + ' · ' + r.dod + '% DoD</span>' +
            '</div>' +
            '<div class="bs-bar-track"><div class="bs-bar-fill" style="width:' + pct + '%"></div></div>' +
          '</div>'
        );
      }).join('');
    }

    loadEl.addEventListener('input', render);
    effEl.addEventListener('input', render);
    render();
  }

  document.addEventListener('DOMContentLoaded', function () {
    Array.prototype.forEach.call(document.querySelectorAll('#bs-whatif'), bind);
  });
})();
