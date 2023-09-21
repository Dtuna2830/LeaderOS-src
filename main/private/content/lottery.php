<?php
  if (!isset($_SESSION["login"])) {
    go("/giris-yap");
  }

  if (get("lottery")) {
    $lottery = $db->prepare("SELECT * FROM Lotteries WHERE slug = ?");
    $lottery->execute(array(get("lottery")));
    $readLottery = $lottery->fetch();
  }
  else {
    $firstLottery = $db->query("SELECT slug FROM Lotteries ORDER BY id ASC LIMIT 1");
    $readFirstLottery = $firstLottery->fetch();
    if ($firstLottery->rowCount() > 0) {
      go("/carkifelek/".$readFirstLottery["slug"]);
    }
  }

  require_once(__ROOT__.'/apps/main/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/main/public/assets/js/plugins/superwheel/superwheel.min.js');
  $extraResourcesJS->addResource('/apps/main/public/assets/js/lottery.js');
?>
<section class="section credit-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Ana Sayfa</a></li>
            <?php if (get("lottery")): ?>
              <?php if ($lottery->rowCount() > 0): ?>
                <li class="breadcrumb-item"><a href="/carkifelek">Çarkıfelek</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $readLottery["title"]; ?></li>
              <?php else: ?>
                <li class="breadcrumb-item"><a href="/carkifelek">Çarkıfelek</a></li>
                <li class="breadcrumb-item active" aria-current="page">Bulunamadı</li>
              <?php endif; ?>
            <?php else: ?>
              <li class="breadcrumb-item active" aria-current="page">Çarkıfelek</li>
            <?php endif; ?>
          </ol>
        </nav>
      </div>
      <?php $lotteries = $db->query("SELECT title, slug FROM Lotteries"); ?>
      <?php if ($lotteries->rowCount() > 0): ?>
        <?php if ($lottery->rowCount() > 0): ?>
          <div class="col-md-8">
            <div class="card">
              <div class="card-body p-0">
                <nav>
                  <ul class="nav nav-tabs nav-fill">
                    <?php foreach ($lotteries as $readLotteries): ?>
                      <li class="nav-item">
                        <a class="nav-link <?php echo ($readLotteries["slug"] == get("lottery")) ? 'active' : null; ?>" href="/carkifelek/<?php echo $readLotteries["slug"]; ?>">
                          <?php echo $readLotteries["title"]; ?>
                        </a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </nav>
                <?php
                $slices = array();
                $lotteryAwards = $db->prepare("SELECT * FROM LotteryAwards WHERE lotteryID = ?");
                $lotteryAwards->execute(array($readLottery["id"]));
                foreach ($lotteryAwards as $readLotteryAwards) {
                  array_push($slices, array(
                    'id' 			    => (int)$readLotteryAwards["id"],
                    'text' 			  => $readLotteryAwards["title"],
                    'type'        => (int)$readLotteryAwards["awardType"],
                    'award'       => $readLotteryAwards["award"],
                    'background' 	=> $readLotteryAwards["color"],
                  ));
                }
                ?>
                <script type="text/javascript">
                  var lotteryID = <?php echo $readLottery["id"]; ?>;
                  var slices = <?php echo json_encode($slices); ?>;
                </script>
                <div class="tab-content px-4 pb-5">
                  <div class="d-flex justify-content-center align-items-center">
                    <div class="superwheel"></div>
                  </div>
                  <p class="text-center my-4">
                    <?php if ($readLottery["price"] == 0): ?>
                      Bu oyunu ücretsiz olarak <strong><?php echo $readLottery["duration"]; ?> saatte</strong> bir oynayabilirsin.
                    <?php else: ?>
                      Bu oyunu <strong><?php echo $readLottery["price"]; ?> <?php echo $readSettings["creditText"] ?></strong> karşılığında oynayabilirsin.
                    <?php endif; ?>
                  </p>
                  <div class="d-flex justify-content-center">
                    <button id="playGame" class="d-block btn btn-rounded btn-success">Kabul Et ve Oyna</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="row">
              <div class="col-md-12">
                <?php
                  $lotteryHistory = $db->prepare("SELECT LH.*, LA.title, LA.awardType, LA.award FROM LotteryHistory LH INNER JOIN LotteryAwards LA ON LH.lotteryAwardID = LA.id INNER JOIN Lotteries L ON LA.lotteryID = L.id WHERE L.id = ? AND LH.accountID = ? AND LA.awardType != ? ORDER by LH.id DESC LIMIT 5");
                  $lotteryHistory->execute(array($readLottery["id"], $readAccount["id"], 3));
                ?>
                <?php if ($lotteryHistory->rowCount() > 0): ?>
                  <div class="card mb-3">
                    <div class="card-header">
                      <div class="row">
                        <div class="col">
                          <span><?php echo $readLottery["title"]; ?> Geçmişi</span>
                        </div>
                        <div class="col-auto">
                          <a class="text-white" href="/profil">Tümü</a>
                        </div>
                      </div>
                    </div>
                    <div class="card-body p-0">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th class="text-center">#</th>
                              <th>Kullanıcı Adı</th>
                              <th class="text-right">Ödül</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($lotteryHistory as $readLotteryHistory): ?>
                              <tr>
                                <td class="text-center">
                                  <img class="rounded-circle" src="https://minotar.net/avatar/<?php echo $readAccount["realname"]; ?>/20.png" alt="<?php echo $serverName." Oyuncu - ".$readAccount["realname"]; ?>">
                                </td>
                                <td>
                                  <?php echo $readAccount["realname"]; ?>
                                </td>
                                <td class="text-right">
                                  <?php echo $readLotteryHistory["title"]; ?>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                <?php else : ?>
                  <?php echo alertError("Çarkıfelek geçmişi bulunamadı!"); ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="col-md-12">
            <?php echo alertError("Bu sayfaya ait çarkıfelek verisi bulunamadı!"); ?>
          </div>
        <?php endif; ?>
      <?php else: ?>
        <div class="col-md-12">
          <?php echo alertError("Çarkıfelek oyunu henüz yönetici tarafından eklenmemiş!"); ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
