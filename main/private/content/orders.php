<?php
  if (!isset($_SESSION["login"])) {
    go("/giris-yap");
  }
?>
<section class="section support-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Ana Sayfa</a></li>
            <?php if (get("action") == 'get'): ?>
              <li class="breadcrumb-item active" aria-current="page">Sipariş Görüntüle</li>
            <?php else: ?>
              <li class="breadcrumb-item active" aria-current="page">Hata!</li>
            <?php endif; ?>
          </ol>
        </nav>
      </div>
      <?php if (get("action") == 'get' && isset($_GET["id"])): ?>
        <?php
          $order = $db->prepare("SELECT * FROM Orders WHERE id = ? AND accountID = ?");
          $order->execute(array(get("id"), $readAccount["id"]));
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
                  <table class="table table-hover">
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
                  <div class="row pb-4">
                    <div class="col-md-12">
                      <div class="input-group">
                        <input type="text" class="form-control" name="coupon" disabled readonly value="<?php echo $readOrder["coupon"]; ?>">
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
                <?php if ($readOrder["discount"] != 0): ?>
                  <div class="row pb-4">
                    <div class="col">
                      <span class="font-weight-bold">İndirim:</span>
                    </div>
                    <div class="col-auto text-right">
                      <span class="text-danger">
                        -<?php echo $readOrder["discount"]." ".$readSettings["creditText"]; ?>
                      </span>
                    </div>
                  </div>
                <?php endif; ?>
                <div class="row">
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
      <?php else: ?>
        <?php go('/404'); ?>
      <?php endif; ?>
    </div>
  </div>
</section>
