<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
?>
<?php if (get("action") == "buy"): ?>
  <?php
    $products = $db->prepare("SELECT P.id, P.categoryID, P.name, P.price, P.duration, P.imageID, P.imageType, GNP.price as discountedPrice, GNP.stock, S.name as serverName FROM GamingNightProducts GNP INNER JOIN Products P ON GNP.productID = P.id INNER JOIN Servers S ON S.id = P.serverID WHERE GNP.productID = ?");
    $products->execute(array(get("id")));
    $readProducts = $products->fetch();
  ?>
  <?php if ($products->rowCount() > 0): ?>
    <?php
      $productPrice = $readProducts["discountedPrice"];
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
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <div class="title background mt-0"><span>Ürün Bilgisi</span></div>
              </div>
              <div class="col-4">
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
                  <?php $discountPercent = round((($readProducts["price"]-$readProducts["discountedPrice"])*100)/($readProducts["price"])); ?>
                  <div class="store-card-discount">
                    <span>%<?php echo $discountPercent; ?></span>
                  </div>
                  <img class="store-card-img" src="/apps/main/public/assets/img/store/products/<?php echo $readProducts["imageID"].'.'.$readProducts["imageType"]; ?>" alt="<?php echo $serverName." Ürün - ".$readProducts["name"]." Satın Al"; ?>">
                </div>
              </div>
              <div class="col-8">
                <div class="row">
                  <span class="col-sm-4 font-weight-bold">Ürün Adı:</span>
                  <span class="col-sm-8"><?php echo $readProducts["name"]; ?></span>
                </div>
                <div class="row">
                  <span class="col-sm-4 font-weight-bold">Sunucu:</span>
                  <span class="col-sm-8">
                    <?php echo $readProducts["serverName"]; ?>
                  </span>
                </div>
                <div class="row">
                  <span class="col-sm-4 font-weight-bold">Kategori:</span>
                  <span class="col-sm-8">
                    <?php if ($readProducts["categoryID"] == 0): ?>
                      -
                    <?php else : ?>
                      <?php
                        $productCategory = $db->prepare("SELECT name FROM ProductCategories WHERE id = ?");
                        $productCategory->execute(array($readProducts["categoryID"]));
                        $readProductCategory = $productCategory->fetch();
                      ?>
                      <?php if ($productCategory->rowCount() > 0): ?>
                        <?php echo $readProductCategory["name"]; ?>
                      <?php else : ?>
                        -
                      <?php endif; ?>
                    <?php endif; ?>
                  </span>
                </div>
                <div class="row">
                  <span class="col-sm-4 font-weight-bold">Fiyat:</span>
                  <span class="col-sm-8">
                    <?php echo $productPrice; ?> <?php echo $readSettings["creditText"] ?>
                  </span>
                </div>
                <div class="row">
                  <span class="col-sm-4 font-weight-bold">Süre:</span>
                  <span class="col-sm-8">
                    <?php if ($readProducts["duration"] == 0): ?>
                      Sınırsız
                    <?php elseif ($readProducts["duration"] == -1): ?>
                      Tek Kullanımlık
                    <?php else : ?>
                      <?php echo $readProducts["duration"]; ?> gün
                    <?php endif; ?>
                  </span>
                </div>
                <?php if ($readProducts["stock"] != -1): ?>
                  <div class="row">
                    <span class="col-sm-4 font-weight-bold">Stok:</span>
                    <span class="col-sm-8">
                      <?php if ($readProducts["stock"] == 0): ?>
                        <span class="text-danger">Yok</span>
                      <?php else : ?>
                        <span class="text-success"><?php echo $readProducts["stock"]; ?> adet</span>
                      <?php endif; ?>
                    </span>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <input type="hidden" id="inputProduct" name="product" value="<?php echo $readProducts["id"]; ?>">
            <?php if (isset($_SESSION["login"])): ?>
              <button type="button" id="buyProductButton" class="btn btn-rounded btn-success">Hemen Satın Al</button>
            <?php else: ?>
              <a href="/giris-yap" class="btn btn-rounded btn-success">Giriş Yap</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript">
      var buyModal = $("#buyModal");
      var couponBox = $("#couponBox");
      var alertCoupon = $("#alertCoupon");
      var inputCoupon = $("#inputCoupon");
      var inputProduct = $("#inputProduct");
      var oldPrice = $("#oldPrice");
      var newPrice = $("#newPrice");
      var addCouponButton = $("#addCouponButton");
      var deleteCouponButton = $("#deleteCouponButton");
      var buyProductButton = $("#buyProductButton");
      var addToCartButton = $("#addToCartButton");

      buyProductButton.on("click", function() {
        $.ajax({
          type: "POST",
          url: "/apps/main/public/ajax/gaming-buy.php",
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
            else if (result == "stock_error") {
              swal.fire({
                title: "HATA!",
                text: "Bu ürünümüz stoklarda kalmamıştır!",
                type: "error",
                confirmButtonColor: "#02b875",
                confirmButtonText: "Tamam"
              }).then(function() {
                buyModal.modal("hide");
              });
            }
            else {
              swal.fire({
                title: "BAŞARILI!",
                text: "Ürün başarıyla satın alındı ve sandığa eklendi!",
                type: "success",
                confirmButtonColor: "#02b875",
                confirmButtonText: "Tamam"
              }).then(function() {
                window.location = '/sandik';
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
