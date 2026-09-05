(function () {
  'use strict';

  var listEl = document.getElementById('blockList');
  var jsonField = document.getElementById('blocksJsonField');
  var form = document.getElementById('pageEditForm');

  if (!listEl || !jsonField || !form) {
    return;
  }

  var TYPE_LABELS = {
    heading: '🔠 Заголовок',
    paragraph: '📝 Текст',
    image: '🖼 Фото',
    cover: '🌄 Баннер с фоном',
    gallery: '🖼️ Галерея фото',
    cards: '🗂 Карточки',
    stats: '📊 Статистика',
    button: '🔘 Кнопка',
    quote: '❝ Цитата',
    list: '📋 Список',
    legacy_html: '⚠️ Старый блок (HTML)'
  };

  var DEFAULTS = {
    heading: { type: 'heading', level: 2, text: '' },
    paragraph: { type: 'paragraph', html: '' },
    image: { type: 'image', url: '', alt: '', caption: '' },
    cover: { type: 'cover', imageUrl: '', overlay: 'dark', heading: '', subtext: '', buttonText: '', buttonUrl: '' },
    gallery: { type: 'gallery', columns: 3, items: [{ url: '', caption: '' }] },
    cards: { type: 'cards', columns: 4, items: [{ icon: '', title: '', text: '' }] },
    stats: { type: 'stats', items: [{ icon: '', number: '', dynamic: '', label: '' }] },
    button: { type: 'button', text: 'Подробнее', url: '', style: 'primary' },
    quote: { type: 'quote', text: '', author: '' },
    list: { type: 'list', style: 'bullet', items: [''] }
  };

  function uid() {
    return 'b' + Math.random().toString(36).slice(2, 10);
  }

  var state = {
    blocks: (window.INITIAL_BLOCKS || []).map(function (block) {
      block = Object.assign({}, block);
      block._id = uid();
      return block;
    })
  };

  function el(tag, className, html) {
    var node = document.createElement(tag);
    if (className) node.className = className;
    if (html !== undefined) node.innerHTML = html;
    return node;
  }

  function syncJsonField() {
    var clean = state.blocks.map(function (block) {
      var copy = Object.assign({}, block);
      delete copy._id;
      return copy;
    });
    jsonField.value = JSON.stringify(clean);
  }

  form.addEventListener('submit', syncJsonField);

  function render() {
    listEl.innerHTML = '';

    if (!state.blocks.length) {
      listEl.appendChild(el('p', 'empty-state', 'На странице пока нет блоков. Добавьте первый блок ниже.'));
    }

    state.blocks.forEach(function (block, index) {
      listEl.appendChild(renderCard(block, index));
    });

    syncJsonField();
  }

  function renderCard(block, index) {
    var card = el('div', 'block-card');

    var head = el('div', 'block-card-head');
    var label = el('span', 'block-card-label', TYPE_LABELS[block.type] || block.type);
    var controls = el('div', 'block-card-controls');

    var upBtn = el('button', 'block-btn', '↑');
    upBtn.type = 'button';
    upBtn.title = 'Переместить вверх';
    upBtn.disabled = index === 0;
    upBtn.addEventListener('click', function () { moveBlock(index, -1); });

    var downBtn = el('button', 'block-btn', '↓');
    downBtn.type = 'button';
    downBtn.title = 'Переместить вниз';
    downBtn.disabled = index === state.blocks.length - 1;
    downBtn.addEventListener('click', function () { moveBlock(index, 1); });

    var delBtn = el('button', 'block-btn block-btn-danger', '✕');
    delBtn.type = 'button';
    delBtn.title = 'Удалить блок';
    delBtn.addEventListener('click', function () { deleteBlock(index); });

    controls.appendChild(upBtn);
    controls.appendChild(downBtn);
    controls.appendChild(delBtn);
    head.appendChild(label);
    head.appendChild(controls);
    card.appendChild(head);

    var body = el('div', 'block-card-body');
    body.appendChild(buildBody(block));
    card.appendChild(body);

    return card;
  }

  function moveBlock(index, dir) {
    var target = index + dir;
    if (target < 0 || target >= state.blocks.length) return;
    var tmp = state.blocks[index];
    state.blocks[index] = state.blocks[target];
    state.blocks[target] = tmp;
    render();
  }

  function deleteBlock(index) {
    if (!confirm('Удалить этот блок? Это действие нельзя отменить.')) return;
    state.blocks.splice(index, 1);
    render();
  }

  function field(labelText, inputEl) {
    var wrap = el('label', 'block-field', labelText);
    wrap.appendChild(inputEl);
    return wrap;
  }

  function textInput(value, onInput, placeholder) {
    var input = document.createElement('input');
    input.type = 'text';
    input.value = value || '';
    if (placeholder) input.placeholder = placeholder;
    input.addEventListener('input', function () { onInput(input.value); });
    return input;
  }

  function textArea(value, onInput, rows) {
    var textarea = document.createElement('textarea');
    textarea.value = value || '';
    textarea.rows = rows || 3;
    textarea.addEventListener('input', function () { onInput(textarea.value); });
    return textarea;
  }

  function selectInput(options, value, onChange) {
    var select = document.createElement('select');
    options.forEach(function (opt) {
      var option = document.createElement('option');
      option.value = opt.value;
      option.textContent = opt.label;
      if (opt.value === value) option.selected = true;
      select.appendChild(option);
    });
    select.addEventListener('change', function () { onChange(select.value); });
    return select;
  }

  function buildBody(block) {
    var wrap = el('div');
    switch (block.type) {
      case 'heading': wrap.appendChild(buildHeading(block)); break;
      case 'paragraph': wrap.appendChild(buildParagraph(block)); break;
      case 'image': wrap.appendChild(buildImage(block)); break;
      case 'cover': wrap.appendChild(buildCover(block)); break;
      case 'gallery': wrap.appendChild(buildGallery(block)); break;
      case 'cards': wrap.appendChild(buildCards(block)); break;
      case 'stats': wrap.appendChild(buildStats(block)); break;
      case 'button': wrap.appendChild(buildButton(block)); break;
      case 'quote': wrap.appendChild(buildQuote(block)); break;
      case 'list': wrap.appendChild(buildList(block)); break;
      case 'legacy_html': wrap.appendChild(buildLegacy(block)); break;
      default: wrap.textContent = 'Неизвестный тип блока.';
    }
    return wrap;
  }

  function buildHeading(block) {
    var wrap = el('div', 'block-fields-row');
    wrap.appendChild(field('Текст заголовка', textInput(block.text, function (v) { block.text = v; }, 'Например: Наши достижения')));
    wrap.appendChild(field('Размер', selectInput([
      { value: '2', label: 'Крупный' },
      { value: '3', label: 'Средний' }
    ], String(block.level || 2), function (v) { block.level = parseInt(v, 10); })));
    return wrap;
  }

  function buildParagraph(block) {
    var wrap = el('div');
    var editorHost = el('div', 'block-quill');
    editorHost.innerHTML = block.html || '';
    wrap.appendChild(editorHost);

    // Инициализация мини-редактора откладывается на следующий тик, чтобы
    // элемент успел попасть в DOM (Quill требует подключённый контейнер).
    setTimeout(function () {
      var quill = new Quill(editorHost, {
        theme: 'snow',
        modules: { toolbar: [['bold', 'italic', 'underline'], ['link'], ['clean']] }
      });
      quill.on('text-change', function () {
        block.html = quill.root.innerHTML;
      });
    }, 0);

    return wrap;
  }

  /**
   * Универсальная зона загрузки фото (клик + drag-and-drop), общая для
   * обычного блока "Фото" и фона в блоке "Баннер с фоном".
   * onUploaded(url) вызывается после успешной загрузки на сервер.
   */
  function createImageUploader(currentUrl, onUploaded, placeholderText) {
    var dropzone = el('div', 'photo-dropzone block-image-dropzone');
    var input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/jpeg,image/png,image/webp';
    input.hidden = true;

    var preview = el('div', 'photo-dropzone-preview');
    preview.innerHTML = currentUrl
      ? '<img src="' + currentUrl + '" alt="">'
      : '<div class="photo-dropzone-placeholder"><span>🖼</span><span>' + placeholderText + '</span></div>';

    dropzone.appendChild(input);
    dropzone.appendChild(preview);

    dropzone.addEventListener('click', function () { input.click(); });

    ['dragover', 'dragenter'].forEach(function (evt) {
      dropzone.addEventListener(evt, function (e) { e.preventDefault(); dropzone.classList.add('is-dragover'); });
    });
    ['dragleave', 'dragend'].forEach(function (evt) {
      dropzone.addEventListener(evt, function () { dropzone.classList.remove('is-dragover'); });
    });

    var uploadFile = function (file) {
      if (!file || !file.type.match(/^image\/(jpeg|png|webp)$/)) return;
      preview.innerHTML = '<div class="photo-dropzone-placeholder"><span>⏳</span><span>Загрузка…</span></div>';

      var formData = new FormData();
      formData.append('file', file);
      formData.append('csrf_token', window.PAGE_EDITOR_CSRF || '');

      fetch('upload-image.php', { method: 'POST', body: formData })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data && data.url) {
            onUploaded(data.url);
            preview.innerHTML = '<img src="' + data.url + '" alt="">';
          } else {
            alert((data && data.error) || 'Не удалось загрузить изображение.');
            render();
          }
        })
        .catch(function () {
          alert('Не удалось загрузить изображение. Проверьте соединение.');
          render();
        });
    };

    input.addEventListener('change', function () {
      var file = input.files && input.files[0];
      if (file) uploadFile(file);
    });

    dropzone.addEventListener('drop', function (e) {
      e.preventDefault();
      dropzone.classList.remove('is-dragover');
      var file = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
      if (file) uploadFile(file);
    });

    return dropzone;
  }

  function buildImage(block) {
    var wrap = el('div');
    var dropzone = createImageUploader(
      block.url,
      function (url) { block.url = url; },
      'Перетащите фото сюда<br>или нажмите, чтобы выбрать файл'
    );

    wrap.appendChild(el('label', 'block-field', 'Фото'));
    wrap.appendChild(dropzone);
    wrap.appendChild(field('Подпись под фото (необязательно)', textInput(block.caption, function (v) { block.caption = v; })));
    wrap.appendChild(field('Alt-текст для доступности и SEO', textInput(block.alt, function (v) { block.alt = v; }, 'Например: Ученики лицея на олимпиаде')));

    return wrap;
  }

  function buildCover(block) {
    var wrap = el('div');
    var dropzone = createImageUploader(
      block.imageUrl,
      function (url) { block.imageUrl = url; },
      'Перетащите фоновую картинку сюда<br>или нажмите, чтобы выбрать файл'
    );

    wrap.appendChild(el('label', 'block-field', 'Фоновая картинка'));
    wrap.appendChild(dropzone);
    wrap.appendChild(field('Затемнение фона', selectInput([
      { value: 'dark', label: 'Тёмное (текст будет белым)' },
      { value: 'light', label: 'Светлое (текст будет тёмным)' },
      { value: 'none', label: 'Без затемнения' }
    ], block.overlay || 'dark', function (v) { block.overlay = v; })));
    wrap.appendChild(field('Заголовок', textInput(block.heading, function (v) { block.heading = v; }, 'Например: День открытых дверей')));
    wrap.appendChild(field('Подпись под заголовком', textArea(block.subtext, function (v) { block.subtext = v; }, 2)));
    var rowWrap = el('div', 'block-fields-row');
    rowWrap.appendChild(field('Текст кнопки (необязательно)', textInput(block.buttonText, function (v) { block.buttonText = v; })));
    rowWrap.appendChild(field('Ссылка кнопки', textInput(block.buttonUrl, function (v) { block.buttonUrl = v; }, '/admission.php')));
    wrap.appendChild(rowWrap);

    return wrap;
  }

  function buildGallery(block) {
    var wrap = el('div');
    if (!Array.isArray(block.items) || !block.items.length) {
      block.items = [{ url: '', caption: '' }];
    }

    wrap.appendChild(field('Колонок в ряд', selectInput([
      { value: '2', label: '2' },
      { value: '3', label: '3' },
      { value: '4', label: '4' }
    ], String(block.columns || 3), function (v) { block.columns = parseInt(v, 10); })));

    var itemsWrap = el('div', 'block-repeat-items');
    block.items.forEach(function (item, i) {
      var row = el('div', 'block-repeat-item');
      var dropzone = createImageUploader(
        item.url,
        function (url) { item.url = url; },
        'Нажмите или перетащите фото'
      );
      row.appendChild(dropzone);
      row.appendChild(field('Подпись (необязательно)', textInput(item.caption, function (v) { item.caption = v; })));

      var removeBtn = el('button', 'btn btn-sm btn-outline', '✕ Убрать это фото');
      removeBtn.type = 'button';
      removeBtn.addEventListener('click', function () {
        block.items.splice(i, 1);
        render();
      });
      row.appendChild(removeBtn);
      itemsWrap.appendChild(row);
    });
    wrap.appendChild(itemsWrap);

    var addBtn = el('button', 'btn btn-sm btn-outline', '+ Добавить фото в галерею');
    addBtn.type = 'button';
    addBtn.addEventListener('click', function () {
      block.items.push({ url: '', caption: '' });
      render();
    });
    wrap.appendChild(addBtn);

    return wrap;
  }

  function buildCards(block) {
    var wrap = el('div');
    if (!Array.isArray(block.items) || !block.items.length) {
      block.items = [{ icon: '', title: '', text: '' }];
    }

    wrap.appendChild(field('Колонок в ряд', selectInput([
      { value: '2', label: '2' },
      { value: '3', label: '3' },
      { value: '4', label: '4' }
    ], String(block.columns || 4), function (v) { block.columns = parseInt(v, 10); })));

    var itemsWrap = el('div', 'block-repeat-items');
    block.items.forEach(function (item, i) {
      var row = el('div', 'block-repeat-item');
      var fieldsRow = el('div', 'block-fields-row');
      fieldsRow.appendChild(field('Иконка (эмодзи, необязательно)', textInput(item.icon, function (v) { item.icon = v; }, '🏆')));
      fieldsRow.appendChild(field('Заголовок карточки', textInput(item.title, function (v) { item.title = v; })));
      row.appendChild(fieldsRow);
      row.appendChild(field('Текст', textArea(item.text, function (v) { item.text = v; }, 2)));

      var removeBtn = el('button', 'btn btn-sm btn-outline', '✕ Удалить карточку');
      removeBtn.type = 'button';
      removeBtn.addEventListener('click', function () {
        block.items.splice(i, 1);
        render();
      });
      row.appendChild(removeBtn);
      itemsWrap.appendChild(row);
    });
    wrap.appendChild(itemsWrap);

    var addBtn = el('button', 'btn btn-sm btn-outline', '+ Добавить карточку');
    addBtn.type = 'button';
    addBtn.addEventListener('click', function () {
      block.items.push({ icon: '', title: '', text: '' });
      render();
    });
    wrap.appendChild(addBtn);

    return wrap;
  }

  function buildStats(block) {
    var wrap = el('div');
    wrap.appendChild(el('p', 'form-hint', 'Если выбрать «Считать автоматически», число будет обновляться само по количеству записей на сайте — вручную вводить его не нужно.'));

    if (!Array.isArray(block.items) || !block.items.length) {
      block.items = [{ icon: '', number: '', dynamic: '', label: '' }];
    }

    var itemsWrap = el('div', 'block-repeat-items');
    block.items.forEach(function (item, i) {
      var row = el('div', 'block-repeat-item');
      var fieldsRow = el('div', 'block-fields-row');
      fieldsRow.appendChild(field('Иконка (эмодзи)', textInput(item.icon, function (v) { item.icon = v; }, '🏆')));
      fieldsRow.appendChild(field('Число показывать', selectInput([
        { value: '', label: 'Задать вручную' },
        { value: 'teachers_count', label: 'Считать автоматически: учителя' },
        { value: 'winners_count', label: 'Считать автоматически: олимпиадники' }
      ], item.dynamic || '', function (v) { item.dynamic = v; })));
      row.appendChild(fieldsRow);
      var fieldsRow2 = el('div', 'block-fields-row');
      fieldsRow2.appendChild(field('Число (если не автоматически)', textInput(item.number, function (v) { item.number = v; }, '95%')));
      fieldsRow2.appendChild(field('Подпись', textInput(item.label, function (v) { item.label = v; }, 'лет работы лицея')));
      row.appendChild(fieldsRow2);

      var removeBtn = el('button', 'btn btn-sm btn-outline', '✕ Удалить показатель');
      removeBtn.type = 'button';
      removeBtn.addEventListener('click', function () {
        block.items.splice(i, 1);
        render();
      });
      row.appendChild(removeBtn);
      itemsWrap.appendChild(row);
    });
    wrap.appendChild(itemsWrap);

    var addBtn = el('button', 'btn btn-sm btn-outline', '+ Добавить показатель');
    addBtn.type = 'button';
    addBtn.addEventListener('click', function () {
      block.items.push({ icon: '', number: '', dynamic: '', label: '' });
      render();
    });
    wrap.appendChild(addBtn);

    return wrap;
  }

  function buildButton(block) {
    var wrap = el('div', 'block-fields-row');
    wrap.appendChild(field('Текст кнопки', textInput(block.text, function (v) { block.text = v; })));
    wrap.appendChild(field('Ссылка', textInput(block.url, function (v) { block.url = v; }, '/admission.php')));
    wrap.appendChild(field('Вид', selectInput([
      { value: 'primary', label: 'Основная (синяя)' },
      { value: 'outline', label: 'Контурная' }
    ], block.style || 'primary', function (v) { block.style = v; })));
    return wrap;
  }

  function buildQuote(block) {
    var wrap = el('div');
    wrap.appendChild(field('Текст цитаты', textArea(block.text, function (v) { block.text = v; }, 3)));
    wrap.appendChild(field('Автор (необязательно)', textInput(block.author, function (v) { block.author = v; })));
    return wrap;
  }

  function buildList(block) {
    var wrap = el('div');
    wrap.appendChild(field('Вид списка', selectInput([
      { value: 'bullet', label: 'Маркированный' },
      { value: 'numbered', label: 'Нумерованный' }
    ], block.style || 'bullet', function (v) { block.style = v; })));

    if (!Array.isArray(block.items) || !block.items.length) {
      block.items = [''];
    }

    var itemsWrap = el('div', 'block-list-items');
    block.items.forEach(function (itemText, i) {
      var row = el('div', 'block-list-item-row');
      var input = textInput(itemText, function (v) { block.items[i] = v; }, 'Пункт списка');
      var removeBtn = el('button', 'block-btn block-btn-danger', '✕');
      removeBtn.type = 'button';
      removeBtn.title = 'Удалить пункт';
      removeBtn.addEventListener('click', function () {
        block.items.splice(i, 1);
        render();
      });
      row.appendChild(input);
      row.appendChild(removeBtn);
      itemsWrap.appendChild(row);
    });
    wrap.appendChild(itemsWrap);

    var addItemBtn = el('button', 'btn btn-sm btn-outline', '+ Добавить пункт');
    addItemBtn.type = 'button';
    addItemBtn.addEventListener('click', function () {
      block.items.push('');
      render();
    });
    wrap.appendChild(addItemBtn);

    return wrap;
  }

  function buildLegacy(block) {
    var wrap = el('div');
    wrap.appendChild(el('p', 'form-hint', 'Это содержимое страницы из предыдущей версии сайта. Можно отредактировать HTML напрямую, а новые блоки — добавлять ниже.'));
    wrap.appendChild(field('HTML-код', textArea(block.html, function (v) { block.html = v; }, 8)));
    return wrap;
  }

  document.querySelectorAll('[data-add-block]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var type = btn.getAttribute('data-add-block');
      var defaults = DEFAULTS[type];
      if (!defaults) return;
      var block = Object.assign({ _id: uid() }, JSON.parse(JSON.stringify(defaults)));
      state.blocks.push(block);
      render();
      var cards = listEl.querySelectorAll('.block-card');
      var lastCard = cards[cards.length - 1];
      if (lastCard) lastCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
  });

  // Кнопка «Предпросмотр»: открывает страницу в новой вкладке в том виде,
  // как она будет выглядеть на сайте — без сохранения текущей формы.
  var previewBtn = document.getElementById('previewPageBtn');
  var previewForm = document.getElementById('previewForm');
  var previewTitleField = document.getElementById('previewTitleField');
  var previewBlocksField = document.getElementById('previewBlocksField');
  var titleField = form.querySelector('[name="title"]');

  if (previewBtn && previewForm && previewTitleField && previewBlocksField && titleField) {
    previewBtn.addEventListener('click', function () {
      syncJsonField();
      previewTitleField.value = titleField.value;
      previewBlocksField.value = jsonField.value;
      previewForm.submit();
    });
  }

  render();
})();
