<?php
  if (isset($_POST["searchAccount"]) && post("search") != null) {
    go('/oyuncu/'.convertURL(post("search")));
  }
  if (isset($_SESSION["login"])) {
    $chestCount = $db->prepare("SELECT C.id FROM Chests C INNER JOIN Products P ON C.productID = P.id INNER JOIN Servers S ON P.serverID = S.id WHERE C.accountID = ? AND C.status = ?");
    $chestCount->execute(array($readAccount["id"], 0));
    $chestCount = $chestCount->rowCount();
  }
  $shoppingCartCount = 0;
  if (isset($_SESSION["login"])) {
    $shoppingCartCount = $db->prepare("SELECT SC.accountID FROM ShoppingCartProducts SCP INNER JOIN ShoppingCarts SC ON SC.accountID = SCP.shoppingCartID WHERE SC.accountID = ?");
    $shoppingCartCount->execute(array($readAccount["id"]));
    $shoppingCartCount = $shoppingCartCount->rowCount();
  }
?>
<style type="text/css">
  <?php if ($readTheme["headerTheme"] == 2 || $readTheme["headerTheme"] == 3): ?>
    @media all and (min-width: 992px) {
      .navbar-dark .navbar-nav .nav-item {
        margin: .375rem .125rem;
      }
    }
  <?php endif; ?>
</style>
<?php if ($readTheme["broadcastStatus"] == 1): ?>
  <?php $broadcast = $db->query("SELECT * FROM Broadcast ORDER BY id DESC"); ?>
  <?php if ($broadcast->rowCount() > 0): ?>
    <ul class="broadcast">
      <?php foreach ($broadcast as $readBroadcast): ?>
        <li class="broadcast-item">
          <a class="broadcast-link" href="<?php echo $readBroadcast["url"]; ?>"><?php echo $readBroadcast["title"]; ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
<?php endif; ?>
<?php if ($readTheme["headerTheme"] == 2 || $readTheme["headerTheme"] == 3): ?>
  <div class="header-banner">
    <div class="<?php echo ($readTheme["headerStyle"] == 1) ? 'container' : 'container-fluid'; ?>">
      <div class="header-banner-content flex-lg-row flex-column">
        <div class="d-flex flex-column <?php echo ($readTheme["headerTheme"] == 3) ? 'order-2' : 'order-lg-1 order-2'; ?> text-center text-uppercase mt-lg-0 mt-4">
          <div>
            <a href="#!" data-toggle="copyip" data-clipboard-action="copy" data-clipboard-text="<?php echo $serverIP; ?>">
              <span class="text-white">IP:</span>
              <span class="text-yellow"><?php echo $serverIP; ?></span>
            </a>
          </div>
          <div class="d-lg-block d-none my-3">
            <button type="button" class="btn btn-info btn-rounded btn-header-ipcopy px-4" data-toggle="copyip" data-clipboard-action="copy" data-clipboard-text="<?php echo $serverIP; ?>">
              <i class="fa fa-gamepad mr-1"></i>
              Tıkla ve Kopyala
            </button>
          </div>
          <div>
            <span class="text-yellow" data-toggle="onlinetext" server-ip="<?php echo $serverIP; ?>">-/-</span>
            <span class="text-white">kişi oynuyor!</span>
          </div>
        </div>
        <div class="d-flex flex-column overflow-hidden <?php echo ($readTheme["headerTheme"] == 3) ? 'order-1' : 'order-lg-2 order-1'; ?>">
          <div class="<?php echo ($readTheme["headerTheme"] == 2) ? 'zoom-hover' : null; ?> text-center">
            <a href="/">
              <img src="/apps/main/public/assets/img/extras/header-logo.png?cache=<?php echo $readTheme["updatedAt"]; ?>" class="header-banner-logo" alt="<?php echo $serverName; ?> Logo">
            </a>
          </div>
        </div>
        <?php if ($readTheme["headerTheme"] == 2): ?>
          <div class="d-lg-flex d-none flex-column order-3 text-center">
            <div>
              <span class="text-white">Muhteşem</span>
              <span class="text-yellow">indirimleri</span>
              <span class="text-white">kaçırma!</span>
            </div>
            <div class="my-3 mb-0">
              <a class="btn btn-info btn-rounded btn-header-store px-5" href="/magaza">
                <i class="fa fa-shopping-cart mr-1"></i>
                Mağaza
              </a>
            </div>
            <div class="d-none">
              <a href="/sepet">
                <span class="text-white">Sepetinizde</span>
                <span class="text-yellow">
                  <span class="shopping-cart-count"><?php echo $shoppingCartCount; ?></span>
                </span>
                <span class="text-white">ürün var</span>
              </a>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
