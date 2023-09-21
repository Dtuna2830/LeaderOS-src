<?php
  if (!checkPerm($readAdmin, 'MANAGE_GAMING_NIGHT')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/gaming-night.js');
?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="header">
        <div class="header-body">
          <div class="row align-items-center">
            <div class="col">
              <h2 class="header-title">Gaming Gecesi</h2>
            </div>
            <div class="col-auto">
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Gaming Gecesi</li>
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
        if (isset($_POST["updateGamingNight"])) {
          if (!$csrf->validate('updateGamingNight')) {
            echo alertError("Sistemsel bir sorun oluştu!");
            echo goDelay("/yonetim-paneli/gaming-gecesi", 2);
          }
          else if (post("gamingNight") == null || post("gamingNightDay") == null || post("gamingNightStart") == null || post("gamingNightEnd") == null) {
            echo alertError("Lütfen boş alan bırakmayınız!");
          }
          else if (post("gamingNight") != "0" && (empty(array_filter($_POST["product"])) || empty(array_filter($_POST["discountedPrice"])) || empty(array_filter($_POST["stock"])))) {
            echo alertError("Lütfen boş alan bırakmayınız!");
          }
          else {
            $updateSettings = $db->prepare("UPDATE Settings SET gamingNight = ?, gamingNightDay = ?, gamingNightStart = ?, gamingNightEnd = ? WHERE id = ?");
            $updateSettings->execute(array(post("gamingNight"), post("gamingNightDay"), str_replace(':', '', post("gamingNightStart")), str_replace(':', '', post("gamingNightEnd")), $readSettings["id"]));
            
            $deleteGamingNightProducts = $db->query("TRUNCATE TABLE GamingNightProducts");
            foreach ($_POST["product"] as $key => $value) {
              $_POST["product"][$key] = strip_tags($_POST["product"][$key]);
              $_POST["discountedPrice"][$key] = strip_tags($_POST["discountedPrice"][$key]);
              $_POST["stock"][$key] = strip_tags($_POST["stock"][$key]);
              $insertGamingNightProducts = $db->prepare("INSERT INTO GamingNightProducts (productID, price, stock) VALUES (?, ?, ?)");
              $insertGamingNightProducts->execute(array($_POST["product"][$key], $_POST["discountedPrice"][$key], $_POST["stock"][$key]));
            }
            echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
            echo goDelay("/yonetim-paneli/gaming-gecesi", 2);
            createLog($readAdmin["id"], "GAMING_NIGHT_UPDATED");
          }
        }
      ?>
      <div class="card">
        <div class="card-body">
          <form action="" method="post">
            <div class="form-group row">
              <label for="selectStatus" class="col-sm-2 col-form-label">Gaming Gecesi:</label>
              <div class="col-sm-10">
                <select id="selectStatus" class="form-control" name="gamingNight" data-toggle="select" data-minimum-results-for-search="-1">
                  <option value="0" <?php echo ($readSettings["gamingNight"] == '0') ? 'selected="selected"' : null; ?>>Kapalı</option>
                  <option value="1" <?php echo ($readSettings["gamingNight"] != '0') ? 'selected="selected"' : null; ?>>Aktif</option>
                </select>
              </div>
            </div>
            <div id="settingsBlock">
              <div class="form-group row">
                <label for="selectDay" class="col-sm-2 col-form-label">Gün:</label>
                <div class="col-sm-10">
                  <select id="selectDay" class="form-control" name="gamingNightDay" data-toggle="select" data-minimum-results-for-search="-1">
                    <option value="Monday" <?php echo ($readSettings["gamingNightDay"] == 'Monday') ? 'selected="selected"' : null; ?>>Pazartesi</option>
                    <option value="Tuesday" <?php echo ($readSettings["gamingNightDay"] == 'Tuesday') ? 'selected="selected"' : null; ?>>Salı</option>
                    <option value="Wednesday" <?php echo ($readSettings["gamingNightDay"] == 'Wednesday') ? 'selected="selected"' : null; ?>>Çarşamba</option>
                    <option value="Thursday" <?php echo ($readSettings["gamingNightDay"] == 'Thursday') ? 'selected="selected"' : null; ?>>Perşembe</option>
                    <option value="Friday" <?php echo ($readSettings["gamingNightDay"] == 'Friday') ? 'selected="selected"' : null; ?>>Cuma</option>
                    <option value="Saturday" <?php echo ($readSettings["gamingNightDay"] == 'Saturday') ? 'selected="selected"' : null; ?>>Cumartesi</option>
                    <option value="Sunday" <?php echo ($readSettings["gamingNightDay"] == 'Sunday') ? 'selected="selected"' : null; ?>>Pazar</option>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label for="inputGamingNightStart" class="col-sm-2 col-form-label">Başlangıç Saati:</label>
                <div class="col-sm-10">
                  <?php $gamingNightStart = $readSettings["gamingNightStart"]; ?>
                  <input type="text" id="inputGamingNightStart" class="form-control" name="gamingNightStart" placeholder="Etkinliğin başlangıç saatini yazınız." value="<?php echo $gamingNightStart[0].$gamingNightStart[1].":".$gamingNightStart[2].$gamingNightStart[3]; ?>">
                </div>
              </div>
              <div class="form-group row">
                <label for="inputGamingNightEnd" class="col-sm-2 col-form-label">Bitiş Saati:</label>
                <div class="col-sm-10">
                  <?php $gamingNightEnd = $readSettings["gamingNightEnd"]; ?>
                  <input type="text" id="inputGamingNightEnd" class="form-control" name="gamingNightEnd" placeholder="Etkinliğin bitiş saatini yazınız." value="<?php echo $gamingNightEnd[0].$gamingNightEnd[1].":".$gamingNightEnd[2].$gamingNightEnd[3]; ?>">
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-12">
                  <span>Ürünler:</span>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-sm-12">
                  <div class="table-responsive">
                    <table id="tableitems" class="table table-sm table-nowrap array-table">
                      <thead>
                      <tr>
                        <th>Ürün</th>
                        <th>İndirimli Fiyat</th>
                        <th>Stok</th>
                        <th class="text-center pt-0 pb-0 align-middle">
                          <button type="button" class="btn btn-sm btn-rounded-circle btn-success addTableItem">
                            <i class="fe fe-plus"></i>
                          </button>
                        </th>
                      </tr>
                      </thead>
                      <tbody>
                      <?php
                        $gamingNightProducts = $db->query("SELECT P.id, P.name, P.price, GNP.price as discountedPrice, GNP.stock FROM GamingNightProducts GNP INNER JOIN Products P ON GNP.productID = P.id");
                      ?>
                      <?php if ($gamingNightProducts->rowCount() > 0): ?>
                        <?php foreach ($gamingNightProducts as $readGamingNightProducts): ?>
                          <tr>
                            <td>
                              <select class="form-control" name="product[]">
                                <?php $servers = $db->query("SELECT id, name FROM Servers ORDER BY id DESC"); ?>
                                <?php foreach ($servers as $readServers): ?>
                                  <optgroup label="<?php echo $readServers["name"]; ?>">
                                    <?php
                                      $productCategories = $db->prepare("SELECT id, name FROM ProductCategories WHERE serverID = ?");
                                      $productCategories->execute(array($readServers["id"]));
                                    ?>
                                    <?php foreach ($productCategories as $readProductCategories): ?>
                                      <optgroup label="&nbsp;&nbsp;<?php echo $readProductCategories["name"]; ?>">
                                        <?php
                                          $products = $db->prepare("SELECT id, name FROM Products WHERE serverID = ? AND categoryID = ?");
                                          $products->execute(array($readServers["id"], $readProductCategories["id"]));
                                        ?>
                                        <?php foreach ($products as $readProducts): ?>
                                          <option value="<?php echo $readProducts["id"]; ?>" <?php echo ($readGamingNightProducts["id"] == $readProducts["id"]) ? 'selected="selected"' : null; ?>>&nbsp;<?php echo $readProducts["name"]; ?></option>
                                        <?php endforeach; ?>
                                      </optgroup>
                                    <?php endforeach; ?>
                                    <?php
                                      $uncategorizedProducts = $db->prepare("SELECT id, name FROM Products WHERE serverID = ? AND categoryID = ?");
                                      $uncategorizedProducts->execute(array($readServers["id"], 0));
                                    ?>
                                    <?php if ($uncategorizedProducts->rowCount() > 0): ?>
                                      <optgroup label="&nbsp;&nbsp;Diğer">
                                        <?php foreach ($uncategorizedProducts as $readUncategorizedProducts): ?>
                                          <option value="<?php echo $readUncategorizedProducts["id"]; ?>" <?php echo ($readGamingNightProducts["id"] == $readUncategorizedProducts["id"]) ? 'selected="selected"' : null; ?>><?php echo $readUncategorizedProducts["name"]; ?></option>
                                        <?php endforeach; ?>
                                      </optgroup>
                                    <?php endif; ?>
                                  </optgroup>
                                <?php endforeach; ?>
                              </select>
                            </td>
                            <td>
                              <input type="text" class="form-control" name="discountedPrice[]" placeholder="Geceye özel fiyatı yazınız." value="<?php echo $readGamingNightProducts["discountedPrice"]; ?>">
                            </td>
                            <td>
                              <input type="text" class="form-control" name="stock[]" placeholder="Geceye özel stok adetini yazınız.." value="<?php echo $readGamingNightProducts["stock"]; ?>">
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
                            <select class="form-control" name="product[]">
                              <?php $servers = $db->query("SELECT id, name FROM Servers ORDER BY id DESC"); ?>
                              <?php foreach ($servers as $readServers): ?>
                                <optgroup label="<?php echo $readServers["name"]; ?>">
                                  <?php
                                    $productCategories = $db->prepare("SELECT id, name FROM ProductCategories WHERE serverID = ?");
                                    $productCategories->execute(array($readServers["id"]));
                                  ?>
                                  <?php foreach ($productCategories as $readProductCategories): ?>
                                    <optgroup label="&nbsp;&nbsp;<?php echo $readProductCategories["name"]; ?>">
                                      <?php
                                        $products = $db->prepare("SELECT id, name FROM Products WHERE serverID = ? AND categoryID = ?");
                                        $products->execute(array($readServers["id"], $readProductCategories["id"]));
                                      ?>
                                      <?php foreach ($products as $readProducts): ?>
                                        <option value="<?php echo $readProducts["id"]; ?>">&nbsp;<?php echo $readProducts["name"]; ?></option>
                                      <?php endforeach; ?>
                                    </optgroup>
                                  <?php endforeach; ?>
                                  <?php
                                    $uncategorizedProducts = $db->prepare("SELECT id, name FROM Products WHERE serverID = ? AND categoryID = ?");
                                    $uncategorizedProducts->execute(array($readServers["id"], 0));
                                  ?>
                                  <?php if ($uncategorizedProducts->rowCount() > 0): ?>
                                    <optgroup label="&nbsp;&nbsp;Diğer">
                                      <?php foreach ($uncategorizedProducts as $readUncategorizedProducts): ?>
                                        <option value="<?php echo $readUncategorizedProducts["id"]; ?>"><?php echo $readUncategorizedProducts["name"]; ?></option>
                                      <?php endforeach; ?>
                                    </optgroup>
                                  <?php endif; ?>
                                </optgroup>
                              <?php endforeach; ?>
                            </select>
                          </td>
                          <td>
                            <input type="text" class="form-control" name="discountedPrice[]" placeholder="Geceye özel fiyatı yazınız.">
                          </td>
                          <td>
                            <input type="text" class="form-control" name="stock[]" placeholder="Geceye özel stok adedini yazınız.">
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
            <?php echo $csrf->input('updateGamingNight'); ?>
            <div class="clearfix">
              <div class="float-right">
                <button type="submit" class="btn btn-rounded btn-success" name="updateGamingNight">Değişiklikleri Kaydet</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>