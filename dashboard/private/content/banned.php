<?php
  if (!checkPerm($readAdmin, 'MANAGE_BANS')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  if (get("target") == 'ban' && (get("action") == 'insert' || (get("action") == 'update' && get("id")))) {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/banned.js');
  }
?>
<?php if (get("target") == 'ban'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">
                    Engellenen Kullanıcılar
                  </h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/engel">Engel</a></li>
                      <?php if (get("category")): ?>
                        <?php if (get("category") == 'site'): ?>
                          <li class="breadcrumb-item active" aria-current="page">Site</li>
                        <?php elseif (get("category") == 'support'): ?>
                          <li class="breadcrumb-item active" aria-current="page">Destek</li>
                        <?php elseif (get("category") == 'comment'): ?>
                          <li class="breadcrumb-item active" aria-current="page">Yorum</li>
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
              $bannedAccounts = $db->prepare("SELECT BA.id FROM BannedAccounts BA INNER JOIN Accounts A ON BA.accountID = A.id WHERE BA.categoryID = ? AND (BA.expiryDate > ? OR BA.expiryDate = ?) ORDER BY BA.id DESC");
              if (get("category") == "site") {
                $bannedAccounts->execute(array(1, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
              }
              else if (get("category") == "support") {
                $bannedAccounts->execute(array(2, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
              }
              else if (get("category") == "comment") {
                $bannedAccounts->execute(array(3, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
              }
              else {
                $bannedAccounts->execute(array(1, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
              }
            }
            else {
              $bannedAccounts = $db->prepare("SELECT BA.id FROM BannedAccounts BA INNER JOIN Accounts A ON BA.accountID = A.id WHERE (BA.expiryDate > ? OR BA.expiryDate = ?) ORDER BY BA.id DESC");
              $bannedAccounts->execute(array(date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
            }
          ?>
          <?php if ($bannedAccounts->rowCount() > 0): ?>
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

              $itemsCount = $bannedAccounts->rowCount();
              $pageCount = ceil($itemsCount/$limit);
              if ($page > $pageCount) {
                $page = 1;
              }
              $visibleItemsCount = $page * $limit - $limit;
              $requestURL = 'engel';
              if (get("category")) {
                $bannedAccounts = $db->prepare("SELECT BA.*, A.realname FROM BannedAccounts BA INNER JOIN Accounts A ON BA.accountID = A.id WHERE BA.categoryID = ? AND (BA.expiryDate > ? OR BA.expiryDate = ?) ORDER BY BA.id DESC LIMIT $visibleItemsCount, $limit");
                if (get("category") == "site") {
                  $bannedAccounts->execute(array(1, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
                  $requestURL = 'engel/site';
                }
                else if (get("category") == "support") {
                  $bannedAccounts->execute(array(2, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
                  $requestURL = 'engel/destek';
                }
                else if (get("category") == "comment") {
                  $bannedAccounts->execute(array(3, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
                  $requestURL = 'engel/yorum';
                }
                else {
                  $bannedAccounts->execute(array(1, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
                  $requestURL = 'engel/site';
                }
              }
              else {
                $bannedAccounts = $db->prepare("SELECT BA.*, A.realname FROM BannedAccounts BA INNER JOIN Accounts A ON BA.accountID = A.id WHERE (BA.expiryDate > ? OR BA.expiryDate = ?) ORDER BY BA.id DESC LIMIT $visibleItemsCount, $limit");
                $bannedAccounts->execute(array(date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
              }

              if (isset($_POST["query"])) {
                if (post("query") != null) {
                  $bannedAccounts = $db->prepare("SELECT BA.*, A.realname FROM BannedAccounts BA INNER JOIN Accounts A ON BA.accountID = A.id WHERE A.realname LIKE :search AND (BA.expiryDate > :nowDate OR BA.expiryDate = :unlimitedDate) ORDER BY BA.id DESC");
                  $bannedAccounts->execute(array(
                    "search"        => '%'.post("query").'%',
                    "nowDate"       => date("Y-m-d H:i:s"),
                    "unlimitedDate" => '1000-01-01 00:00:00'
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
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="Arama Yap (Kullanıcı Adı)" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success">Ara</button>
                      <a class="btn btn-sm btn-white" href="/yonetim-paneli/engel/ekle">Hesap Engelle</a>
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
                        <th>Kullanıcı Adı</th>
                        <th>Kategori</th>
                        <th>Nedeni</th>
                        <th>Kalan Süre</th>
                        <th>Tarih</th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($bannedAccounts as $readBannedAccounts): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            <a href="/yonetim-paneli/engel/duzenle/<?php echo $readBannedAccounts["id"]; ?>">
                              #<?php echo $readBannedAccounts["id"]; ?>
                            </a>
                          </td>
                          <td>
                            <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readBannedAccounts["accountID"]; ?>">
                              <?php echo $readBannedAccounts["realname"]; ?>
                            </a>
                          </td>
                          <td>
                            <?php if ($readBannedAccounts["categoryID"] == 1): ?>
                              Site
                            <?php elseif ($readBannedAccounts["categoryID"] == 2): ?>
                              Destek
                            <?php elseif ($readBannedAccounts["categoryID"] == 3): ?>
                              Yorum
                            <?php else: ?>
                              Hata!
                            <?php endif; ?>
                          </td>
                          <td>
                            <?php if ($readBannedAccounts["reasonID"] == 1): ?>
                              <?php echo limitedContent($readBannedAccounts["reasonText"], 30); ?>
                            <?php else: ?>
                              <?php if ($readBannedAccounts["reasonID"] == 2): ?>
                                Spam
                              <?php elseif ($readBannedAccounts["reasonID"] == 3): ?>
                                Küfür/Hakaret
                              <?php elseif ($readBannedAccounts["reasonID"] == 4): ?>
                                Hile
                              <?php elseif ($readBannedAccounts["reasonID"] == 5): ?>
                                Reklam
                              <?php elseif ($readBannedAccounts["reasonID"] == 6): ?>
                                Oyuncuları Dolandırmak
                              <?php else: ?>
                                Hata!
                              <?php endif; ?>
                            <?php endif; ?>
                          </td>
                          <td>
                            <?php echo ($readBannedAccounts["expiryDate"] == '1000-01-01 00:00:00') ? 'Süresiz' : getDuration($readBannedAccounts["expiryDate"]).' gün'; ?>
                          </td>
                          <td>
                            <?php echo convertTime($readBannedAccounts["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/engel/duzenle/<?php echo $readBannedAccounts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/engel/sil/<?php echo $readBannedAccounts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                              <i class="fe fe-trash-2"></i>
                            </a>
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
  <?php elseif (get("action") == 'insert'): ?>
    <?php
      if (get("id")) {
        $account = $db->prepare("SELECT * FROM Accounts WHERE id = :user OR realname = :user");
        $account->execute(array(
          'user' => get("id")
        ));
        $readAccount = $account->fetch();
      }
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Engelle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/engel">Engel</a></li>
                      <?php if (get("id")): ?>
                        <li class="breadcrumb-item"><a href="/yonetim-paneli/engel">Ekle</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo ($account->rowCount() > 0) ? $readAccount["realname"] : "Bulunamadı!"; ?></li>
                      <?php else: ?>
                        <li class="breadcrumb-item active" aria-current="page">Ekle</li>
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
          <?php if (!get("id") || (get("id") && $account->rowCount() > 0)): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if ((!get("id") && post("username") != null)) {
                $account = $db->prepare("SELECT * FROM Accounts WHERE realname = ?");
                $account->execute(array(post("username")));
                $readAccount = $account->fetch();
              }
              if (isset($_POST["insertBannedAccounts"])) {
                if (post("durationStatus") == 0) {
                  $_POST["duration"] = '1000-01-01 00:00:00';
                }
                else {
                  $_POST["duration"] = createDuration($_POST["duration"]);
                }
                if (!$csrf->validate('insertBannedAccounts')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if ((!get("id") && post("username") == null) || post("categoryID") == null || post("reasonID") == null || post("durationStatus") == null || post("duration") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else if (!get("id") && $account->rowCount() == 0) {
                  echo alertError("Kullanıcı bulunamadı!");
                }
                else {
                  $readAccount["permissions"] = getPermissions($readAccount["id"]);
                  if (!checkPerm($readAdmin, 'SUPER_ADMIN') && checkPerm($readAccount["permissions"], 'SUPER_ADMIN')) {
                    echo alertError("Bu işlemi yapabilecek yetkiye sahip değilsiniz!");
                  }
                  else if ($readAdmin["id"] == $readAccount["id"]) {
                    echo alertError("Kendinizi engelleyemezsiniz!");
                  }
                  else {
                    if (post("categoryID") == 1) {
                      $deleteAccountSessions = $db->prepare("DELETE FROM AccountSessions WHERE accountID = ?");
                      $deleteAccountSessions->execute(array($readAccount["id"]));
                    }
                    $insertBannedAccounts = $db->prepare("INSERT INTO BannedAccounts (accountID, categoryID, reasonID, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?)");
                    $insertBannedAccounts->execute(array($readAccount["id"], post("categoryID"), post("reasonID"), post("duration"), date("Y-m-d H:i:s")));
                    echo alertSuccess("Hesap başarıyla engellendi!");
                  }
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <?php if (get("id")): ?>
                      <label for="staticUsername" class="col-sm-2 col-form-label">Kullanıcı Adı:</label>
                      <div class="col-sm-10">
                        <a id="staticUsername" class="form-control-plaintext" href="/yonetim-paneli/hesap/goruntule/<?php echo $readAccount["accountID"]; ?>">
                          <?php echo $readAccount["realname"]; ?>
                        </a>
                      </div>
                    <?php else: ?>
                      <label for="inputUsername" class="col-sm-2 col-form-label">Kullanıcı Adı:</label>
                      <div class="col-sm-10">
                        <input type="text" id="inputUsername" class="form-control" name="username" placeholder="Engellenecek oyuncunun kullanıcı adını yazınız.">
                      </div>
                    <?php endif; ?>

                  </div>
                  <div class="form-group row">
                    <label for="selectCategoryID" class="col-sm-2 col-form-label">Kategori:</label>
                    <div class="col-sm-10">
                      <select id="selectCategoryID" class="form-control" name="categoryID" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="1">Site</option>
                        <option value="2">Destek</option>
                        <option value="3">Yorum</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectReasonID" class="col-sm-2 col-form-label">Nedeni:</label>
                    <div class="col-sm-10">
                      <select id="selectReasonID" class="form-control" name="reasonID" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="2">Spam</option>
                        <option value="3">Küfür/Hakaret</option>
                        <option value="4">Hile</option>
                        <option value="5">Reklam</option>
                        <option value="6">Oyuncuları Dolandırmak</option>
                        <option value="1">Diğer</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectDurationStatus" class="col-sm-2 col-form-label">Süre:</label>
                    <div class="col-sm-10">
                      <select id="selectDurationStatus" class="form-control" name="durationStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0">Süresiz</option>
                        <option value="1">Süreli</option>
                      </select>
                    </div>
                  </div>
                  <div id="durationBlock" class="form-group row" style="display: none;">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputDuration" class="form-control form-control-prepended" name="duration" placeholder="Engel süresi (Gün).">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-clock"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('insertBannedAccounts'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <button type="submit" class="btn btn-rounded btn-success" name="insertBannedAccounts">Engelle</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError("Kullanıcı bulunamadı!"); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'update' && get("id")): ?>
    <?php
      $bannedAccounts = $db->prepare("SELECT BA.*, A.realname FROM BannedAccounts BA INNER JOIN Accounts A ON BA.accountID = A.id WHERE BA.id = ?");
      $bannedAccounts->execute(array(get("id")));
      $readBannedAccounts = $bannedAccounts->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Engelle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/engel">Engel</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/engel">Düzenle</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($bannedAccounts->rowCount() > 0) ? $readBannedAccounts["realname"] : "Bulunamadı!"; ?></li>
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
          <?php if ($bannedAccounts->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateBannedAccounts"])) {
                if (post("durationStatus") == 0) {
                  $_POST["duration"] = '1000-01-01 00:00:00';
                }
                else {
                  $_POST["duration"] = createDuration($_POST["duration"]);
                }
                if (!$csrf->validate('updateBannedAccounts')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("categoryID") == null || post("reasonID") == null || post("durationStatus") == null || post("duration") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  $readBannedAccounts["permissions"] = getPermissions($readBannedAccounts["id"]);
                  if (!checkPerm($readAdmin, 'SUPER_ADMIN') && checkPerm($readBannedAccounts["permissions"], 'SUPER_ADMIN')) {
                    echo alertError("Bu işlemi yapabilecek yetkiye sahip değilsiniz!");
                  }
                  else {
                    if (post("categoryID") == 1) {
                      $deleteAccountSessions = $db->prepare("DELETE FROM AccountSessions WHERE accountID = ?");
                      $deleteAccountSessions->execute(array($readBannedAccounts["accountID"]));
                    }
                    $updateBannedAccounts = $db->prepare("UPDATE BannedAccounts SET categoryID = ?, reasonID = ?, expiryDate = ? WHERE id = ?");
                    $updateBannedAccounts->execute(array(post("categoryID"), post("reasonID"), post("duration"), get("id")));
                    echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                  }
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="staticUsername" class="col-sm-2 col-form-label">Kullanıcı Adı:</label>
                    <div class="col-sm-10">
                      <a id="staticUsername" class="form-control-plaintext" href="/yonetim-paneli/hesap/goruntule/<?php echo $readBannedAccounts["accountID"]; ?>">
                        <?php echo $readBannedAccounts["realname"]; ?>
                      </a>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectCategoryID" class="col-sm-2 col-form-label">Kategori:</label>
                    <div class="col-sm-10">
                      <select id="selectCategoryID" class="form-control" name="categoryID" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="1" <?php echo ($readBannedAccounts["categoryID"] == 1) ? 'selected="selected"' : null; ?>>Site</option>
                        <option value="2" <?php echo ($readBannedAccounts["categoryID"] == 2) ? 'selected="selected"' : null; ?>>Destek</option>
                        <option value="3" <?php echo ($readBannedAccounts["categoryID"] == 3) ? 'selected="selected"' : null; ?>>Yorum</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectReasonID" class="col-sm-2 col-form-label">Nedeni:</label>
                    <div class="col-sm-10">
                      <select id="selectReasonID" class="form-control" name="reasonID" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="2" <?php echo ($readBannedAccounts["reasonID"] == 2) ? 'selected="selected"' : null; ?>>Spam</option>
                        <option value="3" <?php echo ($readBannedAccounts["reasonID"] == 3) ? 'selected="selected"' : null; ?>>Küfür/Hakaret</option>
                        <option value="4" <?php echo ($readBannedAccounts["reasonID"] == 4) ? 'selected="selected"' : null; ?>>Hile</option>
                        <option value="5" <?php echo ($readBannedAccounts["reasonID"] == 5) ? 'selected="selected"' : null; ?>>Reklam</option>
                        <option value="6" <?php echo ($readBannedAccounts["reasonID"] == 6) ? 'selected="selected"' : null; ?>>Oyuncuları Dolandırmak</option>
                        <option value="1" <?php echo ($readBannedAccounts["reasonID"] == 1) ? 'selected="selected"' : null; ?>>Diğer</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectDurationStatus" class="col-sm-2 col-form-label">Süre:</label>
                    <div class="col-sm-10">
                      <select id="selectDurationStatus" class="form-control" name="durationStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readBannedAccounts["expiryDate"] == '1000-01-01 00:00:00') ? 'selected="selected"' : null; ?>>Süresiz</option>
                        <option value="1" <?php echo ($readBannedAccounts["expiryDate"] != '1000-01-01 00:00:00') ? 'selected="selected"' : null; ?>>Süreli</option>
                      </select>
                    </div>
                  </div>
                  <div id="durationBlock" class="form-group row" style="<?php echo ($readBannedAccounts["expiryDate"] == '1000-01-01 00:00:00') ? "display: none;" : "display: block;"; ?>">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputDuration" class="form-control form-control-prepended" name="duration" placeholder="Engel süresi (Gün)." value="<?php echo ($readBannedAccounts["expiryDate"] != '1000-01-01 00:00:00') ? getDuration($readBannedAccounts["expiryDate"]) : null; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-clock"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateBannedAccounts'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/engel/sil/<?php echo $readBannedAccounts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateBannedAccounts">Değişiklikleri Kaydet</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError("Kullanıcı bulunamadı!"); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteBannedAccounts = $db->prepare("DELETE FROM BannedAccounts WHERE id = ?");
      $deleteBannedAccounts->execute(array(get("id")));
      go("/yonetim-paneli/engel");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
