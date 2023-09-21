<?php
  if (!checkPerm($readAdmin, 'MANAGE_STORE')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  if (get("target") == 'vip' && (get("action") == 'insert' || get("action") == 'update')) {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/vip.server.js');
  }
?>
<?php if (get("target") == 'vip'): ?>
  <?php if (get("action") == 'list'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">VIP Tablo</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/vip/tablo">VIP Tablo</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Liste</li>
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
            $VIPs = $db->query("SELECT VT.*, S.name as serverName, S.slug as serverSlug, PC.name as categoryName, PC.slug as categorySlug FROM VIPTables VT INNER JOIN Servers S ON VT.serverID = S.id LEFT JOIN ProductCategories PC ON VT.categoryID = PC.id ORDER BY VT.id DESC");
          ?>
          <?php if ($VIPs->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["tableID", "serverName", "categoryName"]'>
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
                    <a class="btn btn-sm btn-white" href="/yonetim-paneli/vip/tablo/ekle">VIP Tablo Ekle</a>
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
                        <a href="#" class="text-muted sort" data-sort="tableID">
                          #ID
                        </a>
                      </th>
                      <th>
                        <a href="#" class="text-muted sort" data-sort="serverName">
                          Sunucu
                        </a>
                      </th>
                      <th>
                        <a href="#" class="text-muted sort" data-sort="categoryName">
                          Kategori
                        </a>
                      </th>
                      <th class="text-right">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    <?php foreach ($VIPs as $readVIPs): ?>
                      <tr>
                        <td class="tableID text-center" style="width: 40px;">
                          <a href="/yonetim-paneli/vip/tablo/duzenle/<?php echo $readVIPs["id"]; ?>">
                            #<?php echo $readVIPs["id"]; ?>
                          </a>
                        </td>
                        <td class="serverName">
                          <?php echo $readVIPs["serverName"]; ?>
                        </td>
                        <td class="categoryName">
                          <?php echo $readVIPs["categoryName"]; ?>
                        </td>
                        <td class="text-right">
                          <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/vip/tablo/duzenle/<?php echo $readVIPs["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                            <i class="fe fe-edit-2"></i>
                          </a>
                          <?php
                            $storeURL = "/magaza";
                            if ($readVIPs["categorySlug"] == null)
                              $storeURL .= "/".$readVIPs["serverSlug"];
                            else
                              $storeURL .= "/".$readVIPs["serverSlug"]."/".$readVIPs["categorySlug"];
                          ?>
                          <a class="btn btn-sm btn-rounded-circle btn-primary" href="<?php echo $storeURL; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                            <i class="fe fe-eye"></i>
                          </a>
                          <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/vip/tablo/sil/<?php echo $readVIPs["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
                  <h2 class="header-title">VIP Tablo Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a>
                      </li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/vip/tablo/ekle">VIP Tablo</a></li>
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
            require_once(__ROOT__ . "/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["insertVIP"])) {
              if (!$csrf->validate('insertVIP')) {
                echo alertError("Sistemsel bir sorun oluştu!");
                echo goDelay("/yonetim-paneli/vip/tablo/ekle", 2);
              } else if (post("serverID") == null || !count(array_filter($_POST["vips"])) || !count(array_filter($_POST["titles"]))) {
                echo alertError("Lütfen boş alan bırakmayınız!");
              } else {
                $insertVipTable = $db->prepare("INSERT INTO VIPTables (serverID, categoryID) VALUES (?, ?)");
                $insertVipTable->execute(array(post("serverID"), post("categoryID")));
                $vipTableID = $db->lastInsertId();
                
                if (count(array_filter($_POST["vips"]))) {
                  $insertVIP = $db->prepare("INSERT INTO VIPs (vipID, tableID) VALUES (?, ?)");
                  foreach ($_POST["vips"] as $vipID99) {
                    $insertVIP->execute(array($vipID99, $vipTableID));
                  }
                }
                if (count(array_filter($_POST["titles"]))) {
                  foreach ($_POST["titles"] as $key => $title) {
                    $insertVIPTitles = $db->prepare("INSERT INTO VIPTitles (title, tableID) VALUES (?, ?)");
                    $insertVIPTitles->execute(array($title, $vipTableID));
                  }
                }
                echo alertSuccess("VIP Tablo başarıyla eklendi!");
                echo goDelay("/yonetim-paneli/vip/tablo/ekle", 1);
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group row">
                  <label for="selectServerID" class="col-sm-2 col-form-label">Sunucu:</label>
                  <div class="col-sm-10">
                    <?php $servers = $db->query("SELECT * FROM Servers"); ?>
                    <select id="selectServerID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="serverID" <?php echo ($servers->rowCount() == 0) ? "disabled" : null; ?>>
                      <?php if ($servers->rowCount() > 0): ?>
                        <option disabled selected>Sunucu seçiniz.</option>
                        <?php foreach ($servers as $readServers): ?>
                          <option value="<?php echo $readServers["id"]; ?>"><?php echo $readServers["name"]; ?></option>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <option>Sunucu bulunamadı!</option>
                      <?php endif; ?>
                    </select>
                  </div>
                </div>
                <div id="vipBlock">
                  <div class="form-group row">
                    <label for="selectCategoryID" class="col-sm-2 col-form-label">Kategori:</label>
                    <div class="col-sm-10">
                      <div id="c-loading2" style="display: none; margin-top: 7px">Yükleniyor...</div>
                      <div id="categories">
                        <select name="categoryID" id="selectCategoryID"  class="form-control" data-toggle="select" data-minimum-results-for-search="-1">
                          <option value="0">Yok</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectVIP" class="col-sm-2 col-form-label">VIP Seç:</label>
                    <div class="col-sm-10">
                      <div id="c-loading" style="display: none; margin-top: 7px">Yükleniyor...</div>
                      <div id="products">
                        <select id="selectVIP" class="form-control" data-toggle="select" name="vips[]" multiple="multiple">
                          <option>VIP Seç</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputTable" class="col-sm-2 col-form-label">Başlıklar:</label>
                    <div class="col-sm-10">
                      <div class="table-responsive">
                        <table id="tableitems" class="table table-sm table-hover table-nowrap array-table">
                          <thead>
                          <tr>
                            <th>Başlık</th>
                            <th class="text-center pt-0 pb-0 align-middle">
                              <button type="button" class="btn btn-sm btn-rounded-circle btn-success addTableItem">
                                <i class="fe fe-plus"></i>
                              </button>
                            </th>
                          </tr>
                          </thead>
                          <tbody id="vipTitlesBlock">
                          <tr>
                            <td>
                              <div class="input-group input-group-merge">
                                <input type="text" class="form-control form-control-prepended" name="titles[]" placeholder="VIP Tablo Başlıklarını yazınız.">
                                <div class="input-group-prepend">
                                  <div class="input-group-text">
                                    <span class="fe fe-terminal"></span>
                                  </div>
                                </div>
                              </div>
                              <input type="hidden" name="titleIDs[]" value="">
                            </td>
                            <td class="text-center align-middle">
                              <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                <i class="fe fe-trash-2"></i>
                              </button>
                            </td>
                          </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('insertVIP'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertVIP">
                      Ekle
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'update' && get("id")): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">VIP Tablo Düzenle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a>
                      </li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/vip/tablo/duzenle/<?php echo get("id"); ?>">VIP Tablo</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Düzenle</li>
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
            $vipTables = $db->prepare("SELECT * FROM VIPTables WHERE id = ?");
            $vipTables->execute(array(get("id")));
            $readVipTables = $vipTables->fetch();
          ?>
          <?php if ($vipTables->rowCount() > 0): ?>
            <?php
              $vips = $db->prepare("SELECT * FROM VIPs WHERE tableID = ?");
              $vips->execute(array(get("id")));
              $vipsData = $vips->fetchAll();
            ?>
            <?php
            require_once(__ROOT__ . "/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["updateVIP"])) {
              if (!$csrf->validate('updateVIP')) {
                echo alertError("Sistemsel bir sorun oluştu!");
                echo goDelay("/yonetim-paneli/vip/tablo/duzenle/".get("id"), 2);
              } else if (post("categoryID") == null || !count(array_filter($_POST["vips"])) || !count(array_filter($_POST["titles"]))) {
                echo alertError("Lütfen boş alan bırakmayınız!");
              } else {
                if (count(array_filter($_POST["vips"]))) {
                  $deleteVIPs = $db->prepare("DELETE FROM VIPs WHERE tableID = ?");
                  $deleteVIPs->execute(array($readVipTables["id"]));
                  $insertVIP = $db->prepare("INSERT INTO VIPs (vipID, tableID) VALUES (?, ?)");
                  foreach ($_POST["vips"] as $vipID99) {
                    $insertVIP->execute(array($vipID99, $readVipTables["id"]));
                  }
                }
                if (count(array_filter($_POST["titles"]))) {
                  $deleteVIPTitles = $db->prepare("DELETE FROM VIPTitles WHERE tableID = ?");
                  $deleteVIPTitles->execute(array($readVipTables["id"]));
                  foreach ($_POST["titles"] as $key => $title) {
                    if ($_POST["titleIDs"][$key] > 0) {
                      $insertVIPTitles = $db->prepare("INSERT INTO VIPTitles (id, title, tableID) VALUES (?, ?, ?)");
                      $insertVIPTitles->execute(array($_POST["titleIDs"][$key], $title, $readVipTables["id"]));
                    } else {
                      $insertVIPTitles = $db->prepare("INSERT INTO VIPTitles (title, tableID) VALUES (?, ?)");
                      $insertVIPTitles->execute(array($title, $readVipTables["id"]));
                    }
                  }
                }
                echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                echo goDelay("/yonetim-paneli/vip/tablo/duzenle/".get("id"), 1);
              }
            }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label for="selectServerID" class="col-sm-2 col-form-label">Sunucu:</label>
                    <div class="col-sm-10">
                      <?php $servers = $db->query("SELECT * FROM Servers"); ?>
                      <select id="selectServerID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="serverID" disabled>
                        <?php if ($servers->rowCount() > 0): ?>
                          <?php foreach ($servers as $readServers): ?>
                            <option value="<?php echo $readServers["id"]; ?>" <?php echo ($readVipTables["serverID"] == $readServers["id"]) ? 'selected="selected"' : null ?>><?php echo $readServers["name"]; ?></option>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <option>Sunucu bulunamadı!</option>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                  <div id="vipBlock">
                    <div class="form-group row">
                      <label for="selectCategoryID" class="col-sm-2 col-form-label">Kategori:</label>
                      <div class="col-sm-10">
                        <div id="c-loading2" style="display: none; margin-top: 7px">Yükleniyor...</div>
                        <div id="categories">
                          <select name="categoryID" id="selectCategoryID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1">
                            <option value="0">Yok</option>
                            <?php
                              $categories = $db->prepare("SELECT * FROM ProductCategories WHERE serverID = ?");
                              $categories->execute(array($readVipTables["serverID"]));
                            ?>
                            <?php foreach ($categories as $readCategories): ?>
                              <option value="<?php echo $readCategories["id"]; ?>"><?php echo $readCategories["name"]; ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectVIP" class="col-sm-2 col-form-label">VIP Seç:</label>
                      <div class="col-sm-10">
                        <div id="c-loading" style="display: none; margin-top: 7px">Yükleniyor...</div>
                        <div id="products">
                          <?php
                            $products = $db->prepare("SELECT * FROM Products WHERE serverID = ?");
                            $products->execute(array($readVipTables["serverID"]));
                          ?>
                          <select id="selectVIP" class="form-control" data-toggle="select" name="vips[]" multiple="multiple">
                            <?php if ($products->rowCount() > 0): ?>
                              <?php foreach ($products as $readProducts): ?>
                                <?php
                                $selectedVIPs = $db->prepare("SELECT * FROM VIPs WHERE vipID = ? AND tableID = ?");
                                $selectedVIPs->execute(array($readProducts["id"], $readVipTables["id"]));
                                ?>
                                <option <?php echo ($selectedVIPs->rowCount() > 0) ? "selected" : null ?> value="<?php echo $readProducts["id"]; ?>"><?php echo $readProducts["name"]; ?></option>
                              <?php endforeach; ?>
                            <?php endif; ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputTable" class="col-sm-2 col-form-label">Başlıklar:</label>
                      <div class="col-sm-10">
                        <div class="table-responsive">
                          <table id="tableitems" class="table table-sm table-hover table-nowrap array-table">
                            <thead>
                            <tr>
                              <th>Başlık</th>
                              <th class="text-center pt-0 pb-0 align-middle">
                                <button type="button" class="btn btn-sm btn-rounded-circle btn-success addTableItem">
                                  <i class="fe fe-plus"></i>
                                </button>
                              </th>
                            </tr>
                            </thead>
                            <tbody id="vipTitlesBlock">
                            <?php
                              $vipTitles = $db->prepare("SELECT * FROM VIPTitles WHERE tableID = ?");
                              $vipTitles->execute(array($readVipTables["id"]));
                              $vipTitlesData = $vipTitles->fetchAll();
                            ?>
                            <?php if ($vipTitles->rowCount() > 0): ?>
                              <?php foreach ($vipTitlesData as $readVipTitle): ?>
                                <tr>
                                  <td>
                                    <div class="input-group input-group-merge">
                                      <input type="text" class="form-control form-control-prepended" name="titles[]" placeholder="VIP Tablo Başlıklarını yazınız." value="<?php echo $readVipTitle["title"]; ?>">
                                      <div class="input-group-prepend">
                                        <div class="input-group-text">
                                          <span class="fe fe-terminal"></span>
                                        </div>
                                      </div>
                                    </div>
                                    <input type="hidden" name="titleIDs[]" value="<?php echo $readVipTitle["id"]; ?>">
                                  </td>
                                  <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                      <i class="fe fe-trash-2"></i>
                                    </button>
                                  </td>
                                </tr>
                              <?php endforeach; ?>
                            <?php else: ?>
                              <tr>
                                <td>
                                  <div class="input-group input-group-merge">
                                    <input type="text" class="form-control form-control-prepended" name="titles[]" placeholder="VIP Tablo Başlıklarını yazınız.">
                                    <div class="input-group-prepend">
                                      <div class="input-group-text">
                                        <span class="fe fe-terminal"></span>
                                      </div>
                                    </div>
                                  </div>
                                  <input type="hidden" name="titleIDs[]" value="">
                                </td>
                                <td class="text-center align-middle">
                                  <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                    <i class="fe fe-trash-2"></i>
                                  </button>
                                </td>
                              </tr>
                            <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateVIP'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <button type="submit" class="btn btn-rounded btn-success" name="updateVIP">
                        Kaydet
                      </button>
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
      $deleteVIPTables = $db->prepare("DELETE FROM VIPTables WHERE id = ?");
      $deleteVIPTables->execute(array(get("id")));
      go("/yonetim-paneli/vip/tablo");
    ?>
  <?php elseif (get("action") == 'featureList'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">VIP Özellik</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a>
                      </li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/vip/tablo">VIP Tablo</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Özellik</li>
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
            $VIPs = $db->query("SELECT VT.*, S.name as serverName, S.slug as serverSlug, PC.name as categoryName, PC.slug as categorySlug FROM VIPTables VT INNER JOIN Servers S ON VT.serverID = S.id LEFT JOIN ProductCategories PC ON VT.categoryID = PC.id ORDER BY VT.id DESC");
          ?>
          <?php if ($VIPs->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["tableID", "serverName", "categoryName"]'>
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
                    <a class="btn btn-sm btn-white" href="/yonetim-paneli/vip/tablo/ekle">VIP Tablo Ekle</a>
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
                        <a href="#" class="text-muted sort" data-sort="tableID">
                          #ID
                        </a>
                      </th>
                      <th>
                        <a href="#" class="text-muted sort" data-sort="serverName">
                          Sunucu
                        </a>
                      </th>
                      <th>
                        <a href="#" class="text-muted sort" data-sort="categoryName">
                          Kategori
                        </a>
                      </th>
                      <th class="text-right">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    <?php foreach ($VIPs as $readVIPs): ?>
                      <tr>
                        <td class="tableID text-center" style="width: 40px;">
                          <a href="/yonetim-paneli/vip/ozellik/duzenle/<?php echo $readVIPs["id"]; ?>">
                            #<?php echo $readVIPs["id"]; ?>
                          </a>
                        </td>
                        <td class="serverName">
                          <?php echo $readVIPs["serverName"]; ?>
                        </td>
                        <td class="categoryName">
                          <?php echo $readVIPs["categoryName"]; ?>
                        </td>
                        <td class="text-right">
                          <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/vip/ozellik/duzenle/<?php echo $readVIPs["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                            <i class="fe fe-edit-2"></i>
                          </a>
                          <?php
                            $storeURL = "/magaza";
                            if ($readVIPs["categorySlug"] == null)
                              $storeURL .= "/".$readVIPs["serverSlug"];
                            else
                              $storeURL .= "/".$readVIPs["serverSlug"]."/".$readVIPs["categorySlug"];
                          ?>
                          <a class="btn btn-sm btn-rounded-circle btn-primary" href="<?php echo $storeURL; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                            <i class="fe fe-eye"></i>
                          </a>
                          <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/vip/tablo/sil/<?php echo $readVIPs["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
  <?php elseif (get("action") == 'featureInsert' && get("id")): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">VIP Özellik Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a>
                      </li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/vip/ozellik/duzenle/<?php echo get("id"); ?>">VIP Özellik</a></li>
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
            $vipTables = $db->prepare("SELECT * FROM VIPTables WHERE id = ?");
            $vipTables->execute(array(get("id")));
          ?>
          <?php if ($vipTables->rowCount() > 0): ?>
            <?php
            $vips = $db->prepare("SELECT * FROM VIPs WHERE tableID = ?");
            $vips->execute(array(get("id")));
            $vipsData = $vips->fetchAll();
            $vipTitles = $db->prepare("SELECT * FROM VIPTitles WHERE tableID = ?");
            $vipTitles->execute(array(get("id")));
            $vipTitlesData = $vipTitles->fetchAll();
            require_once(__ROOT__ . "/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["updateVIPFeatures"])) {
              if (!$csrf->validate('updateVIPFeatures')) {
                echo alertError("Sistemsel bir sorun oluştu!");
                echo goDelay("/yonetim-paneli/vip/ozellik/duzenle/" . get("id"), 1);
              } else {
                foreach ($vipsData as $readVips) {
                  $db->prepare("DELETE FROM VIPDesc WHERE vipID = ?")->execute(array($readVips["vipID"]));
                }
                foreach ($vipTitlesData as $readVIPTitles) {
                  foreach ($vipsData as $readVips) {
                    if ($_POST[$readVips["vipID"] . "_" . $readVIPTitles["id"]] != '') {
                      $insertVIPDesc = $db->prepare("INSERT INTO VIPDesc (vipID, titleID, description) VALUES (?, ?, ?)");
                      $insertVIPDesc->execute(array($readVips["vipID"], $readVIPTitles["id"], $_POST[$readVips["vipID"] . "_" . $readVIPTitles["id"]]));
                    }
                  }
                }
                echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                echo goDelay("/yonetim-paneli/vip/ozellik/duzenle/" . get("id"), 1);
              }
            }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <div class="table-responsive">
                      <table class="table">
                        <thead>
                        <tr>
                          <th scope="col">Özellik</th>
                          <?php foreach ($vipsData as $readVips): ?>
                            <?php
                            $product = $db->prepare("SELECT name FROM Products WHERE id = ?");
                            $product->execute(array($readVips["vipID"]));
                            if ($product->rowCount() > 0) {
                              $readProduct = $product->fetch();
                              echo '<th scope="col">' . $readProduct["name"] . '</th>';
                            }
                            ?>
                          <?php endforeach ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($vipTitlesData as $readVIPTitles): ?>
                          <tr>
                            <th scope="row">
                              <?php echo $readVIPTitles["title"]; ?>
                            </th>
                            <?php foreach ($vipsData as $readVips): ?>
                              <?php
                              $vipDesc = $db->prepare("SELECT * FROM VIPDesc WHERE vipID = ? AND titleID = ?");
                              $vipDesc->execute(array($readVips["vipID"], $readVIPTitles["id"]));
                              ?>
                              <td>
                                <input type="text" name="<?php echo $readVips["vipID"] . "_" . $readVIPTitles["id"]; ?>" class="form-control" placeholder="Açıklama giriniz." value="<?php echo ($vipDesc->rowCount() > 0) ? $vipDesc->fetch()["description"] : null; ?>">
                              </td>
                            <?php endforeach; ?>
                          </tr>
                        <?php endforeach ?>
                        </tbody>
                      </table>
                      <small class="form-text text-muted pt-2 h4">
                        <strong><i class="mr-1 fa fa-check text-success"></i> ikonu eklemek için</strong> +
                      </small>
                      <small class="form-text text-muted pt-2 h4">
                        <strong><i class="mr-1 fa fa-times text-danger"></i> ikonu eklemek için</strong> -
                      </small>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateVIPFeatures'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <button type="submit" class="btn btn-rounded btn-success" name="updateVIPFeatures">
                        Kaydet
                      </button>
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
  <?php elseif (get("action") == 'explainList'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">VIP Açıklama</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a>
                      </li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/vip/tablo">VIP Tablo</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Açıklama</li>
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
            $VIPs = $db->query("SELECT VT.*, S.name as serverName, S.slug as serverSlug, PC.name as categoryName, PC.slug as categorySlug FROM VIPTables VT INNER JOIN Servers S ON VT.serverID = S.id LEFT JOIN ProductCategories PC ON VT.categoryID = PC.id ORDER BY VT.id DESC");
          ?>
          <?php if ($VIPs->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["tableID", "serverName", "categoryName"]'>
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
                    <a class="btn btn-sm btn-white" href="/yonetim-paneli/vip/tablo/ekle">VIP Tablo Ekle</a>
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
                        <a href="#" class="text-muted sort" data-sort="tableID">
                          #ID
                        </a>
                      </th>
                      <th>
                        <a href="#" class="text-muted sort" data-sort="serverName">
                          Sunucu
                        </a>
                      </th>
                      <th>
                        <a href="#" class="text-muted sort" data-sort="categoryName">
                          Kategori
                        </a>
                      </th>
                      <th class="text-right">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    <?php foreach ($VIPs as $readVIPs): ?>
                      <tr>
                        <td class="tableID text-center" style="width: 40px;">
                          <a href="/yonetim-paneli/vip/aciklama/duzenle/<?php echo $readVIPs["id"]; ?>">
                            #<?php echo $readVIPs["id"]; ?>
                          </a>
                        </td>
                        <td class="serverName">
                          <?php echo $readVIPs["serverName"]; ?>
                        </td>
                        <td class="categoryName">
                          <?php echo $readVIPs["categoryName"]; ?>
                        </td>
                        <td class="text-right">
                          <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/vip/aciklama/duzenle/<?php echo $readVIPs["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                            <i class="fe fe-edit-2"></i>
                          </a>
                          <?php
                            $storeURL = "/magaza";
                            if ($readVIPs["categorySlug"] == null)
                              $storeURL .= "/".$readVIPs["serverSlug"];
                            else
                              $storeURL .= "/".$readVIPs["serverSlug"]."/".$readVIPs["categorySlug"];
                          ?>
                          <a class="btn btn-sm btn-rounded-circle btn-primary" href="<?php echo $storeURL; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                            <i class="fe fe-eye"></i>
                          </a>
                          <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/vip/tablo/sil/<?php echo $readVIPs["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
  <?php elseif (get("action") == 'explainInsert' && get("id")): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">VIP Açıklama Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a>
                      </li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/vip/aciklama/duzenle/<?php echo get("id"); ?>">VIP Tablo</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Açıklama Ekle</li>
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
            $vipTables = $db->prepare("SELECT * FROM VIPTables WHERE id = ?");
            $vipTables->execute(array(get("id")));
          ?>
          <?php if ($vipTables->rowCount() > 0): ?>
            <?php
            $vipTitles = $db->prepare("SELECT * FROM VIPTitles WHERE tableID = ?");
            $vipTitles->execute(array(get("id")));
            $vipTitlesData = $vipTitles->fetchAll();
            require_once(__ROOT__ . "/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["updateVIPExplain"])) {
              if (!$csrf->validate('updateVIPExplain')) {
                echo alertError("Sistemsel bir sorun oluştu!");
                echo goDelay("/yonetim-paneli/vip/aciklama/duzenle/" . get("id"), 1);
              } else {
                foreach ($vipTitlesData as $readVIPTitles) {
                  if ($_POST[$readVIPTitles["id"]] == "" || $_POST[$readVIPTitles["id"]] == NULL) {
                    $_POST[$readVIPTitles["id"]] = NULL;
                  }
                  $deleteVIPExplain = $db->prepare("DELETE FROM VIPExplain WHERE titleID = ?");
                  $deleteVIPExplain->execute(array($readVIPTitles["id"]));
                  $insertVIPExplain = $db->prepare("INSERT INTO VIPExplain (titleID, name) VALUES (?, ?)");
                  $insertVIPExplain->execute(array($readVIPTitles["id"], $_POST[$readVIPTitles["id"]]));
                }
                echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                echo goDelay("/yonetim-paneli/vip/aciklama/duzenle/" . get("id"), 1);
              }
            }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <div class="table-responsive">
                      <table class="table">
                        <thead>
                        <tr>
                          <th scope="col">Özellik</th>
                          <th>Açıklama</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($vipTitlesData as $readVIPTitles): ?>
                          <tr>
                            <th scope="row">
                              <?php echo $readVIPTitles["title"]; ?>
                            </th>
                            <?php
                              $vipExplain = $db->prepare("SELECT * FROM VIPExplain WHERE titleID = ?");
                              $vipExplain->execute(array($readVIPTitles["id"]));
                            ?>
                            <td>
                              <input type="text" name="<?php echo $readVIPTitles["id"]; ?>" class="form-control" placeholder="Açıklama giriniz." value="<?php echo ($vipExplain->rowCount() > 0) ? $vipExplain->fetch()["name"] : null; ?>">
                            </td>
                          </tr>
                        <?php endforeach ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateVIPExplain'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <button type="submit" class="btn btn-rounded btn-success" name="updateVIPExplain">Kaydet</button>
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
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php endif; ?>