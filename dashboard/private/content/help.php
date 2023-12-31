<?php
  if (!checkPerm($readAdmin, 'MANAGE_HELP_CENTER')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
?>
<?php if (get("target") == 'article'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Yardım Merkezi</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Yardım Merkezi</li>
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
          <?php $helpArticles = $db->query("SELECT HA.id FROM HelpArticles HA INNER JOIN Accounts A ON HA.accountID = A.id INNER JOIN HelpTopics HT ON HA.topicID = HT.id ORDER BY HA.id DESC"); ?>
          <?php if ($helpArticles->rowCount() > 0): ?>
            <?php
              if (get("page")) {
                if (!is_numeric(get("page"))) {
                  $_GET["page"] = 1;
                }
                $page = intval(get("page"));
              }
              else {
                $page = 1;
              }

              $visiblePageCount = 5;
              $limit = 50;

              $itemsCount = $helpArticles->rowCount();
              $pageCount = ceil($itemsCount/$limit);
              if ($page > $pageCount) {
                $page = 1;
              }
              $visibleItemsCount = $page * $limit - $limit;
              $helpArticles = $db->query("SELECT HA.*, A.realname, HT.name as topicName FROM HelpArticles HA INNER JOIN Accounts A ON HA.accountID = A.id INNER JOIN HelpTopics HT ON HA.topicID = HT.id ORDER BY HA.id DESC LIMIT $visibleItemsCount, $limit");

              if (isset($_POST["query"])) {
                if (post("query") != null) {
                  $helpArticles = $db->prepare("SELECT HA.*, A.realname, HT.name as topicName FROM HelpArticles HA INNER JOIN Accounts A ON HA.accountID = A.id INNER JOIN HelpTopics HT ON HA.topicID = HT.id WHERE HA.title LIKE :search OR HT.name LIKE :search ORDER BY HA.id DESC");
                  $helpArticles->execute(array(
                    "search" => '%'.post("query").'%'
                  ));
                }
              }
            ?>
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <form action="" method="post" class="d-flex align-items-center w-100">
                    <div class="col">
                      <div class="row align-items-center">
                        <div class="col-auto pr-0">
                          <span class="fe fe-search text-muted"></span>
                        </div>
                        <div class="col">
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="Arama Yap (Başlık veya Konu)" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success">Ara</button>
                      <a class="btn btn-sm btn-white" href="/yonetim-paneli/yardim/ekle">Makale Ekle</a>
                    </div>
                  </form>
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
                        <th class="text-center" style="width: 40px;">#ID</th>
                        <th>Başlık</th>
                        <th>Yazar</th>
                        <th>Konu</th>
                        <th>Görüntülenme</th>
                        <th>Fayda</th>
                        <th>Tarih</th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($helpArticles as $readHelpArticles): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            <a href="/yonetim-paneli/yardim/duzenle/<?php echo $readHelpArticles["id"]; ?>">
                              #<?php echo $readHelpArticles["id"]; ?>
                            </a>
                          </td>
                          <td>
                            <a href="/yonetim-paneli/yardim/duzenle/<?php echo $readHelpArticles["id"]; ?>">
                              <?php echo $readHelpArticles["title"]; ?>
                            </a>
                          </td>
                          <td>
                            <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readHelpArticles["accountID"]; ?>">
                              <?php echo $readHelpArticles["realname"]; ?>
                            </a>
                          </td>
                          <td>
                            <?php echo $readHelpArticles["topicName"]; ?>
                          </td>
                          <td>
                            <?php echo $readHelpArticles["views"]; ?>
                          </td>
                          <td>
                            <?php echo $readHelpArticles["dislikesCount"]; ?>/<?php echo $readHelpArticles["likesCount"]; ?>
                            <?php if ($readHelpArticles["likesCount"] != 0 || $readHelpArticles["dislikesCount"] != 0): ?>
                              <?php
                                $usefulPercent = intval(($readHelpArticles["likesCount"]*100)/($readHelpArticles["likesCount"]+$readHelpArticles["dislikesCount"]));
                                if ($usefulPercent < 50) {
                                  echo '<span class="text-danger">('.$usefulPercent.'%)</span>';
                                }
                                else if ($usefulPercent < 75) {
                                  echo '<span class="text-warning">('.$usefulPercent.'%)</span>';
                                }
                                else {
                                  echo '<span class="text-success">('.$usefulPercent.'%)</span>';
                                }
                              ?>
                            <?php else: ?>
                              (-%)
                            <?php endif ?>
                          </td>
                          <td>
                            <?php echo convertTime($readHelpArticles["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/yardim/duzenle/<?php echo $readHelpArticles["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/yardim-merkezi/makale/<?php echo $readHelpArticles["id"]; ?>/<?php echo $readHelpArticles["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/yardim/sil/<?php echo $readHelpArticles["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
            <?php if (post("query") == false): ?>
              <nav class="pt-3 pb-5" aria-label="Page Navigation">
                <ul class="pagination justify-content-center">
                  <li class="page-item <?php echo ($page == 1) ? "disabled" : null; ?>">
                    <a class="page-link" href="/yonetim-paneli/yardim/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/yonetim-paneli/yardim/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/yonetim-paneli/yardim/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
                  </li>
                </ul>
              </nav>
            <?php endif; ?>
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
                  <h2 class="header-title">Makale Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/yardim">Yardım Merkezi</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Makale Ekle</li>
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
            if (isset($_POST["insertHelp"])) {
              if (!$csrf->validate('insertHelp')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("title") == null || post("topicID") == null || post("content") == null) {
                echo alertError("Lütfen boş alan bırakmayınız!");
              }
              else {
                $insertHelp = $db->prepare("INSERT INTO HelpArticles (accountID, title, slug, topicID, content, views, likesCount, dislikesCount, updateDate, creationDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $insertHelp->execute(array($readAdmin["id"], post("title"), convertURL(post("title")), post("topicID"), filteredContent($_POST["content"]), 0, 0, 0, date("Y-m-d H:i:s"), date("Y-m-d H:i:s")));
                echo alertSuccess("Makale başarıyla yayımlandı!");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group row">
                  <label for="inputTitle" class="col-sm-2 col-form-label">Başlık:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputTitle" class="form-control" name="title" placeholder="Makale başlığı giriniz.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectTopicID" class="col-sm-2 col-form-label">Konu:</label>
                  <div class="col-sm-10">
                    <?php $helpTopics = $db->query("SELECT id, name FROM HelpTopics"); ?>
                    <select id="selectTopicID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="topicID" <?php echo ($helpTopics->rowCount() == 0) ? "disabled" : null; ?>>
                      <?php if ($helpTopics->rowCount() > 0): ?>
                        <?php foreach ($helpTopics as $readHelpTopics): ?>
                          <option value="<?php echo $readHelpTopics["id"]; ?>">
                            <?php echo $readHelpTopics["name"]; ?>
                          </option>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <option>Konu bulunamadı!</option>
                      <?php endif; ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaContent" class="col-sm-2 col-form-label">İçerik:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaContent" class="form-control" data-toggle="textEditor" name="content" placeholder="Haber içeriğini yazınız."></textarea>
                  </div>
                </div>
                <?php echo $csrf->input('insertHelp'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertHelp">Makaleyi Yayımla</button>
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
      $helpArticle = $db->prepare("SELECT * FROM HelpArticles WHERE id = ?");
      $helpArticle->execute(array(get("id")));
      $readHelpArticle = $helpArticle->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Makale Düzenle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/yardim">Yardım Merkezi</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/yardim">Makale Düzenle</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($helpArticle->rowCount() > 0) ? $readHelpArticle["title"] : "Bulunamadı!"; ?></li>
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
          <?php if ($helpArticle->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateHelp"])) {
                if (!$csrf->validate('updateHelp')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("title") == null || post("topicID") == null || post("content") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  $updateHelp = $db->prepare("UPDATE HelpArticles SET title = ?, slug = ?, topicID = ?, content = ?, updateDate = ? WHERE id = ?");
                  $updateHelp->execute(array(post("title"), convertURL(post("title")), post("topicID"), filteredContent($_POST["content"]), date("Y-m-d H:i:s"), $readHelpArticle["id"]));
                  echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label for="inputTitle" class="col-sm-2 col-form-label">Başlık:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputTitle" class="form-control" name="title" placeholder="Makale başlığı giriniz." value="<?php echo $readHelpArticle["title"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectTopicID" class="col-sm-2 col-form-label">Konu:</label>
                    <div class="col-sm-10">
                      <?php $helpTopics = $db->query("SELECT id, name FROM HelpTopics"); ?>
                      <select id="selectTopicID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="topicID" <?php echo ($helpTopics->rowCount() == 0) ? "disabled" : null; ?>>
                        <?php if ($helpTopics->rowCount() > 0): ?>
                          <?php foreach ($helpTopics as $readHelpTopics): ?>
                            <option value="<?php echo $readHelpTopics["id"]; ?>" <?php echo (($readHelpArticle["topicID"] == $readHelpTopics["id"]) ? 'selected="selected"' : null); ?>>
                              <?php echo $readHelpTopics["name"]; ?>
                            </option>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <option>Konu bulunamadı!</option>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="textareaContent" class="col-sm-2 col-form-label">İçerik:</label>
                    <div class="col-sm-10">
                      <textarea id="textareaContent" class="form-control" data-toggle="textEditor" name="content" placeholder="Haber içeriğini yazınız."><?php echo $readHelpArticle["content"]; ?></textarea>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateHelp'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/yardim/sil/<?php echo $readHelpArticle["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/yardim-merkezi/makale/<?php echo $readHelpArticle["id"]; ?>/<?php echo $readHelpArticle["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                        <i class="fe fe-eye"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateHelp">Değişiklikleri Kaydet</button>
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
      $deleteHelpArticle = $db->prepare("DELETE FROM HelpArticles WHERE id = ?");
      $deleteHelpArticle->execute(array(get("id")));
      go("/yonetim-paneli/yardim");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'topic'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Konular</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/yardim">Yardım Merkezi</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Konular</li>
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
          <?php $helpTopics = $db->query("SELECT * FROM HelpTopics ORDER BY id DESC"); ?>
          <?php if ($helpTopics->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["helpTopicID", "helpTopicName"]'>
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
                    <a class="btn btn-sm btn-white" href="/yonetim-paneli/yardim/konu/ekle">Konu Ekle</a>
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
                          <a href="#" class="text-muted sort" data-sort="helpTopicID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="helpTopicName">
                            Konu Adı
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($helpTopics as $readHelpTopics): ?>
                        <tr>
                          <td class="helpTopicID text-center" style="width: 40px;">
                            <a href="/yonetim-paneli/yardim/konu/duzenle/<?php echo $readHelpTopics["id"]; ?>">
                              #<?php echo $readHelpTopics["id"]; ?>
                            </a>
                          </td>
                          <td class="helpTopicName">
                            <a href="/yonetim-paneli/yardim/konu/duzenle/<?php echo $readHelpTopics["id"]; ?>">
                              <?php echo $readHelpTopics["name"]; ?>
                            </a>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/yardim/konu/duzenle/<?php echo $readHelpTopics["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/yardim-merkezi/konu/<?php echo $readHelpTopics["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/yardim/konu/sil/<?php echo $readHelpTopics["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
                  <h2 class="header-title">Konu Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/yardim">Yardım Merkezi</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/yardim/konu">Konu</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Konu Ekle</li>
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
            if (isset($_POST["insertHelpTopics"])) {
              if (!$csrf->validate('insertHelpTopics')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("name") == null || post("description") == null) {
                echo alertError("Lütfen boş alan bırakmayınız!");
              }
              else if ($_FILES["image"]["size"] == null) {
                echo alertError("Lütfen bir resim seçiniz!");
              }
              else {
                require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                $upload = new \Verot\Upload\Upload($_FILES["image"], "tr_TR");
                $imageID = md5(uniqid(rand(0, 9999)));
                if ($upload->uploaded) {
                  $upload->allowed = array("image/*");
                  $upload->file_new_name_body = $imageID;
                  $upload->process(__ROOT__."/apps/main/public/assets/img/help/topics/");
                  if ($upload->processed) {
                    $insertHelpTopics = $db->prepare("INSERT INTO HelpTopics (name, slug, description, imageID, imageType) VALUES (?, ?, ?, ?, ?)");
                    $insertHelpTopics->execute(array(post("name"), convertURL(post("name")), post("description"), $imageID, $upload->file_dst_name_ext));
                    echo alertSuccess("Konu başarıyla eklendi!");
                  }
                  else {
                    echo alertError("Resim yüklenirken bir hata oluştu: ".$upload->error);
                  }
                }

              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group row">
                  <label for="inputName" class="col-sm-2 col-form-label">Konu Adı:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputName" class="form-control" name="name" placeholder="Konu adı giriniz.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputDesc" class="col-sm-2 col-form-label">Açıklama:</label>
                  <div class="col-sm-10">
                    <textarea id="inputDesc" class="form-control" name="description" placeholder="Konu için açıklama giriniz." rows="3"></textarea>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="fileImage" class="col-sm-2 col-form-label">Resim:</label>
                  <div class="col-sm-10">
                    <div data-toggle="dropimage" class="dropimage">
                      <div class="di-thumbnail">
                        <img src="" alt="Ön İzleme">
                      </div>
                      <div class="di-select">
                        <label for="fileImage">Bir Resim Seçiniz</label>
                        <input type="file" id="fileImage" name="image" accept="image/*">
                      </div>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('insertHelpTopics'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertHelpTopics">Ekle</button>
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
      $helpTopic = $db->prepare("SELECT * FROM HelpTopics WHERE id = ?");
      $helpTopic->execute(array(get("id")));
      $readHelpTopic = $helpTopic->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Konu Düzenle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/yardim">Yardım Merkezi</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/yardim/konu">Konu</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/yardim/konu">Konu Düzenle</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($helpTopic->rowCount() > 0) ? $readHelpTopic["name"] : "Bulunamadı!"; ?></li>
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
          <?php if ($helpTopic->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateHelpTopics"])) {
                if (!$csrf->validate('updateHelpTopics')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("name") == null || post("description") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  if ($_FILES["image"]["size"] != null) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                    $upload = new \Verot\Upload\Upload($_FILES["image"], "tr_TR");
                    $imageID = $readHelpTopic["imageID"];
                    if ($upload->uploaded) {
                      $upload->allowed = array("image/*");
                      $upload->file_overwrite = true;
                      $upload->file_new_name_body = $imageID;
                      $upload->process(__ROOT__."/apps/main/public/assets/img/help/topics/");
                      if ($upload->processed) {
                        $updateHelpTopics = $db->prepare("UPDATE HelpTopics SET imageType = ? WHERE id = ?");
                        $updateHelpTopics->execute(array($upload->file_dst_name_ext, get("id")));
                      }
                      else {
                        echo alertError("Resim yüklenirken bir hata oluştu: ".$upload->error);
                      }
                    }
                  }
                  $updateHelpTopics = $db->prepare("UPDATE HelpTopics SET name = ?, slug = ?, description = ? WHERE id = ?");
                  $updateHelpTopics->execute(array(post("name"), convertURL(post("name")), post("description"), get("id")));
                  echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">Konu Adı:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputName" class="form-control" name="name" placeholder="Konu adı giriniz." value="<?php echo $readHelpTopic["name"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputDesc" class="col-sm-2 col-form-label">Açıklama:</label>
                    <div class="col-sm-10">
                      <textarea id="inputDesc" class="form-control" name="description" placeholder="Konu için açıklama giriniz." rows="3"><?php echo $readHelpTopic["description"]; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="fileImage" class="col-sm-2 col-form-label">Resim:</label>
                    <div class="col-sm-10">
                      <div data-toggle="dropimage" class="dropimage active">
                        <div class="di-thumbnail">
                          <img src="/apps/main/public/assets/img/help/topics/<?php echo $readHelpTopic["imageID"].'.'.$readHelpTopic["imageType"]; ?>" alt="Ön İzleme">
                        </div>
                        <div class="di-select">
                          <label for="fileImage">Bir Resim Seçiniz</label>
                          <input type="file" id="fileImage" name="image" accept="image/*">
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateHelpTopics'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/yardim/konu/sil/<?php echo $readHelpTopic["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/yardim-merkezi/konu/<?php echo $readHelpTopic["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                        <i class="fe fe-eye"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateHelpTopics">Değişiklikleri Kaydet</button>
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
      $deleteHelpTopic = $db->prepare("DELETE FROM HelpTopics WHERE id = ?");
      $deleteHelpTopic->execute(array(get("id")));
      go("/yonetim-paneli/yardim/konu");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
