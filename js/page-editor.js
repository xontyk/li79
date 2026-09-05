(function () {
  'use strict';

  var container = document.getElementById('pageEditor');
  var hiddenField = document.getElementById('contentField');
  var form = document.getElementById('pageEditForm');

  if (!container || !hiddenField || !form || typeof Quill === 'undefined') {
    return;
  }

  var quill = new Quill(container, {
    theme: 'snow',
    modules: {
      toolbar: {
        container: [
          [{ header: [2, 3, false] }],
          ['bold', 'italic', 'underline'],
          ['link', 'image'],
          [{ list: 'ordered' }, { list: 'bullet' }],
          ['clean']
        ],
        handlers: {
          image: insertImage
        }
      }
    }
  });

  function insertImage() {
    var input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/jpeg,image/png,image/webp';

    input.addEventListener('change', function () {
      var file = input.files && input.files[0];
      if (!file) {
        return;
      }

      var range = quill.getSelection(true) || { index: quill.getLength() };
      var placeholder = 'Загрузка изображения…';
      quill.insertText(range.index, placeholder, { italic: true }, 'user');
      quill.setSelection(range.index + placeholder.length);

      var formData = new FormData();
      formData.append('file', file);
      formData.append('csrf_token', window.PAGE_EDITOR_CSRF || '');

      fetch(window.PAGE_EDITOR_UPLOAD_URL, { method: 'POST', body: formData })
        .then(function (response) { return response.json(); })
        .then(function (data) {
          quill.deleteText(range.index, placeholder.length, 'user');
          if (data && data.url) {
            quill.insertEmbed(range.index, 'image', data.url, 'user');
            quill.setSelection(range.index + 1);
          } else {
            alert((data && data.error) || 'Не удалось загрузить изображение.');
          }
        })
        .catch(function () {
          quill.deleteText(range.index, placeholder.length, 'user');
          alert('Не удалось загрузить изображение. Проверьте соединение и попробуйте ещё раз.');
        });
    });

    input.click();
  }

  form.addEventListener('submit', function () {
    hiddenField.value = quill.root.innerHTML;
  });
})();
