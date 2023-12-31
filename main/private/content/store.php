<?php
  $servers = $db->query("SELECT * FROM Servers");

  if (get("action") == "getAll") {
    if ($servers->rowCount() == 1) {
      go("/magaza/".$servers->fetch()["slug"]);
    }
  }
  if (get("action") == "get") {
    if (get("server")) {
      $thisServer = $db->prepare("SELECT * FROM Servers WHERE slug = ?");
      $thisServer->execute(array(get("server")));
      $readThisServer = $thisServer->fetch();
      if ($thisServer->rowCount() > 0) {
        $categoryID = "0";
        if (get("category")) {
          $thisCategory = $db->prepare("SELECT * FROM ProductCategories WHERE serverID = ? AND slug = ?");
          $thisCategory->execute(array($readThisServer["id"], get("category")));
          $readThisCategory = $thisCategory->fetch();
          if ($thisCategory->rowCount() > 0) {
            $categoryID = $readThisCategory["id"];
          }
        }
        else {
          $_GET["category"] = "0";
          $categoryID = get("category");
        }
        $productCategories = $db->prepare("SELECT * FROM ProductCategories WHERE serverID = ? AND parentID = ?");
        $productCategories->execute(array($readThisServer["id"], $categoryID));
      }
    }

    $discountProducts = explode(",", $readSettings["storeDiscountProducts"]);
    require_once(__ROOT__.'/apps/main/private/packages/class/extraresources/extraresources.php');
    $extraResourcesJS = new ExtraResources('js');
    $extraResourcesJS->addResource('/apps/main/public/assets/js/store.js');
  }
