<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/api/includes/config.php");
  require_once(__ROOT__."/apps/main/private/config/app.php");
  
  if (post("token") == null || post("ip") == null || post("username") == null || post("password") == null) {
    die(json_encode([
      'status' => false,
      'message' => "Gerekli parametreler gelmedi! (token, ip, username, password)"
    ]));
  }
  else {
    if (APP_KEY == post("token")) {
      $settings = $db->query("SELECT * FROM Settings ORDER BY id DESC LIMIT 1");
      $readSettings = $settings->fetch();
      if ($readSettings["authApi"] == 1) {
        $login = $db->prepare("SELECT * FROM Accounts WHERE realname = ?");
        $login->execute(array(post("username")));
        $readAccount = $login->fetch();
        if ($login->rowCount() > 0) {
          if ($readSettings["passwordType"] == 1)
            $password = checkSHA256(post("password"), $readAccount["password"]);
          elseif ($readSettings["passwordType"] == 2)
            $password = md5(post("password")) == $readAccount["password"];
          else
            $password = password_verify(post("password"), $readAccount["password"]);
          if ($password == true) {
            if ($readSettings["maintenanceStatus"] == 1 && ($readAccount["permission"] == 0 || $readAccount["permission"] == 6)) {
              die(json_encode([
                'status' => false,
                'message' => "Bakım modu aktif!"
              ]));
            }
            else {
              if ($readSettings["authStatus"] == 1 && $readAccount["authStatus"] == 1) {
                die(json_encode([
                  'status' => false,
                  'message' => "İki adımlı doğrulama aktif!"
                ]));
              }
              else {
                //$loginToken = md5(uniqid(mt_rand(), true));
                //$insertAccountSessions = $db->prepare("INSERT INTO AccountSessions (accountID, loginToken, creationIP, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?)");
                //$insertAccountSessions->execute(array($readAccount["id"], $loginToken, post("ip"), createDuration(365), date("Y-m-d H:i:s")));
                die(json_encode([
                  'status' => true,
                  //'token'  => $loginToken,
                ]));
              }
            }
          }
          else {
            die(json_encode([
              'status' => false,
              'message' => "Şifre hatalı!"
            ]));
          }
        }
        else {
          die(json_encode([
            'status' => false,
            'message' => "Kullanıcı bulunamadı!"
          ]));
        }
      }
      else {
        die(json_encode([
          'status' => false,
          'message' => "Auth API kapalı!"
        ]));
      }
    }
    else {
      die(json_encode([
        'status' => false,
        'message' => "Token geçersiz!"
      ]));
    }
  }