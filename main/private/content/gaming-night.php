<?php
  if ($readSettings["gamingNight"] == 0) {
    go("/404");
  }
  
  require_once(__ROOT__.'/apps/main/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/main/public/assets/js/gaming-night.js');
  $extraResourcesJS->addResource('https://unpkg.com/@pqina/flip/dist/flip.min.js');
?>
<link href="https://unpkg.com/@pqina/flip/dist/flip.min.css" rel="stylesheet">
<section class="section store-section">
  <div class="container">
    <?php if ($readSettings["gamingNightDay"] == date("l") && date("Hi") >= $readSettings["gamingNightStart"] && date("Hi") <= $readSettings["gamingNightEnd"]): ?>
      <div class="row">
        <div class="col-md-12">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="/">Ana Sayfa</a></li>
              <li class="breadcrumb-item active" aria-current="page">
                <?php echo $readSettings["gamingNightText"]; ?>
              </li>
            </ol>
          </nav>
        </div>
      </div>
      <div class="row">
        <div id="modalBox"></div>
        <div class="col-md-12">
          <?php
            $products = $db->query("SELECT P.id, P.name, P.price, P.imageID, P.imageType, GNP.price as discountedPrice, GNP.stock FROM GamingNightProducts GNP INNER JOIN Products P ON GNP.productID = P.id");
          ?>
          <?php if ($products->rowCount() > 0): ?>
            <div class="card">
              <div class="card-header">
                Ürünler
              </div>
              <div class="card-body">
                <div class="row store-cards">
                  <?php foreach ($products as $readProducts): ?>
                    <div class="col-md-3">
                      <div class="store-card">
                        <?php if ($readProducts["stock"] != -1): ?>
                          <div class="store-card-stock <?php echo ($readProducts["stock"] == 0) ? "stock-out" : "have-stock"; ?>">
                            <?php if ($readProducts["stock"] == 0): ?>
                              Stokta Yok!
                            <?php else : ?>
                              Sınırlı Stok!
                            <?php endif; ?>
                          </div>
                        <?php endif; ?>
                        <?php $discountPercent = round((($readProducts["price"]-$readProducts["discountedPrice"])*100)/($readProducts["price"])); ?>
                        <div class="store-card-discount">
                          <span>%<?php echo $discountPercent; ?></span>
                        </div>
                        <img class="store-card-img lazyload" data-src="/apps/main/public/assets/img/store/products/<?php echo $readProducts["imageID"].'.'.$readProducts["imageType"]; ?>" src="/apps/main/public/assets/img/loaders/store.png" alt="<?php echo $serverName." Ürün - ".$readProducts["name"]." Satın Al"; ?>">
                        <div class="row store-card-text">
                          <div class="col">
                            <span><?php echo $readProducts["name"]; ?></span>
                          </div>
                          <div class="col-auto">
                            <span class="old-price"><?php echo $readProducts["price"]; ?></span>
                            <small>/</small>
                            <?php $newPrice = $readProducts["discountedPrice"]; ?>
                            <span class="price"><?php echo $newPrice; ?> <i class="fa fa-coins"></i></span>
                          </div>
                        </div>
                        <div class="store-card-button">
                          <?php if ($readProducts["stock"] != -1): ?>
                            <div class="mb-2">
                              <?php if ($readProducts["stock"] == 0): ?>
                                <span class="text-danger small">Stokta ürün kalmadı!</span>
                              <?php else : ?>
                                <span class="text-success small">Stokta <?php echo $readProducts["stock"]; ?> adet ürün kaldı!</span>
                              <?php endif; ?>
                            </div>
                          <?php endif; ?>
                          <?php if ($readProducts["stock"] == 0): ?>
                            <button class="btn btn-danger w-100 stretched-link disabled">Stokta Yok!</button>
                          <?php else: ?>
                            <button class="btn btn-success w-100 stretched-link openBuyModal" product-id="<?php echo $readProducts["id"]; ?>">Satın Al</button>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError("Bu sayfaya ait veri bulunamadı!"); ?>
          <?php endif; ?>
        </div>
      </div>
    <?php else: ?>
      <?php
      $days = [
        'Monday' => 'Pazartesi',
        'Tuesday' => 'Salı',
        'Wednesday' => 'Çarşamba',
        'Thursday' => 'Perşembe',
        'Friday' => 'Cuma',
        'Saturday' => 'Cumartesi',
        'Sunday' => 'Pazar'
      ];
      $gamingNightStart = $readSettings["gamingNightStart"];
      $gamingNightStartFormatted = $gamingNightStart[0].$gamingNightStart[1].":".$gamingNightStart[2].$gamingNightStart[3];
  
      $gamingNightEnd = $readSettings["gamingNightEnd"];
      $gamingNightEndFormatted = $gamingNightEnd[0].$gamingNightEnd[1].":".$gamingNightEnd[2].$gamingNightEnd[3];
      
      if ($readSettings["gamingNightDay"] == date("l")) {
        $tick = date("Y-m-d")."T".$gamingNightStartFormatted.":00+03:00";
      }
      else {
        $tick = date("Y-m-d", strtotime("next ".$readSettings["gamingNightDay"]))."T".$gamingNightStartFormatted.":00+03:00";
      }
      ?>
      <div class="text-center pt-5">
        <h1 class="text-white"><?php echo $readSettings["gamingNightText"]; ?></h1>
        <p class="mb-5 text-white"><?php echo $readSettings["gamingNightText"]; ?> her <strong><?php echo $days[$readSettings["gamingNightDay"]]; ?></strong> saat <strong><?php echo $gamingNightStartFormatted."-".$gamingNightEndFormatted; ?></strong> arası aktif olmaktadır!</p>
        <style>
          body {
            background-color: black;
            background-image: url(/apps/main/public/assets/img/extras/gaming-bg.png);
          }
          .tick {
            font-size:1rem; white-space:nowrap; font-family:arial,sans-serif; max-width: 38rem; margin: auto;
          }
          .tick-flip,.tick-text-inline {
            font-size:2.5em;
          }
          .tick-label {
            margin-top:1em;font-size:0.825em;
            font-weight: 600;
          }
          .tick-char {
            width:1.5em;
          }
          .tick-text-inline {
            display:inline-block;text-align:center;min-width:1em;
          }
          .tick-text-inline+.tick-text-inline {
            margin-left:-.325em;
          }
          .tick-group {
            margin:0 .5em;text-align:center;
          }
          .tick-text-inline {
            color: rgb(90, 93, 99) !important;
          }
          .tick-label {
            color: #fff !important;
          }
          .tick-flip-panel {
            color: rgb(255, 255, 255) !important;
          }
          .tick-flip {
            font-family: !important;
          }
          .tick-flip-panel-text-wrapper {
            line-height: 1.45 !important;
          }
          .tick-flip-panel {
            background-color: rgb(59, 61, 59) !important;
          }
          .tick-flip {
            border-radius:0.12em !important;
          }
        </style>
  
        <div class="tick" data-did-init="handleTickInit">
          <div data-repeat="true" data-layout="horizontal fit" data-transform="preset(d, h, m, s) -> delay">
            <div class="tick-group">
              <div data-key="value" data-repeat="true" data-transform="pad(00) -> split -> delay">
                <span data-view="flip"></span>
              </div>
              <span data-key="label" data-view="text" class="tick-label"></span>
            </div>
          </div>
        </div>
      </div>
      <script>
        function handleTickInit(tick) {
      
          // uncomment to set labels to different language
          
          var locale = {
              YEAR_PLURAL: 'Yıl',
              YEAR_SINGULAR: 'Yıl',
              MONTH_PLURAL: 'Ay',
              MONTH_SINGULAR: 'Ay',
              WEEK_PLURAL: 'Hafta',
              WEEK_SINGULAR: 'Hafta',
              DAY_PLURAL: 'Gün',
              DAY_SINGULAR: 'Gün',
              HOUR_PLURAL: 'Saat',
              HOUR_SINGULAR: 'Saat',
              MINUTE_PLURAL: 'Dakika',
              MINUTE_SINGULAR: 'Dakika',
              SECOND_PLURAL: 'Saniye',
              SECOND_SINGULAR: 'Saniye',
              MILLISECOND_PLURAL: 'Milisaniye',
              MILLISECOND_SINGULAR: 'Milisaniye'
          };
  
          for (var key in locale) {
              if (!locale.hasOwnProperty(key)) { continue; }
              tick.setConstant(key, locale[key]);
          }
          
          // create the countdown counter
          var counter = Tick.count.down('<?php echo $tick; ?>');
      
          counter.onupdate = function(value) {
            tick.value = value;
          };
      
          counter.onended = function() {
            window.location = '/gaming-gecesi';
          };
        }
      </script>
    <?php endif; ?>
  </div>
</section>
