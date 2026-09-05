(function () {
  'use strict';

  var burgerBtn = document.getElementById('adminBurgerBtn');
  var sidebar = document.getElementById('adminSidebar');

  if (burgerBtn && sidebar) {
    burgerBtn.addEventListener('click', function () {
      var isOpen = sidebar.classList.toggle('open');
      burgerBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  // Предпросмотр выбранного фото перед загрузкой.
  var photoInput = document.querySelector('input[type="file"][name="photo"]');
  var photoPreview = document.getElementById('photoPreview');

  if (photoInput && photoPreview) {
    photoInput.addEventListener('change', function () {
      var file = photoInput.files && photoInput.files[0];
      if (!file) {
        return;
      }
      var reader = new FileReader();
      reader.onload = function (event) {
        photoPreview.src = event.target.result;
        photoPreview.hidden = false;
      };
      reader.readAsDataURL(file);
    });
  }
})();
