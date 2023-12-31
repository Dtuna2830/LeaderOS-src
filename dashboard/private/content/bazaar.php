<?php
  if (!checkPerm($readAdmin, 'MANAGE_BAZAAR')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
?>
<?php if (get("target") == 'bazaar'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Pazar</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Pazar</li>
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
            
            $bazaar = $db->query("SELECT id FROM BazaarItems");
            $itemsCount = $bazaar->rowCount();
            $pageCount = ceil($itemsCount/$limit);
            if ($page > $pageCount) {
              $page = 1;
            }
            $visibleItemsCount = $page * $limit - $limit;
            $bazaar = $db->query("SELECT BI.*, A.realname, S.name as serverName FROM BazaarItems BI INNER JOIN Accounts A ON A.id = BI.owner INNER JOIN Servers S ON S.id = BI.serverID ORDER BY BI.id DESC LIMIT $visibleItemsCount, $limit");
            
            if (isset($_POST["query"])) {
              if (post("query") != null) {
                $bazaar = $db->prepare("SELECT BI.*, A.realname, S.name as serverName FROM BazaarItems BI INNER JOIN Accounts A ON A.id = BI.owner INNER JOIN Servers S ON S.id = BI.serverID WHERE A.realname LIKE :search OR S.name LIKE :search OR BI.name LIKE :search ORDER BY BI.id DESC");
                $bazaar->execute(array(
                  "search" => '%'.post("query").'%'
                ));
              }
            }
          ?>
          <?php if ($bazaar->rowCount() > 0): ?>
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
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="Arama Yap (Kullanıcı, Ürün Adı, Sunucu)" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success">Ara</button>
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
                      <th>Sunucu</th>
                      <th>Ürün</th>
                      <th>Fiyat</th>
                      <th>Durum</th>
                      <th>Tarih</th>
                      <th class="text-right">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    <?php foreach ($bazaar as $readBazaar): ?>
                      <tr>
                        <td class="text-center" style="width: 40px;">
                          #<?php echo $readBazaar["id"]; ?>
                        </td>
                        <td>
                          <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readBazaar["owner"]; ?>">
                            <?php echo $readBazaar["realname"]; ?>
                          </a>
                        </td>
                        <td>
                          <?php echo $readBazaar["serverName"]; ?>
                        </td>
                        <td>
                          <?php echo $readBazaar["name"]; ?>
                        </td>
                        <td>
                          <?php echo $readBazaar["price"] == 0 ? "Satışta Değil" : $readBazaar["price"]; ?>
                        </td>
                        <td>
                          <?php if ($readBazaar["sold"] == 0): ?>
                            <?php if ($readBazaar["price"] == 0): ?>
                              <span class="badge badge-pill badge-warning">Depoda</span>
                            <?php else: ?>
                              <span class="badge badge-pill badge-success">Satışta</span>
                            <?php endif; ?>
                          <?php else: ?>
                            <span class="badge badge-pill badge-danger">Satıldı</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php echo convertTime($readBazaar["creationDate"], 2, true); ?>
                        </td>
                        <td class="text-right">
                          <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/pazar/duzenle/<?php echo $readBazaar["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                            <i class="fe fe-edit-2"></i>
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
                    <a class="page-link" href="/yonetim-paneli/pazar/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/yonetim-paneli/pazar/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/yonetim-paneli/pazar/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
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
  <?php elseif (get("action") == 'update' && get("id")): ?>
    <?php
      $product = $db->prepare("SELECT BI.*, A.realname, S.name as serverName FROM BazaarItems BI INNER JOIN Accounts A ON A.id = BI.owner INNER JOIN Servers S ON S.id = BI.serverID WHERE BI.id = ?");
      $product->execute(array(get("id")));
      $readProduct = $product->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Pazar Ürünü Düzenle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/pazar">Pazar</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/pazar">Pazar Ürünü Düzenle</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($product->rowCount() > 0) ? $readProduct["name"] : "Bulunamadı!"; ?></li>
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
          <?php if ($product->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateProducts"])) {
                if (!$csrf->validate('updateProducts')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("owner") == null || post("price") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  $findAccount = $db->prepare("SELECT id FROM Accounts WHERE realname = ?");
                  $findAccount->execute(array(post("owner")));
                  $readFindAccount = $findAccount->fetch();
                  if ($findAccount->rowCount() > 0) {
                    $updateProducts = $db->prepare("UPDATE BazaarItems SET price = ?, description = ?, owner = ? WHERE id = ?");
                    $updateProducts->execute(array(post("price"), post("description"), $readFindAccount["id"], get("id")));
                    echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                  }
                  else {
                    echo alertError("Bu kullanıcı bulunamadı!");
                  }
                }
              }
            ?>
            <div class="row">
              <div class="col-md-4">
                <div class="card">
                  <div class="text-center py-3">
                    <img width="64px" src="/apps/main/public/assets/img/items/<?php echo strtolower($readProduct["itemID"]).".png"; ?>" />
                  </div>
                  <div class="card-body">
                    <div class="form-group">
                      <strong>Ürün Adı:</strong>
                      <div>
                        <input type="text" class="form-control-plaintext" value="<?php echo $readProduct["name"]; ?>" readonly>
                      </div>
                    </div>
                    <div class="form-group">
                      <strong>Adet:</strong>
                      <div>
                        <?php echo $readProduct["amount"]; ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <strong>Dayanıklık:</strong>
                      <div>
                        <?php echo ($readProduct["durability"] > $readProduct["maxDurability"] ? $readProduct["maxDurability"] : $readProduct["durability"])."/".$readProduct["maxDurability"]; ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <strong>Sunucu:</strong>
                      <div>
                        <?php echo $readProduct["serverName"]; ?>
                      </div>
                    </div>
                    <?php if ($readProduct["lore"] != null && $readProduct["lore"] != ""): ?>
                      <div class="form-group">
                        <strong>Lore:</strong>
                        <div>
                          <?php echo str_replace("\n", "<br>", $readProduct["lore"]); ?>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if ($readProduct["enchantments"] != null && $readProduct["enchantments"] != ""): ?>
                      <div class="form-group">
                        <strong>Büyüler:</strong>
                        <div>
                          <?php echo $readProduct["enchantments"]; ?>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <div class="col-md-8">
                <div class="card">
                  <div class="card-body">
                    <form action="" method="post">
                      <div class="form-group row">
                        <label for="inputOwner" class="col-sm-2 col-form-label">Satıcı:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputOwner" name="owner" class="form-control" value="<?php echo $readProduct["realname"]; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputPrice" class="col-sm-2 col-form-label">Fiyat:</label>
                        <div class="col-sm-10">
                          <div class="input-group input-group-merge">
                            <input type="number" id="inputPrice" class="form-control form-control-prepended" name="price" placeholder="Ürün fiyatını yazınız." value="<?php echo $readProduct["price"] == 0 ? null : $readProduct["price"]; ?>">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <span class="fa fa-coins"></span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="textareaDetails" class="col-sm-2 col-form-label">Detaylar:</label>
                        <div class="col-sm-10">
                          <textarea id="textareaDetails" class="form-control" name="description" placeholder="İlan detaylarını/özelliklerini yazınız."><?php echo $readProduct["description"]; ?></textarea>
                        </div>
                      </div>
                      <?php echo $csrf->input('updateProducts'); ?>
                      <?php if ($readProduct["sold"] == 0): ?>
                        <div class="clearfix">
                          <div class="float-right">
                            <?php if ($readProduct["price"] > 0): ?>
                              <a class="btn btn-rounded btn-danger" href="/yonetim-paneli/pazar/sil/<?php echo $readProduct["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                                Pazardan Kaldır
                              </a>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-rounded btn-success" name="updateProducts">
                              <?php echo $readProduct["price"] == 0 ? "Satışa Çıkar" : "Güncelle" ?>
                            </button>
                          </div>
                        </div>
                      <?php endif; ?>
                    </form>
                  </div>
                </div>
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
      $deleteBazaarItem = $db->prepare("UPDATE BazaarItems SET price = ? WHERE id = ?");
      $deleteBazaarItem->execute(array(0, get("id")));
      go("/yonetim-paneli/pazar/duzenle/".get("id"));
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'bazaar-history'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Pazar Geçmişi</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/pazar">Pazar</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Pazar Geçmişi</li>
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

            $bazaarHistory = $db->query("SELECT id FROM BazaarHistory");
            $itemsCount = $bazaarHistory->rowCount();
            $pageCount = ceil($itemsCount/$limit);
            if ($page > $pageCount) {
              $page = 1;
            }
            $visibleItemsCount = $page * $limit - $limit;
            $bazaarHistory = $db->query("SELECT BH.*, BI.name as itemName, BI.price as itemPrice, A.realname FROM BazaarHistory BH INNER JOIN BazaarItems BI ON BH.itemID = BI.id INNER JOIN Accounts A ON A.id = BH.accountID ORDER BY BH.id DESC LIMIT $visibleItemsCount, $limit");

            if (isset($_POST["query"])) {
              if (post("query") != null) {
                $bazaarHistory = $db->prepare("SELECT BH.*, BI.name as itemName, BI.price as itemPrice, A.realname FROM BazaarHistory BH INNER JOIN BazaarItems BI ON BH.itemID = BI.id INNER JOIN Accounts A ON A.id = BH.accountID WHERE A.realname LIKE :search ORDER BY BH.id DESC");
                $bazaarHistory->execute(array(
                  "search" => '%'.post("query").'%'
                ));
              }
            }
          ?>
          <?php if ($bazaarHistory->rowCount() > 0): ?>
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
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="Arama Yap (Kullanıcı)" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success">Ara</button>
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
                        <th>Ürün</th>
                        <th>Fiyat</th>
                        <th class="text-center">İşlem</th>
                        <th>Tarih</th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($bazaarHistory as $readBazaarHistory): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            #<?php echo $readBazaarHistory["id"]; ?>
                          </td>
                          <td>
                            <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readBazaarHistory["accountID"]; ?>">
                              <?php echo $readBazaarHistory["realname"]; ?>
                            </a>
                          </td>
                          <td>
                            <?php echo $readBazaarHistory["itemName"]; ?>
                          </td>
                          <td>
                            <?php echo $readBazaarHistory["itemPrice"]; ?>
                          </td>
                          <td class="text-center">
                            <?php if ($readBazaarHistory["type"] == 0): ?>
                              <span class="text-danger" data-toggle="tooltip" data-placement="top" title="Satın Alındı">-<i class="fa fa-coins"></i></span>
                            <?php elseif ($readBazaarHistory["type"] == 1): ?>
                              <span class="text-success" data-toggle="tooltip" data-placement="top" title="Satıldı">+<i class="fa fa-coins"></i></span>
                            <?php else: ?>
                              <i class="fa fa-check"></i>
                            <?php endif; ?>
                          </td>
                          <td>
                            <?php echo convertTime($readBazaarHistory["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/pazar/pazar-gecmisi/sil/<?php echo $readBazaarHistory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
                    <a class="page-link" href="/yonetim-paneli/pazar/pazar-gecmisi/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/yonetim-paneli/pazar/pazar-gecmisi/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/yonetim-paneli/pazar/pazar-gecmisi/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
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
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteBazaarHistory = $db->prepare("DELETE FROM BazaarHistory WHERE id = ?");
      $deleteBazaarHistory->execute(array(get("id")));
      go("/yonetim-paneli/pazar/pazar-gecmisi");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
