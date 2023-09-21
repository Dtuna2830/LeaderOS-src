<?php
  if (!checkPerm($readAdmin, 'MANAGE_SLIDER')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  if (get("target") == 'slider' && get("action") == 'getAll') {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  }
?>
<?php if (get("target") == 'slider'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Slider</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Slider</li>
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
          <?php $slider = $db->query("SELECT * FROM Slider ORDER BY id ASC"); ?>
          <?php if ($slider->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["sliderID", "sliderTitle", "sliderURL", "sliderContent"]'>
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
                    <a class="btn btn-sm btn-white" href="/yonetim-paneli/slider/ekle">Slider Ekle</a>
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
                          <a href="#" class="text-muted sort" data-sort="sliderID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="sliderTitle">
                            Başlık
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="sliderURL">
                            Bağlantı
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="sliderContent">
                            İçerik
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($slider as $readSlider): ?>
                        <tr>
                          <td class="sliderID text-center" style="width: 40px;">
                            <a href="/yonetim-paneli/slider/duzenle/<?php echo $readSlider["id"]; ?>">
                              #<?php echo $readSlider["id"]; ?>
                            </a>
                          </td>
                          <td class="sliderTitle">
                            <a href="/yonetim-paneli/slider/duzenle/<?php echo $readSlider["id"]; ?>">
                              <?php echo limitedContent($readSlider["title"], 30); ?>
                            </a>
                          </td>
                          <td class="sliderURL">
                            <?php echo limitedContent($readSlider["url"], 30); ?>
                          </td>
                          <td class="sliderContent">
                            <?php echo limitedContent($readSlider["content"], 30); ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/slider/duzenle/<?php echo $readSlider["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/slider/sil/<?php echo $readSlider["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
                  <h2 class="header-title">Slider Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/slider">Slider</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Ekle</li>
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
            if (isset($_POST["insertSlider"])) {
              if (!$csrf->validate('insertSlider')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("title") == null || post("content") == null || post("url") == null) {
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
                  $upload->image_x = 1280;
                  $upload->image_y = 720;
                  $upload->process(__ROOT__."/apps/main/public/assets/img/slider/");
                  if ($upload->processed) {
                    $insertSlider = $db->prepare("INSERT INTO Slider (title, content, url, imageID, imageType) VALUES (?, ?, ?, ?, ?)");
                    $insertSlider->execute(array(post("title"), post("content"), post("url"), $imageID, $upload->file_dst_name_ext));
                    echo alertSuccess("Slider başarıyla eklendi!");
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
                    <input type="text" id="inputTitle" class="form-control" name="title" placeholder="Başlığı yazınız.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputContent" class="col-sm-2 col-form-label">İçerik:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputContent" class="form-control" name="content" placeholder="İçeriği yazınız.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputURL" class="col-sm-2 col-form-label">Bağlantı (Link):</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputURL" class="form-control" name="url" placeholder="Tıklayınca gidilecek bağlantıyı (link) yazınız.">
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
                <?php echo $csrf->input('insertSlider'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertSlider">Ekle</button>
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
      $slider = $db->prepare("SELECT * FROM Slider WHERE id = ?");
      $slider->execute(array(get("id")));
      $readSlider = $slider->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Slider Düzenle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/slider">Slider</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/slider">Düzenle</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($slider->rowCount() > 0) ? limitedContent($readSlider["title"], 30) : "Bulunamadı!"; ?></li>
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
          <?php if ($slider->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateSlider"])) {
                if (!$csrf->validate('updateSlider')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("title") == null || post("content") == null || post("url") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  if ($_FILES["image"]["size"] != null) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                    $upload = new \Verot\Upload\Upload($_FILES["image"], "tr_TR");
                    $imageID = $readSlider["imageID"];
                    if ($upload->uploaded) {
                      $upload->allowed = array("image/*");
                      $upload->file_overwrite = true;
                      $upload->file_new_name_body = $imageID;
                      $upload->image_resize = true;
                      $upload->image_ratio_crop = true;
                      $upload->image_x = 1280;
                      $upload->image_y = 720;
                      $upload->process(__ROOT__."/apps/main/public/assets/img/slider/");
                      if ($upload->processed) {
                        $updateSlider = $db->prepare("UPDATE Slider SET imageType = ? WHERE id = ?");
                        $updateSlider->execute(array($upload->file_dst_name_ext, get("id")));
                      }
                      else {
                        echo alertError("Resim yüklenirken bir hata oluştu: ".$upload->error);
                      }
                    }
                  }
                  $updateSlider = $db->prepare("UPDATE Slider SET title = ?, content = ?, url = ? WHERE id = ?");
                  $updateSlider->execute(array(post("title"), post("content"), post("url"), get("id")));
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
                      <input type="text" id="inputTitle" class="form-control" name="title" placeholder="Başlığı yazınız." value="<?php echo $readSlider["title"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputContent" class="col-sm-2 col-form-label">İçerik:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputContent" class="form-control" name="content" placeholder="İçeriği yazınız." value="<?php echo $readSlider["content"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputURL" class="col-sm-2 col-form-label">Bağlantı (Link):</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputURL" class="form-control" name="url" placeholder="Tıklayınca gidilecek bağlantıyı (link) yazınız." value="<?php echo $readSlider["url"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="fileImage" class="col-sm-2 col-form-label">Resim:</label>
                    <div class="col-sm-10">
                      <div data-toggle="dropimage" class="dropimage active">
                        <div class="di-thumbnail">
                          <img src="/apps/main/public/assets/img/slider/<?php echo $readSlider["imageID"].'.'.$readSlider["imageType"]; ?>" alt="Ön İzleme">
                        </div>
                        <div class="di-select">
                          <label for="fileImage">Bir Resim Seçiniz</label>
                          <input type="file" id="fileImage" name="image" accept="image/*">
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateSlider'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/slider/sil/<?php echo $readSlider["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateSlider">Değişiklikleri Kaydet</button>
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
      $deleteSlider = $db->prepare("DELETE FROM Slider WHERE id = ?");
      $deleteSlider->execute(array(get("id")));
      go("/yonetim-paneli/slider");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
