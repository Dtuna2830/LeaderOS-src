<?php
  $page = $db->prepare("SELECT * FROM Pages WHERE id = ?");
  $page->execute(array(get("id")));
  $readPage = $page->fetch();
?>
<section class="section page-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Ana Sayfa</a></li>
            <?php if (isset($_GET["id"])): ?>
              <li class="breadcrumb-item"><a href="/">Sayfa</a></li>
              <?php if ($page->rowCount() > 0): ?>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $readPage["title"]; ?></li>
              <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page">Bulunamadı</li>
              <?php endif; ?>
            <?php else: ?>
              <li class="breadcrumb-item active" aria-current="page">Sayfa</li>
            <?php endif; ?>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <?php if ($page->rowCount() > 0): ?>
          <div class="card">
            <div class="card-header">
              <?php echo $readPage["title"]; ?>
            </div>
            <div class="card-body">
              <?php echo showEmoji($readPage["content"]); ?>
            </div>
          </div>
        <?php else: ?>
          <?php echo alertError("Bu sayfaya ait içerik bulunamadı!"); ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
