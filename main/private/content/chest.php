<?php
  if (!isset($_SESSION["login"])) {
    go("/giris-yap");
  }
  require_once(__ROOT__.'/apps/main/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/main/public/assets/js/chest.js');
?>
<section class="section credit-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Ana Sayfa</a></li>
            <?php if (get("target") == 'chest'): ?>
              <?php if (get("action") == 'getAll'): ?>
                <li class="breadcrumb-item active" aria-current="page">Sandık</li>
              <?php elseif (get("action") == 'gift'): ?>
                <li class="breadcrumb-item"><a href="/sandik">Sandık</a></li>
                <li class="breadcrumb-item active" aria-current="page">Hediye Gönder</li>
              <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page">Hata!</li>
              <?php endif; ?>
            <?php else: ?>
              <li class="breadcrumb-item active" aria-current="page">Hata!</li>
            <?php endif; ?>
          </ol>
        </nav>
      </div>
      <div class="col-md-8">
        <?php if (get("target") == 'chest'): ?>
          <?php if (get("action") == 'getAll'): ?>
            <?php
              $chests = $db->prepare("SELECT C.*, P.name as productName, S.name as serverName, COUNT(P.id) as amount FROM Chests C INNER JOIN Products P ON C.productID = P.id INNER JOIN Servers S ON P.serverID = S.id WHERE C.accountID = ? AND C.status = ? GROUP BY P.id ORDER BY C.id DESC");
              $chests->execute(array($readAccount["id"], 0));
            ?>
            <?php if ($chests->rowCount() > 0): ?>
              <div class="card">
                <div class="card-header">
                  Sandık (<?php echo $chestCount; ?>)
                </div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th class="text-center" style="width: 40px;">#ID</th>
                          <th>Ürün</th>
                          <th>Sunucu</th>
                          <th>İşlem Adedi</th>
                          <th>Tarih</th>
                          <th class="text-right">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($chests as $readChests): ?>
                          <tr id="chest-<?php echo $readChests["id"]; ?>">
                            <td class="text-center" style="width: 40px;">
                              #<?php echo $readChests["id"]; ?>
                            </td>
                            <td>
                              <?php echo $readChests["productName"]; ?>
                            </td>
                            <td>
                              <?php echo $readChests["serverName"]; ?>
                            </td>
                            <td>
                              <div class="input-group" style="width: 90px;">
                                <div class="input-group-prepend">
                                  <button class="btn btn-sm btn-danger chestDecreaseButton" type="button" data-target="#amountChanger-<?php echo $readChests["id"]; ?>">-</button>
                                </div>
                                <input type="text" id="amountChanger-<?php echo $readChests["id"]; ?>" class="form-control form-control-sm text-center" value="<?php echo $readChests["amount"]; ?>" min="1" max="<?php echo $readChests["amount"]; ?>" autocomplete="off">
                                <div class="input-group-append">
                                  <button class="btn btn-sm btn-success chestIncreaseButton" type="button" data-target="#amountChanger-<?php echo $readChests["id"]; ?>">+</button>
                                </div>
                              </div>
                            </td>
                            <td>
                              <?php echo convertTime($readChests["creationDate"], 2, true); ?>
                            </td>
                            <td class="text-right">
                              <button type="button" class="btn btn-success btn-circle deliverButton" data-chest="<?php echo $readChests["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Teslim Al">
                                <i class="fa fa-check"></i>
                              </button>
                              <?php if ($readSettings["giftStatus"] == 1): ?>
                                <a class="btn btn-primary btn-circle" href="/sandik/hediye/<?php echo $readChests["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Hediye Et">
                                  <i class="fa fa-gift"></i>
                                </a>
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
              <?php echo alertError("Sandığınızda herhangi bir ürün bulunamadı!"); ?>
            <?php endif; ?>
          <?php elseif (get("action") == 'gift' && $readSettings["giftStatus"] == 1): ?>
            <?php
              $chest = $db->prepare("SELECT C.*, P.name as productName FROM Chests C INNER JOIN Products P ON C.productID = P.id WHERE C.accountID = ? AND C.id = ? AND C.status = ?");
              $chest->execute(array($readAccount["id"], get("id"), 0));
              $readChest = $chest->fetch();
              
              $chestAmount = $chest = $db->prepare("SELECT COUNT(P.id) as amount FROM Chests C INNER JOIN Products P ON C.productID = P.id WHERE C.accountID = ? AND C.status = ? AND P.id = ? GROUP BY P.id");
              $chestAmount->execute(array($readAccount["id"], 0, $readChest["productID"]));
              $readChestAmount = $chestAmount->fetch();
            ?>
            <?php if ($chest->rowCount() > 0 && $chestAmount->rowCount() > 0): ?>
              <?php
                require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
                $csrf = new CSRF('csrf-sessions', 'csrf-token');
                if (isset($_POST["sendGift"])) {
                  if (!$csrf->validate('sendGift')) {
                    echo alertError("Sistemsel bir sorun oluştu!");
                  }
                  else if (post("username") == null || post("amount") == null) {
                    echo alertError("Lütfen boş alan bırakmayınız!");
                  }
                  //else if ($readAccount["realname"] == post("username")) {
                  else if (strtolower($readAccount["realname"]) == strtolower(post("username"))) {
                    echo alertError("Kendine hediye gönderemezsiniz!");
                  }
                  else if ($readChest["isLocked"] == 1) {
                    echo alertError("Eşya kilitli!");
                  }
                  else {
                    $checkAccount = $db->prepare("SELECT id FROM Accounts WHERE realname = ?");
                    $checkAccount->execute(array(post("username")));
                    $readCheckedAccount = $checkAccount->fetch();
                    if ($checkAccount->rowCount() > 0) {
                      if ($readChestAmount["amount"] >= post("amount")) {
                        $limit = post("amount");
                        $chestForGifting = $db->prepare("SELECT C.id FROM Chests C INNER JOIN Products P ON C.productID = P.id WHERE C.accountID = ? AND P.id = ? AND C.status = ? AND C.isLocked = ? ORDER BY C.id DESC LIMIT $limit");
                        $chestForGifting->execute(array($readAccount["id"], $readChest["productID"], 0, 0));
                        foreach ($chestForGifting as $readChestForGifting) {
                          $updateChest = $db->prepare("UPDATE Chests SET accountID = ? WHERE accountID = ? AND id = ? AND status = ? AND isLocked = ?");
                          $updateChest->execute(array($readCheckedAccount["id"], $readAccount["id"], $readChestForGifting["id"], 0, 0));
  
                          $insertChestHistory = $db->prepare("INSERT INTO ChestsHistory (accountID, chestID, type, creationDate) VALUES (?, ?, ?, ?)");
                          $insertChestHistory->execute(array($readAccount["id"], $readChestForGifting["id"], 2, date("Y-m-d H:i:s")));
                          $insertChestHistory->execute(array($readCheckedAccount["id"], $readChestForGifting["id"], 3, date("Y-m-d H:i:s")));
                        }
  
                        echo alertSuccess("Hediye başarıyla ".post("username")." adlı kullanıcıya gönderilmiştir!");
                      }
                      else {
                        echo alertError("Bu üründen en fazla ".$readChestAmount["amount"]." adet hediye gönderebilirsiniz!");
                      }
                    }
                    else {
                      echo alertError("Girmiş olduğunuz oyuncu bulunamadı!");
                    }
                  }
                }
              ?>
              <div class="card">
                <div class="card-header">
                  Hediye Gönder
                </div>
                <div class="card-body">
                  <form action="" method="post">
                    <div class="form-group row">
                      <label for="inputProduct" class="col-sm-2 col-form-label">Ürün:</label>
                      <div class="col-sm-10">
                        <input type="text" id="inputProduct" class="form-control-plaintext" value="<?php echo $readChest["productName"]; ?>" readonly>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputUsername" class="col-sm-2 col-form-label">Kullanıcı:</label>
                      <div class="col-sm-10">
                        <input type="text" id="inputUsername" class="form-control" name="username" placeholder="Hediye göndereceğiniz oyuncunun kullanıcı adını yazınız.">
                        <small>Hediye göndereceğiniz oyuncunun kullanıcı adını yazınız.</small>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputAmount" class="col-sm-2 col-form-label">Adet:</label>
                      <div class="col-sm-10">
                        <input type="number" id="inputAmount" class="form-control" name="amount" placeholder="Hediyenin kaç adet gönderileceğini yazınız." value="1" min="1" max="<?php echo $readChestAmount["amount"]; ?>">
                        <small>Bu üründen en fazla <strong><?php echo $readChestAmount["amount"]; ?></strong> adet gönderebilirsiniz.</small>
                      </div>
                    </div>
                    <?php echo $csrf->input('sendGift'); ?>
                    <div class="clearfix">
                      <div class="float-right">
                        <button type="submit" class="btn btn-rounded btn-success" name="sendGift" onclick="return confirm('Hediyeyi bu kişiye göndermek istediğine emin misin?')">Gönder</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            <?php else: ?>
              <?php echo alertError("Sandık eşyası bulunamadı!"); ?>
            <?php endif; ?>
          <?php else: ?>
            <?php go("/404"); ?>
          <?php endif; ?>
        <?php else: ?>
          <?php go("/404"); ?>
        <?php endif; ?>
      </div>

      <div class="col-md-4">
        <div class="row">
          <div class="col-md-12">
            <?php
              $chestsHistory = $db->prepare("SELECT CH.*, P.name as productName FROM ChestsHistory CH INNER JOIN Chests C ON CH.chestID = C.id INNER JOIN Products P ON C.productID = P.id WHERE CH.accountID = ? ORDER BY CH.id DESC LIMIT 5");
              $chestsHistory->execute(array($readAccount["id"]));
            ?>
            <?php if ($chestsHistory->rowCount() > 0): ?>
              <div class="card">
                <div class="card-header">
                  <div class="row">
                    <div class="col">
                      <span>Sandık Geçmişi</span>
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
                          <th class="text-center">#</th>
                          <th>Kullanıcı</th>
                          <th class="text-center">Ürün</th>
                          <th class="text-center">İşlem</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($chestsHistory as $readChestsHistory): ?>
                          <tr>
                            <td class="text-center">
                              <img class="rounded-circle" src="https://minotar.net/avatar/<?php echo $readAccount["realname"]; ?>/20.png" alt="<?php echo $serverName." Oyuncu - ".$readAccount["realname"]; ?>">
                            </td>
                            <td>
                              <?php echo $readAccount["realname"]; ?>
                            </td>
                            <td class="text-center"><?php echo $readChestsHistory["productName"]; ?></td>
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
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            <?php else: ?>
              <?php echo alertError("Sandık geçmişi bulunamadı!"); ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