?>
<section class="section store-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Ana Sayfa</a></li>
            <?php if (get("server")): ?>
              <?php if ($thisServer->rowCount() > 0): ?>
                <?php if (get("category") == "0"): ?>
                  <li class="breadcrumb-item"><a href="/magaza">Mağaza</a></li>
                  <li class="breadcrumb-item active" aria-current="page"><?php echo $readThisServer["name"]; ?></li>
                <?php else: ?>
                  <li class="breadcrumb-item"><a href="/magaza">Mağaza</a></li>
                  <li class="breadcrumb-item"><a href="/magaza/<?php echo $readThisServer["slug"]; ?>"><?php echo $readThisServer["name"]; ?></a></li>
                  <li class="breadcrumb-item active" aria-current="page"><?php echo $readThisCategory["name"]; ?></li>
                <?php endif; ?>
              <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page">Mağaza</li>
              <?php endif; ?>
            <?php else: ?>
              <li class="breadcrumb-item active" aria-current="page">Mağaza</li>
            <?php endif; ?>
          </ol>
        </nav>
      </div>
    </div>
    <?php if (get("action") == "getAll"): ?>
      <div class="row">
        <?php if ($servers->rowCount() > 0): ?>
          <?php foreach ($servers as $readServers): ?>
            <div class="col-md-3">
              <div class="img-card-wrapper">
                <div class="img-container">
                  <a class="img-card" href="/magaza/<?php echo $readServers["slug"]; ?>">
                    <img class="card-img-top lazyload" data-src="/apps/main/public/assets/img/servers/<?php echo $readServers["imageID"].'.'.$readServers["imageType"]; ?>" src="/apps/main/public/assets/img/loaders/server.png" alt="<?php echo $serverName." Sunucu - ".$readServers["name"]; ?>">
                  </a>
                  <div class="img-card-bottom">
                    <h5 class="mb-0">
                      <a class="text-white" href="/magaza/<?php echo $readServers["slug"]; ?>">
                        <?php echo $readServers["name"]; ?>
                      </a>
                    </h5>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-md-12">
            <?php echo alertError("Siteye henüz sunucu verisi eklenmemiş!"); ?>
          </div>
        <?php endif; ?>
      </div>
    <?php elseif (get("action") == "get" && get("server")): ?>
      <?php
        $serverID = ($thisServer->rowCount() > 0) ? $readThisServer["id"] : "0";
        $vipTables = $db->prepare("SELECT * FROM VIPTables WHERE serverID = ? AND categoryID = ?");
        $vipTables->execute(array($serverID, $categoryID));
        $readVIPTables = $vipTables->fetch();
      ?>
      <?php if ($vipTables->rowCount() > 0): ?>
        <?php if ($thisServer->rowCount() > 0): ?>
          <?php
            $vips = $db->prepare("SELECT * FROM VIPs WHERE tableID = ?");
            $vips->execute(array($readVIPTables["id"]));
            $vipsData = $vips->fetchAll();
            
            $vipTitles = $db->prepare("SELECT * FROM VIPTitles WHERE tableID = ?");
            $vipTitles->execute(array($readVIPTables["id"]));
          ?>
          <?php if ($vips->rowCount() > 0 && $vipTitles->rowCount() > 0): ?>
            <style type="text/css">
              .table-header {
                font-weight: bold;
                background-color: #edf4ff;
              }
            </style>
            <div id="modalBox"></div>
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="table-responsive vip-table mb-2">
                    <table class="table table-hover mb-0">
                      <thead>
                      <tr>
                        <th scope="col"></th>
                        <?php foreach ($vipsData as $readVips): ?>
                          <?php
                          $product = $db->prepare("SELECT * FROM Products WHERE id = ?");
                          $product->execute(array($readVips["vipID"]));
                          ?>
                          <?php if ($product->rowCount() > 0): ?>
                            <?php $readProduct = $product->fetch(); ?>
                            <?php $discountedPriceStatus = ($readProduct["discountedPrice"] != 0 && ($readProduct["discountExpiryDate"] > date("Y-m-d H:i:s") || $readProduct["discountExpiryDate"] == '1000-01-01 00:00:00')); ?>
                            <?php $storeDiscountStatus = ($readSettings["storeDiscount"] != 0 && (in_array($readProduct["id"], $discountProducts) || $readSettings["storeDiscountProducts"] == '0') && ($readSettings["storeDiscountExpiryDate"] > date("Y-m-d H:i:s") || $readSettings["storeDiscountExpiryDate"] == '1000-01-01 00:00:00')); ?>
                            <th class="py-4 text-center">
                              <div class="position-relative d-inline-flex">
                                <div class="store-card">
                                  <?php if ($discountedPriceStatus == true || $storeDiscountStatus == true): ?>
                                    <?php $discountPercent = (($storeDiscountStatus == true) ? $readSettings["storeDiscount"] : round((($readProduct["price"]-$readProduct["discountedPrice"])*100)/($readProduct["price"]))); ?>
                                    <div class="store-card-discount" style="right: -1rem;">
                                      <span>%<?php echo $discountPercent; ?></span>
                                    </div>
                                  <?php endif; ?>
                                  <img class="store-card-img lazyload" data-src="/apps/main/public/assets/img/store/products/<?php echo $readProduct["imageID"].'.'.$readProduct["imageType"]; ?>" src="/apps/main/public/assets/img/loaders/store.png" alt="<?php echo $serverName." Ürün - ".$readProduct["name"]." Satın Al"; ?>" style="max-width: 150px">
                                  <div class="row store-card-text d-flex flex-column">
                                    <span class="d-block"><?php echo $readProduct["name"]; ?></span>
                                    <div class="d-block">
                                      <?php if ($discountedPriceStatus == true || $storeDiscountStatus == true): ?>
                                        <span class="old-price"><?php echo $readProduct["price"]; ?></span>
                                        <small>/</small>
                                        <?php $newPrice = (($storeDiscountStatus == true) ? round(($readProduct["price"]*(100-$readSettings["storeDiscount"]))/100) : $readProduct["discountedPrice"]); ?>
                                        <span class="price"><?php echo $newPrice; ?> <i class="fa fa-coins"></i></span>
                                      <?php else: ?>
                                        <span class="price"><?php echo $readProduct["price"]; ?> <i class="fa fa-coins"></i></span>
                                      <?php endif; ?>
                                    </div>
                                  </div>
                                  <div class="store-card-button">
                                    <div class="mb-2" style="visibility: <?php echo $readProduct["stock"] == -1 ? 'hidden' : 'initial'; ?>">
                                      <?php if ($readProduct["stock"] == 0): ?>
                                        <span class="text-danger small">Stokta ürün kalmadı!</span>
                                      <?php else : ?>
                                        <span class="text-success small">Stokta <?php echo $readProduct["stock"]; ?> adet ürün kaldı!</span>
                                      <?php endif; ?>
                                    </div>
                                    <?php if ($readProduct["stock"] == 0): ?>
                                      <button class="btn btn-danger stretched-link disabled">Stokta Yok!</button>
                                    <?php else: ?>
                                      <button class="btn btn-success stretched-link openBuyModal" product-id="<?php echo $readProduct["id"]; ?>">Satın Al</button>
                                    <?php endif; ?>
                                  </div>
                                </div>
                              </div>
                            </th>
                          <?php endif; ?>
                        <?php endforeach ?>
                      </tr>
                      </thead>
                      <tbody>
                      <?php foreach ($vipTitles as $readVIPTitles): ?>
                        <?php
                        $vipDesc = $db->prepare("SELECT * FROM VIPDesc WHERE titleID = ?");
                        $vipDesc->execute(array($readVIPTitles["id"]));
                        ?>
                        <?php if ($vipDesc->rowCount() == 0): ?>
                          <tr class="sep">
                            <td colspan="<?php echo $vips->rowCount() + 1; ?>" class="text-start table-header">
                              <?php echo $readVIPTitles["title"]; ?>
                            </td>
                          </tr>
                        <?php else: ?>
                          <?php
                          $vipExplain = $db->prepare("SELECT * FROM VIPExplain WHERE titleID = ?");
                          $vipExplain->execute(array($readVIPTitles["id"]));
                          ?>
                          <tr>
                            <td class="text-start">
                              <?php echo $readVIPTitles["title"]; ?>
                              <?php foreach ($vipExplain as $readVIPExplain): ?>
                                <?php if ($readVIPExplain["name"] != NULL): ?>
                                  <i class="fa fa-question-circle" data-toggle="tooltip" title="<?php echo $readVIPExplain["name"]; ?>"></i>
                                <?php endif; ?>
                              <?php endforeach; ?>
                            </td>
                            <?php foreach ($vipsData as $readVips): ?>
                              <?php
                              $vipDesc = $db->prepare("SELECT * FROM VIPDesc WHERE vipID = ? AND titleID = ?");
                              $vipDesc->execute(array($readVips["vipID"], $readVIPTitles["id"]));
                              $readVipDesc = $vipDesc->fetch();
                              if ($vipDesc->rowCount() > 0):
                                if ($readVipDesc["description"] == '-'):
                                  $description = '<i class="fa fa-times text-danger fa-lg"></i>';
                                elseif ($readVipDesc["description"] == '+'):
                                  $description = '<i class="fa fa-check text-success fa-lg"></i>';
                                else:
                                  $description = $readVipDesc["description"];
                                endif;
                              else:
                                $description = "";
                              endif;
                              ?>
                              <td class="text-center">
                                <?php echo $description; ?>
                              </td>
                            <?php endforeach; ?>
                          </tr>
                        <?php endif; ?>
                      <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      <?php else: ?>
        <div class="row">
          <div id="modalBox"></div>
          <div class="col-md-3">
            <?php if ($servers->rowCount() > 0): ?>
              <div class="card">
                <div class="card-header">
                  Sunucular
                </div>
                <div class="card-body p-0">
                  <ul class="list-group list-group-flush">
                    <?php foreach ($servers as $readServers): ?>
                      <li class="list-group-item <?php echo ($readServers["slug"] == get("server")) ? "active":null; ?>">
                        <a href="/magaza/<?php echo $readServers["slug"]; ?>">
                          <?php echo $readServers["name"]; ?>
                        </a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
            <?php else: ?>
              <?php echo alertError("Sunucu bulunamadı!"); ?>
            <?php endif; ?>
          </div>
          <div class="col-md-9">
            <?php if (get("server") && $thisServer->rowCount() > 0): ?>
              <?php if ($readSettings["topSalesStatus"] == 1): ?>
                <?php
                $topSales = $db->prepare("SELECT P.*, COUNT(*) AS productCount FROM OrderProducts OP INNER JOIN Products P ON OP.productID = P.id WHERE P.serverID = ? AND P.categoryID = ? GROUP BY P.id ORDER BY productCount DESC LIMIT 4");
                $topSales->execute(array($readThisServer["id"], $categoryID));
                ?>
                <?php if ($topSales->rowCount() > 0): ?>
                  <div class="card">
                    <div class="card-header">
                      En Çok Satılan Ürünler
                    </div>
                    <div class="card-body">
                      <div class="row store-cards">
                        <?php foreach ($topSales as $readTopSales): ?>
                          <?php $discountedPriceStatus = ($readTopSales["discountedPrice"] != 0 && ($readTopSales["discountExpiryDate"] > date("Y-m-d H:i:s") || $readTopSales["discountExpiryDate"] == '1000-01-01 00:00:00')); ?>
                          <?php $storeDiscountStatus = ($readSettings["storeDiscount"] != 0 && (in_array($readTopSales["id"], $discountProducts) || $readSettings["storeDiscountProducts"] == '0') && ($readSettings["storeDiscountExpiryDate"] > date("Y-m-d H:i:s") || $readSettings["storeDiscountExpiryDate"] == '1000-01-01 00:00:00')); ?>
                          <div class="col-md-3">
                            <div class="store-card">
                              <?php if ($readTopSales["stock"] != -1): ?>
                                <div class="store-card-stock <?php echo ($readTopSales["stock"] == 0) ? "stock-out" : "have-stock"; ?>">
                                  <?php if ($readTopSales["stock"] == 0): ?>
                                    Stokta Yok!
                                  <?php else : ?>
                                    Sınırlı Stok!
                                  <?php endif; ?>
                                </div>
                              <?php endif; ?>
                              <?php if ($discountedPriceStatus == true || $storeDiscountStatus == true): ?>
                                <?php $discountPercent = (($storeDiscountStatus == true) ? $readSettings["storeDiscount"] : round((($readTopSales["price"]-$readTopSales["discountedPrice"])*100)/($readTopSales["price"]))); ?>
                                <div class="store-card-discount">
                                  <span>%<?php echo $discountPercent; ?></span>
                                </div>
                              <?php endif; ?>
                              <img class="store-card-img lazyload" data-src="/apps/main/public/assets/img/store/products/<?php echo $readTopSales["imageID"].'.'.$readTopSales["imageType"]; ?>" src="/apps/main/public/assets/img/loaders/store.png" alt="<?php echo $serverName." Ürün - ".$readTopSales["name"]." Satın Al"; ?>">
                              <div class="row store-card-text">
                                <div class="col">
                                  <span><?php echo $readTopSales["name"]; ?></span>
                                </div>
                                <div class="col-auto">
                                  <?php if ($discountedPriceStatus == true || $storeDiscountStatus == true): ?>
                                    <span class="old-price"><?php echo $readTopSales["price"]; ?></span>
                                    <small>/</small>
                                    <?php $newPrice = (($storeDiscountStatus == true) ? round(($readTopSales["price"]*(100-$readSettings["storeDiscount"]))/100) : $readTopSales["discountedPrice"]); ?>
                                    <span class="price"><?php echo $newPrice; ?> <i class="fa fa-coins"></i></span>
                                  <?php else: ?>
                                    <span class="price"><?php echo $readTopSales["price"]; ?> <i class="fa fa-coins"></i></span>
                                  <?php endif; ?>
                                </div>
                              </div>
                              <div class="store-card-button">
                                <?php if ($readTopSales["stock"] != -1): ?>
                                  <div class="mb-2">
                                    <?php if ($readTopSales["stock"] == 0): ?>
                                      <span class="text-danger small">Stokta ürün kalmadı!</span>
                                    <?php else : ?>
                                      <span class="text-success small">Stokta <?php echo $readTopSales["stock"]; ?> adet ürün kaldı!</span>
                                    <?php endif; ?>
                                  </div>
                                <?php endif; ?>
                                <?php if ($readTopSales["stock"] == 0): ?>
                                  <button class="btn btn-danger w-100 stretched-link disabled">Stokta Yok!</button>
                                <?php else: ?>
                                  <button class="btn btn-success w-100 stretched-link openBuyModal" product-id="<?php echo $readTopSales["id"]; ?>">Satın Al</button>
                                <?php endif; ?>
                              </div>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
              <?php endif; ?>
          
              <?php if ($productCategories->rowCount() > 0): ?>
                <div class="card">
                  <div class="card-header">
                    Kategoriler
                  </div>
                  <div class="card-body">
                    <div class="row store-cards">
                      <?php foreach ($productCategories as $readProductCategories): ?>
                        <div class="col-md-3">
                          <div class="store-card">
                            <img class="store-card-img lazyload" data-src="/apps/main/public/assets/img/store/categories/<?php echo $readProductCategories["imageID"].'.'.$readProductCategories["imageType"]; ?>" src="/apps/main/public/assets/img/loaders/store.png" alt="<?php echo $serverName." Kategori - ".$readProductCategories["name"]." Ürünlerini Görüntüle"; ?>">
                            <div class="store-card-text d-flex justify-content-center">
                              <span><?php echo $readProductCategories["name"]; ?></span>
                            </div>
                            <a class="btn btn-primary w-100 stretched-link store-card-button" href="/magaza/<?php echo $readThisServer["slug"]; ?>/<?php echo $readProductCategories["slug"]; ?>">Ürünleri Görüntüle</a>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
          
              <?php
              $products = $db->prepare("SELECT * FROM Products WHERE serverID = ? AND categoryID = ?");
              $products->execute(array($readThisServer["id"], $categoryID));
              ?>
              <?php if ($products->rowCount() > 0): ?>
                <div class="card">
                  <div class="card-header">
                    Ürünler
                  </div>
                  <div class="card-body">
                    <div class="row store-cards">
                      <?php foreach ($products as $readProducts): ?>
                        <?php $discountedPriceStatus = ($readProducts["discountedPrice"] != 0 && ($readProducts["discountExpiryDate"] > date("Y-m-d H:i:s") || $readProducts["discountExpiryDate"] == '1000-01-01 00:00:00')); ?>
                        <?php $storeDiscountStatus = ($readSettings["storeDiscount"] != 0 && (in_array($readProducts["id"], $discountProducts) || $readSettings["storeDiscountProducts"] == '0') && ($readSettings["storeDiscountExpiryDate"] > date("Y-m-d H:i:s") || $readSettings["storeDiscountExpiryDate"] == '1000-01-01 00:00:00')); ?>
                        <div class="col-md-3">
                          <div class="store-card">
                            <?php if ($readProducts["stock"] != -1): ?>
                              <div class="store-card-stock <?php echo ($readProducts["stock"] == 0) ? "stock-out" : "have-stock"; ?>">
                                <?php if ($readProducts["stock"] == 0): ?>
                                  Stokta Yok!
                                <?php else : ?>
                                  Sınırlı Stok!
                                <?php endif; ?>
                              </div>
                            <?php endif; ?>
                            <?php if ($discountedPriceStatus == true || $storeDiscountStatus == true): ?>
                              <?php $discountPercent = (($storeDiscountStatus == true) ? $readSettings["storeDiscount"] : round((($readProducts["price"]-$readProducts["discountedPrice"])*100)/($readProducts["price"]))); ?>
                              <div class="store-card-discount">
                                <span>%<?php echo $discountPercent; ?></span>
                              </div>
                            <?php endif; ?>
                            <img class="store-card-img lazyload" data-src="/apps/main/public/assets/img/store/products/<?php echo $readProducts["imageID"].'.'.$readProducts["imageType"]; ?>" src="/apps/main/public/assets/img/loaders/store.png" alt="<?php echo $serverName." Ürün - ".$readProducts["name"]." Satın Al"; ?>">
                            <div class="row store-card-text">
                              <div class="col">
                                <span><?php echo $readProducts["name"]; ?></span>
                              </div>
                              <div class="col-auto">
                                <?php if ($discountedPriceStatus == true || $storeDiscountStatus == true): ?>
                                  <span class="old-price"><?php echo $readProducts["price"]; ?></span>
                                  <small>/</small>
                                  <?php $newPrice = (($storeDiscountStatus == true) ? round(($readProducts["price"]*(100-$readSettings["storeDiscount"]))/100) : $readProducts["discountedPrice"]); ?>
                                  <span class="price"><?php echo $newPrice; ?> <i class="fa fa-coins"></i></span>
                                <?php else: ?>
                                  <span class="price"><?php echo $readProducts["price"]; ?> <i class="fa fa-coins"></i></span>
                                <?php endif; ?>
                              </div>
                            </div>
                            <div class="store-card-button">
                              <?php if ($readProducts["stock"] != -1): ?>
                                <div class="mb-2">
                                  <?php if ($readProducts["stock"] == 0): ?>
                                    <span class="text-danger small">Stokta ürün kalmadı!</span>
                                  <?php else : ?>
                                    <span class="text-success small">Stokta <?php echo $readProducts["stock"]; ?> adet ürün kaldı!</span>
                                  <?php endif; ?>
                                </div>
                              <?php endif; ?>
                              <?php if ($readProducts["stock"] == 0): ?>
                                <button class="btn btn-danger w-100 stretched-link disabled">Stokta Yok!</button>
                              <?php else: ?>
                                <button class="btn btn-success w-100 stretched-link openBuyModal" product-id="<?php echo $readProducts["id"]; ?>">Satın Al</button>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
              <?php else: ?>
                <?php if ($productCategories->rowCount() == 0): ?>
                  <?php echo alertError("Bu sayfaya ait ürün bulunamadı!"); ?>
                <?php endif; ?>
              <?php endif; ?>
            <?php else: ?>
              <?php echo alertError("Bu sayfaya ait veri bulunamadı!"); ?>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    <?php else: ?>
      <?php go("/404"); ?>
    <?php endif; ?>
  </div>
</section>
