<?php
/**
 * Разовый инструмент: переносит на блочный конструктор содержимое,
 * которое раньше было зашито прямо в шаблонах (галерея на «Жизни лицея»,
 * распорядок дня, статистика на главной и т.д.). Нужен только тем, у кого
 * сайт был установлен до появления этих блоков — на новой установке всё
 * это уже приходит в sql/schema.sql.
 *
 * Каждая кнопка полностью ЗАМЕНЯЕТ содержимое конкретной страницы на
 * готовый набор блоков. Уже написанный вручную текст на этой странице
 * будет заменён — используйте с осторожностью и по одной странице.
 */

require_once __DIR__ . '/../includes/auth.php';
require_login();

$slugs = ['home', 'about', 'education', 'admission', 'life'];
$labels = [
    'home' => 'Главная',
    'about' => 'О лицее',
    'education' => 'Обучение',
    'admission' => 'Поступающим',
    'life' => 'Жизнь лицея',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $slug = $_POST['slug'] ?? '';

    if (!in_array($slug, $slugs, true)) {
        set_flash('error', 'Неизвестная страница.');
        redirect('migrate.php');
    }

    $blocks = default_page_blocks($slug);
    $contentJson = sanitize_page_blocks($blocks);

    $stmt = $pdo->prepare('UPDATE pages SET content = ? WHERE slug = ?');
    $stmt->execute([$contentJson, $slug]);

    set_flash('success', 'Страница «' . ($labels[$slug] ?? $slug) . '» обновлена — можно открыть её в редакторе и менять как угодно.');
    redirect('migrate.php');
}

$pagesBySlug = [];
foreach ($slugs as $slug) {
    $pagesBySlug[$slug] = get_page($pdo, $slug);
}

$pageTitle = 'Перенос старого содержимого — ' . SITE_NAME;
require __DIR__ . '/../includes/admin_header.php';
?>
<h1>Перенос старого содержимого в блоки</h1>

<div class="admin-form-card admin-form-card-wide">
  <p>Раньше галерея, распорядок дня, карточки-преимущества и статистика на нескольких страницах были зашиты прямо в код шаблона — их нельзя было редактировать в админ-панели. Теперь всё это — обычные блоки.</p>
  <p class="form-hint" style="margin-top:0;">⚠️ Кнопка ниже <strong>полностью заменит</strong> содержимое страницы на готовый набор блоков (то же, что было на сайте раньше, но теперь редактируемое). Если вы уже что-то написали на этой странице через редактор — оно будет затёрто. Используйте по одной странице и проверяйте результат.</p>

  <div class="table-wrap">
    <table class="admin-table">
      <thead><tr><th>Страница</th><th>Сейчас блоков</th><th>Действие</th></tr></thead>
      <tbody>
        <?php foreach ($slugs as $slug): ?>
          <?php $page = $pagesBySlug[$slug]; ?>
          <tr>
            <td><?= e($labels[$slug]) ?></td>
            <td><?= $page ? count(decode_page_blocks($page['content'])) : '—' ?></td>
            <td class="actions">
              <form method="post" class="inline-form" onsubmit="return confirm('Заменить содержимое страницы «<?= e(addslashes($labels[$slug])) ?>» на готовый набор блоков? Текущее содержимое этой страницы будет потеряно.');">
                <?= csrf_field() ?>
                <input type="hidden" name="slug" value="<?= e($slug) ?>">
                <button type="submit" class="btn btn-sm btn-primary">Восстановить блоки по умолчанию</button>
              </form>
              <?php if ($page): ?>
                <a href="page-edit.php?id=<?= (int) $page['id'] ?>" class="btn btn-sm btn-outline">Открыть редактор</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
