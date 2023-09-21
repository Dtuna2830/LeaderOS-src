<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/status.php");
  require_once(__ROOT__."/apps/install/private/config/functions.php");

  if (INSTALL_STATUS == true) {
    die("Kurulum zaten yapılı!");
  }

  if (get("step") == 0) {
    if ((sqlPost("mysqlServer") == null) || (sqlPost("mysqlPort") == null) || (sqlPost("mysqlUsername") == null) || (sqlPost("mysqlPassword") == null) || (sqlPost("mysqlDatabase") == null)) {
      die("Lütfen boş alan bırakmayınız!");
    }
    else {
      $mysqlServer   = sqlPost("mysqlServer");
      $mysqlPort     = sqlPost("mysqlPort");
      $mysqlUsername = sqlPost("mysqlUsername");
      $mysqlPassword = sqlPost("mysqlPassword");
      $mysqlDatabase = sqlPost("mysqlDatabase");

      try {
        $db = new PDO("mysql:host=$mysqlServer;port=$mysqlPort;dbname=$mysqlDatabase;charset=utf8", $mysqlUsername, $mysqlPassword);
      } catch (PDOException $e) {
        die('<strong>MySQL connection error: </strong>'.utf8_encode($e->getMessage()));
      }
      die(true);
    }
  }
  else if (get("step") == 1) {
    if ((post("siteSlogan") == null) || (post("siteServerName") == null) || (post("siteServerIP") == null) || (post("siteServerVersion") == null) || (post("sitePasswordType") == null) || (post("siteMaintenance") == null)) {
      die("Lütfen boş alan bırakmayınız!");
    }
    else {
      die(true);
    }
  }
  else if (get("step") == 2) {
    $mysqlServer   = sqlPost("mysqlServer");
    $mysqlPort     = sqlPost("mysqlPort");
    $mysqlUsername = sqlPost("mysqlUsername");
    $mysqlPassword = sqlPost("mysqlPassword");
    $mysqlDatabase = sqlPost("mysqlDatabase");

    try {
      $db = new PDO("mysql:host=$mysqlServer;port=$mysqlPort;dbname=$mysqlDatabase;charset=utf8", $mysqlUsername, $mysqlPassword);
    }
    catch (PDOException $e) {
      die('<strong>MySQL connection error: </strong>'.utf8_encode($e->getMessage()));
    }

    if ((post("accountUsername") == null) || (post("accountEmail") == null) || (post("accountPassword") == null) || (post("accountPasswordRe") == null)) {
      die("Lütfen boş alan bırakmayınız!");
    }
    else {
      $usernameValid = $db->prepare("SELECT * FROM Accounts WHERE realname = ?");
      $usernameValid->execute(array(post("accountUsername")));

      $emailValid = $db->prepare("SELECT * FROM Accounts WHERE email = ?");
      $emailValid->execute(array(post("accountEmail")));

      if (checkUsername(post("accountUsername"))) {
        die("Girdiğiniz kullanıcı adı uygun olmayan karakter içeriyor!");
      }
      else if ($usernameValid->rowCount() > 0) {
        die('<strong>'.post("accountUsername").'</strong> adlı üye mevcut!');
      }
      else if ($emailValid->rowCount() > 0) {
        die('<strong>'.post("accountEmail").'</strong> başkası tarafından kullanılıyor!');
      }
      else if (post("accountPassword") != post("accountPasswordRe")) {
        die("Şifreler uyuşmuyor!");
      }
      else if (checkBadPassword(post("accountPassword"))) {
        die("Basit şifreler kullanamazsınız!");
      }
      else if (strlen(post("accountUsername")) < 3) {
        die("Kullanıcı adı 3 karakterden az olamaz!");
      }
      else if (strlen(post("accountUsername")) > 16) {
        die("Kullanıcı adı 16 karakterden fazla olamaz!");
      }
      else if (strlen(post("accountPassword")) < 4) {
        die("Şifre 4 karakterden az olamaz!");
      }
      else {
        $dbConnectionForCreate = new PDO("mysql:host=$mysqlServer;port=$mysqlPort;dbname=$mysqlDatabase;charset=utf8", $mysqlUsername, $mysqlPassword);
        $dbFileContents = file_get_contents(__ROOT__."/apps/install/private/sql/database.sql");
        $dbCreate = $dbConnectionForCreate->exec($dbFileContents);
        $dbConnectionForCreate=null;

        $connectFile = (__ROOT__."/apps/main/private/config/connect.php");
        $connectFileData =
'<?php
  define(\'DB_HOST\', \''.$mysqlServer.'\');
  define(\'DB_PORT\', \''.$mysqlPort.'\');
  define(\'DB_USERNAME\', \''.$mysqlUsername.'\');
  define(\'DB_PASSWORD\', \''.phpFileEscape($mysqlPassword).'\');
  define(\'DB_NAME\', \''.$mysqlDatabase.'\');

  try {
    $db = new PDO("mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME.";charset=utf8", DB_USERNAME, DB_PASSWORD);
  }
  catch (PDOException $e) {
    die("<strong>MySQL connection error:</strong> ".utf8_encode($e->getMessage()));
  }
?>';
        $updateConnectFile = file_put_contents($connectFile, $connectFileData) OR die("Dosya hatası! Lütfen LeaderOS ile iletişime geçiniz. (connect.php)");

        $statusFile = (__ROOT__."/apps/main/private/config/status.php");
        $statusFileData =
'<?php
  define(\'INSTALL_STATUS\', true);
  define(\'VERSION\', \''.VERSION.'\');
  define(\'BUILD_NUMBER\', '.BUILD_NUMBER.');
?>';
        $updateStatusFile = file_put_contents($statusFile, $statusFileData) OR die("Dosya hatası! Lütfen LeaderOS ile iletişime geçiniz. (status.php)");

        $appFile = (__ROOT__."/apps/main/private/config/app.php");
        $appFileData =
'<?php
  define(\'APP_KEY\', \''.md5(uniqid(mt_rand(), true)).'\');
?>';
        $updateAppFile = file_put_contents($appFile, $appFileData) OR die("Dosya hatası! Lütfen LeaderOS ile iletişime geçiniz. (app.php)");

        $rules = '
          <p><strong>1</strong>-) Kendini yetkili gibi tanıtmak süresiz ban sebebidir. <span class="text-danger">Cezası: Süresiz sunucudan uzaklaştırılmak.</span></p>
          <p><strong>2</strong>-) Oyun içinde Güvenmediğiniz kişilere eşyalarınızı paranızı vermeyin.</p>
          <p><strong>3</strong>-) Hesap güvenliği size aittir, hesabınızın çalınması durumunda <strong>%servername%</strong> sorumlu değildir.</p>
          <p><strong>4</strong>-) Hile/Makro kullanmak kesinlikle yasaktır. <span class="text-danger">Cezası: 15/30 gün süreliğine sunucudan uzaklaştırılmak.</span></p>
          <p><strong>5</strong>-) Din, dil, ırk vb. ayrımcılıklar yapmak tamamen yasaktır. <span class="text-danger">Cezası: Süresiz sunucudan uzaklaştırılmak.</span></p>
          <p><strong>6</strong>-) Diğer oyuncuları kandırmak veya dolandırmak yasaktır. <span class="text-danger">Cezası: 15/30 gün süreliğine sunucudan uzaklaştırılmak.</span></p>
          <p><strong>7</strong>-) Kaybolan eşyalarınızdan veya yok olan eşyalarınızdan <strong>%servername%</strong> sorumlu değildir.</p>
          <p><strong>8</strong>-) Youtube veya Başka sunucuların reklamlarını yapmak tamamen yasaktır. <span class="text-danger">Cezası: Süresiz sunucudan uzaklaştırılmak.</span></p>
          <p><strong>9</strong>-) Kişisel bilgilerinizi oyundan paylaşmayın.</p>
          <p><strong>10</strong>-) Yekililerden yetki istemek (rehber yaparmisin vs.) yasaktır. <span class="text-danger">Cezası: 7/14 gün süreliğine sunucudan uzaklaştırılmak.</span></p>
          <p><strong>11</strong>-) Sunucuya hakaret etmek, kötülemek ve küfür etmek yasaktır. <span class="text-danger">Cezası: Süresiz sunucudan uzaklaştırılmak.</span></p>
          <p><strong>12</strong>-) Sunucuda siyaset yapmak yasaktır. <span class="text-danger">Cezası: Süresiz sunucudan uzaklaştırılmak.</span></p>
          <p><strong>13</strong>-) Cinsiyet ayrımı yapmak yasaktır. <span class="text-danger">Cezası: Süresiz sunucudan uzaklaştırılmak.</span></p>
          <p><strong>14</strong>-) Oyun içi hatalardan(bug) yararlanmak yasaktır. <span class="text-danger">Cezası: Süresiz sunucudan uzaklaştırılmak.</span></p>
          <p><strong>15</strong>-) Oyundaki eşyalarınızı gerçek para ile başka kişilere satmak yasaktır. <span class="text-danger">Cezası: Süresiz sunucudan uzaklaştırılmak.</span></p>
          <p><strong>16</strong>-) Yöneticiler sizlerden asla eşyalarınızı veya hesabınızın bilgilerini istemez.</p>
          <p><strong>17</strong>-) TPA tuzağı yapmak yasaktır. <span class="text-danger">Cezası: Süresiz sunucudan uzaklaştırılmak.</span></p>
          <p><strong>18</strong>-) Aldığınız ürün bir bağıştır ve sunucumuza katkıda bulunmanız için satılmaktadır.</p>
          <p><strong>19</strong>-) Kcolor: red;i yüklemesi yaptığınız ödemenin hiçbir dönüşü yoktur.</p>
          <p><strong>20</strong>-) ürün Satın alırken sunucuda olmalısınız aksi takdirse eşya size iletilemiyebilir.</p>
          <p><strong>21</strong>-) Eğer sunucuda olmazsanız ve eşya size iletilmezse bundan biz sorumlu değiliz, destek talebi açarsanız yardımcı olabiliriz ancak cevaplamak ile hükümlü değiliz.</p>
          <p><strong>22</strong>-) <strong>%servername%</strong> ürünleri sadece bu sitede satılmaktadır, başka bir yerden satılmaz.</p>
          <p><strong>23</strong>-) <strong>%servername%</strong> istediği zaman bu şartlarda değişiklilik yapma hakkına sahiptir.</p>
          <p><strong>24</strong>-) <strong>%servername%</strong> hesabınıza gerekli görüldüğü zaman el koyabilir, düzenleyebilir, silebilir veya erişiminizi süresiz olarak kesebilir.</p>
          <p><strong>25</strong>-) Şifrenizi güvenli birşey koyun. Başka sunucularda kullanmadığınız bir şifre koyun. Aksi taktirde çalınan hesaplardan <span class="text-danger"><strong>%servername%</strong> sorumlu değildir..</span></p>
          <p><strong>26</strong>-) <strong>%servername%</strong> de oynayan her oyuncu kuralları okudu kabul edilir. <span class="text-danger">İşlem yapılırken kuralları okuduğunuz göz önüne alınır.</span></p>
        ';

        $supportMessageTemplate = '
          <p>Merhaba <strong><span class="text-primary">%username%</span></strong>,</p>
          <p>%message%</p><p class="mb-1"><strong>%servername% Çevrimiçi Destek Hattı</strong></p>
          <p class="mb-1"><strong><span class="text-primary">Sunucu Adresi:</span></strong> %serverip%</p>
          <p class="mb-1"><strong><span class="text-primary">Sunucu Sürümü:</span></strong> %serverversion%</p>
        ';

        $smtpPasswordTemplate = '
          <div style="padding: 3rem 1rem;  color: #333333 !important;  font-family: \'Roboto\', sans-serif !important;  background-color: #ffffff !important;">
            <a href="%url%" target="_blank" style="display: block !important; margin-bottom: 2rem !important; text-align: center !important;">
              <img src="https://i.ibb.co/HC4YrxZ/leaderos.png" style="width: 250px !important; height: auto !important;">
            </a>
            <div style="max-width: 640px !important; margin: 0 auto !important; text-align: center !important; background-color: #ffffff !important; font-size: 1rem !important; line-height: 1.5 !important; border: 1px solid #cccccc !important; border-top: 5px solid #2dce89 !important; border-radius: .5rem !important;">
              <div style="padding: 1.25rem !important;">
                <p style="margin-top: 0 !important; margin-bottom: 1rem !important;">Merhaba <strong>%username%</strong>,</p>
                <p style="margin-top: 0 !important; margin-bottom: 1rem !important;">Şifre sıfırlama isteğinizi aldık, aşağıdaki bağlantıyı kullanarak şifrenizi değiştirebilirsiniz.</p>
                <div style="margin: 1.25rem 0 !important;">
                  <a href="%url%" target="_blank" style="color: #ffffff !important; background-color: #2dce89 !important; padding: .5rem 1.5rem !important; margin-bottom: 1rem !important; text-decoration: none !important; text-align: center !important; vertical-align: middle !important; border-radius: 2rem !important; transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out !important">Şifreyi Değiştir</a>
                </div>
                <p style="margin-top: 0 !important; margin-bottom: 1rem !important; color: #6c757d !important;">Çalışmadı mı? Aşağıdaki bağlantıyı kullanabilirsiniz.</p>
                <a href="%url%" target="_blank" style="color: #f5365c !important; text-decoration: none !important;">%url%</a>
              </div>
            </div>
          </div>
        ';
        $smtpTFATemplate = '
          <div style="padding: 3rem 1rem;  color: #333333 !important;  font-family: \'Roboto\', sans-serif !important;  background-color: #ffffff !important;">
            <a href="%url%" target="_blank" style="display: block !important; margin-bottom: 2rem !important; text-align: center !important;">
              <img src="https://i.ibb.co/HC4YrxZ/leaderos.png" style="width: 250px !important; height: auto !important;">
            </a>
            <div style="max-width: 640px !important; margin: 0 auto !important; text-align: center !important; background-color: #ffffff !important; font-size: 1rem !important; line-height: 1.5 !important; border: 1px solid #cccccc !important; border-top: 5px solid #2dce89 !important; border-radius: .5rem !important;">
              <div style="padding: 1.25rem !important;">
                <p style="margin-top: 0 !important; margin-bottom: 1rem !important;">Merhaba <strong>%username%</strong>,</p>
                <p style="margin-top: 0 !important; margin-bottom: 1rem !important;">İki adımlı doğrulamayı sıfırlama isteğinizi aldık, aşağıdaki bağlantıyı kullanarak iki adımlı doğrulamayı sıfırlayabilirsiniz.</p>
                <div style="margin: 1.25rem 0 !important;">
                  <a href="%url%" target="_blank" style="color: #ffffff !important; background-color: #2dce89 !important; padding: .5rem 1.5rem !important; margin-bottom: 1rem !important; text-decoration: none !important; text-align: center !important; vertical-align: middle !important; border-radius: 2rem !important; transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out !important">Sıfırla</a>
                </div>
                <p style="margin-top: 0 !important; margin-bottom: 1rem !important; color: #6c757d !important;">Çalışmadı mı? Aşağıdaki bağlantıyı kullanabilirsiniz.</p>
                <a href="%url%" target="_blank" style="color: #f5365c !important; text-decoration: none !important;">%url%</a>
              </div>
            </div>
          </div>
        ';

        $recaptchaPagesStatusArray = array(
          "loginPage"     => 0,
          "registerPage"  => 0,
          "recoverPage"   => 0,
          "tfaPage"       => 0,
          "newsPage"      => 0,
          "supportPage"   => 0
        );
        $recaptchaPagesStatusJSON = json_encode($recaptchaPagesStatusArray);
  
        $sidebarItemsStatusArray = array(
          "featuredProduct"   => 1,
          "storeHistory"      => 1,
          "creditHistory"     => 1,
          "topCreditHistory"  => 1,
          "discord"           => 1,
        );
        $sidebarItemsStatusJSON = json_encode($sidebarItemsStatusArray);

        $webhookMessage = "@everyone";

        $webhookCreditEmbed   = "**%username%** adlı kullanıcı **%credit% kredi** (%money% TL) yükledi.";
        $webhookStoreEmbed    = "**%username%** adlı kullanıcı **%server%** sunucusundan **%product%** ürününü satın aldı.";
        $webhookSupportEmbed  = "**%username%** adlı kullanıcı destek mesajı gönderdi.\n%panelurl%";
        $webhookNewsEmbed     = "**%username%** adlı kullanıcı habere yorum yaptı.\n%posturl%\n%panelurl%";
        $webhookLotteryEmbed  = "**%username%** adlı kullanıcı **%lottery%** adlı çarkıfelekten **%award%** adlı ödülü kazandı.";
        $webhookApplicationEmbed  = "**%username%** adlı kullanıcı **%form%** için başvuru yaptı.\n%panelurl%";
        
        $insertSettings = $db->prepare("INSERT INTO Settings (siteSlogan, serverName, serverIP, serverVersion, passwordType, maintenanceStatus, rules, recaptchaPagesStatus, supportMessageTemplate, smtpPasswordTemplate, smtpTFATemplate, storeDiscountProducts, webhookCreditMessage, webhookStoreMessage, webhookSupportMessage, webhookNewsMessage, webhookLotteryMessage, webhookApplicationMessage, webhookCreditEmbed, webhookStoreEmbed, webhookSupportEmbed, webhookNewsEmbed, webhookLotteryEmbed, webhookApplicationEmbed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insertSettings->execute(array(post("siteSlogan"), post("siteServerName"), post("siteServerIP"), post("siteServerVersion"), post("sitePasswordType"), post("siteMaintenance"), $rules, $recaptchaPagesStatusJSON, $supportMessageTemplate, $smtpPasswordTemplate, $smtpTFATemplate, "0", $webhookMessage, $webhookMessage, $webhookMessage, $webhookMessage, $webhookMessage, $webhookMessage, $webhookCreditEmbed, $webhookStoreEmbed, $webhookSupportEmbed, $webhookNewsEmbed, $webhookLotteryEmbed, $webhookApplicationEmbed));

        $headerArray = array(
          array(
            "id"        => md5(time()+1),
            "title"     => "Ana Sayfa",
            "icon"      => "fa fa-home",
            "url"       => "/",
            "tabstatus" => 0,
            "pagetype"  => "home"
          ),
          array(
            "id"        => md5(time()+2),
            "title"     => "Mağaza",
            "icon"      => "fa fa-shopping-cart",
            "url"       => "/magaza",
            "tabstatus" => 0,
            "pagetype"  => "store"
          ),
          array(
            "id"        => md5(time()+3),
            "title"     => "Kredi Yükle",
            "icon"      => "fa fa-coins",
            "url"       => "/kredi/yukle",
            "tabstatus" => 0,
            "pagetype"  => "credit"
          ),
          array(
            "id"        => md5(time()+4),
            "title"     => "Sıralama",
            "icon"      => "fa fa-trophy",
            "url"       => "/siralama",
            "tabstatus" => 0,
            "pagetype"  => "leaderboards"
          ),
          array(
            "id"        => md5(time()+5),
            "title"     => "Destek",
            "icon"      => "fa fa-life-ring",
            "url"       => "/destek",
            "tabstatus" => 0,
            "pagetype"  => "support"
          ),
          array(
            "id"        => md5(time()+6),
            "title"     => "İndir",
            "icon"      => "fa fa-download",
            "url"       => "/indir",
            "tabstatus" => 0,
            "pagetype"  => "download"
          ),
        );
        $headerJSON = json_encode($headerArray);

        $extraColors = array(
          'main'                  => '#5e72e4',
          'main:hover'            => '#324cdd',
          'primary'               => '#5e72e4',
          'primary:hover'         => '#324cdd',
          'success'               => '#2dce89',
          'success:hover'         => '#24a46d',
          'danger'                => '#f5365c',
          'danger:hover'          => '#ec0c38',
          'warning'               => '#fb6340',
          'warning:hover'         => '#fa3a0e',
          'info'                  => '#11cdef',
          'info:hover'            => '#0da5c0',
          'link'                  => '#007bff',
          'link:hover'            => '#0056b3',
          'navbar-dark'           => '#273443',
          'navbar-dark-text'      => '#ffffff',
          'navbar-dark-link'      => '#5e72e4',
          'navbar-dark-link-text' => '#ffffff',
          'nav-tabs-border'       => '#5e72e4',
          'search-icon'           => '#5e72e4',
          'search-icon-text'      => '#ffffff',
          'footer-top'            => '#273443',
          'footer-top-text'       => '#a9aeb4',
          'footer-bottom'         => '#232f3c',
          'card-header'           => '#5e72e4',
          'page-link'             => '#5e72e4',
          'spinner'               => '#5e72e4',
          'scrollup'              => '#5e72e4',
          'body'                  => '#f8f8f8',
          'broadcast'             => '#232f3c',
          'broadcast-text'        => '#ffffff',
          'navbar-online-active'  => '#02b875',
          'navbar-online-passive' => '#f5365c',
          'navbar-online-text'    => '#ffffff',
          'slider-online-active'  => '#02b875',
          'slider-online-passive' => '#f5365c',
          'slider-online-text'    => '#ffffff'
        );

        $colorsArray = array(
          'body' => array(
            'background-color' => $extraColors["body"]
          ),
          'a' => array(
            'color' => $extraColors["link"]
          ),
          'a:active, a:hover, a:focus' => array(
            'color' => $extraColors["link:hover"]
          ),
          '.color-main' => array(
            'color' => $extraColors["main"]
          ),
          '.color-main:hover' => array(
            'color' => $extraColors["main:hover"]
          ),
          '.color-primary' => array(
            'color' => $extraColors["primary"]
          ),
          '.color-primary:hover' => array(
            'color' => $extraColors["primary:hover"]
          ),
          '.color-success' => array(
            'color' => $extraColors["success"]
          ),
          '.color-success:hover' => array(
            'color' => $extraColors["success:hover"]
          ),
          '.color-danger' => array(
            'color' => $extraColors["danger"]
          ),
          '.color-danger:hover' => array(
            'color' => $extraColors["danger:hover"]
          ),
          '.color-warning' => array(
            'color' => $extraColors["warning"]
          ),
          '.color-warning:hover' => array(
            'color' => $extraColors["warning:hover"]
          ),
          '.color-info' => array(
            'color' => $extraColors["info"]
          ),
          '.color-info:hover' => array(
            'color' => $extraColors["info:hover"]
          ),
          '.btn-primary, .badge-primary, .alert-primary, .bg-primary' => array(
            'background-color' => $extraColors["primary"]
          ),
          '.btn-success, .badge-success, .alert-success, .bg-success' => array(
            'background-color' => $extraColors["success"]
          ),
          '.btn-danger, .badge-danger, .alert-danger, .bg-danger' => array(
            'background-color' => $extraColors["danger"]
          ),
          '.btn-warning, .badge-warning, .alert-warning, .bg-warning' => array(
            'background-color' => $extraColors["warning"]
          ),
          '.btn-info, .badge-info, .alert-info, .bg-info' => array(
            'background-color' => $extraColors["info"]
          ),
          '.text-primary' => array(
            'color' => $extraColors["primary"].' !important'
          ),
          '.text-success' => array(
            'color' => $extraColors["success"].' !important'
          ),
          '.text-danger' => array(
            'color' => $extraColors["danger"].' !important'
          ),
          '.text-warning' => array(
            'color' => $extraColors["warning"].' !important'
          ),
          '.text-info' => array(
            'color' => $extraColors["info"].' !important'
          ),
          '.btn-primary' => array(
            'border-color' => $extraColors["primary"]
          ),
          '.btn-primary.active, .btn-primary:active, .btn-primary:hover, .btn-primary:focus' => array(
            'border-color'      => $extraColors["primary:hover"],
            'background-color'  => $extraColors["primary:hover"]
          ),
          '.btn-success' => array(
            'border-color' => $extraColors["success"]
          ),
          '.btn-success.active, .btn-success:active, .btn-success:hover, .btn-success:focus' => array(
            'border-color'      => $extraColors["success:hover"],
            'background-color'  => $extraColors["success:hover"]
          ),
          '.btn-danger' => array(
            'border-color' => $extraColors["danger"]
          ),
          '.btn-danger.active, .btn-danger:active, .btn-danger:hover, .btn-danger:focus' => array(
            'border-color'      => $extraColors["danger:hover"],
            'background-color'  => $extraColors["danger:hover"]
          ),
          '.btn-warning' => array(
            'border-color' => $extraColors["warning"]
          ),
          '.btn-warning.active, .btn-warning:active, .btn-warning:hover, .btn-warning:focus' => array(
            'border-color'      => $extraColors["warning:hover"],
            'background-color'  => $extraColors["warning:hover"]
          ),
          '.btn-info' => array(
            'border-color' => $extraColors["info"]
          ),
          '.btn-info.active, .btn-info:active, .btn-info:hover, .btn-info:focus' => array(
            'border-color'      => $extraColors["info:hover"],
            'background-color'  => $extraColors["info:hover"]
          ),
          '.custom-control-input:checked~.custom-control-label::before' => array(
            'border-color'      => $extraColors["main"],
            'background-color'  => $extraColors["main"]
          ),
          '.broadcast' => array(
            'background-color' => $extraColors["broadcast"]
          ),
          '.broadcast-link' => array(
            'color' => $extraColors["broadcast-text"].' !important',
          ),
          '.navbar-server' => array(
            'color'             => $extraColors["navbar-online-text"],
            'background-color'  => $extraColors["navbar-online-passive"]
          ),
          '.navbar-server.active' => array(
            'background-color' => $extraColors["navbar-online-active"]
          ),
          '.server-online-info' => array(
            'color'             => $extraColors["slider-online-text"],
            'background-color'  => $extraColors["slider-online-passive"]
          ),
          '.server-online-info.active' => array(
            'background-color' => $extraColors["slider-online-active"]
          ),
          '.navbar-dark' => array(
            'background-color' => $extraColors["navbar-dark"]
          ),
          '.navbar-dark .navbar-nav .nav-link' => array(
            'color' => $extraColors["navbar-dark-text"].' !important',
          ),
          '.navbar-dark .navbar-nav .nav-item.active .nav-link, .navbar-dark .navbar-nav .nav-item:hover .nav-link, .navbar-dark .navbar-nav .nav-item:focus .nav-link' => array(
            'color'             => $extraColors["navbar-dark-link-text"].' !important',
            'border-color'      => $extraColors["navbar-dark-link"],
            'background-color'  => $extraColors["navbar-dark-link"]
          ),
          '.navbar-dark .navbar-buttons .nav-item .nav-link' => array(
            'border-color' => $extraColors["navbar-dark-link"]
          ),
          '.nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active' => array(
            'border-color' => $extraColors["nav-tabs-border"]
          ),
          '.search-icon' => array(
            'color'             => $extraColors["search-icon-text"],
            'background-color'  => $extraColors["search-icon"]
          ),
          '.footer-top' => array(
            'background-color' => $extraColors["footer-top"]
          ),
          '.footer-top, .footer-top ul li a' => array(
            'color' => $extraColors["footer-top-text"]
          ),
          '.footer-bottom' => array(
            'background-color' => $extraColors["footer-bottom"]
          ),
          '.card-header:first-child, .modal-header' => array(
            'background-color' => $extraColors["card-header"]
          ),
          '.pagination .page-item.active .page-link, .pagination .page-item.active .page-link:hover' => array(
            'border-color'      => $extraColors["page-link"],
            'background-color'  => $extraColors["page-link"]
          ),
          '.search-cancel:hover, .search-cancel:focus, .search-cancel:active' => array(
            'color' => $extraColors["danger"]
          ),
          '#preloader .spinner-border' => array(
            'color' => $extraColors["spinner"]
          ),
          '#scrollUp:hover' => array(
            'background-color' => $extraColors["scrollup"]
          ),
          '.theme-color' => array(
            'background-color' => $extraColors["main"]
          ),
          '.theme-color.text-primary' => array(
            'color'             => $extraColors["main"].' !important',
            'background-color'  => 'transparent'
          ),
          '.theme-color.btn, .theme-color.badge' => array(
            'border-color' => $extraColors["main"]
          ),
          '.theme-color.btn.active, .theme-color.btn:active, .theme-color.btn:hover, .theme-color.btn:focus' => array(
            'border-color'      => $extraColors["main:hover"],
            'background-color'  => $extraColors["main:hover"]
          )
        );
        $colorsJSON = json_encode($colorsArray);
        $insertTheme = $db->prepare("INSERT INTO Theme (themeID, colorID, colors, header, sidebar, featuredProduct) VALUES (?, ?, ?, ?, ?, ?)");
        $insertTheme->execute(array(1, 1, $colorsJSON, $headerJSON, $sidebarItemsStatusJSON, 0));

        $paymentSettingsArray = array(
          "batihost"  => array(
            "batihostID"    => null,
            "batihostEmail" => null,
            "batihostToken" => null
          ),
          "paywant"   => array(
            "paywantAPIKey"         => null,
            "paywantAPISecretKey"   => null,
            "paywantCommissionType" => '1'
          ),
          "rabisu"    => array(
            "rabisuID"    => null,
            "rabisuToken" => null
          ),
          "shopier"   => array(
            "shopierAPIKey"         => null,
            "shopierAPISecretKey"   => null
          ),
          "keyubu"    => array(
            "keyubuID"    => null,
            "keyubuToken" => null
          ),
          "ininal"    => array(
            "ininalBarcodes" => array()
          ),
          "papara"    => array(
            "paparaNumbers" => array()
          ),
          "shipy"     => array(
            "shipyAPIKey" => null
          ),
          "eft"       => array(
            "bankAccounts" => array()
          ),
          "slimmweb"  => array(
            "slimmwebPaymentID" => null,
            "slimmwebToken"     => null
          ),
          "paytr"     => array(
            "paytrID"             => null,
            "paytrAPIKey"         => null,
            "paytrAPISecretKey"   => null
          ),
          "paylith"   => array(
            "paylithAPIKey"       => null,
            "paylithAPISecretKey" => null
          ),
          "paymax"   => array(
            "paymaxUser"      => null,
            "paymaxKey"       => null,
            "paymaxStoreCode" => null,
            "paymaxHash"      => null
          ),
          "weepay"   => array(
            "weepayID"           => null,
            "weepayAPIKey"       => null,
            "weepayAPISecretKey" => null
          )
        );
        $deletePaymentSettings = $db->query("TRUNCATE TABLE PaymentSettings");
        $insertPaymentSettings = $db->prepare("INSERT INTO PaymentSettings (name, slug, variables) VALUES (?, ?, ?)");
        $insertPaymentSettings->execute(array("Batihost", "batihost", json_encode($paymentSettingsArray["batihost"])));
        $insertPaymentSettings->execute(array("Paywant", "paywant", json_encode($paymentSettingsArray["paywant"])));
        $insertPaymentSettings->execute(array("Rabisu", "rabisu", json_encode($paymentSettingsArray["rabisu"])));
        $insertPaymentSettings->execute(array("Shopier", "shopier", json_encode($paymentSettingsArray["shopier"])));
        $insertPaymentSettings->execute(array("Keyubu", "keyubu", json_encode($paymentSettingsArray["keyubu"])));
        $insertPaymentSettings->execute(array("Ininal", "ininal", json_encode($paymentSettingsArray["ininal"])));
        $insertPaymentSettings->execute(array("Papara", "papara", json_encode($paymentSettingsArray["papara"])));
        $insertPaymentSettings->execute(array("Shipy", "shipy", json_encode($paymentSettingsArray["shipy"])));
        $insertPaymentSettings->execute(array("EFT (IBAN)", "eft", json_encode($paymentSettingsArray["eft"])));
        $insertPaymentSettings->execute(array("SlimmWeb", "slimmweb", json_encode($paymentSettingsArray["slimmweb"])));
        $insertPaymentSettings->execute(array("PayTR", "paytr", json_encode($paymentSettingsArray["paytr"])));
        $insertPaymentSettings->execute(array("Paylith", "paylith", json_encode($paymentSettingsArray["paylith"])));
        $insertPaymentSettings->execute(array("Paymax", "paymax", json_encode($paymentSettingsArray["paymax"])));
        $insertPaymentSettings->execute(array("Weepay", "weepay", json_encode($paymentSettingsArray["weepay"])));

        $loginToken = md5(uniqid(mt_rand(), true));
        $password = createPassword(post("sitePasswordType"), post("accountPassword"));
        $insertAccounts = $db->prepare("INSERT INTO Accounts (username, realname, email, password, creationIP, creationDate) VALUES (?, ?, ?, ?, ?, ?)");
        $insertAccounts->execute(array(strtolower(post("accountUsername")), post("accountUsername"), post("accountEmail"), $password, getIP(), date("Y-m-d H:i:s")));
        $accountID = $db->lastInsertId();
        $insertAccountSessions = $db->prepare("INSERT INTO AccountSessions (accountID, loginToken, creationIP, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?)");
        $insertAccountSessions->execute(array($accountID, $loginToken, getIP(), createDuration(0.01666666666), date("Y-m-d H:i:s")));
        $insertAccountNoticationInfo = $db->prepare("INSERT INTO AccountNoticationInfo (accountID, lastReadDate) VALUES (?, ?)");
        $insertAccountNoticationInfo->execute(array($accountID, date("Y-m-d H:i:s")));
        
        $seoPages = [
          [
            'page' => '404',
            'title' => '%serverName% - Sayfa Bulunamadı!',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'application',
            'title' => '%serverName% - Başvuru',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'bazaar',
            'title' => '%serverName% - Pazar',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'checkout',
            'title' => '%serverName% - Sepet',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'chest',
            'title' => '%serverName% - Sandık',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'credit',
            'title' => '%serverName% - Kredi',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'download',
            'title' => '%serverName% - İndir',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'games',
            'title' => '%serverName% - Oyunlar',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'gift',
            'title' => '%serverName% - Hediye',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'help',
            'title' => '%serverName% - Yardım Merkezi',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'home',
            'title' => '%serverName% - %title%',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'leaderboards',
            'title' => '%serverName% - Sıralama',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'login',
            'title' => '%serverName% - Giriş Yap',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'lottery',
            'title' => '%serverName% - Çarkıfelek',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'maintenance',
            'title' => '%serverName% - Bakım',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'manage-bazaar',
            'title' => '%serverName% - Pazar Yönetimi',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'news',
            'title' => '%serverName% - Blog',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'page',
            'title' => '%serverName% - Sayfa',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'player',
            'title' => '%serverName% - Oyuncu',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'profile',
            'title' => '%serverName% - Profil',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'recover',
            'title' => '%serverName% - Hesap Kurtarma',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'register',
            'title' => '%serverName% - Kayıt Ol',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'rules',
            'title' => '%serverName% - Kurallar',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'store',
            'title' => '%serverName% - Mağaza',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'support',
            'title' => '%serverName% - Destek',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'tfa',
            'title' => '%serverName% - İki Adımlı Doğrulama',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'tfa-recover',
            'title' => '%serverName% - İki Adımlı Doğrulama Sıfırlama',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'gaming-night',
            'title' => '%serverName% - Gaming Gecesi',
            'description' => null,
            'image' => null,
          ],
          [
            'page' => 'forum',
            'title' => '%serverName% - Forum',
            'description' => null,
            'image' => null,
          ],
        ];
        $insertSeoPages = $db->prepare("INSERT INTO SeoPages (page, title, description, image) VALUES (?, ?, ?, ?)");
        foreach ($seoPages as $seoPage) {
          $insertSeoPages->execute(array($seoPage['page'], $seoPage['title'], $seoPage['description'], $seoPage['image']));
        }
        
        $permissions = [
          'SUPER_ADMIN' => "Tüm Yetkiler",
          'VIEW_DASHBOARD' => "Paneli Görüntüle",
          'MANAGE_ACCOUNTS' => "Hesap Yönet",
          'MANAGE_APPLICATIONS' => "Başvuru Yönet",
          'MANAGE_BANS' => "Ban Yönet",
          'MANAGE_BAZAAR' => "Pazar Yönet",
          'MANAGE_BROADCAST' => "Duyuru Yönet",
          'MANAGE_DOWNLOADS' => "İndirmeyi Yönet",
          'MANAGE_GAMES' => "Oyun Yönet",
          'MANAGE_GIFTS' => "Hediye Yönet",
          'MANAGE_HELP_CENTER' => "Yardım Merkezi Yönet",
          'MANAGE_LEADERBOARDS' => "Sıralama Yönet",
          'MANAGE_LOTTERY' => "Çarkıfelek Yönet",
          'MANAGE_BLOG' => "Blog Yönet",
          'MANAGE_NOTIFICATIONS' => "Bildirim Görüntüle",
          'MANAGE_PAGES' => "Sayfa Yönet",
          'MANAGE_PAYMENT' => "Ödeme Yönet",
          'MANAGE_ROLES' => "Rol Yönet",
          'MANAGE_SERVERS' => "Sunucu Yönet",
          'MANAGE_SETTINGS' => "Ayarları Yönet",
          'MANAGE_SLIDER' => "Slider Yönet",
          'MANAGE_STORE' => "Mağaza Yönet",
          'MANAGE_SUPPORT' => "Destek Yönet",
          'MANAGE_THEME' => "Tema Yönet",
          'MANAGE_UPDATES' => "Güncelleme Yönet",
          'MANAGE_LOGS' => "Log Yönet",
          'MANAGE_GAMING_NIGHT' => "Gaming Gecesini Yönet",
          'MANAGE_CUSTOM_FORMS' => "Özel Formları Yönet",
          'MANAGE_FORUM' => "Forumu Yönet",
        ];
        $addPermission = $db->prepare("INSERT INTO Permissions (name, description) VALUES (?, ?)");
        foreach ($permissions as $key => $value) {
          $addPermission->execute(array($key, $value));
        }
        $addRole = $db->prepare("INSERT INTO Roles (id, name, slug, priority) VALUES (?, ?, ?, ?)");
        $addRole->execute(array(1, "Üye", "default", 0));
        $addRole->execute(array(2, "Yönetici", "yonetici", 99));
        
        $addPermissionToRole = $db->prepare("INSERT INTO RolePermissions (roleID, permissionID) VALUES (?, ?)");
        $addPermissionToRole->execute(array(2, 1));
        
        $addRoleToAccount = $db->prepare("INSERT INTO AccountRoles (accountID, roleID) VALUES (?, ?)");
        $addRoleToAccount->execute(array($accountID, 2));
        
        if (isset($_COOKIE["rememberMe"])) {
          setcookie("rememberMe", "", time()-86400*365, '/');
        }
        $_SESSION["login"] = $loginToken;
        die(true);
      }
    }
  }
  else {
    die("Bilinmeyen bir hata!");
  }
?>
