<?php
  if (!checkPerm($readAdmin, 'MANAGE_NOTIFICATIONS')) {
    go('/yonetim-paneli/hata/001');
  }
?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="header">
        <div class="header-body">
          <div class="row align-items-center">
            <div class="col">
              <h2 class="header-title">Bildirimler</h2>
            </div>
            <div class="col-auto">
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Bildirimler</li>
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
      <?php if ($needUpdate == true && checkPerm($readAdmin, 'MANAGE_UPDATES')): ?>
        <div class="card">
          <div class="notification row text-muted">
            <div class="notification-icon col-auto">
              <i class="fe fe-refresh-cw"></i>
            </div>
            <div class="notification-icon">
              <div class="avatar avatar-xs ml-3 text-default h-100">
                <i class="fe fe-alert-circle" style="font-size: 26px;"></i>
              </div>
            </div>
            <div class="notification-content col">
              <strong class="text-danger mr-1">Sistem:</strong>
              <span>Yeni bir güncelleme mevcut! (Yeni Sürüm: <?php echo $newVersion; ?>)</span>
            </div>
            <div class="notification-time col-auto">
              <a class="btn btn-rounded btn-success" href="/yonetim-paneli/guncelleme">Şimdi Güncelle</a>
            </div>
          </div>
        </div>
      <?php endif; ?>
      <?php
        $notifications = $db->prepare("SELECT N.*, A.realname FROM Notifications N INNER JOIN Accounts A ON N.accountID = A.id WHERE N.type IN (?, ?, ?, ?) ORDER BY N.id DESC LIMIT 100");
        $notifications->execute(array(1, 2, 3, 4));
      ?>
      <?php if ($notifications->rowCount() > 0): ?>
        <?php foreach ($notifications as $readNotifications): ?>
          <a href="<?php echo (($readNotifications["type"] == 1) ? "/yonetim-paneli/destek/goruntule/".$readNotifications["variables"] : (($readNotifications["type"] == 2) ? "/yonetim-paneli/haber/yorum/duzenle/".$readNotifications["variables"] : "#")) ?>">
            <div class="notification-block card <?php echo ($readNotifications["creationDate"] > (($readAdmin["lastReadDate"]) ? $readAdmin["lastReadDate"] : '1000-01-01 00:00:00')) ? "active" : null; ?>">
              <div class="notification row text-muted">
                <div class="notification-icon col-auto">
                  <!--
                    1: Support
                    2: Comments
                    3: Credit History
                    4: Store Histroy
                  -->
                  <?php if ($readNotifications["type"] == 1): ?>
                    <i class="fe fe-life-buoy"></i>
                  <?php elseif ($readNotifications["type"] == 2): ?>
                    <i class="fe fe-message-circle"></i>
                  <?php elseif ($readNotifications["type"] == 3): ?>
                    <i class="fe fe-dollar-sign"></i>
                  <?php elseif ($readNotifications["type"] == 4): ?>
                    <i class="fe fe-shopping-cart"></i>
                  <?php else: ?>
                    <i class="fe fe-x-circle"></i>
                  <?php endif; ?>
                </div>
                <div class="notification-content col">
                  <div class="avatar avatar-xs d-inline-block mr-3">
                    <?php echo minecraftHead($readSettings["avatarAPI"], $readNotifications["realname"], 32, "avatar-img"); ?>
                  </div>
                  <strong class="text-primary mr-1"><?php echo $readNotifications["realname"]; ?> </strong>
                  <?php if ($readNotifications["type"] == 1): ?>
                    destek mesajı gönderdi!
                  <?php elseif ($readNotifications["type"] == 2): ?>
                    habere yorum yaptı!
                  <?php elseif ($readNotifications["type"] == 3): ?>
                    <?php echo $readNotifications["variables"]; ?> kredi yükledi!
                  <?php elseif ($readNotifications["type"] == 4): ?>
                    <?php $readNotifications["variables"] = explode(",", $readNotifications["variables"]); ?>
                    <?php echo $readNotifications["variables"][0]; ?> sunucusundan <?php echo $readNotifications["variables"][1]; ?> adlı ürünü satın aldı!
                  <?php else: ?>
                    HATA!
                  <?php endif; ?>
                </div>
                <div class="notification-time col-auto">
                  <?php echo convertTime($readNotifications["creationDate"]); ?>
                </div>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <?php if (checkPerm($readAdmin, 'MANAGE_UPDATES')): ?>
          <?php if ($needUpdate == false): ?>
            <?php echo alertError("Bu sayfaya ait veri bulunamadı!"); ?>
          <?php endif; ?>
        <?php else: ?>
          <?php echo alertError("Bu sayfaya ait veri bulunamadı!"); ?>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php
  if ($readAdmin["lastReadDate"]) {
    $updateAccountNoticationInfo = $db->prepare("UPDATE AccountNoticationInfo SET lastReadDate = ? WHERE accountID = ?");
    $updateAccountNoticationInfo->execute(array(date("Y-m-d H:i:s"), $readAdmin["id"]));
  }
  else {
    $insertAccountNoticationInfo = $db->prepare("INSERT INTO AccountNoticationInfo (accountID, lastReadDate) VALUES (?, ?)");
    $insertAccountNoticationInfo->execute(array($readAdmin["id"], date("Y-m-d H:i:s")));
  }
?>
