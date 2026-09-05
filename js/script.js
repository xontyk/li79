(function () {
  'use strict';

  var burgerBtn = document.getElementById('burgerBtn');
  var mainNav = document.getElementById('mainNav');

  if (burgerBtn && mainNav) {
    burgerBtn.addEventListener('click', function () {
      var isOpen = mainNav.classList.toggle('open');
      burgerBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    mainNav.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        mainNav.classList.remove('open');
        burgerBtn.setAttribute('aria-expanded', 'false');
      });
    });
  }

  // Простая клиентская проверка совпадения паролей (в дополнение к серверной).
  var password = document.getElementById('password');
  var passwordConfirm = document.getElementById('passwordConfirm');
  var passwordError = document.getElementById('passwordError');
  var registerForm = document.getElementById('registerForm');

  if (registerForm && password && passwordConfirm && passwordError) {
    var validatePasswords = function () {
      var mismatch = passwordConfirm.value.length > 0 && password.value !== passwordConfirm.value;
      passwordError.hidden = !mismatch;
      return !mismatch;
    };

    password.addEventListener('input', validatePasswords);
    passwordConfirm.addEventListener('input', validatePasswords);

    registerForm.addEventListener('submit', function (event) {
      if (!validatePasswords()) {
        event.preventDefault();
      }
    });
  }

  // Кнопки-стрелки прокрутки галереи "Жизнь лицея" на главной странице.
  document.querySelectorAll('.carousel-nav').forEach(function (nav) {
    var scroller = nav.closest('.section-header-row') && nav.closest('.section-header-row').parentElement
      ? nav.closest('.section-header-row').parentElement.querySelector('.gallery-scroll')
      : null;
    if (!scroller) {
      return;
    }
    nav.querySelectorAll('.carousel-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var dir = parseInt(btn.getAttribute('data-dir'), 10) || 1;
        scroller.scrollBy({ left: dir * 280, behavior: 'smooth' });
      });
    });
  });

  // Проверка обязательных полей формы обратной связи перед отправкой.
  var contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', function (event) {
      var required = contactForm.querySelectorAll('[required]');
      var valid = true;
      required.forEach(function (field) {
        if (!field.value.trim()) {
          valid = false;
        }
      });
      if (!valid) {
        event.preventDefault();
        alert('Пожалуйста, заполните все обязательные поля формы.');
      }
    });
  }
})();
