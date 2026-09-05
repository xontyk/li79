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
    button: '🔘 Кнопка',
    quote: '❝ Цитата',
    list: '📋 Список',
    legacy_html: '⚠️ Старый блок (HTML)'
  };

  var DEFAULTS = {
    heading: { type: 'heading', level: 2, text: '' },
    paragraph: { type: 'paragraph', html: '' },
    image: { type: 'image', url: '', alt: '', caption: '' },
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

  function buildImage(block) {
    var wrap = el('div');
    var dropzone = el('div', 'photo-dropzone block-image-dropzone');
    var input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/jpeg,image/png,image/webp';
    input.hidden = true;

    var preview = el('div', 'photo-dropzone-preview');
    preview.innerHTML = block.url
      ? '<img src="' + block.url + '" alt="">'
      : '<div class="photo-dropzone-placeholder"><span>🖼</span><span>Перетащите фото сюда<br>или нажмите, чтобы выбрать файл</span></div>';

    dropzone.appendChild(input);
    dropzone.appendChild(preview);

    var openPicker = function () { input.click(); };
    dropzone.addEventListener('click', openPicker);

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
            block.url = data.url;
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

    wrap.appendChild(el('label', 'block-field', 'Фото'));
    wrap.appendChild(dropzone);
    wrap.appendChild(field('Подпись под фото (необязательно)', textInput(block.caption, function (v) { block.caption = v; })));
    wrap.appendChild(field('Alt-текст для доступности и SEO', textInput(block.alt, function (v) { block.alt = v; }, 'Например: Ученики лицея на олимпиаде')));

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

  render();
})();
