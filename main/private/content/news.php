<?php
  use Phelium\Component\reCAPTCHA;
  require_once(__ROOT__.'/apps/main/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/main/public/assets/js/loader.js');
  $recaptchaPagesStatusJSON = $readSettings["recaptchaPagesStatus"];
  $recaptchaPagesStatus = json_decode($recaptchaPagesStatusJSON, true);
  $recaptchaStatus = $readSettings["recaptchaPublicKey"] != '0' && $readSettings["recaptchaPrivateKey"] != '0' && $recaptchaPagesStatus["newsPage"] == 1;
  if ($recaptchaStatus) {
    require_once(__ROOT__.'/apps/main/private/packages/class/recaptcha/recaptcha.php');
    $reCAPTCHA = new reCAPTCHA($readSettings["recaptchaPublicKey"], $readSettings["recaptchaPrivateKey"]);
    $reCAPTCHA->setRemoteIp(getIP());
    $reCAPTCHA->setLanguage("tr");
    $reCAPTCHA->setTheme(($readTheme["recaptchaThemeID"] == 1) ? "light" : (($readTheme["recaptchaThemeID"] == 2) ? "dark" : "light"));
    $extraResourcesJS->addResource($reCAPTCHA->getScriptURL(), true, true);
  }
  $news = $db->prepare("SELECT N.*, A.realname, NC.name as categoryName, NC.slug as categorySlug FROM News N INNER JOIN Accounts A ON N.accountID = A.id INNER JOIN NewsCategories NC ON N.categoryID = NC.id WHERE N.id = ?");
  $news->execute(array(get("id")));
  $readNews = $news->fetch();
