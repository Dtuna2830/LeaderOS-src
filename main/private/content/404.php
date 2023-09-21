<?php
  header('HTTP/1.1 404 Not Found');
  header('Status: 404 Not Found');
?>
<section class="section error-404-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12 text-center">
        <h1>404</h1>
        <p>Bu sayfa bulunamadı!</p>
        <a class="btn btn-rounded btn-primary" href="/">Ana Sayfa</a>
        <?php if (isset($_SESSION["login"])): ?>
          <?php if (checkStaff($readAccount)): ?>
            <a class="btn btn-rounded btn-success" href="/yonetim-paneli">Yönetim Paneli</a>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
