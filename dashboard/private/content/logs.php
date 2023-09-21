<?php
  if (!checkPerm($readAdmin, 'MANAGE_LOGS')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  if (get("target") == 'logs' && get("action") == 'getAll') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  }
  
  $actions = array(
    'GENERAL_SETTINGS_UPDATED' => 'Genel Ayarlar güncellendi',
    'SYSTEM_SETTINGS_UPDATED' => 'Sistem Ayarları güncellendi',
    'SEO_SETTINGS_UPDATED' => 'SEO Ayarları güncellendi',
    'SMTP_SETTINGS_UPDATED' => 'SMTP Ayarları güncellendi',
    'WEBHOOK_SETTINGS_UPDATED' => 'Webhook Ayarları güncellendi',
    'SYSTEM_UPDATED' => 'LeaderOS güncellendi',
    'THEME_GENERAL_UPDATED' => 'Tema Genel Ayarları güncellendi',
    'THEME_HEADER_UPDATED' => 'Tema Header Ayarları güncellendi',
    'THEME_COLOR_UPDATED' => 'Tema Renk Ayarları güncellendi',
    'THEME_CSS_UPDATED' => 'Tema CSS Ayarları güncellendi',
    'GAMING_NIGHT_UPDATED' => 'Gaming Gecesi güncellendi'
  );

?>
<?php if (get("target") == 'logs'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Loglar</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Loglar</li>
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
            if (isset($_GET["page"])) {
              if (!is_numeric($_GET["page"])) {
                $_GET["page"] = 1;
              }
              $page = intval(get("page"));
            }
            else {
              $page = 1;
            }
        
            $visiblePageCount = 5;
            $limit = 50;
        
            $logs = $db->query("SELECT id FROM Logs");
            $itemsCount = $logs->rowCount();
            $pageCount = ceil($itemsCount/$limit);
            if ($page > $pageCount) {
              $page = 1;
            }
            $visibleItemsCount = $page * $limit - $limit;
            $logs = $db->query("SELECT L.*, A.realname FROM Logs L INNER JOIN Accounts A ON L.accountID = A.id ORDER BY L.id DESC LIMIT $visibleItemsCount, $limit");
        
            if (isset($_POST["query"])) {
              if (post("query") != null) {
                $logs = $db->prepare("SELECT L.*, A.realname FROM Logs L INNER JOIN Accounts A ON L.accountID = A.id WHERE A.realname LIKE :search OR L.ip LIKE :search ORDER BY L.id DESC");
                $logs->execute(array(
                  "search" => '%'.post("query").'%'
                ));
              }
            }
          ?>
          <?php if ($logs->rowCount() > 0): ?>
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
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="Arama Yap (Kullanıcıi IP Adresi)" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success">Ara</button>
                      <a class="btn btn-sm btn-danger clickdelete" href="/yonetim-paneli/logs/toplu-sil">Tamamını Sil</a>
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
                      <th>Aksiyon</th>
                      <th>IP Adresi</th>
                      <th>Tarih</th>
                      <th class="text-right">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    <?php foreach ($logs as $readLogs): ?>
                      <tr>
                        <td class="text-center" style="width: 40px;">
                          #<?php echo $readLogs["id"]; ?>
                        </td>
                        <td>
                          <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readLogs["accountID"]; ?>">
                            <?php echo $readLogs["realname"]; ?>
                          </a>
                        </td>
                        <td>
                          <?php echo $actions[$readLogs["action"]]; ?>
                        </td>
                        <td>
                          <?php echo $readLogs["ip"]; ?>
                        </td>
                        <td>
                          <?php echo convertTime($readLogs["creationDate"], 2, true); ?>
                        </td>
                        <td class="text-right">
                          <?php if (checkPerm($readAdmin, 'SUPER_ADMIN')): ?>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/logs/sil/<?php echo $readLogs["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                              <i class="fe fe-trash-2"></i>
                            </a>
                          <?php endif; ?>
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
                    <a class="page-link" href="/yonetim-paneli/logs/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/yonetim-paneli/logs/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/yonetim-paneli/logs/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
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
  <?php elseif (get("action") == 'deleteAll'): ?>
    <?php
      if (checkPerm($readAdmin, 'SUPER_ADMIN')) {
        $deleteLogs = $db->prepare("TRUNCATE TABLE Logs");
        $deleteLogs->execute();
      }
      go("/yonetim-paneli/logs");
    ?>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      if (checkPerm($readAdmin, 'SUPER_ADMIN')) {
        $deleteLogs = $db->prepare("DELETE FROM Logs WHERE id = ?");
        $deleteLogs->execute(array(get("id")));
      }
      go("/yonetim-paneli/logs");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
