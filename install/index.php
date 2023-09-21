<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/status.php");
  require_once(__ROOT__."/apps/install/private/config/functions.php");

  if (INSTALL_STATUS == true) {
    go("/");
  }
?>
<!DOCTYPE html>
<html lang="tr">
  <head>
    <?php require_once(__ROOT__."/apps/install/private/layouts/head.php"); ?>
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3 installBlock">
          <div class="wizard animated fadeIn">
            <div class="progress">
              <div class="progressLine" width="12.5"></div>
            </div>
            <div class="step active">
              <div class="step-icon">
                <i class="fa fa-database"></i>
              </div>
              <p>Veritabanı</p>
            </div>
            <div class="step">
              <div class="step-icon">
                <i class="fa fa-sliders"></i>
              </div>
              <p>Site Ayarları</p>
            </div>
            <div class="step">
              <div class="step-icon">
                <i class="fa fa-user-plus"></i>
              </div>
              <p>Hesap</p>
            </div>
            <div class="step">
              <div class="step-icon">
                <i class="fa fa-check"></i>
              </div>
              <p>Tamamlandı</p>
            </div>
          </div>
          <div class="alert alert-danger animated fadeIn"></div>
          <form id="installForm" method="post" autocomplete="off">
            <div class="card" style="display: block;">
              <div class="card-header">
                MySQL Veritabanı Bilgileri
              </div>
              <div id="loader" class="card-body is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="form-group">
                  <label>MySQL Sunucusu:</label>
                  <input type="text" class="form-control" name="mysqlServer" placeholder="Örnek: localhost">
                </div>
                <div class="form-group">
                  <label>MySQL Portu:</label>
                  <input type="number" class="form-control" name="mysqlPort" placeholder="Örnek: 3306">
                </div>
                <div class="form-group">
                  <label>MySQL Kullanıcı Adı:</label>
                  <input type="text" class="form-control" name="mysqlUsername" placeholder="Örnek: root">
                </div>
                <div class="form-group">
                  <label>MySQL Şifresi:</label>
                  <input type="password" class="form-control" name="mysqlPassword" placeholder="Örnek: 123456">
                </div>
                <div class="form-group">
                  <label>MySQL Veritabanı Adı:</label>
                  <input type="text" class="form-control" name="mysqlDatabase" placeholder="Örnek: database">
                </div>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="button" class="btn btn-next" index="0">İleri</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-header">
                Site Ayarları
              </div>
              <div id="loader" class="card-body">
                <div id="spinner" style="display: none;">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="form-group">
                  <label>Sunucu Adı:</label>
                  <input type="text" class="form-control" name="siteServerName" placeholder="Örnek: XXX Craft">
                </div>
                <div class="form-group">
                  <label>Site Sloganı:</label>
                  <input type="text" class="form-control" name="siteSlogan" placeholder="Örnek: En İyi Sunucu!">
                </div>
                <div class="form-group">
                  <label>Sunucu IP:</label>
                  <input type="text" class="form-control" name="siteServerIP" placeholder="Örnek: play.xxxcraft.com">
                </div>
                <div class="form-group">
                  <label>Sunucu Sürümü:</label>
                  <input type="text" class="form-control" name="siteServerVersion" placeholder="Örnek: 1.8.X">
                </div>
                <div class="form-group">
                  <label>Şifreleme:</label>
                  <select class="form-control" name="sitePasswordType">
                    <option value="1">SHA256</option>
                    <option value="2">MD5</option>
                    <option value="3">BCRYPT</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Bakım Modu:</label>
                  <select class="form-control" name="siteMaintenance">
                    <option value="0">Kapalı</option>
                    <option value="1">Açık</option>
                  </select>
                </div>
                <div class="row">
                  <div class="col">
                    <button type="button" class="btn btn-prev">Geri</button>
                  </div>
                  <div class="col-auto">
                    <button type="button" class="btn btn-next" index="1">İleri</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-header">
                Yönetici Hesabı Oluştur
              </div>
              <div id="loader" class="card-body">
                <div id="spinner" style="display: none;">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="form-group">
                  <label>Kullanıcı Adı:</label>
                  <input type="text" class="form-control" name="accountUsername" placeholder="Örnek: user" autocomplete="username">
                </div>
                <div class="form-group">
                  <label>E-Mail Adresi:</label>
                  <input type="email" class="form-control" name="accountEmail" placeholder="Örnek: user@domain.com" autocomplete="email">
                </div>
                <div class="form-group">
                  <label>Şifre:</label>
                  <input type="password" class="form-control" name="accountPassword" placeholder="Örnek: 123456">
                </div>
                <div class="form-group">
                  <label>Şifre (Tekrar):</label>
                  <input type="password" class="form-control" name="accountPasswordRe" placeholder="Örnek: 123456">
                </div>
                <div class="row">
                  <div class="col">
                    <button type="button" class="btn btn-prev">Geri</button>
                  </div>
                  <div class="col-auto">
                    <button type="submit" class="btn btn-submit" index="2">Kurulumu Yap</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-header">
                Kurulum Başarılı!
              </div>
              <div class="card-body">
                <p>Kurulum başarıyla tamamlandı! Yönetim Panelinden site ayarlarını dilediğin gibi değiştirebilirsin.</p>
                <div class="clearfix">
                  <div class="float-right">
                    <a href="javascript:void(0)" id="redirect" class="btn btn-submit">
                      <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                      <span class="sr-only">Yükleniyor...</span>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php require_once(__ROOT__."/apps/install/private/layouts/footer.php"); ?>
    <script type="text/javascript">
      $(window).on("load", function() {
        $("#loader").removeClass("is-loading");
        $("#spinner").css("display", "none");
      });
    </script>
  </body>
</html>
