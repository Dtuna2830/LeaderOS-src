<?php
  if (!checkPerm($readAdmin, 'MANAGE_GAMES')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
?>
<?php if (get("target") == 'game'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Oyunlar</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Oyunlar</li>
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
          <?php $games = $db->query("SELECT * FROM Games ORDER BY id DESC"); ?>
          <?php if ($games->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["gameID", "gameTitle", "gameCreationDate"]'>
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
                    <a class="btn btn-sm btn-white" href="/yonetim-paneli/oyun/ekle">Oyun Ekle</a>
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
                          <a href="#" class="text-muted sort" data-sort="gameID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="gameTitle">
                            Başlık
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="gameCreationDate">
                            Tarih
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($games as $readGames): ?>
                        <tr>
                          <td class="gameID text-center" style="width: 40px;">
                            <a href="/yonetim-paneli/oyun/duzenle/<?php echo $readGames["id"]; ?>">
                              #<?php echo $readGames["id"]; ?>
                            </a>
                          </td>
                          <td class="gameTitle">
                            <a href="/yonetim-paneli/oyun/duzenle/<?php echo $readGames["id"]; ?>">
                              <?php echo $readGames["title"]; ?>
                            </a>
                          </td>
                          <td class="gameCreationDate">
                            <?php echo convertTime($readGames["creationDate"], 2, true); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/oyun/duzenle/<?php echo $readGames["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/oyun/<?php echo $readGames["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                              <i class="fe fe-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/oyun/sil/<?php echo $readGames["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
                  <h2 class="header-title">Oyun Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/oyun">Oyunlar</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Oyun Ekle</li>
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
            if (isset($_POST["insertGames"])) {
              if (!$csrf->validate('insertGames')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("title") == null || post("content") == null) {
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
                  $upload->process(__ROOT__."/apps/main/public/assets/img/games/");
                  if ($upload->processed) {
                    $insertGames = $db->prepare("INSERT INTO Games (title, slug, imageID, imageType, content, creationDate) VALUES (?, ?, ?, ?, ?, ?)");
                    $insertGames->execute(array(post("title"), convertURL(post("title")), $imageID, $upload->file_dst_name_ext, filteredContent($_POST["content"]), date("Y-m-d H:i:s")));
                    echo alertSuccess("Oyun başarıyla eklendi!");
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
                    <input type="text" id="inputTitle" class="form-control" name="title" placeholder="Oyun başlığını giriniz.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaContent" class="col-sm-2 col-form-label">İçerik:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaContent" class="form-control" data-toggle="textEditor" name="content" placeholder="Oyun içeriğini yazınız."></textarea>
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
                <?php echo $csrf->input('insertGames'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertGames">Ekle</button>
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
      $game = $db->prepare("SELECT * FROM Games WHERE id = ?");
      $game->execute(array(get("id")));
      $readGame = $game->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Oyun Düzenle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/oyun">Oyunlar</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/oyun">Oyun Düzenle</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($game->rowCount() > 0) ? limitedContent($readGame["title"], 30): "Bulunamadı!"; ?></li>
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
          <?php if ($game->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateGames"])) {
                if (!$csrf->validate('updateGames')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("title") == null || post("content") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  if ($_FILES["image"]["size"] != null) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                    $upload = new \Verot\Upload\Upload($_FILES["image"], "tr_TR");
                    $imageID = $readGame["imageID"];
                    if ($upload->uploaded) {
                      $upload->allowed = array("image/*");
                      $upload->file_overwrite = true;
                      $upload->file_new_name_body = $imageID;
                      $upload->image_resize = true;
                      $upload->image_ratio_crop = true;
                      $upload->image_x = 640;
                      $upload->image_y = 360;
                      $upload->process(__ROOT__."/apps/main/public/assets/img/games/");
                      if ($upload->processed) {
                        $updateGames = $db->prepare("UPDATE Games SET imageType = ? WHERE id = ?");
                        $updateGames->execute(array($upload->file_dst_name_ext, $readGame["id"]));
                      }
                      else {
                        echo alertError("Resim yüklenirken bir hata oluştu: ".$upload->error);
                      }
                    }
                  }

                  $updateGames = $db->prepare("UPDATE Games SET title = ?, slug = ?, content = ? WHERE id = ?");
                  $updateGames->execute(array(post("title"), convertURL(post("title")), filteredContent($_POST["content"]), get("id")));
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
                      <input type="text" id="inputTitle" class="form-control" name="title" placeholder="Oyun başlığını giriniz." value="<?php echo $readGame["title"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="textareaContent" class="col-sm-2 col-form-label">İçerik:</label>
                    <div class="col-sm-10">
                      <textarea id="textareaContent" class="form-control" data-toggle="textEditor" name="content" placeholder="Oyun içeriğini yazınız."><?php echo $readGame["content"]; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="fileImage" class="col-sm-2 col-form-label">Resim:</label>
                    <div class="col-sm-10">
                      <div data-toggle="dropimage" class="dropimage active">
                        <div class="di-thumbnail">
                          <img src="/apps/main/public/assets/img/games/<?php echo $readGame["imageID"].'.'.$readGame["imageType"]; ?>" alt="Ön İzleme">
                        </div>
                        <div class="di-select">
                          <label for="fileImage">Bir Resim Seçiniz</label>
                          <input type="file" id="fileImage" name="image" accept="image/*">
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateGames'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/oyun/sil/<?php echo $readGame["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/oyun/<?php echo $readGame["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                        <i class="fe fe-eye"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateGames">Değişiklikleri Kaydet</button>
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
      $deleteGame = $db->prepare("DELETE FROM Games WHERE id = ?");
      $deleteGame->execute(array(get("id")));
      go("/yonetim-paneli/oyun");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
