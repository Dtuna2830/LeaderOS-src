<?php
  if (!checkPerm($readAdmin, 'MANAGE_SUPPORT')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  if (get("target") == 'support' && get("action") == 'get' && get("id")) {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/support.js');
    $extraResourcesJS->addResource('/apps/main/public/assets/js/plugins/ckeditor/translations/tr.js');
    $extraResourcesJS->addResource('/apps/main/public/assets/js/plugins/ckeditor/ckeditor.js');
    $extraResourcesJS->addResource('/apps/main/public/assets/js/ckeditor5.js');
  }
  if (get("target") == 'support' && get("action") == 'getAll') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/support.delete.js');
  }
?>
<?php if (get("target") == 'support'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Destek</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/destek">Destek</a></li>
                      <?php if (get("category")): ?>
                        <?php if (get("category") == 'unread'): ?>
                          <li class="breadcrumb-item active" aria-current="page">Yanıt Bekleyenler</li>
                        <?php elseif (get("category") == 'readed'): ?>
                          <li class="breadcrumb-item active" aria-current="page">Yanıtlananlar</li>
                        <?php elseif (get("category") == 'closed'): ?>
                          <li class="breadcrumb-item active" aria-current="page">Kapatılanlar</li>
                        <?php else: ?>
                          <li class="breadcrumb-item active" aria-current="page">Hata!</li>
                        <?php endif; ?>
                      <?php else: ?>
                        <li class="breadcrumb-item active" aria-current="page">Tümü</li>
                      <?php endif; ?>
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
            if (get("category")) {
              if (get("category") == 'unread') {
                $supports = $db->prepare("SELECT S.id FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id WHERE S.statusID IN (?, ?) ORDER BY S.id DESC");
                $supports->execute(array(1, 3));
              }
              else if (get("category") == 'readed') {
                $supports = $db->prepare("SELECT S.id FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id WHERE S.statusID = ? ORDER BY S.id DESC");
                $supports->execute(array(2));
              }
              else if (get("category") == 'closed') {
                $supports = $db->prepare("SELECT S.id FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id WHERE S.statusID = ? ORDER BY S.id DESC");
                $supports->execute(array(4));
              }
              else {
                $supports = $db->query("SELECT S.id FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id ORDER BY S.id DESC");
              }
            }
            else {
              $supports = $db->query("SELECT S.id FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id ORDER BY S.id DESC");
            }
          ?>
          <?php if ($supports->rowCount() > 0): ?>
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

              $itemsCount = $supports->rowCount();
              $pageCount = ceil($itemsCount/$limit);
              if ($page > $pageCount) {
                $page = 1;
              }
              $visibleItemsCount = $page * $limit - $limit;
              $requestURL = 'destek';
              if (get("category")) {
                if (get("category") == 'unread') {
                  $supports = $db->prepare("SELECT S.*, SC.name as categoryName, Se.name as serverName, A.realname FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id WHERE S.statusID IN (?, ?) ORDER BY S.id DESC LIMIT $visibleItemsCount, $limit");
                  $supports->execute(array(1, 3));
                  $requestURL = 'destek/yanit-bekleyen';
                }
                else if (get("category") == 'readed') {
                  $supports = $db->prepare("SELECT S.*, SC.name as categoryName, Se.name as serverName, A.realname FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id WHERE S.statusID = ? ORDER BY S.id DESC LIMIT $visibleItemsCount, $limit");
                  $supports->execute(array(2));
                  $requestURL = 'destek/yanitli';
                }
                else if (get("category") == 'closed') {
                  $supports = $db->prepare("SELECT S.*, SC.name as categoryName, Se.name as serverName, A.realname FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id WHERE S.statusID = ? ORDER BY S.id DESC LIMIT $visibleItemsCount, $limit");
                  $supports->execute(array(4));
                  $requestURL = 'destek/kapali';
                }
                else {
                  $supports = $db->query("SELECT S.*, SC.name as categoryName, Se.name as serverName, A.realname FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id ORDER BY S.id DESC LIMIT $visibleItemsCount, $limit");
                }
              }
              else {
                $supports = $db->query("SELECT S.*, SC.name as categoryName, Se.name as serverName, A.realname FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id ORDER BY S.id DESC LIMIT $visibleItemsCount, $limit");
              }

              if (isset($_POST["query"])) {
                if (post("query") != null) {
                  $supports = $db->prepare("SELECT S.*, SC.name as categoryName, Se.name as serverName, A.realname FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN Accounts A ON S.accountID = A.id WHERE S.title LIKE :search OR SC.name LIKE :search OR Se.name LIKE :search ORDER BY S.id DESC");
                  $supports->execute(array(
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
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="Arama Yap (Başlık, Kategori veya Sunucu)" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success">Ara</button>
                      <button type="button" class="btn btn-sm btn-danger clickdelete" onclick='document.getElementById("deleteSelected").submit();'>Seçilenleri Sil</button>
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
                        <th class="text-center" style="width: 40px;">
                          <div class="custom-control custom-checkbox table-checkbox">
                            <input type="checkbox" class="custom-control-input" name="ordersSelect" id="ordersSelectAll">
                            <label class="custom-control-label" for="ordersSelectAll">
                              &nbsp;
                            </label>
                          </div>
                        </th>
                        <th class="text-center" style="width: 40px;">#ID</th>
                        <th>Başlık</th>
                        <th>Kullanıcı Adı</th>
                        <th>Sunucu</th>
                        <th>Kategori</th>
                        <th>Tarih</th>
                        <th class="text-center">Durum</th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <form action="/yonetim-paneli/destek/toplu-sil" method="post" id="deleteSelected">
                        <?php foreach ($supports as $readSupports): ?>
                          <tr>
                            <td class="text-center" style="width: 40px;">
                              <div class="custom-control custom-checkbox table-checkbox">
                                <input type="checkbox" class="custom-control-input" name="ordersSelect[]" value="<?php echo $readSupports["id"]; ?>" id="ordersSelect-<?php echo $readSupports["id"]; ?>">
                                <label class="custom-control-label" for="ordersSelect-<?php echo $readSupports["id"]; ?>">
                                  &nbsp;
                                </label>
                              </div>
                            </td>
                            <td class="text-center" style="width: 40px;">
                              <a href="/yonetim-paneli/destek/goruntule/<?php echo $readSupports["id"]; ?>">
                                #<?php echo $readSupports["id"]; ?>
                              </a>
                            </td>
                            <td>
                              <a href="/yonetim-paneli/destek/goruntule/<?php echo $readSupports["id"]; ?>">
                                <?php echo $readSupports["title"]; ?>
                              </a>
                            </td>
                            <td>
                              <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readSupports["accountID"]; ?>">
                                <?php echo $readSupports["realname"]; ?>
                              </a>
                            </td>
                            <td>
                              <?php echo $readSupports["serverName"]; ?>
                            </td>
                            <td>
                              <?php echo $readSupports["categoryName"]; ?>
                            </td>
                            <td>
                              <?php echo convertTime($readSupports["creationDate"], 2, true); ?>
                            </td>
                            <td class="text-center">
                              <?php if ($readSupports["statusID"] == 1): ?>
                                <span class="badge badge-pill badge-danger">Cevaplanmadı</span>
                              <?php elseif ($readSupports["statusID"] == 2): ?>
                                <span class="badge badge-pill badge-success">Cevaplandı</span>
                              <?php elseif ($readSupports["statusID"] == 3): ?>
                                <span class="badge badge-pill badge-warning">Kullanıcı Yanıtı</span>
                              <?php elseif ($readSupports["statusID"] == 4): ?>
                                <span class="badge badge-pill badge-danger">Kapatıldı</span>
                              <?php else: ?>
                                <span class="badge badge-pill badge-danger">Hata!</span>
                              <?php endif; ?>
                            </td>
                            <td class="text-right">
                              <a class="btn btn-sm btn-rounded-circle btn-primary" href="/yonetim-paneli/destek/goruntule/<?php echo $readSupports["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                                <i class="fe fe-eye"></i>
                              </a>
                              <a class="btn btn-sm btn-rounded-circle btn-warning" href="/yonetim-paneli/destek/kapat/<?php echo $readSupports["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Kapat">
                                <i class="fe fe-x"></i>
                              </a>
                              <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/destek/sil/<?php echo $readSupports["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                                <i class="fe fe-trash-2"></i>
                              </a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </form>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <?php if (post("query") == false): ?>
              <nav class="pt-3 pb-5" aria-label="Page Navigation">
                <ul class="pagination justify-content-center">
                  <li class="page-item <?php echo ($page == 1) ? "disabled" : null; ?>">
                    <a class="page-link" href="/yonetim-paneli/<?php echo $requestURL; ?>/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/yonetim-paneli/<?php echo $requestURL; ?>/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/yonetim-paneli/<?php echo $requestURL; ?>/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
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
  <?php elseif (get("action") == 'get' && get("id")): ?>
    <?php
      $support = $db->prepare("SELECT S.*, A.realname, Se.name as serverName, SC.name as categoryName FROM Supports S INNER JOIN Accounts A ON S.accountID = A.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN SupportCategories SC ON S.categoryID = SC.id WHERE S.id = ?");
      $support->execute(array(get("id")));
      $readSupport = $support->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Destek Görüntüle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/destek">Destek</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/destek">Destek Görüntüle</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo ($support->rowCount() > 0) ? limitedContent($readSupport["title"], 50): "Bulunamadı!"; ?></li>
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
          <?php if ($support->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/commonmark/autoload.php");
              $converter = new \League\CommonMark\GithubFlavoredMarkdownConverter();
            ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["insertSupportMessages"])) {
                if (!$csrf->validate('insertSupportMessages')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("message") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  $insertSupportMessages = $db->prepare("INSERT INTO SupportMessages (accountID,supportID, message, writeLocation, creationDate) VALUES (?, ?, ?, ?, ?)");
                  $insertSupportMessages->execute(array($readAdmin["id"], $readSupport["id"], filteredContent($_POST["message"]), 2, date("Y-m-d H:i:s")));
                  $updateSupports = $db->prepare("UPDATE Supports SET updateDate = ?, statusID = ?, readStatus = ? WHERE id = ? AND accountID = ?");
                  $updateSupports->execute(array(date("Y-m-d H:i:s"), 2, 0, get("id"), $readSupport["accountID"]));
                  echo alertSuccess("Mesaj başarıyla gönderildi!");
                }
              }
            ?>
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <h4 class="card-header-title">
                      <?php echo $readSupport["title"]; ?>
                    </h4>
                  </div>
                  <div class="col-auto">
                    <span class="badge badge-pill badge-primary" data-toggle="tooltip" data-placement="top" data-original-title="Sunucu">
                      <?php echo $readSupport["serverName"]; ?>
                    </span>
                    <span class="badge badge-pill badge-primary" data-toggle="tooltip" data-placement="top" data-original-title="Kategori">
                      <?php echo $readSupport["categoryName"]; ?>
                    </span>
                    <span class="badge badge-pill badge-primary" data-toggle="tooltip" data-placement="top" data-original-title="Tarih">
                      <?php echo convertTime($readSupport["creationDate"], 2, true); ?>
                    </span>
                    <?php if ($readSupport["statusID"] == 1): ?>
                      <span class="badge badge-pill badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Durum">Cevaplanmadı</span>
                    <?php elseif ($readSupport["statusID"] == 2): ?>
                      <span class="badge badge-pill badge-success" data-toggle="tooltip" data-placement="top" data-original-title="Durum">Cevaplandı</span>
                    <?php elseif ($readSupport["statusID"] == 3): ?>
                      <span class="badge badge-pill badge-warning" data-toggle="tooltip" data-placement="top" data-original-title="Durum">Kullanıcı Yanıtı</span>
                    <?php elseif ($readSupport["statusID"] == 4): ?>
                      <span class="badge badge-pill badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Durum">Kapatıldı</span>
                    <?php else: ?>
                      <span class="badge badge-pill badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Durum">Hata!</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <div id="messagesBox" class="card-body pb-0" style="overflow: auto; max-height: 500px;">
                <div class="message">
                  <div class="message-img">
                    <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readSupport["accountID"]; ?>">
                      <img class="float-left rounded-circle" src="https://minotar.net/avatar/<?php echo $readSupport["realname"]; ?>/40.png" alt="<?php echo $serverName." Oyuncu - ".$readSupport["realname"]; ?> Mesaj">
                    </a>
                  </div>
                  <div class="message-content">
                    <div class="message-header">
                      <div class="message-username">
                        <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readSupport["accountID"]; ?>">
                          <?php echo $readSupport["realname"]; ?>
                        </a>
                      </div>
                      <div class="message-date">
                        <?php echo convertTime($readSupport["creationDate"], 2, true); ?>
                      </div>
                    </div>
                    <div class="message-body">
                      <p>
                        <?php echo $converter->convertToHtml($readSupport["message"]); ?>
                      </p>
                    </div>
                  </div>
                </div>
                <?php
                  $supportMessages = $db->prepare("SELECT SM.*, A.realname FROM SupportMessages SM INNER JOIN Accounts A ON SM.accountID = A.id WHERE SM.supportID = ? ORDER BY SM.id ASC");
                  $supportMessages->execute(array(get("id")));
                ?>
                <?php if ($supportMessages->rowCount() > 0): ?>
                  <?php foreach ($supportMessages as $readSupportMessages): ?>
                    <div class="message">
                      <div class="message-img">
                        <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readSupportMessages["accountID"]; ?>">
                          <img class="float-left rounded-circle" src="https://minotar.net/avatar/<?php echo $readSupportMessages["realname"]; ?>/40.png" alt="<?php echo $serverName." Oyuncu - ".$readSupportMessages["realname"]; ?> Mesaj">
                        </a>
                      </div>
                      <div class="message-content">
                        <div class="message-header">
                          <div class="message-username">
                            <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readSupportMessages["accountID"]; ?>">
                              <?php echo $readSupportMessages["realname"]; ?>
                            </a>
                          </div>
                          <div class="message-date">
                            <?php echo convertTime($readSupportMessages["creationDate"]); ?>
                          </div>
                        </div>
                        <div class="message-body">
                          <p>
                            <?php
                              if ($readSupportMessages["writeLocation"] == 1) {
                                echo $converter->convertToHtml($readSupportMessages["message"]);
                              }
                              else {
                                $message = showEmoji(hashtag(hashtag($readSupportMessages["message"], "@", "/oyuncu"), "#", "/etiket"));
                                $search = array("%username%", "%message%", "%servername%", "%serverip%", "%serverversion%");
                                $replace = array($readSupport["realname"], $message, $serverName, $serverIP, $serverVersion);
                                $template = $readSettings["supportMessageTemplate"];
                                echo str_replace($search, $replace, $template);
                              }
                            ?>
                          </p>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
              <div class="card-footer">
                <form action="" method="post">
                  <div class="message">
                    <div class="message-img">
                      <img class="float-left rounded-circle" src="https://minotar.net/avatar/<?php echo $readAdmin["realname"]; ?>/40.png" alt="<?php echo $serverName." Oyuncu - ".$readAdmin["realname"]; ?> Mesaj">
                    </div>
                    <div class="message-content">
                      <div class="message-body">
                        <div class="mb-3">
                          <select id="selectAnswer" class="form-control" data-toggle="select" data-minimum-results-for-search="-1">
                            <?php $supportAnswers = $db->query("SELECT * FROM SupportAnswers"); ?>
                            <?php if ($supportAnswers->rowCount() > 0): ?>
                              <option value="">Bir cevap seçebilirsiniz.</option>
                              <?php foreach ($supportAnswers as $readSupportAnswers): ?>
                                <option value="<?php echo htmlentities($readSupportAnswers["content"]); ?>">
                                  <?php echo $readSupportAnswers["title"]; ?>
                                </option>
                              <?php endforeach; ?>
                            <?php else: ?>
                              <option value="">Hazır cevap bulunamadı!</option>
                            <?php endif; ?>
                          </select>
                        </div>
                        <textarea id="textareaMessage" class="form-control" data-toggle="textEditor" name="message" placeholder="Mesajınızı yazınız."></textarea>
                      </div>
                      <div class="message-footer">
                        <?php echo $csrf->input('insertSupportMessages'); ?>
                        <div class="clearfix">
                          <div class="float-right">
                            <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/destek/sil/<?php echo $readSupport["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                              <i class="fe fe-trash-2"></i>
                            </a>
                            <a class="btn btn-rounded-circle btn-warning" href="/yonetim-paneli/destek/kapat/<?php echo $readSupport["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Kapat">
                              <i class="fe fe-x"></i>
                            </a>
                            <button type="submit" class="btn btn-rounded btn-success" name="insertSupportMessages">Gönder</button>
                          </div>
                        </div>
                      </div>
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
  <?php elseif (get("action") == 'close' && get("id")): ?>
    <?php
      $closeSupport = $db->prepare("UPDATE Supports SET statusID = ?, updateDate = ? WHERE id = ?");
      $closeSupport->execute(array(4, date("Y-m-d H:i:s"), get("id")));
      go("/yonetim-paneli/destek");
    ?>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteSupport = $db->prepare("DELETE FROM Supports WHERE id = ?");
      $deleteSupport->execute(array(get("id")));
      $deleteSupportMessages = $db->prepare("DELETE FROM SupportMessages WHERE supportID = ?");
      $deleteSupportMessages->execute(array(get("id")));
      go("/yonetim-paneli/destek");
    ?>
  <?php elseif (get("action") == 'delete-selected' && count($_POST["ordersSelect"])): ?>
    <?php
      foreach ($_POST["ordersSelect"] as $supportID) {
        $deleteSupport = $db->prepare("DELETE FROM Supports WHERE id = ?");
        $deleteSupport->execute(array($supportID));
        $deleteSupportMessages = $db->prepare("DELETE FROM SupportMessages WHERE supportID = ?");
        $deleteSupportMessages->execute(array($supportID));
      }
      go("/yonetim-paneli/destek");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'category'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Kategoriler</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/destek">Destek</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Kategoriler</li>
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
          <?php $supportCategories = $db->query("SELECT * FROM SupportCategories ORDER BY id DESC"); ?>
          <?php if ($supportCategories->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["supportCategoryID", "suportCategoryName"]'>
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
                    <a class="btn btn-sm btn-white" href="/yonetim-paneli/destek/kategori/ekle">Kategori Ekle</a>
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
                          <a href="#" class="text-muted sort" data-sort="supportCategoryID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="suportCategoryName">
                            Kategori Adı
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($supportCategories as $readSupportCategories): ?>
                        <tr>
                          <td class="supportCategoryID text-center" style="width: 40px;">
                            <a href="/yonetim-paneli/destek/kategori/duzenle/<?php echo $readSupportCategories["id"]; ?>">
                              #<?php echo $readSupportCategories["id"]; ?>
                            </a>
                          </td>
                          <td class="suportCategoryName">
                            <a href="/yonetim-paneli/destek/kategori/duzenle/<?php echo $readSupportCategories["id"]; ?>">
                              <?php echo $readSupportCategories["name"]; ?>
                            </a>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/destek/kategori/duzenle/<?php echo $readSupportCategories["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/destek/kategori/sil/<?php echo $readSupportCategories["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
                  <h2 class="header-title">Kategori Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/destek">Destek</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/destek/kategori">Kategori</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Kategori Ekle</li>
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
            if (isset($_POST["insertSupportCategories"])) {
              if (!$csrf->validate('insertSupportCategories')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("name") == null) {
                echo alertError("Lütfen boş alan bırakmayınız!");
              }
              else {
                $insertSupportCategories = $db->prepare("INSERT INTO SupportCategories (name, userTemplate) VALUES (?, ?)");
                $insertSupportCategories->execute(array(post("name"), post("userTemplate")));
                echo alertSuccess("Kategori başarıyla eklendi!");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputName" class="col-sm-2 col-form-label">Kategori Adı:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputName" class="form-control" name="name" placeholder="Kategori adı yazınız.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputUserTemplate" class="col-sm-2 col-form-label">Mesaj Şablonu (Opsiyonel):</label>
                  <div class="col-sm-10">
                    <textarea cols="3" id="inputUserTemplate" class="form-control" name="userTemplate" placeholder="Kullanıcı bu kategoride yeni bir ticket açarken buraya yazdığınız mesaj otomatik olarak doldurulur."></textarea>
                  </div>
                </div>
                <?php echo $csrf->input('insertSupportCategories'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertSupportCategories">Ekle</button>
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
      $supportCategory = $db->prepare("SELECT * FROM SupportCategories WHERE id = ?");
      $supportCategory->execute(array(get("id")));
      $readSupportCategory = $supportCategory->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Kategori Düzenle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/destek">Destek</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/destek/kategori">Kategori</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/destek/kategori">Kategori Düzenle</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($supportCategory->rowCount() > 0) ? $readSupportCategory["name"] : "Bulunamadı!"; ?></li>
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
          <?php if ($supportCategory->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateSupportCategories"])) {
                if (!$csrf->validate('updateSupportCategories')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("name") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  $updateSupportCategories = $db->prepare("UPDATE SupportCategories SET name = ?, userTemplate = ? WHERE id = ?");
                  $updateSupportCategories->execute(array(post("name"), post("userTemplate"), get("id")));
                  echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">Kategori Adı:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputName" class="form-control" name="name" placeholder="Kategori adı yazınız." value="<?php echo $readSupportCategory["name"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputUserTemplate" class="col-sm-2 col-form-label">Mesaj Şablonu (Opsiyonel):</label>
                    <div class="col-sm-10">
                      <textarea cols="3" id="inputUserTemplate" class="form-control" name="userTemplate" placeholder="Kullanıcı bu kategoride yeni bir ticket açarken buraya yazdığınız mesaj otomatik olarak doldurulur."><?php echo $readSupportCategory["userTemplate"]; ?></textarea>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateSupportCategories'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/destek/kategori/sil/<?php echo $readSupportCategory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateSupportCategories">Değişiklikleri Kaydet</button>
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
      $deleteSupportCategory = $db->prepare("DELETE FROM SupportCategories WHERE id = ?");
      $deleteSupportCategory->execute(array(get("id")));
      go("/yonetim-paneli/destek/kategori");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'answer'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Cevaplar</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/destek">Destek</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Cevaplar</li>
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
          <?php $supportAnswers = $db->query("SELECT * FROM SupportAnswers ORDER BY id DESC"); ?>
          <?php if ($supportAnswers->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["supportAnswerID", "supportAnswerTitle"]'>
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
                    <a class="btn btn-sm btn-white" href="/yonetim-paneli/destek/cevap/ekle">Cevap Ekle</a>
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
                          <a href="#" class="text-muted sort" data-sort="supportAnswerID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="supportAnswerTitle">
                            Başlık
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($supportAnswers as $readSupportAnswers): ?>
                        <tr>
                          <td class="supportAnswerID text-center" style="width: 40px;">
                            <a href="/yonetim-paneli/destek/cevap/duzenle/<?php echo $readSupportAnswers["id"]; ?>">
                              #<?php echo $readSupportAnswers["id"]; ?>
                            </a>
                          </td>
                          <td class="supportAnswerTitle">
                            <a href="/yonetim-paneli/destek/cevap/duzenle/<?php echo $readSupportAnswers["id"]; ?>">
                              <?php echo $readSupportAnswers["title"]; ?>
                            </a>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/destek/cevap/duzenle/<?php echo $readSupportAnswers["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/destek/cevap/sil/<?php echo $readSupportAnswers["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
                  <h2 class="header-title">Cevap Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/destek">Destek</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/destek/cevap">Cevap</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Cevap Ekle</li>
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
            if (isset($_POST["insertSupportAnswers"])) {
              if (!$csrf->validate('insertSupportAnswers')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("title") == null || post("content") == null) {
                echo alertError("Lütfen boş alan bırakmayınız!");
              }
              else {
                $insertSupportAnswers = $db->prepare("INSERT INTO SupportAnswers (title, content) VALUES (?, ?)");
                $insertSupportAnswers->execute(array(post("title"), filteredContent($_POST["content"])));
                echo alertSuccess("Cevap başarıyla eklendi!");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputTitle" class="col-sm-2 col-form-label">Başlık:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputTitle" class="form-control" name="title" placeholder="Cevap başlığını yazınız.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputContent" class="col-sm-2 col-form-label">İçerik:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaContent" class="form-control" data-toggle="textEditor" name="content" placeholder="Cevap içeriğini yazınız."></textarea>
                  </div>
                </div>
                <?php echo $csrf->input('insertSupportAnswers'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertSupportAnswers">Ekle</button>
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
      $supportAnswers = $db->prepare("SELECT * FROM SupportAnswers WHERE id = ?");
      $supportAnswers->execute(array(get("id")));
      $readSupportAnswers = $supportAnswers->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Cevap Düzenle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/destek">Destek</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/destek/cevap">Cevap</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/destek/cevap">Cevap Düzenle</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($supportAnswers->rowCount() > 0) ? $readSupportAnswers["title"] : "Bulunamadı!"; ?></li>
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
          <?php if ($supportAnswers->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateSupportAnswers"])) {
                if (!$csrf->validate('updateSupportAnswers')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("title") == null || post("content") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  $updateSupportAnswers = $db->prepare("UPDATE SupportAnswers SET title = ?, content = ? WHERE id = ?");
                  $updateSupportAnswers->execute(array(post("title"), filteredContent($_POST["content"]), get("id")));
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
                      <input type="text" id="inputTitle" class="form-control" name="title" placeholder="Cevap başlığını yazınız." value="<?php echo $readSupportAnswers["title"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputContent" class="col-sm-2 col-form-label">İçerik:</label>
                    <div class="col-sm-10">
                      <textarea id="textareaContent" class="form-control" data-toggle="textEditor" name="content" placeholder="Cevap içeriğini yazınız."><?php echo $readSupportAnswers["content"]; ?></textarea>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateSupportAnswers'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/destek/cevap/sil/<?php echo $readSupportAnswers["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateSupportAnswers">Değişiklikleri Kaydet</button>
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
      $deleteSupportAnswer = $db->prepare("DELETE FROM SupportAnswers WHERE id = ?");
      $deleteSupportAnswer->execute(array(get("id")));
      go("/yonetim-paneli/destek/cevap");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
