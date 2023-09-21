<style>
  .bg-help {
    width: 100%;
    height: 100%;
    background-position: center !important;
    background-size: cover !important;
    background-image: url(/apps/main/public/assets/img/extras/help-bg.png);
  }
</style>
<section class="help-section">
  <?php if (get("search") || get("action") == "getTopic"): ?>
    <section class="bg-help py-5">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-8 col-md-10 text-center">
            <form action="/yardim-merkezi" method="GET">
              <div class="input-group">
                <input name="search" class="form-control form-control-lg rounded" type="text" placeholder="Ara..." value="<?php echo (get("search") ? get("search") : null) ?>">
                <?php if (get("search")): ?>
                  <a href="/yardim-merkezi">
                    <i class="fa fa-times position-absolute" style="top: 16px; right: 15px;"></i>
                  </a>
                <?php endif ?>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  <?php endif ?>
  <?php if (get("action") == "getAll"): ?>
    <?php if (get("search")): ?>
      <section class="container py-5">
        <h4 class="mb-5 text-center text-dark">"<?php echo get("search"); ?>" için arama sonuçları</h4>
        <div class="row">
          <?php
            $helpArticles = $db->prepare("SELECT id, slug, title FROM HelpArticles WHERE title LIKE :search OR content LIKE :search");
            $helpArticles->execute(array(":search" => "%".get("search")."%"));
          ?>
          <?php if ($helpArticles->rowCount() > 0): ?>
            <?php foreach ($helpArticles as $readHelpArticles): ?>
              <div class="col-md-6 mb-3">
                <a href="/yardim-merkezi/makale/<?php echo $readHelpArticles["id"]."/".$readHelpArticles["slug"]; ?>" class="text-default">
                  <div class="d-flex align-items-center bg-white py-3 px-4 border rounded">
                    <i class="fa fa-book text-muted mr-2"></i>
                    <span class="fw-500">
                      <?php echo $readHelpArticles["title"]; ?>
                    </span>
                  </div>
                </a>
              </div>
            <?php endforeach ?>
          <?php else: ?>
            <?php echo alertError("Yaptığınız arama için sonuç bulunamadı!"); ?>
          <?php endif ?>
        </div>
      </section>
    <?php else: ?>
      <section class="bg-help py-5">
        <div class="container">
          <div class="row justify-content-center py-md-5">
            <div class="col-lg-6 col-md-8 text-center">
              <h1 class="text-light pb-3">Size nasıl yardımcı olabiliriz?</h1>
              <form method="GET">
                <div class="input-group mb-3">
                  <input name="search" class="form-control form-control-lg rounded" type="text" placeholder="Ara...">
                </div>
              </form>
            </div>
          </div>
        </div>
      </section>
      <section class="container mt-4 pt-5 mt-md-0 pt-md-7 pb-5">
        <h2 class="h3 mb-4 text-center text-dark">Konular</h2>
        <div class="row">
          <?php
            $helpTopics = $db->query("SELECT name, description, slug, imageID, imageType FROM HelpTopics ORDER BY name ASC");
          ?>
          <?php if ($helpTopics->rowCount() > 0): ?>
            <?php foreach ($helpTopics as $readHelpTopics): ?>
              <div class="col-lg-4 col-sm-6 mb-grid-gutter">
                <a class="card h-100 shadow-none border" href="/yardim-merkezi/konu/<?php echo $readHelpTopics["slug"]; ?>">
                  <div class="card-body ps-grid-gutter pe-grid-gutter text-center">
                    <img class="mt-2 mb-4" src="/apps/main/public/assets/img/help/topics/<?php echo $readHelpTopics["imageID"].".".$readHelpTopics["imageType"]; ?>" loading="lazy" width="64" height="64" />
                    <h3 class="h5 text-dark">
                      <?php echo $readHelpTopics["name"]; ?>
                    </h3>
                    <p class="fs-sm text-body">
                      <?php echo limitedContent($readHelpTopics["description"], 250); ?>
                    </p>
                    <div class="btn btn-primary mb-2">Görüntüle</div>
                  </div>
                </a>
              </div>
            <?php endforeach ?>
          <?php else: ?>
            <?php echo alertError("Konu bulunamadı!"); ?>
          <?php endif ?>
        </div>
      </section>
      <section class="container pt-md-4 mb-2 pb-5 pb-md-6 mb-md-0">
        <h2 class="h3 mb-4 text-center text-dark">SSS</h2>
        <div class="row">
          <?php
            $topHelpArticles = $db->query("SELECT id, slug, title FROM HelpArticles ORDER BY views DESC LIMIT 10");
          ?>
          <?php if ($topHelpArticles->rowCount() > 0): ?>
            <?php foreach ($topHelpArticles as $readTopHelpArticles): ?>
              <div class="col-md-6 mb-3">
                <a href="/yardim-merkezi/makale/<?php echo $readTopHelpArticles["id"]."/".$readTopHelpArticles["slug"]; ?>" class="text-default">
                  <div class="d-flex align-items-center bg-white py-3 px-4 border rounded">
                    <i class="fa fa-book text-muted mr-2"></i>
                    <span class="fw-500">
                      <?php echo $readTopHelpArticles["title"]; ?>
                    </span>
                  </div>
                </a>
              </div>
            <?php endforeach ?>
          <?php else: ?>
            <?php echo alertError("Makale bulunamadı!"); ?>
          <?php endif ?>
        </div>
      </section>
    <?php endif ?>
  <?php elseif (get("action") == "getTopic" && get("category")): ?>
    <?php
    $helpTopic = $db->prepare("SELECT id, name FROM HelpTopics WHERE slug = ?");
    $helpTopic->execute(array(get("category")));
    $readHelpTopic = $helpTopic->fetch();
    ?>
    <section class="container py-5">
      <?php if ($helpTopic->rowCount() > 0): ?>
        <h2 class="mb-5 text-center text-dark">
          <?php echo $readHelpTopic["name"]; ?>
        </h2>
        <div class="row">
          <?php
            $helpArticles = $db->prepare("SELECT id, slug, title FROM HelpArticles WHERE topicID = ? ORDER BY id DESC");
            $helpArticles->execute(array($readHelpTopic["id"]));
          ?>
          <?php if ($helpArticles->rowCount() > 0): ?>
            <?php foreach ($helpArticles as $readHelpArticles): ?>
              <div class="col-md-6 mb-3">
                <a href="/yardim-merkezi/makale/<?php echo $readHelpArticles["id"]."/".$readHelpArticles["slug"]; ?>" class="text-default">
                  <div class="d-flex align-items-center bg-white py-3 px-4 border rounded">
                    <i class="fa fa-book text-muted mr-2"></i>
                    <span class="fw-500">
                      <?php echo $readHelpArticles["title"]; ?>
                    </span>
                  </div>
                </a>
              </div>
            <?php endforeach ?>
          <?php else: ?>
            <?php echo alertError("Makale bulunamadı!"); ?>
          <?php endif ?>
        </div>
      <?php else: ?>
        <?php echo alertError("Konu bulunamadı!"); ?>
      <?php endif ?>
    </section>
  <?php elseif (get("action") == "getArticle" && get("id")): ?>
    <?php
    $helpArticle = $db->prepare("SELECT HA.*, HT.name as topicName, HT.slug as topicSlug FROM HelpArticles HA INNER JOIN HelpTopics HT ON HA.topicID = HT.id WHERE HA.id = ?");
    $helpArticle->execute(array(get("id")));
    $readHelpArticle = $helpArticle->fetch();
    
    require_once(__ROOT__.'/apps/main/private/packages/class/extraresources/extraresources.php');
    $extraResourcesJS = new ExtraResources('js');
    $extraResourcesJS->addResource('/apps/main/public/assets/js/help.article.js');
    ?>
    <?php if ($helpArticle->rowCount() > 0): ?>
      <?php if (!isset($_COOKIE["helpArticleID"])): ?>
        <?php
        $updateHelpArticleViews = $db->prepare("UPDATE HelpArticles SET views = views + 1 WHERE id = ?");
        $updateHelpArticleViews->execute(array($readHelpArticle["id"]));
        setcookie("helpArticleID", $readHelpArticle["id"]);
        ?>
      <?php endif; ?>
      <section class="container py-5">
        <div class="row">
          <div class="col-md-9 mb-3">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Ana Sayfa</a></li>
                <li class="breadcrumb-item"><a href="/yardim-merkezi">Yardım Merkezi</a></li>
                <li class="breadcrumb-item"><a href="/yardim-merkezi/konu/<?php echo $readHelpArticle["topicSlug"]; ?>"><?php echo $readHelpArticle["topicName"]; ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $readHelpArticle["title"]; ?></li>
              </ol>
            </nav>
            <div class="card shadow-none border">
              <div class="card-header">
                <?php echo $readHelpArticle["title"]; ?>
              </div>
              <div class="card-body">
                <!-- Content-->
                <div class="article-content">
                  <?php echo $readHelpArticle["content"]; ?>
                </div>
                <div class="mt-3">
                  <span class="fw-50">Son Güncelleme:</span>
                  <span class="fst-italic">
                    <?php echo convertTime($readHelpArticle["updateDate"], 2, true); ?>
                  </span>
                </div>
                <?php if (isset($_SESSION["login"])): ?>
                  <?php if (checkPerm($readAccount, 'MANAGE_HELP_CENTER')): ?>
                    <div class="mt-3">
                      <span class="fw-50">Yetkililer için:</span>
                      <a href="/yonetim-paneli/yardim-merkezi/duzenle/<?php echo $readHelpArticle["id"]; ?>">Düzenle</a>
                    </div>
                  <?php endif ?>
                <?php endif ?>
                <!-- Rate article-->
                <?php $helpArticleVotes = isset($_COOKIE["helpArticleVotes"]) ? explode(",", $_COOKIE["helpArticleVotes"]) : array(); ?>
                <?php if (!isset($_COOKIE["helpArticleVotes"]) || !in_array($readHelpArticle["id"], $helpArticleVotes)): ?>
                  <div class="text-center border-top mt-5 pt-5">
                    <h4 class="h5 mb-3">
                      Bu makale yardımcı oldu mu?
                    </h4>
                    <div id="help-vote" data-id="<?php echo $readHelpArticle["id"]; ?>">
                      <button type="button" class="btn btn-danger mb-2 mx-1" data-value="0">Hayır</button>
                      <button type="button" class="btn btn-success mb-2 mx-1" data-value="1">Evet</button>
                    </div>
                  </div>
                <?php endif ?>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="mb-4">
              <form action="/yardim-merkezi" method="GET">
                <div class="input-group">
                  <input name="search" class="form-control rounded" type="text" placeholder="Ara..." style="height: 48px;">
                </div>
              </form>
            </div>
            <!-- Related articles-->
            <?php
              $relatedArticles = $db->prepare("SELECT id, title, slug FROM HelpArticles WHERE topicID = ? AND id != ?");
              $relatedArticles->execute(array($readHelpArticle["topicID"], $readHelpArticle["id"]));
            ?>
            <?php if ($relatedArticles->rowCount() > 0): ?>
              <div class="mb-5">
                <h3 class="text-dark h5">İlgili Makaleler</h3>
                <ul>
                  <?php foreach ($relatedArticles as $readRelatedArticles): ?>
                    <a href="/yardim-merkezi/makale/<?php echo $readRelatedArticles["id"]."/".$readRelatedArticles["slug"]; ?>" class="text-default">
                      <div class="d-flex align-items-center bg-white py-2 px-3 border rounded">
                        <span class="fw-500">
                          <?php echo $readRelatedArticles["title"]; ?>
                        </span>
                      </div>
                    </a>
                  <?php endforeach ?>
                </ul>
              </div>
            <?php endif ?>
          </div>
        </div>
      </section>
    <?php else: ?>
      <section class="container py-5">
        <?php echo alertError("Makale bulunamadı!"); ?>
      </section>
    <?php endif ?>
  <?php else: ?>
    <?php go("/404"); ?>
  <?php endif ?>
  <!-- Submit request-->
  <style>
    .footer {
      margin-top: 0;
    }
  </style>
  <section class="bg-white py-5">
    <div class="container text-center">
      <h2 class="h4 pb-2 mb-3">Aradığınız yanıtı bulamadınız mı?</h2>
      <a class="btn btn-rounded btn-success" href="/destek/gonder">Destek İsteyin</a>
    </div>
  </section>
</section>