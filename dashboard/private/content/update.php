<?php
  if (!checkPerm($readAdmin, 'MANAGE_UPDATES')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/update.js');
?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="header">
        <div class="header-body">
          <div class="row align-items-center">
            <div class="col">
              <h2 class="header-title">Güncelleme</h2>
            </div>
            <div class="col-auto">
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Güncelleme</li>
                </ol>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col">
              <h4 class="card-header-title">
                Güncelleme Durumu
              </h4>
            </div>
            <div class="col-auto">
              <?php if ($needUpdate == true): ?>
                <span class="badge badge-pill badge-danger">Güncelleme Gerekli</span>
              <?php else: ?>
                <span class="badge badge-pill badge-success">Güncel</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div id="loader" class="card-body">
          <div id="spinner" style="display: none;">
            <div class="spinner-border" role="status">
              <span class="sr-only">-/-</span>
            </div>
          </div>
          <?php if ($needUpdate == true): ?>
            <div id="updateBlock">
              <div class="row">
                <div class="col d-flex align-items-center text-muted">
                  <span>Yeni bir güncelleme mevcut! Yeni sürüm olan <a rel="external" href="https://www.leaderos.com.tr/haberler">LeaderOS v<?php echo $newVersion; ?></a> güncellemesini yapmak için <strong class="text-success">Güncelle</strong> tuşuna basınız.</span>
                </div>
                <div class="col-auto d-flex align-items-center">
                  <button type="button" id="updateButton" class="btn btn-rounded btn-success">Güncelle</button>
                </div>
              </div>
            </div>
          <?php else: ?>
            <span class="text-muted">En güncel sürümü kullanıyorsunuz! Güncelleme geldiğinde size bildirim gönderilecektir.</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
