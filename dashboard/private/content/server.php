<?php
  if (!checkPerm($readAdmin, 'MANAGE_SERVERS')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  if (get("target") == 'server') {
    if (get("action") == 'insert' || get("action") == 'update') {
      $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/server.js');
      $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/store.minecraft-items.js');
      $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/server.check.js');
    }
    if (get("action") == 'get' && get("id")) {
      $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/server.console.js');
    }
  }
?>
<?php if (get("target") == 'server'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Sunucular</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Sunucular</li>
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
          <?php $servers = $db->query("SELECT * FROM Servers ORDER BY id DESC"); ?>
          <?php if ($servers->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["serverID", "servername", "serverIP", "serverConsoleID", "serverConsolePort"]'>
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
                    <a class="btn btn-sm btn-white" href="/yonetim-paneli/sunucu/ekle">Sunucu Ekle</a>
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
                          <a href="#" class="text-muted sort" data-sort="serverID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="servername">
                            Sunucu Adı
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="serverIP">
                            Sunucu IP:Port
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="serverConsoleID">
                            Konsol Tipi
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="serverConsolePort">
                            Konsol Port
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($servers as $readServers): ?>
                        <tr>
                          <td class="serverID text-center" style="width: 40px;">
                            <a href="/yonetim-paneli/sunucu/ozet/<?php echo $readServers["id"]; ?>">
                              #<?php echo $readServers["id"]; ?>
                            </a>
                          </td>
                          <td class="servername">
                            <a href="/yonetim-paneli/sunucu/ozet/<?php echo $readServers["id"]; ?>">
                              <?php echo $readServers["name"]; ?>
                            </a>
                          </td>
                          <td class="serverIP">
                            <?php echo $readServers["ip"].":".$readServers["port"]; ?>
                          </td>
                          <td class="serverConsoleID">
                            <?php echo ($readServers["consoleID"] == 1 ? 'Websend' : (($readServers["consoleID"] == 2) ? 'RCON' : (($readServers["consoleID"] == 3) ? 'Websender' : 'Hata!'))); ?>
                          </td>
                          <td class="serverConsolePort">
                            <?php echo $readServers["consolePort"]; ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/sunucu/duzenle/<?php echo $readServers["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-primary" href="/yonetim-paneli/sunucu/ozet/<?php echo $readServers["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Konsol & Özet">
                              <i class="fe fe-activity"></i>
                            </a>
                            <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/sunucu/sil/<?php echo $readServers["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
                  <h2 class="header-title">Sunucu Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/sunucu">Sunucular</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Sunucu Ekle</li>
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
            if (isset($_POST["insertServers"])) {
              if (!$csrf->validate('insertServers')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("name") == null || post("ip") == null || post("port") == null || post("consoleID") == null || post("consolePort") == null || post("consolePassword") == null || post("minecraftStatus") == null) {
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
                  $upload->process(__ROOT__."/apps/main/public/assets/img/servers/");
                  if ($upload->processed) {
                    if (post("minecraftItem") == "default") {
                      $minecraftItem = null;
                    }
                    else {
                      $minecraftItem = post("minecraftItem");
                    }
                    $insertServers = $db->prepare("INSERT INTO Servers (name, slug, ip, port, consoleID, consolePort, consolePassword, imageID, imageType, minecraftStatus, minecraftTitle, minecraftDescription, minecraftItem) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insertServers->execute(array(post("name"), convertURL(post("name")), post("ip"), post("port"), post("consoleID"), post("consolePort"), post("consolePassword"), $imageID, $upload->file_dst_name_ext, post("minecraftStatus"), post("minecraftTitle"), post("minecraftDescription"), $minecraftItem));
                    echo alertSuccess("Sunucu başarıyla eklendi!");
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
                  <label for="inputName" class="col-sm-2 col-form-label">Sunucu Adı:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputName" class="form-control" name="name" placeholder="Sunucu adını giriniz.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputIP" class="col-sm-2 col-form-label">Sunucu IP:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputIP" class="form-control" name="ip" placeholder="Sunucu IP adresini giriniz.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputPort" class="col-sm-2 col-form-label">Sunucu Port:</label>
                  <div class="col-sm-10">
                    <input type="number" id="inputPort" class="form-control" name="port" placeholder="Sunucu portunu giriniz.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectConsoleID" class="col-sm-2 col-form-label">Konsol Türü:</label>
                  <div class="col-sm-10">
                    <select id="selectConsoleID" class="form-control" name="consoleID" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="1">Websend</option>
                      <option value="2">RCON</option>
                      <option value="3">Websender</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputConsolePort" class="col-sm-2 col-form-label">Konsol Port:</label>
                  <div class="col-sm-10">
                    <input type="number" id="inputConsolePort" class="form-control" name="consolePort" placeholder="Konsol portunu giriniz.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputConsolePassword" class="col-sm-2 col-form-label">Konsol Şifre:</label>
                  <div class="col-sm-10">
                    <input type="password" id="inputConsolePassword" class="form-control" name="consolePassword" placeholder="Konsol şifresini giriniz.">
                    <div id="checkConnect" class="mt-3">
                      <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                        <span class="sr-only">-/-</span>
                      </div>
                      <a href="javascript:void(0);">Konsol Bağlantısını Kontrol Et</a>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectMinecraftStatus" class="col-sm-2 col-form-label">Oyunda Göster:</label>
                  <div class="col-sm-10">
                    <select id="selectMinecraftStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="minecraftStatus">
                      <option value="0">Hayır</option>
                      <option value="1">Evet</option>
                    </select>
                  </div>
                </div>
                <div id="minecraftBlock" style="display: none;">
                  <div class="form-group row">
                    <label for="input-minecrafttitle" class="col-sm-2 col-form-label">Minecraft GUI Başlığı (Title):</label>
                    <div class="col-sm-10">
                      <input type="text" id="input-minecrafttitle" class="form-control" name="minecraftTitle" placeholder="Boş bırakırsanız ürün adı gözükecektir. Renk Kodları (&) kullanabilirsiniz..">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="input-minecraftdesc" class="col-sm-2 col-form-label">Minecraft GUI Açıklama (Lore):</label>
                    <div class="col-sm-10">
                      <textarea id="input-minecraftdesc" class="form-control" name="minecraftDescription" placeholder="Boş bırakabilirsiniz. Renk Kodları (&) kullanabilirsiniz."></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectMinecraftItem" class="col-sm-2 col-form-label">Minecraft Icon:</label>
                    <div class="col-sm-10">
                      <div id="mi-loading" style="margin-top: 7px">Yükleniyor</div>
                      <div id="product-minecraftitems" style="display: none;" data-type="insert">
                        <select id="selectMinecraftItem" class="form-control" name="minecraftItem">
                          <option value="default">Varsayılan</option>
                        </select>
                      </div>
                    </div>
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
                <?php echo $csrf->input('insertServers'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertServers">Ekle</button>
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
      $server = $db->prepare("SELECT * FROM Servers WHERE id = ?");
      $server->execute(array(get("id")));
      $readServer = $server->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Sunucu Düzenle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/sunucu">Sunucular</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/sunucu">Sunucu Düzenle</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($server->rowCount() > 0) ? $readServer["name"] : "Bulunamadı!"; ?></li>
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
          <?php if ($server->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateServers"])) {
                if (!$csrf->validate('updateServers')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("name") == null || post("ip") == null || post("port") == null || post("consoleID") == null || post("consolePort") == null || post("consolePassword") == null || post("minecraftStatus") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  if ($_FILES["image"]["size"] != null) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/upload/upload.php");
                    $upload = new \Verot\Upload\Upload($_FILES["image"], "tr_TR");
                    $imageID = $readServer["imageID"];
                    if ($upload->uploaded) {
                      $upload->allowed = array("image/*");
                      $upload->file_overwrite = true;
                      $upload->file_new_name_body = $imageID;
                      $upload->image_resize = true;
                      $upload->image_ratio_crop = true;
                      $upload->image_x = 640;
                      $upload->image_y = 360;
                      $upload->process(__ROOT__."/apps/main/public/assets/img/servers/");
                      if ($upload->processed) {
                        $updateServers = $db->prepare("UPDATE Servers SET imageType = ? WHERE id = ?");
                        $updateServers->execute(array($upload->file_dst_name_ext, $readServer["id"]));
                      }
                      else {
                        echo alertError("Resim yüklenirken bir hata oluştu: ".$upload->error);
                      }
                    }
                  }
                  if (post("minecraftItem") == "default") {
                    $minecraftItem = null;
                  }
                  else {
                    $minecraftItem = post("minecraftItem");
                  }
                  $updateServers = $db->prepare("UPDATE Servers SET name = ?, slug = ?, ip = ?, port = ?, consoleID = ?, consolePort = ?, consolePassword = ?, minecraftStatus = ?, minecraftTitle = ?, minecraftDescription = ?, minecraftItem = ? WHERE id = ?");
                  $updateServers->execute(array(post("name"), convertURL(post("name")), post("ip"), post("port"), post("consoleID"), post("consolePort"), post("consolePassword"), post("minecraftStatus"), post("minecraftTitle"), post("minecraftDescription"), $minecraftItem, get("id")));
                  echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">Sunucu Adı:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputName" class="form-control" name="name" placeholder="Sunucu adını giriniz." value="<?php echo $readServer["name"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputIP" class="col-sm-2 col-form-label">Sunucu IP:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputIP" class="form-control" name="ip" placeholder="Sunucu IP adresini giriniz." value="<?php echo $readServer["ip"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPort" class="col-sm-2 col-form-label">Sunucu Port:</label>
                    <div class="col-sm-10">
                      <input type="number" id="inputPort" class="form-control" name="port" placeholder="Sunucu portunu giriniz." value="<?php echo $readServer["port"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectConsoleID" class="col-sm-2 col-form-label">Konsol Türü:</label>
                    <div class="col-sm-10">
                      <select id="selectConsoleID" class="form-control" name="consoleID" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="1" <?php echo ($readServer["consoleID"] == 1) ? 'selected="selected"' : null; ?>>Websend</option>
                        <option value="2" <?php echo ($readServer["consoleID"] == 2) ? 'selected="selected"' : null; ?>>RCON</option>
                        <option value="3" <?php echo ($readServer["consoleID"] == 3) ? 'selected="selected"' : null; ?>>Websender</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputConsolePort" class="col-sm-2 col-form-label">Konsol Port:</label>
                    <div class="col-sm-10">
                      <input type="number" id="inputConsolePort" class="form-control" name="consolePort" placeholder="Konsol portunu giriniz." value="<?php echo $readServer["consolePort"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputConsolePassword" class="col-sm-2 col-form-label">Konsol Şifre:</label>
                    <div class="col-sm-10">
                      <input type="password" id="inputConsolePassword" class="form-control" name="consolePassword" placeholder="Konsol şifresini giriniz." value="<?php echo $readServer["consolePassword"]; ?>">
                      <div id="checkConnect" class="mt-3">
                        <div class="spinner-grow spinner-grow-sm mr-2" role="status" style="display: none;">
                          <span class="sr-only">-/-</span>
                        </div>
                        <a href="javascript:void(0);">Konsol Bağlantısını Kontrol Et</a>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectMinecraftStatus" class="col-sm-2 col-form-label">Oyunda Göster:</label>
                    <div class="col-sm-10">
                      <select id="selectMinecraftStatus" class="form-control" data-toggle="select" data-minimum-results-for-search="-1" name="minecraftStatus">
                        <option value="0" <?php echo ($readServer["minecraftStatus"] == 0) ? 'selected="selected"' : null; ?>>Hayır</option>
                        <option value="1" <?php echo ($readServer["minecraftStatus"] == 1) ? 'selected="selected"' : null; ?>>Evet</option>
                      </select>
                    </div>
                  </div>
                  <div id="minecraftBlock" style="<?php echo ($readServer["minecraftStatus"] == 0) ? "display: none;" : "display: block;"; ?>">
                    <div class="form-group row">
                      <label for="input-minecrafttitle" class="col-sm-2 col-form-label">Minecraft GUI Başlığı (Title):</label>
                      <div class="col-sm-10">
                        <input type="text" id="input-minecrafttitle" class="form-control" name="minecraftTitle" placeholder="Boş bırakırsanız ürün adı gözükecektir. Renk Kodları (&) kullanabilirsiniz." value="<?php echo $readServer["minecraftTitle"]; ?>">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="input-minecraftdesc" class="col-sm-2 col-form-label">Minecraft GUI Açıklama (Lore):</label>
                      <div class="col-sm-10">
                        <textarea id="input-minecraftdesc" class="form-control" name="minecraftDescription" placeholder="Boş bırakabilirsiniz. Renk Kodları (&) kullanabilirsiniz."><?php echo $readServer["minecraftDescription"]; ?></textarea>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="selectMinecraftItem" class="col-sm-2 col-form-label">Minecraft Icon:</label>
                      <div class="col-sm-10">
                        <div id="mi-loading" style="margin-top: 7px">Yükleniyor...</div>
                        <div id="product-minecraftitems" style="display: none;" data-type="update" data-selected="<?php echo $readServer["minecraftItem"]; ?>">
                          <select id="selectMinecraftItem" class="form-control" name="minecraftItem">
                            <option value="default">Varsayılan</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="fileImage" class="col-sm-2 col-form-label">Resim:</label>
                    <div class="col-sm-10">
                      <div data-toggle="dropimage" class="dropimage active">
                        <div class="di-thumbnail">
                          <img src="/apps/main/public/assets/img/servers/<?php echo $readServer["imageID"].'.'.$readServer["imageType"]; ?>" alt="Ön İzleme">
                        </div>
                        <div class="di-select">
                          <label for="fileImage">Bir Resim Seçiniz</label>
                          <input type="file" id="fileImage" name="image" accept="image/*">
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateServers'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/sunucu/sil/<?php echo $readServer["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/yonetim-paneli/sunucu/ozet/<?php echo $readServer["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Konsol & Özet">
                        <i class="fe fe-activity"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateServers">Değişiklikleri Kaydet</button>
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
  <?php elseif (get("action") == 'get' && get("id")): ?>
    <?php
      $server = $db->prepare("SELECT * FROM Servers WHERE id = ?");
      $server->execute(array(get("id")));
      $readServer = $server->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Sunucu Özet</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/sunucu">Sunucular</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/sunucu">Sunucu Özet</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($server->rowCount() > 0) ? $readServer["name"] : "Bulunamadı!"; ?></li>
                    </ol>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <?php if ($server->rowCount() > 0): ?>
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <h4 class="card-header-title">
                      Konsol
                    </h4>
                  </div>
                  <div class="col-auto">
                    <a id="consoleRefresh" class="small text-muted" href="#">
                      <i class="fe fe-refresh-cw"></i>
                    </a>
                  </div>
                </div>
              </div>
              <div id="consoleBox" class="card-body" style="height: 200px !important; overflow: auto;">
                <div id="spinner">
                  <div class="spinner-border" role="status">
                    <span class="sr-only">-/-</span>
                  </div>
                </div>
                <?php
                  $consoleIP = $readServer["ip"];
                  $consoleID = $readServer["consoleID"];
                  $consolePort = $readServer["consolePort"];
                  $consolePassword = $readServer["consolePassword"];
                  $consoleTimeout = 3;

                  if ($consoleID == 1) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/websend/websend.php");
                    $console = new Websend($consoleIP, $consolePort);
                    $console->password = $consolePassword;
                  }
                  else if ($consoleID == 2) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/rcon/rcon.php");
                    $console = new Rcon($consoleIP, $consolePort, $consolePassword, $consoleTimeout);
                  }
                  else if ($consoleID == 3) {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/websender/websender.php");
                    $console = new Websender($consoleIP, $consolePassword, $consolePort);
                  }
                  else {
                    require_once(__ROOT__."/apps/dashboard/private/packages/class/websend/websend.php");
                    $console = new Websend($consoleIP, $consolePort);
                    $console->password = $consolePassword;
                  }
                ?>
                <div class="row mb-3">
                  <div class="col d-flex align-items-center">
                    <span class="badge badge-pill badge-secondary mr-2">Konsol</span>
                    <?php if (@$console->connect()): ?>
                      <strong class="text-success mr-1">BAŞARILI:</strong>
                      <span class="text-success">Bağlantı kuruldu!</span>
                      <?php $console->disconnect(); ?>
                    <?php else: ?>
                      <strong class="text-danger mr-1">HATA:</strong>
                      <span class="text-danger">Bağlantı kurulamadı!</span>
                    <?php endif; ?>
                  </div>
                  <div class="col-auto small">
                    <span><?php echo date("H:i"); ?></span>
                  </div>
                </div>
                <div id="consoleHistory"></div>
              </div>
              <div class="card-footer p-0">
                <input type="text" id="consoleCommand" class="form-control border-0" style="padding: .75rem 1.5rem; border-radius: 0 0 .375rem .375rem;" name="command" placeholder="Komutunuzu giriniz.">
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col">
                        <h6 class="card-title text-uppercase text-muted mb-2">
                          Çevrimiçi Oyuncu
                        </h6>
                        <span data-toggle="onlinetext" server-ip="<?php echo $readServer["ip"].':'.$readServer["port"]; ?>">-/-</span>
                      </div>
                      <div class="col-auto">
                        <span class="h2 fe fe-globe text-muted mb-0"></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <?php
                  $storeHistory = $db->prepare("SELECT P.name as productName, A.realname FROM OrderProducts OP INNER JOIN Orders O ON OP.orderID = O.id INNER JOIN Products P ON OP.productID = P.id INNER JOIN Servers S ON P.serverID = S.id INNER JOIN Accounts A ON O.accountID = A.id WHERE S.id = ? ORDER BY O.id DESC LIMIT 50");
                  $storeHistory->execute(array($readServer["id"]));
                ?>
                <?php if ($storeHistory->rowCount() > 0): ?>
                  <div class="card">
                    <div class="card-header">
                      <div class="row align-items-center">
                        <div class="col">
                          <h4 class="card-header-title">
                            Mağaza Geçmişi
                          </h4>
                        </div>
                        <div class="col-auto">
                          <span class="text-primary small">(Son 50 İşlem)</span>
                        </div>
                      </div>
                    </div>
                    <div class="card-body p-0">
                      <div class="table-responsive mb-0">
                        <table class="table table-sm table-no-wrap card-table">
                          <thead>
                            <tr>
                              <th>Kullanıcı</th>
                              <th class="text-center">Ürün</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($storeHistory as $readStoreHistory): ?>
                              <tr>
                                <td>
                                  <a class="avatar avatar-xs d-inline-block" href="/yonetim-paneli/hesap/goruntule/<?php echo $readStoreHistory["realname"]; ?>">
                                    <img src="https://minotar.net/avatar/<?php echo $readStoreHistory["realname"]; ?>/20.png" alt="Yönetici Hesabı" class="rounded-circle">
                                  </a>
                                  <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readStoreHistory["realname"]; ?>"><?php echo $readStoreHistory["realname"]; ?></a>
                                </td>
                                <td class="text-center"><?php echo $readStoreHistory["productName"]; ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                <?php else: ?>
                  <?php echo alertError("Mağaza geçmişi bulunamadı!"); ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <script type="text/javascript">
            var serverID      = <?php echo $readServer["id"]; ?>;
            var username        = '<?php echo $readAdmin["realname"]; ?>';
            var creationDate    = '<?php echo date('H:i'); ?>';
          </script>
        <?php else : ?>
          <div class="col-md-12"><?php echo alertError("Bu sayfaya ait veri bulunamadı!"); ?></div>
        <?php endif; ?>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
      $deleteServer = $db->prepare("DELETE FROM Servers WHERE id = ?");
      $deleteServer->execute(array(get("id")));
      go("/yonetim-paneli/sunucu");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
