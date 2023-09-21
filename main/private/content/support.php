<?php
  if (!isset($_SESSION["login"])) {
    go("/giris-yap");
  }
  use Phelium\Component\reCAPTCHA;
  if (get("target") == 'support' && (get("action") == 'insert' || get("action") == 'get')) {
    require_once(__ROOT__.'/apps/main/private/packages/class/extraresources/extraresources.php');
    $extraResourcesJS = new ExtraResources('js');
    $extraResourcesJS->addResource('/apps/main/public/assets/js/support.js');
    $extraResourcesJS->addResource('/apps/main/public/assets/js/plugins/ckeditor/translations/tr.js');
    $extraResourcesJS->addResource('/apps/main/public/assets/js/plugins/ckeditor/ckeditor.js');
    $extraResourcesJS->addResource('/apps/main/public/assets/js/ckeditor5.js');
    if (get("action") == 'insert') {
      $extraResourcesJS->addResource('/apps/main/public/assets/js/support.open.js');
    }
    $recaptchaPagesStatusJSON = $readSettings["recaptchaPagesStatus"];
    $recaptchaPagesStatus = json_decode($recaptchaPagesStatusJSON, true);
    $recaptchaStatus = $readSettings["recaptchaPublicKey"] != '0' && $readSettings["recaptchaPrivateKey"] != '0' && $recaptchaPagesStatus["supportPage"] == 1;
    if ($recaptchaStatus) {
      require_once(__ROOT__.'/apps/main/private/packages/class/recaptcha/recaptcha.php');
      $reCAPTCHA = new reCAPTCHA($readSettings["recaptchaPublicKey"], $readSettings["recaptchaPrivateKey"]);
      $reCAPTCHA->setRemoteIp(getIP());
      $reCAPTCHA->setLanguage("tr");
      $reCAPTCHA->setTheme(($readTheme["recaptchaThemeID"] == 1) ? "light" : (($readTheme["recaptchaThemeID"] == 2) ? "dark" : "light"));
      $extraResourcesJS->addResource($reCAPTCHA->getScriptURL(), true, true);
    }
  }
?>
<style>
  .ck-editor__editable {
    min-height: 150px !important;
  }
