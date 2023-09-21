<?php
  if (isset($_SESSION["login"])) {
    go("/profil");
  }
  use Phelium\Component\reCAPTCHA;
  $recaptchaPagesStatusJSON = $readSettings["recaptchaPagesStatus"];
  $recaptchaPagesStatus = json_decode($recaptchaPagesStatusJSON, true);
  $recaptchaStatus = $readSettings["recaptchaPublicKey"] != '0' && $readSettings["recaptchaPrivateKey"] != '0' && $recaptchaPagesStatus["registerPage"] == 1;
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
<section class="section page-section">
  <div class="container">
    <div class="row">
      <div class="col-md-4 offset-md-4">
        <?php
          require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
          $csrf = new CSRF('csrf-sessions', 'csrf-token');
          if (isset($_POST["insertAccounts"])) {
            if (!$csrf->validate('insertAccounts')) {
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
            else if (post("username") == null || post("email") == null || post("password") == null || post("passwordRe") == null) {
              echo alertError("Lütfen boş alan bırakmayınız!");
            }
            else {
              $usernameValid = $db->prepare("SELECT * FROM Accounts WHERE realname = ?");
              $usernameValid->execute(array(post("username")));

              $emailValid = $db->prepare("SELECT * FROM Accounts WHERE email = ?");
              $emailValid->execute(array(post("email")));

              $ipCount = $db->prepare("SELECT * FROM Accounts WHERE creationIP = ?");
              $ipCount->execute(array(getIP()));

              $badUsernameList = array(
                'yarrak',
                'sikis',
                'serefsiz',
                'amcik',
                'orospu'
              );

              if (!post("acceptRules")) {
                echo alertError("Lütfen kuralları kabul ediniz!");
              }
              else if ($registerLimit != 0 && $ipCount->rowCount() >= $registerLimit) {
                echo alertError('Aynı IP adresinden en fazla <strong>'.$registerLimit.' kez</strong> kayıt olabilirsiniz!');
              }
              else if (checkUsername(post("username"))) {
                echo alertError("Girdiğiniz kullanıcı adı uygun olmayan karakter içeriyor!");
              }
              else if (strlen(post("username")) < 3) {
                echo alertError("Kullanıcı adı 3 karakterden az olamaz!");
              }
              else if (strlen(post("username")) > 16) {
                echo alertError("Kullanıcı adı 16 karakterden fazla olamaz!");
              }
              else if (checkEmail(post("email"))) {
                echo alertError("Lütfen geçerli bir e-mail adresi giriniz!");
              }
              else if ($usernameValid->rowCount() > 0) {
                echo alertError('<strong>'.post("username").'</strong> adlı üye mevcut!');
              }
              else if ($emailValid->rowCount() > 0) {
                echo alertError('<strong>'.post("email").'</strong> başkası tarafından kullanılıyor!');
              }
              else if (strlen(post("password")) < 4) {
                echo alertError("Şifre 4 karakterden az olamaz!");
              }
              else if (post("password") != post("passwordRe")) {
                echo alertError("Şifreler uyuşmuyor!");
              }
              else if (checkBadPassword(post("password"))) {
                echo alertError("Basit şifreler kullanamazsınız!");
              }
              else if (checkBadUsername(post("username"), $badUsernameList)) {
                echo alertError("Yasaklı kelimeler içeren kullanıcı adlarını kullanamazsınız!");
              }
              else {
                $loginToken = md5(uniqid(mt_rand(), true));
                $password = createPassword($readSettings["passwordType"], post("password"));
                $insertAccounts = $db->prepare("INSERT INTO Accounts (username, realname, email, password, creationIP, creationDate) VALUES (?, ?, ?, ?, ?, ?)");
                $insertAccounts->execute(array(strtolower(post("username")), post("username"), post("email"), $password, getIP(), date("Y-m-d H:i:s")));
                $accountID = $db->lastInsertId();
                $insertAccountSessions = $db->prepare("INSERT INTO AccountSessions (accountID, loginToken, creationIP, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?)");
                $insertAccountSessions->execute(array($accountID, $loginToken, getIP(), createDuration(0.01666666666), date("Y-m-d H:i:s")));
                $_SESSION["login"] = $loginToken;
                echo alertSuccess("Başarıyla kayıt oldunuz! Yönlendiriliyorsunuz...");
                echo goDelay("/profil", 2);
              }
            }
          }
        ?>
        <div class="card">
          <div class="card-header">
            Kayıt Ol
          </div>
          <div class="card-body">
            <form action="" method="post">
              <div class="form-group">
                <input type="text" class="form-control" name="username" placeholder="Kullanıcı Adı" value="<?php echo ((post("username")) ? post("username") : null); ?>">
              </div>
              <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email Adresi" value="<?php echo ((post("email")) ? post("email") : null); ?>">
              </div>
              <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Şifre">
              </div>
              <div class="form-group">
                <input type="password" class="form-control" name="passwordRe" placeholder="Şifre (Tekrar)">
              </div>
              <div class="form-group custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="acceptRules" name="acceptRules" checked="checked">
                <label class="custom-control-label" for="acceptRules">
                  <a href="/kurallar" rel="external">Kuralları</a> okudum ve kabul ediyorum.
                </label>
              </div>
              <?php if ($recaptchaStatus): ?>
                <div class="form-group d-flex justify-content-center">
                  <?php echo $reCAPTCHA->getHtml(); ?>
                </div>
              <?php endif; ?>
              <?php echo $csrf->input('insertAccounts'); ?>
              <button type="submit" class="theme-color btn btn-primary w-100" name="insertAccounts">Kayıt Ol</button>
            </form>
          </div>
          <div class="card-footer text-center">
            Hesabınız mı var?
            <a href="/giris-yap">Giriş Yap</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
