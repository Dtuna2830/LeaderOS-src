<?php
  if (!checkPerm($readAdmin, 'MANAGE_APPLICATIONS')) {
    go('/yonetim-paneli/hata/001');
  }
  require_once(__ROOT__.'/apps/dashboard/private/packages/class/extraresources/extraresources.php');
  $extraResourcesJS = new ExtraResources('js');
  $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/loader.js');
  
  if (get("target") == 'form' && (get("action") == 'insert' || get("action") == 'update')) {
    $extraResourcesJS->addResource('/apps/dashboard/public/assets/js/application.form.js');
  }
?>
<?php if (get("target") == 'form'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Başvuru Formları</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Başvuru Formları</li>
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
          <?php $forms = $db->query("SELECT * FROM ApplicationForms ORDER BY id DESC"); ?>
          <?php if ($forms->rowCount() > 0): ?>
            <div class="card" data-toggle="lists" data-lists-values='["formID", "formTitle", "formCreationDate"]'>
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <div class="row align-items-center">
                      <div class="col-auto pr-0">
                        <span class="fe fe-search text-muted"></span>
                      </div>
                      <div class="col">
                        <input type="search" class="form-control form-control-flush search" name="search" placeholder="Ara">
                      </div>
                    </div>
                  </div>
                  <div class="col-auto">
                    <a class="btn btn-sm btn-white" href="/yonetim-paneli/yetkili-basvuru/form/ekle">Başvuru Formu Ekle</a>
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
                        <a href="#" class="text-muted sort" data-sort="formID">
                          #ID
                        </a>
                      </th>
                      <th>
                        <a href="#" class="text-muted sort" data-sort="formTitle">
                          Başlık
                        </a>
                      </th>
                      <th>
                        <a href="#" class="text-muted sort" data-sort="formCreationDate">
                          Oluşturma Tarihi
                        </a>
                      </th>
                      <th class="text-right">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    <?php foreach ($forms as $readForms): ?>
                      <tr>
                        <td class="formID text-center" style="width: 40px;">
                          <a href="/yonetim-paneli/yetkili-basvuru/form/duzenle/<?php echo $readForms["id"]; ?>">
                            #<?php echo $readForms["id"]; ?>
                          </a>
                        </td>
                        <td class="formTitle">
                          <a href="/yonetim-paneli/yetkili-basvuru/form/duzenle/<?php echo $readForms["id"]; ?>">
                            <?php echo $readForms["title"]; ?>
                          </a>
                        </td>
                        <td class="formCreationDate">
                          <?php echo convertTime($readForms["creationDate"], 2, true); ?>
                        </td>
                        <td class="text-right">
                          <a class="btn btn-sm btn-rounded-circle btn-success" href="/yonetim-paneli/yetkili-basvuru/form/duzenle/<?php echo $readForms["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Düzenle">
                            <i class="fe fe-edit-2"></i>
                          </a>
                          <a class="btn btn-sm btn-rounded-circle btn-primary" href="/yetkili-basvuru/form/<?php echo $readForms["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                            <i class="fe fe-eye"></i>
                          </a>
                          <a class="btn btn-sm btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/yetkili-basvuru/form/sil/<?php echo $readForms["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
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
                  <h2 class="header-title">Başvuru Formu Ekle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/yetkili-basvuru/form">Başvuru Formları</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Başvuru Formu Ekle</li>
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
            if (isset($_POST["insertForms"])) {
              if (!$csrf->validate('insertForms')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("title") == null || post("description") == null) {
                echo alertError("Lütfen boş alan bırakmayınız!");
              }
              else {
                $insertForms = $db->prepare("INSERT INTO ApplicationForms (title, slug, description, reappliable, creationDate) VALUES (?, ?, ?, ?, ?)");
                $insertForms->execute(array(post("title"), convertURL(post("title")), filteredContent($_POST["description"]), post("reappliable"), date("Y-m-d H:i:s")));
                
                $formID = $db->lastInsertId();
                foreach ($_POST["formQuestion"] as $key => $value) {
                  if ($_POST["formQuestion"][$key] == '') continue;
                  $_POST["formQuestion"][$key] = strip_tags($_POST["formQuestion"][$key]);
                  $_POST["formQuestionType"][$key] = strip_tags($_POST["formQuestionType"][$key]);
                  $_POST["formQuestionVariables"][$key] = ($_POST["formQuestionVariables"][$key] != null) ? strip_tags($_POST["formQuestionVariables"][$key]) : '-';
                  $insertFormQuestions = $db->prepare("INSERT INTO ApplicationFormQuestions (formID, question, type, variables) VALUES (?, ?, ?, ?)");
                  $insertFormQuestions->execute(array($formID, $_POST["formQuestion"][$key], $_POST["formQuestionType"][$key], $_POST["formQuestionVariables"][$key]));
                }
                
                echo alertSuccess("Başvuru Formu başarıyla eklendi!");
              }
            }
          ?>
          <div class="card">
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group row">
                  <label for="inputTitle" class="col-sm-2 col-form-label">Başlık:</label>
                  <div class="col-sm-10">
                    <input type="text" id="inputTitle" class="form-control" name="title" placeholder="Başlık girin.">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="textareaContent" class="col-sm-2 col-form-label">Açıklama:</label>
                  <div class="col-sm-10">
                    <textarea id="textareaContent" class="form-control" name="description" placeholder="Açıklama girin."></textarea>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="selectReapply" class="col-sm-2 col-form-label">Yeniden Uygulanabilir:</label>
                  <div class="col-sm-10">
                    <select id="selectReapply" class="form-control" name="reappliable" data-toggle="select" data-minimum-results-for-search="-1">
                      <option value="1">Aktif</option>
                      <option value="0">Devre dışı</option>
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-sm-12">
                    <span>Sorular:</span>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-sm-12">
                    <div class="table-responsive">
                      <table id="tableitems" class="table table-sm table-nowrap array-table">
                        <thead>
                        <tr>
                          <th>Sorular</th>
                          <th>Tip</th>
                          <th>Değişkenler</th>
                          <th class="text-center pt-0 pb-0 align-middle">
                            <button type="button" class="btn btn-sm btn-rounded-circle btn-success addTableItem">
                              <i class="fe fe-plus"></i>
                            </button>
                          </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="d-none">
                          <td>
                            <textarea rows="2" class="form-control" name="formQuestion[]" placeholder="Soru girin"></textarea>
                          </td>
                          <td>
                            <select class="form-control" name="formQuestionType[]">
                              <option value="1">Text</option>
                              <option value="2">Uzun Text</option>
                              <option value="3">Select</option>
                              <option value="4">Multi-Select</option>
                            </select>
                          </td>
                          <td class="variableData">
                            <div class="selectData" style="display: none;">
                              <input type="text" class="form-control" name="formQuestionVariables[]"  placeholder="Değişkenleri virgül ile ayırabilirsiniz.">
                            </div>
                            <div class="textData" style="margin: .5rem 0;">
                              <span>-</span>
                              <input type="hidden" name="formQuestionVariables[]" value="-" disabled>
                            </div>
                          </td>
                          <td class="text-center align-middle">
                            <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                              <i class="fe fe-trash-2"></i>
                            </button>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <textarea rows="2" type="text" class="form-control" name="formQuestion[]" placeholder="Soru girin"></textarea>
                          </td>
                          <td>
                            <select class="form-control" name="formQuestionType[]">
                              <option value="1">Text</option>
                              <option value="2">Uzun Text</option>
                              <option value="3">Select</option>
                              <option value="4">Multi-Select</option>
                            </select>
                          </td>
                          <td class="variableData">
                            <div class="selectData" style="display: none;">
                              <input type="text" class="form-control" name="formQuestionVariables[]" placeholder="Değişkenleri virgül ile ayırabilirsiniz.">
                            </div>
                            <div class="textData" style="margin: .5rem 0;">
                              <span>-</span>
                              <input type="hidden" name="formQuestionVariables[]" value="-" disabled>
                            </div>
                          </td>
                          <td class="text-center align-middle">
                            <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                              <i class="fe fe-trash-2"></i>
                            </button>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <?php echo $csrf->input('insertForms'); ?>
                <div class="clearfix">
                  <div class="float-right">
                    <button type="submit" class="btn btn-rounded btn-success" name="insertForms">Ekle</button>
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
    $form = $db->prepare("SELECT * FROM ApplicationForms WHERE id = ?");
    $form->execute(array(get("id")));
    $readForm = $form->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Başvuru Formunu Düzenle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/yetkili-basvuru/form">Başvuru Formları</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/yetkili-basvuru/form">Başvuru Formunu Düzenle</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($form->rowCount() > 0) ? limitedContent($readForm["title"], 30): "Bulunamadı!"; ?></li>
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
          <?php if ($form->rowCount() > 0): ?>
            <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["updateForms"])) {
              if (!$csrf->validate('updateForms')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else if (post("title") == null || post("description") == null) {
                echo alertError("Lütfen tüm alanları doldurun.");
              }
              else {
                $updateForms = $db->prepare("UPDATE ApplicationForms SET title = ?, slug = ?, description = ?, reappliable = ? WHERE id = ?");
                $updateForms->execute(array(post("title"), convertURL(post("title")), filteredContent($_POST["description"]), post("reappliable"), get("id")));
                
                $formID = $readForm["id"];
                $disableOldQuestions = $db->prepare("UPDATE ApplicationFormQuestions SET isEnabled = ? WHERE formID = ?");
                $disableOldQuestions->execute(array(0, $formID));
                foreach ($_POST["formQuestion"] as $key => $value) {
                  if ($_POST["formQuestion"][$key] == '') continue;
                  $_POST["formQuestion"][$key] = strip_tags($_POST["formQuestion"][$key]);
                  $_POST["formQuestionType"][$key] = strip_tags($_POST["formQuestionType"][$key]);
                  $_POST["formQuestionVariables"][$key] = ($_POST["formQuestionVariables"][$key] != null) ? strip_tags($_POST["formQuestionVariables"][$key]) : '-';
                  $insertFormQuestions = $db->prepare("INSERT INTO ApplicationFormQuestions (formID, question, type, variables) VALUES (?, ?, ?, ?)");
                  $insertFormQuestions->execute(array($formID, $_POST["formQuestion"][$key], $_POST["formQuestionType"][$key], $_POST["formQuestionVariables"][$key]));
                }
                
                echo alertSuccess("Değişiklikler başarıyla kaydedildi!");
              }
            }
            ?>
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <div class="form-group row">
                    <label for="inputTitle" class="col-sm-2 col-form-label">Başlık:</label>
                    <div class="col-sm-10">
                      <input type="text" id="inputTitle" class="form-control" name="title" placeholder="Başlık girin." value="<?php echo $readForm["title"]; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="textareaContent" class="col-sm-2 col-form-label">Açıklama:</label>
                    <div class="col-sm-10">
                      <textarea id="textareaContent" class="form-control" name="description" placeholder="Açıklama girin."><?php echo $readForm["description"]; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="selectReapply" class="col-sm-2 col-form-label">Yeniden Uygulanabilir:</label>
                    <div class="col-sm-10">
                      <select id="selectReapply" class="form-control" name="reappliable" data-toggle="select" data-minimum-results-for-search="-1">
                        <option value="0" <?php echo ($readForm["reappliable"] == 0) ? 'selected="selected"' : null; ?>>Devre dışı</option>
                        <option value="1" <?php echo ($readForm["reappliable"] == 1) ? 'selected="selected"' : null; ?>>Aktif</option>
                      </select>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-sm-12">
                      <span>Sorular:</span>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-12">
                      <div class="table-responsive">
                        <table id="tableitems" class="table table-sm table-nowrap array-table">
                          <thead>
                          <tr>
                            <th>Soru</th>
                            <th>Tip</th>
                            <th>Değişkenler</th>
                            <th class="text-center pt-0 pb-0 align-middle">
                              <button type="button" class="btn btn-sm btn-rounded-circle btn-success addTableItem">
                                <i class="fe fe-plus"></i>
                              </button>
                            </th>
                          </tr>
                          </thead>
                          <tbody>
                          <tr class="d-none">
                            <td>
                              <textarea rows="2" class="form-control" name="formQuestion[]" placeholder="Soru girin"></textarea>
                            </td>
                            <td>
                              <select class="form-control" name="formQuestionType[]">
                                <option value="1">Text</option>
                                <option value="2">Uzun Text</option>
                                <option value="3">Select</option>
                                <option value="4">Multi-Select</option>
                              </select>
                            </td>
                            <td class="variableData">
                              <div class="selectData" style="display: none;">
                                <input type="text" class="form-control" name="formQuestionVariables[]"  placeholder="Değişkenleri virgül ile ayırabilirsin.">
                              </div>
                              <div class="textData" style="margin: .5rem 0;">
                                <span>-</span>
                                <input type="hidden" name="formQuestionVariables[]" value="-" disabled>
                              </div>
                            </td>
                            <td class="text-center align-middle">
                              <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                <i class="fe fe-trash-2"></i>
                              </button>
                            </td>
                          </tr>
                          <?php
                            $questions = $db->prepare("SELECT * FROM ApplicationFormQuestions WHERE formId = ? AND isEnabled = ?");
                            $questions->execute(array($readForm["id"], 1));
                          ?>
                          <?php foreach($questions as $readQuestion): ?>
                            <tr>
                              <td>
                                <textarea rows="2" type="text" class="form-control" name="formQuestion[]" placeholder="Soru girin"><?php echo $readQuestion["question"] ?></textarea>
                              </td>
                              <td>
                                <select class="form-control" name="formQuestionType[]">
                                  <option value="1" <?php echo ($readQuestion["type"] == 1) ? "selected" : null; ?>>Text</option>
                                  <option value="2" <?php echo ($readQuestion["type"] == 2) ? "selected" : null; ?>>Uzun Text</option>
                                  <option value="3" <?php echo ($readQuestion["type"] == 3) ? "selected" : null; ?>>Select</option>
                                  <option value="4" <?php echo ($readQuestion["type"] == 4) ? "selected" : null; ?>>Multi-Select</option>
                                </select>
                              </td>
                              <td class="variableData">
                                <div class="selectData" style="display: <?php echo ($readQuestion["type"] == 1 || $readQuestion["type"] == 2) ? "none" : "block" ?>;">
                                  <input type="text" class="form-control" name="formQuestionVariables[]" placeholder="Değişkenleri virgül ile ayırabilirsin." value="<?php echo ($readQuestion["variables"] != '-') ? $readQuestion["variables"] : null; ?>">
                                </div>
                                <div class="textData" style="display: <?php echo ($readQuestion["type"] == 1 || $readQuestion["type"] == 2) ? "block" : "none" ?>; margin: .5rem 0;">
                                  <span>-</span>
                                  <input type="hidden" name="formQuestionVariables[]" value="-" disabled>
                                </div>
                              </td>
                              <td class="text-center align-middle">
                                <button type="button" class="btn btn-sm btn-rounded-circle btn-danger deleteTableItem">
                                  <i class="fe fe-trash-2"></i>
                                </button>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <?php echo $csrf->input('updateForms'); ?>
                  <div class="clearfix">
                    <div class="float-right">
                      <a class="btn btn-rounded-circle btn-danger clickdelete" href="/yonetim-paneli/yetkili-basvuru/form/sil/<?php echo $readForm["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Sil">
                        <i class="fe fe-trash-2"></i>
                      </a>
                      <a class="btn btn-rounded-circle btn-primary" href="/yetkili-basvuru/form/<?php echo $readForm["slug"]; ?>" rel="external" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                        <i class="fe fe-eye"></i>
                      </a>
                      <button type="submit" class="btn btn-rounded btn-success" name="updateForms">Değişiklikleri Kaydet</button>
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
    $deleteForm = $db->prepare("DELETE FROM ApplicationForms WHERE id = ?");
    $deleteForm->execute(array(get("id")));
    go("/yonetim-paneli/yetkili-basvuru/form");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php elseif (get("target") == 'application'): ?>
  <?php if (get("action") == 'getAll'): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Başvurular</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Başvurular</li>
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
            if (isset($_GET["page"])) {
              if (!is_numeric($_GET["page"])) {
                $_GET["page"] = 1;
              }
              $page = intval(get("page"));
            }
            else {
              $page = 1;
            }
            
            $visiblePageCount = 5;
            $limit = 50;
            
            if (get("status") != null) {
              $applications = $db->prepare("SELECT AP.id FROM Applications AP INNER JOIN Accounts A ON A.id = AP.accountID INNER JOIN ApplicationForms AF ON AF.id = AP.formID WHERE AP.status = ?");
              $applications->execute(array(get("status")));
            } else {
              $applications = $db->query("SELECT AP.id FROM Applications AP INNER JOIN Accounts A ON A.id = AP.accountID INNER JOIN ApplicationForms AF ON AF.id = AP.formID");
            }
            $itemsCount = $applications->rowCount();
            $pageCount = ceil($itemsCount/$limit);
            if ($page > $pageCount) {
              $page = 1;
            }
            $visibleItemsCount = $page * $limit - $limit;
            if (get("status") != null) {
              $applications = $db->prepare("SELECT AP.*, A.realname, AF.title FROM Applications AP INNER JOIN Accounts A ON A.id = AP.accountID INNER JOIN ApplicationForms AF ON AF.id = AP.formID WHERE AP.status = ? ORDER BY AP.id DESC LIMIT $visibleItemsCount, $limit");
              $applications->execute(array(get("status")));
            } else {
              $applications = $db->query("SELECT AP.*, A.realname, AF.title FROM Applications AP INNER JOIN Accounts A ON A.id = AP.accountID INNER JOIN ApplicationForms AF ON AF.id = AP.formID ORDER BY AP.id DESC LIMIT $visibleItemsCount, $limit");
            }
            
            
            if (isset($_POST["query"])) {
              if (post("query") != null) {
                $applications = $db->prepare("SELECT AP.*, A.realname, AF.title FROM Applications AP INNER JOIN Accounts A ON A.id = AP.accountID INNER JOIN ApplicationForms AF ON AF.id = AP.formID WHERE A.realname LIKE :search OR AF.title LIKE :search ORDER BY AP.id DESC");
                $applications->execute(array(
                  "search" => '%'.post("query").'%'
                ));
              }
            }
          ?>
          <?php if ($applications->rowCount() > 0): ?>
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
                          <input type="search" class="form-control form-control-flush search" name="query" placeholder="Ara (Kullanıcı Adı, Form Başlığı)" value="<?php echo (isset($_POST["query"])) ? post("query"): null; ?>">
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
                  <table class="table table-sm table-nowrap card-table">
                    <thead>
                    <tr>
                      <th class="text-center" style="width: 40px;">#ID</th>
                      <th>Kullanıcı Adı</th>
                      <th>Form</th>
                      <th class="text-center">Durum</th>
                      <th>Tarih</th>
                      <th class="text-right">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    <?php foreach ($applications as $readApplications): ?>
                      <tr>
                        <td class="text-center" style="width: 40px;">
                          #<?php echo $readApplications["id"]; ?>
                        </td>
                        <td>
                          <a href="/yonetim-paneli/hesap/goruntule/<?php echo $readApplications["accountID"]; ?>">
                            <?php echo $readApplications["realname"]; ?>
                          </a>
                        </td>
                        <td>
                          <?php echo $readApplications["title"]; ?>
                        </td>
                        <td class="text-center">
                          <?php if ($readApplications["status"] == 0): ?>
                            <span class="badge badge-pill badge-danger">Reddedildi</span>
                          <?php elseif ($readApplications["status"] == 1): ?>
                            <span class="badge badge-pill badge-success">Onaylandı</span>
                          <?php elseif ($readApplications["status"] == 2): ?>
                            <span class="badge badge-pill badge-warning">Onay Bekliyor</span>
                          <?php else: ?>
                            ERROR!
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php echo convertTime($readApplications["creationDate"], 2, true); ?>
                        </td>
                        <td class="text-right">
                          <a class="btn btn-sm btn-rounded-circle btn-primary" href="/yonetim-paneli/yetkili-basvuru/goruntule/<?php echo $readApplications["id"]; ?>" data-toggle="tooltip" data-placement="top" title="Görüntüle">
                            <i class="fe fe-eye"></i>
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
                    <a class="page-link" href="/yonetim-paneli/yetkili-basvuru/<?php echo $page-1; ?>" tabindex="-1" aria-disabled="true"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <?php for ($i = $page - $visiblePageCount; $i < $page + $visiblePageCount + 1; $i++): ?>
                    <?php if ($i > 0 and $i <= $pageCount): ?>
                      <li class="page-item <?php echo (($page == $i) ? "active" : null); ?>">
                        <a class="page-link" href="/yonetim-paneli/yetkili-basvuru/<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endif; ?>
                  <?php endfor; ?>
                  <li class="page-item <?php echo ($page == $pageCount) ? "disabled" : null; ?>">
                    <a class="page-link" href="/yonetim-paneli/yetkili-basvuru/<?php echo $page+1; ?>"><i class="fa fa-angle-right"></i></a>
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
  <?php elseif (get("action") == 'view' && get("id")): ?>
    <?php
    $application = $db->prepare("SELECT AP.*, A.realname, AF.title FROM Applications AP INNER JOIN Accounts A ON A.id = AP.accountID INNER JOIN ApplicationForms AF ON AP.formID = AF.id WHERE AP.id = ?");
    $application->execute(array(get("id")));
    $readApplication = $application->fetch();
    ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="header">
            <div class="header-body">
              <div class="row align-items-center">
                <div class="col">
                  <h2 class="header-title">Başvuruları Görüntüle</h2>
                </div>
                <div class="col-auto">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="/yonetim-paneli">Yönetim Paneli</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/yetkili-basvuru">Başvurular</a></li>
                      <li class="breadcrumb-item"><a href="/yonetim-paneli/yetkili-basvuru">Başvuruları Görüntüle</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo ($application->rowCount() > 0) ? $readApplication["realname"] : "Bulunamadı"; ?></li>
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
          <?php if ($application->rowCount() > 0): ?>
            <?php
            require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
            $csrf = new CSRF('csrf-sessions', 'csrf-token');
            if (isset($_POST["rejectApplication"])) {
              if (!$csrf->validate('updateApplication')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else {
                $updateApplication = $db->prepare("UPDATE Applications SET reason = ?, status = ? WHERE id = ?");
                $updateApplication->execute(array(post("reason"), 0, get("id")));
                echo alertWarning("Başvuru başarıyla reddedildi!");
              }
            }
            if (isset($_POST["approveApplication"])) {
              if (!$csrf->validate('updateApplication')) {
                echo alertError("Sistemsel bir sorun oluştu!");
              }
              else {
                $updateApplication = $db->prepare("UPDATE Applications SET reason = ?, status = ? WHERE id = ?");
                $updateApplication->execute(array(post("reason"), 1, get("id")));
                echo alertSuccess("Başvuru başarıyla onaylandı!");
              }
            }
            ?>
            <div class="row">
              <div class="col-md-3">
                <div class="card">
                  <div class="card-header">
                    Kullanıcı Adı
                  </div>
                  <div class="card-body">
                    <?php echo $readApplication["realname"]; ?>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card">
                  <div class="card-header">
                    Form
                  </div>
                  <div class="card-body">
                    <?php echo $readApplication["title"]; ?>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card">
                  <div class="card-header">
                    Durum
                  </div>
                  <div class="card-body">
                    <?php if ($readApplication["status"] == 0): ?>
                      <span class="badge badge-pill badge-danger">Reddedildi</span>
                    <?php elseif ($readApplication["status"] == 1): ?>
                      <span class="badge badge-pill badge-success">Onaylandı</span>
                    <?php elseif ($readApplication["status"] == 2): ?>
                      <span class="badge badge-pill badge-warning">Onay Bekliyor</span>
                    <?php else: ?>
                      ERROR!
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card">
                  <div class="card-header">
                    Tarih
                  </div>
                  <div class="card-body">
                    <?php echo convertTime($readApplication["creationDate"], 2, true); ?>
                  </div>
                </div>
              </div>
            </div>
            
            <?php
            $answers = $db->prepare("SELECT GROUP_CONCAT(AA.answer) as answer, AFQ.question FROM ApplicationAnswers AA INNER JOIN ApplicationFormQuestions AFQ ON AFQ.id = AA.questionID WHERE AA.applicationID = ? GROUP BY AFQ.id");
            $answers->execute(array($readApplication["id"]));
            ?>
            <?php foreach ($answers as $readAnswer): ?>
              <div class="card">
                <div class="card-header">
                  <?php echo $readAnswer["question"] ?>
                </div>
                <div class="card-body">
                  <?php echo $readAnswer["answer"]; ?>
                </div>
              </div>
            <?php endforeach; ?>
            
            <form action="" method="post">
              <?php echo $csrf->input('updateApplication'); ?>
              <div class="card">
                <div class="card-header">
                  Sebep
                </div>
                <div class="card-body">
                  <textarea class="form-control" name="reason" rows="2"><?php echo $readApplication["reason"]; ?></textarea>
                  <div class="clearfix">
                    <div class="float-right mt-4">
                      <button type="submit" class="btn btn-rounded btn-danger" name="rejectApplication">
                        Reddedildi
                      </button>
                      <button type="submit" class="btn btn-rounded btn-success" name="approveApplication">
                        Onaylandı
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          <?php else: ?>
            <?php echo alertError("Bu sayfaya ait veri bulunamadı!"); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php elseif (get("action") == 'delete' && get("id")): ?>
    <?php
    $deleteApplication = $db->prepare("DELETE FROM Applications WHERE id = ?");
    $deleteApplication->execute(array(get("id")));
    go("/yonetim-paneli/yetkili-basvuru");
    ?>
  <?php else: ?>
    <?php go('/404'); ?>
  <?php endif; ?>
<?php else: ?>
  <?php go('/404'); ?>
<?php endif; ?>