<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
?>
<?php if (get("action") == "buy"): ?>
  <?php
    $products = $db->prepare("SELECT * FROM Products WHERE id = ?");
    $products->execute(array(get("id")));
    $readProducts = $products->fetch();
    $discountProducts = explode(",", $readSettings["storeDiscountProducts"]);
  ?>
  <?php if ($products->rowCount() > 0): ?>
    <?php $discountedPriceStatus = ($readProducts["discountedPrice"] != 0 && ($readProducts["discountExpiryDate"] > date("Y-m-d H:i:s") || $readProducts["discountExpiryDate"] == '1000-01-01 00:00:00')); ?>
    <?php $storeDiscountStatus = ($readSettings["storeDiscount"] != 0 && (in_array($readProducts["id"], $discountProducts) || $readSettings["storeDiscountProducts"] == '0') && ($readSettings["storeDiscountExpiryDate"] > date("Y-m-d H:i:s") || $readSettings["storeDiscountExpiryDate"] == '1000-01-01 00:00:00')); ?>
    <?php
      if ($discountedPriceStatus == true || $storeDiscountStatus == true) {
        $productPrice = (($storeDiscountStatus == true) ? round(($readProducts["price"]*(100-$readSettings["storeDiscount"]))/100) : $readProducts["discountedPrice"]);
      }
      else {
        $productPrice = $readProducts["price"];
      }
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
                  <?php if ($discountedPriceStatus == true || $storeDiscountStatus == true): ?>
                    <?php $discountPercent = (($storeDiscountStatus == true) ? $readSettings["storeDiscount"] : round((($readProducts["price"]-$readProducts["discountedPrice"])*100)/($readProducts["price"]))); ?>
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
            <div id="couponBox">
              <div class="row">
                <div class="col-md-12">
                  <div class="title background"><span>Ürün Açıklaması</span></div>
                  <div class="product-details">
                    <?php echo $readProducts["details"]; ?>
                  </div>
                </div>
              </div>
              <div class="row pt-3">
                <div class="col">
                  <span class="font-weight-bold">Ödenecek Tutar:</span>
                </div>
                <div class="col-auto text-right">
                  <s id="oldPrice" class="text-danger" style="display: none;"></s>
                  <span id="newPrice" class="text-success" value="<?php echo $productPrice; ?>">
                    <?php echo $productPrice; ?> <?php echo $readSettings["creditText"] ?>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <input type="hidden" id="inputProduct" name="product" value="<?php echo $readProducts["id"]; ?>">
            <?php if (isset($_SESSION["login"])): ?>
              <button type="button" class="btn btn-rounded btn-primary addToCartButton" data-buynow="false">Sepete Ekle</button>
              <button type="button" class="btn btn-rounded btn-success addToCartButton" data-buynow="true">Hemen Satın Al</button>
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
      var addToCartButton = $(".addToCartButton");

      addToCartButton.on("click", function() {
        var button = $(this);
        $.ajax({
          type: "POST",
          url: "/apps/main/public/ajax/shopping-cart.php?action=add&productID=" + inputProduct.val(),
          success: function(result) {
            if (result == "error" || result == "error_product") {
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
            else if (result == "error_login") {
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
            else {
              if (button.attr("data-buynow") === "true") {
                window.location = '/sepet';
             }
             else {
               if (result == "error_credit") {
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
               else if (result == "error_stock") {
                 swal.fire({
                   title: "HATA!",
                   text: "Stok nedeniyle bu üründen daha fazla ekleyemezsin!",
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
                   text: "Ürün başarıyla sepete eklendi!",
                   type: "success",
                   showCancelButton: true,
                   cancelButtonColor: "#02b875",
                   cancelButtonText: "Alışverişe Devam Et",
                   confirmButtonColor: "#5e72e4",
                   confirmButtonText: "Sepete Git"
                 }).then(function(isAccepted) {
                   if (isAccepted.value) {
                     window.location = '/sepet';
                   }
                 });
                 var response = JSON.parse(result);
                 $(".shopping-cart-count").text(response.items.length);
               }
             }
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
