<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/api/includes/config.php");
  require_once(__ROOT__."/apps/main/private/config/app.php");
  
  if (post("token") == null || post("ip") == null || post("username") == null || post("email") == null || post("password") == null) {
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
        if ($readSettings["maintenanceStatus"] == 1) {
          $registerLimit  = $readSettings["registerLimit"];
  
          $usernameValid = $db->prepare("SELECT * FROM Accounts WHERE realname = ?");
          $usernameValid->execute(array(post("username")));
  
          $emailValid = $db->prepare("SELECT * FROM Accounts WHERE email = ?");
          $emailValid->execute(array(post("email")));
  
          $ipCount = $db->prepare("SELECT * FROM Accounts WHERE creationIP = ?");
          $ipCount->execute(array(getIP()));
  
          if ($registerLimit != 0 && $ipCount->rowCount() >= $registerLimit) {
            die(json_encode([
              'status' => false,
              'message' => "Bu IP adresinden daha fazla kayıt oluşturamazsın!"
            ]));
          }
          else if (checkUsername(post("username"))) {
            die(json_encode([
              'status' => false,
              'message' => "Girdiğiniz kullanıcı adı uygun olmayan karakter içeriyor!"
            ]));
          }
          else if (strlen(post("username")) < 3) {
            die(json_encode([
              'status' => false,
              'message' => "Kullanıcı adı 3 karakterden az olamaz!"
            ]));
          }
          else if (strlen(post("username")) > 16) {
            die(json_encode([
              'status' => false,
              'message' => "Kullanıcı adı 16 karakterden fazla olamaz!"
            ]));
          }
          else if (checkEmail(post("email"))) {
            die(json_encode([
              'status' => false,
              'message' => "Geçerli bir email adresi giriniz!"
            ]));
          }
          else if ($usernameValid->rowCount() > 0) {
            die(json_encode([
              'status' => false,
              'message' => "Bu kullanıcı adı başkası tarafından kullanılıyor!"
            ]));
          }
          else if ($emailValid->rowCount() > 0) {
            die(json_encode([
              'status' => false,
              'message' => "Bu email adresi başkası tarafından kullanılıyor!"
            ]));
          }
          else if (strlen(post("password")) < 4) {
            die(json_encode([
              'status' => false,
              'message' => "Şifre 4 karakterden az olamaz!"
            ]));
          }
          else {
            $loginToken = md5(uniqid(mt_rand(), true));
            if ($readSettings["passwordType"] == 1)
              $password = createSHA256(post("password"));
            elseif ($readSettings["passwordType"] == 2)
              $password = md5(post("password"));
            else
              $password = password_hash(post("password"), PASSWORD_BCRYPT);
            $insertAccounts = $db->prepare("INSERT INTO Accounts (username, realname, email, password, creationIP, creationDate) VALUES (?, ?, ?, ?, ?, ?)");
            $insertAccounts->execute(array(strtolower(post("username")), post("username"), post("email"), $password, post("ip"), date("Y-m-d H:i:s")));
            $accountID = $db->lastInsertId();
            //$insertAccountSessions = $db->prepare("INSERT INTO AccountSessions (accountID, loginToken, creationIP, expiryDate, creationDate) VALUES (?, ?, ?, ?, ?)");
            //$insertAccountSessions->execute(array($accountID, $loginToken, getIP(), createDuration(0.01666666666), date("Y-m-d H:i:s")));
            //$_SESSION["login"] = $loginToken;
            die(json_encode([
              'status' => true,
              'accountID' => $accountID,
              //'token' => $loginToken,
            ]));
          }
        } else {
          die(json_encode([
            'status' => false,
            'message' => "Bakım modu aktif!"
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