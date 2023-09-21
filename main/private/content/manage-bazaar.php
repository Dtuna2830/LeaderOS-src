<?php
  if (!isset($_SESSION["login"])) {
    go("/giris-yap");
  }
  if ($readSettings["bazaarStatus"] == 0) {
    go("/404");
  }
  require_once(__ROOT__.'/apps/main/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
?>
<section class="section credit-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Ana Sayfa</a></li>
            <?php if (get("action") == 'getAll'): ?>
              <li class="breadcrumb-item active" aria-current="page">Pazar Deposu</li>
            <?php elseif (get("action") == 'sell'): ?>
              <li class="breadcrumb-item"><a href="/pazar-satis">Pazar Deposu</a></li>
              <li class="breadcrumb-item active" aria-current="page">Sat</li>
            <?php elseif (get("action") == 'help'): ?>
              <li class="breadcrumb-item"><a href="/pazar">Pazar</a></li>
              <li class="breadcrumb-item active" aria-current="page">Yardım</li>
            <?php else: ?>
              <li class="breadcrumb-item active" aria-current="page">Hata!</li>
            <?php endif; ?>
          </ol>
        </nav>
      </div>
      <?php if (get("action") == 'getAll'): ?>
        <div class="col-md-8">
          <?php if ($readSettings["bazaarCommission"] > 0): ?>
            <?php echo alertWarning('Satışlarınızdan %'. $readSettings["bazaarCommission"] .' alınmaktadır!'); ?>
          <?php endif; ?>
          <?php
            $items = $db->prepare("SELECT BI.*, S.name as serverName FROM BazaarItems BI INNER JOIN Servers S ON S.id = BI.serverID WHERE BI.owner = ? ORDER BY BI.id DESC");
            $items->execute(array($readAccount["id"]));
          ?>
          <?php if ($items->rowCount() > 0): ?>
            <div class="card">
              <div class="card-header">
                <div class="row">
                  <div class="col">Pazar Deposu</div>
                  <div class="col-auto">
                    <a href="/pazar-yardim" class="text-white">Pazar Yardım?</a>
                  </div>
                </div>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                    <tr>
                      <th class="text-center" style="width: 40px;">#ID</th>
                      <th>Ürün</th>
                      <th>Sunucu</th>
                      <th>Fiyat</th>
                      <th>Durum</th>
                      <th>Tarih</th>
                      <th class="text-right">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $readItems): ?>
                      <tr>
                        <td class="text-center" style="width: 40px;">
                          #<?php echo $readItems["id"]; ?>
                        </td>
                        <td>
                          <?php echo $readItems["name"]; ?>
                        </td>
                        <td>
                          <?php echo $readItems["serverName"]; ?>
                        </td>
                        <td>
                          <?php echo $readItems["price"] == 0 ? "Satışta Değil" : $readItems["price"]; ?>
                        </td>
                        <td>
                          <?php if ($readItems["sold"] == 0): ?>
                            <?php if ($readItems["price"] == 0): ?>
                              <span class="badge badge-pill badge-warning">Depoda</span>
                            <?php else: ?>
                              <span class="badge badge-pill badge-success">Satışta</span>
                            <?php endif; ?>
                          <?php else: ?>
                            <span class="badge badge-pill badge-danger">Satıldı</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php echo convertTime($readItems["creationDate"], 2, true); ?>
                        </td>
                        <td class="text-right">
                          <a class="btn btn-success btn-circle" href="/pazar-satis/<?php echo $readItems["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                            <i class="fa fa-pen"></i>
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
            <?php echo alertError("Deponuzda herhangi bir ürün bulunamadı!"); ?>
          <?php endif; ?>
        </div>
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-12">
              <?php
                $bazaarHistory = $db->prepare("SELECT BH.*, BI.name as itemName, BI.price as itemPrice FROM BazaarHistory BH INNER JOIN BazaarItems BI ON BH.itemID = BI.id WHERE BH.accountID = ? ORDER BY BH.id DESC LIMIT 5");
                $bazaarHistory->execute(array($readAccount["id"]));
              ?>
              <?php if ($bazaarHistory->rowCount() > 0): ?>
                <div class="card">
                  <div class="card-header">
                    <div class="row">
                      <div class="col">
                        <span>Pazar Geçmişi</span>
                      </div>
                      <div class="col-auto">
                        <a class="text-white" href="/profil">Tümü</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body p-0">
                    <div class="table-responsive">
                      <table class="table table-hover">
                        <thead>
                        <tr>
                          <th>Ürün</th>
                          <th class="text-center">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($bazaarHistory as $readBazaarHistory): ?>
                          <tr>
                            <td><?php echo $readBazaarHistory["itemName"]; ?></td>
                            <td class="text-center">
                              <?php if ($readBazaarHistory["type"] == 0): ?>
                                <span class="text-danger" data-toggle="tooltip" data-placement="top" title="Satın Alındı">-<?php echo $readBazaarHistory["itemPrice"] ?> <i class="fa fa-coins"></i></span>
                              <?php elseif ($readBazaarHistory["type"] == 1): ?>
                                <span class="text-success" data-toggle="tooltip" data-placement="top" title="Satıldı">+<?php echo $readBazaarHistory["itemPrice"] ?> <i class="fa fa-coins"></i></span>
                              <?php else: ?>
                                <i class="fa fa-check"></i>
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              <?php else: ?>
                <?php echo alertError("Pazar geçmişi bulunamadı!"); ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php elseif (get("action") == 'sell' && get("id")): ?>
        <?php
          $item = $db->prepare("SELECT BI.*, S.name as serverName FROM BazaarItems BI INNER JOIN Servers S ON S.id = BI.serverID WHERE BI.owner = ? AND BI.id = ?");
          $item->execute(array($readAccount["id"], get("id")));
          $readItem = $item->fetch();
        ?>
        <?php if ($item->rowCount() > 0): ?>
          <div class="col-md-4">
            <div class="card">
              <div class="text-center py-3 bg-light">
                <img width="64px" src="/apps/main/public/assets/img/items/<?php echo strtolower($readItem["itemID"]).".png"; ?>" />
              </div>
              <div class="card-body">
                <div class="form-group">
                  <strong>Ürün Adı:</strong>
                  <div>
                    <input type="text" class="form-control-plaintext" value="<?php echo $readItem["name"]; ?>" readonly>
                  </div>
                </div>
                <div class="form-group">
                  <strong>Adet:</strong>
                  <div>
                    <?php echo $readItem["amount"]; ?>
                  </div>
                </div>
                <div class="form-group">
                  <strong>Dayanıklık:</strong>
                  <div>
                    <?php echo ($readItem["durability"] > $readItem["maxDurability"] ? $readItem["maxDurability"] : $readItem["durability"])."/".$readItem["maxDurability"]; ?>
                  </div>
                </div>
                <div class="form-group">
                  <strong>Sunucu:</strong>
                  <div>
                    <?php echo $readItem["serverName"]; ?>
                  </div>
                </div>
                <?php if ($readItem["lore"] != null && $readItem["lore"] != ""): ?>
                  <div class="form-group">
                    <strong>Lore:</strong>
                    <div>
                      <?php echo str_replace("\n", "<br>", $readItem["lore"]); ?>
                    </div>
                  </div>
                <?php endif; ?>
                <?php if ($readItem["enchantments"] != null && $readItem["enchantments"] != ""): ?>
                  <div class="form-group">
                    <strong>Büyüler:</strong>
                    <div>
                      <?php
                        $enchantments = $readItem["enchantments"];
                        $enchantments = explode(",", $enchantments);
                        foreach ($enchantments as $enchantment) {
                          $enchantment = explode(":", $enchantment);
                          echo "* Lvl. ".$enchantment[1]." - ".$enchantment[0]."<br>";
                        }
                      ?>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="col-md-8">
            <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["sellItem"]) || isset($_POST["removeItem"])) {
              if (!$csrf->validate('sellItem')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("price") == null) {
                echo alertError("Lütfen boş alan bırakmayınız!");
              }
              else if (post("price") <= 0) {
                echo alertError("Geçerli bir fiyat yazınız!");
              }
              else if ($readItem["sold"] == 1) {
                echo alertError("Satılan bir ürünü düzenleyemezsiniz!");
              }
              else {
                if (isset($_POST["sellItem"])) {
                  $oldPrice = $readItem["price"];
                  $updateItem = $db->prepare("UPDATE BazaarItems SET price = ?, description = ? WHERE owner = ? AND id = ?");
                  $updateItem->execute(array(post("price"), post("description"), $readAccount["id"], $readItem["id"]));
                  if ($oldPrice == 0) {
                    echo alertSuccess("Ürününüz başarıyla pazarda satışa konuldu!");
                  }
                  else {
                    echo alertSuccess("Ürününüz başarıyla güncellendi!");
                  }
                }
                else {
                  $updateItem = $db->prepare("UPDATE BazaarItems SET price = ? WHERE id = ?");
                  $updateItem->execute(array(0, $readItem["id"]));
          
                  echo alertSuccess("Ürün pazardan kaldırıldı. Ürünü /sitepazar komutu ile geri alabilirsiniz.");
                }
              }
            }
            ?>
            <div class="card">
              <div class="card-header">
                Pazar Ürünü
              </div>
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputProduct" class="col-sm-2 col-form-label">Ürün:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputProduct" class="form-control-plaintext" value="<?php echo $readItem["name"]; ?>" readonly>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPrice" class="col-sm-2 col-form-label">Fiyat:</label>
                    <div class="col-sm-10">
                      <input type="number" name="price" id="inputPrice" class="form-control" value="<?php echo $readItem["price"] == 0 ? null : $readItem["price"]; ?>" placeholder="Kaç krediye satılacağını yazınız.">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputDesc" class="col-sm-2 col-form-label">Açıklama:</label>
                    <div class="col-sm-10">
                      <textarea name="description" id="inputDesc" class="form-control"><?php echo $readItem["description"]; ?></textarea>
                    </div>
                  </div>
                  <?php echo $csrf->input('sellItem'); ?>
                  <?php if ($readItem["sold"] == 0): ?>
                    <div class="clearfix">
                      <div class="float-right">
                        <?php if ($readItem["price"] > 0): ?>
                          <button type="submit" class="btn btn-rounded btn-danger" name="removeItem" onclick="return confirm('Bu ürünü pazardan kaldırmak istediğine emin misin?')">Pazardan Kaldır</button>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-rounded btn-success" name="sellItem">
                          <?php echo $readItem["price"] == 0 ? "Satışa Çıkar" : "Güncelle" ?>
                        </button>
                      </div>
                    </div>
                  <?php endif; ?>
                </form>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="col-md-12">
            <?php echo alertError("Pazar ürünü bulunamadı!"); ?>
          </div>
        <?php endif; ?>
      <?php elseif (get("action") == 'help'): ?>
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">Pazar Yardım</div>
            <div class="card-body">
              <h5>Nasıl Satış Yapılır?</h5>
              <p>Satmak istediğiniz ürünü oyundan <strong>/sitepazar</strong> yazın ve ürünü yerleştirin. Pazar deponuza eklediğiniz ürünü sitedeki Pazar Depsoundan görebilir ve satışa çıkarabilirsiniz.</p>
              <hr>
              <h5>Satın aldığım ürünü nasıl kullanırım?</h5>
              <p>Satın aldığınız ürünler pazar deponuza eklenir. Oyundan pazar deponuza erişmek için <strong>/sitepazar</strong> komutunu kullanabilirsiniz.</p>
              <hr>
              <h5>Satmaktan vazgeçtim ürünümü geri nasıl alırım?</h5>
              <p>Ürünü düzenleme ekranından <strong>"Pazardan Kaldır"</strong> butonuna tıklayın. Pazardan kaldırdığınız için artık oyundan <strong>/sitepazar</strong> komutunu kullanarak ürünü geri alabilirsiniz.</p>
            </div>
          </div>
        </div>
      <?php else: ?>
        <?php go("/404"); ?>
      <?php endif; ?>
    </div>
  </div>
</section>
