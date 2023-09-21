<?php
  if (isset($_SESSION["login"])) {
    go("/profil");
  }
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\SMTP;
  use PHPMailer\PHPMailer\Exception;
  use Phelium\Component\reCAPTCHA;
  $recaptchaPagesStatusJSON = $readSettings["recaptchaPagesStatus"];
  $recaptchaPagesStatus = json_decode($recaptchaPagesStatusJSON, true);
  $recaptchaStatus = $readSettings["recaptchaPublicKey"] != '0' && $readSettings["recaptchaPrivateKey"] != '0' && $recaptchaPagesStatus["recoverPage"] == 1;
  if ($recaptchaStatus) {
    require_once(__ROOT__.'/apps/main/private/packages/class/extraresources/extraresources.php');
    require_once(__ROOT__.'/apps/main/private/packages/class/recaptcha/recaptcha.php');
    $reCAPTCHA = new reCAPTCHA($readSettings["recaptchaPublicKey"], $readSettings["recaptchaPrivateKey"]);
    $reCAPTCHA->setRemoteIp(getIP());
    $reCAPTCHA->setLanguage("tr");
    $reCAPTCHA->setTheme(($readTheme["recaptchaThemeID"] == 1) ? "light" : (($readTheme["recaptchaThemeID"] == 2) ? "dark" : "light"));
    $extraResourcesJS = new ExtraResources('js');
    $extraResourcesJS->addResource($reCAPTCHA->getScriptURL(), true, true);
  }
