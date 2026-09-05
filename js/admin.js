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

  // Зона загрузки фото: клик, drag-and-drop, превью и удаление фото.
  var dropzone = document.getElementById('photoDropzone');
  var photoInput = document.getElementById('photoInput');
  var preview = document.getElementById('photoDropzonePreview');
  var chooseBtn = document.getElementById('photoChooseBtn');
  var removeBtn = document.getElementById('photoRemoveBtn');
  var removeField = document.getElementById('removePhotoField');

  var PLACEHOLDER_HTML =
    '<div class="photo-dropzone-placeholder">' +
    '<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">' +
    '<path d="M4 16.5V6a2 2 0 0 1 2-2h4l1.5 2H18a2 2 0 0 1 2 2v8.5"/>' +
    '<path d="M2.5 18.5 8 13a2 2 0 0 1 2.8 0l1.7 1.7a2 2 0 0 0 2.8 0L18 12l3.5 3.5"/>' +
    '<circle cx="8" cy="9" r="1.5"/>' +
    '<path d="M2 18.5v0A2.5 2.5 0 0 0 4.5 21h15a2.5 2.5 0 0 0 2.5-2.5v0"/></svg>' +
    '<span>Перетащите фото сюда<br>или нажмите, чтобы выбрать файл</span></div>';

  if (dropzone && photoInput && preview) {
    var showFile = function (file) {
      var reader = new FileReader();
      reader.onload = function (event) {
        preview.innerHTML = '<img src="' + event.target.result + '" alt="Предпросмотр фото">';
        if (removeBtn) {
          removeBtn.hidden = false;
        }
      };
      reader.readAsDataURL(file);
    };

    var openPicker = function () {
      photoInput.click();
    };

    dropzone.addEventListener('click', openPicker);
    if (chooseBtn) {
      chooseBtn.addEventListener('click', function (event) {
        event.stopPropagation();
        openPicker();
      });
    }

    photoInput.addEventListener('change', function () {
      var file = photoInput.files && photoInput.files[0];
      if (file) {
        if (removeField) {
          removeField.value = '0';
        }
        showFile(file);
      }
    });

    ['dragover', 'dragenter'].forEach(function (evt) {
      dropzone.addEventListener(evt, function (event) {
        event.preventDefault();
        dropzone.classList.add('is-dragover');
      });
    });

    ['dragleave', 'dragend'].forEach(function (evt) {
      dropzone.addEventListener(evt, function () {
        dropzone.classList.remove('is-dragover');
      });
    });

    dropzone.addEventListener('drop', function (event) {
      event.preventDefault();
      dropzone.classList.remove('is-dragover');
      var file = event.dataTransfer && event.dataTransfer.files && event.dataTransfer.files[0];
      if (file && file.type.match(/^image\/(jpeg|png|webp)$/)) {
        photoInput.files = event.dataTransfer.files;
        if (removeField) {
          removeField.value = '0';
        }
        showFile(file);
      }
    });

    if (removeBtn) {
      removeBtn.addEventListener('click', function (event) {
        event.stopPropagation();
        photoInput.value = '';
        preview.innerHTML = PLACEHOLDER_HTML;
        removeBtn.hidden = true;
        if (removeField) {
          removeField.value = '1';
        }
      });
    }
  }
})();
