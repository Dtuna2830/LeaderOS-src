<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
  require_once(__ROOT__."/apps/main/private/packages/class/onesignal/onesignal.php");

  if ($_POST) {
    if (post("appKey") == APP_KEY) {
      if (post("type") == "credit") {
        if (isset($_POST["username"]) && isset($_POST["credit"]) && isset($_POST["earnings"])) {
          $username = post("username");
          $credit = post("credit");
          $earnings = post("earnings");
          $superAdminPermission = $db->prepare("SELECT * FROM Permissions WHERE name = ?");
          $superAdminPermission->execute(array("SUPER_ADMIN"));
          $readSuperAdminPermission = $superAdminPermission->fetch();
          if ($superAdminPermission->rowCount() > 0) {
            $adminAccounts = $db->prepare("SELECT AOSI.oneSignalID FROM Accounts A INNER JOIN AccountOneSignalInfo AOSI ON A.id = AOSI.accountID LEFT JOIN AccountRoles AR ON AR.accountID = A.id INNER JOIN Roles R ON AR.roleID = R.id INNER JOIN RolePermissions RP ON RP.roleID = R.id LEFT JOIN AccountPermissions AP ON AP.accountID = A.id WHERE AP.permissionID = :perm OR RP.permissionID = :perm GROUP BY A.id");
            $adminAccounts->execute(array(
              'perm' => $readSuperAdminPermission["id"],
            ));
            if ($adminAccounts->rowCount() > 0) {
              $oneSignalIDList = array();
              foreach ($adminAccounts as $readAdminAccounts) {
                array_push($oneSignalIDList, $readAdminAccounts["oneSignalID"]);
              }
              $oneSignal = new OneSignal($readSettings["oneSignalAppID"], $readSettings["oneSignalAPIKey"], $oneSignalIDList);
              $oneSignal->sendMessage('LeaderOS Bildirim', $username.' adlı kullanıcı '.$credit.' ('.$earnings.' TL) kredi yükledi.', '/yonetim-paneli/magaza/kredi-gecmisi');
            }
          }
        }
        else {
          die("Gerekli degerler gelmedi!");
        }
      }
      else {
        die("Gecersiz webhook tipi girildi!");
      }
    }
    else {
      die("Guvenlik saglanamadi!");
    }
  }
  else {
    die("POST verisi bulunamadi!");
  }

?>
