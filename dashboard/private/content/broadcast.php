<?php
  if (!checkPerm($readAdmin, 'MANAGE_BROADCAST')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  if (get("target") == 'broadcast' && get("action") == 'getAll') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  }
?>
<?php if (get("target") == 'broadcast'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Duyuru</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Duyuru</li>
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
          <?php $broadcast = $db->query("SELECT * FROM Broadcast ORDER BY id ASC"); ?>
          <?php if ($broadcast->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["broadcastID", "broadcastTitle", "broadcastURL"]'>
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <div class="row align-items-center">
                      <div class="col-auto pr-0">
                        <span class="fe fe-search text-muted"></span>
                      </div>
                      <div class="col">
                        <input type="search" class="form-control form-control-flush search" name="search" placeholder="Arama Yap">
                      </div>
                    </div>
                  </div>
                  <div class="col-auto">
                    <a class="btn btn-sm btn-white" href="/yonetim-paneli/duyuru/ekle">Duyuru Ekle</a>
                  </div>
                </div>
              </div>
              <div id="loader" class="card-body p-0 is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                   </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 40px;">
                          <a href="#" class="text-muted sort" data-sort="broadcastID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="broadcastTitle">
                            Başlık
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="broadcastURL">
                            Bağlantı
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($broadcast as $readBroadcast): ?>
                        <tr>
                          <td class="broadcastID text-center" style="width: 40px;">
                            <a href="/yonetim-paneli/duyuru/duzenle/<?php echo $readBroadcast["id"]; ?>">
                              #<?php echo $readBroadcast["id"]; ?>
                            </a>
                          </td>
                          <td class="broadcastTitle">
                            <a href="/yonetim-paneli/duyuru/duzenle/<?php echo $readBroadcast["id"]; ?>">
                              <?php echo limitedContent($readBroadcast["title"], 30); ?>
                            </a>
                          </td>
                          <td class="broadcastURL">
                            <?php echo limitedContent($readBroadcast["url"], 30); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/duyuru/duzenle/<?php echo $readBroadcast["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/duyuru/sil/<?php echo $readBroadcast["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                              <i class="fe fe-trash-2"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError("Bu sayfaya ait veri bulunamadı!"); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'insert'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Duyuru Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/duyuru">Duyuru</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Ekle</li>
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
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["insertBroadcast"])) {
              if (!$csrf->validate('insertBroadcast')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("title") == null || post("url") == null) {
                echo alertError("Lütfen boş alan bırakmayınız!");
              }
              else {
                $insertBroadcast = $db->prepare("INSERT INTO Broadcast (title, url) VALUES (?, ?)");
                $insertBroadcast->execute(array(post("title"), post("url")));
                echo alertSuccess("Duyuru başarıyla eklendi!");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputTitle" class="col-sm-2 col-form-label">Başlık:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputTitle" class="form-control" name="title" placeholder="Başlığı yazınız.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputURL" class="col-sm-2 col-form-label">Bağlantı (Link):</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputURL" class="form-control" name="url" placeholder="Tıklayınca gidilecek bağlantıyı (link) yazınız.">
                  </div>
                </div>
                <?php echo $csrf->input('insertBroadcast'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertBroadcast">Ekle</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'update' && get("id")): ?>
    <?php
      $broadcast = $db->prepare("SELECT * FROM Broadcast WHERE id = ?");
      $broadcast->execute(array(get("id")));
      $readBroadcast = $broadcast->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Duyuru Düzenle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/duyuru">Duyuru</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/duyuru">Düzenle</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($broadcast->rowCount() > 0) ? limitedContent($readBroadcast["title"], 30) : "Bulunamadı!"; ?></li>
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
          <?php if ($broadcast->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateBroadcast"])) {
                if (!$csrf->validate('updateBroadcast')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("title") == null || post("url") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  $updateBroadcast = $db->prepare("UPDATE Broadcast SET title = ?, url = ? WHERE id = ?");
                  $updateBroadcast->execute(array(post("title"), post("url"), get("id")));
                  echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputTitle" class="col-sm-2 col-form-label">Başlık:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputTitle" class="form-control" name="title" placeholder="Başlığı yazınız." value="<?php echo $readBroadcast["title"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputURL" class="col-sm-2 col-form-label">Bağlantı (Link):</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputURL" class="form-control" name="url" placeholder="Tıklayınca gidilecek bağlantıyı (link) yazınız." value="<?php echo $readBroadcast["url"]; ?>">
                    </div>
                  </div>
                  <?php echo $csrf->input('updateBroadcast'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/duyuru/sil/<?php echo $readBroadcast["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateBroadcast">Değişiklikleri Kaydet</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError("Bu sayfaya ait veri bulunamadı!"); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteBroadcast = $db->prepare("DELETE FROM Broadcast WHERE id = ?");
      $deleteBroadcast->execute(array(get("id")));
      go("/yonetim-paneli/duyuru");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
