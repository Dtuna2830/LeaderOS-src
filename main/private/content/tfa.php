<?php
  if (isset($_SESSION["login"])) {
    go("/profil");
  }
  if (isset($_SESSION["tfa"]) && $_SESSION["tfa"]["expiryDate"] <= date("Y-m-d H:i:s")) {
    unset($_SESSION["tfa"]);
  }
  if (!isset($_SESSION["tfa"]) || $readSettings["authStatus"] == 0) {
    go("/giris-yap");
  }
  use Phelium\Component\reCAPTCHA;
  $recaptchaPagesStatusJSON = $readSettings["recaptchaPagesStatus"];
  $recaptchaPagesStatus = json_decode($recaptchaPagesStatusJSON, true);
  $recaptchaStatus = $readSettings["recaptchaPublicKey"] != '0' && $readSettings["recaptchaPrivateKey"] != '0' && $recaptchaPagesStatus["tfaPage"] == 1;
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
            <?php if ($_SESSION["tfa"]["ipAddress"] == getIP()): ?>
              <?php
                $account = $db->prepare("SELECT * FROM Accounts WHERE id = ?");
                $account->execute(array($_SESSION["tfa"]["accountID"]));
                $readAccount = $account->fetch();
              ?>
              <?php if ($account->rowCount() > 0): ?>
                <?php
                  require_once(__ROOT__."/apps/main/private/packages/class/tfa/tfa.php");
                  $tfa = new GoogleAuthenticator();

                  $accountAuth = $db->prepare("SELECT * FROM AccountAuths WHERE accountID = ?");
                  $accountAuth->execute(array($readAccount["id"]));
                  $readAccountAuth = $accountAuth->fetch();
                  if ($accountAuth->rowCount() > 0 && $readAccount["authStatus"] == 1) {
                    if (!isset($_SESSION["tfa"]["secretKey"])) {
                      $_SESSION["tfa"]["secretKey"] = $readAccountAuth["secretKey"];
                    }
                  }
                  else {
                    if (!isset($_SESSION["tfa"]["secretKey"])) {
                      $_SESSION["tfa"]["secretKey"] = $tfa->createSecret();
                    }
                    $qrCode = $tfa->getQRCodeGoogleUrl($readAccount["realname"], $_SESSION["tfa"]["secretKey"], $serverName);
                  }
                ?>
                <?php
                  require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
                  $csrf = new CSRF('csrf-sessions', 'csrf-token');
                  if (isset($_POST["verifyTFA"])) {
                    if (!$csrf->validate('verifyTFA')) {
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
                    else if (post("oneCode") == null) {
                      echo alertError("Lütfen boş alan bırakmayınız!");
                    }
                    else {
                      $verifyTFA = $tfa->verifyCode($_SESSION["tfa"]["secretKey"], post("oneCode"));
                      if ($verifyTFA) {
                        $loginToken = md5(uniqid(mt_rand(), true));
                        $insertAccountSessions = $db->prepare("INSERT INTO AccountSessions (accountID, loginToken, creationIP, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?)");
                        $insertAccountSessions->execute(array($readAccount["id"], $loginToken, getIP(), createDuration(((isset($_SESSION["tfa"]["rememberMe"]) && $_SESSION["tfa"]["rememberMe"] == 'true') ? 365 : 0.01666666666)), date("Y-m-d H:i:s")));

                        if ($accountAuth->rowCount() > 0) {
                          $updateAccountAuths = $db->prepare("UPDATE AccountAuths SET secretKey = ? WHERE accountID = ?");
                          $updateAccountAuths->execute(array($_SESSION["tfa"]["secretKey"], $readAccount["id"]));
                        }
                        else {
                          $insertAccountAuths = $db->prepare("INSERT INTO AccountAuths (accountID, secretKey) VALUES (?, ?)");
                          $insertAccountAuths->execute(array($readAccount["id"], $_SESSION["tfa"]["secretKey"]));
                        }

                        if (isset($_SESSION["tfa"]["profileUpdate"]) && $_SESSION["tfa"]["profileUpdate"] == 'true') {
                          $updateAccounts = $db->prepare("UPDATE Accounts SET authStatus = ? WHERE id = ?");
                          $updateAccounts->execute(array(1, $readAccount["id"]));
                        }

                        if (isset($_SESSION["tfa"]["rememberMe"]) && $_SESSION["tfa"]["rememberMe"] == 'true') {
                          createCookie("rememberMe", $loginToken, 365, $sslStatus);
                        }
                        $_SESSION["login"] = $loginToken;

                        unset($_SESSION["tfa"]);
                        unset($_SESSION["tfa-recover"]);
                        go("/profil");
                      }
                      else {
                        echo alertError("Girmiş olduğunuz tek kullanımlık kod yanlış!");
                      }
                    }
                  }
                ?>
                <div class="card">
                  <div class="card-header">
                    İki Adımlı Doğrulama
                  </div>
                  <div class="card-body">
                    <form action="" method="post">
                      <?php if ($accountAuth->rowCount() == 0 || $readAccount["authStatus"] == 0): ?>
                        <div class="form-group text-center">
                          <img src="<?php echo $qrCode; ?>" alt="Google Authenticator QR Kod">
                        </div>
                        <div class="form-group text-center">
                          <span>QR Kod'u okutamıyorsanız <strong><?php echo $_SESSION["tfa"]["secretKey"]; ?></strong> anahtarı ile hesap ekleyiniz.</span>
                        </div>
                      <?php endif; ?>
                      <div class="form-group">
                        <?php if ($accountAuth->rowCount() > 0 && $readAccount["authStatus"] == 1): ?>
                          <div class="row">
                            <div class="col">
                              <label for="input-password" class="form-control-label">Kod:</label>
                            </div>
                            <div class="col-auto">
                              <a class="small" href="/dogrulama-sifirla">Erişimim Yok</a>
                            </div>
                          </div>
                        <?php endif; ?>
                        <input type="text" class="form-control" name="oneCode" placeholder="Tek kullanımlık kodu giriniz." autocomplete="off">
                      </div>
                      <?php if ($recaptchaStatus): ?>
                        <div class="form-group d-flex justify-content-center">
                          <?php echo $reCAPTCHA->getHtml(); ?>
                        </div>
                      <?php endif; ?>
                      <?php echo $csrf->input('verifyTFA'); ?>
                      <button type="submit" class="theme-color btn btn-primary w-100" name="verifyTFA">Doğrula</button>
                    </form>
                  </div>
                  <div class="card-footer text-center">
                    <a href="https://egitim.leaderos.com.tr/google-authenticator" rel="external">Google Authenticator'i nasıl kullanırım?</a>
                  </div>
                </div>
              <?php else: ?>
                <?php unset($_SESSION["tfa"]); ?>
                <?php echo alertError("Kullanıcı bulunamadı!"); ?>
              <?php endif; ?>
            <?php else: ?>
              <?php unset($_SESSION["tfa"]); ?>
              <?php echo alertError("IP adresi geçersiz!"); ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
