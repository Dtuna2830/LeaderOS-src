<?php
  if (!isset($_SESSION["login"])) {
    go("/giris-yap");
  }
?>
<section class="section profile-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Ana Sayfa</a></li>
            <?php if (isset($_GET["step"])): ?>
              <li class="breadcrumb-item"><a href="/profil">Profil</a></li>
              <?php if ($_GET["step"] == "update"): ?>
                <li class="breadcrumb-item active" aria-current="page">Profili Düzenle</li>
              <?php elseif ($_GET["step"] == "change-password"): ?>
                <li class="breadcrumb-item active" aria-current="page">Şifre Değiştir</li>
              <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page">Hata!</li>
              <?php endif; ?>
            <?php else: ?>
              <li class="breadcrumb-item active" aria-current="page">Profil</li>
            <?php endif; ?>
          </ol>
        </nav>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-img-profile">
            <a href="/profil">
              <?php echo minecraftHead($readSettings["avatarAPI"], $readAccount["realname"], 70); ?>
            </a>
          </div>
          <div class="card-body">
            <div class="form-group row">
              <label class="col-sm-5">Kullanıcı Adı:</label>
              <label class="col-sm-7">
                <?php echo $readAccount["realname"]; ?>
              </label>
            </div>
            <div class="form-group row">
              <label class="col-sm-5">E-Posta:</label>
              <label class="col-sm-7">
                <?php echo $readAccount["email"]; ?>
              </label>
            </div>
            <div class="form-group row">
              <label class="col-sm-5">Yetki:</label>
              <label class="col-sm-7">
                <?php echo styledRoles(getRoles($readAccount["id"])); ?>
              </label>
            </div>
            <div class="form-group row">
              <label class="col-sm-5"><?php echo $readSettings["creditText"] ?>:</label>
              <label class="col-sm-7">
                <?php echo $readAccount["credit"]; ?> <a class="text-success" href="/kredi/yukle"><i class="fa fa-plus-circle"></i></a>
              </label>
            </div>
            <div class="form-group row">
              <label class="col-sm-5">Son Giriş:</label>
              <label class="col-sm-7">
                <?php if ($readAccount["lastlogin"] == 0): ?>
                  Giriş Yapılmadı
                <?php else: ?>
                  <?php echo convertTime(date("Y-m-d H:i:s", ($readAccount["lastlogin"]/1000)), 2, true); ?>
                <?php endif; ?>
              </label>
            </div>
            <div class="form-group row">
              <label class="col-sm-5">Kayıt Tarihi:</label>
              <label class="col-sm-7">
                <?php if ($readAccount["creationDate"] == "1000-01-01 00:00:00"): ?>
                  Bilinmiyor
                <?php else: ?>
                  <?php echo convertTime($readAccount["creationDate"], 2, true); ?>
                <?php endif; ?>
              </label>
            </div>
            <?php if ($readSettings["authStatus"] == 1): ?>
              <div class="form-group row">
                <label class="col-sm-5">
                  2FA:
                  <a href="https://egitim.leaderos.com.tr/google-authenticator" rel="external">
                    <i class="fa fa-question-circle theme-color text-primary" data-toggle="tooltip" data-placement="top" title="İki Adımlı Doğrulama"></i>
                  </a>
                </label>
                <label class="col-sm-7">
                  <?php echo ($readAccount["authStatus"] == 0) ? "Kapalı" : "Açık"; ?>
                </label>
              </div>
            <?php endif; ?>
            <?php
              $accountSocialMedia = $db->prepare("SELECT * FROM AccountSocialMedia WHERE accountID = ?");
              $accountSocialMedia->execute(array($readAccount["id"]));
              $readAccountSocialMedia = $accountSocialMedia->fetch();
            ?>
            <div class="form-group row">
              <label class="col-sm-5">Skype:</label>
              <label class="col-sm-7">
                <?php if ($accountSocialMedia->rowCount() > 0): ?>
                  <?php echo (($readAccountSocialMedia["skype"] != '0') ? $readAccountSocialMedia["skype"] : "-"); ?>
                <?php else: ?>
                  -
                <?php endif; ?>
              </label>
            </div>
            <div class="form-group row">
              <label class="col-sm-5">Discord:</label>
              <label class="col-sm-7">
                <?php if ($accountSocialMedia->rowCount() > 0): ?>
                  <?php echo (($readAccountSocialMedia["discord"] != '0') ? $readAccountSocialMedia["discord"] : "-"); ?>
                <?php else: ?>
                  -
                <?php endif; ?>
              </label>
            </div>
            <?php
              $siteBannedAccountStatus = $db->prepare("SELECT * FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?) ORDER BY expiryDate DESC LIMIT 1");
              $siteBannedAccountStatus->execute(array($readAccount["id"], 1, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
              $readSiteBannedAccountStatus = $siteBannedAccountStatus->fetch();
            ?>
            <?php if ($siteBannedAccountStatus->rowCount() > 0): ?>
              <div class="form-group row">
                <label class="col-sm-5">Site Engel:</label>
                <label class="col-sm-7">
                  <?php echo ($readSiteBannedAccountStatus["expiryDate"] == '1000-01-01 00:00:00') ? 'Süresiz' : getDuration($readSiteBannedAccountStatus["expiryDate"]).' gün'; ?>
                </label>
              </div>
            <?php endif; ?>
            <?php
              $supportBannedAccountStatus = $db->prepare("SELECT * FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?) ORDER BY expiryDate DESC LIMIT 1");
              $supportBannedAccountStatus->execute(array($readAccount["id"], 2, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
              $readSupportBannedAccountStatus = $supportBannedAccountStatus->fetch();
            ?>
            <?php if ($supportBannedAccountStatus->rowCount() > 0): ?>
              <div class="form-group row">
                <label class="col-sm-5">Destek Engel:</label>
                <label class="col-sm-7">
                  <?php echo ($readSupportBannedAccountStatus["expiryDate"] == '1000-01-01 00:00:00') ? 'Süresiz' : getDuration($readSupportBannedAccountStatus["expiryDate"]).' gün'; ?>
                </label>
              </div>
            <?php endif; ?>
            <?php
              $commentBannedAccountStatus = $db->prepare("SELECT * FROM BannedAccounts WHERE accountID = ? AND categoryID = ? AND (expiryDate > ? OR expiryDate = ?) ORDER BY expiryDate DESC LIMIT 1");
              $commentBannedAccountStatus->execute(array($readAccount["id"], 3, date("Y-m-d H:i:s"), '1000-01-01 00:00:00'));
              $readCommentBannedAccountStatus = $commentBannedAccountStatus->fetch();
            ?>
            <?php if ($commentBannedAccountStatus->rowCount() > 0): ?>
              <div class="form-group row">
                <label class="col-sm-5">Yorum Engel:</label>
                <label class="col-sm-7">
                  <?php echo ($readCommentBannedAccountStatus["expiryDate"] == '1000-01-01 00:00:00') ? 'Süresiz' : getDuration($readCommentBannedAccountStatus["expiryDate"]).' gün'; ?>
                </label>
              </div>
            <?php endif; ?>
            <div class="row justify-content-between">
              <div class="col-md-6 btn-account-edit">
                <a class="btn btn-success w-100" href="/profil/duzenle">Profili Düzenle</a>
              </div>
              <div class="col-md-6 btn-account-password">
                <a class="btn btn-primary w-100" href="/profil/sifre-degistir">Şifreyi Değiştir</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <?php if (get("target") == 'profile'): ?>
          <?php if (get("action") == 'get'): ?>
            <?php
              $statServers = $db->query("SELECT serverName, serverSlug FROM Leaderboards");
              $statServers->execute();
            ?>
            <?php if ($statServers->rowCount() > 0): ?>
              <div class="card">
                <div class="card-body p-0">
                  <nav>
                    <div class="nav nav-tabs nav-fill">
                      <?php foreach ($statServers as $readStatServers): ?>
                        <?php
                          if (!get("siralama")) {
                            $_GET["siralama"] = $readStatServers["serverSlug"];
                          }
                        ?>
                        <a class="nav-item nav-link <?php echo (get("siralama") == $readStatServers["serverSlug"]) ? "active" : null; ?>" id="nav-<?php echo $readStatServers["serverSlug"]; ?>-tab" href="?siralama=<?php echo $readStatServers["serverSlug"]; ?>">
                          <?php echo $readStatServers["serverName"]; ?>
                        </a>
                      <?php endforeach; ?>
                    </div>
                  </nav>
                  <div class="tab-content" id="nav-tabContent">
                    <?php
                      $statServer = $db->query("SELECT * FROM Leaderboards");
                      $statServer->execute();
                    ?>
                    <?php foreach ($statServer as $readStatServer): ?>
                      <?php
                        $usernameColumn = $readStatServer["usernameColumn"];
                        $mysqlTable = $readStatServer["mysqlTable"];
                        $sorter = $readStatServer["sorter"];
                        $tableTitles = $readStatServer["tableTitles"];
                        $tableData = $readStatServer["tableData"];
                        $tableTitlesArray = explode(",", $tableTitles);
                        $tableDataArray = explode(",", $tableData);

                        if ($readStatServer["mysqlServer"] == '0') {
                          $accountOrder = $db->prepare("SELECT $usernameColumn,$tableData FROM $mysqlTable WHERE $usernameColumn = ? ORDER BY $sorter DESC LIMIT 1");
                          $accountOrder->execute(array($readAccount["realname"]));
                        }
                        else {
                          try {
                            $newDB = new PDO("mysql:host=".$readStatServer["mysqlServer"]."; port=".$readStatServer["mysqlPort"]."; dbname=".$readStatServer["mysqlDatabase"]."; charset=utf8", $readStatServer["mysqlUsername"], $readStatServer["mysqlPassword"]);
                          }
                          catch (PDOException $e) {
                            die("<strong>MySQL bağlantı hatası:</strong> ".utf8_encode($e->getMessage()));
                          }
                          $accountOrder = $newDB->prepare("SELECT $usernameColumn,$tableData FROM $mysqlTable WHERE $usernameColumn = ? ORDER BY $sorter DESC LIMIT 1");
                          $accountOrder->execute(array($readAccount["realname"]));
                        }
                      ?>
                      <div class="tab-pane fade <?php echo (get("siralama") == $readStatServer["serverSlug"]) ? "show active" : null; ?>" id="nav-<?php echo $readStatServer["serverSlug"] ?>">
                        <?php if ($accountOrder->rowCount() > 0): ?>
                          <div class="table-responsive">
                            <table class="table table-hover">
                              <thead>
                                <tr>
                                  <th class="text-center" style="width: 40px;">Sıra</th>
                                  <th class="text-center" style="width: 20px;">#</th>
                                  <th>Kullanıcı Adı</th>
                                  <?php
                                    foreach ($tableTitlesArray as $readTableTitles) {
                                      echo '<th class="text-center">'.$readTableTitles.'</th>';
                                    }
                                  ?>
                                </tr>
                              </thead>
                              <tbody>
                                <?php foreach ($accountOrder as $readAccountOrder): ?>
                                  <tr>
                                    <td class="text-center" style="width: 40px;">
                                      <?php
                                        if ($readStatServer["mysqlServer"] == '0') {
                                          $userPosition = $db->prepare("SELECT $usernameColumn FROM $mysqlTable ORDER BY $sorter DESC");
                                          $userPosition->execute();
                                        }
                                        else {
                                          $userPosition = $newDB->prepare("SELECT $usernameColumn FROM $mysqlTable ORDER BY $sorter DESC");
                                          $userPosition->execute();
                                        }
                                      ?>
                                      <?php $rank = 1; ?>
                                      <?php foreach ($userPosition as $readUserPosition): ?>
                                        <?php if ($readUserPosition[$usernameColumn] == $readAccount["realname"]): ?>
                                          <?php if ($rank == 1): ?>
                                            <strong class="text-success">1</strong>
                                          <?php elseif ($rank == 2): ?>
                                            <strong class="text-warning">2</strong>
                                          <?php elseif ($rank == 3): ?>
                                            <strong class="text-danger">3</strong>
                                          <?php else: ?>
                                            <?php echo $rank; ?>
                                          <?php endif; ?>
                                          <?php break; ?>
                                        <?php endif; ?>
                                        <?php $rank++; ?>
                                      <?php endforeach; ?>
                                    </td>
                                    <td class="text-center" style="width: 20px;">
                                      <?php echo minecraftHead($readSettings["avatarAPI"], $readAccount["realname"], 20); ?>
                                    </td>
                                    <td>
                                      <?php echo $readAccount["realname"]; ?>
                                    </td>
                                    <?php foreach ($tableDataArray as $readTableData): ?>
                                      <td class="text-center"><?php echo $readAccountOrder[$readTableData]; ?></td>
                                    <?php endforeach; ?>
                                  </tr>
                                <?php endforeach; ?>
                              </tbody>
                            </table>
                          </div>
                        <?php else: ?>
                          <div class="p-4"><?php echo alertError("Bu sunucuda size ait sıralama kaydı bulunmamaktadır!", false); ?></div>
                        <?php endif; ?>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            <?php endif; ?>
            <div class="card">
              <div class="card-body p-0">
                <nav>
                  <div class="nav nav-tabs nav-fill" id="nav-profile-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-support-tab" data-toggle="tab" href="#nav-support" role="tab" aria-controls="nav-support" aria-selected="true">Destek Mesajları</a>
                    <a class="nav-item nav-link" id="nav-credit-history-tab" data-toggle="tab" href="#nav-credit-history" role="tab" aria-controls="nav-credit-history" aria-selected="false"><?php echo $readSettings["creditText"] ?> Geçmişi</a>
                    <a class="nav-item nav-link" id="nav-store-history-tab" data-toggle="tab" href="#nav-store-history" role="tab" aria-controls="nav-store-history" aria-selected="false">Mağaza Geçmişi</a>
                  </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                  <div class="tab-pane fade show active" id="nav-support" role="tabpanel" aria-labelledby="nav-support-tab">
                    <?php
                      $supports = $db->prepare("SELECT S.*, SC.name as categoryName, Se.name as serverName FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id WHERE S.accountID = ? ORDER BY S.updateDate DESC LIMIT 50");
                      $supports->execute(array($readAccount["id"]));
                    ?>
                    <?php if ($supports->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($supports->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th class="text-center" style="width: 40px;">ID</th>
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
                                  <a href="/destek/goruntule/<?php echo $readSupports["id"]; ?>/">
                                    #<?php echo $readSupports["id"]; ?>
                                  </a>
                                </td>
                                <td>
                                  <a href="/destek/goruntule/<?php echo $readSupports["id"]; ?>/">
                                    <?php echo $readSupports["title"]; ?>
                                  </a>
                                </td>
                                <td>
                                  <?php echo $readSupports["categoryName"]; ?>
                                </td>
                                <td>
                                  <?php echo convertTime($readSupports["updateDate"]); ?>
                                </td>
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
                                  <a class="btn btn-success btn-circle" href="/destek/goruntule/<?php echo $readSupports["id"]; ?>/" data-toggle="tooltip" data-placement="top" title="Mesajı Oku">
                                    <i class="fa fa-eye"></i>
                                  </a>
                                  <a class="btn btn-danger btn-circle clickdelete" href="/destek/sil/<?php echo $readSupports["id"]; ?>/" data-toggle="tooltip" data-placement="top" title="Mesajı Sil">
                                    <i class="fa fa-trash"></i>
                                  </a>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError("Size ait destek mesajı bulunmamaktadır!", false); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="tab-pane fade" id="nav-credit-history" role="tabpanel" aria-labelledby="nav-credit-history-tab">
                    <?php
                      $creditHistory = $db->prepare("SELECT * FROM CreditHistory WHERE accountID = ? AND paymentStatus = ? ORDER BY id DESC LIMIT 50");
                      $creditHistory->execute(array($readAccount["id"], 1));
                    ?>
                    <?php if ($creditHistory->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($creditHistory->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th class="text-center">ID</th>
                              <th class="text-center">Miktar</th>
                              <th class="text-center">Ödeme</th>
                              <th>Tarih</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($creditHistory as $readCreditHistory): ?>
                              <tr>
                                <td class="text-center">#<?php echo $readCreditHistory["id"]; ?></td>
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
                                <td><?php echo convertTime($readCreditHistory["creationDate"], 2, true); ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError("Size ait ". $readSettings["creditText"] ." geçmişi bulunmamaktadır!", false); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="tab-pane fade" id="nav-store-history" role="tabpanel" aria-labelledby="nav-store-history-tab">
                    <?php
                      $storeHistory = $db->prepare("SELECT * FROM Orders WHERE accountID = ? ORDER BY id DESC LIMIT 50");
                      $storeHistory->execute(array($readAccount["id"]));
                    ?>
                    <?php if ($storeHistory->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($storeHistory->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th class="text-center">ID</th>
                              <th>Ödenen Tutar</th>
                              <th>Tarih</th>
                              <th class="text-right">&nbsp;</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($storeHistory as $readStoreHistory): ?>
                              <tr>
                                <td class="text-center">#<?php echo $readStoreHistory["id"]; ?></td>
                                <td><?php echo $readStoreHistory["subtotal"]." ".$readSettings["creditText"]; ?></td>
                                <td><?php echo convertTime($readStoreHistory["creationDate"], 2, true); ?></td>
                                <td class="text-right">
                                  <a class="btn btn-success btn-circle" href="/siparis/goruntule/<?php echo $readStoreHistory["id"]; ?>/" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                                    <i class="fa fa-eye"></i>
                                  </a>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError("Size ait mağaza geçmişi bulunmamaktadır!", false); ?></div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-body p-0">
                <nav>
                  <div class="nav nav-tabs nav-fill" id="nav-profile-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-lottery-history-tab" data-toggle="tab" href="#nav-lottery-history" role="tab" aria-controls="nav-lottery-history" aria-selected="false">Çarkıfelek Geçmişi</a>
                    <a class="nav-item nav-link" id="nav-gift-history-tab" data-toggle="tab" href="#nav-gift-history" role="tab" aria-controls="nav-gift-history" aria-selected="false">Hediye Geçmişi</a>
                    <a class="nav-item nav-link" id="nav-chest-history-tab" data-toggle="tab" href="#nav-chest-history" role="tab" aria-controls="nav-chest-history" aria-selected="false">Sandık Geçmişi</a>
                  </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                  <div class="tab-pane fade show active" id="nav-lottery-history" role="tabpanel" aria-labelledby="nav-lottery-history-tab">
                    <?php
                      $lotteryHistory = $db->prepare("SELECT LH.*, L.title as lotteryTitle, LA.title as awardTitle, LA.awardType, LA.award FROM LotteryHistory LH INNER JOIN LotteryAwards LA ON LH.lotteryAwardID = LA.id INNER JOIN Lotteries L ON LA.lotteryID = L.id WHERE LH.accountID = ? AND LA.awardType != ? ORDER by LH.id DESC LIMIT 50");
                      $lotteryHistory->execute(array($readAccount["id"], 3));
                    ?>
                    <?php if ($lotteryHistory->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($lotteryHistory->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th class="text-center">ID</th>
                              <th>Çarkıfelek</th>
                              <th>Ödül</th>
                              <th>Tarih</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($lotteryHistory as $readLotteryHistory): ?>
                              <tr>
                                <td class="text-center">#<?php echo $readLotteryHistory["id"]; ?></td>
                                <td>
                                  <?php echo $readLotteryHistory["lotteryTitle"]; ?>
                                </td>
                                <td>
                                  <?php echo $readLotteryHistory["awardTitle"]; ?>
                                </td>
                                <td><?php echo convertTime($readLotteryHistory["creationDate"], 2, true); ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError("Size ait çarkıfelek geçmişi bulunmamaktadır!", false); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="tab-pane fade" id="nav-gift-history" role="tabpanel" aria-labelledby="nav-gift-history-tab">
                    <?php
                      $giftHistory = $db->prepare("SELECT PGH.*, PG.name, PG.giftType, PG.gift FROM ProductGiftsHistory PGH INNER JOIN ProductGifts PG ON PGH.giftID = PG.id WHERE PGH.accountID = ? ORDER by PGH.id DESC LIMIT 50");
                      $giftHistory->execute(array($readAccount["id"]));
                    ?>
                    <?php if ($giftHistory->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($giftHistory->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th class="text-center">ID</th>
                              <th>Kod</th>
                              <th>Hediye</th>
                              <th>Tarih</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($giftHistory as $readGiftHistory): ?>
                              <tr>
                                <td class="text-center">#<?php echo $readGiftHistory["id"]; ?></td>
                                <td>
                                  <?php echo $readGiftHistory["name"]; ?>
                                </td>
                                <td>
                                  <?php if ($readGiftHistory["giftType"] == 1): ?>
                                    <?php
                                      $product = $db->prepare("SELECT name FROM Products WHERE id = ?");
                                      $product->execute(array($readGiftHistory["gift"]));
                                      $readProduct = $product->fetch();
                                      echo $readProduct["name"];
                                    ?>
                                  <?php else: ?>
                                    <?php echo $readGiftHistory["gift"]; ?> <?php echo $readSettings["creditText"] ?>
                                  <?php endif; ?>
                                </td>
                                <td><?php echo convertTime($readGiftHistory["creationDate"], 2, true); ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError("Size ait hediye geçmişi bulunmamaktadır!", false); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="tab-pane fade" id="nav-chest-history" role="tabpanel" aria-labelledby="nav-chest-history-tab">
                    <?php
                      $chestsHistory = $db->prepare("SELECT CH.*, P.name as productName, S.name as serverName FROM ChestsHistory CH INNER JOIN Chests C ON CH.chestID = C.id INNER JOIN Products P ON C.productID = P.id INNER JOIN Servers S ON P.serverID = S.id WHERE CH.accountID = ? ORDER BY CH.id DESC LIMIT 5");
                      $chestsHistory->execute(array($readAccount["id"]));
                    ?>
                    <?php if ($chestsHistory->rowCount() > 0): ?>
                      <div class="table-responsive" <?php echo ($chestsHistory->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th class="text-center">ID</th>
                              <th>Ürün</th>
                              <th>Sunucu</th>
                              <th class="text-center">İşlem</th>
                              <th>Tarih</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($chestsHistory as $readChestsHistory): ?>
                              <tr>
                                <td class="text-center">
                                  #<?php echo $readChestsHistory["id"]; ?>
                                </td>
                                <td><?php echo $readChestsHistory["productName"]; ?></td>
                                <td><?php echo $readChestsHistory["serverName"]; ?></td>
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
                                <td><?php echo convertTime($readChestsHistory["creationDate"], 2, true); ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="p-4"><?php echo alertError("Size ait sandık geçmişi bulunmamaktadır!", false); ?></div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
            <?php if ($readSettings["bazaarStatus"] == 1): ?>
              <div class="card">
                <div class="card-body p-0">
                  <nav>
                    <div class="nav nav-tabs nav-fill" role="tablist">
                      <a class="nav-item nav-link active" href="#!" role="tab">Pazar Geçmişi</a>
                    </div>
                  </nav>
                  <div class="tab-content">
                    <div class="tab-pane fade show active" id="nav-bazaar-history" role="tabpanel" aria-labelledby="nav-lottery-bazaar-tab">
                      <?php
                        $bazaarHistory = $db->prepare("SELECT BH.*, BI.name as itemName, BI.price as itemPrice FROM BazaarHistory BH INNER JOIN BazaarItems BI ON BH.itemID = BI.id WHERE BH.accountID = ? ORDER BY BH.id DESC LIMIT 50");
                        $bazaarHistory->execute(array($readAccount["id"]));
                      ?>
                      <?php if ($bazaarHistory->rowCount() > 0): ?>
                        <div class="table-responsive" <?php echo ($bazaarHistory->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                          <table class="table table-hover">
                            <thead>
                            <tr>
                              <th class="text-center">ID</th>
                              <th>Ürün</th>
                              <th>Fiyat</th>
                              <th>Durum</th>
                              <th>Tarih</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($bazaarHistory as $readBazaarHistory): ?>
                              <tr>
                                <td class="text-center">#<?php echo $readBazaarHistory["id"]; ?></td>
                                <td>
                                  <?php echo $readBazaarHistory["itemName"]; ?>
                                </td>
                                <td>
                                  <?php echo $readBazaarHistory["itemPrice"]; ?>
                                </td>
                                <td class="text-center">
                                  <?php if ($readBazaarHistory["type"] == 0): ?>
                                    <span class="text-danger" data-toggle="tooltip" data-placement="top" title="Satın Alındı">-<i class="fa fa-coins"></i></span>
                                  <?php elseif ($readBazaarHistory["type"] == 1): ?>
                                    <span class="text-success" data-toggle="tooltip" data-placement="top" title="Satıldı">+<i class="fa fa-coins"></i></span>
                                  <?php else: ?>
                                    <i class="fa fa-check"></i>
                                  <?php endif; ?>
                                </td>
                                <td><?php echo convertTime($readBazaarHistory["creationDate"], 2, true); ?></td>
                              </tr>
                            <?php endforeach; ?>
                            </tbody>
                          </table>
                        </div>
                      <?php else: ?>
                        <div class="p-4"><?php echo alertError("Size ait pazar geçmişi bulunmamaktadır!", false); ?></div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>
            <?php
            $applications = $db->prepare("SELECT AP.id, AF.title, AP.reason, AP.status FROM Applications AP INNER JOIN Accounts A ON A.id = AP.accountID INNER JOIN ApplicationForms AF ON AF.id = AP.formID WHERE AP.accountID = ? ORDER BY AP.id DESC LIMIT 50");
            $applications->execute(array($readAccount["id"]));
            ?>
            <?php if ($applications->rowCount() > 0): ?>
              <div class="card">
                <div class="card-body p-0">
                  <nav>
                    <div class="nav nav-tabs nav-fill" role="tablist">
                      <a class="nav-item nav-link active" href="#!" role="tab">Yetkili Başvurularım</a>
                    </div>
                  </nav>
                  <div class="tab-content">
                    <div class="tab-pane fade show active" role="tabpanel">
                      <div class="table-responsive" <?php echo ($applications->rowCount() > 10) ? 'style="height: 400px; overflow:auto;"' : null; ?>>
                        <table class="table table-hover">
                          <thead>
                          <tr>
                            <th class="text-center" style="width: 40px;">ID</th>
                            <th>Form</th>
                            <th>Sebep</th>
                            <th class="text-center">Durum</th>
                            <th class="text-right"> </th>
                          </tr>
                          </thead>
                          <tbody>
                          <?php foreach ($applications as $readApplications): ?>
                            <tr>
                              <td class="text-center" style="width: 40px;">
                                <a href="/applications/<?php echo $readApplications["id"]; ?>">
                                  #<?php echo $readApplications["id"]; ?>
                                </a>
                              </td>
                              <td>
                                <a href="/applications/<?php echo $readApplications["id"]; ?>">
                                  <?php echo $readApplications["title"]; ?>
                                </a>
                              </td>
                              <td>
                                <?php echo ($readApplications["reason"] == '') ? '-' : $readApplications["reason"]; ?>
                              </td>
                              <td class="text-center">
                                <?php if ($readApplications["status"] == 0): ?>
                                  <span class="badge badge-pill badge-danger">Reddedildi</span>
                                <?php elseif ($readApplications["status"] == 1): ?>
                                  <span class="badge badge-pill badge-success">Onaylandı</span>
                                <?php elseif ($readApplications["status"] == 2): ?>
                                  <span class="badge badge-pill badge-warning">Onay Bekliyor</span>
                                <?php else: ?>
                                  <span class="badge badge-pill badge-danger">Hata!</span>
                                <?php endif; ?>
                              </td>
                              <td class="text-right">
                                <a class="btn btn-success btn-circle" href="/yetkili-basvuru/<?php echo $readApplications["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                                  <i class="fa fa-eye"></i>
                                </a>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          <?php elseif (get("action") == 'update'): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');

              $accountSocialMedia = $db->prepare("SELECT * FROM AccountSocialMedia WHERE accountID = ?");
              $accountSocialMedia->execute(array($readAccount["id"]));
              $readAccountSocialMedia = $accountSocialMedia->fetch();

              if (isset($_POST["updateAccounts"])) {
                if (post("skype") == null) {
                  $_POST["skype"] = '0';
                }
                if (post("discord") == null) {
                  $_POST["discord"] = '0';
                }
                if (!$csrf->validate('updateAccounts')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("email") == null || ($readSettings["authStatus"] == 1 && post("authStatus") == null)) {
                  echo alertError("Lütfen zorunlu alanları doldurunuz!");
                }
                else if (post("password") == null) {
                  echo alertError("Değişiklerin kaydedilmesi şifrenizi giriniz!");
                }
                else {
                  $emailValid = $db->prepare("SELECT * FROM Accounts WHERE email = ?");
                  $emailValid->execute(array(post("email")));

                  $password = checkPassword($readSettings["passwordType"], post("password"), $readAccount["password"]);
                  if (!$password) {
                    echo alertError("Şifrenizi yanlış girdiniz!");
                  }
                  else if (checkEmail(post("email"))) {
                    echo alertError("Lütfen geçerli bir email adresi giriniz!");
                  }
                  else if (post("email") != $readAccount["email"] && $emailValid->rowCount() > 0) {
                    echo alertError('<strong>'.post("email").'</strong> başkası tarafından kullanılıyor!');
                  }
                  else {
                    if ($readAccount["email"] != post("email")) {
                      $loginToken = md5(uniqid(mt_rand(), true));
                      $updateAccounts = $db->prepare("UPDATE Accounts SET email = ? WHERE id = ?");
                      $updateAccounts->execute(array(post("email"), $readAccount["id"]));
                      $deleteAccountSessions = $db->prepare("DELETE FROM AccountSessions WHERE accountID = ?");
                      $deleteAccountSessions->execute(array($readAccount["id"]));
                      $insertAccountSessions = $db->prepare("INSERT INTO AccountSessions (accountID, loginToken, creationIP, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?)");
                      $insertAccountSessions->execute(array($readAccount["id"], $loginToken, getIP(), createDuration(((isset($_COOKIE["rememberMe"])) ? 365 : 0.01666666666)), date("Y-m-d H:i:s")));
                      $_SESSION["login"] = $loginToken;
                      if (isset($_COOKIE["rememberMe"])) {
                        createCookie("rememberMe", $loginToken, 365, $sslStatus);
                      }
                    }
                    if ($accountSocialMedia->rowCount() > 0) {
                      $updateAccountSocialMedia = $db->prepare("UPDATE AccountSocialMedia SET skype = ?, discord = ? WHERE accountID = ?");
                      $updateAccountSocialMedia->execute(array(post("skype"), post("discord"), $readAccount["id"]));
                    }
                    else {
                      $insertAccountSocialMedia = $db->prepare("INSERT INTO AccountSocialMedia (accountID, skype, discord) VALUES (?, ?, ?)");
                      $insertAccountSocialMedia->execute(array($readAccount["id"], post("skype"), post("discord")));
                    }

                    if ($readSettings["authStatus"] == 1 && (post("authStatus") == 0 || post("authStatus") == 1)) {
                      if (post("authStatus") == 1 && $readAccount["authStatus"] == 0) {
                        $deleteAccountSessions = $db->prepare("DELETE FROM AccountSessions WHERE accountID = ? AND loginToken = ? AND creationIP = ?");
                        $deleteAccountSessions->execute(array($readAccount["id"], $_SESSION["login"], getIP()));
                        unset($_SESSION["login"]);
                        $_SESSION["tfa"] = array(
                          'accountID'     => $readAccount["id"],
                          'profileUpdate' => 'true',
                          'rememberMe'    => (isset($_COOKIE["rememberMe"])) ? 'true' : 'false',
                          'ipAddress'     => getIP(),
                          'expiryDate'    => createDuration(0.00347222222)
                        );
                        removeCookie("rememberMe");
                        go("/dogrulama");
                      }
                      else {
                        $deleteAccountAuths = $db->prepare("DELETE FROM AccountAuths WHERE accountID = ?");
                        $deleteAccountAuths->execute(array($readAccount["id"]));
                        $updateAccounts = $db->prepare("UPDATE Accounts SET authStatus = ? WHERE id = ?");
                        $updateAccounts->execute(array(0, $readAccount["id"]));
                      }
                    }

                    echo alertSuccess("Profiliniz başarıyla düzenlenmiştir!");
                  }
                }
              }
            ?>
            <div class="card">
              <div class="card-header">
                Profili Düzenle
              </div>
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label class="col-sm-3">Kullanıcı Adı:</label>
                    <div class="col-sm-9">
                      <?php echo $readAccount["realname"]; ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-3">Email Adresi:</label>
                    <div class="col-sm-9">
                      <input type="email" class="form-control" name="email" placeholder="Örn: merhaba@gmail.com" value="<?php echo $readAccount["email"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-3">Skype:</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="skype" placeholder="Skype adresinizi giriniz." value="<?php echo (($accountSocialMedia->rowCount() > 0 && $readAccountSocialMedia["skype"] != '0') ? $readAccountSocialMedia["skype"] : null); ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-3">Discord:</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="discord" placeholder="Discord adresinizi giriniz." value="<?php echo (($accountSocialMedia->rowCount() > 0 && $readAccountSocialMedia["discord"] != '0') ? $readAccountSocialMedia["discord"] : null); ?>">
                    </div>
                  </div>
                  <?php if ($readSettings["authStatus"] == 1): ?>
                    <div class="form-group row">
                      <label class="col-sm-3">
                        2FA:
                        <a href="https://egitim.leaderos.com.tr/google-authenticator" rel="external">
                          <i class="fa fa-question-circle theme-color text-primary" data-toggle="tooltip" data-placement="top" title="İki Adımlı Doğrulama"></i>
                        </a>
                      </label>
                      <div class="col-sm-9">
                        <select class="form-control" name="authStatus" data-toggle="select2">
                          <option value="0" <?php echo ($readAccount["authStatus"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                          <option value="1" <?php echo ($readAccount["authStatus"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                        </select>
                      </div>
                    </div>
                  <?php endif; ?>
                  <hr>
                  <div class="form-group row">
                    <label class="col-sm-3">Şifre:</label>
                    <div class="col-sm-9">
                      <input type="password" class="form-control" name="password" placeholder="Değişiklerin onaylanması için mevcut şifrenizi giriniz.">
                    </div>
                  </div>
                  <?php echo $csrf->input('updateAccounts'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <button type="submit" class="btn btn-success btn-rounded" name="updateAccounts">Kaydet</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php elseif (get("action") == 'change-password'): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["changePassword"])) {
                if (!$csrf->validate('changePassword')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if ((post("currentPassword") == null) || (post("password") == null) || (post("passwordRe") == null)) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  $currentPassword = checkPassword($readSettings["passwordType"], post("currentPassword"), $readAccount["password"]);
                  if (!$currentPassword) {
                    echo alertError("Mevcut şifreyi yanlış girdiniz!");
                  }
                  else if (strlen(post("password")) < 4) {
                    echo alertError("Şifreniz 4 karakterden az olamaz!");
                  }
                  else if (post("password") != post("passwordRe")) {
                    echo alertError("Yeni şifreniz tekrarı ile uyuşmuyor!");
                  }
                  else if (checkBadPassword(post("password"))) {
                    echo alertError("Basit şifreler kullanamazsınız!");
                  }
                  else {
                    $loginToken = md5(uniqid(mt_rand(), true));
                    $password = createPassword($readSettings["passwordType"], post("password"));
                    $updateAccounts = $db->prepare("UPDATE Accounts SET password = ? WHERE id = ?");
                    $updateAccounts->execute(array($password, $readAccount["id"]));
                    $deleteAccountSessions = $db->prepare("DELETE FROM AccountSessions WHERE accountID = ?");
                    $deleteAccountSessions->execute(array($readAccount["id"]));
                    $insertAccountSessions = $db->prepare("INSERT INTO AccountSessions (accountID, loginToken, creationIP, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?)");
                    $insertAccountSessions->execute(array($readAccount["id"], $loginToken, getIP(), createDuration(((isset($_COOKIE["rememberMe"])) ? 365 : 0.01666666666)), date("Y-m-d H:i:s")));
                    echo alertSuccess("Şifreniz başarıyla değiştirilmiştir!");
                    $_SESSION["login"] = $loginToken;
                    if (isset($_COOKIE["rememberMe"])) {
                      createCookie("rememberMe", $loginToken, 365, $sslStatus);
                    }
                  }
                }
              }
            ?>
            <div class="card">
              <div class="card-header">
                Şifre Değiştir
              </div>
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label class="col-sm-3">Mevcut Şifre:</label>
                    <div class="col-sm-9">
                      <input type="password" class="form-control" name="currentPassword" placeholder="Mevcut şifrenizi giriniz">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-3">Yeni Şifre:</label>
                    <div class="col-sm-9">
                      <input type="password" class="form-control" name="password" placeholder="Yeni şifrenizi giriniz">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-sm-3">Yeni Şifre (Tekrar):</label>
                    <div class="col-sm-9">
                      <input type="password" class="form-control" name="passwordRe" placeholder="Yeni şifrenizi tekrar giriniz">
                    </div>
                  </div>
                  <?php echo $csrf->input('changePassword'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <button type="submit" class="btn btn-success btn-rounded" name="changePassword">Değiştir</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php go('/404'); ?>
          <?php endif; ?>
        <?php else: ?>
          <?php go('/404'); ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
