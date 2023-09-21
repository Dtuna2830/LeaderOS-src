<footer class="footer">
  <div class="footer-top">
    <div class="container">
      <div class="row">
        <div class="col-md-5 mb-5 mb-md-0">
          <h5 class="footer-title">Hakkımızda</h5>
          <p class="mb-0">
            <?php if ($readSettings["footerAboutText"] == '0'): ?>
              Yönetici Panelinden bu yazıyı düzenleyebilirsiniz.
            <?php else: ?>
              <?php echo $readSettings["footerAboutText"]; ?>
            <?php endif; ?>
          </p>
        </div>
        <div class="col-6 col-md-2">
          <h5 class="footer-title">Hızlı Menü</h5>
          <ul class="list-unstyled mb-0">
            <li class="mb-2">
              <a href="/">Ana Sayfa</a>
            </li>
            <li class="mb-2">
              <a href="/magaza">Mağaza</a>
            </li>
            <li class="mb-2">
              <a href="/kredi/yukle"><?php echo $readSettings["creditText"] ?> Yükle</a>
            </li>
            <?php if (isset($_SESSION["login"])): ?>
              <li class="mb-2">
                <a href="/profil">Profil</a>
              </li>
            <?php else: ?>
              <li class="mb-2">
                <a href="/giris-yap">Giriş Yap</a>
              </li>
              <li class="mb-2">
                <a href="/kayit-ol">Kayıt Ol</a>
              </li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="col-6 col-md-2">
          <h5 class="footer-title">Sosyal Medya</h5>
          <ul class="list-unstyled mb-0">
            <?php if (($readSettings["footerFacebook"] != '0') || ($readSettings["footerTwitter"] != '0') || ($readSettings["footerInstagram"] != '0') || ($readSettings["footerYoutube"] != '0') || ($readSettings["footerDiscord"] != '0')): ?>
              <?php if ($readSettings["footerFacebook"] != '0'): ?>
                <li class="mb-2">
                  <a href="<?php echo $readSettings["footerFacebook"]; ?>" rel="external">
                    <i class="fab fa-facebook text-white mr-1"></i> Facebook
                  </a>
                </li>
              <?php endif; ?>
              <?php if ($readSettings["footerTwitter"] != '0'): ?>
                <li class="mb-2">
                  <a href="<?php echo $readSettings["footerTwitter"]; ?>" rel="external">
                    <i class="fab fa-twitter text-white mr-1"></i> Twitter
                  </a>
                </li>
              <?php endif; ?>
              <?php if ($readSettings["footerInstagram"] != '0'): ?>
                <li class="mb-2">
                  <a href="<?php echo $readSettings["footerInstagram"]; ?>" rel="external">
                    <i class="fab fa-instagram text-white mr-1"></i> Instagram
                  </a>
                </li>
              <?php endif; ?>
              <?php if ($readSettings["footerYoutube"] != '0'): ?>
                <li class="mb-2">
                  <a href="<?php echo $readSettings["footerYoutube"]; ?>" rel="external">
                    <i class="fab fa-youtube text-white mr-1"></i> Youtube
                  </a>
                </li>
              <?php endif; ?>
              <?php if ($readSettings["footerDiscord"] != '0'): ?>
                <li class="mb-2">
                  <a href="<?php echo $readSettings["footerDiscord"]; ?>" rel="external">
                    <i class="fab fa-discord text-white mr-1"></i> Discord
                  </a>
                </li>
              <?php endif; ?>
            <?php else: ?>
              <li>Yönetici Panelinden sosyal medya butonlarını ekleyebilirsin.</li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="col-md-3 mt-5 mt-md-0">
          <h5 class="footer-title">İletişim</h5>
          <ul class="list-unstyled mb-0">
            <?php if (($readSettings["footerEmail"] != '0') || ($readSettings["footerPhone"] != '0') || ($readSettings["footerWhatsapp"] != '0')): ?>
              <?php if ($readSettings["footerEmail"] != '0'): ?>
                <li class="mb-2">
                  <a href="mailto:<?php echo $readSettings["footerEmail"]; ?>" rel="external">
                    <i class="fa fa-envelope text-white mr-1"></i> <?php echo $readSettings["footerEmail"]; ?>
                  </a>
                </li>
              <?php endif; ?>
              <?php if ($readSettings["footerPhone"] != '0'): ?>
                <li class="mb-2">
                  <a href="tel:<?php echo $readSettings["footerPhone"]; ?>" rel="external">
                    <i class="fa fa-phone text-white mr-1"></i> <?php echo $readSettings["footerPhone"]; ?>
                  </a>
                </li>
              <?php endif; ?>
              <?php if ($readSettings["footerWhatsapp"] != '0'): ?>
                <li class="mb-2">
                  <a href="https://wa.me/<?php echo str_replace(array("+", " "), array('', ''), $readSettings["footerWhatsapp"]); ?>" rel="external">
                    <i class="fab fa-whatsapp text-white mr-1"></i> <?php echo $readSettings["footerWhatsapp"]; ?>
                  </a>
                </li>
              <?php endif; ?>
            <?php else: ?>
              <li>Yönetici Panelinden iletişim seçeneklerini ekleyebilirsin.</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      <div class="row">
        <div class="col-12 col-md text-center text-md-left mb-2 mb-md-0">
          Tüm hakları saklıdır &copy; <?php echo date("Y"); ?>
        </div>
        <div class="col-12 col-md-auto text-center text-md-left">
          <copyright data-toggle="tooltip" data-placement="top" title="Yazılım: FIRAT KAYA">
            <a href="https://www.leaderos.com.tr/" rel="external">
              LEADEROS <?php echo ("v".VERSION); ?>
            </a>
          </copyright>
        </div>
      </div>
    </div>
  </div>
</footer>
