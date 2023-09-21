<?php
  if (!checkPerm($readAdmin, 'MANAGE_ROLES')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
?>
<?php if (get("target") == 'role'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Roller</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Roller</li>
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
          <?php $roles = $db->query("SELECT * FROM Roles ORDER BY priority DESC, id DESC"); ?>
          <?php if ($roles->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["roleID", "roleName", "rolePriority"]'>
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
                    <a class="btn btn-sm btn-white" href="/yonetim-paneli/rol/ekle">Rol Ekle</a>
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
                          <a href="#" class="text-muted sort" data-sort="roleID">
                            #ID
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="roleName">
                            Ad
                          </a>
                        </th>
                        <th>
                          <a href="#" class="text-muted sort" data-sort="rolePriority">
                            Öncelik
                          </a>
                        </th>
                        <th class="text-right">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php foreach ($roles as $readRoles): ?>
                        <tr>
                          <td class="roleID text-center" style="width: 40px;">
                            <a href="/yonetim-paneli/rol/duzenle/<?php echo $readRoles["id"]; ?>">
                              #<?php echo $readRoles["id"]; ?>
                            </a>
                          </td>
                          <td class="roleName">
                            <?php echo $readRoles["name"]; ?>
                          </td>
                          <td class="rolePriority">
                            <?php echo $readRoles["priority"]; ?>
                          </td>
                          <td class="text-right">
                            <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/rol/duzenle/<?php echo $readRoles["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                              <i class="fe fe-edit-2"></i>
                            </a>
                            <?php if ($readRoles["id"] != 1): ?>
                              <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/rol/sil/<?php echo $readRoles["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                                <i class="fe fe-trash-2"></i>
                              </a>
                            <?php endif; ?>
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
                  <h2 class="header-title">Rol Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/rol">Roller</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Rol Ekle</li>
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
            if (isset($_POST["insertRole"])) {
              if (!$csrf->validate('insertRole')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("name") == null || post("priority") == null) {
                echo alertError("Lütfen boş alan bırakmayınız!");
              }
              else {
                $insertRole = $db->prepare("INSERT INTO Roles (name, slug, priority) VALUES (?, ?, ?)");
                $insertRole->execute(array(post("name"), convertURL(post("name")), post("priority")));
                $roleID = $db->lastInsertId();
  
                foreach ($_POST["permissions"] as $permission) {
                  $permission = strip_tags($permission);
                  $addPermToUser = $db->prepare("INSERT INTO RolePermissions (roleID, permissionID) VALUES (?, ?)");
                  $addPermToUser->execute(array($roleID, $permission));
                }
                
                echo alertSuccess("Rol başarıyla eklendi!");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputName" class="col-sm-2 col-form-label">Ad:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputName" class="form-control" name="name" placeholder="Rol adını giriniz.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputPriority" class="col-sm-2 col-form-label">Öncelik:</label>
                  <div class="col-sm-10">
                    <input type="number" id="inputPriority" class="form-control" name="priority" placeholder="Rol önceliğini giriniz." value="0">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectExtraPermission" class="col-sm-2 col-form-label">Yetkiler:</label>
                  <div class="col-sm-10">
                    <div class="row">
                      <?php $permissions = $db->query("SELECT * FROM Permissions"); ?>
                      <?php foreach ($permissions as $permission): ?>
                        <div class="col-sm-3">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="permissions[]" class="custom-control-input" id="permission_<?php echo $permission["id"] ?>" value="<?php echo $permission["id"] ?>">
                            <label class="custom-control-label" for="permission_<?php echo $permission["id"] ?>"><?php echo $permission["description"] ?></label>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('insertRole'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertRole">Ekle</button>
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
      $role = $db->prepare("SELECT * FROM Roles WHERE id = ?");
      $role->execute(array(get("id")));
      $readRole = $role->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Rol Düzenle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/rol">Roller</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/rol">Rol Düzenle</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($role->rowCount() > 0) ? $readRole["name"]: "Bulunamadı!"; ?></li>
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
          <?php if ($role->rowCount() > 0): ?>
            <?php
              require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
              $csrf = new CSRF('csrf-sessions', 'csrf-token');
              if (isset($_POST["updateRoles"])) {
                if (!$csrf->validate('updateRoles')) {
                  echo alertError("Sistemsel bir sorun oluştu!");
                }
                else if (post("name") == null || post("priority") == null) {
                  echo alertError("Lütfen boş alan bırakmayınız!");
                }
                else {
                  $slug = ($readRole["slug"] == 'default') ? 'default' : convertURL(post("name"));
                  $updateRoles = $db->prepare("UPDATE Roles SET name = ?, slug = ?, priority = ? WHERE id = ?");
                  $updateRoles->execute(array(post("name"), $slug, post("priority"), get("id")));
  
                  $removePermsFromRole = $db->prepare("DELETE FROM RolePermissions WHERE roleID = ?");
                  $removePermsFromRole->execute(array($readRole["id"]));
                  if ($readRole["id"] != 1) {
                    foreach ($_POST["permissions"] as $permission) {
                      $permission = strip_tags($permission);
                      $addPermToUser = $db->prepare("INSERT INTO RolePermissions (roleID, permissionID) VALUES (?, ?)");
                      $addPermToUser->execute(array($readRole["id"], $permission));
                    }
                  }
                  echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
                }
              }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">Ad:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputName" class="form-control" name="name" placeholder="Rol adını giriniz." value="<?php echo $readRole["name"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPriority" class="col-sm-2 col-form-label">Öncelik:</label>
                    <div class="col-sm-10">
                      <input type="number" id="inputPriority" class="form-control" name="priority" placeholder="Rol önceliğini giriniz." value="<?php echo $readRole["priority"]; ?>">
                    </div>
                  </div>
                  <?php if ($readRole["id"] != 1): ?>
                    <div class="form-group row">
                      <label for="selectExtraPermission" class="col-sm-2 col-form-label">Yetkiler:</label>
                      <div class="col-sm-10">
                        <div class="row">
                          <?php
                            $rolePermissionList = [];
                            $rolePermissions = $db->prepare("SELECT permissionID FROM RolePermissions WHERE roleID = ?");
                            $rolePermissions->execute(array($readRole["id"]));
                            foreach ($rolePermissions as $readRolePermission) {
                              $rolePermissionList[] = $readRolePermission["permissionID"];
                            }
                          ?>
                          <?php $permissions = $db->query("SELECT * FROM Permissions"); ?>
                          <?php foreach ($permissions as $permission): ?>
                            <div class="col-sm-3">
                              <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="permissions[]" class="custom-control-input" id="permission_<?php echo $permission["id"] ?>" value="<?php echo $permission["id"] ?>" <?php echo in_array($permission["id"], $rolePermissionList) ? "checked" : null; ?>>
                                <label class="custom-control-label" for="permission_<?php echo $permission["id"] ?>"><?php echo $permission["description"] ?></label>
                              </div>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      </div>
                    </div>
                  <?php endif; ?>
                  <?php echo $csrf->input('updateRoles'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <?php if ($readRole["id"] != 1): ?>
                        <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/rol/sil/<?php echo $readRole["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                          <i class="fe fe-trash-2"></i>
                        </a>
                      <?php endif; ?>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateRoles">Değişiklikleri Kaydet</button>
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
      if (get("id") != 1) {
        $deleteRole = $db->prepare("DELETE FROM Roles WHERE id = ?");
        $deleteRole->execute(array(get("id")));
      }
      go("/yonetim-paneli/rol");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>