?>
<section class="section news-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Ana Sayfa</a></li>
            <?php if (isset($_GET["id"])): ?>
              <?php if ($news->rowCount() > 0): ?>
                <li class="breadcrumb-item"><a href="/kategori/<?php echo $readNews["categorySlug"]; ?>"><?php echo $readNews["categoryName"]; ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $readNews["title"]; ?></li>
              <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page">Bulunamadı!</li>
              <?php endif; ?>
            <?php else: ?>
              <li class="breadcrumb-item active" aria-current="page">Haber</li>
            <?php endif; ?>
          </ol>
        </nav>
      </div>
      <?php if ($news->rowCount() > 0): ?>
        <div class="col-md-8 col-news">
          <?php if (!isset($_COOKIE["newsID"])): ?>
            <?php
              $updateNews = $db->prepare("UPDATE News SET views = views + 1 WHERE id = ?");
              $updateNews->execute(array($readNews["id"]));
              setcookie("newsID", $readNews["id"]);
            ?>
          <?php endif; ?>
          <?php
            $newsComments = $db->prepare("SELECT NC.*, A.realname FROM NewsComments NC INNER JOIN Accounts A ON NC.accountID = A.id WHERE NC.newsID = ? AND NC.status = ? ORDER BY NC.id DESC");
            $newsComments->execute(array($readNews["id"], 1));
          ?>
          <div class="card mb-4">
            <div class="card-header">
              <?php echo $readNews["title"]; ?>
            </div>
            <div class="card-body">
              <div class="news-info mb-4">
                <div class="news-author float-left">
                  <div class="author-img float-left mr-2">
                    <a href="/oyuncu/<?php echo $readNews["realname"]; ?>">
                      <?php echo minecraftHead($readSettings["avatarAPI"], $readNews["realname"], 34); ?>
                    </a>
                  </div>
                  <div class="author-info float-left" style="line-height: 1.125rem;">
                    <a href="/oyuncu/<?php echo $readNews["realname"]; ?>">
                      <span class="d-block" style="font-weight: 600;">
                        <?php echo $readNews["realname"]; ?>
                      </span>
                    </a>
                    <span><?php echo convertTime($readNews["creationDate"], 2, true); ?></span>
                  </div>
                </div>
                <div class="float-right">
                  <label class="mr-2" data-toggle="tooltip" data-placement="top" title="Görüntülenme"><i class="fa fa-eye"></i> <?php echo $readNews["views"]; ?></label>
                  <label data-toggle="tooltip" data-placement="top" title="Yorumlar"><i class="fa fa-comments"></i> <?php echo $newsComments->rowCount(); ?></label>
                </div>
                <div class="clearfix"></div>
              </div>
              <div class="news-content mb-4">
                <?php echo showEmoji(hashtag(hashtag($readNews["content"], "@", "/oyuncu"), "#", "/etiket")); ?>
              </div>
              <div class="news-tags">
                <span style="font-weight: 600;">Etiketler:</span>
                <?php
                  $newsTags = $db->prepare("SELECT NT.* FROM NewsTags NT INNER JOIN News N ON NT.newsID = N.id WHERE NT.newsID = ?");
                  $newsTags->execute(array($readNews["id"]));
                  if ($newsTags->rowCount() > 0) {
                    foreach ($newsTags as $readNewsTags) {
                      echo '<a class="theme-color btn btn-tag btn-primary btn-rounded" href="/etiket/'.$readNewsTags["slug"].'">'.$readNewsTags["name"].'</a>';
                    }
                  }
                  else {
                    echo "-";
                  }
                ?>
              </div>
            </div>
          </div>
          <?php if ($readSettings["commentsStatus"] == 1 && $readNews["commentsStatus"] == 1): ?>
            <?php if (isset($_SESSION["login"])): ?>
              <?php
                require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
                $csrf = new CSRF('csrf-sessions', 'csrf-token');
                if (isset($_POST["insertNewsComments"])) {
                  if (!$csrf->validate('insertNewsComments')) {
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
                  else if (post("message") == null) {
                    echo alertError("Lütfen boş alan bırakmayınız!");
                  }
                  else {
                    $commentBannedStatus = $db->prepare("SELECT * FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?)");
                    $commentBannedStatus->execute(array($readAccount["id"], 3, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
                    if ($commentBannedStatus->rowCount() > 0) {
                      echo alertError("Yorum bölümünden engellendiğiniz için yorum yapamazsınız!");
                    }
                    else {
                      if (checkStaff($readAccount)) {
                        $status = 1;
                        echo alertSuccess("Yorumunuz başarıyla gönderilmiştir.");
                      }
                      else {
                        $status = 0;
                        echo alertSuccess("Yorumunuz yönetici onayından sonra gösterilecektir.");
                      }
                      $insertNewsComments = $db->prepare("INSERT INTO NewsComments (accountID, message, newsID, status, creationDate) VALUES (?, ?, ?, ?, ?)");
                      $insertNewsComments->execute(array($readAccount["id"], post("message"), get("id"), $status, date("Y-m-d H:i:s")));
                      $notificationsVariables = $db->lastInsertId();
                      $insertNotifications = $db->prepare("INSERT INTO Notifications (accountID, type, variables, creationDate) VALUES (?, ?, ?, ?)");
                      $insertNotifications->execute(array($readAccount["id"], 2, $notificationsVariables, date("Y-m-d H:i:s")));
  
                      $websiteURL = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"]);
                      if ($readSettings["webhookNewsURL"] != '0') {
                        require_once(__ROOT__."/apps/main/private/packages/class/webhook/webhook.php");
                        $search = array("%username%", "%panelurl%", "%posturl%");
                        $replace = array($readAccount["realname"], "$websiteURL/yonetim-paneli/haber/yorum/duzenle/$notificationsVariables", "$websiteURL/haber/$readNews[id]/$readNews[slug]");
                        $webhookMessage = $readSettings["webhookNewsMessage"];
                        $webhookEmbed = $readSettings["webhookNewsEmbed"];
                        $postFields = (array(
                          'content'     => ($webhookMessage != '0') ? str_replace($search, $replace, $webhookMessage) : null,
                          'avatar_url'  => 'https://minotar.net/avatar/'.$readAccount["realname"].'/256.png',
                          'tts'         => false,
                          'embeds'      => array(
                            array(
                              'type'        => 'rich',
                              'title'       => $readSettings["webhookNewsTitle"],
                              'color'       => hexdec($readSettings["webhookNewsColor"]),
                              'description' => str_replace($search, $replace, $webhookEmbed),
                              'image'       => array(
                                'url' => ($readSettings["webhookNewsImage"] != '0') ? $readSettings["webhookNewsImage"] : null
                              ),
                              'footer'      =>
                              ($readSettings["webhookNewsAdStatus"] == 1) ? array(
                                'text'      => 'Powered by LeaderOS',
                                'icon_url'  => 'https://i.ibb.co/wNHKQ7B/leaderos-logo.png'
                              ) : array()
                            )
                          )
                        ));
                        $curl = new \LeaderOS\Http\Webhook($readSettings["webhookNewsURL"]);
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
                            $oneSignal->sendMessage('LeaderOS Bildirim', $readAccount["realname"].' adlı kullanıcı habere yorum yaptı.', '/yonetim-paneli/haber/yorum/duzenle/'.$notificationsVariables);
                          }
                        }
                      }
                    }
                  }
                }
              ?>
              <div class="card mb-4">
                <div class="card-header">
                  Yorum Yap
                </div>
                <div class="card-body">
                  <form action="" method="post">
                    <div class="message">
                      <div class="message-img">
                        <?php echo minecraftHead($readSettings["avatarAPI"], $readAccount["realname"], 40, "float-left"); ?>
                      </div>
                      <div class="message-content">
                        <div class="message-body">
                          <textarea class="form-control" name="message" rows="3" placeholder="Yorumunuzu yazınız."></textarea>
                        </div>
                        <?php if ($recaptchaStatus): ?>
                          <div class="d-flex justify-content-end mt-3">
                            <?php echo $reCAPTCHA->getHtml(); ?>
                          </div>
                        <?php endif; ?>
                        <div class="message-footer">
                          <?php echo $csrf->input('insertNewsComments'); ?>
                          <div class="clearfix">
                            <div class="float-right">
                              <button type="submit" class="btn btn-success btn-rounded" name="insertNewsComments">Yorumu Gönder</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            <?php else: ?>
              <?php echo alertError("Yorum yapmak için giriş yapmanız gerekiyor."); ?>
            <?php endif; ?>
            <?php if ($newsComments->rowCount() > 0): ?>
              <div class="card mb-4">
                <div class="card-header">
                  Yorumlar
                </div>
                <div id="loader" class="card-body is-loading">
                  <div id="spinner">
                    <div class="spinner-border" role="status">
                      <span class="sr-only">-/-</span>
                    </div>
                  </div>
                  <?php foreach ($newsComments as $readNewsComments): ?>
                    <div class="message">
                      <div class="message-img">
                        <a href="/oyuncu/<?php echo $readNewsComments["realname"]; ?>">
                          <?php echo minecraftHead($readSettings["avatarAPI"], $readNewsComments["realname"], 40, "float-left"); ?>
                        </a>
                      </div>
                      <div class="message-content">
                        <div class="message-header">
                          <div class="message-username">
                            <a style="font-weight: 600;" href="/oyuncu/<?php echo $readNewsComments["realname"]; ?>">
                              <?php echo $readNewsComments["realname"]; ?>
                            </a>
                          </div>
                          <div class="message-date">
                            <?php echo convertTime($readNewsComments["creationDate"]); ?>
                          </div>
                        </div>
                        <div class="message-body">
                          <p>
                            <?php echo showEmoji(urlContent(hashtag(hashtag($readNewsComments["message"], "@", "/oyuncu"), "#", "/etiket"))); ?>
                          </p>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
              </div>
            </div>
            <?php else: ?>
              <?php if (isset($_SESSION["login"])): ?>
                <?php echo alertWarning("İlk yorumu sen yapmak ister misin?"); ?>
              <?php endif; ?>
            <?php endif; ?>
          <?php else: ?>
            <?php echo alertWarning("Bu haber yorumlara kapalıdır."); ?>
          <?php endif; ?>
        </div>
  
        <div class="col-md-4 col-other-news">
          <?php
            $otherNews = $db->prepare("SELECT N.id, N.title, N.slug, N.content, N.views, N.imageID, N.imageType, N.creationDate, NC.name as categoryName, NC.slug as categorySlug from News N INNER JOIN Accounts A ON N.accountID = A.id INNER JOIN NewsCategories NC ON N.categoryID = NC.id WHERE N.id != ? ORDER BY N.id DESC LIMIT 5");
            $otherNews->execute(array($readNews["id"]));
          ?>
          <?php if ($otherNews->rowCount() > 0): ?>
            <?php foreach ($otherNews as $readOtherNews): ?>
              <?php
                $otherNewsComments = $db->prepare("SELECT NC.id FROM NewsComments NC INNER JOIN Accounts A ON NC.accountID = A.id WHERE NC.newsID = ? AND NC.status = ?");
                $otherNewsComments->execute(array($readOtherNews["id"], 1));
              ?>
              <article class="news">
                <div class="img-card-wrapper">
                  <div class="img-container">
                    <a class="img-card" href="/haber/<?php echo $readOtherNews["id"]; ?>/<?php echo $readOtherNews["slug"]; ?>">
                      <img class="card-img-top lazyload" data-src="/apps/main/public/assets/img/news/<?php echo $readOtherNews["imageID"].'.'.$readOtherNews["imageType"]; ?>" src="/apps/main/public/assets/img/loaders/news.png" alt="<?php echo $serverName." Haber - ".$readOtherNews["title"]; ?>">
                    </a>
                    <div class="img-card-tl">
                      <a href="/kategori/<?php echo $readOtherNews["categorySlug"]; ?>">
                        <span class="theme-color badge badge-pill badge-primary"><?php echo $readOtherNews["categoryName"]; ?></span>
                      </a>
                      <a href="/haber/<?php echo $readOtherNews["id"]; ?>/<?php echo $readOtherNews["slug"]; ?>">
                        <span class="theme-color badge badge-pill badge-primary"><i class="fa fa-eye"></i> <?php echo $readOtherNews["views"]; ?></span>
                      </a>
                      <a href="/haber/<?php echo $readOtherNews["id"]; ?>/<?php echo $readOtherNews["slug"]; ?>">
                        <span class="theme-color badge badge-pill badge-primary"><i class="fa fa-comments"></i> <?php echo $otherNewsComments->rowCount(); ?></span>
                      </a>
                    </div>
                    <div class="img-card-tr">
                      <a href="/haber/<?php echo $readOtherNews["id"]; ?>/<?php echo $readOtherNews["slug"]; ?>">
                        <span class="theme-color badge badge-pill badge-primary"><?php echo convertTime($readOtherNews["creationDate"], 1); ?></span>
                      </a>
                    </div>
                    <div class="img-card-bottom">
                      <h5 class="mb-0">
                        <a class="text-white" href="/haber/<?php echo $readOtherNews["id"]; ?>/<?php echo $readOtherNews["slug"]; ?>">
                          <?php echo $readOtherNews["title"]; ?>
                        </a>
                      </h5>
                    </div>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          <?php else: ?>
            <?php echo alertError("Başka haber bulunamadı!"); ?>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="col-md-12">
          <?php echo alertError("Bu habere ulaşılamadı!"); ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
