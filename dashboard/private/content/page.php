<?php
  if (!checkPerm($readAdmin, 'MANAGE_PAGES')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
?>
<?php if (get("target") == 'page'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Sayfalar</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Sayfalar</li>
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
          <?php $pages = $db->query("SELECT P.*, A.realname FROM Pages P INNER JOIN Accounts A ON P.accountID = A.id ORDER BY P.id DESC"); ?>
          <?php if ($pages->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["pageID", "pageTitle", "pageUsername", "pageCreationDate"]'>
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
                    <a class="btn btn-sm btn-white" href="/yonetim-paneli/sayfa/ekle">Sayfa Ekle</a>
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
                          <a href="#" class="text-muted sort" data-sort="pageID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="pageTitle">
                            Başlık
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="pageUsername">
                            Yazar
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="pageCreationDate">
                            Tarih
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($pages as $readPages): ?>
                        <tr>
                          <td class="pageID text-center" style="width: 40px;">
                            <a href="/yonetim-paneli/sayfa/duzenle/<?php echo $readPages["id"]; ?>">
                              #<?php echo $readPages["id"]; ?>
                            </a>
                          </td>
                          <td class="pageTitle">
                            <a href="/yonetim-paneli/sayfa/duzenle/<?php echo $readPages["id"]; ?>">
                              <?php echo $readPages["title"]; ?>
                            </a>
                          </td>
                          <td class="pageUsername">
                            <?php echo $readPages["realname"]; ?>
                          </td>
                          <td class="pageCreationDate">
                            <?php echo convertTime($readPages["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/sayfa/duzenle/<?php echo $readPages["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/sayfa/<?php echo $readPages["id"]; ?>/<?php echo $readPages["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/sayfa/sil/<?php echo $readPages["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
                  <h2 class="header-title">Sayfa Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/sayfa">Sayfalar</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Sayfa Ekle</li>
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
            if (isset($_POST["insertPages"])) {
              if (!$csrf->validate('insertPages')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("title") == null || post("content") == null) {
                echo alertError("Lütfen boş alan bırakmayınız!");
              }
              else {
                $insertPages = $db->prepare("INSERT INTO Pages (accountID, title, slug, content, creationDate) VALUES (?, ?, ?, ?, ?)");
                $insertPages->execute(array($readAdmin["id"], post("title"), convertURL(post("title")), filteredContent($_POST["content"]), date("Y-m-d H:i:s")));
                echo alertSuccess("Sayfa başarıyla eklendi!");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputTitle" class="col-sm-2 col-form-label">Başlık:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputTitle" class="form-control" name="title" placeholder="Sayfa başlığı giriniz.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaContent" class="col-sm-2 col-form-label">İçerik:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaContent" class="form-control" data-toggle="textEditor" name="content" placeholder="Sayfa içeriğini yazınız."></textarea>
                  </div>
                </div>
                <?php echo $csrf->input('insertPages'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertPages">Ekle</button>
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
      $page = $db->prepare("SELECT * FROM Pages WHERE id = ?");
      $page->execute(array(get("id")));
      $readPage = $page->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Sayfa Düzenle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/sayfa">Sayfalar</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/sayfa">Sayfa Düzenle</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($page->rowCount() > 0) ? limitedContent($readPage["title"], 30): "Bulunamadı!"; ?></li>
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
          <?php if ($page->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updatePages"])) {
                if (!$csrf->validate('updatePages')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("title") == null || post("content") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  $updatePages = $db->prepare("UPDATE Pages SET title = ?, slug = ?, content = ? WHERE id = ?");
                  $updatePages->execute(array(post("title"), convertURL(post("title")), filteredContent($_POST["content"]), get("id")));
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
                      <input type="text" id="inputTitle" class="form-control" name="title" placeholder="Sayfa başlığı giriniz." value="<?php echo $readPage["title"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="textareaContent" class="col-sm-2 col-form-label">İçerik:</label>
                    <div class="col-sm-10">
                      <textarea id="textareaContent" class="form-control" data-toggle="textEditor" name="content" placeholder="Sayfa içeriğini yazınız."><?php echo $readPage["content"]; ?></textarea>
                    </div>
                  </div>
                  <?php echo $csrf->input('updatePages'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/sayfa/sil/<?php echo $readPage["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/sayfa/<?php echo $readPage["id"]; ?>/<?php echo $readPage["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                        <i class="fe fe-eye"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updatePages">Değişiklikleri Kaydet</button>
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
      $deletePage = $db->prepare("DELETE FROM Pages WHERE id = ?");
      $deletePage->execute(array(get("id")));
      go("/yonetim-paneli/sayfa");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
