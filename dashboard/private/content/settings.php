<?php
  if (!checkPerm($readAdmin, 'MANAGE_SETTINGS')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  if (get("target") == 'general' && get("action") == 'update') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/settings.general.js');
  }
  if (get("target") == 'system' && get("action") == 'update') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/settings.system.js');
  }
  if (get("target") == 'seo' && get("action") == 'update') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/settings.seo.js');
  }
  if (get("target") == 'smtp' && get("action") == 'update') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/settings.smtp.check.js');
  }
  if (get("target") == 'webhooks' && get("action") == 'update') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/settings.webhooks.js');
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/settings.webhooks.check.js');
  }
?>
<?php if (get("target") == 'general'): ?>
  <?php if (get("action") == 'update'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Genel Ayarlar</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Ayarlar</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Genel Ayarlar</li>
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
            if (isset($_POST["updateGeneralSettings"])) {
              $supportMessageTemplateCheckMessage = strpos($_POST["supportMessageTemplate"], "%message%");
              if (post("footerFacebook") == null) {
                $_POST["footerFacebook"] = '0';
              }
              if (post("footerTwitter") == null) {
                $_POST["footerTwitter"] = '0';
              }
              if (post("footerInstagram") == null) {
                $_POST["footerInstagram"] = '0';
              }
              if (post("footerYoutube") == null) {
                $_POST["footerYoutube"] = '0';
              }
              if (post("footerDiscord") == null) {
                $_POST["footerDiscord"] = '0';
              }
              if (post("footerEmail") == null) {
                $_POST["footerEmail"] = '0';
              }
              if (post("footerPhone") == null) {
                $_POST["footerPhone"] = '0';
              }
              if (post("footerWhatsapp") == null) {
                $_POST["footerWhatsapp"] = '0';
              }
              if (post("footerAboutText") == null) {
                $_POST["footerAboutText"] = '0';
              }
              if (!$csrf->validate('updateGeneralSettings')) {
                echo alertError("Sistemsel bir sorun oluştu!");
                echo goDelay("/yonetim-paneli/ayarlar/genel", 2);
              }
              else if (post("siteSlogan") == null || post("serverName") == null || post("serverIP") == null || post("supportMessageTemplate") == null) {
                echo alertError("Lütfen gerekli alanları doldurunuz!");
              }
              else if ($supportMessageTemplateCheckMessage == false) {
                echo alertError('Lütfen Destek Mesajı Şablonuna <strong>%message%</strong> parametresini ekleyiniz!');
              }
              else {
                if ($_FILES["lottery-bg"]["size"] != null) {
                  require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                  $upload = new \Verot\Upload\Upload($_FILES["lottery-bg"], "tr_TR");
                  if ($upload->uploaded) {
                    $upload->allowed = array("image/*");
                    $upload->file_overwrite = true;
                    $upload->file_new_name_body = "lottery-bg";
                    $upload->image_convert = "png";
                    $upload->image_resize = true;
                    $upload->image_ratio_crop = true;
                    $upload->image_x = 150;
                    $upload->image_y = 150;
                    $upload->process(__ROOT__."/apps/main/public/assets/img/extras/");
                    if (!$upload->processed) {
                      echo alertError("Çarkıfelek logosu yüklenirken bir hata oluştu: ".$upload->error);
                    }
                  }
                }
                if ($_FILES["favicon"]["size"] != null) {
                  require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                  $upload = new \Verot\Upload\Upload($_FILES["favicon"], "tr_TR");
                  if ($upload->uploaded) {
                    $upload->allowed = array("image/*");
                    $upload->file_overwrite = true;
                    $upload->file_new_name_body = "favicon";
                    $upload->image_convert = "png";
                    $upload->image_resize = true;
                    $upload->image_ratio_crop = true;
                    $upload->image_x = 64;
                    $upload->image_y = 64;
                    $upload->process(__ROOT__."/apps/main/public/assets/img/extras/");
                    if (!$upload->processed) {
                      echo alertError("Favicon yüklenirken bir hata oluştu: ".$upload->error);
                    }
                  }
                }
                if ($_FILES["logo"]["size"] != null) {
                  require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                  $upload = new \Verot\Upload\Upload($_FILES["logo"], "tr_TR");
                  if ($upload->uploaded) {
                    $upload->allowed = array("image/*");
                    $upload->file_overwrite = true;
                    $upload->file_new_name_body = "logo";
                    $upload->image_convert = "png";
                    $upload->process(__ROOT__."/apps/main/public/assets/img/extras/");
                    if (!$upload->processed) {
                      echo alertError("Logo yüklenirken bir hata oluştu: ".$upload->error);
                    }
                  }
                }

                $updateSettings = $db->prepare("UPDATE Settings SET siteSlogan = ?, serverName = ?, serverIP = ?, serverVersion = ?, siteTags = ?, siteDescription = ?, rules = ?, supportMessageTemplate = ?, footerFacebook = ?, footerTwitter = ?, footerInstagram = ?, footerYoutube = ?, footerDiscord = ?, footerEmail = ?, footerPhone = ?, footerWhatsapp = ?, footerAboutText = ?, headerLogoType = ?, updatedAt = ? WHERE id = ?");
                $updateSettings->execute(array(post("siteSlogan"), post("serverName"), post("serverIP"), post("serverVersion"), post("siteTags"), post("siteDescription"), filteredContent($_POST["rules"]), filteredContent($_POST["supportMessageTemplate"]), post("footerFacebook"), post("footerTwitter"), post("footerInstagram"), post("footerYoutube"), post("footerDiscord"), post("footerEmail"), post("footerPhone"), post("footerWhatsapp"), post("footerAboutText"), post("headerLogoType"), time(), $readSettings["id"]));
                echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                echo goDelay("/yonetim-paneli/ayarlar/genel", 2);
  
                createLog($readAdmin["id"], "GENERAL_SETTINGS_UPDATED");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group row">
                  <label for="inputServerName" class="col-sm-2 col-form-label">Sunucu Adı:*</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputServerName" class="form-control" name="serverName" placeholder="Sunucu adını yazınız." value="<?php echo $readSettings["serverName"]; ?>" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputSiteSlogan" class="col-sm-2 col-form-label">Site Başlığı:*</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputSiteSlogan" class="form-control" name="siteSlogan" placeholder="Site başlığını yazınız." value="<?php echo $readSettings["siteSlogan"]; ?>" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputServerIP" class="col-sm-2 col-form-label">Sunucu IP:*</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputServerIP" class="form-control" name="serverIP" placeholder="Sunucu IP adresini yazınız." value="<?php echo $readSettings["serverIP"]; ?>" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputServerVersion" class="col-sm-2 col-form-label">Sunucu Sürümü:*</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputServerVersion" class="form-control" name="serverVersion" placeholder="Sunucu sürümünü yazınız." value="<?php echo $readSettings["serverVersion"]; ?>" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputSiteTags" class="col-sm-2 col-form-label">Google Etiket:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputTags" class="form-control" data-toggle="tagsinput" name="siteTags" placeholder="Google etiketlerini yazınız." value="<?php echo $readSettings["siteTags"]; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaSiteDescription" class="col-sm-2 col-form-label">Google Açıklama:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaSiteDescription" class="form-control" name="siteDescription" maxlength="155" placeholder="Google aramasında çıkması istediğiniz açıklamayı yazınız." rows="5"><?php echo $readSettings["siteDescription"]; ?></textarea>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputFooterFacebook" class="col-sm-2 col-form-label">Facebook URL:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputFooterFacebook" class="form-control" name="footerFacebook" placeholder="Facebook sayfanızın URL adresini yazınız." value="<?php echo ($readSettings["footerFacebook"] != '0') ? $readSettings["footerFacebook"] : null; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputFooterTwitter" class="col-sm-2 col-form-label">Twitter URL:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputFooterTwitter" class="form-control" name="footerTwitter" placeholder="Twitter sayfanızın URL adresini yazınız." value="<?php echo ($readSettings["footerTwitter"] != '0') ? $readSettings["footerTwitter"] : null; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputFooterInstagram" class="col-sm-2 col-form-label">Instagram URL:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputFooterInstagram" class="form-control" name="footerInstagram" placeholder="Instagram sayfanızın URL adresini yazınız." value="<?php echo ($readSettings["footerInstagram"] != '0') ? $readSettings["footerInstagram"] : null; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputFooterYoutube" class="col-sm-2 col-form-label">YouTube URL:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputFooterYoutube" class="form-control" name="footerYoutube" placeholder="YouTube kanalınızın URL adresini yazınız." value="<?php echo ($readSettings["footerYoutube"] != '0') ? $readSettings["footerYoutube"] : null; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputFooterDiscord" class="col-sm-2 col-form-label">Discord URL:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputFooterDiscord" class="form-control" name="footerDiscord" placeholder="Discord kanalınızın davet URL adresini yazınız." value="<?php echo ($readSettings["footerDiscord"] != '0') ? $readSettings["footerDiscord"] : null; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputFooterEmail" class="col-sm-2 col-form-label">E-Posta:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputFooterEmail" class="form-control" name="footerEmail" placeholder="İletişim alanı için e-posta adresinizi yazınız." value="<?php echo ($readSettings["footerEmail"] != '0') ? $readSettings["footerEmail"] : null; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputFooterPhone" class="col-sm-2 col-form-label">Telefon Numarası:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputFooterPhone" class="form-control" name="footerPhone" placeholder="İletişim alanı için telefon numaranızı yazınız." value="<?php echo ($readSettings["footerPhone"] != '0') ? $readSettings["footerPhone"] : null; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputFooterWhatsapp" class="col-sm-2 col-form-label">WhatsApp:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputFooterWhatsapp" class="form-control" name="footerWhatsapp" placeholder="İletişim alanı için WhatsApp numaranızı yazınız." value="<?php echo ($readSettings["footerWhatsapp"] != '0') ? $readSettings["footerWhatsapp"] : null; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaFooterAboutText" class="col-sm-2 col-form-label">Hakkımızda:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaFooterAboutText" class="form-control" name="footerAboutText" maxlength="255" placeholder="Footerdaki hakkımızda yazısını yazınız." rows="5"><?php echo ($readSettings["footerAboutText"] != '0') ? $readSettings["footerAboutText"] : null; ?></textarea>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaRules" class="col-sm-2 col-form-label">Kurallar:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaRules" class="form-control" data-toggle="textEditor" name="rules" placeholder="Kurallar sayfasında gösterilen yazı."><?php echo $readSettings["rules"] ?></textarea>
                    <small class="form-text text-muted pt-2"><strong>Sunucu Adı:</strong> %servername%</small>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaSupportMessageTemplate" class="col-sm-2 col-form-label">Destek Mesajı Şablonu:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaSupportMessageTemplate" class="form-control" data-toggle="textEditor" name="supportMessageTemplate" placeholder="Destek mesajlarında yöneticilerin mesaj gönderirken hazır olarak gelen yazı şablonudur."><?php echo $readSettings["supportMessageTemplate"] ?></textarea>
                    <small class="form-text text-muted pt-2"><strong>NOT:</strong> %message% olmak zorundadır!</small>
                    <small class="form-text text-muted"><strong>Mesaj:</strong> %message%</small>
                    <small class="form-text text-muted"><strong>Kullanıcı Adı:</strong> %username%</small>
                    <small class="form-text text-muted"><strong>Sunucu Adı:</strong> %servername%</small>
                    <small class="form-text text-muted"><strong>Sunucu IP:</strong> %serverip%</small>
                    <small class="form-text text-muted"><strong>Sunucu Sürümü:</strong> %serverversion%</small>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="fileLottery" class="col-sm-2 col-form-label">Çarkıfelek Logo:</label>
                  <div class="col-sm-10">
                    <div data-toggle="dropimage" class="dropimage <?php echo (file_exists(__ROOT__."/apps/main/public/assets/img/extras/lottery-bg.png")) ? "active" : null; ?>">
                      <div class="di-thumbnail">
                        <img src="<?php echo (file_exists(__ROOT__."/apps/main/public/assets/img/extras/lottery-bg.png")) ? "/apps/main/public/assets/img/extras/lottery-bg.png?cache=".$readSettings["updatedAt"] : null; ?>" alt="Ön İzleme">
                      </div>
                      <div class="di-select">
                        <label for="fileLottery">Bir Resim Seçiniz</label>
                        <input type="file" id="fileLottery" name="lottery-bg" accept="image/*">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectHeaderLogoType" class="col-sm-2 col-form-label">Sunucu Logo (Header):</label>
                  <div class="col-sm-10">
                    <select id="selectHeaderLogoType" class="form-control" name="headerLogoType" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="1" <?php echo ($readSettings["headerLogoType"] == 1) ? 'selected="selected"' : null; ?>>Yazı</option>
                      <option value="2" <?php echo ($readSettings["headerLogoType"] == 2) ? 'selected="selected"' : null; ?>>Resim</option>
                    </select>
                  </div>
                </div>
                <div id="headerLogoOptions" style="<?php echo (($readSettings["headerLogoType"] == 1) ? "display: none;" : (($readSettings["headerLogoType"] == 2) ? "display: block;" : "display: none;")); ?>">
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div data-toggle="dropimage" class="dropimage <?php echo (file_exists(__ROOT__."/apps/main/public/assets/img/extras/logo.png")) ? "active" : null; ?>">
                        <div class="di-thumbnail">
                          <img src="<?php echo (file_exists(__ROOT__."/apps/main/public/assets/img/extras/logo.png")) ? "/apps/main/public/assets/img/extras/logo.png?cache=".$readSettings["updatedAt"] : null; ?>" alt="Ön İzleme">
                        </div>
                        <div class="di-select">
                          <label for="fileLogo">Bir Resim Seçiniz</label>
                          <input type="file" id="fileLogo" name="logo" accept="image/*">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="fileFavicon" class="col-sm-2 col-form-label">Sunucu Logo (Favicon):</label>
                  <div class="col-sm-10">
                    <div data-toggle="dropimage" class="dropimage <?php echo (file_exists(__ROOT__."/apps/main/public/assets/img/extras/favicon.png")) ? "active" : null; ?>">
                      <div class="di-thumbnail">
                        <img src="<?php echo (file_exists(__ROOT__."/apps/main/public/assets/img/extras/favicon.png")) ? "/apps/main/public/assets/img/extras/favicon.png?cache=".$readSettings["updatedAt"] : null; ?>" alt="Ön İzleme">
                      </div>
                      <div class="di-select">
                        <label for="fileFavicon">Bir Resim Seçiniz</label>
                        <input type="file" id="fileFavicon" name="favicon" accept="image/*">
                      </div>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('updateGeneralSettings'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="updateGeneralSettings">Değişiklikleri Kaydet</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'system'): ?>
  <?php if (get("action") == 'update'): ?>
    <?php
      $recaptchaPagesStatusJSON = $readSettings["recaptchaPagesStatus"];
      $recaptchaPagesStatus = json_decode($recaptchaPagesStatusJSON, true);
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Sistem Ayarları</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Ayarlar</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Sistem Ayarları</li>
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
            if (isset($_POST["updateSystemSettings"])) {
              if (post("analyticsStatus") == 0) {
                $_POST["analyticsUA"] = '0';
              }
              if (post("oneSignalStatus") == 0) {
                $_POST["oneSignalAppID"] = '0';
                $_POST["oneSignalAPIKey"] = '0';
              }
              if (post("tawktoStatus") == 0) {
                $_POST["tawktoID"] = '0';
              }
              if (post("bonusCreditStatus") == 0) {
                $_POST["bonusCredit"] = '0';
                $_POST["bonusCreditMinAmount"] = '0';
              }
              if (post("recaptchaStatus") == 0) {
                $_POST["recaptchaPublicKey"] = '0';
                $_POST["recaptchaPrivateKey"] = '0';
              }
              if (!$csrf->validate('updateSystemSettings')) {
                echo alertError("Sistemsel bir sorun oluştu!");
                echo goDelay("/yonetim-paneli/ayarlar/sistem", 2);
              }
              else if (post("creditPackageStatus") == null ||post("forumStatus") == null || post("gamingNightText") == null || post("bazaarStatus") == null || post("bazaarCommission") == null || post("authApi") == null || post("creditMultiplier") == null || post("debugModeStatus") == null || post("avatarAPI") == null || post("onlineAPI") == null || post("passwordType") == null || post("sslStatus") == null || post("maintenanceStatus") == null || post("creditStatus") == null || post("giftStatus") == null || post("topSalesStatus") == null || post("preloaderStatus") == null || post("authStatus") == null || post("commentsStatus") == null || post("analyticsStatus") == null || post("oneSignalStatus") == null || post("tawktoStatus") == null || post("bonusCreditStatus") == null || post("recaptchaStatus") == null || post("minPay") == null || post("maxPay") == null || post("newsLimit") == null || post("registerLimit") == null) {
                echo alertError("Lütfen gerekli alanları doldurunuz!");
              }
              else {
                if ($readSettings["creditMultiplier"] != post("creditMultiplier")) {
                  if ($readSettings["minPay"] == post("minPay"))
                    $_POST["minPay"] = 1 * post("creditMultiplier");
    
                  if ($readSettings["maxPay"] == post("maxPay"))
                    $_POST["maxPay"] = 100 * post("creditMultiplier");
                }
  
                if (post("updateProductPrices")) {
                  $products = $db->query("SELECT * FROM Products");
                  foreach ($products as $readProduct) {
                    $updateProduct = $db->prepare("UPDATE Products SET price = ?, discountedPrice = ? WHERE id = ?");
                    $updateProduct->execute(array($readProduct["price"] * post("creditMultiplier"), $readProduct["discountedPrice"] * post("creditMultiplier"), $readProduct["id"]));
                  }
                }
                $recaptchaPagesStatusArray = $recaptchaPagesStatus;
                $recaptchaPagesStatusArray["loginPage"] = $_POST["recaptchaPagesStatus"][0];
                $recaptchaPagesStatusArray["registerPage"] = $_POST["recaptchaPagesStatus"][1];
                $recaptchaPagesStatusArray["recoverPage"] = $_POST["recaptchaPagesStatus"][2];
                $recaptchaPagesStatusArray["newsPage"] = $_POST["recaptchaPagesStatus"][3];
                $recaptchaPagesStatusArray["supportPage"] = $_POST["recaptchaPagesStatus"][4];
                $recaptchaPagesStatusArray["tfaPage"] = $_POST["recaptchaPagesStatus"][5];
                $recaptchaPagesStatusJSON = json_encode($recaptchaPagesStatusArray);
                $updateSettings = $db->prepare("UPDATE Settings SET creditPackageStatus = ?, forumStatus = ?, gamingNightText = ?, bazaarStatus = ?, bazaarCommission = ?, authApi = ?, creditMultiplier = ?, debugModeStatus = ?, avatarAPI = ?, onlineAPI = ?, passwordType = ?, sslStatus = ?, maintenanceStatus = ?, creditStatus = ?, giftStatus = ?, topSalesStatus = ?, preloaderStatus = ?, authStatus = ?, commentsStatus = ?, analyticsUA = ?, oneSignalAppID = ?, oneSignalAPIKey = ?, tawktoID = ?, bonusCredit = ?, bonusCreditMinAmount = ?, recaptchaPagesStatus = ?, recaptchaPublicKey = ?, recaptchaPrivateKey = ?, minPay = ?, maxPay = ?, newsLimit = ?, registerLimit = ?, creditText = ? WHERE id = ?");
                $updateSettings->execute(array(post("creditPackageStatus"), post("forumStatus"), post("gamingNightText"), post("bazaarStatus"), post("bazaarCommission"), post("authApi"), post("creditMultiplier"), post("debugModeStatus"), post("avatarAPI"), post("onlineAPI"), post("passwordType"), post("sslStatus"), post("maintenanceStatus"), post("creditStatus"), post("giftStatus"), post("topSalesStatus"), post("preloaderStatus"), post("authStatus"), post("commentsStatus"), post("analyticsUA"), post("oneSignalAppID"), post("oneSignalAPIKey"), post("tawktoID"), post("bonusCredit"), post("bonusCreditMinAmount"), $recaptchaPagesStatusJSON, post("recaptchaPublicKey"), post("recaptchaPrivateKey"), post("minPay"), post("maxPay"), post("newsLimit"), post("registerLimit"), post("creditText"), $readSettings["id"]));
                echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                echo goDelay("/yonetim-paneli/ayarlar/sistem", 2);
                
                createLog($readAdmin["id"], "SYSTEM_SETTINGS_UPDATED");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="selectSSLStatus" class="col-sm-2 col-form-label">DEBUG Mod:*</label>
                  <div class="col-sm-10">
                    <select id="selectDebugModeStatus" class="form-control" name="debugModeStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["debugModeStatus"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["debugModeStatus"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectAvatarAPI" class="col-sm-2 col-form-label">Avatar API:*</label>
                  <div class="col-sm-10">
                    <select id="selectAvatarAPI" class="form-control" name="avatarAPI" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="1" <?php echo ($readSettings["avatarAPI"] == 1) ? 'selected="selected"' : null; ?>>minotar.net (Önerilen)</option>
                      <option value="2" <?php echo ($readSettings["avatarAPI"] == 2) ? 'selected="selected"' : null; ?>>cravatar.eu</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectOnlineAPI" class="col-sm-2 col-form-label">Online API:*</label>
                  <div class="col-sm-10">
                    <select id="selectOnlineAPI" class="form-control" name="onlineAPI" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="1" <?php echo ($readSettings["onlineAPI"] == 1) ? 'selected="selected"' : null; ?>>mcapi.us (Önerilen)</option>
                      <option value="2" <?php echo ($readSettings["onlineAPI"] == 2) ? 'selected="selected"' : null; ?>>mc-api.net</option>
                      <option value="3" <?php echo ($readSettings["onlineAPI"] == 3) ? 'selected="selected"' : null; ?>>mcapi.tc</option>
                      <option value="4" <?php echo ($readSettings["onlineAPI"] == 4) ? 'selected="selected"' : null; ?>>keyubu.net</option>
                      <option value="5" <?php echo ($readSettings["onlineAPI"] == 5) ? 'selected="selected"' : null; ?>>mcsrvstat.us</option>
                      <option value="6" <?php echo ($readSettings["onlineAPI"] == 6) ? 'selected="selected"' : null; ?>>mcsrvstat.us (Pocket Edition)</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectPasswordType" class="col-sm-2 col-form-label">Şifreleme Yöntemi:*</label>
                  <div class="col-sm-10">
                    <select id="selectPasswordType" class="form-control" name="passwordType" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="1" <?php echo ($readSettings["passwordType"] == 1) ? 'selected="selected"' : null; ?>>SHA256</option>
                      <option value="2" <?php echo ($readSettings["passwordType"] == 2) ? 'selected="selected"' : null; ?>>MD5</option>
                      <option value="3" <?php echo ($readSettings["passwordType"] == 3) ? 'selected="selected"' : null; ?>>BCRYPT</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectSSLStatus" class="col-sm-2 col-form-label">HTTPS Yönlendirme (SSL):*</label>
                  <div class="col-sm-10">
                    <select id="selectSSLStatus" class="form-control" name="sslStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["sslStatus"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["sslStatus"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectMaintenanceStatus" class="col-sm-2 col-form-label">Bakım Modu:*</label>
                  <div class="col-sm-10">
                    <select id="selectMaintenanceStatus" class="form-control" name="maintenanceStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["maintenanceStatus"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["maintenanceStatus"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectAuthApi" class="col-sm-2 col-form-label">Auth API:*</label>
                  <div class="col-sm-10">
                    <select id="selectAuthApi" class="form-control" name="authApi" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["authApi"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["authApi"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectCreditPackageStatus" class="col-sm-2 col-form-label">Kredi Paketi:*</label>
                  <div class="col-sm-10">
                    <select id="selectCreditPackageStatus" class="form-control" name="creditPackageStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["creditPackageStatus"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["creditPackageStatus"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectCreditStatus" class="col-sm-2 col-form-label">Oyuncular Arası Kredi Gönderme:*</label>
                  <div class="col-sm-10">
                    <select id="selectCreditStatus" class="form-control" name="creditStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["creditStatus"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["creditStatus"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectGiftStatus" class="col-sm-2 col-form-label">Oyuncular Arası Hediye Gönderme:*</label>
                  <div class="col-sm-10">
                    <select id="selectGiftStatus" class="form-control" name="giftStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["giftStatus"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["giftStatus"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectTopSalesStatus" class="col-sm-2 col-form-label">En Çok Satılan Ürünler:*</label>
                  <div class="col-sm-10">
                    <select id="selectTopSalesStatus" class="form-control" name="topSalesStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["topSalesStatus"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["topSalesStatus"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectPreloaderStatus" class="col-sm-2 col-form-label">Preloader:*</label>
                  <div class="col-sm-10">
                    <select id="selectPreloaderStatus" class="form-control" name="preloaderStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["preloaderStatus"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["preloaderStatus"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectBazaarStatus" class="col-sm-2 col-form-label">Oyuncu Pazarı:*</label>
                  <div class="col-sm-10">
                    <select id="selectBazaarStatus" class="form-control" name="bazaarStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["bazaarStatus"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["bazaarStatus"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputBazaarCommission" class="col-sm-2 col-form-label">Pazar Komisyonu (%):</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputBazaarCommission" class="form-control" name="bazaarCommission" placeholder="Satış yapan oyuncudan yüzde kaç komisyon alınacağını yazınız." value="<?php echo $readSettings["bazaarCommission"]; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectAuthStatus" class="col-sm-2 col-form-label">
                    2FA:*
                    <a href="https://egitim.leaderos.com.tr/google-authenticator" rel="external">
                      <i class="fa fa-question-circle text-primary" data-toggle="tooltip" data-placement="top" title="İki Adımlı Doğrulama"></i>
                    </a>
                  </label>
                  <div class="col-sm-10">
                    <select id="selectAuthStatus" class="form-control" name="authStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["authStatus"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["authStatus"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectCommentsStatus" class="col-sm-2 col-form-label">Yorumlar:*</label>
                  <div class="col-sm-10">
                    <select id="selectCommentsStatus" class="form-control" name="commentsStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["commentsStatus"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["commentsStatus"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectOneSignalStatus" class="col-sm-2 col-form-label">One Signal (Bildirimler):*</label>
                  <div class="col-sm-10">
                    <select id="selectOneSignalStatus" class="form-control" name="oneSignalStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["oneSignalAppID"] == '0' || $readSettings["oneSignalAPIKey"] == '0') ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["oneSignalAppID"] != '0' && $readSettings["oneSignalAPIKey"] != '0') ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div id="oneSignalOptions" style="<?php echo ($readSettings["oneSignalAppID"] == '0' || $readSettings["oneSignalAPIKey"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-group row">
                    <label for="inputOneSignalAppID" class="col-sm-2 col-form-label">One Signal App ID:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputOneSignalAppID" class="form-control" name="oneSignalAppID" placeholder="One Signal'den aldığınız App ID'yi giriniz." value="<?php echo ($readSettings["oneSignalAppID"] != '0' && $readSettings["oneSignalAPIKey"] != '0') ? $readSettings["oneSignalAppID"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputOneSignalAPIKey" class="col-sm-2 col-form-label">One Signal REST API Key:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputOneSignalAPIKey" class="form-control" name="oneSignalAPIKey" placeholder="One Signal'den aldığınız REST API Key'i giriniz." value="<?php echo ($readSettings["oneSignalAppID"] != '0' && $readSettings["oneSignalAPIKey"] != '0') ? $readSettings["oneSignalAPIKey"] : null; ?>">
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectTawktoStatus" class="col-sm-2 col-form-label">Tawk.to (Canlı Sohbet):*</label>
                  <div class="col-sm-10">
                    <select id="selectTawktoStatus" class="form-control" name="tawktoStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["tawktoID"] == '0') ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["tawktoID"] != '0') ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div id="tawktoOptions" style="<?php echo ($readSettings["tawktoID"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-group row">
                    <label for="inputTawktoID" class="col-sm-2 col-form-label">Tawkto Site ID:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputTawktoID" class="form-control" name="tawktoID" placeholder="Tawk.to'dan aldığınız Site ID'yi giriniz." value="<?php echo ($readSettings["tawktoID"] != '0') ? $readSettings["tawktoID"] : null; ?>">
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputCreditText" class="col-sm-2 col-form-label">Kredi Yazısı:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputCreditText" class="form-control" name="creditText" placeholder="Kredi yerine yazacak yazıyı değiştirebilirsiniz. Örnek: RC" value="<?php echo $readSettings["creditText"]; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputGamingNightText" class="col-sm-2 col-form-label">Gaming Gecesi Yazısı:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputGamingNightText" class="form-control" name="gamingNightText" placeholder="Gaming Gecesi yerine yazacak yazıyı değiştirebilirsiniz. Örnek: İndirimi Gecesi" value="<?php echo $readSettings["gamingNightText"]; ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectForumStatus" class="col-sm-2 col-form-label">Forum:*</label>
                  <div class="col-sm-10">
                    <select id="selectForumStatus" class="form-control" name="forumStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["forumStatus"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["forumStatus"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectBonusCreditStatus" class="col-sm-2 col-form-label">Bonus Kredi:*</label>
                  <div class="col-sm-10">
                    <select id="selectBonusCreditStatus" class="form-control" name="bonusCreditStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["bonusCredit"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["bonusCredit"] != 0) ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div id="bonusCreditOptions" style="<?php echo ($readSettings["bonusCredit"] == 0) ? "display: none;" : "display: block;"; ?>">
                  <div class="form-group row">
                    <label for="inputBonusCredit" class="col-sm-2 col-form-label">Bonus Kredi Yüzdesi (%):</label>
                    <div class="col-sm-10">
                      <input type="number" id="inputBonusCredit" class="form-control" name="bonusCredit" placeholder="Kullanıcıya ekstra olarak yüzde (%) kaç kredi verileceğini yazınız." value="<?php echo ($readSettings["bonusCredit"] != 0) ? $readSettings["bonusCredit"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputBonusCreditMinAmount" class="col-sm-2 col-form-label">Bonus Kredi için Min Yükleme Tutarı:</label>
                    <div class="col-sm-10">
                      <input type="number" id="inputBonusCreditMinAmount" class="form-control" name="bonusCreditMinAmount" placeholder="Bonus krediyi X kredi ve üzeri yüklemelerde verebilirsin. Tüm yüklemeler için 0 yazınız." value="<?php echo $readSettings["bonusCreditMinAmount"] == 0 ? null : $readSettings["bonusCreditMinAmount"]; ?>">
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputCreditMultiplier" class="col-sm-2 col-form-label">Kredi Çarpanı:</label>
                  <div class="col-sm-10">
                    <input type="number" id="inputCreditMultiplier" class="form-control" name="creditMultiplier" placeholder="Kredi çarpanı değerini giriniz." value="<?php echo $readSettings["creditMultiplier"]; ?>">
                    <small id="helpCreditMultiplier" class="form-text text-muted">Örnek: Çarpanı 100 yaparsanız 1TL ödemeye 100 kredi alır.</small>
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="updateProductPrices" name="updateProductPrices">
                      <label class="custom-control-label" for="updateProductPrices">
                        Ürün fiyatlarını çarpana göre güncelle
                      </label>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectAnalyticsStatus" class="col-sm-2 col-form-label">Google Analytics:*</label>
                  <div class="col-sm-10">
                    <select id="selectAnalyticsStatus" class="form-control" name="analyticsStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["analyticsUA"] == '0') ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["analyticsUA"] != '0') ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div id="analyticsOptions" style="<?php echo ($readSettings["analyticsUA"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-group row">
                    <label for="inputAnalyticsUA" class="col-sm-2 col-form-label">Google Analytics Kimlik:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputAnalyticsUA" class="form-control" name="analyticsUA" placeholder="Google Analytics sitesinden aldığınız mülk kimliğini giriniz. (Örn: UA-000001)" value="<?php echo ($readSettings["analyticsUA"] != '0') ? $readSettings["analyticsUA"] : null; ?>">
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectRECAPTCHAStatus" class="col-sm-2 col-form-label">
                    Google reCAPTCHA:*
                    <a href="https://egitim.leaderos.com.tr/google-recaptcha" rel="external">
                      <i class="fa fa-info-circle text-primary"></i>
                    </a>
                  </label>
                  <div class="col-sm-10">
                    <select id="selectRECAPTCHAStatus" class="form-control" name="recaptchaStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["recaptchaPublicKey"] == '0' || $readSettings["recaptchaPrivateKey"] == '0') ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["recaptchaPublicKey"] != '0' && $readSettings["recaptchaPrivateKey"] != '0') ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div id="recaptchaOptions" style="<?php echo ($readSettings["recaptchaPublicKey"] == '0' || $readSettings["recaptchaPrivateKey"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-group row">
                    <label for="inputRECAPTCHAPagesStatus" class="col-sm-2 col-form-label">reCAPTCHA Aktif Sayfalar:</label>
                    <div class="d-flex">
                      <label for="switchLoginPage" class="col-auto col-form-label">Giriş:</label>
                      <div class="col col-form-label">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="recaptchaPagesStatus[]">
                          <input type="checkbox" id="switchLoginPage" class="custom-control-input" <?php echo ($recaptchaPagesStatus["loginPage"] == 1) ? "checked" : null; ?>>
                          <label for="switchLoginPage" class="custom-control-label"></label>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <label for="switchRegisterPage" class="col-auto col-form-label">Kayıt:</label>
                      <div class="col col-form-label">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="recaptchaPagesStatus[]">
                          <input type="checkbox" id="switchRegisterPage" class="custom-control-input" <?php echo ($recaptchaPagesStatus["registerPage"] == 1) ? "checked" : null; ?>>
                          <label for="switchRegisterPage" class="custom-control-label"></label>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <label for="switchRecoverPage" class="col-auto col-form-label">Şifre Sıfırlama:</label>
                      <div class="col col-form-label">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="recaptchaPagesStatus[]">
                          <input type="checkbox" id="switchRecoverPage" class="custom-control-input" <?php echo ($recaptchaPagesStatus["recoverPage"] == 1) ? "checked" : null; ?>>
                          <label for="switchRecoverPage" class="custom-control-label"></label>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <label for="switchNewsPage" class="col-auto col-form-label">Haber:</label>
                      <div class="col col-form-label">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="recaptchaPagesStatus[]">
                          <input type="checkbox" id="switchNewsPage" class="custom-control-input" <?php echo ($recaptchaPagesStatus["newsPage"] == 1) ? "checked" : null; ?>>
                          <label for="switchNewsPage" class="custom-control-label"></label>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <label for="switchSupportPage" class="col-auto col-form-label">Destek:</label>
                      <div class="col col-form-label">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="recaptchaPagesStatus[]">
                          <input type="checkbox" id="switchSupportPage" class="custom-control-input" <?php echo ($recaptchaPagesStatus["supportPage"] == 1) ? "checked" : null; ?>>
                          <label for="switchSupportPage" class="custom-control-label"></label>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <label for="switchTFAPage" class="col-auto col-form-label">
                        2FA:
                        <a href="https://egitim.leaderos.com.tr/google-authenticator" rel="external">
                          <i class="fa fa-question-circle text-primary" data-toggle="tooltip" data-placement="top" title="İki Adımlı Doğrulama"></i>
                        </a>
                      </label>
                      <div class="col col-form-label">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="recaptchaPagesStatus[]">
                          <input type="checkbox" id="switchTFAPage" class="custom-control-input" <?php echo ($recaptchaPagesStatus["tfaPage"] == 1) ? "checked" : null; ?>>
                          <label for="switchTFAPage" class="custom-control-label"></label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputRECAPTCHAPublicKey" class="col-sm-2 col-form-label">reCAPTCHA Site Anahtarı:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputRECAPTCHAPublicKey" class="form-control" name="recaptchaPublicKey" placeholder="Google reCAPTCHA sitesinden aldığınız Site Anahtarı'nı giriniz." value="<?php echo ($readSettings["recaptchaPublicKey"] != '0') ? $readSettings["recaptchaPublicKey"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputRECAPTCHAPrivateKey" class="col-sm-2 col-form-label">reCAPTCHA Gizli Anahtar:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputRECAPTCHAPrivateKey" class="form-control" name="recaptchaPrivateKey" placeholder="Google reCAPTCHA sitesinden aldığınız Gizli Anahtar'ı giriniz." value="<?php echo ($readSettings["recaptchaPrivateKey"] != '0') ? $readSettings["recaptchaPrivateKey"] : null; ?>">
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputMinPay" class="col-sm-2 col-form-label">Minimum Ödeme Tutarı:</label>
                  <div class="col-sm-10">
                    <input type="number" id="inputMinPay" class="form-control" name="minPay" placeholder="Kredi yükleme ekranında kabul edilen minimum ödeme tutarını yazınız." value="<?php echo $readSettings["minPay"]; ?>" min="1" max="99999">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputMaxPay" class="col-sm-2 col-form-label">Maksimum Ödeme Tutarı:</label>
                  <div class="col-sm-10">
                    <input type="number" id="inputMaxPay" class="form-control" name="maxPay" placeholder="Kredi yükleme ekranında kabul edilen maksimum ödeme tutarını yazınız." value="<?php echo $readSettings["maxPay"]; ?>" min="1" max="99999">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectNewsLimit" class="col-sm-2 col-form-label">Haber Yazısı Limit (Her Sayfa):*</label>
                  <div class="col-sm-10">
                    <select id="selectNewsLimit" class="form-control" name="newsLimit" data-toggle="select" data-minimum-results-for-search="-1">
                      <?php
                        for ($i=1; $i <= 12; $i++) {
                          if ($i % 3 == 0) {
                            if ($readSettings["newsLimit"] == $i) {
                              echo '<option value="'.$i.'" selected>'.$i.'</option>';
                            }
                            else {
                              echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                          }
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectRegisterLimit" class="col-sm-2 col-form-label">Kayıt Limit:*</label>
                  <div class="col-sm-10">
                    <select id="selectRegisterLimit" class="form-control" name="registerLimit" data-toggle="select" data-minimum-results-for-search="-1">
                      <?php
                        for ($i=1; $i <= 3; $i++) {
                          if ($readSettings["registerLimit"] == $i) {
                            echo '<option value="'.$i.'" selected>'.$i.'</option>';
                          }
                          else {
                            echo '<option value="'.$i.'">'.$i.'</option>';
                          }
                        }
                        if ($readSettings["registerLimit"] == 0) {
                          echo '<option value="0" selected>Sınırsız</option>';
                        }
                        else {
                          echo '<option value="0">Sınırsız</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <?php echo $csrf->input('updateSystemSettings'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="updateSystemSettings">Değişiklikleri Kaydet</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'seo'): ?>
  <?php if (get("action") == 'update'): ?>
    <?php
      $seoPages = $db->query("SELECT * FROM SeoPages");
      $seoPageNames = [
          '404' => '404',
          'application' => 'Başvuru',
          'bazaar' => 'Pazar',
          'manage-bazaar' => 'Pazar Yönetimi',
          'checkout' => 'Sepet',
          'chest' => 'Sandık',
          'credit' => 'Kredi',
          'download' => 'İndir',
          'games' => 'Oyunlar',
          'gift' => 'Hediye',
          'help' => 'Yardım Merkezi',
          'home' => 'Anasayfa',
          'leaderboards' => 'Sıralama',
          'login' => 'Giriş',
          'lottery' => 'Çarkıfelek',
          'maintenance' => 'Bakım',
          'news' => 'Blog',
          'page' => 'Sayfa',
          'player' => 'Oyuncu',
          'profile' => 'Profil',
          'recover' => 'Şifremi Unuttum',
          'register' => 'Kayıt',
          'rules' => 'Kurallar',
          'store' => 'Mağaza',
          'support' => 'Destek',
          'tfa' => 'İki Adımlık Doğrulama',
          'tfa-recover' => 'İki Adımlık Doğrulama Sıfırlama',
          'gaming-night' => 'Gaming Gecesi',
          'forum' => 'Forum',
      ];
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">SEO Ayarları</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Ayarlar</a></li>
                      <li class="breadcrumb-item active" aria-current="page">SEO Ayarları</li>
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
            if (isset($_POST["updateSeoSettings"])) {
              if (!$csrf->validate('updateSeoSettings')) {
                echo alertError("Sistemsel bir sorun oluştu!");
                echo goDelay("/yonetim-paneli/ayarlar/seo", 2);
              }
              else {
                foreach ($_POST as $key => $value) {
                  $fields = explode('_', $key);
                  if (isset($fields[1])) {
                    $updateSettings = $db->prepare("UPDATE SeoPages SET $fields[1] = ? WHERE id = ?");
                    $updateSettings->execute(array(post($key), $fields[0]));
                  }
                }
                echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                echo goDelay("/yonetim-paneli/ayarlar/seo", 2);
                
                createLog($readAdmin["id"], "SEO_SETTINGS_UPDATED");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <div class="mb-4">
                <?php foreach ($seoPageNames as $key => $value): ?>
                    <button class="btn btn-light mr-1 mb-2 changeSeoPage" data-key="<?php echo $key; ?>">
                      <?php echo $value; ?>
                    </button>
                <?php endforeach; ?>
              </div>
              <form action="" method="post">
                <?php foreach ($seoPages as $readSeoPages): ?>
                  <div id="seoPageBlock_<?php echo $readSeoPages["page"]; ?>" style="display: none;">
                    <div class="form-group row">
                      <label for="inputTitle_<?php echo $readSeoPages["id"]; ?>" class="col-sm-2 col-form-label">Başlık:</label>
                      <div class="col-sm-10">
                        <input type="text" id="inputTitle_<?php echo $readSeoPages["id"]; ?>" class="form-control" name="<?php echo $readSeoPages["id"]; ?>_title" placeholder="Sayfa başlığını yazınız." value="<?php echo $readSeoPages["title"]; ?>" required>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputDesc_<?php echo $readSeoPages["id"]; ?>" class="col-sm-2 col-form-label">Açıklama (Opsiyonel):</label>
                      <div class="col-sm-10">
                        <textarea id="inputDesc_<?php echo $readSeoPages["id"]; ?>" class="form-control" name="<?php echo $readSeoPages["id"]; ?>_description" rows="3" placeholder="Sayfa açıklamasını yazınız."><?php echo $readSeoPages["description"]; ?></textarea>
                        <small class="form-text text-muted pt-2">Açıklamayı boş bırakırsanız varsayılan "Google Açıklama" değeri kullanılacaktır.</small>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputImage_<?php echo $readSeoPages["id"]; ?>" class="col-sm-2 col-form-label">Resim (Opsiyonel):</label>
                      <div class="col-sm-10">
                        <input type="text" id="inputImage_<?php echo $readSeoPages["id"]; ?>" class="form-control" name="<?php echo $readSeoPages["id"]; ?>_image" placeholder="Sayfa resim linkini yazınız." value="<?php echo $readSeoPages["image"]; ?>">
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
                
                <?php echo $csrf->input('updateSeoSettings'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="updateSeoSettings" style="display: none;">Değişiklikleri Kaydet</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'smtp'): ?>
  <?php if (get("action") == 'update'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">SMTP Ayarları</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Ayarlar</a></li>
                      <li class="breadcrumb-item active" aria-current="page">SMTP Ayarları</li>
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
            if (isset($_POST["updateSMTPSettings"])) {
              if (!$csrf->validate('updateSMTPSettings')) {
                echo alertError("Sistemsel bir sorun oluştu!");
                echo goDelay("/yonetim-paneli/ayarlar/smtp", 2);
              }
              else if (post("smtpServer") == null || post("smtpPort") == null || post("smtpSecure") == null || post("smtpUsername") == null || post("smtpPassword") == null || post("smtpPasswordTemplate") == null || post("smtpTFATemplate") == null) {
                echo alertError("Lütfen gerekli alanları doldurunuz!");
              }
              else {
                $updateSettings = $db->prepare("UPDATE Settings SET smtpServer = ?, smtpPort = ?, smtpSecure = ?, smtpUsername = ?, smtpPassword = ?, smtpPasswordTemplate = ?, smtpTFATemplate = ? WHERE id = ?");
                $updateSettings->execute(array(post("smtpServer"), post("smtpPort"), post("smtpSecure"), post("smtpUsername"), post("smtpPassword"), filteredContent($_POST["smtpPasswordTemplate"]), filteredContent($_POST["smtpTFATemplate"]), $readSettings["id"]));
                echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                echo goDelay("/yonetim-paneli/ayarlar/smtp", 2);
  
                createLog($readAdmin["id"], "SMTP_SETTINGS_UPDATED");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputSMTPServer" class="col-sm-2 col-form-label">SMTP Sunucu:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputSMTPServer" class="form-control" name="smtpServer" placeholder="SMTP sunucu adresini giriniz. (Örn: mail.leaderos.info)" value="<?php echo $readSettings["smtpServer"]; ?>" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputSMTPPort" class="col-sm-2 col-form-label">SMTP Port:</label>
                  <div class="col-sm-10">
                    <input type="number" id="inputSMTPPort" class="form-control" name="smtpPort" placeholder="SMTP sunucu adresini giriniz. (Örn: 587)" value="<?php echo $readSettings["smtpPort"]; ?>" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectSMTPSecure" class="col-sm-2 col-form-label">SMTP Güvenlik Seçeneği:</label>
                  <div class="col-sm-10">
                    <select id="selectSMTPSecure" class="form-control" name="smtpSecure" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="1" <?php echo ($readSettings["smtpSecure"] == 1) ? 'selected="selected"' : null; ?>>SSL</option>
                      <option value="2" <?php echo ($readSettings["smtpSecure"] == 2) ? 'selected="selected"' : null; ?>>TLS</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputSMTPUsername" class="col-sm-2 col-form-label">SMTP Email Adresi:</label>
                  <div class="col-sm-10">
                    <input type="email" id="inputSMTPUsername" class="form-control" name="smtpUsername" placeholder="SMTP email adresini giriniz. (Örn: destek@leaderos.info)" value="<?php echo $readSettings["smtpUsername"]; ?>" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputSMTPPassword" class="col-sm-2 col-form-label">SMTP Email Şifresi:</label>
                  <div class="col-sm-10">
                    <input type="password" id="inputSMTPPassword" class="form-control" name="smtpPassword" placeholder="SMTP email şifresini giriniz. (Örn: emailsifresi)" value="<?php echo $readSettings["smtpPassword"]; ?>" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaSMTPPasswordTemplate" class="col-sm-2 col-form-label">Şifre Sıfırlama E-Posta Şablonu:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaSMTPPasswordTemplate" class="form-control" data-toggle="textEditor" name="smtpPasswordTemplate" placeholder="Şifre sıfırlama için gönderilen e-postanın şablonudur."><?php echo $readSettings["smtpPasswordTemplate"] ?></textarea>
                    <small class="form-text text-muted pt-2"><strong>Kullanıcı Adı:</strong> %username%</small>
                    <small class="form-text text-muted"><strong>Şifre Sıfırlama Bağlantısı:</strong> %url%</small>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaSMTPTFATemplate" class="col-sm-2 col-form-label">TFA Sıfırlama E-Posta Şablonu:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaSMTPTFATemplate" class="form-control" data-toggle="textEditor" name="smtpTFATemplate" placeholder="TFA (İki Adımlı Doğrulama) sıfırlama için gönderilen e-postanın şablonudur."><?php echo $readSettings["smtpTFATemplate"] ?></textarea>
                    <small class="form-text text-muted pt-2"><strong>Kullanıcı Adı:</strong> %username%</small>
                    <small class="form-text text-muted"><strong>TFA Sıfırlama Bağlantısı:</strong> %url%</small>
                  </div>
                </div>
                <?php echo $csrf->input('updateSMTPSettings'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="button" id="testSMTP" class="btn btn-rounded btn-info">
                      <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                        <span class="sr-only">-/-</span>
                      </div>
                      <span>Bağlantıyı Kontrol Et</span>
                    </button>
                    <button type="submit" class="btn btn-rounded btn-success" name="updateSMTPSettings">Değişiklikleri Kaydet</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'webhooks'): ?>
  <?php if (get("action") == 'update'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Discord Webhook Ayarları</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Ayarlar</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Discord Webhook Ayarları</li>
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
            $webhookMessage       = "@everyone";
            $webhookCreditEmbed   = "**%username%** adlı kullanıcı **%credit% kredi** (%money% TL) yükledi.";
            $webhookStoreEmbed    = "**%username%** adlı kullanıcı **%server%** sunucusundan **%product%** ürününü satın aldı.";
            $webhookSupportEmbed  = "**%username%** adlı kullanıcı destek mesajı gönderdi.\n%panelurl%";
            $webhookNewsEmbed     = "**%username%** adlı kullanıcı habere yorum yaptı.\n%posturl%\n%panelurl%";
            $webhookLotteryEmbed  = "**%username%** adlı kullanıcı **%lottery%** adlı çarkıfelekten **%award%** adlı ödülü kazandı.";
            $webhookApplicationEmbed  = "**%username%** adlı kullanıcı **%form%** için başvuru yaptı.\n%panelurl%";
            if (isset($_POST["updateWebhookSettings"])) {
              $_POST["webhookCreditAdStatus"] = (post("webhookCreditAdStatus")) ? '1' : '0';
              $_POST["webhookStoreAdStatus"] = (post("webhookStoreAdStatus")) ? '1' : '0';
              $_POST["webhookSupportAdStatus"] = (post("webhookSupportAdStatus")) ? '1' : '0';
              $_POST["webhookNewsAdStatus"] = (post("webhookNewsAdStatus")) ? '1' : '0';
              $_POST["webhookLotteryAdStatus"] = (post("webhookLotteryAdStatus")) ? '1' : '0';
              $_POST["webhookApplicationAdStatus"] = (post("webhookApplicationAdStatus")) ? '1' : '0';

              if (post("webhookCreditStatus") == 0) {
                $_POST["webhookCreditURL"] = '0';
                $_POST["webhookCreditTitle"] = 'Kredi';
                $_POST["webhookCreditImage"] = '0';
                $_POST["webhookCreditColor"] = '000000';
                $_POST["webhookCreditAdStatus"] = '1';
                $_POST["webhookCreditMessage"] = $webhookMessage;
                $_POST["webhookCreditEmbed"] = $webhookCreditEmbed;
              }
              if (post("webhookCreditMessage") == null) {
                $_POST["webhookCreditMessage"] = '0';
              }
              if (post("webhookCreditImage") == null) {
                $_POST["webhookCreditImage"] = '0';
              }

              if (post("webhookStoreStatus") == 0) {
                $_POST["webhookStoreURL"] = '0';
                $_POST["webhookStoreTitle"] = 'Mağaza';
                $_POST["webhookStoreImage"] = '0';
                $_POST["webhookStoreColor"] = '000000';
                $_POST["webhookStoreAdStatus"] = '1';
                $_POST["webhookStoreMessage"] = $webhookMessage;
                $_POST["webhookStoreEmbed"] = $webhookStoreEmbed;
              }
              if (post("webhookStoreMessage") == null) {
                $_POST["webhookStoreMessage"] = '0';
              }
              if (post("webhookStoreImage") == null) {
                $_POST["webhookStoreImage"] = '0';
              }

              if (post("webhookSupportStatus") == 0) {
                $_POST["webhookSupportURL"] = '0';
                $_POST["webhookSupportTitle"] = 'Destek';
                $_POST["webhookSupportImage"] = '0';
                $_POST["webhookSupportColor"] = '000000';
                $_POST["webhookSupportAdStatus"] = '1';
                $_POST["webhookSupportMessage"] = $webhookMessage;
                $_POST["webhookSupportEmbed"] = $webhookSupportEmbed;
              }
              if (post("webhookSupportMessage") == null) {
                $_POST["webhookSupportMessage"] = '0';
              }
              if (post("webhookSupportImage") == null) {
                $_POST["webhookSupportImage"] = '0';
              }

              if (post("webhookNewsStatus") == 0) {
                $_POST["webhookNewsURL"] = '0';
                $_POST["webhookNewsTitle"] = 'Haberler';
                $_POST["webhookNewsImage"] = '0';
                $_POST["webhookNewsColor"] = '000000';
                $_POST["webhookNewsAdStatus"] = '1';
                $_POST["webhookNewsMessage"] = $webhookMessage;
                $_POST["webhookNewsEmbed"] = $webhookNewsEmbed;
              }
              if (post("webhookNewsMessage") == null) {
                $_POST["webhookNewsMessage"] = '0';
              }
              if (post("webhookNewsImage") == null) {
                $_POST["webhookNewsImage"] = '0';
              }

              if (post("webhookLotteryStatus") == 0) {
                $_POST["webhookLotteryURL"] = '0';
                $_POST["webhookLotteryTitle"] = 'Çarkıfelek';
                $_POST["webhookLotteryImage"] = '0';
                $_POST["webhookLotteryColor"] = '000000';
                $_POST["webhookLotteryAdStatus"] = '1';
                $_POST["webhookLotteryMessage"] = $webhookMessage;
                $_POST["webhookLotteryEmbed"] = $webhookLotteryEmbed;
              }
              if (post("webhookLotteryMessage") == null) {
                $_POST["webhookLotteryMessage"] = '0';
              }
              if (post("webhookLotteryImage") == null) {
                $_POST["webhookLotteryImage"] = '0';
              }
  
              if (post("webhookApplicationStatus") == 0) {
                $_POST["webhookApplicationURL"] = '0';
                $_POST["webhookApplicationTitle"] = 'Application';
                $_POST["webhookApplicationImage"] = '0';
                $_POST["webhookApplicationColor"] = '000000';
                $_POST["webhookApplicationAdStatus"] = '1';
                $_POST["webhookApplicationMessage"] = $webhookMessage;
                $_POST["webhookApplicationEmbed"] = $webhookApplicationEmbed;
              }
              if (post("webhookApplicationMessage") == null) {
                $_POST["webhookApplicationMessage"] = '0';
              }
              if (post("webhookApplicationImage") == null) {
                $_POST["webhookApplicationImage"] = '0';
              }

              if (!$csrf->validate('updateWebhookSettings')) {
                echo alertError("Sistemsel bir sorun oluştu!");
                echo goDelay("/yonetim-paneli/ayarlar/webhooks", 2);
              }
              else if (post("webhookCreditStatus") == null ||
              post("webhookCreditTitle") == null ||
              post("webhookCreditColor") == null ||
              post("webhookCreditImage") == null ||
              post("webhookCreditURL") == null ||
              post("webhookCreditMessage") == null ||
              post("webhookCreditEmbed") == null ||
              post("webhookCreditAdStatus") == null ||
              post("webhookStoreStatus") == null ||
              post("webhookStoreTitle") == null ||
              post("webhookStoreColor") == null ||
              post("webhookStoreImage") == null ||
              post("webhookStoreURL") == null ||
              post("webhookStoreMessage") == null ||
              post("webhookStoreEmbed") == null ||
              post("webhookStoreAdStatus") == null ||
              post("webhookSupportStatus") == null ||
              post("webhookSupportTitle") == null ||
              post("webhookSupportColor") == null ||
              post("webhookSupportImage") == null ||
              post("webhookSupportURL") == null ||
              post("webhookSupportMessage") == null ||
              post("webhookSupportEmbed") == null ||
              post("webhookSupportAdStatus") == null ||
              post("webhookNewsStatus") == null ||
              post("webhookNewsTitle") == null ||
              post("webhookNewsColor") == null ||
              post("webhookNewsImage") == null ||
              post("webhookNewsURL") == null ||
              post("webhookNewsMessage") == null ||
              post("webhookNewsEmbed") == null ||
              post("webhookNewsAdStatus") == null ||
              post("webhookLotteryStatus") == null ||
              post("webhookLotteryTitle") == null ||
              post("webhookLotteryColor") == null ||
              post("webhookLotteryImage") == null ||
              post("webhookLotteryURL") == null ||
              post("webhookLotteryMessage") == null ||
              post("webhookLotteryEmbed") == null ||
              post("webhookLotteryAdStatus") == null ||
              post("webhookApplicationStatus") == null ||
              post("webhookApplicationTitle") == null ||
              post("webhookApplicationColor") == null ||
              post("webhookApplicationImage") == null ||
              post("webhookApplicationURL") == null ||
              post("webhookApplicationMessage") == null ||
              post("webhookApplicationEmbed") == null ||
              post("webhookApplicationAdStatus") == null) {
                echo alertError("Lütfen gerekli alanları doldurunuz!");
              }
              else {
                $_POST["webhookCreditColor"] = ltrim($_POST["webhookCreditColor"], '#');
                $_POST["webhookStoreColor"] = ltrim($_POST["webhookStoreColor"], '#');
                $_POST["webhookSupportColor"] = ltrim($_POST["webhookSupportColor"], '#');
                $_POST["webhookNewsColor"] = ltrim($_POST["webhookNewsColor"], '#');
                $_POST["webhookLotteryColor"] = ltrim($_POST["webhookLotteryColor"], '#');
                $updateSettings = $db->prepare("UPDATE Settings SET webhookCreditTitle = ?, webhookCreditColor = ?, webhookCreditImage = ?, webhookCreditURL = ?, webhookCreditMessage = ?, webhookCreditEmbed = ?, webhookCreditAdStatus = ?, webhookStoreTitle = ?, webhookStoreColor = ?, webhookStoreImage = ?, webhookStoreURL = ?, webhookStoreMessage = ?, webhookStoreEmbed = ?, webhookStoreAdStatus = ?, webhookSupportTitle = ?, webhookSupportColor = ?, webhookSupportImage = ?, webhookSupportURL = ?, webhookSupportMessage = ?, webhookSupportEmbed = ?, webhookSupportAdStatus = ?, webhookNewsTitle = ?, webhookNewsColor = ?, webhookNewsImage = ?, webhookNewsURL = ?, webhookNewsMessage = ?, webhookNewsEmbed = ?, webhookNewsAdStatus = ?, webhookLotteryTitle = ?, webhookLotteryColor = ?, webhookLotteryImage = ?, webhookLotteryURL = ?, webhookLotteryMessage = ?, webhookLotteryEmbed = ?, webhookLotteryAdStatus = ?, webhookApplicationTitle = ?, webhookApplicationColor = ?, webhookApplicationImage = ?, webhookApplicationURL = ?, webhookApplicationMessage = ?, webhookApplicationEmbed = ?, webhookApplicationAdStatus = ? WHERE id = ?");
                $updateSettings->execute(array(post("webhookCreditTitle"), post("webhookCreditColor"), post("webhookCreditImage"), post("webhookCreditURL"), strip_tags($_POST["webhookCreditMessage"]), strip_tags($_POST["webhookCreditEmbed"]), post("webhookCreditAdStatus"), post("webhookStoreTitle"), post("webhookStoreColor"), post("webhookStoreImage"), post("webhookStoreURL"), strip_tags($_POST["webhookStoreMessage"]), strip_tags($_POST["webhookStoreEmbed"]), post("webhookStoreAdStatus"), post("webhookSupportTitle"), post("webhookSupportColor"), post("webhookSupportImage"), post("webhookSupportURL"), strip_tags($_POST["webhookSupportMessage"]), strip_tags($_POST["webhookSupportEmbed"]), post("webhookSupportAdStatus"), post("webhookNewsTitle"), post("webhookNewsColor"), post("webhookNewsImage"), post("webhookNewsURL"), strip_tags($_POST["webhookNewsMessage"]), strip_tags($_POST["webhookNewsEmbed"]), post("webhookNewsAdStatus"), post("webhookLotteryTitle"), post("webhookLotteryColor"), post("webhookLotteryImage"), post("webhookLotteryURL"), strip_tags($_POST["webhookLotteryMessage"]), strip_tags($_POST["webhookLotteryEmbed"]), post("webhookLotteryAdStatus"), post("webhookApplicationTitle"), post("webhookApplicationColor"), post("webhookApplicationImage"), post("webhookApplicationURL"), strip_tags($_POST["webhookApplicationMessage"]), strip_tags($_POST["webhookApplicationEmbed"]), post("webhookApplicationAdStatus"), $readSettings["id"]));
                echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                echo goDelay("/yonetim-paneli/ayarlar/webhooks", 2);
  
                createLog($readAdmin["id"], "WEBHOOK_SETTINGS_UPDATED");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="selectWebhookCreditStatus" class="col-sm-2 col-form-label">Kredi Yükleme:</label>
                  <div class="col-sm-10">
                    <select id="selectWebhookCreditStatus" class="form-control" name="webhookCreditStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["webhookCreditURL"] == '0') ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["webhookCreditURL"] != '0') ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div id="webhookCreditOptions" style="<?php echo ($readSettings["webhookCreditURL"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-row row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="form-group col-md-6">
                          <label for="inputWebhookCreditTitle">Başlık:</label>
                          <input type="text" name="webhookCreditTitle" id="inputWebhookCreditTitle" class="form-control" placeholder="Mesaj başlığını giriniz." value="<?php echo $readSettings["webhookCreditTitle"]; ?>">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputWebhookCreditColor">Renk:</label>
                          <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                            <input type="text" id="inputWebhookCreditColor" class="form-control form-control-appended" name="webhookCreditColor" placeholder="Renk kodunu yazınız." value="<?php echo ($readSettings["webhookCreditColor"] != null) ? "#".$readSettings["webhookCreditColor"] : "#000000"; ?>" required>
                            <div class="input-group-append">
                              <div class="input-group-text input-group-addon">
                                <i></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookCreditURL">Webhook URL:</label>
                      <input type="text" id="inputWebhookCreditURL" class="form-control" name="webhookCreditURL" placeholder="https://discordapp.com/api/webhooks/XXXXXXXXXXX/XXXXXXXXXXX" value="<?php echo ($readSettings["webhookCreditURL"] != '0') ? $readSettings["webhookCreditURL"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookCreditImage">Resim:</label>
                      <input type="text" id="inputWebhookCreditImage" class="form-control" name="webhookCreditImage" placeholder="Bir resim URL'si giriniz. (Boş bırakabilirsiniz)" value="<?php echo ($readSettings["webhookCreditImage"] != '0') ? $readSettings["webhookCreditImage"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookCreditMessage">Mesaj:</label>
                      <textarea id="inputWebhookCreditMessage" class="form-control" name="webhookCreditMessage" placeholder="Mesaj içeriğini giriniz." rows="2"><?php echo ($readSettings["webhookCreditMessage"] != '0') ? $readSettings["webhookCreditMessage"] : null; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong>Kullanıcı Adı:</strong> %username%</small>
                      <small class="form-text text-muted"><strong>Yüklenen Kredi (Bonus Kredi Dahil):</strong> %credit%</small>
                      <small class="form-text text-muted"><strong>Kazanılan Para (Bonus Kredi Dahil Değil):</strong> %money%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookCreditEmbed">Embed:</label>
                      <textarea id="inputWebhookCreditEmbed" class="form-control" name="webhookCreditEmbed" placeholder="Embed içeriğini giriniz." rows="2"><?php echo $readSettings["webhookCreditEmbed"]; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong>Kullanıcı Adı:</strong> %username%</small>
                      <small class="form-text text-muted"><strong>Yüklenen Kredi (Bonus Kredi Dahil):</strong> %credit%</small>
                      <small class="form-text text-muted"><strong>Kazanılan Para (Bonus Kredi Dahil Değil):</strong> %money%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="col">
                          <div id="testWebhookCredit">
                            <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                              <span class="sr-only">-/-</span>
                            </div>
                            <a href="javascript:void(0);">Test mesajı gönder</a>
                          </div>
                        </div>
                        <div class="col-auto">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="webhookCreditAdStatus" class="custom-control-input" id="checkboxWebhookCreditAdStatus" <?php echo ($readSettings["webhookCreditAdStatus"] == 1) ? 'checked="checked"' : null; ?>>
                            <label class="custom-control-label" for="checkboxWebhookCreditAdStatus">Powered by LeaderOS yazısı gözüksün</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="selectWebhookStoreStatus" class="col-sm-2 col-form-label">Mağaza Kullanımı:</label>
                  <div class="col-sm-10">
                    <select id="selectWebhookStoreStatus" class="form-control" name="webhookStoreStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["webhookStoreURL"] == '0') ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["webhookStoreURL"] != '0') ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div id="webhookStoreOptions" style="<?php echo ($readSettings["webhookStoreURL"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-row row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="form-group col-md-6">
                          <label for="inputWebhookStoreTitle">Başlık:</label>
                          <input type="text" name="webhookStoreTitle" id="inputWebhookStoreTitle" class="form-control" placeholder="Mesaj başlığını giriniz." value="<?php echo $readSettings["webhookStoreTitle"]; ?>">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputWebhookStoreColor">Renk:</label>
                          <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                            <input type="text" id="inputWebhookStoreColor" class="form-control form-control-appended" name="webhookStoreColor" placeholder="Renk kodunu yazınız." value="<?php echo ($readSettings["webhookStoreColor"] != null) ? "#".$readSettings["webhookStoreColor"] : "#000000"; ?>" required>
                            <div class="input-group-append">
                              <div class="input-group-text input-group-addon">
                                <i></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookStoreURL">Webhook URL:</label>
                      <input type="text" id="inputWebhookStoreURL" class="form-control" name="webhookStoreURL" placeholder="https://discordapp.com/api/webhooks/XXXXXXXXXXX/XXXXXXXXXXX" value="<?php echo ($readSettings["webhookStoreURL"] != '0') ? $readSettings["webhookStoreURL"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookStoreImage">Resim:</label>
                      <input type="text" id="inputWebhookStoreImage" class="form-control" name="webhookStoreImage" placeholder="Bir resim URL'si giriniz. (Boş bırakabilirsiniz)" value="<?php echo ($readSettings["webhookStoreImage"] != '0') ? $readSettings["webhookStoreImage"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookStoreMessage">Mesaj:</label>
                      <textarea id="inputWebhookStoreMessage" class="form-control" name="webhookStoreMessage" placeholder="Mesaj içeriğini giriniz." rows="2"><?php echo ($readSettings["webhookStoreMessage"] != '0') ? $readSettings["webhookStoreMessage"] : null; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong>Kullanıcı Adı:</strong> %username%</small>
                      <small class="form-text text-muted"><strong>Sunucu Adı:</strong> %server%</small>
                      <small class="form-text text-muted"><strong>Ürün Adı:</strong> %product%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookStoreEmbede">Embed:</label>
                      <textarea id="inputWebhookStoreEmbed" class="form-control" name="webhookStoreEmbed" placeholder="Embed içeriğini giriniz." rows="2"><?php echo $readSettings["webhookStoreEmbed"]; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong>Kullanıcı Adı:</strong> %username%</small>
                      <small class="form-text text-muted"><strong>Sunucu Adı:</strong> %server%</small>
                      <small class="form-text text-muted"><strong>Ürün Adı:</strong> %product%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="col">
                          <div id="testWebhookStore">
                            <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                              <span class="sr-only">-/-</span>
                            </div>
                            <a href="javascript:void(0);">Test mesajı gönder</a>
                          </div>
                        </div>
                        <div class="col-auto">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="webhookStoreAdStatus" class="custom-control-input" id="checkboxWebhookStoreAdStatus" <?php echo ($readSettings["webhookStoreAdStatus"] == 1) ? 'checked="checked"' : null; ?>>
                            <label class="custom-control-label" for="checkboxWebhookStoreAdStatus">Powered by LeaderOS yazısı gözüksün</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="selectWebhookSupportStatus" class="col-sm-2 col-form-label">Destek Mesajları:</label>
                  <div class="col-sm-10">
                    <select id="selectWebhookSupportStatus" class="form-control" name="webhookSupportStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["webhookSupportURL"] == '0') ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["webhookSupportURL"] != '0') ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div id="webhookSupportOptions" style="<?php echo ($readSettings["webhookSupportURL"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-row row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="form-group col-md-6">
                          <label for="inputWebhookSupportTitle">Başlık:</label>
                          <input type="text" name="webhookSupportTitle" id="inputWebhookSupportTitle" class="form-control" placeholder="Mesaj başlığını giriniz." value="<?php echo $readSettings["webhookSupportTitle"]; ?>">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputWebhookSupportColor">Renk:</label>
                          <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                            <input type="text" id="inputWebhookSupportColor" class="form-control form-control-appended" name="webhookSupportColor" placeholder="Renk kodunu yazınız." value="<?php echo ($readSettings["webhookSupportColor"] != null) ? "#".$readSettings["webhookSupportColor"] : "#000000"; ?>" required>
                            <div class="input-group-append">
                              <div class="input-group-text input-group-addon">
                                <i></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookSupportURL">Webhook URL:</label>
                      <input type="text" id="inputWebhookSupportURL" class="form-control" name="webhookSupportURL" placeholder="https://discordapp.com/api/webhooks/XXXXXXXXXXX/XXXXXXXXXXX" value="<?php echo ($readSettings["webhookSupportURL"] != '0') ? $readSettings["webhookSupportURL"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookSupportImage">Resim:</label>
                      <input type="text" id="inputWebhookSupportImage" class="form-control" name="webhookSupportImage" placeholder="Bir resim URL'si giriniz. (Boş bırakabilirsiniz)" value="<?php echo ($readSettings["webhookSupportImage"] != '0') ? $readSettings["webhookSupportImage"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookSupportMessage">Mesaj:</label>
                      <textarea id="inputWebhookSupportMessage" class="form-control" name="webhookSupportMessage" placeholder="Mesaj içeriğini giriniz." rows="2"><?php echo ($readSettings["webhookSupportMessage"] != '0') ? $readSettings["webhookSupportMessage"] : null; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong>Kullanıcı Adı:</strong> %username%</small>
                      <small class="form-text text-muted"><strong>Yönetim Paneli URL:</strong> %panelurl%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookSupportEmbed">Embed:</label>
                      <textarea id="inputWebhookSupportEmbed" class="form-control" name="webhookSupportEmbed" placeholder="Embed içeriğini giriniz." rows="2"><?php echo $readSettings["webhookSupportEmbed"]; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong>Kullanıcı Adı:</strong> %username%</small>
                      <small class="form-text text-muted"><strong>Yönetim Paneli URL:</strong> %panelurl%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="col">
                          <div id="testWebhookSupport">
                            <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                              <span class="sr-only">-/-</span>
                            </div>
                            <a href="javascript:void(0);">Test mesajı gönder</a>
                          </div>
                        </div>
                        <div class="col-auto">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="webhookSupportAdStatus" class="custom-control-input" id="checkboxWebhookSupportAdStatus" <?php echo ($readSettings["webhookSupportAdStatus"] == 1) ? 'checked="checked"' : null; ?>>
                            <label class="custom-control-label" for="checkboxWebhookSupportAdStatus">Powered by LeaderOS yazısı gözüksün</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="selectWebhookNewsStatus" class="col-sm-2 col-form-label">Yorumlar:</label>
                  <div class="col-sm-10">
                    <select id="selectWebhookNewsStatus" class="form-control" name="webhookNewsStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["webhookNewsURL"] == '0') ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["webhookNewsURL"] != '0') ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div id="webhookNewsOptions" style="<?php echo ($readSettings["webhookNewsURL"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-row row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="form-group col-md-6">
                          <label for="inputWebhookNewsTitle">Başlık:</label>
                          <input type="text" name="webhookNewsTitle" id="inputWebhookNewsTitle" class="form-control" placeholder="Mesaj başlığını giriniz." value="<?php echo $readSettings["webhookNewsTitle"]; ?>">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputWebhookNewsColor">Renk:</label>
                          <div id="colorPicker" id="inputWebhookNewsColor" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                            <input type="text" class="form-control form-control-appended" name="webhookNewsColor" placeholder="Renk kodunu yazınız." value="<?php echo ($readSettings["webhookNewsColor"] != null) ? "#".$readSettings["webhookNewsColor"] : "#000000"; ?>" required>
                            <div class="input-group-append">
                              <div class="input-group-text input-group-addon">
                                <i></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookNewsURL">Webhook URL:</label>
                      <input type="text" id="inputWebhookNewsURL" class="form-control" name="webhookNewsURL" placeholder="https://discordapp.com/api/webhooks/XXXXXXXXXXX/XXXXXXXXXXX" value="<?php echo ($readSettings["webhookNewsURL"] != '0') ? $readSettings["webhookNewsURL"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookNewsImage">Resim:</label>
                      <input type="text" id="inputWebhookNewsImage" class="form-control" name="webhookNewsImage" placeholder="Bir resim URL'si giriniz. (Boş bırakabilirsiniz)" value="<?php echo ($readSettings["webhookNewsImage"] != '0') ? $readSettings["webhookNewsImage"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookNewsMessage">Mesaj:</label>
                      <textarea id="inputWebhookNewsMessage" class="form-control" name="webhookNewsMessage" placeholder="Mesaj içeriğini giriniz." rows="2"><?php echo ($readSettings["webhookNewsMessage"] != '0') ? $readSettings["webhookNewsMessage"] : null; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong>Kullanıcı Adı:</strong> %username%</small>
                      <small class="form-text text-muted"><strong>Yönetim Paneli URL:</strong> %panelurl%</small>
                      <small class="form-text text-muted"><strong>Haber URL:</strong> %posturl%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookNewsEmbed">Embed:</label>
                      <textarea id="inputWebhookNewsEmbed" class="form-control" name="webhookNewsEmbed" placeholder="Embed içeriğini giriniz." rows="2"><?php echo $readSettings["webhookNewsEmbed"]; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong>Kullanıcı Adı:</strong> %username%</small>
                      <small class="form-text text-muted"><strong>Yönetim Paneli URL:</strong> %panelurl%</small>
                      <small class="form-text text-muted"><strong>Haber URL:</strong> %posturl%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="col">
                          <div id="testWebhookNews">
                            <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                              <span class="sr-only">-/-</span>
                            </div>
                            <a href="javascript:void(0);">Test mesajı gönder</a>
                          </div>
                        </div>
                        <div class="col-auto">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="webhookNewsAdStatus" class="custom-control-input" id="checkboxWebhookNewsAdStatus" <?php echo ($readSettings["webhookNewsAdStatus"] == 1) ? 'checked="checked"' : null; ?>>
                            <label class="custom-control-label" for="checkboxWebhookNewsAdStatus">Powered by LeaderOS yazısı gözüksün</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="selectWebhookLotteryStatus" class="col-sm-2 col-form-label">Çarkıfelek:</label>
                  <div class="col-sm-10">
                    <select id="selectWebhookLotteryStatus" class="form-control" name="webhookLotteryStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["webhookLotteryURL"] == '0') ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["webhookLotteryURL"] != '0') ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div id="webhookLotteryOptions" style="<?php echo ($readSettings["webhookLotteryURL"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-row row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="form-group col-md-6">
                          <label for="inputWebhookLotteryTitle">Başlık:</label>
                          <input type="text" name="webhookLotteryTitle" id="inputWebhookLotteryTitle" class="form-control" placeholder="Mesaj başlığını giriniz." value="<?php echo $readSettings["webhookLotteryTitle"]; ?>">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputWebhookLotteryColor">Renk:</label>
                          <div id="colorPicker" id="inputWebhookLotteryColor" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                            <input type="text" class="form-control form-control-appended" name="webhookLotteryColor" placeholder="Renk kodunu yazınız." value="<?php echo ($readSettings["webhookLotteryColor"] != null) ? "#".$readSettings["webhookLotteryColor"] : "#000000"; ?>" required>
                            <div class="input-group-append">
                              <div class="input-group-text input-group-addon">
                                <i></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookLotteryURL">Webhook URL:</label>
                      <input type="text" id="inputWebhookLotteryURL" class="form-control" name="webhookLotteryURL" placeholder="https://discordapp.com/api/webhooks/XXXXXXXXXXX/XXXXXXXXXXX" value="<?php echo ($readSettings["webhookLotteryURL"] != '0') ? $readSettings["webhookLotteryURL"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookLotteryImage">Resim:</label>
                      <input type="text" id="inputWebhookLotteryImage" class="form-control" name="webhookLotteryImage" placeholder="Bir resim URL'si giriniz. (Boş bırakabilirsiniz)" value="<?php echo ($readSettings["webhookLotteryImage"] != '0') ? $readSettings["webhookLotteryImage"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookLotteryMessage">Mesaj:</label>
                      <textarea id="inputWebhookLotteryMessage" class="form-control" name="webhookLotteryMessage" placeholder="Mesaj içeriğini giriniz." rows="2"><?php echo ($readSettings["webhookLotteryMessage"] != '0') ? $readSettings["webhookLotteryMessage"] : null; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong>Kullanıcı Adı:</strong> %username%</small>
                      <small class="form-text text-muted"><strong>Çarkıfelek Adı:</strong> %lottery%</small>
                      <small class="form-text text-muted"><strong>Ödül:</strong> %award%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookLotteryEmbed">Embed:</label>
                      <textarea id="inputWebhookLotteryEmbed" class="form-control" name="webhookLotteryEmbed" placeholder="Embed içeriğini giriniz." rows="2"><?php echo $readSettings["webhookLotteryEmbed"]; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong>Kullanıcı Adı:</strong> %username%</small>
                      <small class="form-text text-muted"><strong>Çarkıfelek Adı:</strong> %lottery%</small>
                      <small class="form-text text-muted"><strong>Ödül:</strong> %award%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="col">
                          <div id="testWebhookLottery">
                            <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                              <span class="sr-only">-/-</span>
                            </div>
                            <a href="javascript:void(0);">Test mesajı gönder</a>
                          </div>
                        </div>
                        <div class="col-auto">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="webhookLotteryAdStatus" class="custom-control-input" id="checkboxWebhookLotteryAdStatus" <?php echo ($readSettings["webhookLotteryAdStatus"] == 1) ? 'checked="checked"' : null; ?>>
                            <label class="custom-control-label" for="checkboxWebhookLotteryAdStatus">Powered by LeaderOS yazısı gözüksün</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectWebhookApplicationStatus" class="col-sm-2 col-form-label">Yetkili Başvuru:</label>
                  <div class="col-sm-10">
                    <select id="selectWebhookApplicationStatus" class="form-control" name="webhookApplicationStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readSettings["webhookApplicationURL"] == '0') ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readSettings["webhookApplicationURL"] != '0') ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div id="webhookApplicationOptions" style="<?php echo ($readSettings["webhookApplicationURL"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-row row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="form-group col-md-6">
                          <label for="inputWebhookApplicationTitle">Başlık:</label>
                          <input type="text" name="webhookApplicationTitle" id="inputWebhookApplicationTitle" class="form-control" placeholder="Mesaj başlığını giriniz." value="<?php echo $readSettings["webhookApplicationTitle"]; ?>">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputWebhookApplicationColor">Renk:</label>
                          <div id="colorPicker" id="inputWebhookApplicationColor" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                            <input type="text" class="form-control form-control-appended" name="webhookApplicationColor" placeholder="Renk kodunu yazınız." value="<?php echo ($readSettings["webhookApplicationColor"] != null) ? "#".$readSettings["webhookApplicationColor"] : "#000000"; ?>" required>
                            <div class="input-group-append">
                              <div class="input-group-text input-group-addon">
                                <i></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookApplicationURL">Webhook URL:</label>
                      <input type="text" id="inputWebhookApplicationURL" class="form-control" name="webhookApplicationURL" placeholder="https://discordapp.com/api/webhooks/XXXXXXXXXXX/XXXXXXXXXXX" value="<?php echo ($readSettings["webhookApplicationURL"] != '0') ? $readSettings["webhookApplicationURL"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookApplicationImage">Resim:</label>
                      <input type="text" id="inputWebhookApplicationImage" class="form-control" name="webhookApplicationImage" placeholder="Bir resim URL'si giriniz. (Boş bırakabilirsiniz)" value="<?php echo ($readSettings["webhookApplicationImage"] != '0') ? $readSettings["webhookApplicationImage"] : null; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookApplicationMessage">Mesaj:</label>
                      <textarea id="inputWebhookApplicationMessage" class="form-control" name="webhookApplicationMessage" placeholder="Mesaj içeriğini giriniz." rows="2"><?php echo ($readSettings["webhookApplicationMessage"] != '0') ? $readSettings["webhookApplicationMessage"] : null; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong>Kullanıcı Adı:</strong> %username%</small>
                      <small class="form-text text-muted"><strong>Başvuru Formu:</strong> %form%</small>
                      <small class="form-text text-muted"><strong>Yönetim Paneli URL:</strong> %panelurl%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <label for="inputWebhookApplicationEmbed">Embed:</label>
                      <textarea id="inputWebhookApplicationEmbed" class="form-control" name="webhookApplicationEmbed" placeholder="Embed içeriğini giriniz." rows="2"><?php echo $readSettings["webhookApplicationEmbed"]; ?></textarea>
                      <small class="form-text text-muted pt-2"><strong>Kullanıcı Adı:</strong> %username%</small>
                      <small class="form-text text-muted"><strong>Başvuru Formu:</strong> %form%</small>
                      <small class="form-text text-muted"><strong>Yönetim Paneli URL:</strong> %panelurl%</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                      <div class="row">
                        <div class="col">
                          <div id="testWebhookApplication">
                            <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                              <span class="sr-only">-/-</span>
                            </div>
                            <a href="javascript:void(0);">Test mesajı gönder</a>
                          </div>
                        </div>
                        <div class="col-auto">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="webhookApplicationAdStatus" class="custom-control-input" id="checkboxWebhookApplicationAdStatus" <?php echo ($readSettings["webhookApplicationAdStatus"] == 1) ? 'checked="checked"' : null; ?>>
                            <label class="custom-control-label" for="checkboxWebhookApplicationAdStatus">Powered by LeaderOS yazısı gözüksün</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('updateWebhookSettings'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="updateWebhookSettings">Değişiklikleri Kaydet</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'language'): ?>
  <?php if (get("action") == 'update'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Dil Ayarları</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Ayarlar</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Dil Ayarları</li>
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
            if (isset($_POST["updateLanguageSettings"])) {
              if (!$csrf->validate('updateLanguageSettings')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("languageID") == null) {
                echo alertError("Lütfen gerekli alanları doldurunuz!");
              }
              else {
                $updateSettings = $db->prepare("UPDATE Settings SET languageID = ? WHERE id = ?");
                $updateSettings->execute(array(post("languageID"), $readSettings["id"]));
                echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="selectLanguageID" class="col-sm-2 col-form-label">Dil:</label>
                  <div class="col-sm-10">
                    <select id="selectLanguageID" class="form-control" name="languageID" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="1" <?php echo ($readSettings["languageID"] == 1) ? 'selected="selected"' : null; ?>>English</option>
                      <option value="2" <?php echo ($readSettings["languageID"] == 2) ? 'selected="selected"' : null; ?>>Türkçe</option>
                    </select>
                  </div>
                </div>
                <?php echo $csrf->input('updateLanguageSettings'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="updateLanguageSettings">Değişiklikleri Kaydet</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
