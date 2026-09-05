</main>
<footer class="site-footer">
  <div class="container footer-inner">
    <div class="footer-col">
      <h3>Лицей-интернат №79</h3>
      <p><?= e(SITE_ADDRESS) ?></p>
    </div>
    <div class="footer-col">
      <h4>Контакты</h4>
      <p><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', SITE_PHONE)) ?>"><?= e(SITE_PHONE) ?></a></p>
      <p><a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></p>
    </div>
    <div class="footer-col">
      <h4>Мы в Telegram</h4>
      <p>Новости, фото и объявления лицея</p>
      <a href="<?= e(SITE_TELEGRAM) ?>" class="btn btn-sm btn-accent" target="_blank" rel="noopener">Подписаться</a>
    </div>
  </div>
  <div class="container footer-bottom">
    <p>&copy; <?= date('Y') ?> Лицей-интернат №79. Все права защищены.</p>
  </div>
</footer>
<script src="/js/script.js" defer></script>
</body>
</html>
