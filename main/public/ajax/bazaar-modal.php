<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
?>
<?php if ($readSettings["bazaarStatus"] == 1): ?>
  <?php if (get("action") == "buy"): ?>
    <?php
    $products = $db->prepare("SELECT BI.*, A.realname FROM BazaarItems BI INNER JOIN Accounts A ON BI.owner = A.id WHERE BI.id = ? AND BI.price > ? AND BI.sold = ?");
    $products->execute(array(get("id"), 0, 0));
    $readProducts = $products->fetch();
    ?>
    <?php if ($products->rowCount() > 0): ?>
      <?php $productPrice = $readProducts["price"]; ?>
      <!-- Modal -->
      <div class="modal fade" id="buyModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="buyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <div class="modal-title" id="buyModalLabel">Pazar <i class="fa fa-angle-double-right"></i> <?php echo $readProducts["name"]; ?></div>
              <button type="button" class="close" data-dismiss="modal" aria-label="Kapat">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="title background mt-0"><span>Ürün Bilgisi</span></div>
                </div>
                <div class="col-4">
                  <div class="store-card">
                    <img class="store-card-img" src="/apps/main/public/assets/img/items/<?php echo strtolower($readProducts["itemID"]).'.png'; ?>">
                  </div>
                </div>
                <div class="col-8">
                  <div class="row">
                    <span class="col-sm-4 font-weight-bold">Ürün Adı:</span>
                    <span class="col-sm-8"><?php echo $readProducts["name"]; ?></span>
                  </div>
                  <div class="row">
                    <span class="col-sm-4 font-weight-bold">Ürün ID:</span>
                    <span class="col-sm-8"><?php echo $readProducts["itemID"]; ?></span>
                  </div>
                  <div class="row">
                    <span class="col-sm-4 font-weight-bold">Adet:</span>
                    <span class="col-sm-8"><?php echo $readProducts["amount"]; ?></span>
                  </div>
                  <div class="row">
                    <span class="col-sm-4 font-weight-bold">Dayanıklık:</span>
                    <span class="col-sm-8"><?php echo ($readProducts["durability"] > $readProducts["maxDurability"] ? $readProducts["maxDurability"] : $readProducts["durability"])."/".$readProducts["maxDurability"]; ?></span>
                  </div>
                  <div class="row">
                    <span class="col-sm-4 font-weight-bold">Satıcı:</span>
                    <span class="col-sm-8"><?php echo $readProducts["realname"]; ?></span>
                  </div>
                  <div class="row">
                    <span class="col-sm-4 font-weight-bold">Fiyat:</span>
                    <span class="col-sm-8">
                      <?php echo $productPrice; ?> <?php echo $readSettings["creditText"] ?>
                    </span>
                  </div>
                </div>
              </div>
              <?php if ($readProducts["lore"] != null && $readProducts["lore"] != ""): ?>
                <div class="row">
                  <div class="col-md-12">
                    <div class="title background"><span>Ürün Açıklaması</span></div>
                    <div class="product-details">
                      <?php
                        $lore = $readProducts["lore"];
                        $lore = str_replace("\n", "<br>", $lore);
                        echo $lore;
                      ?>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
              <?php if ($readProducts["enchantments"] != null && $readProducts["enchantments"] != ""): ?>
                <div class="row">
                  <div class="col-md-12">
                    <div class="title background"><span>Ürün Büyüleri</span></div>
                    <div class="product-details">
                      <?php
                        $enchantments = $readProducts["enchantments"];
                        $enchantments = explode(",", $enchantments);
                        foreach ($enchantments as $enchantment) {
                          $enchantment = explode(":", $enchantment);
                          echo "* Lvl. ".$enchantment[1]." - ".$enchantment[0]."<br>";
                        }
                      ?>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
              <?php if ($readProducts["description"] != null && $readProducts["description"] != ""): ?>
                <div class="row">
                  <div class="col-md-12">
                    <div class="title background"><span>İlan Açıklaması</span></div>
                    <div class="product-details">
                      <?php
                        $description = $readProducts["description"];
                        $description = str_replace("\n", "<br>", $description);
                        echo $description;
                      ?>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
              <div class="row pt-3">
                <div class="col">
                  <span class="font-weight-bold">Ödenecek Tutar:</span>
                </div>
                <div class="col-auto text-right">
                  <span class="text-success" value="<?php echo $productPrice; ?>">
                    <?php echo $productPrice; ?> <?php echo $readSettings["creditText"] ?>
                  </span>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <input type="hidden" id="inputProduct" name="product" value="<?php echo $readProducts["id"]; ?>">
              <button type="button" class="btn btn-rounded btn-danger" data-dismiss="modal">İptal</button>
              <?php if (isset($_SESSION["login"])): ?>
                <button type="button" id="buyProductButton" class="btn btn-rounded btn-success">Satın Al</button>
              <?php else: ?>
                <a href="/giris-yap" class="btn btn-rounded btn-success">Giriş Yap</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <script type="text/javascript">
        var buyModal = $("#buyModal");
        var inputProduct = $("#inputProduct");
        var buyProductButton = $("#buyProductButton");
        
        buyProductButton.on("click", function() {
          $.ajax({
            type: "POST",
            url: "/apps/main/public/ajax/bazaar-buy.php",
            data: {productID: inputProduct.val()},
            success: function(result) {
              if (result == "error") {
                swal.fire({
                  title: "HATA!",
                  text: "Beklenmedik bir hata oluştu, lütfen daha sonra tekrar deneyiniz.",
                  type: "error",
                  confirmButtonColor: "#02b875",
                  confirmButtonText: "Tamam"
                }).then(function() {
                  buyModal.modal("hide");
                });
              }
              if (result == "error_login") {
                swal.fire({
                  title: "HATA!",
                  text: "Satın alım işlemi için giriş yapmalısınız.",
                  type: "error",
                  confirmButtonColor: "#02b875",
                  confirmButtonText: "Tamam"
                }).then(function() {
                  buyModal.modal("hide");
                });
              }
              else if (result == "error_self") {
                swal.fire({
                  title: "HATA!",
                  text: "Kendi sattığın ürünü alamazsın!",
                  type: "error",
                  confirmButtonColor: "#02b875",
                  confirmButtonText: "Tamam"
                }).then(function() {
                  buyModal.modal("hide");
                });
              }
              else if (result == "unsuccessful") {
                swal.fire({
                  title: "HATA!",
                  text: "Yetersiz bakiye!",
                  type: "error",
                  confirmButtonColor: "#02b875",
                  confirmButtonText: "Tamam"
                }).then(function() {
                  window.location = '/kredi/yukle';
                });
              }
              else {
                swal.fire({
                  title: "BAŞARILI!",
                  text: "Ürün başarıyla satın alındı! Oyundan /sitepazar yazarak teslim alabilirsiniz.",
                  type: "success",
                  confirmButtonColor: "#02b875",
                  confirmButtonText: "Tamam"
                }).then(function() {
                  window.location = '/pazar-satis';
                });
              }
            }
          });
        });
      </script>
    <?php else : ?>
      <?php die(false); ?>
    <?php endif; ?>
  <?php else : ?>
    <?php die(false); ?>
  <?php endif; ?>
<?php endif; ?>
