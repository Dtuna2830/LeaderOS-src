<?php
  if (!checkPerm($readAdmin, 'MANAGE_STORE')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  if (get("target") == 'product' && (get("action") == 'insert' || get("action") == 'update')) {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/store.product.js');
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/store.minecraft-items.js');
  }
  if (get("target") == 'category' && (get("action") == 'insert' || get("action") == 'update')) {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/store.minecraft-items.js');
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/store.category.js');
  }
  if (get("target") == 'credit-package' && (get("action") == 'insert' || get("action") == 'update')) {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/store.product.js');
  }
  if (get("target") == 'coupon' && (get("action") == 'insert' || get("action") == 'update')) {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/store.coupon.js');
  }
  if (get("target") == 'chest' && get("action") == 'send') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/gift.products.js');
  }
  if (get("target") == 'discount' && get("action") == 'update') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/store.discount.js');
  }
  if (get("target") == 'credit' && get("action") == 'send') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/payment.js');
  }
?>
<?php if (get("target") == 'product'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Ürünler</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/magaza/urun">Mağaza</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Ürün</li>
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
            $products = $db->prepare("SELECT P.*, PC.name as categoryName, S.name as serverName FROM Products P LEFT JOIN ProductCategories PC ON P.categoryID = PC.id OR P.categoryID = ? INNER JOIN Servers S ON P.serverID = S.id GROUP BY P.id ORDER BY P.id DESC");
            $products->execute(array(0));
          ?>
          <?php if ($products->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["productID", "productName", "productCategoryName", "productServerName", "productPrice", "productStock"]'>
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
                    <a class="btn btn-sm btn-white" href="/yonetim-paneli/magaza/urun/ekle">Ürün Ekle</a>
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
                          <a href="#" class="text-muted sort" data-sort="productID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productName">
                            Ürün Adı
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productCategoryName">
                            Kategori Adı
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productServerName">
                            Sunucu Adı
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productPrice">
                            Fiyat
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productStock">
                            Stok
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($products as $readProducts): ?>
                        <tr>
                          <td class="productID text-center" style="width: 40px;">
                            <a href="/yonetim-paneli/magaza/urun/duzenle/<?php echo $readProducts["id"]; ?>">
                              #<?php echo $readProducts["id"]; ?>
                            </a>
                          </td>
                          <td class="productName">
                            <a href="/yonetim-paneli/magaza/urun/duzenle/<?php echo $readProducts["id"]; ?>">
                              <?php echo $readProducts["name"]; ?>
                            </a>
                          </td>
                          <td class="productCategoryName">
                            <?php echo (($readProducts["categoryID"] == 0) ? 'Kategorisiz' : $readProducts["categoryName"]); ?>
                          </td>
                          <td class="productServerName">
                            <?php echo $readProducts["serverName"]; ?>
                          </td>
                          <td class="productPrice">
                            <?php echo ($readProducts["discountedPrice"] > 0) ? $readProducts["discountedPrice"] : $readProducts["price"]; ?> <?php echo $readSettings["creditText"] ?>
                          </td>
                          <td class="productStock">
                            <?php echo ($readProducts["stock"] == -1) ? "Sınırsız" : $readProducts["stock"]; ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/magaza/urun/duzenle/<?php echo $readProducts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/magaza/urun/sil/<?php echo $readProducts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
                  <h2 class="header-title">Ürün Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Mağaza</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Ürün</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Ürün Ekle</li>
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
            if (isset($_POST["insertProducts"])) {
              if (post("stockStatus") == 0) {
                $_POST["stock"] = -1;
              }
              if (post("durationStatus") == 0) {
                $_POST["duration"] = 0;
              }
              if (post("durationStatus") == -1) {
                $_POST["duration"] = -1;
              }
              if (post("discountStatus") == 0) {
                $_POST["discountedPrice"] = 0;
              }
              if (post("discountDurationStatus") == 0 || post("discountStatus") == 0) {
                $_POST["discountDuration"] = '1000-01-01 00:00:00';
              }
              else {
                $_POST["discountDuration"] = date("Y-m-d H:i:s", strtotime($_POST["discountDuration"]));
              }
              if (!$csrf->validate('insertProducts')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("name") == null || post("categoryID") == null || post("serverID") == null || post("details") == null || !count(array_filter($_POST["commands"])) || post("price") == null || post("discountStatus") == null || post("discountedPrice") == null || post("discountDurationStatus") == null || post("discountDuration") == null || post("durationStatus") == null || post("duration") == null || post("stockStatus") == null || post("stock") == null || post("minecraftStatus") == null || post("giveRoleID") == null) {
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
                  $upload->image_resize = true;
                  $upload->image_ratio_crop = true;
                  $upload->image_x = 360;
                  $upload->image_y = 360;
                  $upload->process(__ROOT__."/apps/main/public/assets/img/store/products/");
                  if ($upload->processed) {
                    if (post("minecraftItem") == "default") {
                      $minecraftItem = null;
                    }
                    else {
                      $minecraftItem = post("minecraftItem");
                    }
                    $insertProducts = $db->prepare("INSERT INTO Products (name, imageID, imageType, categoryID, serverID, details, price, discountedPrice, discountExpiryDate, duration, stock, giveRoleID, minecraftStatus, minecraftTitle, minecraftDescription, minecraftItem, creationDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insertProducts->execute(array(post("name"), $imageID, $upload->file_dst_name_ext, post("categoryID"), post("serverID"), filteredContent($_POST["details"]), post("price"), post("discountedPrice"), post("discountDuration"), post("duration"), post("stock"), post("giveRoleID"), post("minecraftStatus"), post("minecraftTitle"), post("minecraftDescription"), $minecraftItem, date("Y-m-d H:i:s")));
                    $productsLastInsertID = $db->lastInsertId();
                    if (count(array_filter($_POST["commands"]))) {
                      $insertProductCommands = $db->prepare("INSERT INTO ProductCommands (productID, command) VALUES (?, ?)");
                      foreach ($_POST["commands"] as $command) {
                        $command = ltrim(strip_tags($command), '/');
                        $insertProductCommands->execute(array($productsLastInsertID, $command));
                      }
                    }
                    echo alertSuccess("Ürün başarıyla eklendi!");
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
                  <label for="inputname" class="col-sm-2 col-form-label">Ürün Adı:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputname" class="form-control" name="name" placeholder="Ürün adını yazınız.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectServerID" class="col-sm-2 col-form-label">Sunucu:</label>
                  <div class="col-sm-10">
                    <?php $servers = $db->query("SELECT * FROM Servers"); ?>
                    <select id="selectServerID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="serverID" <?php echo ($servers->rowCount() == 0) ? "disabled" : null; ?>>
                      <?php if ($servers->rowCount() > 0): ?>
                        <?php foreach ($servers as $readServers): ?>
                          <option value="<?php echo $readServers["id"]; ?>"><?php echo $readServers["name"]; ?></option>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <option>Sunucu bulunamadı!</option>
                      <?php endif; ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectCategoryID" class="col-sm-2 col-form-label">Kategori:</label>
                  <div class="col-sm-10">
                    <div id="c-loading" style="display: none; margin-top: 7px">Yükleniyor...</div>
                    <div id="product-categories">
                      <?php
                        $firstServer = $db->query("SELECT * FROM Servers ORDER BY id ASC LIMIT 1");
                        $readFirstServer = $firstServer->fetch();

                        $productCategories = $db->prepare("SELECT * FROM ProductCategories WHERE serverID = ?");
                        $productCategories->execute(array($readFirstServer["id"]));
                      ?>
                      <select id="selectCategoryID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="categoryID">
                        <option value="0">Kategorisiz</option>
                        <?php if ($productCategories->rowCount() > 0): ?>
                          <?php foreach ($productCategories as $readProductCategories): ?>
                            <option value="<?php echo $readProductCategories["id"]; ?>"><?php echo $readProductCategories["name"]; ?></option>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputPrice" class="col-sm-2 col-form-label">Fiyat:</label>
                  <div class="col-sm-10">
                    <div class="input-group input-group-merge">
                      <input type="number" id="inputPrice" class="form-control form-control-prepended" name="price" placeholder="Ürün fiyatını yazınız.">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <span class="fa fa-coins"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectDiscountStatus" class="col-sm-2 col-form-label">İndirim:</label>
                  <div class="col-sm-10">
                    <select id="selectDiscountStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="discountStatus">
                      <option value="0">Yok</option>
                      <option value="1">Var</option>
                    </select>
                  </div>
                </div>
                <div id="discountBlock" style="display: none;">
                  <div class="form-group row">
                    <label for="inputDiscountedPrice" class="col-sm-2 col-form-label">İndirimli Fiyat:</label>
                    <div class="col-sm-10">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputDiscountedPrice" class="form-control form-control-prepended" name="discountedPrice" placeholder="Ürünün indirimli fiyatını yazınız.">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fa fa-coins"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectDiscountDurationStatus" class="col-sm-2 col-form-label">İndirim Süresi:</label>
                    <div class="col-sm-10">
                      <select id="selectDiscountDurationStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="discountDurationStatus">
                        <option value="0">Süresiz</option>
                        <option value="1">Süreli</option>
                      </select>
                    </div>
                  </div>
                  <div id="discountDurationBlock" style="display: none;">
                    <div class="form-group row">
                      <div class="col-sm-10 offset-sm-2">
                        <div class="input-group input-group-merge">
                          <input type="text" id="inputDiscountDuration" class="form-control form-control-prepended" name="discountDuration" placeholder="Ürüne uygulanacak indirimin son kullanım tarihini seçiniz." data-toggle="flatpickr" data-expirydate="true">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <span class="fe fe-clock"></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectDurationStatus" class="col-sm-2 col-form-label">Ürün Süresi:</label>
                  <div class="col-sm-10">
                    <select id="selectDurationStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="durationStatus">
                      <option value="0">Süresiz</option>
                      <option value="1">Süreli</option>
                      <option value="-1">Tek Kullanımlık</option>
                    </select>
                  </div>
                </div>
                <div id="durationBlock" style="display: none;">
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputDuration" class="form-control form-control-prepended" name="duration" placeholder="Ürünün kullanım süresi (Gün).">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-clock"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectStockStatus" class="col-sm-2 col-form-label">Stok:</label>
                  <div class="col-sm-10">
                    <select id="selectStockStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="stockStatus">
                      <option value="0">Sınırsız</option>
                      <option value="1">Sınırlı</option>
                    </select>
                  </div>
                </div>
                <div id="stockBlock" style="display: none;">
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputStock" class="form-control form-control-prepended" name="stock" placeholder="Ürünün stok adedini yazınız.">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-package"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectRole" class="col-sm-2 col-form-label">Satın Alanlara Site Rolü Ver:</label>
                  <div class="col-sm-10">
                    <select id="selectRole" class="form-control" data-toggle="select" name="giveRoleID">
                      <option value="0">Yok</option>
                      <?php $roles = $db->query("SELECT * FROM Roles ORDER BY priority DESC"); ?>
                      <?php foreach ($roles as $readRoles): ?>
                        <?php if ($readRoles["id"] == 1) continue; ?>
                        <option value="<?php echo $readRoles["id"] ?>"><?php echo $readRoles["name"] ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectMinecraftStatus" class="col-sm-2 col-form-label">Oyunda Göster:</label>
                  <div class="col-sm-10">
                    <select id="selectMinecraftStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="minecraftStatus">
                      <option value="0">Hayır</option>
                      <option value="1">Evet</option>
                    </select>
                  </div>
                </div>
                <div id="minecraftBlock" style="display: none;">
                  <div class="form-group row">
                    <label for="input-minecrafttitle" class="col-sm-2 col-form-label">Minecraft GUI Başlığı (Title):</label>
                    <div class="col-sm-10">
                      <input type="text" id="input-minecrafttitle" class="form-control" name="minecraftTitle" placeholder="Boş bırakırsanız ürün adı gözükecektir. Renk Kodları (&) kullanabilirsiniz..">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="input-minecraftdesc" class="col-sm-2 col-form-label">Minecraft GUI Açıklama (Lore):</label>
                    <div class="col-sm-10">
                      <textarea id="input-minecraftdesc" class="form-control" name="minecraftDescription" placeholder="Boş bırakabilirsiniz. Renk Kodları (&) kullanabilirsiniz."></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectMinecraftItem" class="col-sm-2 col-form-label">Minecraft Icon:</label>
                    <div class="col-sm-10">
                      <div id="mi-loading" style="margin-top: 7px">Yükleniyor</div>
                      <div id="product-minecraftitems" style="display: none;" data-type="insert">
                        <select id="selectMinecraftItem" class="form-control" name="minecraftItem">
                          <option value="default">Varsayılan</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaDetails" class="col-sm-2 col-form-label">Detaylar:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaDetails" class="form-control" data-toggle="textEditor" name="details" placeholder="Ürünün detaylarını/özelliklerini yazınız."></textarea>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputTable" class="col-sm-2 col-form-label">Komutlar:</label>
                  <div class="col-sm-10">
                    <div class="table-responsive">
                      <table id="tableitems" class="table table-sm table-hover table-nowrap array-table">
                        <thead>
                          <tr>
                            <th>Komut</th>
                            <th class="text-center pt-0 pb-0 align-middle">
                              <button type="button" class="btn btn-sm btn-rounded-circle btn-success addTableItem">
                                <i class="fe fe-plus"></i>
                              </button>
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>
                              <div class="input-group input-group-merge">
                                <input type="text" class="form-control form-control-prepended" name="commands[]" placeholder="Ürün satın alındığında konsole gönderilecek komudu yazınız.">
                                <div class="input-group-prepend">
                                  <div class="input-group-text">
                                    <span class="fe fe-terminal"></span>
                                  </div>
                                </div>
                              </div>
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
                    <small class="form-text text-muted pb-2"><strong>Kullanıcı Adı:</strong> %username%</small>
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
                <?php echo $csrf->input('insertProducts'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertProducts">Ekle</button>
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
      $product = $db->prepare("SELECT * FROM Products WHERE id = ?");
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
                  <h2 class="header-title">Ürün Düzenle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Mağaza</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Ürün</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Ürün Düzenle</a></li>
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
                if (post("stockStatus") == 0) {
                  $_POST["stock"] = -1;
                }
                if (post("durationStatus") == 0) {
                  $_POST["duration"] = 0;
                }
                if (post("durationStatus") == -1) {
                  $_POST["duration"] = -1;
                }
                if (post("discountStatus") == 0) {
                  $_POST["discountedPrice"] = 0;
                }
                if (post("discountDurationStatus") == 0 || post("discountStatus") == 0) {
                  $_POST["discountDuration"] = '1000-01-01 00:00:00';
                }
                else {
                  $_POST["discountDuration"] = date("Y-m-d H:i:s", strtotime($_POST["discountDuration"]));
                }
                if (!$csrf->validate('updateProducts')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("name") == null || post("categoryID") == null || post("serverID") == null || post("details") == null || !count(array_filter($_POST["commands"])) || post("price") == null || post("discountStatus") == null || post("discountedPrice") == null || post("discountDurationStatus") == null || post("discountDuration") == null || post("durationStatus") == null || post("duration") == null || post("stockStatus") == null || post("stock") == null || post("minecraftStatus") == null || post("giveRoleID") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  if ($_FILES["image"]["size"] != null) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                    $upload = new \Verot\Upload\Upload($_FILES["image"], "tr_TR");
                    $imageID = $readProduct["imageID"];
                    if ($upload->uploaded) {
                      $upload->allowed = array("image/*");
                      $upload->file_overwrite = true;
                      $upload->file_new_name_body = $imageID;
                      $upload->image_resize = true;
                      $upload->image_ratio_crop = true;
                      $upload->image_x = 360;
                      $upload->image_y = 360;
                      $upload->process(__ROOT__."/apps/main/public/assets/img/store/products/");
                      if ($upload->processed) {
                        $updateProducts = $db->prepare("UPDATE Products SET imageType = ? WHERE id = ?");
                        $updateProducts->execute(array($upload->file_dst_name_ext, get("id")));
                      }
                      else {
                        echo alertError("Resim yüklenirken bir hata oluştu: ".$upload->error);
                      }
                    }
                  }
                  if (post("minecraftItem") == "default") {
                    $minecraftItem = null;
                  }
                  else {
                    $minecraftItem = post("minecraftItem");
                  }
                  $updateProducts = $db->prepare("UPDATE Products SET name = ?, categoryID = ?, serverID = ?, details = ?, price = ?, discountedPrice = ?, discountExpiryDate = ?, duration = ?, stock = ?, giveRoleID = ?, minecraftStatus = ?, minecraftTitle = ?, minecraftDescription = ?, minecraftItem = ? WHERE id = ?");
                  $updateProducts->execute(array(post("name"), post("categoryID"), post("serverID"), filteredContent($_POST["details"]), post("price"), post("discountedPrice"),  post("discountDuration"), post("duration"), post("stock"), post("giveRoleID"), post("minecraftStatus"), post("minecraftTitle"), post("minecraftDescription"), $minecraftItem, get("id")));
                  if (count(array_filter($_POST["commands"]))) {
                    $deleteProductCommands = $db->prepare("DELETE FROM ProductCommands WHERE productID = ?");
                    $deleteProductCommands->execute(array($readProduct["id"]));
                    $insertProductCommands = $db->prepare("INSERT INTO ProductCommands (productID, command) VALUES (?, ?)");
                    foreach ($_POST["commands"] as $command) {
                      $command = ltrim(strip_tags($command), '/');
                      $insertProductCommands->execute(array($readProduct["id"], $command));
                    }
                  }
                  echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label for="inputname" class="col-sm-2 col-form-label">Ürün Adı:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputname" class="form-control" name="name" placeholder="Ürün adını yazınız." value="<?php echo $readProduct["name"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectServerID" class="col-sm-2 col-form-label">Sunucu:</label>
                    <div class="col-sm-10">
                      <?php $servers = $db->query("SELECT * FROM Servers"); ?>
                      <select id="selectServerID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="serverID" <?php echo ($servers->rowCount() == 0) ? "disabled" : null; ?>>
                        <?php if ($servers->rowCount() > 0): ?>
                          <?php foreach ($servers as $readServers): ?>
                            <option value="<?php echo $readServers["id"]; ?>" <?php echo (($readProduct["serverID"] == $readServers["id"]) ? 'selected="selected"' : null); ?>><?php echo $readServers["name"]; ?></option>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <option>Sunucu bulunamadı!</option>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectCategoryID" class="col-sm-2 col-form-label">Kategori:</label>
                    <div class="col-sm-10">
                      <div id="c-loading" style="display: none; margin-top: 7px">Yükleniyor...</div>
                      <div id="product-categories">
                        <?php
                          $productCategories = $db->prepare("SELECT * FROM ProductCategories WHERE serverID = ?");
                          $productCategories->execute(array($readProduct["serverID"]));
                        ?>
                        <select id="selectCategoryID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="categoryID">
                          <option value="0">Kategorisiz</option>
                          <?php if ($productCategories->rowCount() > 0): ?>
                            <?php foreach ($productCategories as $readProductCategories): ?>
                              <option value="<?php echo $readProductCategories["id"]; ?>" <?php echo (($readProduct["categoryID"] == $readProductCategories["id"]) ? 'selected="selected"' : null); ?>><?php echo $readProductCategories["name"]; ?></option>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPrice" class="col-sm-2 col-form-label">Fiyat:</label>
                    <div class="col-sm-10">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputPrice" class="form-control form-control-prepended" name="price" placeholder="Ürün fiyatını yazınız." value="<?php echo $readProduct["price"]; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fa fa-coins"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectDiscountStatus" class="col-sm-2 col-form-label">İndirim:</label>
                    <div class="col-sm-10">
                      <select id="selectDiscountStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="discountStatus">
                        <option value="0" <?php echo ($readProduct["discountedPrice"] == 0) ? 'selected="selected"' : null; ?>>Yok</option>
                        <option value="1" <?php echo ($readProduct["discountedPrice"] != 0) ? 'selected="selected"' : null; ?>>Var</option>
                      </select>
                    </div>
                  </div>
                  <div id="discountBlock" style="<?php echo ($readProduct["discountedPrice"] == 0) ? "display: none;" : "display: block;"; ?>">
                    <div class="form-group row">
                      <label for="inputDiscountedPrice" class="col-sm-2 col-form-label">İndirimli Fiyat:</label>
                      <div class="col-sm-10">
                        <div class="input-group input-group-merge">
                          <input type="number" id="inputDiscountedPrice" class="form-control form-control-prepended" name="discountedPrice" placeholder="Ürünün indirimli fiyatını yazınız." value="<?php echo ($readProduct["discountedPrice"] != 0) ? $readProduct["discountedPrice"] : null; ?>">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <span class="fa fa-coins"></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectDiscountDurationStatus" class="col-sm-2 col-form-label">İndirim Süresi:</label>
                      <div class="col-sm-10">
                        <select id="selectDiscountDurationStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="discountDurationStatus">
                          <option value="0" <?php echo ($readProduct["discountExpiryDate"] == '1000-01-01 00:00:00') ? 'selected="selected"' : null; ?>>Süresiz</option>
                          <option value="1" <?php echo ($readProduct["discountExpiryDate"] != '1000-01-01 00:00:00') ? 'selected="selected"' : null; ?>>Süreli</option>
                        </select>
                      </div>
                    </div>
                    <div id="discountDurationBlock" style="<?php echo ($readProduct["discountExpiryDate"] == '1000-01-01 00:00:00') ? "display: none;" : "display: block;"; ?>">
                      <div class="form-group row">
                        <div class="col-sm-10 offset-sm-2">
                          <div class="input-group input-group-merge">
                            <input type="text" id="inputDiscountDuration" class="form-control form-control-prepended" name="discountDuration" placeholder="Ürüne uygulanacak indirimin son kullanım tarihini seçiniz." data-toggle="flatpickr" data-expirydate="true" value="<?php echo ($readProduct["discountExpiryDate"] != '1000-01-01 00:00:00') ? $readProduct["discountExpiryDate"] : null; ?>">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <span class="fe fe-clock"></span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectDurationStatus" class="col-sm-2 col-form-label">Ürün Süresi:</label>
                    <div class="col-sm-10">
                      <select id="selectDurationStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="durationStatus">
                        <option value="0" <?php echo ($readProduct["duration"] == 0) ? 'selected="selected"' : null; ?>>Süresiz</option>
                        <option value="1" <?php echo ($readProduct["duration"] != 0 && $readProduct["duration"] != -1) ? 'selected="selected"' : null; ?>>Süreli</option>
                        <option value="-1" <?php echo ($readProduct["duration"] == -1) ? 'selected="selected"' : null; ?>>Tek Kullanımlık</option>
                      </select>
                    </div>
                  </div>
                  <div id="durationBlock" style="<?php echo ($readProduct["duration"] == 0 || $readProduct["duration"] == -1) ? "display: none;" : "display: block;"; ?>">
                    <div class="form-group row">
                      <div class="col-sm-10 offset-sm-2">
                        <div class="input-group input-group-merge">
                          <input type="number" id="inputDuration" class="form-control form-control-prepended" name="duration" placeholder="Ürünün kullanım süresi (Gün)." value="<?php echo ($readProduct["duration"] != 0) ? $readProduct["duration"] : null; ?>">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <span class="fe fe-clock"></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectStockStatus" class="col-sm-2 col-form-label">Stok:</label>
                    <div class="col-sm-10">
                      <select id="selectStockStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="stockStatus">
                        <option value="0" <?php echo ($readProduct["stock"] == -1) ? 'selected="selected"' : null; ?>>Sınırsız</option>
                        <option value="1" <?php echo ($readProduct["stock"] != -1) ? 'selected="selected"' : null; ?>>Sınırlı</option>
                      </select>
                    </div>
                  </div>
                  <div id="stockBlock" style="<?php echo ($readProduct["stock"] == -1) ? "display: none;" : "display: block;"; ?>">
                    <div class="form-group row">
                      <div class="col-sm-10 offset-sm-2">
                        <div class="input-group input-group-merge">
                          <input type="number" id="inputStock" class="form-control form-control-prepended" name="stock" placeholder="Ürünün stok adedini yazınız." value="<?php echo ($readProduct["stock"] != -1) ? $readProduct["stock"] : null; ?>">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <span class="fe fe-package"></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectRole" class="col-sm-2 col-form-label">Satın Alanlara Site Rolü Ver:</label>
                    <div class="col-sm-10">
                      <select id="selectRole" class="form-control" data-toggle="select" name="giveRoleID">
                        <option value="0">Yok</option>
                        <?php $roles = $db->query("SELECT * FROM Roles ORDER BY priority DESC"); ?>
                        <?php foreach ($roles as $readRoles): ?>
                          <?php if ($readRoles["id"] == 1) continue; ?>
                          <option value="<?php echo $readRoles["id"] ?>" <?php echo ($readProduct["giveRoleID"] == $readRoles["id"]) ? 'selected="selected"' : null; ?>><?php echo $readRoles["name"] ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectMinecraftStatus" class="col-sm-2 col-form-label">Oyunda Göster:</label>
                    <div class="col-sm-10">
                      <select id="selectMinecraftStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="minecraftStatus">
                        <option value="0" <?php echo ($readProduct["minecraftStatus"] == 0) ? 'selected="selected"' : null; ?>>Hayır</option>
                        <option value="1" <?php echo ($readProduct["minecraftStatus"] == 1) ? 'selected="selected"' : null; ?>>Evet</option>
                      </select>
                    </div>
                  </div>
                  <div id="minecraftBlock" style="<?php echo ($readProduct["minecraftStatus"] == 0) ? "display: none;" : "display: block;"; ?>">
                    <div class="form-group row">
                      <label for="input-minecrafttitle" class="col-sm-2 col-form-label">Minecraft GUI Başlığı (Title):</label>
                      <div class="col-sm-10">
                        <input type="text" id="input-minecrafttitle" class="form-control" name="minecraftTitle" placeholder="Boş bırakırsanız ürün adı gözükecektir. Renk Kodları (&) kullanabilirsiniz." value="<?php echo $readProduct["minecraftTitle"]; ?>">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="input-minecraftdesc" class="col-sm-2 col-form-label">Minecraft GUI Açıklama (Lore):</label>
                      <div class="col-sm-10">
                        <textarea id="input-minecraftdesc" class="form-control" name="minecraftDescription" placeholder="Boş bırakabilirsiniz. Renk Kodları (&) kullanabilirsiniz."><?php echo $readProduct["minecraftDescription"]; ?></textarea>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectMinecraftItem" class="col-sm-2 col-form-label">Minecraft Icon:</label>
                      <div class="col-sm-10">
                        <div id="mi-loading" style="margin-top: 7px">Yükleniyor...</div>
                        <div id="product-minecraftitems" style="display: none;" data-type="update" data-selected="<?php echo $readProduct["minecraftItem"]; ?>">
                          <select id="selectMinecraftItem" class="form-control" name="minecraftItem">
                            <option value="default">Varsayılan</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="textareaDetails" class="col-sm-2 col-form-label">Detaylar:</label>
                    <div class="col-sm-10">
                      <textarea id="textareaDetails" class="form-control" data-toggle="textEditor" name="details" placeholder="Ürünün detaylarını/özelliklerini yazınız."><?php echo $readProduct["details"]; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputTable" class="col-sm-2 col-form-label">Komutlar:</label>
                    <div class="col-sm-10">
                      <div class="table-responsive">
                        <table id="tableitems" class="table table-sm table-hover table-nowrap array-table">
                          <thead>
                            <tr>
                              <th>Komut</th>
                              <th class="text-center pt-0 pb-0 align-middle">
                                <button type="button" class="btn btn-sm btn-rounded-circle btn-success addTableItem">
                                  <i class="fe fe-plus"></i>
                                </button>
                              </th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                              $productCommands = $db->prepare("SELECT * FROM ProductCommands WHERE productID = ?");
                              $productCommands->execute(array($readProduct["id"]));
                            ?>
                            <?php foreach ($productCommands as $productCommand): ?>
                              <tr>
                                <td>
                                  <div class="input-group input-group-merge">
                                    <input type="text" class="form-control form-control-prepended" name="commands[]" placeholder="Ürün satın alındığında konsole gönderilecek komudu yazınız." value="<?php echo $productCommand["command"]; ?>">
                                    <div class="input-group-prepend">
                                      <div class="input-group-text">
                                        <span class="fe fe-terminal"></span>
                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td class="text-center align-middle">
                                  <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                    <i class="fe fe-trash-2"></i>
                                  </button>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                      <small class="form-text text-muted pb-2"><strong>Kullanıcı Adı:</strong> %username%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="fileImage" class="col-sm-2 col-form-label">Resim:</label>
                    <div class="col-sm-10">
                      <div data-toggle="dropimage" class="dropimage active">
                        <div class="di-thumbnail">
                          <img src="/apps/main/public/assets/img/store/products/<?php echo $readProduct["imageID"].'.'.$readProduct["imageType"]; ?>" alt="Ön İzleme">
                        </div>
                        <div class="di-select">
                          <label for="fileImage">Bir Resim Seçiniz</label>
                          <input type="file" id="fileImage" name="image" accept="image/*">
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateProducts'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/magaza/urun/sil/<?php echo $readProduct["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateProducts">Değişiklikleri Kaydet</button>
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
      $deleteProduct = $db->prepare("DELETE FROM Products WHERE id = ?");
      $deleteProduct->execute(array(get("id")));
      go("/yonetim-paneli/magaza/urun");
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
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Mağaza</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Kategori</li>
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
          <?php $productCategories = $db->query("SELECT PC.*, S.name as serverName, S.slug as serverSlug FROM ProductCategories PC INNER JOIN Servers S ON PC.serverID = S.id ORDER BY PC.id DESC"); ?>
          <?php if ($productCategories->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["productCategoryID", "productCategoryName", "productServerName", "productParentCategoryName"]'>
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
                    <a class="btn btn-sm btn-white" href="/yonetim-paneli/magaza/kategori/ekle">Kategori Ekle</a>
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
                          <a href="#" class="text-muted sort" data-sort="productCategoryID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productCategoryName">
                            Kategori Adı
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productServerName">
                            Sunucu
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productParentCategoryName">
                            Üst Kategori
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($productCategories as $readProductCategories): ?>
                        <tr>
                          <td class="productCategoryID text-center" style="width: 40px;">
                            <a href="/yonetim-paneli/magaza/kategori/duzenle/<?php echo $readProductCategories["id"]; ?>">
                              #<?php echo $readProductCategories["id"]; ?>
                            </a>
                          </td>
                          <td class="productCategoryName">
                            <a href="/yonetim-paneli/magaza/kategori/duzenle/<?php echo $readProductCategories["id"]; ?>">
                              <?php echo $readProductCategories["name"]; ?>
                            </a>
                          </td>
                          <td class="productServerName">
                            <?php echo $readProductCategories["serverName"]; ?>
                          </td>
                          <td class="productParentCategoryName">
                            <?php
                              $parentCategory = $db->prepare("SELECT name FROM ProductCategories WHERE id = ?");
                              $parentCategory->execute(array($readProductCategories["parentID"]));
                              $readParentCategory = $parentCategory->fetch();
                            ?>
                            <?php if ($parentCategory->rowCount() > 0): ?>
                              <?php echo $readParentCategory["name"]; ?>
                            <?php else: ?>
                              -
                            <?php endif; ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/magaza/kategori/duzenle/<?php echo $readProductCategories["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/magaza/<?php echo $readProductCategories["serverSlug"]; ?>/<?php echo $readProductCategories["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/magaza/kategori/sil/<?php echo $readProductCategories["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Mağaza</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/kategori">Kategori</a></li>
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
            if (isset($_POST["insertProductCategories"])) {
              if (!$csrf->validate('insertProductCategories')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("name") == null || post("serverID") == null || post("parentID") == null || post("minecraftStatus") == null) {
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
                  $upload->image_resize = true;
                  $upload->image_ratio_crop = true;
                  $upload->image_x = 360;
                  $upload->image_y = 360;
                  $upload->process(__ROOT__."/apps/main/public/assets/img/store/categories/");
                  if ($upload->processed) {
                    if (post("minecraftItem") == "default") {
                      $minecraftItem = null;
                    }
                    else {
                      $minecraftItem = post("minecraftItem");
                    }
                    $insertProductCategories = $db->prepare("INSERT INTO ProductCategories (serverID, parentID, name, slug, imageID, imageType, minecraftStatus, minecraftTitle, minecraftDescription, minecraftItem) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insertProductCategories->execute(array(post("serverID"), post("parentID"), post("name"), convertURL(post("name")), $imageID, $upload->file_dst_name_ext, post("minecraftStatus"), post("minecraftTitle"), post("minecraftDescription"), $minecraftItem));
                    echo alertSuccess("Kategori başarıyla eklendi!");
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
                  <label for="inputName" class="col-sm-2 col-form-label">Kategori Adı:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputName" class="form-control" name="name" placeholder="Kategori adı yazınız.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectServerID" class="col-sm-2 col-form-label">Sunucu:</label>
                  <div class="col-sm-10">
                    <?php $servers = $db->query("SELECT * FROM Servers"); ?>
                    <select id="selectServerID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="serverID" <?php echo ($servers->rowCount() == 0) ? "disabled" : null; ?>>
                      <?php if ($servers->rowCount() > 0): ?>
                        <?php foreach ($servers as $readServers): ?>
                          <option value="<?php echo $readServers["id"]; ?>"><?php echo $readServers["name"]; ?></option>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <option>Sunucu bulunamadı!</option>
                      <?php endif; ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectParentID" class="col-sm-2 col-form-label">Üst Kategori:</label>
                  <div class="col-sm-10">
                    <div id="c-loading" style="display: none; margin-top: 7px">Yükleniyor...</div>
                    <div id="product-categories">
                      <?php
                        $firstServer = $db->query("SELECT * FROM Servers ORDER BY id ASC LIMIT 1");
                        $readFirstServer = $firstServer->fetch();

                        $productCategories = $db->prepare("SELECT * FROM ProductCategories WHERE serverID = ?");
                        $productCategories->execute(array($readFirstServer["id"]));
                      ?>
                      <select id="selectParentID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="parentID">
                        <option value="0">Kategorisiz</option>
                        <?php if ($productCategories->rowCount() > 0): ?>
                          <?php foreach ($productCategories as $readProductCategories): ?>
                            <option value="<?php echo $readProductCategories["id"]; ?>"><?php echo $readProductCategories["name"]; ?></option>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectMinecraftStatus" class="col-sm-2 col-form-label">Oyunda Göster:</label>
                  <div class="col-sm-10">
                    <select id="selectMinecraftStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="minecraftStatus">
                      <option value="0">Hayır</option>
                      <option value="1">Evet</option>
                    </select>
                  </div>
                </div>
                <div id="minecraftBlock" style="display: none;">
                  <div class="form-group row">
                    <label for="input-minecrafttitle" class="col-sm-2 col-form-label">Minecraft GUI Başlığı (Title):</label>
                    <div class="col-sm-10">
                      <input type="text" id="input-minecrafttitle" class="form-control" name="minecraftTitle" placeholder="Boş bırakırsanız ürün adı gözükecektir. Renk Kodları (&) kullanabilirsiniz.">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="input-minecraftdesc" class="col-sm-2 col-form-label">Minecraft GUI Açıklama (Lore):</label>
                    <div class="col-sm-10">
                      <textarea id="input-minecraftdesc" class="form-control" name="minecraftDescription" placeholder="Boş bırakabilirsiniz. Renk Kodları (&) kullanabilirsiniz."></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectMinecraftItem" class="col-sm-2 col-form-label">Minecraft Icon:</label>
                    <div class="col-sm-10">
                      <div id="mi-loading" style="margin-top: 7px">Yükleniyor</div>
                      <div id="product-minecraftitems" style="display: none;" data-type="insert">
                        <select id="selectMinecraftItem" class="form-control" name="minecraftItem">
                          <option value="default">Varsayılan</option>
                        </select>
                      </div>
                    </div>
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
                <?php echo $csrf->input('insertProductCategories'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertProductCategories">Ekle</button>
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
      $productCategory = $db->prepare("SELECT PC.*, S.slug as serverSlug FROM ProductCategories PC INNER JOIN Servers S ON PC.serverID = S.id WHERE PC.id = ?");
      $productCategory->execute(array(get("id")));
      $readProductCategory = $productCategory->fetch();
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
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Mağaza</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/kategori">Kategori</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/kategori">Kategori Düzenle</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($productCategory->rowCount() > 0) ? $readProductCategory["name"] : "Bulunamadı!"; ?></li>
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
          <?php if ($productCategory->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateProductCategories"])) {
                if (!$csrf->validate('updateProductCategories')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("name") == null || post("serverID") == null || post("parentID") == null || post("minecraftStatus") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  if ($_FILES["image"]["size"] != null) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                    $upload = new \Verot\Upload\Upload($_FILES["image"], "tr_TR");
                    $imageID = $readProductCategory["imageID"];
                    if ($upload->uploaded) {
                      $upload->allowed = array("image/*");
                      $upload->file_overwrite = true;
                      $upload->file_new_name_body = $imageID;
                      $upload->image_resize = true;
                      $upload->image_ratio_crop = true;
                      $upload->image_x = 360;
                      $upload->image_y = 360;
                      $upload->process(__ROOT__."/apps/main/public/assets/img/store/categories/");
                      if ($upload->processed) {
                        $updateProductCategories = $db->prepare("UPDATE ProductCategories SET imageType = ? WHERE id = ?");
                        $updateProductCategories->execute(array($upload->file_dst_name_ext, get("id")));
                      }
                      else {
                        echo alertError("Resim yüklenirken bir hata oluştu: ".$upload->error);
                      }
                    }
                  }
                  if (post("minecraftItem") == "default") {
                    $minecraftItem = null;
                  }
                  else {
                    $minecraftItem = post("minecraftItem");
                  }
                  $updateProductCategories = $db->prepare("UPDATE ProductCategories SET serverID = ?, parentID = ?, name = ?, slug = ?, minecraftStatus = ?, minecraftTitle = ?, minecraftDescription = ?, minecraftItem = ? WHERE id = ?");
                  $updateProductCategories->execute(array(post("serverID"), post("parentID"), post("name"), convertURL(post("name")), post("minecraftStatus"), post("minecraftTitle"), post("minecraftDescription"), $minecraftItem, get("id")));
                  echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">Kategori Adı:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputName" class="form-control" name="name" placeholder="Kategori adı yazınız." value="<?php echo $readProductCategory["name"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectServerID" class="col-sm-2 col-form-label">Sunucu:</label>
                    <div class="col-sm-10">
                      <?php
                        $servers = $db->query("SELECT * FROM Servers");
                      ?>
                      <select id="selectServerID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="serverID" <?php echo ($servers->rowCount() == 0) ? "disabled" : null; ?>>
                        <?php if ($servers->rowCount() > 0): ?>
                          <?php foreach ($servers as $readServers): ?>
                            <option value="<?php echo $readServers["id"]; ?>" <?php echo (($readProductCategory["serverID"] == $readServers["id"]) ? 'selected="selected"' : null); ?>><?php echo $readServers["name"]; ?></option>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <option>Sunucu bulunamadı!</option>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectParentID" class="col-sm-2 col-form-label">Üst Kategori:</label>
                    <div class="col-sm-10">
                      <div id="c-loading" style="display: none; margin-top: 7px">Yükleniyor...</div>
                      <div id="product-categories">
                        <?php
                          $productCategories = $db->prepare("SELECT * FROM ProductCategories WHERE id != ? AND serverID = ?");
                          $productCategories->execute(array($readProductCategory["id"], $readProductCategory["serverID"]));
                        ?>
                        <select id="selectParentID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="parentID">
                          <option value="0">Kategorisiz</option>
                          <?php if ($productCategories->rowCount() > 0): ?>
                            <?php foreach ($productCategories as $readProductCategories): ?>
                              <option value="<?php echo $readProductCategories["id"]; ?>" <?php echo (($readProductCategory["parentID"] == $readProductCategories["id"]) ? 'selected="selected"' : null); ?>><?php echo $readProductCategories["name"]; ?></option>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectMinecraftStatus" class="col-sm-2 col-form-label">Oyunda Göster:</label>
                    <div class="col-sm-10">
                      <select id="selectMinecraftStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="minecraftStatus">
                        <option value="0" <?php echo ($readProductCategory["minecraftStatus"] == 0) ? 'selected="selected"' : null; ?>>Hayır</option>
                        <option value="1" <?php echo ($readProductCategory["minecraftStatus"] == 1) ? 'selected="selected"' : null; ?>>Evet</option>
                      </select>
                    </div>
                  </div>
                  <div id="minecraftBlock" style="<?php echo ($readProductCategory["minecraftStatus"] == 0) ? "display: none;" : "display: block;"; ?>">
                    <div class="form-group row">
                      <label for="input-minecrafttitle" class="col-sm-2 col-form-label">Minecraft GUI Başlığı (Title):</label>
                      <div class="col-sm-10">
                        <input type="text" id="input-minecrafttitle" class="form-control" name="minecraftTitle" placeholder="Boş bırakırsanız ürün adı gözükecektir. Renk Kodları (&) kullanabilirsiniz." value="<?php echo $readProductCategory["minecraftTitle"]; ?>">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="input-minecraftdesc" class="col-sm-2 col-form-label">Minecraft GUI Açıklama (Lore):</label>
                      <div class="col-sm-10">
                        <textarea id="input-minecraftdesc" class="form-control" name="minecraftDescription" placeholder="Boş bırakabilirsiniz. Renk Kodları (&) kullanabilirsiniz."><?php echo $readProductCategory["minecraftDescription"]; ?></textarea>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectMinecraftItem" class="col-sm-2 col-form-label">Minecraft Icon:</label>
                      <div class="col-sm-10">
                        <div id="mi-loading" style="margin-top: 7px">Yükleniyor...</div>
                        <div id="product-minecraftitems" style="display: none;" data-type="update" data-selected="<?php echo $readProductCategory["minecraftItem"]; ?>">
                          <select id="selectMinecraftItem" class="form-control" name="minecraftItem">
                            <option value="default">Varsayılan</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="fileImage" class="col-sm-2 col-form-label">Resim:</label>
                    <div class="col-sm-10">
                      <div data-toggle="dropimage" class="dropimage active">
                        <div class="di-thumbnail">
                          <img src="/apps/main/public/assets/img/store/categories/<?php echo $readProductCategory["imageID"].'.'.$readProductCategory["imageType"]; ?>" alt="Ön İzleme">
                        </div>
                        <div class="di-select">
                          <label for="fileImage">Bir Resim Seçiniz</label>
                          <input type="file" id="fileImage" name="image" accept="image/*">
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateProductCategories'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/magaza/kategori/sil/<?php echo $readProductCategory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/magaza/<?php echo $readProductCategory["serverSlug"]; ?>/<?php echo $readProductCategory["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                        <i class="fe fe-eye"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateProductCategories">Değişiklikleri Kaydet</button>
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
      $deleteProductCategory = $db->prepare("DELETE FROM ProductCategories WHERE id = ?");
      $deleteProductCategory->execute(array(get("id")));
      go("/yonetim-paneli/magaza/kategori");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'credit-package'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Kredi Paketleri</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/magaza/kredi-paket">Mağaza</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Kredi Paketleri</li>
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
          <?php if ($readSettings["creditPackageStatus"] == 1): ?>
            <?php
            $products = $db->prepare("SELECT * FROM CreditPackages ORDER BY id DESC");
            $products->execute(array(0));
            ?>
            <?php if ($products->rowCount() > 0): ?>
              <div class="card" data-toggle="lists" data-lists-values='["productID", "productName", "productAmount", "productBonus", "productPrice", "productStock"]'>
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
                      <a class="btn btn-sm btn-white" href="/yonetim-paneli/magaza/kredi-paket/ekle">Kredi Paketi Ekle</a>
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
                          <a href="#" class="text-muted sort" data-sort="productID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productName">
                            Ürün Adı
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productPrice">
                            Fiyat
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productAmount">
                            Miktar
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productBonus">
                            Bonus
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productStock">
                            Stok
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                      </thead>
                      <tbody class="list">
                      <?php foreach ($products as $readProducts): ?>
                        <tr>
                          <td class="productID text-center" style="width: 40px;">
                            <a href="/yonetim-paneli/magaza/kredi-paket/duzenle/<?php echo $readProducts["id"]; ?>">
                              #<?php echo $readProducts["id"]; ?>
                            </a>
                          </td>
                          <td class="productName">
                            <a href="/yonetim-paneli/magaza/kredi-paket/duzenle/<?php echo $readProducts["id"]; ?>">
                              <?php echo $readProducts["name"]; ?>
                            </a>
                          </td>
                          <td class="productPrice">
                            <?php echo ($readProducts["discountedPrice"] > 0) ? $readProducts["discountedPrice"] : $readProducts["price"]; ?> TL
                          </td>
                          <td class="productAmount">
                            <?php echo $readProducts["amount"]; ?>
                          </td>
                          <td class="productBonus">
                            <?php echo $readProducts["bonus"] == 0 ? "-" : $readProducts["bonus"]; ?>
                          </td>
                          <td class="productStock">
                            <?php echo ($readProducts["stock"] == -1) ? "Sınırsız" : $readProducts["stock"]; ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/magaza/kredi-paket/duzenle/<?php echo $readProducts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/magaza/kredi-paket/sil/<?php echo $readProducts["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
          <?php else: ?>
            <?php echo alertError("Kredi paketi sistemini kullanmak için Kredi Paketi ayarını aktifleştirmeniz gerekmektedir. <a href='/yonetim-paneli/ayarlar/sistem' class='text-white text-decoration-underline'>Ayarlar > Sistem Ayarları > Kredi Paketi</a>"); ?>
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
                  <h2 class="header-title">Kredi Paketi Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/kredi-paket">Mağaza</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/kredi-paket">Kredi Paketi</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Kredi Paketi Ekle</li>
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
            if (isset($_POST["insertProducts"])) {
              if (post("stockStatus") == 0) {
                $_POST["stock"] = -1;
              }
              if (post("discountStatus") == 0) {
                $_POST["discountedPrice"] = 0;
              }
              if (post("discountDurationStatus") == 0 || post("discountStatus") == 0) {
                $_POST["discountDuration"] = '1000-01-01 00:00:00';
              }
              else {
                $_POST["discountDuration"] = date("Y-m-d H:i:s", strtotime($_POST["discountDuration"]));
              }
              if (!$csrf->validate('insertProducts')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("name") == null || post("bonus") == null || post("details") == null || post("price") == null || post("amount") == null || post("discountStatus") == null || post("discountedPrice") == null || post("discountDurationStatus") == null || post("discountDuration") == null || post("stockStatus") == null || post("stock") == null) {
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
                  $upload->image_resize = true;
                  $upload->image_ratio_crop = true;
                  $upload->image_x = 360;
                  $upload->image_y = 360;
                  $upload->process(__ROOT__."/apps/main/public/assets/img/store/products/");
                  if ($upload->processed) {
                    $insertProducts = $db->prepare("INSERT INTO CreditPackages (name, imageID, imageType, details, amount, price, bonus, discountedPrice, discountExpiryDate, stock, creationDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insertProducts->execute(array(post("name"), $imageID, $upload->file_dst_name_ext, filteredContent($_POST["details"]), post("amount"), post("price"), post("bonus"), post("discountedPrice"), post("discountDuration"), post("stock"), date("Y-m-d H:i:s")));
                    echo alertSuccess("Paket başarıyla eklendi!");
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
                  <label for="inputname" class="col-sm-2 col-form-label">Paket Adı:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputname" class="form-control" name="name" placeholder="Paket adını yazınız.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputAmount" class="col-sm-2 col-form-label">Kredi Miktarı:</label>
                  <div class="col-sm-10">
                    <div class="input-group input-group-merge">
                      <input type="number" id="inputAmount" class="form-control form-control-prepended" name="amount" placeholder="Satın aldığında hesaba eklenecek kredi miktarını yazınız.">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <span class="fa fa-coins"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputBonus" class="col-sm-2 col-form-label">Bonus Miktarı:</label>
                  <div class="col-sm-10">
                    <div class="input-group input-group-merge">
                      <input type="number" id="inputBonus" class="form-control form-control-prepended" name="bonus" placeholder="Satın aldığında hesaba eklenecek bonus miktarını yazınız." value="0">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <span class="fa fa-plus"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputPrice" class="col-sm-2 col-form-label">Fiyat:</label>
                  <div class="col-sm-10">
                    <div class="input-group input-group-merge">
                      <input type="number" step="any" id="inputPrice" class="form-control form-control-prepended" name="price" placeholder="Paket fiyatını yazınız.">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          ₺
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectDiscountStatus" class="col-sm-2 col-form-label">İndirim:</label>
                  <div class="col-sm-10">
                    <select id="selectDiscountStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="discountStatus">
                      <option value="0">Yok</option>
                      <option value="1">Var</option>
                    </select>
                  </div>
                </div>
                <div id="discountBlock" style="display: none;">
                  <div class="form-group row">
                    <label for="inputDiscountedPrice" class="col-sm-2 col-form-label">İndirimli Fiyat:</label>
                    <div class="col-sm-10">
                      <div class="input-group input-group-merge">
                        <input type="number" step="any" id="inputDiscountedPrice" class="form-control form-control-prepended" name="discountedPrice" placeholder="Paketin indirimli fiyatını yazınız.">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                           ₺
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectDiscountDurationStatus" class="col-sm-2 col-form-label">İndirim Süresi:</label>
                    <div class="col-sm-10">
                      <select id="selectDiscountDurationStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="discountDurationStatus">
                        <option value="0">Süresiz</option>
                        <option value="1">Süreli</option>
                      </select>
                    </div>
                  </div>
                  <div id="discountDurationBlock" style="display: none;">
                    <div class="form-group row">
                      <div class="col-sm-10 offset-sm-2">
                        <div class="input-group input-group-merge">
                          <input type="text" id="inputDiscountDuration" class="form-control form-control-prepended" name="discountDuration" placeholder="Pakete uygulanacak indirimin son kullanım tarihini seçiniz." data-toggle="flatpickr" data-expirydate="true">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <span class="fe fe-clock"></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectStockStatus" class="col-sm-2 col-form-label">Stok:</label>
                  <div class="col-sm-10">
                    <select id="selectStockStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="stockStatus">
                      <option value="0">Sınırsız</option>
                      <option value="1">Sınırlı</option>
                    </select>
                  </div>
                </div>
                <div id="stockBlock" style="display: none;">
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputStock" class="form-control form-control-prepended" name="stock" placeholder="Paketin stok adedini yazınız.">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-package"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaDetails" class="col-sm-2 col-form-label">Detaylar:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaDetails" class="form-control" data-toggle="textEditor" name="details" placeholder="Paketin detaylarını/özelliklerini yazınız."></textarea>
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
                <?php echo $csrf->input('insertProducts'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertProducts">Ekle</button>
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
    $product = $db->prepare("SELECT * FROM CreditPackages WHERE id = ?");
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
                  <h2 class="header-title">Kredi Paketi Düzenle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/kredi-paket">Mağaza</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/kredi-paket">Kredi Paketi</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/kredi-paket">Kredi Paketi Düzenle</a></li>
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
              if (post("stockStatus") == 0) {
                $_POST["stock"] = -1;
              }
              if (post("discountStatus") == 0) {
                $_POST["discountedPrice"] = 0;
              }
              if (post("discountDurationStatus") == 0 || post("discountStatus") == 0) {
                $_POST["discountDuration"] = '1000-01-01 00:00:00';
              }
              else {
                $_POST["discountDuration"] = date("Y-m-d H:i:s", strtotime($_POST["discountDuration"]));
              }
              if (!$csrf->validate('updateProducts')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("name") == null || post("bonus") == null || post("details") == null || post("price") == null || post("amount") == null || post("discountStatus") == null || post("discountedPrice") == null || post("discountDurationStatus") == null || post("discountDuration") == null || post("stockStatus") == null || post("stock") == null) {
                echo alertError("Lütfen boş alan bırakmayınız!");
              }
              else {
                if ($_FILES["image"]["size"] != null) {
                  require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                  $upload = new \Verot\Upload\Upload($_FILES["image"], "tr_TR");
                  $imageID = $readProduct["imageID"];
                  if ($upload->uploaded) {
                    $upload->allowed = array("image/*");
                    $upload->file_overwrite = true;
                    $upload->file_new_name_body = $imageID;
                    $upload->image_resize = true;
                    $upload->image_ratio_crop = true;
                    $upload->image_x = 360;
                    $upload->image_y = 360;
                    $upload->process(__ROOT__."/apps/main/public/assets/img/store/products/");
                    if ($upload->processed) {
                      $updateProducts = $db->prepare("UPDATE CreditPackages SET imageType = ? WHERE id = ?");
                      $updateProducts->execute(array($upload->file_dst_name_ext, get("id")));
                    }
                    else {
                      echo alertError("Resim yüklenirken bir hata oluştu: ".$upload->error);
                    }
                  }
                }
                $updateProducts = $db->prepare("UPDATE CreditPackages SET name = ?, details = ?, amount = ?, price = ?, bonus = ?, discountedPrice = ?, discountExpiryDate = ?, stock = ? WHERE id = ?");
                $updateProducts->execute(array(post("name"), filteredContent($_POST["details"]), post("amount"), post("price"), post("bonus"), post("discountedPrice"),  post("discountDuration"), post("stock"), get("id")));
                echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
              }
            }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label for="inputname" class="col-sm-2 col-form-label">Paket Adı:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputname" class="form-control" name="name" placeholder="Paket adını yazınız." value="<?php echo $readProduct["name"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputAmount" class="col-sm-2 col-form-label">Kredi Miktarı:</label>
                    <div class="col-sm-10">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputAmount" class="form-control form-control-prepended" name="amount" placeholder="Satın aldığında hesaba eklenecek kredi miktarını yazınız." value="<?php echo $readProduct["amount"]; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fa fa-coins"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputBonus" class="col-sm-2 col-form-label">Bonus Miktarı:</label>
                    <div class="col-sm-10">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputBonus" class="form-control form-control-prepended" name="bonus" placeholder="Satın aldığında hesaba eklenecek bonus miktarını yazınız." value="<?php echo $readProduct["bonus"]; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fa fa-plus"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPrice" class="col-sm-2 col-form-label">Fiyat:</label>
                    <div class="col-sm-10">
                      <div class="input-group input-group-merge">
                        <input type="number" step="any" id="inputPrice" class="form-control form-control-prepended" name="price" placeholder="Paket fiyatını yazınız." value="<?php echo $readProduct["price"]; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                           ₺
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectDiscountStatus" class="col-sm-2 col-form-label">İndirim:</label>
                    <div class="col-sm-10">
                      <select id="selectDiscountStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="discountStatus">
                        <option value="0" <?php echo ($readProduct["discountedPrice"] == 0) ? 'selected="selected"' : null; ?>>Yok</option>
                        <option value="1" <?php echo ($readProduct["discountedPrice"] != 0) ? 'selected="selected"' : null; ?>>Var</option>
                      </select>
                    </div>
                  </div>
                  <div id="discountBlock" style="<?php echo ($readProduct["discountedPrice"] == 0) ? "display: none;" : "display: block;"; ?>">
                    <div class="form-group row">
                      <label for="inputDiscountedPrice" class="col-sm-2 col-form-label">İndirimli Fiyat:</label>
                      <div class="col-sm-10">
                        <div class="input-group input-group-merge">
                          <input type="number" step="any" id="inputDiscountedPrice" class="form-control form-control-prepended" name="discountedPrice" placeholder="Paketin indirimli fiyatını yazınız." value="<?php echo ($readProduct["discountedPrice"] != 0) ? $readProduct["discountedPrice"] : null; ?>">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              ₺
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectDiscountDurationStatus" class="col-sm-2 col-form-label">İndirim Süresi:</label>
                      <div class="col-sm-10">
                        <select id="selectDiscountDurationStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="discountDurationStatus">
                          <option value="0" <?php echo ($readProduct["discountExpiryDate"] == '1000-01-01 00:00:00') ? 'selected="selected"' : null; ?>>Süresiz</option>
                          <option value="1" <?php echo ($readProduct["discountExpiryDate"] != '1000-01-01 00:00:00') ? 'selected="selected"' : null; ?>>Süreli</option>
                        </select>
                      </div>
                    </div>
                    <div id="discountDurationBlock" style="<?php echo ($readProduct["discountExpiryDate"] == '1000-01-01 00:00:00') ? "display: none;" : "display: block;"; ?>">
                      <div class="form-group row">
                        <div class="col-sm-10 offset-sm-2">
                          <div class="input-group input-group-merge">
                            <input type="text" id="inputDiscountDuration" class="form-control form-control-prepended" name="discountDuration" placeholder="Pakete uygulanacak indirimin son kullanım tarihini seçiniz." data-toggle="flatpickr" data-expirydate="true" value="<?php echo ($readProduct["discountExpiryDate"] != '1000-01-01 00:00:00') ? $readProduct["discountExpiryDate"] : null; ?>">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <span class="fe fe-clock"></span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectStockStatus" class="col-sm-2 col-form-label">Stok:</label>
                    <div class="col-sm-10">
                      <select id="selectStockStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="stockStatus">
                        <option value="0" <?php echo ($readProduct["stock"] == -1) ? 'selected="selected"' : null; ?>>Sınırsız</option>
                        <option value="1" <?php echo ($readProduct["stock"] != -1) ? 'selected="selected"' : null; ?>>Sınırlı</option>
                      </select>
                    </div>
                  </div>
                  <div id="stockBlock" style="<?php echo ($readProduct["stock"] == -1) ? "display: none;" : "display: block;"; ?>">
                    <div class="form-group row">
                      <div class="col-sm-10 offset-sm-2">
                        <div class="input-group input-group-merge">
                          <input type="number" id="inputStock" class="form-control form-control-prepended" name="stock" placeholder="Paketin stok adedini yazınız." value="<?php echo ($readProduct["stock"] != -1) ? $readProduct["stock"] : null; ?>">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <span class="fe fe-package"></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="textareaDetails" class="col-sm-2 col-form-label">Detaylar:</label>
                    <div class="col-sm-10">
                      <textarea id="textareaDetails" class="form-control" data-toggle="textEditor" name="details" placeholder="Paketin detaylarını/özelliklerini yazınız."><?php echo $readProduct["details"]; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="fileImage" class="col-sm-2 col-form-label">Resim:</label>
                    <div class="col-sm-10">
                      <div data-toggle="dropimage" class="dropimage active">
                        <div class="di-thumbnail">
                          <img src="/apps/main/public/assets/img/store/products/<?php echo $readProduct["imageID"].'.'.$readProduct["imageType"]; ?>" alt="Ön İzleme">
                        </div>
                        <div class="di-select">
                          <label for="fileImage">Bir Resim Seçiniz</label>
                          <input type="file" id="fileImage" name="image" accept="image/*">
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateProducts'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/magaza/kredi-paket/sil/<?php echo $readProduct["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateProducts">Değişiklikleri Kaydet</button>
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
    $deleteProduct = $db->prepare("DELETE FROM CreditPackages WHERE id = ?");
    $deleteProduct->execute(array(get("id")));
    go("/yonetim-paneli/magaza/kredi-paket");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'coupon'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Kuponlar</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Mağaza</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Kuponlar</li>
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
          <?php $productCoupons = $db->query("SELECT * FROM ProductCoupons ORDER BY id DESC"); ?>
          <?php if ($productCoupons->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["productCouponID", "productCouponName", "productCouponDuration", "productCouponPiece", "productCouponPieceStock", "productCouponCreationDate"]'>
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
                    <a class="btn btn-sm btn-white" href="/yonetim-paneli/magaza/kupon/ekle">Kupon Ekle</a>
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
                          <a href="#" class="text-muted sort" data-sort="productCouponID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productCouponName">
                            Kupon Adı
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productCouponDuration">
                            Kalan Süre
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productCouponPiece">
                            Adet
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productCouponPieceStock">
                            Kalan Adet
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="productCouponCreationDate">
                            Tarih
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($productCoupons as $readProductCoupons): ?>
                        <tr>
                          <td class="productCouponID text-center" style="width: 40px;">
                            <a href="/yonetim-paneli/magaza/kupon/duzenle/<?php echo $readProductCoupons["id"]; ?>">
                              #<?php echo $readProductCoupons["id"]; ?>
                            </a>
                          </td>
                          <td class="productCouponName">
                            <a href="/yonetim-paneli/magaza/kupon/duzenle/<?php echo $readProductCoupons["id"]; ?>">
                              <?php echo $readProductCoupons["name"]; ?>
                            </a>
                          </td>
                          <td class="productCouponDuration">
                            <?php echo (($readProductCoupons["expiryDate"] == '1000-01-01 00:00:00') ? 'Süresiz' : getDuration($readProductCoupons["expiryDate"]).' gün'); ?>
                          </td>
                          <td class="productCouponPiece">
                            <?php echo (($readProductCoupons["piece"] == 0) ? 'Sınırsız' : $readProductCoupons["piece"].' adet'); ?>
                          </td>
                          <td class="productCouponPieceStock">
                            <?php if ($readProductCoupons["piece"] == 0): ?>
                              Sınırsız
                            <?php else: ?>
                              <?php
                                $productCouponsHistory = $db->prepare("SELECT * FROM ProductCouponsHistory WHERE couponID = ?");
                                $productCouponsHistory->execute(array($readProductCoupons["id"]));
                                echo (max($readProductCoupons["piece"]-$productCouponsHistory->rowCount(), 0).' adet');
                              ?>
                            <?php endif; ?>
                          </td>
                          <td class="productCouponCreationDate">
                            <?php echo convertTime($readProductCoupons["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/magaza/kupon/duzenle/<?php echo $readProductCoupons["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/magaza/kupon/sil/<?php echo $readProductCoupons["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
                  <h2 class="header-title">Kupon Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Mağaza</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/kupon">Kupon</a></li>
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
            if (isset($_POST["insertProductCoupons"])) {
              if (post("durationStatus") == 0) {
                $_POST["duration"] = '1000-01-01 00:00:00';
              }
              else {
                $_POST["duration"] = date("Y-m-d H:i:s", strtotime($_POST["duration"]));
              }
              if (post("pieceStatus") == 0) {
                $_POST["piece"] = 0;
              }
              if (post("productsStatus") == 0) {
                $_POST["products"] = 0;
              }
              if (!$csrf->validate('insertProductCoupons')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("name") == null || post("productsStatus") == null || post("products") == null || post("discount") == null || post("pieceStatus") == null || post("piece") == null || post("durationStatus") == null || post("duration") == null || post("minPayment") == null) {
                echo alertError("Lütfen boş alan bırakmayınız!");
              }
              else {
                $insertProductCoupons = $db->prepare("INSERT INTO ProductCoupons (name, products, discount, piece, minPayment, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $insertProductCoupons->execute(array(str_replace(" ", "", post("name")), post("products"), post("discount"), post("piece"), post("minPayment"), post("duration"), date("Y-m-d H:i:s")));
                echo alertSuccess("Kupon başarıyla eklendi!");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputName" class="col-sm-2 col-form-label">Kupon Adı:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputName" class="form-control" name="name" placeholder="Kupon adı giriniz.">
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
                      <input type="text" id="inputDuration" class="form-control form-control-prepended" name="duration" placeholder="Kupon son kullanım tarihini seçiniz." data-toggle="flatpickr" data-expirydate="true">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <span class="fe fe-clock"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectPieceStatus" class="col-sm-2 col-form-label">Adet:</label>
                  <div class="col-sm-10">
                    <select id="selectPieceStatus" class="form-control" name="pieceStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0">Sınırsız</option>
                      <option value="1">Sınırlı</option>
                    </select>
                  </div>
                </div>
                <div id="pieceBlock" class="form-group row" style="display: none;">
                  <div class="col-sm-10 offset-sm-2">
                    <div class="input-group input-group-merge">
                      <input type="number" id="inputPiece" class="form-control form-control-prepended" name="piece" placeholder="Kupon kullanım adedini yazınız.">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <span class="fe fe-plus-circle"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputDiscount" class="col-sm-2 col-form-label">İndirim (%):</label>
                  <div class="col-sm-10">
                    <div class="input-group input-group-merge">
                      <input type="number" id="inputDiscount" class="form-control form-control-prepended" name="discount" min="1" max="100" placeholder="Ürüne uygulanacak indirim yüzdesini yazınız.">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <span class="fe fe-percent"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputMinPayment" class="col-sm-2 col-form-label">Minimum Ödeme Tutarı:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputMinPayment" class="form-control" name="minPayment" placeholder="Kupon kaç kredi üzeri ödemelerde geçerli olacağını yazınız.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectProductsStatus" class="col-sm-2 col-form-label">Ürünler:</label>
                  <div class="col-sm-10">
                    <select id="selectProductsStatus" class="form-control" name="productsStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0">Tümü</option>
                      <option value="1">Seçili Ürünler</option>
                    </select>
                  </div>
                </div>
                <div id="productsBlock" class="form-group row" style="display: none;">
                  <div class="col-sm-10 offset-sm-2">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <input type="text" class="form-control form-control-prepended" data-toggle="jstree-search" placeholder="Ürün Ara">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-search"></span>
                          </div>
                        </div>
                      </div>
                      <input type="hidden" data-toggle="jstree-value" name="products" value="">
                    </div>
                    <div data-toggle="jstree" json="/apps/dashboard/public/ajax/products.php"></div>
                  </div>
                </div>
                <?php echo $csrf->input('insertProductCoupons'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertProductCoupons">Ekle</button>
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
      $productCoupon = $db->prepare("SELECT * FROM ProductCoupons WHERE id = ?");
      $productCoupon->execute(array(get("id")));
      $readProductCoupon = $productCoupon->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Kupon Düzenle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Mağaza</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/kupon">Kupon</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/kupon">Düzenle</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($productCoupon->rowCount() > 0) ? $readProductCoupon["name"] : "Bulunamadı!"; ?></li>
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
          <?php if ($productCoupon->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateProductCoupons"])) {
                if (post("durationStatus") == 0) {
                  $_POST["duration"] = '1000-01-01 00:00:00';
                }
                else {
                  $_POST["duration"] = date("Y-m-d H:i:s", strtotime($_POST["duration"]));
                }
                if (post("pieceStatus") == 0) {
                  $_POST["piece"] = 0;
                }
                if (post("productsStatus") == 0) {
                  $_POST["products"] = 0;
                }
                if (!$csrf->validate('updateProductCoupons')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("name") == null || post("productsStatus") == null || post("products") == null || post("discount") == null || post("pieceStatus") == null || post("piece") == null || post("durationStatus") == null || post("duration") == null || post("minPayment") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  $updateProductCoupons = $db->prepare("UPDATE ProductCoupons SET name = ?, products = ?, discount = ?, expiryDate = ?, piece = ?, minPayment = ? WHERE id = ?");
                  $updateProductCoupons->execute(array(str_replace(" ", "", post("name")), post("products"), post("discount"), post("duration"), post("piece"), post("minPayment"), get("id")));
                  echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">Kupon Adı:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputName" class="form-control" name="name" placeholder="Kupon adı giriniz." value="<?php echo $readProductCoupon["name"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectDurationStatus" class="col-sm-2 col-form-label">Süre:</label>
                    <div class="col-sm-10">
                      <select id="selectDurationStatus" class="form-control" name="durationStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readProductCoupon["expiryDate"] == '1000-01-01 00:00:00') ? 'selected="selected"' : null; ?>>Süresiz</option>
                        <option value="1" <?php echo ($readProductCoupon["expiryDate"] != '1000-01-01 00:00:00') ? 'selected="selected"' : null; ?>>Süreli</option>
                      </select>
                    </div>
                  </div>
                  <div id="durationBlock" class="form-group row" style="<?php echo ($readProductCoupon["expiryDate"] == '1000-01-01 00:00:00') ? "display: none;" : "display: block;"; ?>">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="input-group input-group-merge">
                        <input type="text" id="inputDuration" class="form-control form-control-prepended" name="duration" placeholder="Kupon son kullanım tarihini seçiniz." data-toggle="flatpickr" data-expirydate="true" value="<?php echo ($readProductCoupon["expiryDate"] != '1000-01-01 00:00:00') ? $readProductCoupon["expiryDate"] : null; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-clock"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectPieceStatus" class="col-sm-2 col-form-label">Adet:</label>
                    <div class="col-sm-10">
                      <select id="selectPieceStatus" class="form-control" name="pieceStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readProductCoupon["piece"] == 0) ? 'selected="selected"' : null; ?>>Sınırsız</option>
                        <option value="1" <?php echo ($readProductCoupon["piece"] != 0) ? 'selected="selected"' : null; ?>>Sınırlı</option>
                      </select>
                    </div>
                  </div>
                  <div id="pieceBlock" class="form-group row" style="<?php echo ($readProductCoupon["piece"] == 0) ? "display: none;" : "display: block;"; ?>">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputPiece" class="form-control form-control-prepended" name="piece" placeholder="Kupon kullanım adedini yazınız." value="<?php echo ($readProductCoupon["piece"] != 0) ? $readProductCoupon["piece"] : null; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-plus-circle"></span>
                          </div>
                        </div>
                      </div>
                      <?php if ($readProductCoupon["piece"] != 0): ?>
                        <?php
                          $productCouponsHistory = $db->prepare("SELECT * FROM ProductCouponsHistory WHERE couponID = ?");
                          $productCouponsHistory->execute(array($readProductCoupon["id"]));
                        ?>
                        <small class="form-text text-muted pt-2">
                          <strong>Kalan:</strong> <?php echo (max($readProductCoupon["piece"]-$productCouponsHistory->rowCount().' adet', 0)); ?>
                        </small>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputDiscount" class="col-sm-2 col-form-label">İndirim (%):</label>
                    <div class="col-sm-10">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputDiscount" class="form-control form-control-prepended" name="discount" min="1" max="100" placeholder="Ürüne uygulanacak indirim yüzdesini yazınız." value="<?php echo $readProductCoupon["discount"]; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-percent"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputMinPayment" class="col-sm-2 col-form-label">Minimum Ödeme Tutarı:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputMinPayment" class="form-control" name="minPayment" placeholder="Kupon kaç kredi üzeri ödemelerde geçerli olacağını yazınız." value="<?php echo $readProductCoupon["minPayment"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectProductsStatus" class="col-sm-2 col-form-label">Ürünler:</label>
                    <div class="col-sm-10">
                      <select id="selectProductsStatus" class="form-control" name="productsStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readProductCoupon["products"] == 0) ? 'selected="selected"' : null; ?>>Tümü</option>
                        <option value="1" <?php echo ($readProductCoupon["products"] != 0) ? 'selected="selected"' : null; ?>>Seçili Ürünler</option>
                      </select>
                    </div>
                  </div>
                  <div id="productsBlock" class="form-group row" style="<?php echo ($readProductCoupon["products"] == 0) ? "display: none;" : "display: block;"; ?>">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="form-group">
                        <div class="input-group input-group-merge">
                          <input type="text" class="form-control form-control-prepended" data-toggle="jstree-search" placeholder="Ürün Ara">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <span class="fe fe-search"></span>
                            </div>
                          </div>
                        </div>
                        <input type="hidden" data-toggle="jstree-value" name="products" value="<?php echo ($readProductCoupon["products"] != 0) ? $readProductCoupon["products"] : null; ?>">
                      </div>
                      <div data-toggle="jstree" json="/apps/dashboard/public/ajax/products.php"></div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateProductCoupons'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/magaza/kupon/sil/<?php echo $readProductCoupon["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateProductCoupons">Değişiklikleri Kaydet</button>
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
      $deleteProductCoupon = $db->prepare("DELETE FROM ProductCoupons WHERE id = ?");
      $deleteProductCoupon->execute(array(get("id")));
      go("/yonetim-paneli/magaza/kupon");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'credit'): ?>
  <?php if (get("action") == 'send'): ?>
    <?php
      if (get("id")) {
        $account = $db->prepare("SELECT * FROM Accounts WHERE id = ?");
        $account->execute(array(get("id")));
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
                  <h2 class="header-title"><?php echo $readSettings["creditText"] ?> Gönder</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Mağaza</a></li>
                      <?php if (get("id")): ?>
                        <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/kredi/gonder"><?php echo $readSettings["creditText"] ?> Gönder</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo ($account->rowCount() > 0) ? $readAccount["realname"] : "Bulunamadı!"; ?></li>
                      <?php else: ?>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $readSettings["creditText"] ?> Gönder</li>
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
              if (isset($_POST["insertCreditHistory"])) {
                if ((!get("id") && post("username") != null)) {
                  $account = $db->prepare("SELECT * FROM Accounts WHERE realname = ?");
                  $account->execute(array(post("username")));
                  $readAccount = $account->fetch();
                }
                if (!$csrf->validate('insertCreditHistory')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if ((!get("id") && post("username") == null) || post("type") == null || post("price") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else if (!get("id") && $account->rowCount() == 0) {
                  echo alertError("Kullanıcı bulunamadı!");
                }
                else {
                  if (post("type") == 3) {
                    $_POST["type"] = 2;
                  }
                  $insertCreditHistory = $db->prepare("INSERT INTO CreditHistory (accountID, paymentID, paymentAPI, paymentStatus, type, price, earnings, creationDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                  $insertCreditHistory->execute(array($readAccount["id"], 0, post("paymentAPI"), 1, post("type"), post("price"), post("price")/$readSettings["creditMultiplier"], date("Y-m-d H:i:s")));
                  $updateAccounts = $db->prepare("UPDATE Accounts SET credit = credit + ? WHERE id = ?");
                  $updateAccounts->execute(array(post("price"), $readAccount["id"]));

                  if ($readSettings["webhookCreditURL"] != '0') {
                    require_once(__ROOT__."/apps/main/private/packages/class/webhook/webhook.php");
                    $search = array("%username%", "%credit%", "%money%");
                    $replace = array($readAccount["realname"], (int)post("price"), (int)(post("price")/$readSettings["creditMultiplier"]));
                    $webhookMessage = $readSettings["webhookCreditMessage"];
                    $webhookEmbed = $readSettings["webhookCreditEmbed"];
                    $postFields = (array(
                      'content'     => ($webhookMessage != '0') ? str_replace($search, $replace, $webhookMessage) : null,
                      'avatar_url'  => 'https://minotar.net/avatar/'.$readAccount["realname"].'/256.png',
                      'tts'         => false,
                      'embeds'      => array(
                        array(
                          'type'        => 'rich',
                          'title'       => $readSettings["webhookCreditTitle"],
                          'color'       => hexdec($readSettings["webhookCreditColor"]),
                          'description' => str_replace($search, $replace, $webhookEmbed),
                          'image'       => array(
                            'url' => ($readSettings["webhookCreditImage"] != '0') ? $readSettings["webhookCreditImage"] : null
                          ),
                          'footer'      =>
                          ($readSettings["webhookCreditAdStatus"] == 1) ? array(
                            'text'      => 'Powered by LeaderOS',
                            'icon_url'  => 'https://i.ibb.co/wNHKQ7B/leaderos-logo.png'
                          ) : array()
                        )
                      )
                    ));
                    $curl = new \LeaderOS\Http\Webhook($readSettings["webhookCreditURL"]);
                    $curl(json_encode($postFields, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                  }

                  echo alertSuccess($readSettings["creditText"] ." başarıyla gönderildi!");
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
                        <a id="staticUsername" class="form-control-plaintext" href="/yonetim-paneli/hesap/goruntule/<?php echo $readAccount["id"]; ?>">
                          <?php echo $readAccount["realname"]; ?>
                        </a>
                      </div>
                    <?php else: ?>
                      <label for="inputUsername" class="col-sm-2 col-form-label">Kullanıcı Adı:</label>
                      <div class="col-sm-10">
                        <input type="text" id="inputUsername" class="form-control" name="username" placeholder="<?php echo $readSettings["creditText"] ?> gönderilecek oyuncunun kullanıcı adını yazınız.">
                      </div>
                    <?php endif; ?>
                  </div>
                  <div class="form-group row">
                    <label for="inputPrice" class="col-sm-2 col-form-label">Gönderilecek Miktar:</label>
                    <div class="col-sm-10">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputPrice" class="form-control form-control-prepended" name="price" placeholder="Oyuncuya gönderilecek <?php echo $readSettings["creditText"] ?> miktarını yazınız.">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fa fa-coins"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectAPIID" class="col-sm-2 col-form-label">Ödeme Altyapısı:</label>
                    <div class="col-sm-10">
                      <select id="selectAPIID" class="form-control" name="paymentAPI" data-toggle="select" data-minimum-results-for-search="-1">
                        <?php
                          $paymentSettings = $db->prepare("SELECT name, slug FROM PaymentSettings WHERE status = ? ORDER BY slug ASC");
                          $paymentSettings->execute(array(1));
                        ?>
                        <?php if ($paymentSettings->rowCount() > 0): ?>
                          <?php foreach ($paymentSettings as $readPaymentSettings): ?>
                            <option value="<?php echo $readPaymentSettings["slug"]; ?>"><?php echo $readPaymentSettings["name"]; ?></option>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectType" class="col-sm-2 col-form-label">Ödeme Yöntemi:</label>
                    <div class="col-sm-10">
                      <select id="selectType" class="form-control" name="type" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="1">Mobil Ödeme</option>
                        <option value="2">Kredi Kartı</option>
                        <option value="3" disabled="disabled">EFT</option>
                      </select>
                    </div>
                  </div>
                  <?php echo $csrf->input('insertCreditHistory'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <button type="submit" class="btn btn-rounded btn-success" name="insertCreditHistory"><?php echo $readSettings["creditText"] ?> Gönder</button>
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
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'chest'): ?>
  <?php if (get("action") == 'send'): ?>
    <?php
      if (get("id")) {
        $account = $db->prepare("SELECT * FROM Accounts WHERE id = ?");
        $account->execute(array(get("id")));
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
                  <h2 class="header-title">Eşya Gönder</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Mağaza</a></li>
                      <?php if (get("id")): ?>
                        <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/esya/gonder">Eşya Gönder</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo ($account->rowCount() > 0) ? $readAccount["realname"] : "Bulunamadı!"; ?></li>
                      <?php else: ?>
                        <li class="breadcrumb-item active" aria-current="page">Eşya Gönder</li>
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
              if (isset($_POST["sendChestItem"])) {
                if ((!get("id") && post("username") != null)) {
                  $account = $db->prepare("SELECT * FROM Accounts WHERE realname = ?");
                  $account->execute(array(post("username")));
                  $readAccount = $account->fetch();
                }
                if (!$csrf->validate('sendChestItem')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if ((!get("id") && post("username") == null) || post("productID") == null || post("amount") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else if (!get("id") && $account->rowCount() == 0) {
                  echo alertError("Kullanıcı bulunamadı!");
                }
                else {
                  for ($i = 0; $i < post("amount"); $i++) {
                    $insertChests = $db->prepare("INSERT INTO Chests (accountID, productID, status, creationDate) VALUES (?, ?, ?, ?)");
                    $insertChests->execute(array($readAccount["id"], post("productID"), 0, date("Y-m-d H:i:s")));
                  }
                  
                  echo alertSuccess("Eşya başarıyla gönderildi!");
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
                        <a id="staticUsername" class="form-control-plaintext" href="/yonetim-paneli/hesap/goruntule/<?php echo $readAccount["id"]; ?>">
                          <?php echo $readAccount["realname"]; ?>
                        </a>
                      </div>
                    <?php else: ?>
                      <label for="inputUsername" class="col-sm-2 col-form-label">Kullanıcı Adı:</label>
                      <div class="col-sm-10">
                        <input type="text" id="inputUsername" class="form-control" name="username" placeholder="Eşya gönderilecek oyuncunun kullanıcı adını yazınız.">
                      </div>
                    <?php endif; ?>
                  </div>
                  <div class="form-group row">
                    <label for="selectServerID" class="col-sm-2 col-form-label">Sunucu:</label>
                    <div class="col-sm-10">
                      <?php $servers = $db->query("SELECT * FROM Servers"); ?>
                      <select id="selectServerID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" <?php echo ($servers->rowCount() == 0) ? "disabled" : null; ?>>
                        <?php if ($servers->rowCount() > 0): ?>
                          <?php foreach ($servers as $readServers): ?>
                            <option value="<?php echo $readServers["id"]; ?>"><?php echo $readServers["name"]; ?></option>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <option>Sunucu bulunamadı!</option>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectProductID" class="col-sm-2 col-form-label">Ürün:</label>
                    <div class="col-sm-10">
                      <div id="c-loading" style="display: none; margin-top: 7px">Yükleniyor...</div>
                      <div id="products">
                        <?php
                          $firstServer = $db->query("SELECT * FROM Servers ORDER BY id ASC LIMIT 1");
                          $readFirstServer = $firstServer->fetch();

                          $productCategories = $db->prepare("SELECT * FROM ProductCategories WHERE serverID = ?");
                          $productCategories->execute(array($readFirstServer["id"]));
                        ?>
                        <select name="productID" id="selectProductID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1">
                          <?php foreach ($productCategories as $readCategories): ?>
                            <optgroup label="<?php echo $readCategories["name"]; ?>" data-select2-id="<?php echo $readCategories["id"]; ?>">
                              <?php
                                $products = $db->prepare("SELECT * FROM Products WHERE serverID = ? AND categoryID = ?");
                                $products->execute(array($readCategories["serverID"], $readCategories["id"]));
                              ?>
                              <?php foreach ($products as $readProducts): ?>
                                <option value="<?php echo $readProducts["id"]; ?>"><?php echo $readProducts["name"]; ?></option>
                              <?php endforeach; ?>
                            </optgroup>
                          <?php endforeach; ?>

                          <?php
                            $products = $db->prepare("SELECT * FROM Products WHERE serverID = ? AND categoryID = ?");
                            $products->execute(array($readFirstServer["id"], 0));
                          ?>
                          <?php if ($products->rowCount() > 0): ?>
                            <optgroup label="Diğer" data-select2-id="0">
                              <?php foreach ($products as $readProducts): ?>
                                <option value="<?php echo $readProducts["id"]; ?>"><?php echo $readProducts["name"]; ?></option>
                              <?php endforeach; ?>
                            </optgroup>
                          <?php endif; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputAmount" class="col-sm-2 col-form-label">Adet:</label>
                    <div class="col-sm-10">
                      <input type="number" id="inputAmount" class="form-control" name="amount" placeholder="Gönderilcek eşya adetini giriniz." value="1">
                    </div>
                  </div>
                  <?php echo $csrf->input('sendChestItem'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <button type="submit" class="btn btn-rounded btn-success" name="sendChestItem">Eşya Gönder</button>
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
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'discount'): ?>
  <?php if (get("action") == 'update'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Toplu İndirim</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Mağaza</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Toplu İndirim</li>
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
            if (isset($_POST["updateDiscount"])) {
              if (post("storeDiscountStatus") == 0) {
                $_POST["storeDiscount"] = 0;
              }
              if (post("storeDiscountDurationStatus") == 0) {
                $_POST["storeDiscountDuration"] = '1000-01-01 00:00:00';
              }
              else {
                $_POST["storeDiscountDuration"] = date("Y-m-d H:i:s", strtotime($_POST["storeDiscountDuration"]));
              }
              if (post("storeDiscountProductStatus") == 0) {
                $_POST["storeDiscountProducts"] = 0;
              }
              if (!$csrf->validate('updateDiscount')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("storeDiscountStatus") == null || post("storeDiscount") == null || post("storeDiscountDurationStatus") == null || post("storeDiscountDuration") == null || post("storeDiscountProductStatus") == null || post("storeDiscountProducts") == null) {
                echo alertError("Lütfen boş alan bırakmayınız!");
              }
              else {
                $updateSettings = $db->prepare("UPDATE Settings SET storeDiscount = ?, storeDiscountExpiryDate = ?, storeDiscountProducts = ? WHERE id = ?");
                $updateSettings->execute(array(post("storeDiscount"), post("storeDiscountDuration"), post("storeDiscountProducts"), $readSettings["id"]));
                echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="selectStoreDiscountStatus" class="col-sm-2 col-form-label">İndirim Durumu:</label>
                  <div class="col-sm-10">
                    <select id="selectStoreDiscountStatus" class="form-control" name="storeDiscountStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["storeDiscount"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["storeDiscount"] != 0) ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div id="storeDiscountBlock" style="<?php echo ($readSettings["storeDiscount"] == 0) ? "display: none;" : "display: block;"; ?>">
                  <div class="form-group row">
                    <label for="inputStoreDiscount" class="col-sm-2 col-form-label">İndirim (%):</label>
                    <div class="col-sm-10">
                      <div class="input-group input-group-merge">
                        <input type="number" id="inputStoreDiscount" class="form-control form-control-prepended" name="storeDiscount" aria-describedby="storeDiscountHelp" min="1" max="100" placeholder="Ürünlere uygulanacak indirim yüzdesini yazınız." value="<?php echo ($readSettings["storeDiscount"] > 0) ? $readSettings["storeDiscount"] : null; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-percent"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectStoreDiscountDurationStatus" class="col-sm-2 col-form-label">Süre:</label>
                    <div class="col-sm-10">
                      <select id="selectStoreDiscountDurationStatus" class="form-control" name="storeDiscountDurationStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readSettings["storeDiscountExpiryDate"] == '1000-01-01 00:00:00') ? 'selected="selected"' : null; ?>>Süresiz</option>
                        <option value="1" <?php echo ($readSettings["storeDiscountExpiryDate"] != '1000-01-01 00:00:00') ? 'selected="selected"' : null; ?>>Süreli</option>
                      </select>
                    </div>
                  </div>
                  <div id="storeDiscountDurationBlock" class="form-group row" style="<?php echo ($readSettings["storeDiscountExpiryDate"] == '1000-01-01 00:00:00') ? "display: none;" : "display: block;"; ?>">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="input-group input-group-merge">
                        <input type="text" id="inputStoreDiscountDuration" class="form-control form-control-prepended" name="storeDiscountDuration" placeholder="İndirim son kullanım tarihini seçiniz." data-toggle="flatpickr" data-expirydate="true" value="<?php echo ($readSettings["storeDiscountExpiryDate"] != '1000-01-01 00:00:00') ? $readSettings["storeDiscountExpiryDate"] : null; ?>">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <span class="fe fe-clock"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectStoreDiscountProductsStatus" class="col-sm-2 col-form-label">Ürünler:</label>
                    <div class="col-sm-10">
                      <select id="selectStoreDiscountProductsStatus" class="form-control" name="storeDiscountProductStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readSettings["storeDiscountProducts"] == 0) ? 'selected="selected"' : null; ?>>Tümü</option>
                        <option value="1" <?php echo ($readSettings["storeDiscountProducts"] != 0) ? 'selected="selected"' : null; ?>>Seçili Ürünler</option>
                      </select>
                    </div>
                  </div>
                  <div id="storeDiscountProductsBlock" class="form-group row" style="<?php echo ($readSettings["storeDiscountProducts"] == 0) ? "display: none;" : "display: block;"; ?>">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="form-group">
                        <div class="input-group input-group-merge">
                          <input type="text" class="form-control form-control-prepended" data-toggle="jstree-search" placeholder="Ürün Ara">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <span class="fe fe-search"></span>
                            </div>
                          </div>
                        </div>
                        <input type="hidden" data-toggle="jstree-value" name="storeDiscountProducts" value="<?php echo ($readSettings["storeDiscountProducts"] != 0) ? $readSettings["storeDiscountProducts"] : null; ?>">
                      </div>
                      <div data-toggle="jstree" json="/apps/dashboard/public/ajax/products.php"></div>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('updateDiscount'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="updateDiscount">Değişiklikleri Kaydet</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
<?php elseif (get("target") == 'chest-history'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Sandık Geçmişi</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Mağaza</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Sandık Geçmişi</li>
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

            $chestsHistory = $db->query("SELECT CH.id FROM ChestsHistory CH INNER JOIN Accounts A ON CH.accountID = A.id INNER JOIN Chests C ON CH.chestID = C.id INNER JOIN Products P ON C.productID = P.id INNER JOIN Servers S ON P.serverID = S.id");
            $itemsCount = $chestsHistory->rowCount();
            $pageCount = ceil($itemsCount/$limit);
            if ($page > $pageCount) {
              $page = 1;
            }
            $visibleItemsCount = $page * $limit - $limit;
            $chestsHistory = $db->query("SELECT CH.*, A.realname, P.name as productName, S.name as serverName FROM ChestsHistory CH INNER JOIN Accounts A ON CH.accountID = A.id INNER JOIN Chests C ON CH.chestID = C.id INNER JOIN Products P ON C.productID = P.id INNER JOIN Servers S ON P.serverID = S.id ORDER BY CH.id DESC LIMIT $visibleItemsCount, $limit");

            if (isset($_POST["query"])) {
              if (post("query") != null) {
                $chestsHistory = $db->prepare("SELECT CH.*, A.realname, P.name as productName, S.name as serverName FROM ChestsHistory CH INNER JOIN Accounts A ON CH.accountID = A.id INNER JOIN Chests C ON CH.chestID = C.id INNER JOIN Products P ON C.productID = P.id INNER JOIN Servers S ON P.serverID = S.id WHERE A.realname LIKE :search ORDER BY CH.id DESC");
                $chestsHistory->execute(array(
                  "search" => '%'.post("query").'%'
                ));
              }
            }
          ?>
          <?php if ($chestsHistory->rowCount() > 0): ?>
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
                        <th>Sunucu</th>
                        <th class="text-center">İşlem</th>
                        <th>Tarih</th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($chestsHistory as $readChestsHistory): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            #<?php echo $readChestsHistory["id"]; ?>
                          </td>
                          <td>
                            <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readChestsHistory["accountID"]; ?>">
                              <?php echo $readChestsHistory["realname"]; ?>
                            </a>
                          </td>
                          <td>
                            <?php echo $readChestsHistory["productName"]; ?>
                          </td>
                          <td>
                            <?php echo $readChestsHistory["serverName"]; ?>
                          </td>
                          <td class="text-center">
                            <?php if ($readChestsHistory["type"] == 1): ?>
                              <i class="fa fa-check" data-toggle="tooltip" data-placement="top" title="Teslim"></i>
                            <?php elseif ($readChestsHistory["type"] == 2): ?>
                              <i class="fa fa-gift" data-toggle="tooltip" data-placement="top" title="Hediye (Gönderen)"></i>
                            <?php elseif ($readChestsHistory["type"] == 3): ?>
                              <i class="fa fa-gift" data-toggle="tooltip" data-placement="top" title="Hediye (Alan)"></i>
                            <?php else: ?>
                              <i class="fa fa-check"></i>
                            <?php endif; ?>
                          </td>
                          <td>
                            <?php echo convertTime($readChestsHistory["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/magaza/sandik-gecmisi/sil/<?php echo $readChestsHistory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
                    <a class="page-link" href="/yonetim-paneli/magaza/sandik-gecmisi/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/yonetim-paneli/magaza/sandik-gecmisi/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/yonetim-paneli/magaza/sandik-gecmisi/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
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
      $deleteChestsHistory = $db->prepare("DELETE FROM ChestsHistory WHERE id = ?");
      $deleteChestsHistory->execute(array(get("id")));
      go("/yonetim-paneli/magaza/sandik-gecmisi");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'coupon-history'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Kupon Geçmişi</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Mağaza</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Kupon Geçmişi</li>
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

            $productCouponsHistory = $db->query("SELECT PCH.id FROM ProductCouponsHistory PCH INNER JOIN Accounts A ON PCH.accountID = A.id INNER JOIN ProductCoupons PC ON PCH.couponID = PC.id");
            $itemsCount = $productCouponsHistory->rowCount();
            $pageCount = ceil($itemsCount/$limit);
            if ($page > $pageCount) {
              $page = 1;
            }
            $visibleItemsCount = $page * $limit - $limit;
            $productCouponsHistory = $db->query("SELECT PCH.*, A.realname, PC.name as couponName FROM ProductCouponsHistory PCH INNER JOIN Accounts A ON PCH.accountID = A.id INNER JOIN ProductCoupons PC ON PCH.couponID = PC.id ORDER BY PCH.id DESC LIMIT $visibleItemsCount, $limit");

            if (isset($_POST["query"])) {
              if (post("query") != null) {
                $productCouponsHistory = $db->prepare("SELECT PCH.*, A.realname, PC.name as couponName FROM ProductCouponsHistory PCH INNER JOIN Accounts A ON PCH.accountID = A.id INNER JOIN ProductCoupons PC ON PCH.couponID = PC.id WHERE A.realname LIKE :search ORDER BY PCH.id DESC");
                $productCouponsHistory->execute(array(
                  "search" => '%'.post("query").'%'
                ));
              }
            }
          ?>
          <?php if ($productCouponsHistory->rowCount() > 0): ?>
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
                        <th>Kupon</th>
                        <th>Tarih</th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($productCouponsHistory as $readProductCouponsHistory): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            #<?php echo $readProductCouponsHistory["id"]; ?>
                          </td>
                          <td>
                            <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readProductCouponsHistory["accountID"]; ?>">
                              <?php echo $readProductCouponsHistory["realname"]; ?>
                            </a>
                          </td>
                          <td>
                            <?php echo $readProductCouponsHistory["couponName"]; ?>
                          </td>
                          <td>
                            <?php echo convertTime($readProductCouponsHistory["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/magaza/kupon-gecmisi/sil/<?php echo $readProductCouponsHistory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
                    <a class="page-link" href="/yonetim-paneli/magaza/kupon-gecmisi/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/yonetim-paneli/magaza/kupon-gecmisi/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/yonetim-paneli/magaza/kupon-gecmisi/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
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
        $deleteProductCouponsHistory = $db->prepare("DELETE FROM ProductCouponsHistory WHERE id = ?");
        $deleteProductCouponsHistory->execute(array(get("id")));
        go("/yonetim-paneli/magaza/kupon-gecmisi");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'credit-charge-history'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php echo $readSettings["creditText"] ?> Yükleme Geçmişi</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Mağaza</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo $readSettings["creditText"] ?> Yükleme Geçmişi</li>
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

            $creditHistory = $db->prepare("SELECT CH.id FROM CreditHistory CH INNER JOIN Accounts A ON CH.accountID = A.id WHERE CH.type IN (?, ?) AND CH.paymentStatus = ?");
            $creditHistory->execute(array(1, 2, 1));
            $itemsCount = $creditHistory->rowCount();
            $pageCount = ceil($itemsCount/$limit);
            if ($page > $pageCount) {
              $page = 1;
            }
            $visibleItemsCount = $page * $limit - $limit;
            $creditHistory = $db->prepare("SELECT CH.*, A.realname FROM CreditHistory CH INNER JOIN Accounts A ON CH.accountID = A.id WHERE CH.type IN (?, ?) AND CH.paymentStatus = ? ORDER BY CH.id DESC LIMIT $visibleItemsCount, $limit");
            $creditHistory->execute(array(1, 2, 1));

            if (isset($_POST["query"])) {
              if (post("query") != null) {
                $creditHistory = $db->prepare("SELECT CH.*, A.realname FROM CreditHistory CH INNER JOIN Accounts A ON CH.accountID = A.id WHERE A.realname LIKE :search AND CH.type IN (:mobileType, :creditCardType) AND CH.paymentStatus = :paymentStatus ORDER BY CH.id DESC");
                $creditHistory->execute(array(
                  "search"          => '%'.post("query").'%',
                  "paymentStatus"   => 1,
                  "mobileType"      => 1,
                  "creditCardType"  => 2
                ));
              }
            }
          ?>
          <?php if ($creditHistory->rowCount() > 0): ?>
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
                        <th class="text-center">Miktar</th>
                        <th class="text-center">Kazanç</th>
                        <th class="text-center">Ödeme</th>
                        <th class="text-center">Altyapı</th>
                        <th>Tarih</th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($creditHistory as $readCreditHistory): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            #<?php echo $readCreditHistory["id"]; ?>
                          </td>
                          <td>
                            <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readCreditHistory["accountID"]; ?>">
                              <?php echo $readCreditHistory["realname"]; ?>
                            </a>
                          </td>
                          <td class="text-center">
                            <?php echo ($readCreditHistory["type"] == 3 || $readCreditHistory["type"] == 5) ? '<span class="text-danger">-'.$readCreditHistory["price"].'</span>' : '<span class="text-success">+'.$readCreditHistory["price"].'</span>'; ?>
                          </td>
                          <td class="text-center">
                            <?php echo $readCreditHistory["earnings"]; ?>
                          </td>
                          <td class="text-center">
                            <?php if ($readCreditHistory["type"] == 1): ?>
                              <i class="fa fa-mobile" data-toggle="tooltip" data-placement="top" title="Mobil Ödeme"></i>
                            <?php elseif ($readCreditHistory["type"] == 2): ?>
                              <i class="fa fa-credit-card" data-toggle="tooltip" data-placement="top" title="Kredi Kartı Ödeme"></i>
                            <?php elseif ($readCreditHistory["type"] == 3): ?>
                              <i class="fa fa-paper-plane" data-toggle="tooltip" data-placement="top" title="Gönderim (Gönderen)"></i>
                            <?php elseif ($readCreditHistory["type"] == 4): ?>
                              <i class="fa fa-paper-plane" data-toggle="tooltip" data-placement="top" title="Gönderim (Alan)"></i>
                            <?php elseif ($readCreditHistory["type"] == 5): ?>
                              <i class="fa fa-ticket" data-toggle="tooltip" data-placement="top" title="Çarkıfelek (Bilet)"></i>
                            <?php elseif ($readCreditHistory["type"] == 6): ?>
                              <i class="fa fa-ticket" data-toggle="tooltip" data-placement="top" title="Çarkıfelek (Kazanç)"></i>
                            <?php else: ?>
                              <i class="fa fa-paper-plane"></i>
                            <?php endif; ?>
                          </td>
                          <td class="text-center">
                            <?php
                              $paymentAPI = $db->prepare("SELECT name FROM PaymentSettings WHERE slug = ?");
                              $paymentAPI->execute(array($readCreditHistory["paymentAPI"]));
                              $readPaymentAPI = $paymentAPI->fetch();

                              if ($paymentAPI->rowCount() > 0) {
                                echo $readPaymentAPI["name"];
                              }
                              else {
                                echo "Diğer";
                              }
                            ?>
                          </td>
                          <td>
                            <?php echo convertTime($readCreditHistory["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/magaza/kredi-yukleme-gecmisi/sil/<?php echo $readCreditHistory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
                    <a class="page-link" href="/yonetim-paneli/magaza/kredi-yukleme-gecmisi/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/yonetim-paneli/magaza/kredi-yukleme-gecmisi/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/yonetim-paneli/magaza/kredi-yukleme-gecmisi/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
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
      $deleteCreditHistory = $db->prepare("DELETE FROM CreditHistory WHERE id = ?");
      $deleteCreditHistory->execute(array(get("id")));
      go("/yonetim-paneli/magaza/kredi-yukleme-gecmisi");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'credit-usage-history'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title"><?php echo $readSettings["creditText"] ?> Kullanım Geçmişi</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Mağaza</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo $readSettings["creditText"] ?> Kullanım Geçmişi</li>
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

            $creditHistory = $db->prepare("SELECT CH.id FROM CreditHistory CH INNER JOIN Accounts A ON CH.accountID = A.id WHERE CH.type != ? AND CH.type != ? AND CH.paymentStatus = ?");
            $creditHistory->execute(array(1, 2, 1));
            $itemsCount = $creditHistory->rowCount();
            $pageCount = ceil($itemsCount/$limit);
            if ($page > $pageCount) {
              $page = 1;
            }
            $visibleItemsCount = $page * $limit - $limit;
            $creditHistory = $db->prepare("SELECT CH.*, A.realname FROM CreditHistory CH INNER JOIN Accounts A ON CH.accountID = A.id WHERE CH.type != ? AND CH.type != ? AND CH.paymentStatus = ? ORDER BY CH.id DESC LIMIT $visibleItemsCount, $limit");
            $creditHistory->execute(array(1, 2, 1));

            if (isset($_POST["query"])) {
              if (post("query") != null) {
                $creditHistory = $db->prepare("SELECT CH.*, A.realname FROM CreditHistory CH INNER JOIN Accounts A ON CH.accountID = A.id WHERE A.realname LIKE :search AND CH.type != :mobileType AND CH.type != :creditCardType AND CH.paymentStatus = :paymentStatus ORDER BY CH.id DESC");
                $creditHistory->execute(array(
                  "search"        => '%'.post("query").'%',
                  "paymentStatus" => 1,
                  "mobileType"      => 1,
                  "creditCardType"  => 2
                ));
              }
            }
          ?>
          <?php if ($creditHistory->rowCount() > 0): ?>
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
                        <th class="text-center">Miktar</th>
                        <th class="text-center">Ödeme</th>
                        <th>Tarih</th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($creditHistory as $readCreditHistory): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            #<?php echo $readCreditHistory["id"]; ?>
                          </td>
                          <td>
                            <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readCreditHistory["accountID"]; ?>">
                              <?php echo $readCreditHistory["realname"]; ?>
                            </a>
                          </td>
                          <td class="text-center">
                            <?php echo ($readCreditHistory["type"] == 3 || $readCreditHistory["type"] == 5) ? '<span class="text-danger">-'.$readCreditHistory["price"].'</span>' : '<span class="text-success">+'.$readCreditHistory["price"].'</span>'; ?>
                          </td>
                          <td class="text-center">
                            <?php if ($readCreditHistory["type"] == 1): ?>
                              <i class="fa fa-mobile" data-toggle="tooltip" data-placement="top" title="Mobil Ödeme"></i>
                            <?php elseif ($readCreditHistory["type"] == 2): ?>
                              <i class="fa fa-credit-card" data-toggle="tooltip" data-placement="top" title="Kredi Kartı Ödeme"></i>
                            <?php elseif ($readCreditHistory["type"] == 3): ?>
                              <i class="fa fa-paper-plane" data-toggle="tooltip" data-placement="top" title="Gönderim (Gönderen)"></i>
                            <?php elseif ($readCreditHistory["type"] == 4): ?>
                              <i class="fa fa-paper-plane" data-toggle="tooltip" data-placement="top" title="Gönderim (Alan)"></i>
                            <?php elseif ($readCreditHistory["type"] == 5): ?>
                              <i class="fa fa-ticket" data-toggle="tooltip" data-placement="top" title="Çarkıfelek (Bilet)"></i>
                            <?php elseif ($readCreditHistory["type"] == 6): ?>
                              <i class="fa fa-ticket" data-toggle="tooltip" data-placement="top" title="Çarkıfelek (Kazanç)"></i>
                            <?php else: ?>
                              <i class="fa fa-paper-plane"></i>
                            <?php endif; ?>
                          </td>
                          <td>
                            <?php echo convertTime($readCreditHistory["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/magaza/kredi-kullanim-gecmisi/sil/<?php echo $readCreditHistory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
                    <a class="page-link" href="/yonetim-paneli/magaza/kredi-kullanim-gecmisi/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/yonetim-paneli/magaza/kredi-kullanim-gecmisi/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/yonetim-paneli/magaza/kredi-kullanim-gecmisi/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
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
      $deleteCreditHistory = $db->prepare("DELETE FROM CreditHistory WHERE id = ?");
      $deleteCreditHistory->execute(array(get("id")));
      go("/yonetim-paneli/magaza/kredi-kullanim-gecmisi");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'store-history'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Mağaza Geçmişi</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Mağaza</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Mağaza Geçmişi</li>
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

            $storeHistory = $db->query("SELECT id FROM Orders");
            $itemsCount = $storeHistory->rowCount();
            $pageCount = ceil($itemsCount/$limit);
            if ($page > $pageCount) {
              $page = 1;
            }
            $visibleItemsCount = $page * $limit - $limit;
            $storeHistory = $db->query("SELECT O.*, A.realname FROM Orders O INNER JOIN Accounts A ON O.accountID = A.id ORDER BY O.id DESC LIMIT $visibleItemsCount, $limit");

            if (isset($_POST["query"])) {
              if (post("query") != null) {
                $storeHistory = $db->prepare("SELECT O.*, A.realname FROM Orders O INNER JOIN Accounts A ON O.accountID = A.id WHERE A.realname LIKE :search ORDER BY O.id DESC");
                $storeHistory->execute(array(
                  "search" => '%'.post("query").'%'
                ));
              }
            }
          ?>
          <?php if ($storeHistory->rowCount() > 0): ?>
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
                        <th>Ödenen Tutar</th>
                        <th>Tarih</th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($storeHistory as $readStoreHistory): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            #<?php echo $readStoreHistory["id"]; ?>
                          </td>
                          <td>
                            <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readStoreHistory["realname"]; ?>">
                              <?php echo $readStoreHistory["realname"]; ?>
                            </a>
                          </td>
                          <td><?php echo $readStoreHistory["subtotal"]." ".$readSettings["creditText"]; ?></td>
                          <td><?php echo convertTime($readStoreHistory["creationDate"], 2, true); ?></td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/yonetim-paneli/magaza/magaza-gecmisi/goruntule/<?php echo $readStoreHistory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/magaza/magaza-gecmisi/sil/<?php echo $readStoreHistory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
                    <a class="page-link" href="/yonetim-paneli/magaza/magaza-gecmisi/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/yonetim-paneli/magaza/magaza-gecmisi/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/yonetim-paneli/magaza/magaza-gecmisi/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
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
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Sipariş Görüntüle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/urun">Mağaza</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/magaza/magaza-gecmisi">Mağaza Geçmişi</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Görüntüle</li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <?php
          $order = $db->prepare("SELECT * FROM Orders WHERE id = ?");
          $order->execute(array(get("id")));
          $readOrder = $order->fetch();
        ?>
        <?php if ($order->rowCount() > 0): ?>
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                Sipariş #<?php echo $readOrder["id"]; ?>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                    <tr>
                      <th>Ürün</th>
                      <th>Adet</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                      $orderProducts = $db->prepare("SELECT P.name, OP.quantity FROM OrderProducts OP INNER JOIN Products P ON OP.productID = P.id WHERE OP.orderID = ?");
                      $orderProducts->execute(array($readOrder["id"]));
                    ?>
                    <?php foreach ($orderProducts as $readOrderProducts): ?>
                      <tr>
                        <td><?php echo $readOrderProducts["name"]; ?></td>
                        <td><?php echo $readOrderProducts["quantity"]; ?></td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                Sipariş Özeti
              </div>
              <div class="card-body">
                <?php if ($readOrder["coupon"] != null): ?>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="input-group">
                        <input type="text" class="form-control" name="coupon" disabled readonly value="<?php echo $readOrder["coupon"]; ?>">
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
                <div class="row pt-4">
                  <div class="col">
                    <span class="font-weight-bold">İndirim:</span>
                  </div>
                  <div class="col-auto text-right">
                    <span class="text-danger">
                      -<?php echo $readOrder["discount"]." ".$readSettings["creditText"]; ?>
                    </span>
                  </div>
                </div>
                <div class="row pt-4">
                  <div class="col">
                    <span class="font-weight-bold">Ödenen Tutar:</span>
                  </div>
                  <div class="col-auto text-right">
                    <span class="text-success">
                      <?php echo $readOrder["subtotal"]." ".$readSettings["creditText"]; ?>
                    </span>
                  </div>
                </div>
                <div class="row pt-4">
                  <div class="col">
                    <span class="font-weight-bold">İşlem Tarihi:</span>
                  </div>
                  <div class="col-auto text-right">
                    <?php echo convertTime($readOrder["creationDate"], 2, true); ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="col-md-12">
            <?php echo alertError("Sipariş bulunamadı!"); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteStoreHistory = $db->prepare("DELETE FROM Orders WHERE id = ?");
      $deleteStoreHistory->execute(array(get("id")));
      go("/yonetim-paneli/magaza/magaza-gecmisi");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
