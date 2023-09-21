<?php
  if (!checkPerm($readAdmin, 'MANAGE_THEME')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  if (get("target") == 'general' && get("action") == 'update') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/theme.general.js');
  }
  if (get("target") == 'header' && get("action") == 'update') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/theme.header.js');
  }
  if (get("target") == 'color' && get("action") == 'update') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/theme.color.js');
  }
  $theme = $db->query("SELECT * FROM Theme ORDER BY id DESC LIMIT 1");
  $readTheme = $theme->fetch();
?>
<?php if (get("target") == 'themes'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <style>
      .preview {
        align-items: center;
        width: 100%;
        display: flex;
        justify-content: center;
      }
      .preview .boxImg {
        background: #000;
        width: 100%;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
        min-height: 400px;
        transition: ease-in 1s;
      }
      .preview .boxImg {
        background-size: cover !important;
      }
      .preview .boxImg:hover {
        background-position: bottom !important;
      }
    </style>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Temalar</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Temalar</li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <?php
          $themes = [
            [
              'name' => 'Premium',
              'price' => '200',
              'demo' => 'https://theme4.sofixa.com',
              'buy' => 'https://www.sofixa.com/fiyatlandirma/leaderos-tema',
              'thumbnail' => 'https://cdn.sofixa.com/sofixacom/leaderos-theme/premium.png'
            ],
            [
              'name' => 'Leaf',
              'price' => '200',
              'demo' => 'https://theme5.sofixa.com',
              'buy' => 'https://www.sofixa.com/fiyatlandirma/leaderos-tema',
              'thumbnail' => 'https://i.hizliresim.com/blhb5qp.png'
            ],
            [
              'name' => 'Gale',
              'price' => '200',
              'demo' => 'https://theme6.sofixa.com',
              'buy' => 'https://www.sofixa.com/fiyatlandirma/leaderos-tema',
              'thumbnail' => 'https://cdn.sofixa.com/sofixacom/leaderos-theme/gale-95425.png'
            ],
            [
              'name' => 'TheArchon',
              'price' => '100',
              'demo' => 'https://theme1.sofixa.com',
              'buy' => 'https://www.sofixa.com/fiyatlandirma/leaderos-tema',
              'thumbnail' => 'https://cdn.sofixa.com/sofixacom/leaderos-theme/thearchon-66490.png'
            ],
            [
              'name' => 'ManaCube',
              'price' => '100',
              'demo' => 'https://theme2.sofixa.com',
              'buy' => 'https://www.sofixa.com/fiyatlandirma/leaderos-tema',
              'thumbnail' => 'https://cdn.sofixa.com/sofixacom/leaderos-theme/manacube-53958.png'
            ],
            [
              'name' => 'LemonCloud',
              'price' => '100',
              'demo' => 'https://theme3.sofixa.com',
              'buy' => 'https://www.sofixa.com/fiyatlandirma/leaderos-tema',
              'thumbnail' => 'https://cdn.sofixa.com/sofixacom/leaderos-theme/lemoncloud.png'
            ]
          ];
        ?>
        <?php foreach ($themes as $theme): ?>
          <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card h-100">
              <div class="preview">
                <div class="boxImg" style="background: url(<?php echo $theme["thumbnail"]; ?>);"></div>
              </div>
              <div class="card-body pt-4 pb-0">
                <div class="row align-items-center">
                  <div class="col">
                    <h2 class="card-text mb-0"><?php echo $theme["name"]; ?></h2>
                  </div>
                  <div class="col-auto" style="font-size: 17px;">
                    <?php echo $theme["price"]; ?>₺
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 pr-2">
                    <a href="<?php echo $theme["demo"]; ?>" target="_blank" class="btn btn-white d-block w-100 mt-4">İncele</a>
                  </div>
                  <div class="col-md-6 pl-2">
                    <a href="<?php echo $theme["buy"]; ?>" class="btn btn-success d-block w-100 mt-2 mt-md-4">Satın Al</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php else: ?>
    <?php go("/404"); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'general'): ?>
  <?php if (get("action") == 'update'): ?>
    <?php
      $sidebarWidgetsJSON = $readTheme["sidebar"];
      $sidebarWidgets = json_decode($sidebarWidgetsJSON, true);
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Genel</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Tema</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Genel</li>
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
            if (isset($_POST["updateThemeSettings"])) {
              if (post("sidebarStatus") == '0' || post("discordWidgetStatus") == '0') {
                $_POST["discordServerID"] = '0';
              }
              if (!$csrf->validate('updateThemeSettings')) {
                echo alertError("Sistemsel bir sorun oluştu!");
                echo goDelay("/yonetim-paneli/tema/genel", 2);
              }
              else if (post("themeID") == null || post("sliderStatus") == null || post("sliderStyle") == null || post("serverOnlineInfoStatus") == null || post("sidebarStatus") == null || post("newsCardStyle") == null || post("headerStyle") == null || post("discordThemeID") == null || post("discordServerID") == null || post("recaptchaThemeID") == null) {
                echo alertError("Lütfen gerekli alanları doldurunuz!");
              }
              else {
                /* FIX THEME COLORS */
                $colors = json_decode($readTheme["colors"], true);
                if ($readTheme["colorID"] != 0) {
                  if (post("themeID") == 1) {
                    $colors["body"]["background-color"]                   = '#f8f8f8';
                    $colors["a"]["color"]                                 = '#007bff';
                    $colors["a:active, a:hover, a:focus"]["color"]        = '#0056b3';
                    $colors[".navbar-dark"]["background-color"]           = '#273443';
                    $colors[".footer-top"]["background-color"]            = '#273443';
                    $colors[".footer-top, .footer-top ul li a"]["color"]  = '#a9aeb4';
                    $colors[".footer-bottom"]["background-color"]         = '#232f3c';
                    $colors[".broadcast"]["background-color"]             = '#232f3c';
                  }
                  if (post("themeID") == 2) {
                    $colors["body"]["background-color"]                   = '#12263f';
                    $colors["a"]["color"]                                 = '#ffffff';
                    $colors["a:active, a:hover, a:focus"]["color"]        = '#95aac9';
                    $colors[".navbar-dark"]["background-color"]           = '#152e4d';
                    $colors[".footer-top"]["background-color"]            = '#152e4d';
                    $colors[".footer-top, .footer-top ul li a"]["color"]  = '#95aac9';
                    $colors[".footer-bottom"]["background-color"]         = '#1e3a5c';
                    $colors[".broadcast"]["background-color"]             = '#12263f';
                  }
                }
                $colors = json_encode($colors);

                if ($_FILES["header-logo"]["size"] != null) {
                  require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                  $upload = new \Verot\Upload\Upload($_FILES["header-logo"], "tr_TR");
                  if ($upload->uploaded) {
                    $upload->allowed = array("image/*");
                    $upload->file_overwrite = true;
                    $upload->file_new_name_body = "header-logo";
                    $upload->image_convert = "png";
                    $upload->process(__ROOT__."/apps/main/public/assets/img/extras/");
                    if (!$upload->processed) {
                      echo alertError("Header Logo yüklenirken bir hata oluştu: ".$upload->error);
                    }
                  }
                }
                if ($_FILES["header-bg"]["size"] != null) {
                  require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                  $upload = new \Verot\Upload\Upload($_FILES["header-bg"], "tr_TR");
                  if ($upload->uploaded) {
                    $upload->allowed = array("image/*");
                    $upload->file_overwrite = true;
                    $upload->file_new_name_body = "header-bg";
                    $upload->image_convert = "png";
                    $upload->process(__ROOT__."/apps/main/public/assets/img/extras/");
                    if (!$upload->processed) {
                      echo alertError("Header Arkaplan yüklenirken bir hata oluştu: ".$upload->error);
                    }
                  }
                }
                $sidebarWidgetsArray = $sidebarWidgets;
                $sidebarWidgetsArray["featuredProduct"] = $_POST["sidebarWidgets"][0];
                $sidebarWidgetsArray["storeHistory"] = $_POST["sidebarWidgets"][1];
                $sidebarWidgetsArray["creditHistory"] = $_POST["sidebarWidgets"][2];
                $sidebarWidgetsArray["topCreditHistory"] = $_POST["sidebarWidgets"][3];
                $sidebarWidgetsArray["discord"] = $_POST["sidebarWidgets"][4];
                $sidebarWidgetsJSON = json_encode($sidebarWidgetsArray);
                $updateTheme = $db->prepare("UPDATE Theme SET sidebar = ?, themeID = ?, colors = ?, broadcastStatus = ?, sliderStatus = ?, sliderStyle = ?, serverOnlineInfoStatus = ?, sidebarStatus = ?, featuredProduct = ?, newsCardStyle = ?, headerTheme = ?, headerStyle = ?, discordThemeID = ?, discordServerID = ?, recaptchaThemeID = ?, updatedAt = ? WHERE id = ?");
                $updateTheme->execute(array($sidebarWidgetsJSON, post("themeID"), $colors, post("broadcastStatus"), post("sliderStatus"), post("sliderStyle"), post("serverOnlineInfoStatus"), post("sidebarStatus"), post("featuredProduct"), post("newsCardStyle"), post("headerTheme"), post("headerStyle"), post("discordThemeID"), post("discordServerID"), post("recaptchaThemeID"), time(), $readTheme["id"]));
                echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                echo goDelay("/yonetim-paneli/tema/genel", 2);
                createLog($readAdmin["id"], "THEME_GENERAL_UPDATED");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group row">
                  <label for="selectTheme" class="col-sm-2 col-form-label">Tema:</label>
                  <div class="col-sm-10">
                    <select id="selectTheme" class="form-control" name="themeID" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readTheme["themeID"] == 0) ? 'selected="selected"' : null; ?>>Özelleştirilmiş</option>
                      <option value="1" <?php echo ($readTheme["themeID"] == 1) ? 'selected="selected"' : null; ?>>Flat</option>
                      <option value="2" <?php echo ($readTheme["themeID"] == 2) ? 'selected="selected"' : null; ?>>Epic</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectBroadcastStatus" class="col-sm-2 col-form-label">Duyuru Bandı:</label>
                  <div class="col-sm-10">
                    <select id="selectBroadcastStatus" class="form-control" name="broadcastStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readTheme["broadcastStatus"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readTheme["broadcastStatus"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectSliderStatus" class="col-sm-2 col-form-label">Slider:</label>
                  <div class="col-sm-10">
                    <select id="selectSliderStatus" class="form-control" name="sliderStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readTheme["sliderStatus"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readTheme["sliderStatus"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div id="sliderOptions" style="<?php echo ($readTheme["sliderStatus"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-group row">
                    <label for="selectSliderStyle" class="col-sm-2 col-form-label">Slider Tipi:</label>
                    <div class="col-sm-10">
                      <select id="selectSliderStyle" class="form-control" name="sliderStyle" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="1" <?php echo ($readTheme["sliderStyle"] == 1) ? 'selected="selected"' : null; ?>>Büyük</option>
                        <option value="2" <?php echo ($readTheme["sliderStyle"] == 2) ? 'selected="selected"' : null; ?>>Küçük</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectServerOnlineInfoStatus" class="col-sm-2 col-form-label">Slider Online Bandı:</label>
                    <div class="col-sm-10">
                      <select id="selectServerOnlineInfoStatus" class="form-control" name="serverOnlineInfoStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readTheme["serverOnlineInfoStatus"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                        <option value="1" <?php echo ($readTheme["serverOnlineInfoStatus"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectSidebarStatus" class="col-sm-2 col-form-label">Sidebar:</label>
                  <div class="col-sm-10">
                    <select id="selectSidebarStatus" class="form-control" name="sidebarStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0" <?php echo ($readTheme["sidebarStatus"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                      <option value="1" <?php echo ($readTheme["sidebarStatus"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                    </select>
                  </div>
                </div>
                <div id="sidebarOptions" style="<?php echo ($readTheme["sidebarStatus"] == '0') ? "display: none;" : "display: block;"; ?>">
                  <div class="form-group row">
                    <label for="inputSidebarWidgets" class="col-sm-2 col-form-label">Sidebar Widgets:</label>
                    <div class="d-flex">
                      <label for="switchSidebarWidgetFeaturedProduct" class="col-auto col-form-label">Fırsat Ürünü:</label>
                      <div class="col col-form-label">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="sidebarWidgets[]">
                          <input type="checkbox" id="switchSidebarWidgetFeaturedProduct" class="custom-control-input" <?php echo ($sidebarWidgets["featuredProduct"] == 1) ? "checked" : null; ?>>
                          <label for="switchSidebarWidgetFeaturedProduct" class="custom-control-label"></label>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <label for="switchSidebarWidgetStoreHistory" class="col-auto col-form-label">Son Market Alışverişleri:</label>
                      <div class="col col-form-label">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="sidebarWidgets[]">
                          <input type="checkbox" id="switchSidebarWidgetStoreHistory" class="custom-control-input" <?php echo ($sidebarWidgets["storeHistory"] == 1) ? "checked" : null; ?>>
                          <label for="switchSidebarWidgetStoreHistory" class="custom-control-label"></label>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <label for="switchSidebarWidgetCreditHistory" class="col-auto col-form-label">Son <?php echo $readSettings["creditText"] ?> Yüklemeleri:</label>
                      <div class="col col-form-label">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="sidebarWidgets[]">
                          <input type="checkbox" id="switchSidebarWidgetCreditHistory" class="custom-control-input" <?php echo ($sidebarWidgets["creditHistory"] == 1) ? "checked" : null; ?>>
                          <label for="switchSidebarWidgetCreditHistory" class="custom-control-label"></label>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <label for="switchSidebarWidgetTopCredit" class="col-auto col-form-label">En Çok <?php echo $readSettings["creditText"] ?> Yükleyenler:</label>
                      <div class="col col-form-label">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="sidebarWidgets[]">
                          <input type="checkbox" id="switchSidebarWidgetTopCredit" class="custom-control-input" <?php echo ($sidebarWidgets["topCreditHistory"] == 1) ? "checked" : null; ?>>
                          <label for="switchSidebarWidgetTopCredit" class="custom-control-label"></label>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex">
                      <label for="switchSidebarWidgetDiscord" class="col-auto col-form-label">Discord:</label>
                      <div class="col col-form-label">
                        <div class="custom-control custom-switch">
                          <input type="hidden" name="sidebarWidgets[]">
                          <input type="checkbox" id="switchSidebarWidgetDiscord" class="custom-control-input" <?php echo ($sidebarWidgets["discord"] == 1) ? "checked" : null; ?>>
                          <label for="switchSidebarWidgetDiscord" class="custom-control-label"></label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputFeaturedProduct" class="col-sm-2 col-form-label">Fırsat Ürünü:</label>
                    <div class="col-sm-10">
                      <select name="featuredProduct" id="inputFeaturedProduct" data-toggle="select">
                        <?php
                          $products = $db->prepare("SELECT P.id, P.name as pName, PC.name as pcName, S.name as sName FROM Products P LEFT JOIN ProductCategories PC ON P.categoryID = PC.id INNER JOIN Servers S ON S.id = P.serverID ORDER BY S.name ASC");
                          $products->execute();
                        ?>
                        <option value="0">Yok</option>
                        <?php foreach ($products as $readProducts): ?>
                          <?php
                            if ($readProducts["pcName"] == null) {
                              $readProducts["pcName"] = "Kategorisiz";
                            }
                          ?>
                          <option value="<?php echo $readProducts["id"]; ?>" <?php echo ($readTheme["featuredProduct"] == $readProducts["id"]) ? 'selected="selected"' : null; ?>>
                            <?php echo $readProducts["sName"]." -> ".$readProducts["pcName"]." -> ".$readProducts["pName"]; ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectDiscordWidgetStatus" class="col-sm-2 col-form-label">Discord Widget:</label>
                    <div class="col-sm-10">
                      <select id="selectDiscordWidgetStatus" class="form-control" name="discordWidgetStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readTheme["discordServerID"] == '0') ? 'selected="selected"' : null; ?>>Kapalı</option>
                        <option value="1" <?php echo ($readTheme["discordServerID"] != '0') ? 'selected="selected"' : null; ?>>Açık</option>
                      </select>
                    </div>
                  </div>
                  <div id="discordWidgetOptions" style="<?php echo ($readTheme["discordServerID"] == '0') ? "display: none;" : "display: block;"; ?>">
                    <div class="form-group row">
                      <label for="selectDiscordThemeID" class="col-sm-2 col-form-label">Discord Tema:</label>
                      <div class="col-sm-10">
                        <select id="selectDiscordThemeID" class="form-control" name="discordThemeID" data-toggle="select" data-minimum-results-for-search="-1">
                          <option value="1" <?php echo ($readTheme["discordThemeID"] == 1) ? 'selected="selected"' : null; ?>>Light</option>
                          <option value="2" <?php echo ($readTheme["discordThemeID"] == 2) ? 'selected="selected"' : null; ?>>Dark</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputDiscordServerID" class="col-sm-2 col-form-label">Discord Sunucu ID:</label>
                      <div class="col-sm-10">
                        <input type="text" id="inputDiscordServerID" class="form-control" name="discordServerID" placeholder="Discord sunucunuzun ID'sini giriniz. (Widget için)" value="<?php echo ($readTheme["discordServerID"] != '0') ? $readTheme["discordServerID"] : null; ?>">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectNewsCardStyle" class="col-sm-2 col-form-label">Haber Kart Tipi:</label>
                  <div class="col-sm-10">
                    <select id="selectNewsCardStyle" class="form-control" name="newsCardStyle" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="1" <?php echo ($readTheme["newsCardStyle"] == 1) ? 'selected="selected"' : null; ?>>Küçük</option>
                      <option value="2" <?php echo ($readTheme["newsCardStyle"] == 2) ? 'selected="selected"' : null; ?>>Büyük</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectHeaderTheme" class="col-sm-2 col-form-label">Header Teması:</label>
                  <div class="col-sm-10">
                    <select id="selectHeaderTheme" class="form-control" name="headerTheme" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="1" <?php echo ($readTheme["headerTheme"] == 1) ? 'selected="selected"' : null; ?>>LeaderOS</option>
                      <option value="2" <?php echo ($readTheme["headerTheme"] == 2) ? 'selected="selected"' : null; ?>>HiveMC</option>
                      <option value="3" <?php echo ($readTheme["headerTheme"] == 3) ? 'selected="selected"' : null; ?>>Hypixel</option>
                    </select>
                  </div>
                </div>
                <div id="headerThemeOptions" style="<?php echo (($readTheme["headerTheme"] == 1) ? "display: none;" : (($readTheme["headerTheme"] == 2 || $readTheme["headerTheme"] == 3) ? "display: block;" : "display: none;")); ?>">
                  <div class="form-group row">
                    <label for="fileHeaderLogo" class="col-sm-2 col-form-label">Header Logo:</label>
                    <div class="col-sm-10">
                      <div data-toggle="dropimage" class="dropimage <?php echo (file_exists(__ROOT__."/apps/main/public/assets/img/extras/header-logo.png")) ? "active" : null; ?>">
                        <div class="di-thumbnail">
                          <img src="<?php echo (file_exists(__ROOT__."/apps/main/public/assets/img/extras/header-logo.png")) ? "/apps/main/public/assets/img/extras/header-logo.png?cache=".$readTheme["updatedAt"] : null; ?>" alt="Ön İzleme">
                        </div>
                        <div class="di-select">
                          <label for="fileHeaderLogo">Bir Resim Seçiniz</label>
                          <input type="file" id="fileHeaderLogo" name="header-logo" accept="image/*">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="fileHeaderBackground" class="col-sm-2 col-form-label">Header Arkaplan:</label>
                    <div class="col-sm-10">
                      <div data-toggle="dropimage" class="dropimage <?php echo (file_exists(__ROOT__."/apps/main/public/assets/img/extras/header-bg.png")) ? "active" : null; ?>">
                        <div class="di-thumbnail">
                          <img src="<?php echo (file_exists(__ROOT__."/apps/main/public/assets/img/extras/header-bg.png")) ? "/apps/main/public/assets/img/extras/header-bg.png?cache=".$readTheme["updatedAt"] : null; ?>" alt="Ön İzleme">
                        </div>
                        <div class="di-select">
                          <label for="fileHeaderBackground">Bir Resim Seçiniz</label>
                          <input type="file" id="fileHeaderBackground" name="header-bg" accept="image/*">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectHeaderStyle" class="col-sm-2 col-form-label">Header Tipi:</label>
                  <div class="col-sm-10">
                    <select id="selectHeaderStyle" class="form-control" name="headerStyle" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="1" <?php echo ($readTheme["headerStyle"] == 1) ? 'selected="selected"' : null; ?>>Dar</option>
                      <option value="2" <?php echo ($readTheme["headerStyle"] == 2) ? 'selected="selected"' : null; ?>>Geniş</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectRecaptchaThemeID" class="col-sm-2 col-form-label">reCAPTCHA Tema:</label>
                  <div class="col-sm-10">
                    <select id="selectRecaptchaThemeID" class="form-control" name="recaptchaThemeID" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="1" <?php echo ($readTheme["recaptchaThemeID"] == 1) ? 'selected="selected"' : null; ?>>Light</option>
                      <option value="2" <?php echo ($readTheme["recaptchaThemeID"] == 2) ? 'selected="selected"' : null; ?>>Dark</option>
                    </select>
                  </div>
                </div>
                <?php echo $csrf->input('updateThemeSettings'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="updateThemeSettings">Değişiklikleri Kaydet</button>
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
<?php elseif (get("target") == 'header'): ?>
  <?php if (get("action") == 'update'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Header</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Tema</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Header</li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <div class="card">
            <div class="card-header">
              Header Elaman Ekle/Düzenle
            </div>
            <div class="card-body">
              <form id="formNestable" class="form-horizontal">
                <div class="form-group">
                  <label for="selectPageList">Sayfa Türü:</label>
                  <select id="selectPageTypeList" class="form-control" name="pageTypeList" data-toggle="select" data-minimum-results-for-search="-1">
                    <option value="custom">Özel Sayfa</option>
                    <option value="home">Ana Sayfa</option>
                    <option value="store">Mağaza</option>
                    <option value="games">Oyunlar</option>
                    <option value="lottery">Çarkıfelek</option>
                    <option value="credit"><?php echo $readSettings["creditText"] ?></option>
                    <option value="credit-charge"><?php echo $readSettings["creditText"] ?> Yükle</option>
                    <option value="credit-send"><?php echo $readSettings["creditText"] ?> Gönder</option>
                    <option value="leaderboards">Sıralama</option>
                    <option value="support">Destek</option>
                    <option value="chest">Sandık</option>
                    <option value="help">Yardım Merkezi</option>
                    <option value="bazaar">Pazar</option>
                    <option value="gaming-night">Gaming Gecesi</option>
                    <option value="forum">Forum</option>
                    <option value="download">İndir</option>
                  </select>
                  <input type="hidden" name="pagetype" value="custom">
                </div>
                <div class="form-group">
                  <label for="inputTitle">Başlık:</label>
                  <div class="input-group">
                    <input type="text" id="inputTitle" class="form-control" name="title" placeholder="Örn: Özel Sayfa">
                    <div class="input-group-append">
                      <button type="button" class="btn btn-success" data-toggle="iconpicker"></button>
                    </div>
                  </div>
                  <input type="hidden" id="inputIcon" name="icon">
                </div>
                <div class="form-group">
                  <label for="inputURL">Bağlantı (URL):</label>
                  <input type="text" class="form-control" id="inputURL" name="url" placeholder="Örn: /ozel-sayfa">
                </div>
                <div class="form-group">
                  <label for="selectTab">Sekme:</label>
                  <select id="selectTabStatus" class="form-control" name="tabstatus" data-toggle="select" data-minimum-results-for-search="-1">
                    <option value="0">Aynı Sekme</option>
                    <option value="1">Yeni Sekme</option>
                  </select>
                </div>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="button" class="btn btn-rounded btn-danger" data-action="cancel" style="display: none;">İptal</button>
                    <button type="button" class="btn btn-rounded btn-success" data-action="update" style="display: none;">Güncelle</button>
                    <button type="button" class="btn btn-rounded btn-success" data-action="insert" style="display: inline-block;">Ekle</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="col-md-9">
          <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["updateHeader"])) {
              if (!$csrf->validate('updateHeader')) {
                echo alertError("Sistemsel bir sorun oluştu!");
                echo goDelay("/yonetim-paneli/tema/header", 2);
              }
              else if (post("json") == null) {
                echo alertError("Lütfen boş alan bırakmayınız!");
              }
              else {
                $updateTheme = $db->prepare("UPDATE Theme SET header = ? WHERE id = ?");
                $updateTheme->execute(array($_POST["json"], $readTheme["id"]));
                echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                echo goDelay("/yonetim-paneli/tema/header", 2);
                createLog($readAdmin["id"], "THEME_HEADER_UPDATED");
              }
            }
          ?>
          <div class="card">
            <div class="card-header">
              Header Elamanları
            </div>
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group">
                  <div class="dd" data-toggle="nestable"></div>
                  <input type="hidden" name="json" value='<?php echo htmlentities($readTheme["header"], ENT_QUOTES, 'UTF-8'); ?>'>
                </div>
                <?php echo $csrf->input('updateHeader'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="updateHeader">Değişiklikleri Kaydet</button>
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
<?php elseif (get("target") == 'color'): ?>
  <?php if (get("action") == 'update'): ?>
    <?php $readColors = json_decode($readTheme["colors"], true); ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Renk</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Tema</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Renk</li>
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
          <?php if ($readTheme["themeID"] != 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if ($readTheme["themeID"] == 1) {
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
              }
              else if ($readTheme["themeID"] == 2) {
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
                  'link'                  => '#ffffff',
                  'link:hover'            => '#95aac9',
                  'navbar-dark'           => '#152e4d',
                  'navbar-dark-text'      => '#ffffff',
                  'navbar-dark-link'      => '#5e72e4',
                  'navbar-dark-link-text' => '#ffffff',
                  'nav-tabs-border'       => '#5e72e4',
                  'search-icon'           => '#5e72e4',
                  'search-icon-text'      => '#ffffff',
                  'footer-top'            => '#152e4d',
                  'footer-top-text'       => '#95aac9',
                  'footer-bottom'         => '#1e3a5c',
                  'card-header'           => '#5e72e4',
                  'page-link'             => '#5e72e4',
                  'spinner'               => '#5e72e4',
                  'scrollup'              => '#5e72e4',
                  'body'                  => '#12263f',
                  'broadcast'             => '#12263f',
                  'broadcast-text'        => '#ffffff',
                  'navbar-online-active'  => '#02b875',
                  'navbar-online-passive' => '#f5365c',
                  'navbar-online-text'    => '#ffffff',
                  'slider-online-active'  => '#02b875',
                  'slider-online-passive' => '#f5365c',
                  'slider-online-text'    => '#ffffff'
                );
              }
              else {
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
              }
              if (isset($_POST["updateColors"])) {
                if (!$csrf->validate('updateColors')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                  echo goDelay("/yonetim-paneli/tema/renk", 2);
                }
                else if (post("colorID") == null) {
                  echo alertError("Lütfen gerekli alanları doldurunuz!");
                }
                else {
                  if (post("colorID") == 1) {
                    $extraColors["main"]              = $extraColors["primary"];
                    $extraColors["main:hover"]        = $extraColors["primary:hover"];
                    $extraColors["nav-tabs-border"]   = $extraColors["main"];
                    $extraColors["navbar-dark-link"]  = $extraColors["main"];
                    $extraColors["search-icon"]       = $extraColors["main"];
                    $extraColors["card-header"]       = $extraColors["main"];
                    $extraColors["page-link"]         = $extraColors["main"];
                    $extraColors["spinner"]           = $extraColors["main"];
                    $extraColors["scrollup"]          = $extraColors["main"];
                  }
                  else if (post("colorID") == 2) {
                    $extraColors["main"]        		  = $extraColors["success"];
                    $extraColors["main:hover"]  	   	= $extraColors["success:hover"];
                    $extraColors["nav-tabs-border"]   = $extraColors["main"];
                    $extraColors["navbar-dark-link"]  = $extraColors["main"];
                    $extraColors["search-icon"]       = $extraColors["main"];
                    $extraColors["card-header"]       = $extraColors["main"];
                    $extraColors["page-link"]         = $extraColors["main"];
                    $extraColors["spinner"]           = $extraColors["main"];
                    $extraColors["scrollup"]          = $extraColors["main"];
                  }
                  else if (post("colorID") == 3) {
                    $extraColors["main"]        		  = $extraColors["danger"];
                    $extraColors["main:hover"]  		  = $extraColors["danger:hover"];
                    $extraColors["nav-tabs-border"]   = $extraColors["main"];
                    $extraColors["navbar-dark-link"]  = $extraColors["main"];
                    $extraColors["search-icon"]       = $extraColors["main"];
                    $extraColors["card-header"]       = $extraColors["main"];
                    $extraColors["page-link"]         = $extraColors["main"];
                    $extraColors["spinner"]           = $extraColors["main"];
                    $extraColors["scrollup"]          = $extraColors["main"];
                  }
                  else if (post("colorID") == 4) {
                    $extraColors["main"]        		  = $extraColors["warning"];
                    $extraColors["main:hover"]  		  = $extraColors["warning:hover"];
                    $extraColors["nav-tabs-border"]   = $extraColors["main"];
                    $extraColors["navbar-dark-link"]  = $extraColors["main"];
                    $extraColors["search-icon"]       = $extraColors["main"];
                    $extraColors["card-header"]       = $extraColors["main"];
                    $extraColors["page-link"]         = $extraColors["main"];
                    $extraColors["spinner"]           = $extraColors["main"];
                    $extraColors["scrollup"]          = $extraColors["main"];
                  }
                  else if (post("colorID") == 0) {
                    $extraColors["main"]                    = post("main");
                    $extraColors["main:hover"]              = post("main:hover");
                    $extraColors["primary"]                 = post("primary");
                    $extraColors["primary:hover"]           = post("primary:hover");
                    $extraColors["success"]                 = post("success");
                    $extraColors["success:hover"]           = post("success:hover");
                    $extraColors["danger"]                  = post("danger");
                    $extraColors["danger:hover"]            = post("danger:hover");
                    $extraColors["warning"]                 = post("warning");
                    $extraColors["warning:hover"]           = post("warning:hover");
                    $extraColors["info"]                    = post("info");
                    $extraColors["info:hover"]              = post("info:hover");
                    $extraColors["link"]                    = post("link");
                    $extraColors["link:hover"]              = post("link:hover");
                    $extraColors["nav-tabs-border"]         = post("nav-tabs-border");
                    $extraColors["navbar-dark"]             = post("navbar-dark");
                    $extraColors["navbar-dark-text"]        = post("navbar-dark-text");
                    $extraColors["navbar-dark-link"]        = post("navbar-dark-link");
                    $extraColors["navbar-dark-link-text"]   = post("navbar-dark-link-text");
                    $extraColors["search-icon"]             = post("search-icon");
                    $extraColors["search-icon-text"]        = post("search-icon-text");
                    $extraColors["footer-top"]              = post("footer-top");
                    $extraColors["footer-top-text"]         = post("footer-top-text");
                    $extraColors["footer-bottom"]           = post("footer-bottom");
                    $extraColors["card-header"]             = post("card-header");
                    $extraColors["page-link"]               = post("page-link");
                    $extraColors["spinner"]                 = post("spinner");
                    $extraColors["scrollup"]                = post("scrollup");
                    $extraColors["body"]                    = post("body");
                    $extraColors["broadcast"]               = post("broadcast");
                    $extraColors["broadcast-text"]          = post("broadcast-text");
                    $extraColors["navbar-online-active"]    = post("navbar-online-active");
                    $extraColors["navbar-online-passive"]   = post("navbar-online-passive");
                    $extraColors["navbar-online-text"]      = post("navbar-online-text");
                    $extraColors["slider-online-active"]    = post("slider-online-active");
                    $extraColors["slider-online-passive"]   = post("slider-online-passive");
                    $extraColors["slider-online-text"]      = post("slider-online-text");
                  }
                  else {
                    $color = '#000000';
                    $hoverColor = '#000000';
                  }
                  $colors = array(
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
                  $colors = json_encode($colors);
                  $updateTheme = $db->prepare("UPDATE Theme SET colorID = ?, colors = ? WHERE id = ?");
                  $updateTheme->execute(array(post("colorID"), $colors, $readTheme["id"]));
                  echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                  echo goDelay("/yonetim-paneli/tema/renk", 2);
                  createLog($readAdmin["id"], "THEME_COLOR_UPDATED");
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="selectColor" class="col-sm-2 col-form-label">Renk:</label>
                    <div class="col-sm-10">
                      <select id="selectColor" class="form-control" name="colorID" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readTheme["colorID"] == 0) ? 'selected="selected"' : null; ?>>Özelleştirilmiş</option>
                        <option value="1" <?php echo ($readTheme["colorID"] == 1) ? 'selected="selected"' : null; ?>>Mavi (Varsayılan)</option>
                        <option value="2" <?php echo ($readTheme["colorID"] == 2) ? 'selected="selected"' : null; ?>>Yeşil</option>
                        <option value="3" <?php echo ($readTheme["colorID"] == 3) ? 'selected="selected"' : null; ?>>Kırmızı</option>
                        <option value="4" <?php echo ($readTheme["colorID"] == 4) ? 'selected="selected"' : null; ?>>Turuncu</option>
                      </select>
                    </div>
                  </div>
                  <div id="colorSettingsBlock" style="<?php echo ($readTheme["colorID"] == 0) ? "display: block;" : "display: none;"; ?>">
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Ana Renk:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="main" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".color-main"]["color"] != null) ? $readColors[".color-main"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Ana Renk (Koyu):</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="main:hover" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".color-main:hover"]["color"] != null) ? $readColors[".color-main:hover"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Mavi:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="primary" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".color-primary"]["color"] != null) ? $readColors[".color-primary"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Mavi (Koyu):</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="primary:hover" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".color-primary:hover"]["color"] != null) ? $readColors[".color-primary:hover"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Yeşil:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="success" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".color-success"]["color"] != null) ? $readColors[".color-success"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Yeşil (Koyu):</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="success:hover" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".color-success"]["color"] != null) ? $readColors[".color-success:hover"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Kırmızı:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="danger" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".color-danger"]["color"] != null) ? $readColors[".color-danger"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Kırmızı (Koyu):</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="danger:hover" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".color-danger:hover"]["color"] != null) ? $readColors[".color-danger:hover"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Turuncu:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="warning" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".color-warning"]["color"] != null) ? $readColors[".color-warning"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Turuncu (Koyu):</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="warning:hover" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".color-warning:hover"]["color"] != null) ? $readColors[".color-warning:hover"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Açık Mavi:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="info" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".color-info"]["color"] != null) ? $readColors[".color-info"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Açık Mavi (Koyu):</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="info:hover" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".color-info:hover"]["color"] != null) ? $readColors[".color-info:hover"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Arkaplan:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="body" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors["body"]["background-color"] != null) ? $readColors["body"]["background-color"] : (($readTheme["themeID"] == 1) ? "#f8f8f8" : "#12263f"); ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Bağlantı (Link):</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="link" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors["a"]["color"] != null) ? $readColors["a"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Bağlantı (Link) (Koyu):</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="link:hover" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors["a:active, a:hover, a:focus"]["color"] != null) ? $readColors["a:active, a:hover, a:focus"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Sekmeli Menü (Nav Tabs):</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="nav-tabs-border" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active"]["border-color"] != null) ? $readColors[".nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active"]["border-color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Duyuru Arkaplan:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="broadcast" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".broadcast"]["background-color"] != null) ? $readColors[".broadcast"]["background-color"] : (($readTheme["themeID"] == 1) ? "#232f3c" : "#12263f"); ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Duyuru Yazı:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="broadcast-text" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".broadcast-link"]["color"] != null) ? $readColors[".broadcast-link"]["color"] : "#ffffff"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Navbar Sunucu Online (Aktif):</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="navbar-online-active" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".navbar-server.active"]["background-color"] != null) ? $readColors[".navbar-server.active"]["background-color"] : "#02b875"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Navbar Sunucu Online (Kapalı):</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="navbar-online-passive" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".navbar-server"]["background-color"] != null) ? $readColors[".navbar-server"]["background-color"] : "#f5365c"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Navbar Sunucu Online Yazı:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="navbar-online-text" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".navbar-server"]["color"] != null) ? $readColors[".navbar-server"]["color"] : "#ffffff"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Slider Sunucu Online (Aktif):</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="slider-online-active" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".server-online-info.active"]["background-color"] != null) ? $readColors[".server-online-info.active"]["background-color"] : "#02b875"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Slider Sunucu Online (Kapalı):</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="slider-online-passive" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".server-online-info"]["background-color"] != null) ? $readColors[".server-online-info"]["background-color"] : "#f5365c"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Slider Sunucu Online Yazı:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="slider-online-text" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".server-online-info"]["color"] != null) ? $readColors[".server-online-info"]["color"] : "#ffffff"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Header Arkaplan:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="navbar-dark" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".navbar-dark"]["background-color"] != null) ? $readColors[".navbar-dark"]["background-color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Header Link Arkaplan (Aktif):</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="navbar-dark-link" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".navbar-dark .navbar-nav .nav-item.active .nav-link, .navbar-dark .navbar-nav .nav-item:hover .nav-link, .navbar-dark .navbar-nav .nav-item:focus .nav-link"]["background-color"] != null) ? $readColors[".navbar-dark .navbar-nav .nav-item.active .nav-link, .navbar-dark .navbar-nav .nav-item:hover .nav-link, .navbar-dark .navbar-nav .nav-item:focus .nav-link"]["background-color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Header Link Yazı (Aktif):</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="navbar-dark-link-text" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".navbar-dark .navbar-nav .nav-item.active .nav-link, .navbar-dark .navbar-nav .nav-item:hover .nav-link, .navbar-dark .navbar-nav .nav-item:focus .nav-link"]["color"] != null) ? $readColors[".navbar-dark .navbar-nav .nav-item.active .nav-link, .navbar-dark .navbar-nav .nav-item:hover .nav-link, .navbar-dark .navbar-nav .nav-item:focus .nav-link"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Header Link Yazı:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="navbar-dark-text" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".navbar-dark .navbar-nav .nav-link"]["color"] != null) ? $readColors[".navbar-dark .navbar-nav .nav-link"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Arama Tuşu Arkaplan:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="search-icon" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".search-icon"]["background-color"] != null) ? $readColors[".search-icon"]["background-color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Arama Tuşu Ikon:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="search-icon-text" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".search-icon"]["color"] != null) ? $readColors[".search-icon"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Footer Arkaplan (Üst):</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="footer-top" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".footer-top"]["background-color"] != null) ? $readColors[".footer-top"]["background-color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Footer Arkaplan (Alt):</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="footer-bottom" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".footer-bottom"]["background-color"] != null) ? $readColors[".footer-bottom"]["background-color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Footer Yazı:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="footer-top-text" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".footer-top, .footer-top ul li a"]["color"] != null) ? $readColors[".footer-top, .footer-top ul li a"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Kart Başlığı Arkaplan:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="card-header" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".card-header:first-child, .modal-header"]["background-color"] != null) ? $readColors[".card-header:first-child, .modal-header"]["background-color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Sayfalama Butonu Arkaplan (Aktif):</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="page-link" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors[".pagination .page-item.active .page-link, .pagination .page-item.active .page-link:hover"]["background-color"] != null) ? $readColors[".pagination .page-item.active .page-link, .pagination .page-item.active .page-link:hover"]["background-color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Yükleniyor Efekti:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="spinner" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors["#preloader .spinner-border"]["color"] != null) ? $readColors["#preloader .spinner-border"]["color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectColor" class="col-sm-2 col-form-label">Yukarı Kaydırma Butonu Arkaplan:</label>
                      <div class="col-sm-10">
                        <div id="colorPicker" class="colorpicker-component input-group input-group-merge mb-3" data-toggle="colorPicker">
                          <input type="text" class="form-control form-control-appended" name="scrollup" placeholder="Renk kodunu yazınız." value="<?php echo ($readColors["#scrollUp:hover"]["background-color"] != null) ? $readColors["#scrollUp:hover"]["background-color"] : "#000000"; ?>" required>
                          <div class="input-group-append">
                            <div class="input-group-text input-group-addon">
                              <i></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <input type="hidden" name="extraColors" value='<?php echo json_encode($extraColors); ?>'>
                  <?php echo $csrf->input('updateColors'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <button id="colorSettingsButton" class="btn btn-rounded-circle btn-warning" type="button" style="<?php echo ($readTheme["colorID"] == 0) ? "display: inline-block;" : "display: none;"; ?>" data-toggle="tooltip" data-placement="top" title="Renk Ayarlarını Sıfırla">
                        <i class="fe fe-refresh-cw"></i>
                      </button>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateColors">Değişiklikleri Kaydet</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError("Özelleştirilmiş tema seçeneğinde Renk ayarları kapalıdır! Tema > CSS'den renk değişimi yapabilirsiniz."); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'css'): ?>
  <?php if (get("action") == 'update'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">CSS</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Tema</a></li>
                      <li class="breadcrumb-item active" aria-current="page">CSS</li>
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
            if (isset($_POST["updateCustomCSS"])) {
              if (!$csrf->validate('updateCustomCSS')) {
                echo alertError("Sistemsel bir sorun oluştu!");
                echo goDelay("/yonetim-paneli/tema/css", 2);
              }
              else {
                $updateTheme = $db->prepare("UPDATE Theme SET customCSS = ? WHERE id = ?");
                $updateTheme->execute(array(post("customCSS"), $readTheme["id"]));
                echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                echo goDelay("/yonetim-paneli/tema/css", 2);
                createLog($readAdmin["id"], "THEME_CSS_UPDATED");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div  class="form-group row">
                  <div class="col-sm-12">
                    <p>Kendi CSS kodlarınızı bu alana yazabilirsiniz.</p>
                    <textarea id="textareaCustomCSS" class="form-control" data-toggle="codeEditor" name="customCSS"><?php echo $readTheme["customCSS"]; ?></textarea>
                  </div>
                </div>
                <?php echo $csrf->input('updateCustomCSS'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="updateCustomCSS">Değişiklikleri Kaydet</button>
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
