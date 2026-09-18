/**
 * Chrome: mobile nav, dropdowns, sticky header, reduced-motion.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var header = document.querySelector('.bs-header');
    var toggle = document.querySelector('[data-bs-nav-toggle]');
    var panel = document.getElementById('bs-nav-panel');

    if (toggle && panel) {
      toggle.addEventListener('click', function () {
        var open = header.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        document.body.classList.toggle('bs-nav-lock', open);
      });
    }

    Array.prototype.forEach.call(document.querySelectorAll('.bs-menu-trigger'), function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        var item = btn.closest('.bs-menu-item');
        var open = item.classList.toggle('is-open');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        Array.prototype.forEach.call(document.querySelectorAll('.bs-menu-item.is-open'), function (other) {
          if (other !== item) {
            other.classList.remove('is-open');
            var t = other.querySelector('.bs-menu-trigger');
            if (t) t.setAttribute('aria-expanded', 'false');
          }
        });
      });
    });

    document.addEventListener('click', function (e) {
      if (!e.target.closest('.bs-menu-item')) {
        Array.prototype.forEach.call(document.querySelectorAll('.bs-menu-item.is-open'), function (item) {
          item.classList.remove('is-open');
          var t = item.querySelector('.bs-menu-trigger');
          if (t) t.setAttribute('aria-expanded', 'false');
        });
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key !== 'Escape') return;
      if (header && header.classList.contains('is-open')) {
        header.classList.remove('is-open');
        document.body.classList.remove('bs-nav-lock');
        if (toggle) toggle.setAttribute('aria-expanded', 'false');
      }
    });

    var lastY = 0;
    window.addEventListener('scroll', function () {
      if (!header) return;
      var y = window.scrollY || 0;
      header.classList.toggle('is-stuck', y > 8);
      lastY = y;
    }, { passive: true });
  });
})();
