<?php
  if (!checkPerm($readAdmin, 'MANAGE_BLOG')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
?>
<?php if (get("target") == 'news'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Haberler</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Haberler</li>
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
          <?php $news = $db->query("SELECT N.id FROM News N INNER JOIN Accounts A ON N.accountID = A.id INNER JOIN NewsCategories NC ON N.categoryID = NC.id ORDER BY N.id DESC"); ?>
          <?php if ($news->rowCount() > 0): ?>
            <?php
              if (get("page")) {
                if (!is_numeric(get("page"))) {
                  $_GET["page"] = 1;
                }
                $page = intval(get("page"));
              }
              else {
                $page = 1;
              }

              $visiblePageCount = 5;
              $limit = 50;

              $itemsCount = $news->rowCount();
              $pageCount = ceil($itemsCount/$limit);
              if ($page > $pageCount) {
                $page = 1;
              }
              $visibleItemsCount = $page * $limit - $limit;
              $news = $db->query("SELECT N.*, A.realname, NC.name as categoryName FROM News N INNER JOIN Accounts A ON N.accountID = A.id INNER JOIN NewsCategories NC ON N.categoryID = NC.id ORDER BY N.id DESC LIMIT $visibleItemsCount, $limit");

              if (isset($_POST["query"])) {
                if (post("query") != null) {
                  $news = $db->prepare("SELECT N.*, A.realname, NC.name as categoryName FROM News N INNER JOIN Accounts A ON N.accountID = A.id INNER JOIN NewsCategories NC ON N.categoryID = NC.id WHERE N.title LIKE :search OR A.realname LIKE :search OR NC.name LIKE :search ORDER BY N.id DESC");
                  $news->execute(array(
                    "search" => '%'.post("query").'%'
                  ));
                }
              }
            ?>
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <form action="" method="post" class="d-flex align-items-center w-100">
                    <div class="col">
                      <div class="row align-items-center">
                        <div class="col-auto pr-0">
                          <span class="fe fe-search text-muted"></span>
                        </div>
                        <div class="col">
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="Arama Yap (Başlık, Yazar veya Kategori)" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success">Ara</button>
                      <a class="btn btn-sm btn-white" href="/yonetim-paneli/haber/ekle">Haber Ekle</a>
                    </div>
                  </form>
                </div>
              </div>
              <div id="loader" class="card-body p-0 is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 40px;">#ID</th>
                        <th>Başlık</th>
                        <th>Yazar</th>
                        <th>Kategori</th>
                        <th>Görüntülenme</th>
                        <th>Yorumlar</th>
                        <th>Tarih</th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($news as $readNews): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            <a href="/yonetim-paneli/haber/duzenle/<?php echo $readNews["id"]; ?>">
                              #<?php echo $readNews["id"]; ?>
                            </a>
                          </td>
                          <td>
                            <a href="/yonetim-paneli/haber/duzenle/<?php echo $readNews["id"]; ?>">
                              <?php echo $readNews["title"]; ?>
                            </a>
                          </td>
                          <td>
                            <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readNews["accountID"]; ?>">
                              <?php echo $readNews["realname"]; ?>
                            </a>
                          </td>
                          <td>
                            <?php echo $readNews["categoryName"]; ?>
                          </td>
                          <td>
                            <?php echo $readNews["views"]; ?>
                          </td>
                          <td>
                            <?php
                              $newsComments = $db->prepare("SELECT NC.id FROM NewsComments NC INNER JOIN Accounts A ON NC.accountID = A.id WHERE NC.newsID = ? AND NC.status = ?");
                              $newsComments->execute(array($readNews["id"], 1));
                              echo $newsComments->rowCount();
                            ?>
                          </td>
                          <td>
                            <?php echo convertTime($readNews["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/haber/duzenle/<?php echo $readNews["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/haber/<?php echo $readNews["id"]; ?>/<?php echo $readNews["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/haber/sil/<?php echo $readNews["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                              <i class="fe fe-trash-2"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <?php if (post("query") == false): ?>
              <nav class="pt-3 pb-5" aria-label="Page Navigation">
                <ul class="pagination justify-content-center">
                  <li class="page-item <?php echo ($page == 1) ? "disabled" : null; ?>">
                    <a class="page-link" href="/yonetim-paneli/haber/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/yonetim-paneli/haber/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/yonetim-paneli/haber/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
                  </li>
                </ul>
              </nav>
            <?php endif; ?>
          <?php else: ?>
            <?php echo alertError("Bu sayfaya ait veri bulunamadı!"); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'insert'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Haber Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/haber">Haberler</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Haber Ekle</li>
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
            if (isset($_POST["insertNews"])) {
              if (!$csrf->validate('insertNews')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("title") == null || post("categoryID") == null || post("content") == null || post("commentsStatus") == null) {
                echo alertError("Lütfen boş alan bırakmayınız!");
              }
              else if ($_FILES["image"]["size"] == null) {
                echo alertError("Lütfen bir resim seçiniz!");
              }
              else {
                require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                $upload = new \Verot\Upload\Upload($_FILES["image"], "tr_TR");
                $imageID = md5(uniqid(rand(0, 9999)));
                if ($upload->uploaded) {
                  $upload->allowed = array("image/*");
                  $upload->file_new_name_body = $imageID;
                  $upload->image_resize = true;
                  $upload->image_ratio_crop = true;
                  $upload->image_x = 640;
                  $upload->image_y = 360;
                  $upload->process(__ROOT__."/apps/main/public/assets/img/news/");
                  if ($upload->processed) {
                    $insertNews = $db->prepare("INSERT INTO News (accountID, title, slug, categoryID, content, imageID, imageType, views, commentsStatus, updateDate, creationDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insertNews->execute(array($readAdmin["id"], post("title"), convertURL(post("title")), post("categoryID"), filteredContent($_POST["content"]), $imageID, $upload->file_dst_name_ext, 0, post("commentsStatus"), date("Y-m-d H:i:s"), date("Y-m-d H:i:s")));
                    $newsLastInsertID = $db->lastInsertId();
                    if (post("tags") != null) {
                      $tags = explode(',', trim(post("tags"), ','));
                      $insertNewsTags = $db->prepare("INSERT INTO NewsTags (newsID, name, slug) VALUES (?, ?, ?)");
                      foreach ($tags as $tag) {
                        $insertNewsTags->execute(array($newsLastInsertID, $tag, convertURL($tag)));
                      }
                    }
                    echo alertSuccess("Haber yazısı başarıyla yayımlandı!");
                  }
                  else {
                    echo alertError("Resim yüklenirken bir hata oluştu: ".$upload->error);
                  }
                }
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group row">
                  <label for="inputTitle" class="col-sm-2 col-form-label">Başlık:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputTitle" class="form-control" name="title" placeholder="Haber başlığı giriniz.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputCategoryID" class="col-sm-2 col-form-label">Kategori:</label>
                  <div class="col-sm-10">
                    <?php $newsCategories = $db->query("SELECT * FROM NewsCategories"); ?>
                    <select id="selectCategoryID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="categoryID" <?php echo ($newsCategories->rowCount() == 0) ? "disabled" : null; ?>>
                      <?php if ($newsCategories->rowCount() > 0): ?>
                        <?php foreach ($newsCategories as $readNewsCategories): ?>
                          <option value="<?php echo $readNewsCategories["id"]; ?>">
                            <?php echo $readNewsCategories["name"]; ?>
                          </option>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <option>Kategori bulunamadı!</option>
                      <?php endif; ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaContent" class="col-sm-2 col-form-label">İçerik:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaContent" class="form-control" data-toggle="textEditor" name="content" placeholder="Haber içeriğini yazınız."></textarea>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputTags" class="col-sm-2 col-form-label">Etiketler:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputTags" class="form-control" data-toggle="tagsinput" name="tags" placeholder="Etiketleri yazınız.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectCommentsStatus" class="col-sm-2 col-form-label">Yorumlar:</label>
                  <div class="col-sm-10">
                    <select id="selectCommentsStatus" class="form-control" name="commentsStatus" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="0">Kapalı</option>
                      <option value="1" selected="selected">Açık</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="fileImage" class="col-sm-2 col-form-label">Resim:</label>
                  <div class="col-sm-10">
                    <div data-toggle="dropimage" class="dropimage">
                      <div class="di-thumbnail">
                        <img src="" alt="Ön İzleme">
                      </div>
                      <div class="di-select">
                        <label for="fileImage">Bir Resim Seçiniz</label>
                        <input type="file" id="fileImage" name="image" accept="image/*">
                      </div>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('insertNews'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertNews">Haberi Yayımla</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'update' && get("id")): ?>
    <?php
      $news = $db->prepare("SELECT * FROM News WHERE id = ?");
      $news->execute(array(get("id")));
      $readNews = $news->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Haber Düzenle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/haber">Haberler</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/haber">Haber Düzenle</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($news->rowCount() > 0) ? $readNews["title"] : "Bulunamadı!"; ?></li>
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
          <?php if ($news->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateNews"])) {
                if (!$csrf->validate('updateNews')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("title") == null || post("categoryID") == null || post("content") == null || post("commentsStatus") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  if ($_FILES["image"]["size"] != null) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                    $upload = new \Verot\Upload\Upload($_FILES["image"], "tr_TR");
                    $imageID = $readNews["imageID"];
                    if ($upload->uploaded) {
                      $upload->allowed = array("image/*");
                      $upload->file_overwrite = true;
                      $upload->file_new_name_body = $imageID;
                      $upload->image_resize = true;
                      $upload->image_ratio_crop = true;
                      $upload->image_x = 640;
                      $upload->image_y = 360;
                      $upload->process(__ROOT__."/apps/main/public/assets/img/news/");
                      if ($upload->processed) {
                        $updateNews = $db->prepare("UPDATE News SET imageType = ? WHERE id = ?");
                        $updateNews->execute(array($upload->file_dst_name_ext, $readNews["id"]));
                      }
                      else {
                        echo alertError("Resim yüklenirken bir hata oluştu: ".$upload->error);
                      }
                    }
                  }
                  $updateNews = $db->prepare("UPDATE News SET title = ?, slug = ?, categoryID = ?, content = ?, commentsStatus = ? WHERE id = ?");
                  $updateNews->execute(array(post("title"), convertURL(post("title")), post("categoryID"), filteredContent($_POST["content"]), post("commentsStatus"), $readNews["id"]));
                  if (post("tags") != null) {
                    $tags = explode(',', trim(post("tags"), ','));
                    $deleteNewsTags = $db->prepare("DELETE FROM NewsTags WHERE newsID = ?");
                    $deleteNewsTags->execute(array($readNews["id"]));
                    $insertNewsTags = $db->prepare("INSERT INTO NewsTags (newsID, name, slug) VALUES (?, ?, ?)");
                    foreach ($tags as $tag) {
                      $insertNewsTags->execute(array($readNews["id"], $tag, convertURL($tag)));
                    }
                  }
                  echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label for="inputTitle" class="col-sm-2 col-form-label">Başlık:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputTitle" class="form-control" name="title" placeholder="Haber başlığı giriniz." value="<?php echo $readNews["title"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputCategoryID" class="col-sm-2 col-form-label">Kategori:</label>
                    <div class="col-sm-10">
                      <?php $newsCategories = $db->query("SELECT * FROM NewsCategories"); ?>
                      <select id="selectCategoryID" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="categoryID" <?php echo ($newsCategories->rowCount() == 0) ? "disabled" : null; ?>>
                        <?php if ($newsCategories->rowCount() > 0): ?>
                          <?php foreach ($newsCategories as $readNewsCategories): ?>
                            <option value="<?php echo $readNewsCategories["id"]; ?>" <?php echo (($readNews["categoryID"] == $readNewsCategories["id"]) ? 'selected="selected"' : null); ?>>
                              <?php echo $readNewsCategories["name"]; ?>
                            </option>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <option>Kategori bulunamadı!</option>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="textareaContent" class="col-sm-2 col-form-label">İçerik:</label>
                    <div class="col-sm-10">
                      <textarea id="textareaContent" class="form-control" data-toggle="textEditor" name="content" placeholder="Haber içeriğini yazınız."><?php echo $readNews["content"]; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputTags" class="col-sm-2 col-form-label">Etiketler:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputTags" class="form-control" data-toggle="tagsinput" name="tags" placeholder="Etiketleri yazınız." value="
                        <?php
                          $newsTags = $db->prepare("SELECT NT.* FROM NewsTags NT INNER JOIN News N ON NT.newsID = N.id WHERE NT.newsID = ?");
                          $newsTags->execute(array($readNews["id"]));
                          if ($newsTags->rowCount() > 0) {
                            foreach ($newsTags as $readNewsTags) {
                              echo $readNewsTags["name"].',';
                            }
                          }
                         ?>
                       ">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectCommentsStatus" class="col-sm-2 col-form-label">Yorumlar:</label>
                    <div class="col-sm-10">
                      <select id="selectCommentsStatus" class="form-control" name="commentsStatus" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readNews["commentsStatus"] == 0) ? 'selected="selected"' : null; ?>>Kapalı</option>
                        <option value="1" <?php echo ($readNews["commentsStatus"] == 1) ? 'selected="selected"' : null; ?>>Açık</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="fileImage" class="col-sm-2 col-form-label">Resim:</label>
                    <div class="col-sm-10">
                      <div data-toggle="dropimage" class="dropimage active">
                        <div class="di-thumbnail">
                          <img src="/apps/main/public/assets/img/news/<?php echo $readNews["imageID"].'.'.$readNews["imageType"]; ?>" alt="Ön İzleme">
                        </div>
                        <div class="di-select">
                          <label for="fileImage">Bir Resim Seçiniz</label>
                          <input type="file" id="fileImage" name="image" accept="image/*">
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateNews'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/haber/sil/<?php echo $readNews["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/haber/<?php echo $readNews["id"]; ?>/<?php echo $readNews["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                        <i class="fe fe-eye"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateNews">Değişiklikleri Kaydet</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError("Bu sayfaya ait veri bulunamadı!"); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteNews = $db->prepare("DELETE FROM News WHERE id = ?");
      $deleteNews->execute(array(get("id")));
      go("/yonetim-paneli/haber");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'category'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Kategoriler</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/haber">Haber</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Kategoriler</li>
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
          <?php $newsCategories = $db->query("SELECT * FROM NewsCategories ORDER BY id DESC"); ?>
          <?php if ($newsCategories->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["newsCategoryID", "newsCategoryName"]'>
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <div class="row align-items-center">
                      <div class="col-auto pr-0">
                        <span class="fe fe-search text-muted"></span>
                      </div>
                      <div class="col">
                        <input type="search" class="form-control form-control-flush search" name="search" placeholder="Arama Yap">
                      </div>
                    </div>
                  </div>
                  <div class="col-auto">
                    <a class="btn btn-sm btn-white" href="/yonetim-paneli/haber/kategori/ekle">Kategori Ekle</a>
                  </div>
                </div>
              </div>
              <div id="loader" class="card-body p-0 is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 40px;">
                          <a href="#" class="text-muted sort" data-sort="newsCategoryID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="newsCategoryName">
                            Kategori Adı
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($newsCategories as $readNewsCategories): ?>
                        <tr>
                          <td class="newsCategoryID text-center" style="width: 40px;">
                            <a href="/yonetim-paneli/haber/kategori/duzenle/<?php echo $readNewsCategories["id"]; ?>">
                              #<?php echo $readNewsCategories["id"]; ?>
                            </a>
                          </td>
                          <td class="newsCategoryName">
                            <a href="/yonetim-paneli/haber/kategori/duzenle/<?php echo $readNewsCategories["id"]; ?>">
                              <?php echo $readNewsCategories["name"]; ?>
                            </a>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/haber/kategori/duzenle/<?php echo $readNewsCategories["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/kategori/<?php echo $readNewsCategories["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/haber/kategori/sil/<?php echo $readNewsCategories["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                              <i class="fe fe-trash-2"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError("Bu sayfaya ait veri bulunamadı!"); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'insert'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Kategori Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/haber">Haber</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/haber/kategori">Kategori</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Kategori Ekle</li>
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
            if (isset($_POST["insertNewsCategories"])) {
              if (!$csrf->validate('insertNewsCategories')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("name") == null) {
                echo alertError("Lütfen boş alan bırakmayınız!");
              }
              else {
                $insertNewsCategories = $db->prepare("INSERT INTO NewsCategories (name, slug) VALUES (?, ?)");
                $insertNewsCategories->execute(array(post("name"), convertURL(post("name"))));
                echo alertSuccess("Kategori başarıyla eklendi!");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputName" class="col-sm-2 col-form-label">Kategori Adı:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputName" class="form-control" name="name" placeholder="Kategori adı giriniz.">
                  </div>
                </div>
                <?php echo $csrf->input('insertNewsCategories'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertNewsCategories">Ekle</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'update' && get("id")): ?>
    <?php
      $newsCategory = $db->prepare("SELECT * FROM NewsCategories WHERE id = ?");
      $newsCategory->execute(array(get("id")));
      $readNewsCategory = $newsCategory->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Kategori Düzenle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/haber">Haber</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/haber/kategori">Kategori</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/haber/kategori">Kategori Düzenle</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($newsCategory->rowCount() > 0) ? $readNewsCategory["name"] : "Bulunamadı!"; ?></li>
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
          <?php if ($newsCategory->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateNewsCategories"])) {
                if (!$csrf->validate('updateNewsCategories')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("name") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  $updateNewsCategories = $db->prepare("UPDATE NewsCategories SET name = ?, slug = ? WHERE id = ?");
                  $updateNewsCategories->execute(array(post("name"), convertURL(post("name")), get("id")));
                  echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">Kategori Adı:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputName" class="form-control" name="name" placeholder="Kategori adı giriniz." value="<?php echo $readNewsCategory["name"]; ?>">
                    </div>
                  </div>
                  <?php echo $csrf->input('updateNewsCategories'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/haber/kategori/sil/<?php echo $readNewsCategory["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/kategori/<?php echo $readNewsCategory["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                        <i class="fe fe-eye"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateNewsCategories">Değişiklikleri Kaydet</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError("Bu sayfaya ait veri bulunamadı!"); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteNewsCategory = $db->prepare("DELETE FROM NewsCategories WHERE id = ?");
      $deleteNewsCategory->execute(array(get("id")));
      go("/yonetim-paneli/haber/kategori");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'comment'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Yorumlar</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/haber">Haberler</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Yorumlar</li>
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
          <?php $newsComments = $db->query("SELECT NC.id FROM NewsComments NC INNER JOIN Accounts A ON NC.accountID = A.id INNER JOIN News N ON NC.newsID = N.id ORDER BY NC.id DESC"); ?>
          <?php if ($newsComments->rowCount() > 0): ?>
            <?php
              if (get("page")) {
                if (!is_numeric(get("page"))) {
                  $_GET["page"] = 1;
                }
                $page = intval(get("page"));
              }
              else {
                $page = 1;
              }

              $visiblePageCount = 5;
              $limit = 50;

              $itemsCount = $newsComments->rowCount();
              $pageCount = ceil($itemsCount/$limit);
              if ($page > $pageCount) {
                $page = 1;
              }
              $visibleItemsCount = $page * $limit - $limit;
              $newsComments = $db->query("SELECT NC.*, A.realname, N.slug as newsSlug FROM NewsComments NC INNER JOIN Accounts A ON NC.accountID = A.id INNER JOIN News N ON NC.newsID = N.id ORDER BY NC.id DESC LIMIT $visibleItemsCount, $limit");

              if (isset($_POST["query"])) {
                if (post("query") != null) {
                  $newsComments = $db->prepare("SELECT NC.*, A.realname, N.slug as newsSlug FROM NewsComments NC INNER JOIN Accounts A ON NC.accountID = A.id INNER JOIN News N ON NC.newsID = N.id WHERE NC.message LIKE :search OR A.realname LIKE :search ORDER BY NC.id DESC");
                  $newsComments->execute(array(
                    "search" => '%'.post("query").'%'
                  ));
                }
              }
            ?>
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <form action="" method="post" class="d-flex align-items-center w-100">
                    <div class="col">
                      <div class="row align-items-center">
                        <div class="col-auto pr-0">
                          <span class="fe fe-search text-muted"></span>
                        </div>
                        <div class="col">
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="Arama Yap (Mesaj veya Yazar)" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-sm btn-success">Ara</button>
                    </div>
                  </form>
                </div>
              </div>
              <div id="loader" class="card-body p-0 is-loading">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <div class="table-responsive">
                  <table id="tableComments" class="table table-sm table-nowrap card-table">
                    <thead>
                      <tr>
                        <th class="text-center" style="width: 40px;">#ID</th>
                        <th>Mesaj</th>
                        <th>Yazar</th>
                        <th>Tarih</th>
                        <th class="text-center">Durum</th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($newsComments as $readNewsComments): ?>
                        <tr>
                          <td class="text-center" style="width: 40px;">
                            <a href="/yonetim-paneli/haber/yorum/duzenle/<?php echo $readNewsComments["id"]; ?>">
                              #<?php echo $readNewsComments["id"]; ?>
                            </a>
                          </td>
                          <td>
                            <a href="/yonetim-paneli/haber/yorum/duzenle/<?php echo $readNewsComments["id"]; ?>">
                              <?php echo limitedContent($readNewsComments["message"], 30); ?>
                            </a>
                          </td>
                          <td>
                            <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readNewsComments["accountID"]; ?>">
                              <?php echo $readNewsComments["realname"]; ?>
                            </a>
                          </td>
                          <td>
                            <?php echo convertTime($readNewsComments["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-center">
                            <?php if ($readNewsComments["status"] == 0): ?>
                              <span class="badge badge-danger">Onaylanmadı</span>
                            <?php else: ?>
                              <span class="badge badge-success">Onaylandı</span>
                            <?php endif; ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/haber/yorum/duzenle/<?php echo $readNewsComments["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/haber/<?php echo $readNewsComments["newsID"]; ?>/<?php echo $readNewsComments["newsSlug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/haber/yorum/sil/<?php echo $readNewsComments["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                              <i class="fe fe-trash-2"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <?php if (post("query") == false): ?>
              <nav class="pt-3 pb-5" aria-label="Page Navigation">
                <ul class="pagination justify-content-center">
                  <li class="page-item <?php echo ($page == 1) ? "disabled" : null; ?>">
                    <a class="page-link" href="/yonetim-paneli/haber/yorum/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/yonetim-paneli/haber/yorum/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/yonetim-paneli/haber/yorum/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
                  </li>
                </ul>
              </nav>
            <?php endif; ?>
          <?php else: ?>
            <?php echo alertError("Bu sayfaya ait veri bulunamadı!"); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'get' && get("id")): ?>
    <?php
      $newsComment = $db->prepare("SELECT NC.*, A.realname, N.title as newsTitle, N.slug as newsSlug FROM NewsComments NC INNER JOIN Accounts A ON NC.accountID = A.id INNER JOIN News N ON NC.newsID = N.id WHERE NC.id = ?");
      $newsComment->execute(array(get("id")));
      $readNewsComment = $newsComment->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Yorum Görüntüle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/haber">Haberler</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/haber/yorum">Yorumlar</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($newsComment->rowCount() > 0) ? limitedContent($readNewsComment["message"], 30): "Bulunamadı!"; ?></li>
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
          <?php if ($newsComment->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateNewsComments"])) {
                if (!$csrf->validate('updateNewsComments')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else {
                  $changeStatus = ($readNewsComment["status"] == 0) ? '1': '0';
                  $updateNewsComments = $db->prepare("UPDATE NewsComments SET status = ? WHERE id = ?");
                  $updateNewsComments->execute(array($changeStatus, get("id")));
                  if ($changeStatus == '0') {
                    echo alertSuccess("Yorumun onayı başarıyla kaldırıldı!");
                  }
                  else {
                    echo alertSuccess("Yorum başarıyla onaylandı!");
                  }
                }
              }
            ?>
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <h4 class="card-header-title">
                      <a href="/haber/<?php echo $readNewsComment["newsID"]; ?>/<?php echo $readNewsComment["newsSlug"]; ?>" rel="external"><?php echo limitedContent($readNewsComment["newsTitle"], 30); ?></a>
                    </h4>
                  </div>
                  <div class="col-auto">
                    <span class="badge badge-pill badge-primary" data-toggle="tooltip" data-placement="top" data-original-title="Tarih"><?php echo convertTime($readNewsComment["creationDate"]); ?></span>
                    <?php if ($readNewsComment["status"] == 0): ?>
                      <span class="badge badge-pill badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Durum">Onaylanmadı</span>
                    <?php else: ?>
                      <span class="badge badge-pill badge-success" data-toggle="tooltip" data-placement="top" data-original-title="Durum">Onaylandı</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <form action="" method="post">
                  <div class="message">
                    <div class="message-img">
                      <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readNewsComment["accountID"]; ?>">
                        <img class="float-left rounded-circle" src="https://minotar.net/avatar/<?php echo $readNewsComment["realname"]; ?>/40.png" alt="<?php echo $serverName." Oyuncu - ".$readNewsComment["realname"]; ?> Mesaj">
                      </a>
                    </div>
                    <div class="message-content">
                      <div class="message-header">
                        <div class="message-username">
                          <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readNewsComment["accountID"]; ?>">
                            <?php echo $readNewsComment["realname"]; ?>
                          </a>
                        </div>
                        <div class="message-date">
                          <?php echo convertTime($readNewsComment["creationDate"]); ?>
                        </div>
                      </div>
                      <div class="message-body">
                        <p>
                          <?php echo showEmoji(urlContent(hashtag(hashtag($readNewsComment["message"], "@", "/yonetim-paneli/hesap/goruntule"), "#", "/etiket"))); ?>
                        </p>
                      </div>
                      <div class="message-footer">
                        <?php echo $csrf->input('updateNewsComments'); ?>
                        <div class="clearfix">
                          <div class="float-right">
                            <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/haber/yorum/sil/<?php echo $readNewsComment["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                              <i class="fe fe-trash-2"></i>
                            </a>
                            <a class="btn btn-rounded-circle btn-primary" href="/haber/<?php echo $readNewsComment["newsID"]; ?>/<?php echo $readNewsComment["newsSlug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                              <i class="fe fe-eye"></i>
                            </a>
                            <?php if ($readNewsComment["status"] == 0): ?>
                              <button type="submit" class="btn btn-rounded btn-success" name="updateNewsComments">Onayla</button>
                            <?php else: ?>
                              <button type="submit" class="btn btn-rounded btn-warning" name="updateNewsComments">Onayı Kaldır</button>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <?php else: ?>
            <?php echo alertError("Bu sayfaya ait veri bulunamadı!"); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteNewsComment = $db->prepare("DELETE FROM NewsComments WHERE id = ?");
      $deleteNewsComment->execute(array(get("id")));
      go("/yonetim-paneli/haber/yorum");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