?>
<section class="section section-recover">
  <div class="container">
    <div class="row">
      <div class="col-md-4 offset-md-4">
        <?php if (get("id") && get("token")): ?>
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            $checkToken = $db->prepare("SELECT * FROM AccountRecovers WHERE accountID = ? AND recoverToken = ? AND creationIP = ? AND expiryDate > ?");
            $checkToken->execute(array(get("id"), get("token"), getIP(), date("Y-m-d H:i:s")));
          ?>
          <?php if ($checkToken->rowCount() > 0): ?>
            <?php
              if (isset($_POST["recoverAccount"])) {
                if (!$csrf->validate('recoverAccount')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("password") == null || post("passwordRe") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else if (post("password") != post("passwordRe")) {
                  echo alertError("Şifreler uyuşmuyor!");
                }
                else if (strlen(post("password")) < 4) {
                  echo alertError("Şifre 4 karakterden az olamaz!");
                }
                else if (checkBadPassword(post("password"))) {
                  echo alertError("Basit şifreler kullanamazsınız!");
                }
                else {
                  $password = createPassword($readSettings["passwordType"], post("password"));
                  $updateAccounts = $db->prepare("UPDATE Accounts SET password = ? WHERE id = ?");
                  $updateAccounts->execute(array($password, get("id")));
                  $deleteAccountRecovers = $db->prepare("DELETE FROM AccountRecovers WHERE accountID = ?");
                  $deleteAccountRecovers->execute(array(get("id")));
                  $deleteAccountSessions = $db->prepare("DELETE FROM AccountSessions WHERE accountID = ?");
                  $deleteAccountSessions->execute(array(get("id")));
                  echo alertSuccess("Şifreniz başarıyla değişmiştir! Yönlendiriliyorsunuz...");
                  echo goDelay("/giris-yap", 2);
                }
              }
            ?>
            <div class="card">
              <div class="card-header">
                Şifre Değiştir
              </div>
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="Yeni Şifre">
                  </div>
                  <div class="form-group">
                    <input type="password" class="form-control" name="passwordRe" placeholder="Yeni Şifre (Tekrar)">
                  </div>
                  <?php echo $csrf->input('recoverAccount'); ?>
                  <button type="submit" class="theme-color btn btn-primary w-100" name="recoverAccount">Şifre Değiştir</button>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError("Şifre yenileme bağlantısı bozuk veya kullanım süresi sona erdi!"); ?>
          <?php endif; ?>
        <?php else: ?>
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["sendEmail"])) {
              if (!$csrf->validate('sendEmail')) {
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
              else if (post("username") == null || post("email") == null) {
                echo alertError("Lütfen boş alan bırakmayınız!");
              }
              else {
                $checkAccount = $db->prepare("SELECT * FROM Accounts WHERE realname = ? AND email = ?");
                $checkAccount->execute(array(post("username"), post("email")));
                $readAccount = $checkAccount->fetch();
                if ($checkAccount->rowCount() > 0) {
                  $recoverToken = md5(uniqid(mt_rand(), true));
                  $url = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http").'://'.$_SERVER["SERVER_NAME"].'/sifremi-unuttum/'.$readAccount["id"].'/'.$recoverToken);
                  $search = array("%username%", "%url%");
                  $replace = array($readAccount["realname"], $url);
                  $template = $readSettings["smtpPasswordTemplate"];
                  $content = str_replace($search, $replace, $template);
                  require_once(__ROOT__."/apps/main/private/packages/class/phpmailer/exception.php");
                  require_once(__ROOT__."/apps/main/private/packages/class/phpmailer/phpmailer.php");
                  require_once(__ROOT__."/apps/main/private/packages/class/phpmailer/smtp.php");
                  $phpMailer = new PHPMailer(true);
                  try {
                    $phpMailer->IsSMTP();
                    $phpMailer->setLanguage('tr', __ROOT__.'/apps/main/private/packages/class/phpmailer/lang/');
                    $phpMailer->SMTPAuth = true;
                    $phpMailer->Host = $readSettings["smtpServer"];
                    $phpMailer->Port = $readSettings["smtpPort"];
                    $phpMailer->SMTPSecure = (($readSettings["smtpSecure"] == 1) ? PHPMailer::ENCRYPTION_SMTPS : (($readSettings["smtpSecure"] == 2) ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS));
                    $phpMailer->Username = $readSettings["smtpUsername"];
                    $phpMailer->Password = $readSettings["smtpPassword"];
                    $phpMailer->SetFrom($phpMailer->Username, $readSettings["serverName"]);
                    $phpMailer->AddAddress($readAccount["email"], $readAccount["realname"]);
                    $phpMailer->isHTML(true);
                    $phpMailer->CharSet = 'UTF-8';
                    $phpMailer->Subject = $readSettings["serverName"]." - Şifre Yenileme İsteği";
                    $phpMailer->Body = $content;
                    $phpMailer->send();
                    $checkAccountRecovers = $db->prepare("SELECT * FROM AccountRecovers WHERE accountID = ?");
                    $checkAccountRecovers->execute(array($readAccount["id"]));
                    if ($checkAccountRecovers->rowCount() > 0) {
                      $updateAccountRecovers = $db->prepare("UPDATE AccountRecovers SET recoverToken = ?, creationIP = ?, expiryDate = ?, creationDate = ? WHERE accountID = ?");
                      $updateAccountRecovers->execute(array($recoverToken, getIP(), createDuration(0.04166666666), date("Y-m-d H:i:s"), $readAccount["id"]));
                    }
                    else {
                      $insertAccountRecovers = $db->prepare("INSERT INTO AccountRecovers (accountID, recoverToken, creationIP, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?)");
                      $insertAccountRecovers->execute(array($readAccount["id"], $recoverToken, getIP(), createDuration(0.04166666666), date("Y-m-d H:i:s")));
                    }
                    echo alertSuccess("Email adresinize şifre yenileme bağlantısı gönderildi!");
                  } catch (Exception $e) {
                    echo alertError("Sistemsel bir hata nedeni ile mail gönderilemedi: ".$e->errorMessage());
                  }
                }
                else {
                  echo alertError("Kullanıcı adı ile email adresi eşleşmedi!");
                }
              }
            }
          ?>
          <div class="card">
            <div class="card-header">
              Şifremi Unuttum
            </div>
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group">
                  <input type="text" class="form-control" name="username" placeholder="Kullanıcı Adı">
                </div>
                <div class="form-group">
                  <input type="email" class="form-control" name="email" placeholder="Email Adresi">
                </div>
                <?php if ($recaptchaStatus): ?>
                  <div class="form-group d-flex justify-content-center">
                    <?php echo $reCAPTCHA->getHtml(); ?>
                  </div>
                <?php endif; ?>
                <?php echo $csrf->input('sendEmail'); ?>
                <button type="submit" class="theme-color btn btn-primary w-100" name="sendEmail">Gönder</button>
              </form>
            </div>
            <div class="card-footer text-center">
              Şifreni hatırladın mı?
              <a href="/giris-yap">Giriş Yap</a>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