<?php endif; ?>
<header class="header sticky-top">
  <nav class="navbar navbar-expand-lg navbar-dark shadow-none">
    <div class="<?php echo ($readTheme["headerStyle"] == 1) ? 'container' : 'container-fluid'; ?>">
      <a class="navbar-brand <?php echo (($readSettings["headerLogoType"] == 2) ? 'image' : null); ?> <?php echo ($readTheme["headerTheme"] == 2 || $readTheme["headerTheme"] == 3) ? 'd-inline-block d-lg-none' : null; ?>" href="/">
        <?php if ($readSettings["headerLogoType"] == 1): ?>
          <?php echo $serverName; ?>
        <?php elseif ($readSettings["headerLogoType"] == 2): ?>
          <img src="/apps/main/public/assets/img/extras/logo.png?cache=<?php echo $readSettings["updatedAt"]; ?>" alt="<?php echo $serverName; ?> Logo">
        <?php else: ?>
          <?php echo $serverName; ?>
        <?php endif; ?>
      </a>
      <button class="navbar-toggler p-0" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="navbar-collapse collapse justify-content-between align-items-center w-100" id="navbarSupportedContent">
        <ul id="navbarMainContent" class="nav navbar-nav text-center <?php echo ($readTheme["headerTheme"] == 1) ? 'mx-auto' : null; ?> <?php echo ($readTheme["headerTheme"] == 2 || $readTheme["headerTheme"] == 3) ? 'justify-content-between w-100' : null; ?>">
          <?php
            $activatedStatus = false;
            $headerJSON = json_decode($readTheme["header"], true);
          ?>
          <?php foreach ($headerJSON as $readHeader): ?>
            <?php if ($readHeader["pagetype"] == "support"): ?>
              <?php if (isset($_SESSION["login"])): ?>
                <?php
                  $unreadMessages = $db->prepare("SELECT S.id FROM Supports S INNER JOIN SupportCategories SC ON S.categoryID = SC.id INNER JOIN Servers Se ON S.serverID = Se.id WHERE S.statusID = ? AND S.readStatus = ? AND S.accountID = ?");
                  $unreadMessages->execute(array(2, 0, $readAccount["id"]));
                ?>
                <?php if ($unreadMessages->rowCount() > 0): ?>
                  <?php $readHeader["title"].=" <span>(".$unreadMessages->rowCount().")</span>"; ?>
                <?php endif; ?>
              <?php endif; ?>
            <?php endif; ?>
            <?php if ($readHeader["pagetype"] == "chest"): ?>
              <?php if (isset($_SESSION["login"])): ?>
                <?php if ($chestCount > 0): ?>
                  <?php $readHeader["title"].=" <span>(".$chestCount.")</span>"; ?>
                <?php endif; ?>
              <?php endif; ?>
            <?php endif; ?>
            <?php if (isset($readHeader["children"])): ?>
              <li class="nav-item dropdown <?php echo (((get("route") == $readHeader["pagetype"]) && ($activatedStatus == false)) ? "active" : null); ?>">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="<?php echo $readHeader["icon"]; ?>"></i> <?php echo $readHeader["title"]; ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <?php foreach ($readHeader["children"] as $readHeaderChildren): ?>
                    <a class="dropdown-item" href="<?php echo $readHeaderChildren["url"]; ?>" <?php echo (($readHeaderChildren["tabstatus"] == 1) ? "rel=\"external\"" : null); ?>><?php echo $readHeaderChildren["title"]; ?></a>
                  <?php endforeach; ?>
                </div>
              </li>
            <?php else: ?>
              <li class="nav-item <?php echo (((get("route") == $readHeader["pagetype"]) && ($activatedStatus == false)) ? "active" : null); ?>">
                <a class="nav-link" href="<?php echo $readHeader["url"]; ?>" <?php echo (($readHeader["tabstatus"] == 1) ? "rel=\"external\"" : null); ?>><i class="<?php echo $readHeader["icon"]; ?>"></i> <?php echo $readHeader["title"]; ?></a>
              </li>
            <?php endif; ?>
            <?php if (get("route") == $readHeader["pagetype"]): ?>
              <?php $activatedStatus = true; ?>
            <?php endif; ?>
          <?php endforeach; ?>
          <?php if (isset($_SESSION["login"])): ?>
            <?php if ($readTheme["headerTheme"] == 2 || $readTheme["headerTheme"] == 3): ?>
              <li class="nav-item dropdown pc <?php echo ((get("route") == "profile") ? "active" : null); ?>">
                <a id="profileDropdown" class="nav-link dropdown-toggle <?php echo ((get("route") == "profile") ? "active" : null); ?>" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">
                  <div class="d-inline-flex align-items-center">
                    <?php echo minecraftHead($readSettings["avatarAPI"], $readAccount["realname"], 14, "mr-1"); ?>
                    <?php echo $readAccount["realname"]; ?>
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                  <a class="dropdown-item" href="/profil">
                    <i class="fa fa-user-circle mr-1"></i>
                    <span>Profil</span>
                  </a>
                  <a class="dropdown-item" href="/kredi/yukle">
                    <i class="fa fa-coins mr-1"></i>
                    <span><?php echo $readSettings["creditText"] ?>: <strong><?php echo $readAccount["credit"]; ?> <i class="fa fa-plus-circle text-success"></i></strong></span>
                  </a>
                  <a class="dropdown-item" href="/sepet">
                    <i class="fa fa-shopping-cart mr-1"></i>
                    <span>Sepet (<span class="shopping-cart-count"><?php echo $shoppingCartCount; ?></span>)</span>
                  </a>
                  <a class="dropdown-item" href="/sandik">
                    <i class="fa fa-archive mr-1"></i>
                    <span>Sandık (<?php echo $chestCount; ?>)</span>
                  </a>
                  <?php if ($readSettings["bazaarStatus"] == 1): ?>
                    <a class="dropdown-item" href="/pazar-satis">
                      <i class="fa fa-box-open mr-1"></i>
                      <span>Pazar Deposu</span>
                    </a>
                  <?php endif; ?>
                  <a class="dropdown-item" href="/carkifelek">
                    <i class="fa fa-chart-pie mr-1"></i>
                    <span>Çarkıfelek</span>
                  </a>
                  <a class="dropdown-item" href="/hediye">
                    <i class="fa fa-gift mr-1"></i>
                    <span>Hediye Kuponu</span>
                  </a>
                  <?php if (checkStaff($readAccount)): ?>
                    <a class="dropdown-item" rel="external" href="/yonetim-paneli">
                      <i class="fa fa-tachometer-alt mr-1"></i>
                      <span>Yönetim Paneli</span>
                    </a>
                  <?php endif; ?>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="/cikis-yap" onclick="return confirm('Çıkış yapmak istediğinize emin misiniz?');">
                    <i class="fa fa-sign-out-alt mr-1"></i>
                    <span>Çıkış Yap</span>
                  </a>
                </div>
              </li>
            <?php endif; ?>
            <li class="nav-item mobil <?php echo ((get("route") == 'profile') ? 'active' : null); ?>">
              <a class="nav-link" href="/profil">
                <i class="fa fa-user-circle"></i>
                <span>Profil</span>
              </a>
            </li>
            <li class="nav-item mobil">
              <a class="nav-link" href="/kredi/yukle">
                <i class="fa fa-coins"></i>
                <span><?php echo $readSettings["creditText"] ?>: <strong><?php echo $readAccount["credit"]; ?></strong></span>
              </a>
            </li>
            <li class="nav-item mobil <?php echo ((get("route") == 'checkout') ? 'active' : null); ?>">
              <a class="nav-link" href="/sepet">
                <i class="fa fa-shopping-cart"></i>
                <span>Sepet (<span class="shopping-cart-count"><?php echo $shoppingCartCount; ?></span>)</span>
              </a>
            </li>
            <li class="nav-item mobil <?php echo ((get("route") == 'chest') ? 'active' : null); ?>">
              <a class="nav-link" href="/sandik">
                <i class="fa fa-archive"></i>
                <span>Sandık (<?php echo $chestCount; ?>)</span>
              </a>
            </li>
            <?php if ($readSettings["bazaarStatus"] == 1): ?>
              <li class="nav-item mobil <?php echo ((get("route") == 'manage-bazaar') ? 'active' : null); ?>">
                <a class="nav-link" href="/pazar-satis">
                  <i class="fa fa-box-open"></i>
                  <span>Pazar Deposu</span>
                </a>
              </li>
            <?php endif; ?>
            <li class="nav-item mobil <?php echo ((get("route") == 'lottery') ? 'active' : null); ?>">
              <a class="nav-link" href="/carkifelek">
                <i class="fa fa-chart-pie"></i>
                <span>Çarkıfelek</span>
              </a>
            </li>
            <li class="nav-item mobil <?php echo ((get("route") == 'gift') ? 'active' : null); ?>">
              <a class="nav-link" href="/hediye">
                <i class="fa fa-gift"></i>
                <span>Hediye Kuponu</span>
              </a>
            </li>
            <?php if (checkStaff($readAccount)): ?>
              <li class="nav-item mobil">
                <a class="nav-link" href="/yonetim-paneli">
                  <i class="fa fa-tachometer-alt"></i>
                  <span>Yönetim Paneli</span>
                </a>
              </li>
            <?php endif; ?>
            <li class="nav-item mobil">
              <a class="nav-link" href="/cikis-yap" onclick="return confirm('Çıkış yapmak istediğinize emin misiniz?');">
                <i class="fa fa-sign-out-alt"></i>
                <span>Çıkış Yap</span>
              </a>
            </li>
          <?php else : ?>
            <?php if ($readTheme["headerTheme"] == 2 || $readTheme["headerTheme"] == 3): ?>
              <li class="nav-item pc <?php echo ((get("route") == 'login') ? 'active' : null); ?>">
                <a class="nav-link" href="/giris-yap">
                  <i class="fa fa-sign-in-alt"></i>
                  Giriş Yap
                </a>
              </li>
              <li class="nav-item pc <?php echo ((get("route") == 'register') ? 'active' : null); ?>">
                <a class="nav-link" href="/kayit-ol">
                  <i class="fa fa-user-plus"></i>
                  Kayıt Ol
                </a>
              </li>
            <?php endif; ?>
            <li class="nav-item mobil <?php echo ((get("route") == 'login') ? 'active' : null); ?>">
              <a class="nav-link" href="/giris-yap">
                <i class="fa fa-sign-in-alt"></i>
                <span>Giriş Yap</span>
              </a>
            </li>
            <li class="nav-item mobil <?php echo ((get("route") == 'register') ? 'active' : null); ?>">
              <a class="nav-link" href="/kayit-ol">
                <i class="fa fa-user-plus"></i>
                <span>Kayıt Ol</span>
              </a>
            </li>
          <?php endif; ?>
          <?php if ($readTheme["headerTheme"] == 2 || $readTheme["headerTheme"] == 3): ?>
            <li class="nav-item nav-search pc">
              <a class="nav-link" href="/sepet" style="position: relative;">
                <div class="theme-color btn search-icon">
                  <span class="shopping-cart-count shopping-cart-count-circle"><?php echo $shoppingCartCount; ?></span>
                  <i class="fa fa-shopping-cart"></i>
                </div>
              </a>
            </li>
          <?php endif; ?>
        </ul>
        <?php if ($readTheme["headerTheme"] == 1): ?>
          <ul class="nav navbar-nav navbar-right navbar-buttons flex-row justify-content-center flex-nowrap">
            <?php if (isset($_SESSION["login"])): ?>
              <li class="nav-item dropdown pc <?php echo ((get("route") == "profile") ? "active" : null); ?>">
                <a id="profileDropdown" class="nav-link dropdown-toggle <?php echo ((get("route") == "profile") ? "active" : null); ?>" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">
                  <div class="d-inline-flex align-items-center">
                    <?php echo minecraftHead($readSettings["avatarAPI"], $readAccount["realname"], 14, "mr-1"); ?>
                    <?php echo $readAccount["realname"]; ?>
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                  <a class="dropdown-item" href="/profil">
                    <i class="fa fa-user-circle mr-1"></i>
                    <span>Profil</span>
                  </a>
                  <a class="dropdown-item" href="/kredi/yukle">
                    <i class="fa fa-coins mr-1"></i>
                    <span><?php echo $readSettings["creditText"] ?>: <strong><?php echo $readAccount["credit"]; ?> <i class="fa fa-plus-circle text-success"></i></strong></span>
                  </a>
                  <a class="dropdown-item" href="/sepet">
                    <i class="fa fa-shopping-cart mr-1"></i>
                    <span>Sepet (<span class="shopping-cart-count"><?php echo $shoppingCartCount; ?></span>)</span>
                  </a>
                  <a class="dropdown-item" href="/sandik">
                    <i class="fa fa-archive mr-1"></i>
                    <span>Sandık (<?php echo $chestCount; ?>)</span>
                  </a>
                  <?php if ($readSettings["bazaarStatus"] == 1): ?>
                    <a class="dropdown-item" href="/pazar-satis">
                      <i class="fa fa-box-open mr-1"></i>
                      <span>Pazar Deposu</span>
                    </a>
                  <?php endif; ?>
                  <a class="dropdown-item" href="/carkifelek">
                    <i class="fa fa-chart-pie mr-1"></i>
                    <span>Çarkıfelek</span>
                  </a>
                  <a class="dropdown-item" href="/hediye">
                    <i class="fa fa-gift mr-1"></i>
                    <span>Hediye Kuponu</span>
                  </a>
                  <?php if (checkStaff($readAccount)): ?>
                    <a class="dropdown-item" rel="external" href="/yonetim-paneli">
                      <i class="fa fa-tachometer-alt mr-1"></i>
                      <span>Yönetim Paneli</span>
                    </a>
                  <?php endif; ?>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="/cikis-yap" onclick="return confirm('Çıkış yapmak istediğinize emin misiniz?');">
                    <i class="fa fa-sign-out-alt mr-1"></i>
                    <span>Çıkış Yap</span>
                  </a>
                </div>
              </li>
            <?php else : ?>
              <li class="nav-item pc">
                <a class="nav-link" href="/giris-yap">Giriş Yap</a>
              </li>
              <li class="nav-item pc active">
                <a class="nav-link" href="/kayit-ol">Kayıt Ol</a>
              </li>
            <?php endif; ?>
            <li class="nav-item nav-search pc">
              <a class="nav-link" href="/sepet" style="position: relative;">
                <div class="theme-color btn search-icon">
                  <span class="shopping-cart-count shopping-cart-count-circle"><?php echo $shoppingCartCount; ?></span>
                  <i class="fa fa-shopping-cart"></i>
                </div>
              </a>
            </li>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </nav>
  <?php if ($readTheme["headerTheme"] == 1): ?>
    <nav class="navbar navbar-server" data-toggle="onlinebox">
      <div class="<?php echo ($readTheme["headerStyle"] == 1) ? 'container' : 'container-fluid'; ?>">
        <div class="navbar-online">
          Çevrimiçi: <span data-toggle="onlinetext" server-ip="<?php echo $serverIP; ?>">-/-</span>
        </div>
        <div class="navbar-ip" data-toggle="copyip" data-clipboard-action="copy" data-clipboard-text="<?php echo $serverIP; ?>">
          <span class="py-2" data-toggle="tooltip" data-placement="bottom" title="Sunucu Adresini Kopyala">
            <?php echo $serverIP; ?>
          </span>
        </div>
        <div class="navbar-version">
          Sürüm: <?php echo $serverVersion; ?>
        </div>
      </div>
    </nav>
  <?php endif; ?>
</header>

<!-- Preloader -->
<?php if ($readSettings["preloaderStatus"] == 1): ?>
  <div id="preloader">
    <div class="spinner-border" role="status">
      <span class="sr-only">Yükleniyor...</span>
    </div>
  </div>
<?php endif; ?>