</style>
<section class="section support-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="/destek">Destek</a></li>
            <?php if (get("target") == 'support'): ?>
              <?php if (get("action") == 'getAll'): ?>
                <li class="breadcrumb-item active" aria-current="page">Destek Bildirimleri</li>
              <?php elseif (get("action") == 'insert'): ?>
                <li class="breadcrumb-item active" aria-current="page">Gönder</li>
              <?php elseif (get("action") == 'get'): ?>
                <li class="breadcrumb-item active" aria-current="page">Görüntüle</li>
              <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page">Hata!</li>
              <?php endif; ?>
            <?php else: ?>
              <li class="breadcrumb-item active" aria-current="page">Destek</li>
            <?php endif; ?>
          </ol>
        </nav>
      </div>
      <div class="col-md-8">
        <?php if (get("target") == 'support'): ?>
          <?php if (get("action") == 'getAll'): ?>
            <?php
              $supports = $db->prepare("SELECT S.*, SC.name as categoryName, Se.name as serverName FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id WHERE S.accountID = ? ORDER BY S.updateDate DESC");
              $supports->execute(array($readAccount["id"]));
            ?>
            <a class="btn btn-success w-100 mb-3" href="/destek/gonder">Destek Mesajı Gönder</a>
            <?php if ($supports->rowCount() > 0): ?>
              <div class="card">
                <div class="card-header">
                  Destek Mesajları
                </div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th class="text-center" style="width: 40px;">#ID</th>
                          <th>Başlık</th>
                          <th>Kategori</th>
                          <th>Son Güncelleme</th>
                          <th class="text-center">Durum</th>
                          <th class="text-center">İşlem</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($supports as $readSupports): ?>
                          <tr>
                            <td class="text-center" style="width: 40px;">
                              <a href="/destek/goruntule/<?php echo $readSupports["id"]; ?>">
                                #<?php echo $readSupports["id"]; ?>
                              </a>
                            </td>
                            <td>
                              <a href="/destek/goruntule/<?php echo $readSupports["id"]; ?>">
                                <?php echo $readSupports["title"]; ?>
                              </a>
                            </td>
                            <td><?php echo $readSupports["categoryName"]; ?></td>
                            <td><?php echo convertTime($readSupports["updateDate"]); ?></td>
                            <td class="text-center">
                              <?php if ($readSupports["statusID"] == 1): ?>
                                <span class="badge badge-pill badge-danger">Cevaplanmadı</span>
                              <?php elseif ($readSupports["statusID"] == 2): ?>
                                <span class="badge badge-pill badge-success">Cevaplandı</span>
                              <?php elseif ($readSupports["statusID"] == 3): ?>
                                <span class="badge badge-pill badge-warning">Kullanıcı Yanıtı</span>
                              <?php elseif ($readSupports["statusID"] == 4): ?>
                                <span class="badge badge-pill badge-danger">Kapatıldı</span>
                              <?php else: ?>
                                <span class="badge badge-pill badge-danger">HATA!</span>
                              <?php endif; ?>
                            </td>
                            <td class="text-center">
                              <a class="btn btn-success btn-circle" href="/destek/goruntule/<?php echo $readSupports["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Mesajı Oku">
                                <i class="fa fa-eye"></i>
                              </a>
                              <a class="btn btn-danger btn-circle clickdelete" href="/destek/sil/<?php echo $readSupports["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Mesajı Sil">
                                <i class="fa fa-trash"></i>
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
              <?php echo alertError("Daha önce hiç destek mesajı göndermemişsiniz."); ?>
            <?php endif; ?>
          <?php elseif (get("action") == 'insert'): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["insertSupports"])) {
                if (!$csrf->validate('sendSupportMessage')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if ($recaptchaStatus && post("g-recaptcha-response") == null) {
                  echo alertError("Robot olmadığınızı doğrulayın!");
                }
                else if ($recaptchaStatus && !$reCAPTCHA->isValid(post("g-recaptcha-response"))) {
                  // Hata Tespit
                  //var_dump($reCAPTCHA->getErrorCodes());
                  echo alertError("Spam işlem tespit edildi!");
                }
                else if ((post("title") == null) || (post("serverID") == null) || (post("categoryID") == null) || (post("message") == null)) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  $supportBannedStatus = $db->prepare("SELECT * FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?)");
                  $supportBannedStatus->execute(array($readAccount["id"], 2, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
                  if ($supportBannedStatus->rowCount() > 0) {
                    echo alertError("Destek bölümünden engellendiğiniz için mesaj gönderemezsiniz!");
                  }
                  else {
                    $insertSupports = $db->prepare("INSERT INTO Supports (title, accountID, message, serverID, categoryID, statusID, readStatus, updateDate, creationDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insertSupports->execute(array(post("title"), $readAccount["id"], post("message"), post("serverID"), post("categoryID"), 1, 1, date("Y-m-d H:i:s"), date("Y-m-d H:i:s")));
                    $notificationsVariables = $db->lastInsertId();
                    $insertNotifications = $db->prepare("INSERT INTO Notifications (accountID, type, variables, creationDate) VALUES (?, ?, ?, ?)");
                    $insertNotifications->execute(array($readAccount["id"], 1, $notificationsVariables, date("Y-m-d H:i:s")));

                    $websiteURL = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"]);
                    if ($readSettings["webhookSupportURL"] != '0') {
                      require_once(__ROOT__."/apps/main/private/packages/class/webhook/webhook.php");
                      $search = array("%username%", "%panelurl%");
                      $replace = array($readAccount["realname"], "$websiteURL/yonetim-paneli/destek/goruntule/$notificationsVariables");
                      $webhookMessage = $readSettings["webhookSupportMessage"];
                      $webhookEmbed = $readSettings["webhookSupportEmbed"];
                      $postFields = (array(
                        'content'     => ($webhookMessage != '0') ? str_replace($search, $replace, $webhookMessage) : null,
                        'avatar_url'  => 'https://minotar.net/avatar/'.$readAccount["realname"].'/256.png',
                        'tts'         => false,
                        'embeds'      => array(
                          array(
                            'type'        => 'rich',
                            'title'       => $readSettings["webhookSupportTitle"],
                            'color'       => hexdec($readSettings["webhookSupportColor"]),
                            'description' => str_replace($search, $replace, $webhookEmbed),
                            'image'       => array(
                              'url' => ($readSettings["webhookSupportImage"] != '0') ? $readSettings["webhookSupportImage"] : null
                            ),
                            'footer'      =>
                            ($readSettings["webhookSupportAdStatus"] == 1) ? array(
                              'text'      => 'Powered by LeaderOS',
                              'icon_url'  => 'https://i.ibb.co/wNHKQ7B/leaderos-logo.png'
                            ) : array()
                          )
                        )
                      ));
                      $curl = new \LeaderOS\Http\Webhook($readSettings["webhookSupportURL"]);
                      $curl(json_encode($postFields, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                    }

                    if ($readSettings["oneSignalAppID"] != '0' && $readSettings["oneSignalAPIKey"] != '0') {
                      require_once(__ROOT__."/apps/main/private/packages/class/onesignal/onesignal.php");
                      $notificationPermission = $db->prepare("SELECT * FROM Permissions WHERE name = ?");
                      $notificationPermission->execute(array("MANAGE_SUPPORT"));
                      $readNotificationPermission = $notificationPermission->fetch();
                      if ($notificationPermission->rowCount() > 0) {
                        $adminAccounts = $db->prepare("SELECT AOSI.oneSignalID FROM Accounts A INNER JOIN AccountOneSignalInfo AOSI ON A.id = AOSI.accountID LEFT JOIN AccountRoles AR ON AR.accountID = A.id INNER JOIN Roles R ON AR.roleID = R.id INNER JOIN RolePermissions RP ON RP.roleID = R.id LEFT JOIN AccountPermissions AP ON AP.accountID = A.id WHERE AP.permissionID = :perm OR RP.permissionID = :perm GROUP BY A.id");
                        $adminAccounts->execute(array(
                          'perm' => $readNotificationPermission["id"],
                        ));
                        if ($adminAccounts->rowCount() > 0) {
                          $oneSignalIDList = array();
                          foreach ($adminAccounts as $readAdminAccounts) {
                            array_push($oneSignalIDList, $readAdminAccounts["oneSignalID"]);
                          }
                          $oneSignal = new OneSignal($readSettings["oneSignalAppID"], $readSettings["oneSignalAPIKey"], $oneSignalIDList);
                          $oneSignal->sendMessage('LeaderOS Bildirim', $readAccount["realname"].' adlı kullanıcı destek mesajı gönderdi.', '/yonetim-paneli/destek/goruntule/'.$notificationsVariables);
                        }
                      }
                    }
                    echo alertSuccess("Mesajınız bize başarıyla gönderilmiştir. En kısa zamanda yetkililer tarafından cevaplanacaktır.");
                    echo goDelay("/destek", 2);
                  }
                }
              }
            ?>
            <div class="card">
              <div class="card-header">
                Destek Mesajı Gönder
              </div>
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputTitle" class="col-sm-2 col-form-label">Başlık:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputTitle" class="form-control" name="title" placeholder="Bir başlık belirleyin.">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectServer" class="col-sm-2 col-form-label">Sunucu:</label>
                    <div class="col-sm-10">
                      <?php
                        $servers = $db->query("SELECT * FROM Servers");
                      ?>
                      <select id="selectServer" class="form-control" name="serverID" data-toggle="select2" <?php echo ($servers->rowCount() == 0) ? "disabled" : null; ?>>
                        <?php if ($servers->rowCount() > 0): ?>
                          <?php foreach ($servers as $readServers): ?>
                            <option value="<?php echo $readServers["id"]; ?>"><?php echo $readServers["name"]; ?></option>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <option>Sunucu bulunamadı!</option>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectCategory" class="col-sm-2 col-form-label">Kategori:</label>
                    <div class="col-sm-10">
                      <?php
                        $supportCategories = $db->query("SELECT * FROM SupportCategories");
                      ?>
                      <select id="selectCategory" class="form-control" name="categoryID" data-toggle="select2" <?php echo ($supportCategories->rowCount() == 0) ? "disabled" : null; ?>>
                        <option disabled selected>Kategori seçiniz.</option>
                        <?php if ($supportCategories->rowCount() > 0): ?>
                          <?php foreach ($supportCategories as $readSupportCategories): ?>
                            <option value="<?php echo $readSupportCategories["id"]; ?>" data-template="<?php echo $readSupportCategories["userTemplate"] ?>"><?php echo $readSupportCategories["name"]; ?></option>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <option>Kategori bulunamadı!</option>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="ckeditor" class="col-sm-2 col-form-label">Mesaj:</label>
                    <div class="col-sm-10">
                      <textarea
                          id="ckeditor"
                          name="message"
                          placeholder="Destek ekibimize iletmek istediğiniz mesajı yazın."
                          class="form-control"
                          rows="6"
                          data-upload="/apps/main/public/ajax/support-upload.php"
                          data-csrf="<?php echo $csrf->string('sendSupportMessage'); ?>"></textarea>
                    </div>
                  </div>
                  <?php if ($recaptchaStatus): ?>
                    <div class="form-group d-flex justify-content-end">
                      <?php echo $reCAPTCHA->getHtml(); ?>
                    </div>
                  <?php endif; ?>
                  <?php echo $csrf->input('sendSupportMessage'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <button type="submit" class="btn btn-success btn-rounded" name="insertSupports">Gönder</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php elseif (get("action") == 'get' && isset($_GET["id"])): ?>
            <?php
              $support = $db->prepare("SELECT S.*, A.realname, Se.name as serverName, SC.name as categoryName FROM Supports S INNER JOIN Accounts A ON S.accountID = A.id INNER JOIN Servers Se ON S.serverID = Se.id INNER JOIN SupportCategories SC ON S.categoryID = SC.id WHERE S.id = ? AND S.accountID = ?");
              $support->execute(array(get("id"), $readAccount["id"]));
              $readSupport = $support->fetch();
            ?>
            <?php if ($support->rowCount() > 0): ?>
              <?php
                require_once(__ROOT__."/apps/main/private/packages/class/commonmark/autoload.php");
                $converter = new \League\CommonMark\GithubFlavoredMarkdownConverter();
              ?>
              <?php
                require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
                $csrf = new CSRF('csrf-sessions', 'csrf-token');

                $updateSupports = $db->prepare("UPDATE Supports SET readStatus = ? WHERE id = ? AND accountID = ?");
                $updateSupports->execute(array(1, get("id"), $readAccount["id"]));

                if (isset($_POST["insertSupportMessages"])) {
                  if (!$csrf->validate('sendSupportMessage')) {
                    echo '<div class="mb-3">'.alertError("Sistemsel bir sorun oluştu!").'</div>';
                  }
                  else if (post("message") == null) {
                    echo '<div class="mb-3">'.alertError("Lütfen mesaj alanını doldurun!").'</div>';
                  }
                  else {
                    $supportBannedStatus = $db->prepare("SELECT * FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?)");
                    $supportBannedStatus->execute(array($readAccount["id"], 2, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
                    if ($supportBannedStatus->rowCount() > 0) {
                      echo alertError("Destek bölümünden engellendiğiniz için mesaj gönderemezsiniz!");
                    }
                    else {
                      $insertSupportMessages = $db->prepare("INSERT INTO SupportMessages (accountID, message, supportID, writeLocation, creationDate) VALUES (?, ?, ?, ?, ?)");
                      $insertSupportMessages->execute(array($readAccount["id"], post("message"), get("id"), 1, date("Y-m-d H:i:s")));
                      $updateSupports = $db->prepare("UPDATE Supports SET updateDate = ?, statusID = ?, readStatus = ? WHERE id = ? AND accountID = ?");
                      $updateSupports->execute(array(date("Y-m-d H:i:s"), 3, 1, get("id"), $readAccount["id"]));
                      $insertNotifications = $db->prepare("INSERT INTO Notifications (accountID, type, variables, creationDate) VALUES (?, ?, ?, ?)");
                      $insertNotifications->execute(array($readAccount["id"], 1, $readSupport["id"], date("Y-m-d H:i:s")));

                      $websiteURL = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"]);
                      if ($readSettings["webhookSupportURL"] != '0') {
                        require_once(__ROOT__."/apps/main/private/packages/class/webhook/webhook.php");
                        $search = array("%username%", "%panelurl%");
                        $replace = array($readAccount["realname"], "$websiteURL/yonetim-paneli/destek/goruntule/$readSupport[id]");
                        $webhookMessage = $readSettings["webhookSupportMessage"];
                        $webhookEmbed = $readSettings["webhookSupportEmbed"];
                        $postFields = (array(
                          'content'     => ($webhookMessage != '0') ? str_replace($search, $replace, $webhookMessage) : null,
                          'avatar_url'  => 'https://minotar.net/avatar/'.$readAccount["realname"].'/256.png',
                          'tts'         => false,
                          'embeds'      => array(
                            array(
                              'type'        => 'rich',
                              'title'       => $readSettings["webhookSupportTitle"],
                              'color'       => hexdec($readSettings["webhookSupportColor"]),
                              'description' => str_replace($search, $replace, $webhookEmbed),
                              'image'       => array(
                                'url' => ($readSettings["webhookSupportImage"] != '0') ? $readSettings["webhookSupportImage"] : null
                              ),
                              'footer'      =>
                              ($readSettings["webhookSupportAdStatus"] == 1) ? array(
                                'text'      => 'Powered by LeaderOS',
                                'icon_url'  => 'https://i.ibb.co/wNHKQ7B/leaderos-logo.png'
                              ) : array()
                            )
                          )
                        ));
                        $curl = new \LeaderOS\Http\Webhook($readSettings["webhookSupportURL"]);
                        $curl(json_encode($postFields, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                      }

                      if ($readSettings["oneSignalAppID"] != '0' && $readSettings["oneSignalAPIKey"] != '0') {
                        require_once(__ROOT__."/apps/main/private/packages/class/onesignal/onesignal.php");
                        $notificationPermission = $db->prepare("SELECT * FROM Permissions WHERE name = ?");
                        $notificationPermission->execute(array("MANAGE_BLOG"));
                        $readNotificationPermission = $notificationPermission->fetch();
                        if ($notificationPermission->rowCount() > 0) {
                          $adminAccounts = $db->prepare("SELECT AOSI.oneSignalID FROM Accounts A INNER JOIN AccountOneSignalInfo AOSI ON A.id = AOSI.accountID LEFT JOIN AccountRoles AR ON AR.accountID = A.id INNER JOIN Roles R ON AR.roleID = R.id INNER JOIN RolePermissions RP ON RP.roleID = R.id LEFT JOIN AccountPermissions AP ON AP.accountID = A.id WHERE AP.permissionID = :perm OR RP.permissionID = :perm GROUP BY A.id");
                          $adminAccounts->execute(array(
                            'perm' => $readNotificationPermission["id"],
                          ));
                          if ($adminAccounts->rowCount() > 0) {
                            $oneSignalIDList = array();
                            foreach ($adminAccounts as $readAdminAccounts) {
                              array_push($oneSignalIDList, $readAdminAccounts["oneSignalID"]);
                            }
                            $oneSignal = new OneSignal($readSettings["oneSignalAppID"], $readSettings["oneSignalAPIKey"], $oneSignalIDList);
                            $oneSignal->sendMessage('LeaderOS Bildirim', $readAccount["realname"].' adlı kullanıcı destek mesajı gönderdi.', '/yonetim-paneli/destek/goruntule/'.$readSupport["id"]);
                          }
                        }
                      }
                      echo '<div class="mb-3">'.alertSuccess("Mesajınız bize başarıyla gönderilmiştir. En kısa zamanda yetkililer tarafından cevaplanacaktır.").'</div>';
                    }
                  }
                }
              ?>
              <div class="card">
                <div class="card-header">
                  <div class="row">
                    <div class="col">
                      <?php echo limitedContent($readSupport["title"], 50); ?>
                    </div>
                    <div class="col-auto">
                      <span class="badge badge-pill badge-default" data-toggle="tooltip" data-placement="top" data-original-title="Sunucu">
                        <?php echo $readSupport["serverName"]; ?>
                      </span>
                      <span class="badge badge-pill badge-default" data-toggle="tooltip" data-placement="top" data-original-title="Kategori">
                        <?php echo $readSupport["categoryName"]; ?>
                      </span>
                      <span class="badge badge-pill badge-default" data-toggle="tooltip" data-placement="top" data-original-title="Tarih">
                        <?php echo convertTime($readSupport["creationDate"], 2, true); ?>
                      </span>
                      <?php if ($readSupport["statusID"] == 1): ?>
                        <span class="badge badge-pill badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Durum">Cevaplanmadı</span>
                      <?php elseif ($readSupport["statusID"] == 2): ?>
                        <span class="badge badge-pill badge-success" data-toggle="tooltip" data-placement="top" data-original-title="Durum">Cevaplandı</span>
                      <?php elseif ($readSupport["statusID"] == 3): ?>
                        <span class="badge badge-pill badge-warning" data-toggle="tooltip" data-placement="top" data-original-title="Durum">Kullanıcı Yanıtı</span>
                      <?php elseif ($readSupport["statusID"] == 4): ?>
                        <span class="badge badge-pill badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Durum">Kapatıldı</span>
                      <?php else: ?>
                        <span class="badge badge-pill badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Durum">HATA!</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
                <div id="messagesBox" class="card-body pb-0" style="overflow: auto; max-height: 500px;">
                  <div class="message">
                    <div class="message-img">
                      <a href="/oyuncu/<?php echo $readSupport["realname"]; ?>">
                        <?php echo minecraftHead($readSettings["avatarAPI"], $readSupport["realname"], 40, "float-left"); ?>
                      </a>
                    </div>
                    <div class="message-content">
                      <div class="message-header">
                        <div class="message-username">
                          <a href="/oyuncu/<?php echo $readSupport["realname"]; ?>">
                            <?php echo $readSupport["realname"]; ?>
                          </a>
                        </div>
                        <div class="message-date">
                          <?php echo convertTime($readSupport["creationDate"], 2, true); ?>
                        </div>
                      </div>
                      <div class="message-body">
                        <p>
                          <?php echo $converter->convertToHtml($readSupport["message"]); ?>
                        </p>
                      </div>
                    </div>
                  </div>
                <?php
                  $supportMessages = $db->prepare("SELECT SM.*, A.realname FROM SupportMessages SM INNER JOIN Accounts A ON SM.accountID = A.id WHERE SM.supportID = ? ORDER BY SM.id ASC");
                  $supportMessages->execute(array(get("id")));
                ?>
                <?php if ($supportMessages->rowCount() > 0): ?>
                  <?php foreach ($supportMessages as $readSupportMessages): ?>
                    <div class="message">
                      <div class="message-img">
                        <a href="/oyuncu/<?php echo $readSupportMessages["realname"]; ?>">
                          <?php echo minecraftHead($readSettings["avatarAPI"], $readSupportMessages["realname"], 40, "float-left"); ?>
                        </a>
                      </div>
                      <div class="message-content">
                        <div class="message-header">
                          <div class="message-username">
                            <a href="/oyuncu/<?php echo $readSupportMessages["realname"]; ?>">
                              <?php echo $readSupportMessages["realname"]; ?>
                            </a>
                          </div>
                          <div class="message-date">
                            <?php echo convertTime($readSupportMessages["creationDate"], 2, true); ?>
                          </div>
                        </div>
                        <div class="message-body">
                          <p>
                            <?php
                              if ($readSupportMessages["writeLocation"] == 1) {
                                echo $converter->convertToHtml($readSupportMessages["message"]);
                              }
                              else {
                                $message = showEmoji(hashtag(hashtag($readSupportMessages["message"], "@", "/oyuncu"), "#", "/etiket"));
                                $search = array("%username%", "%message%", "%servername%", "%serverip%", "%serverversion%");
                                $replace = array($readSupport["realname"], $message, $serverName, $serverIP, $serverVersion);
                                $template = $readSettings["supportMessageTemplate"];
                                echo str_replace($search, $replace, $template);
                              }
                            ?>
                          </p>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
                </div>
                <?php if ($readSupport["statusID"] != 4): ?>
                  <div class="card-footer">
                    <form action="" method="post">
                      <div class="message">
                        <div class="message-img">
                          <?php echo minecraftHead($readSettings["avatarAPI"], $readAccount["realname"], 40, "float-left"); ?>
                        </div>
                        <div class="message-content">
                          <div class="message-body">
                            <textarea
                                id="ckeditor"
                                name="message"
                                placeholder="Mesajınızı yazınız."
                                class="form-control"
                                rows="3"
                                data-upload="/apps/main/public/ajax/support-upload.php"
                                data-csrf="<?php echo $csrf->string('sendSupportMessage'); ?>"></textarea>
                          </div>
                          <div class="message-footer">
                            <?php echo $csrf->input('sendSupportMessage'); ?>
                            <div class="clearfix">
                              <div class="float-right">
                                <button type="submit" class="btn btn-success btn-rounded" name="insertSupportMessages">Mesaj Gönder</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                <?php endif; ?>
              </div>
            <?php else: ?>
              <?php echo alertError("Destek mesajı bulunamadı!"); ?>
            <?php endif; ?>
          <?php elseif (get("action") == 'delete' && isset($_GET["id"])): ?>
            <?php
              $closeSupport = $db->prepare("UPDATE Supports SET statusID = ? WHERE id = ? AND accountID = ?");
              $closeSupport->execute(array(4, get("id"), $readAccount["id"]));
              go('/destek');
            ?>
          <?php else: ?>
            <?php go('/404'); ?>
          <?php endif; ?>
        <?php else: ?>
          <?php go('/404'); ?>
        <?php endif; ?>
      </div>
      <div class="col-md-4">
        <?php
          $onlineAccountsHistory = $db->prepare("SELECT OAH.*, A.realname FROM OnlineAccountsHistory OAH INNER JOIN Accounts A ON OAH.accountID = A.id WHERE OAH.expiryDate > ? AND OAH.type = ?");
          $onlineAccountsHistory->execute(array(date("Y-m-d H:i:s"), 1));
        ?>
        <?php if ($onlineAccountsHistory->rowCount() > 0): ?>
          <div class="card">
            <div class="card-header">
              Çevrimiçi Yetkililer
            </div>
            <div class="card-body p-0">
              <ul class="list-group list-group-flush">
                <?php foreach ($onlineAccountsHistory as $readOnlineAccountsHistory): ?>
                  <li class="list-group-item">
                    <a href="/oyuncu/<?php echo $readOnlineAccountsHistory["realname"]; ?>" data-toggle="tooltip" data-placement="left" data-original-title="Son Görülme: <?php echo convertTime($readOnlineAccountsHistory["creationDate"]); ?>">
                      <div class="row">
                        <div class="col">
                          <?php echo minecraftHead($readSettings["avatarAPI"], $readOnlineAccountsHistory["realname"], 20, "mr-2"); ?>
                          <span>
                            <?php echo $readOnlineAccountsHistory["realname"]; ?>
                          </span>
                        </div>
                        <div class="col-auto">
                          <span class="text-success">●</span>
                        </div>
                      </div>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        <?php else: ?>
          <?php echo alertWarning("Şu anda Çevrimiçi Yetkili bulunmamaktadır!"); ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
