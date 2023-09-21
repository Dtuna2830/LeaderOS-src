<?php
  if (!isset($_SESSION["login"])) {
    go("/giris-yap");
  }
  if (get("action") == 'send' && get("id")) {
    $receiverAccount = $db->prepare("SELECT * FROM Accounts WHERE id = ?");
    $receiverAccount->execute(array(get("id")));
    $readReceiverAccount = $receiverAccount->fetch();
  }
  
  require_once(__ROOT__.'/apps/main/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/main/public/assets/js/credit.js');
?>
<section class="section credit-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Ana Sayfa</a></li>
            <?php if (get("target") == "credit"): ?>
              <?php if (get("action") == "charge"): ?>
                <li class="breadcrumb-item"><a href="/kredi/yukle"><?php echo $readSettings["creditText"] ?></a></li>
                <li class="breadcrumb-item active" aria-current="page">Yükle</li>
              <?php elseif (get("action") == "send"): ?>
                <?php if (get("id")): ?>
                  <li class="breadcrumb-item"><a href="/kredi/yukle"><?php echo $readSettings["creditText"] ?></a></li>
                  <li class="breadcrumb-item"><a href="/kredi/gonder">Gönder</a></li>
                  <?php if ($receiverAccount->rowCount() > 0): ?>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $readReceiverAccount["realname"]; ?></li>
                  <?php else: ?>
                    <li class="breadcrumb-item active" aria-current="page">Bulunamadı</li>
                  <?php endif; ?>
                <?php else: ?>
                  <li class="breadcrumb-item"><a href="/kredi/yukle"><?php echo $readSettings["creditText"] ?></a></li>
                  <li class="breadcrumb-item active" aria-current="page">Gönder</li>
                <?php endif; ?>
              <?php elseif (get("action") == "pay"): ?>
                <li class="breadcrumb-item"><a href="/kredi/yukle"><?php echo $readSettings["creditText"] ?></a></li>
                <li class="breadcrumb-item"><a href="/kredi/yukle">Yükle</a></li>
                <?php if (get("api") == "paytr"): ?>
                  <li class="breadcrumb-item active" aria-current="page">PayTR</li>
                <?php elseif (get("api") == "ininal"): ?>
                  <li class="breadcrumb-item active" aria-current="page">Ininal</li>
                <?php elseif (get("api") == "papara"): ?>
                  <li class="breadcrumb-item active" aria-current="page">Papara</li>
                <?php elseif (get("api") == "eft"): ?>
                  <li class="breadcrumb-item active" aria-current="page">EFT</li>
                <?php else: ?>
                  <li class="breadcrumb-item active" aria-current="page">Bulunamadı!</li>
                <?php endif; ?>
              <?php else: ?>
                <li class="breadcrumb-item"><a href="/kredi/yukle"><?php echo $readSettings["creditText"] ?></a></li>
                <li class="breadcrumb-item active" aria-current="page">Yükle</li>
              <?php endif; ?>
            <?php else: ?>
              <?php go("/404"); ?>
            <?php endif; ?>
          </ol>
        </nav>
      </div>
      <?php $emailStatus = $readAccount["email"] != "your@email.com" && $readAccount["email"] != "guncelle@gmail.com"; ?>
      <?php if (get("target") == 'credit' && get("action") == 'charge' && $emailStatus && $readSettings["creditPackageStatus"] == 1): ?>
        <?php
        $products = $db->query("SELECT * FROM CreditPackages ORDER BY price ASC");
        ?>
        <?php if ($products->rowCount() > 0): ?>
          <div id="modalBox"></div>
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                Kredi Paketleri
              </div>
              <div class="card-body">
                <div class="row store-cards">
                  <?php foreach ($products as $readProducts): ?>
                    <?php $discountedPriceStatus = ($readProducts["discountedPrice"] != 0 && ($readProducts["discountExpiryDate"] > date("Y-m-d H:i:s") || $readProducts["discountExpiryDate"] == '1000-01-01 00:00:00')); ?>
                    <div class="col-md-3">
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
                        <img class="store-card-img lazyload" data-src="/apps/main/public/assets/img/store/products/<?php echo $readProducts["imageID"].'.'.$readProducts["imageType"]; ?>" src="/apps/main/public/assets/img/loaders/store.png" alt="<?php echo $serverName." Ürün - ".$readProducts["name"]." Satın Al"; ?>">
                        <div class="row store-card-text">
                          <div class="col">
                            <span><?php echo $readProducts["name"]; ?></span>
                          </div>
                          <div class="col-auto">
                            <?php if ($discountedPriceStatus): ?>
                              <span class="old-price"><?php echo str_replace('.00', '', $readProducts["price"]); ?></span>
                              <small>/</small>
                              <?php $newPrice = str_replace('.00', '', $readProducts["discountedPrice"]); ?>
                              <span class="price"><?php echo $newPrice; ?> <i class="fa fa-lira-sign"></i></span>
                            <?php else: ?>
                              <span class="price"><?php echo str_replace('.00', '', $readProducts["price"]); ?> <i class="fa fa-lira-sign"></i></span>
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
          </div>
        <?php else: ?>
          <?php echo alertError("Bu sayfaya ait ürün bulunamadı!"); ?>
        <?php endif; ?>
      <?php else: ?>
        <div class="col-md-8">
          <?php if (get("target") == 'credit'): ?>
            <?php if (get("action") == 'charge'): ?>
              <?php if ($emailStatus): ?>
                <?php
                require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
                $csrf = new CSRF('csrf-sessions', 'csrf-token');
                ?>
                <?php if ($readSettings["creditMultiplier"] != 1): ?>
                  <?php echo alertWarning('<strong>1 TL = '.$readSettings["creditMultiplier"].' '. $readSettings["creditText"] .'</strong>'); ?>
                <?php endif; ?>
                <?php if ($readSettings["bonusCredit"] != 0): ?>
                  <?php if ($readSettings["bonusCreditMinAmount"] == 0): ?>
                    <?php echo alertWarning("Tüm ". $readSettings["creditText"] ." yüklemelerinde %".$readSettings["bonusCredit"]." bonus ". $readSettings["creditText"]); ?>
                  <?php else: ?>
                    <?php echo alertWarning($readSettings["bonusCreditMinAmount"]." ".$readSettings["creditText"]." ve üzeri yüklemelerde %".$readSettings["bonusCredit"]." bonus ". $readSettings["creditText"]); ?>
                  <?php endif; ?>
                <?php endif; ?>
                <div class="card">
                  <div class="card-header">
                    <?php echo $readSettings["creditText"] ?> Yükle
                  </div>
                  <div class="card-body">
                    <form id="creditChargeForm" action="/apps/main/public/ajax/pay.php" method="post">
                      <div class="form-group row">
                        <label for="inputUsername" class="col-sm-2 col-form-label">Kullanıcı:</label>
                        <div class="col-sm-10">
                          <input type="text" id="inputUsername" class="form-control" value="<?php echo $readAccount["realname"]; ?>" readonly="readonly">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputPrice" class="col-sm-2 col-form-label">Miktar:</label>
                        <div class="col-sm-10">
                          <div class="input-group">
                            <input type="number" id="inputPrice" class="form-control" name="price" placeholder="Yüklenecek Miktar" aria-label="Yüklenecek Miktar" aria-describedby="ariaPrice" min="<?php echo $readSettings["minPay"]; ?>" max="<?php echo $readSettings["maxPay"]; ?>" required="required">
                            <div class="input-group-append">
                              <span id="ariaPrice" class="input-group-text"><i class="fa fa-coins"></i></span>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="selectPayment" class="col-sm-2 col-form-label">Ödeme:</label>
                        <div class="col-sm-10">
                          <select id="selectPayment" class="form-control" name="paymentID" data-toggle="select2" required="required">
                            <?php
                              $payment = $db->prepare("SELECT P.* FROM Payment P INNER JOIN PaymentSettings PS ON P.apiID = PS.slug WHERE PS.status = ? ORDER BY P.id DESC");
                              $payment->execute(array(1));
                            ?>
                            <?php if ($payment->rowCount() > 0): ?>
                              <?php foreach ($payment as $readPayment): ?>
                                <option value="<?php echo $readPayment["id"]; ?>">
                                  <?php echo $readPayment["title"]; ?>
                                  <?php if ($readSettings["bonusCredit"] == 0 && $readPayment["bonusCredit"] > 0): ?>
                                    <?php if ($readPayment["bonusCreditMinAmount"] == 0): ?>
                                      (Tüm yüklemelerde %<?php echo $readPayment["bonusCredit"]; ?> Bonus!)
                                    <?php else: ?>
                                      (<?php echo $readPayment["bonusCreditMinAmount"]." ".$readSettings["creditText"] ?> üzeri yüklemelerde %<?php echo $readPayment["bonusCredit"]; ?> Bonus!)
                                    <?php endif; ?>
                                  <?php endif; ?>
                                </option>
                              <?php endforeach; ?>
                            <?php else: ?>
                              <option>Ödeme yöntemi bulunamadı!</option>
                            <?php endif; ?>
                          </select>
                        </div>
                      </div>
                      <?php if ($payment->rowCount() > 0): ?>
                        <hr>
                        <?php
                        $accountContactInfo = $db->prepare("SELECT * FROM AccountContactInfo WHERE accountID = ?");
                        $accountContactInfo->execute(array($readAccount["id"]));
                        $readAccountContactInfo = $accountContactInfo->fetch();
                        ?>
                        <div class="form-group row">
                          <label for="inputFirstName" class="col-sm-2 col-form-label">Ad:</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" id="inputFirstName" placeholder="Adınızı yazınız." name="firstName" required="required" value="<?php echo (isset($readAccountContactInfo["firstName"])) ? $readAccountContactInfo["firstName"] : null; ?>">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="inputLastName" class="col-sm-2 col-form-label">Soyad:</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" id="inputLastName" placeholder="Soyadınızı yazınız." name="lastName" required="required" value="<?php echo (isset($readAccountContactInfo["lastName"])) ? $readAccountContactInfo["lastName"] : null; ?>">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="inputEmail" class="col-sm-2 col-form-label">E-Posta:</label>
                          <div class="col-sm-10">
                            <input type="email" class="form-control" id="inputEmail" required="required" value="<?php echo $readAccount["email"]; ?>" readonly="readonly">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="inputPhoneNumber" class="col-sm-2 col-form-label">Telefon:</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" id="inputPhoneNumber" placeholder="Telefon numaranızı yazınız." name="phoneNumber" required="required" value="<?php echo (isset($readAccountContactInfo["phoneNumber"])) ? $readAccountContactInfo["phoneNumber"] : null; ?>">
                          </div>
                        </div>
                      <?php endif; ?>
                      <?php echo $csrf->input('chargeCredit'); ?>
                      <div class="clearfix">
                        <div class="float-right">
                          <button type="submit" class="btn btn-rounded btn-success" name="chargeCredit"><?php echo $readSettings["creditText"] ?> Yükle</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              <?php else: ?>
                <?php echo alertError($readSettings["creditText"] ." yüklemek için e-posta adresinizi güncellemeniz gerekmektedir."); ?>
                <a href="/profil/duzenle" class="btn btn-success w-100">E-Posta adresinizi güncellemek için tıklayın.</a>
              <?php endif; ?>
            <?php elseif (get("action") == 'send' && $readSettings["creditStatus"] == 1): ?>
              <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["sendCredit"])) {
                $receiverAccount = $db->prepare("SELECT * FROM Accounts WHERE realname = ?");
                $receiverAccount->execute(array(post("username")));
                $readReceiverAccount = $receiverAccount->fetch();
          
                if (!$csrf->validate('sendCredit')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("username") == null || post("price") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else if (post("price") <= 0) {
                  echo alertError("Lütfen geçerli bir miktar yazınız!");
                }
                else if ($readAccount["id"] == $readReceiverAccount["id"]) {
                  echo alertError("Kendine ". $readSettings["creditText"] ." gönderemezsin!");
                }
                else if ($receiverAccount->rowCount() == 0) {
                  echo alertError("Böyle bir kullanıcı bulunamadı!");
                }
                else if (post("price") > $readAccount["credit"]) {
                  echo alertError("Yetersiz bakiye!");
                }
                else if (!is_numeric(post("price"))) {
                  echo alertError("Gönderilecek miktara bir sayı yazınız!");
                }
                else {
                  $db->beginTransaction();
            
                  $updateSenderAccount = $db->prepare("UPDATE Accounts SET credit = credit - :amount  WHERE id = :sender");
                  $updateSenderAccount->execute(array(
                    ":amount" => post("price"),
                    ":sender" => $readAccount["id"]
                  ));
            
                  $updateReceiverAccount = $db->prepare("UPDATE Accounts SET credit = credit + :amount WHERE id = :receiver");
                  $updateReceiverAccount->execute(array(
                    ":amount"   => post("price"),
                    ":receiver" => $readReceiverAccount["id"]
                  ));
            
                  $insertCreditHistory = $db->prepare("INSERT INTO CreditHistory (accountID, paymentID, paymentStatus, type, price, earnings, creationDate) VALUES (?, ?, ?, ?, ?, ?, ?)");
                  $insertCreditHistory->execute(array($readAccount["id"], 0, 1, 3, post("price"), 0, date("Y-m-d H:i:s")));
                  $insertCreditHistory->execute(array($readReceiverAccount["id"], 0, 1, 4, post("price"), 0, date("Y-m-d H:i:s")));
            
                  if ($updateSenderAccount && $updateReceiverAccount && $insertCreditHistory) {
                    $db->commit(); // işlemi tamamla
                    echo alertSuccess('<strong>'.post("price").' '.$readSettings["creditText"].'</strong> tutarındaki kredi başarıyla <strong>'.post("username").'</strong> adlı kullanıcıya gönderilmiştir!');
                  }
                  else {
                    $db->rollBack(); // işlemi geri al
                    alertError("İşlem yapılırken sistemsel bir sorun oluştu!");
                  }
                }
              }
              ?>
              <div class="card">
                <div class="card-header">
                  <?php echo $readSettings["creditText"] ?> Gönder
                </div>
                <div class="card-body">
                  <form action="" method="post">
                    <div class="form-group row">
                      <label for="inputUsername" class="col-sm-2 col-form-label">Kullanıcı:</label>
                      <div class="col-sm-10">
                        <input type="text" id="inputUsername" class="form-control" name="username" placeholder="Kullanıcı Adı" value="<?php echo (get("id") && $receiverAccount->rowCount() > 0) ? $readReceiverAccount["realname"] : null; ?>">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputPrice" class="col-sm-2 col-form-label">Miktar:</label>
                      <div class="col-sm-10">
                        <div class="input-group">
                          <input type="number" id="inputPrice" class="form-control" name="price" placeholder="Gönderilecek Miktar" aria-label="Gönderilecek Miktar" aria-describedby="ariaPrice">
                          <div class="input-group-append">
                            <span id="ariaPrice" class="input-group-text"><i class="fa fa-coins"></i></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php echo $csrf->input('sendCredit'); ?>
                    <div class="clearfix">
                      <div class="float-right">
                        <button type="submit" class="btn btn-rounded btn-success" name="sendCredit" onclick="return confirm('<?php echo $readSettings["creditText"] ?>'yi bu kişiye göndermek istediğine emin misin?')"><?php echo $readSettings["creditText"] ?> Gönder</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            <?php elseif (get("action") == "pay"): ?>
              <?php if (get("api") == 'paytr'): ?>
                <div class="card">
                  <div class="card-header">
                    <?php echo $readSettings["creditText"] ?> Yükle
                  </div>
                  <div class="card-body">
                    <div class="iframe-payment-content">
                      <?php if (isset($_SESSION["PAYTR_IFRAME_TOKEN"])): ?>
                        <script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
                        <iframe src="https://www.paytr.com/odeme/guvenli/<?php echo $_SESSION["PAYTR_IFRAME_TOKEN"]; ?>" id="paytriframe" frameborder="0" scrolling="no" style="width: 100%;"></iframe>
                        <script>iFrameResize({}, "#paytriframe");</script>
                        <?php unset($_SESSION["PAYTR_IFRAME_TOKEN"]); ?>
                      <?php else: ?>
                        <?php go("/kredi/yukle"); ?>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php elseif (get("api") == 'ininal'): ?>
                <?php
                $ininal = $db->prepare("SELECT variables FROM PaymentSettings WHERE slug = ?");
                $ininal->execute(array('ininal'));
                $readIninal = $ininal->fetch();
                $readVariables = json_decode($readIninal["variables"], true);
                ?>
                <?php if (count(array_filter($readVariables["ininalBarcodes"]))): ?>
                  <?php foreach ($readVariables["ininalBarcodes"] as $ininalBarcode): ?>
                    <div class="alert alert-success d-flex flex-column align-items-center">
                      <p class="mb-1"><strong>BARKOD NO:</strong></p>
                      <p class="mb-1"><?php echo $ininalBarcode; ?></p>
                    </div>
                  <?php endforeach; ?>
                  <div class="alert alert-primary">
                    <p class="mb-2"><strong>Nasıl Para Gönderebilirim?</strong></p>
                    <p class="mb-1"><strong>1)</strong> İninal Mobil Uygulamasına giriş yapınız.</p>
                    <p class="mb-1"><strong>2)</strong> Alt kısımdan <strong>"İşlemler"</strong> sekmesine geçinız.</p>
                    <p class="mb-1"><strong>3)</strong> <strong>"Para Gönder"</strong> butonuna tıklayınız.</p>
                    <p class="mb-1"><strong>4)</strong> <strong>"Alıcı Kart Barkodu"</strong>'na tıklayınız.</p>
                    <p class="mb-1"><strong>5)</strong> Açılan kamerada <strong>"Barkod numarasını kendim girmek istiyorum"</strong>'a tıklayınız.</p>
                    <p class="mb-1"><strong>6)</strong> Açılan sayfaya sitemizdeki Barkod NO'sunu yazınız ve <strong>"Devam Et"</strong> butonuna tıklayınız.</p>
                    <p class="mb-1"><strong>7)</strong> Miktar kısmına yükleyeceğiniz <?php echo $readSettings["creditText"] ?> miktarını yazınız.</p>
                    <p class="mb-1"><strong>8)</strong> Açıklama kısmına, yükleme yapılacak hesabın kullanıcı adını yazınız.</p>
                    <p class="mb-1"><strong>9)</strong> Ödeme yaptıktan sonra <strong>Destek Bildirimi</strong> açınız.</p>
                  </div>
                <?php else: ?>
                  <?php echo alertError("Barkod numarası bulunamadı!"); ?>
                <?php endif; ?>
              <?php elseif (get("api") == 'papara'): ?>
                <?php
                $papara = $db->prepare("SELECT variables FROM PaymentSettings WHERE slug = ?");
                $papara->execute(array('papara'));
                $readPapara = $papara->fetch();
                $readVariables = json_decode($readPapara["variables"], true);
                ?>
                <?php if (count(array_filter($readVariables["paparaNumbers"]))): ?>
                  <?php foreach ($readVariables["paparaNumbers"] as $paparaNumber): ?>
                    <div class="alert alert-success d-flex flex-column align-items-center">
                      <p class="mb-1"><strong>PAPARA NO:</strong></p>
                      <p class="mb-1"><?php echo $paparaNumber; ?></p>
                    </div>
                  <?php endforeach; ?>
                  <div class="alert alert-primary">
                    <p class="mb-2"><strong>Nasıl Para Gönderebilirim?</strong></p>
                    <p class="mb-1"><strong>1)</strong> Papara Mobil Uygulamasına giriş yapınız.</p>
                    <p class="mb-1"><strong>2)</strong> Alt kısımdan <strong>"Gönder"</strong> sekmesine geçinız.</p>
                    <p class="mb-1"><strong>3)</strong> <strong>"Papara Numarsına"</strong> butonuna tıklayınız.</p>
                    <p class="mb-1"><strong>4)</strong> Açılan sayfaya sitemizdeki Papara NO'sunu, Gönderilecek tutarı yazınız ve <strong>"Para Gönder"</strong> butonuna tıklayınız.</p>
                    <p class="mb-1"><strong>5)</strong> Ödeme yaptıktan sonra <strong>Destek Bildirimi</strong> açınız ve mesaja <strong>"İşlem Numarasını"</strong> yazınız.</p>
                  </div>
                <?php else: ?>
                  <?php echo alertError("Papara numarası bulunamadı!"); ?>
                <?php endif; ?>
              <?php elseif (get("api") == 'eft'): ?>
                <?php
                $eft = $db->prepare("SELECT variables FROM PaymentSettings WHERE slug = ?");
                $eft->execute(array('eft'));
                $readEFT = $eft->fetch();
                $readVariables = json_decode($readEFT["variables"], true);
                ?>
                <?php if (count(array_filter($readVariables["bankAccounts"]))): ?>
                  <?php echo alertWarning('Ödeme işlemini yaptıktan sonra <strong>Destek Bildirimi</strong> açınız.'); ?>
                  <?php foreach ($readVariables["bankAccounts"] as $bankAccount): ?>
                    <div class="alert alert-success d-flex flex-column align-items-center">
                      <p class="mb-1"><strong>AD SOYAD:</strong> <?php echo $bankAccount["fullName"]; ?></p>
                      <p class="mb-1"><strong>BANKA:</strong> <?php echo $bankAccount["bankName"]; ?></p>
                      <p class="mb-1"><strong>IBAN:</strong> <?php echo $bankAccount["iban"]; ?></p>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <?php echo alertError("Banka hesabı bulunamadı!"); ?>
                <?php endif; ?>
              <?php else: ?>
                <?php echo alertError("Ödeme yöntemi bulunamadı!"); ?>
              <?php endif; ?>
            <?php elseif (get("action") == 'alert'): ?>
              <?php if (get("result") == 'success'): ?>
                <div class="card mb-3">
                  <div class="card-header">
                    İşlem Başarılı!
                  </div>
                  <div class="card-body text-success text-center">
                    <div class="mt-3">
                      <img src="/apps/main/public/assets/img/extras/success.png" alt="İşlem Başarılı!" width="120px">
                    </div>
                    <p class="mt-4"><?php echo $readSettings["creditText"] ?> yükleme işleminiz başarıyla gerçekleştirilmiştir!</p>
                    <a href="/magaza" class="btn btn-success rounded-pill mb-3">Alışverişe Başla!</a>
                  </div>
                </div>
              <?php elseif (get("result") == 'unsuccess'): ?>
                <div class="card mb-3">
                  <div class="card-header">
                    İşlem Başarısız!
                  </div>
                  <div class="card-body text-danger text-center">
                    <div class="mt-3">
                      <img src="/apps/main/public/assets/img/extras/unsuccess.png" alt="İşlem Başarısız!" width="120px">
                    </div>
                    <p class="mt-4"><?php echo $readSettings["creditText"] ?> yükleme işleminiz maalesef başarısız olmuştur!</p>
                    <a href="/kredi/yukle" class="btn btn-primary rounded-pill mb-3">Tekrar Dene!</a>
                  </div>
                </div>
              <?php else: ?>
                <?php go("/404"); ?>
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
            <?php if (get("target") == "credit"): ?>
              <?php if (get("action") == 'send'): ?>
                <div class="col-md-12">
                  <?php
                    $creditHistory = $db->prepare("SELECT * FROM CreditHistory WHERE accountID = ? AND type IN (?, ?) AND paymentStatus = ? ORDER by id DESC LIMIT 5");
                    $creditHistory->execute(array($readAccount["id"], 3, 4, 1));
                  ?>
                  <?php if ($creditHistory->rowCount() > 0): ?>
                    <div class="card mb-3">
                      <div class="card-header">
                        <div class="row">
                          <div class="col">
                            <span><?php echo $readSettings["creditText"] ?> Gönderim Geçmişi</span>
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
                              <th>Kullanıcı Adı</th>
                              <th class="text-center">Miktar</th>
                              <th class="text-center">Ödeme</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($creditHistory as $readCreditHistory): ?>
                              <tr>
                                <td class="text-center">
                                  <img class="rounded-circle" src="https://minotar.net/avatar/<?php echo $readAccount["realname"]; ?>/20.png" alt="<?php echo $serverName." Oyuncu - ".$readAccount["realname"]; ?>">
                                </td>
                                <td>
                                  <?php echo $readAccount["realname"]; ?>
                                </td>
                                <td class="text-center"><?php echo ($readCreditHistory["type"] == 3 || $readCreditHistory["type"] == 5) ? '<span class="text-danger">-'.$readCreditHistory["price"].'</span>' : '<span class="text-success">+'.$readCreditHistory["price"].'</span>'; ?></td>
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
                              </tr>
                            <?php endforeach; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  <?php else : ?>
                    <?php echo alertError($readSettings["creditText"] ." gönderim geçmişi bulunamadı!"); ?>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <div class="col-md-12">
                  <?php
                    $creditHistory = $db->prepare("SELECT * FROM CreditHistory WHERE accountID = ? AND type IN (?, ?) AND paymentStatus = ? ORDER by id DESC LIMIT 5");
                    $creditHistory->execute(array($readAccount["id"], 1, 2, 1));
                  ?>
                  <?php if ($creditHistory->rowCount() > 0): ?>
                    <div class="card mb-3">
                      <div class="card-header">
                        <div class="row">
                          <div class="col">
                            <span><?php echo $readSettings["creditText"] ?> Yükleme Geçmişi</span>
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
                              <th>Kullanıcı Adı</th>
                              <th class="text-center">Miktar</th>
                              <th class="text-center">Ödeme</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($creditHistory as $readCreditHistory): ?>
                              <tr>
                                <td class="text-center">
                                  <img class="rounded-circle" src="https://minotar.net/avatar/<?php echo $readAccount["realname"]; ?>/20.png" alt="<?php echo $serverName." Oyuncu - ".$readAccount["realname"]; ?>">
                                </td>
                                <td>
                                  <?php echo $readAccount["realname"]; ?>
                                </td>
                                <td class="text-center"><?php echo ($readCreditHistory["type"] == 3 || $readCreditHistory["type"] == 5) ? '<span class="text-danger">-'.$readCreditHistory["price"].'</span>' : '<span class="text-success">+'.$readCreditHistory["price"].'</span>'; ?></td>
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
                              </tr>
                            <?php endforeach; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  <?php else : ?>
                    <?php echo alertError($readSettings["creditText"] ." yükleme geçmişi bulunamadı!"); ?>
                  <?php endif; ?>
                </div>
                <div class="col-md-12">
                  <?php
                    $topCreditHistory = $db->prepare("SELECT SUM(CH.price) as totalPrice, COUNT(CH.id) as totalProcess, A.realname FROM CreditHistory CH INNER JOIN Accounts A ON CH.accountID = A.id WHERE CH.type IN (?, ?) AND CH.paymentStatus = ? AND CH.creationDate LIKE ? GROUP BY CH.accountID HAVING totalProcess > 0 ORDER BY totalPrice DESC LIMIT 5");
                    $topCreditHistory->execute(array(1, 2, 1, '%'.date("Y-m").'%'));
                  ?>
                  <?php if ($topCreditHistory->rowCount() > 0): ?>
                    <div class="card mb-3">
                      <div class="card-header">
                        <div class="row">
                          <div class="col">
                            <span>En Çok <?php echo $readSettings["creditText"] ?> Yükleyenler</span>
                          </div>
                          <div class="col-auto">
                            <span>(Bu Ay)</span>
                          </div>
                        </div>
                      </div>
                      <div class="card-body p-0">
                        <div class="table-responsive">
                          <table class="table table-hover">
                            <thead>
                            <tr>
                              <th class="text-center">#</th>
                              <th>Kullanıcı Adı</th>
                              <th class="text-center">Toplam</th>
                              <th class="text-center">Adet</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($topCreditHistory as $topCreditHistoryRead): ?>
                              <tr>
                                <td class="text-center">
                                  <img class="rounded-circle" src="https://minotar.net/avatar/<?php echo $topCreditHistoryRead["realname"]; ?>/20.png" alt="<?php echo $serverName." Oyuncu - ".$topCreditHistoryRead["realname"]; ?>">
                                </td>
                                <td>
                                  <a href="/oyuncu/<?php echo $topCreditHistoryRead["realname"]; ?>">
                                    <?php echo $topCreditHistoryRead["realname"]; ?>
                                  </a>
                                </td>
                                <td class="text-center"><?php echo $topCreditHistoryRead["totalPrice"] ?></td>
                                <td class="text-center"><?php echo $topCreditHistoryRead["totalProcess"] ?> kez</td>
                              </tr>
                            <?php endforeach; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
