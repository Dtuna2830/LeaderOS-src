<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
  if (!isset($_SESSION["login"])) {
    die(false);
  }
?>
<?php if (get("action") == "buy"): ?>
  <?php
    $products = $db->prepare("SELECT * FROM CreditPackages WHERE id = ?");
    $products->execute(array(get("id")));
    $readProducts = $products->fetch();
  ?>
  <?php if ($products->rowCount() > 0): ?>
    <?php $discountedPriceStatus = ($readProducts["discountedPrice"] != 0 && ($readProducts["discountExpiryDate"] > date("Y-m-d H:i:s") || $readProducts["discountExpiryDate"] == '1000-01-01 00:00:00')); ?>
    <?php
      if ($discountedPriceStatus) {
        $productPrice = $readProducts["discountedPrice"];
      }
      else {
        $productPrice = $readProducts["price"];
      }
    ?>
    <?php
    require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
    $csrf = new CSRF('csrf-sessions', 'csrf-token');
    ?>
    <!-- Modal -->
    <div class="modal fade" id="buyModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="buyModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <div class="modal-title" id="buyModalLabel">Market <i class="fa fa-angle-double-right"></i> <?php echo $readProducts["name"]; ?></div>
            <button type="button" class="close" data-dismiss="modal" aria-label="Kapat">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="/apps/main/public/ajax/pay.php" method="post">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="title background mt-0"><span>Ürün Bilgisi</span></div>
                </div>
                <div class="col-4">
                  <div class="store-card">
                    <?php if ($readProducts["bonus"] != 0): ?>
                      <div class="store-card-stock have-stock bg-success">
                        +<?php echo $readProducts["bonus"]." Bonus ".$readSettings["creditText"] ?>
                      </div>
                    <?php else: ?>
                      <?php if ($readProducts["stock"] != -1): ?>
                        <div class="store-card-stock <?php echo ($readProducts["stock"] == 0) ? "stock-out" : "have-stock"; ?>">
                          <?php if ($readProducts["stock"] == 0): ?>
                            Stokta Yok!
                          <?php else : ?>
                            Sınırlı Stok!
                          <?php endif; ?>
                        </div>
                      <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($discountedPriceStatus): ?>
                      <?php $discountPercent = (round((($readProducts["price"]-$readProducts["discountedPrice"])*100)/($readProducts["price"]))); ?>
                      <div class="store-card-discount">
                        <span>%<?php echo $discountPercent; ?></span>
                      </div>
                    <?php endif; ?>
                    <img class="store-card-img" src="/apps/main/public/assets/img/store/products/<?php echo $readProducts["imageID"].'.'.$readProducts["imageType"]; ?>" alt="<?php echo $serverName." Ürün - ".$readProducts["name"]." Satın Al"; ?>">
                  </div>
                </div>
                <div class="col-8">
                  <div class="row">
                    <span class="col-sm-4 font-weight-bold">Ürün Adı:</span>
                    <span class="col-sm-8"><?php echo $readProducts["name"]; ?></span>
                  </div>
                  <div class="row">
                    <span class="col-sm-4 font-weight-bold">Fiyat:</span>
                    <span class="col-sm-8">
                      <?php echo str_replace('.00', '', $productPrice); ?>
                    </span>
                  </div>
                  <div class="row">
                    <span class="col-sm-4 font-weight-bold">Miktar:</span>
                    <span class="col-sm-8"><?php echo $readProducts["amount"]." ".$readSettings["creditText"]; ?></span>
                  </div>
                  <?php if ($readProducts["bonus"] != 0): ?>
                    <div class="row">
                      <span class="col-sm-4 font-weight-bold">Bonus:</span>
                      <span class="col-sm-8 text-success">+<?php echo $readProducts["bonus"]." ".$readSettings["creditText"]; ?></span>
                    </div>
                  <?php endif; ?>
                  <?php if ($readProducts["stock"] != -1): ?>
                    <div class="row">
                      <span class="col-sm-4 font-weight-bold">Stok:</span>
                      <span class="col-sm-8">
                        <?php if ($readProducts["stock"] == 0): ?>
                          <span class="text-danger">Yok</span>
                        <?php else : ?>
                          <span class="text-warning"><?php echo $readProducts["stock"]; ?> adet</span>
                        <?php endif; ?>
                      </span>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
              <div id="couponBox">
                <div class="row">
                  <div class="col-md-12">
                    <div class="title background"><span>Ürün Açıklaması</span></div>
                    <div class="product-details">
                      <?php echo $readProducts["details"]; ?>
                    </div>
                  </div>
                </div>
                <?php
                  $accountContactInfo = $db->prepare("SELECT * FROM AccountContactInfo WHERE accountID = ?");
                  $accountContactInfo->execute(array($readAccount["id"]));
                  $readAccountContactInfo = $accountContactInfo->fetch();
                ?>
                <div class="row">
                  <div class="col-md-12">
                    <div class="title background"><span>Müşteri Bilgileri</span></div>
                    <div>
                      <div class="form-row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <input type="text" class="form-control" id="firstName" name="firstName" placeholder="Ad" value="<?php echo (isset($readAccountContactInfo["firstName"])) ? $readAccountContactInfo["firstName"] : null; ?>" required>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Soyad" value="<?php echo (isset($readAccountContactInfo["lastName"])) ? $readAccountContactInfo["lastName"] : null; ?>" required>
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <input type="text" class="form-control" id="inputPhoneNumber" placeholder="Telefon Numarası" name="phoneNumber" required="required" value="<?php echo (isset($readAccountContactInfo["phoneNumber"])) ? $readAccountContactInfo["phoneNumber"] : null; ?>">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="title background"><span>Ödeme Yöntemi</span></div>
                    <div>
                      <select id="selectPayment" class="form-control" name="paymentID" required="required">
                        <?php
                          $payment = $db->prepare("SELECT P.* FROM Payment P INNER JOIN PaymentSettings PS ON P.apiID = PS.slug WHERE PS.status = ? ORDER BY P.id DESC");
                          $payment->execute(array(1));
                        ?>
                        <?php if ($payment->rowCount() > 0): ?>
                          <?php foreach ($payment as $readPayment): ?>
                            <option value="<?php echo $readPayment["id"]; ?>">
                              <?php echo $readPayment["title"]; ?>
                            </option>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <option>Ödeme yöntemi bulunamadı!</option>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row pt-4">
                  <div class="col">
                    <span class="font-weight-bold">Ödenecek Tutar:</span>
                  </div>
                  <div class="col-auto text-right">
                    <s id="oldPrice" class="text-danger" style="display: none;"></s>
                    <span id="newPrice" class="text-success" value="<?php echo $productPrice; ?>">
                      <?php echo str_replace('.00', '', $productPrice); ?> <i class="fa fa-lira-sign"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <?php echo $csrf->input('chargeCredit'); ?>
              <input type="hidden" name="price" value="<?php echo $readProducts["price"]; ?>">
              <input type="hidden" name="creditPackage" value="<?php echo $readProducts["id"]; ?>">
              <button type="submit" class="btn btn-rounded btn-success" name="chargeCredit">Satın Al</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php else : ?>
    <?php die(false); ?>
  <?php endif; ?>
<?php else : ?>
  <?php die(false); ?>
<?php endif; ?>
