<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
  require_once(__ROOT__."/apps/main/private/packages/class/curlpost/curlpost.php");

  $siteURL = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"]);

  function completePayment($accountID = 0, $paymentID = 0, $paymentType = 1, $earnings = 0, $paymentMethodID = 0) {
    global $db, $paymentStatus, $readSettings, $readPaymentSettings;
    // Ödeme işlemdeyse
    if ($paymentStatus == 'IN_PROCESS') {
      $searchPaymentMethod = $db->prepare("SELECT * FROM Payment WHERE id = ?");
      $searchPaymentMethod->execute(array($paymentMethodID));
      $readPaymentMethod = $searchPaymentMethod->fetch();
      if ($searchPaymentMethod->rowCount() > 0) {
        $searchAccount = $db->prepare("SELECT * FROM Accounts WHERE id = ?");
        $searchAccount->execute(array($accountID));
        $readAccount = $searchAccount->fetch();
        if ($searchAccount->rowCount() > 0) {
          $searchPaymentID = $db->prepare("SELECT * FROM CreditHistory WHERE paymentID = ?");
          $searchPaymentID->execute(array($paymentID));
          if ($searchPaymentID->rowCount() == 0) {
            if ($readSettings["creditPackageStatus"] == 0) {
              $credit = $earnings;
              if ($readSettings["bonusCredit"] == 0) {
                if ($readPaymentMethod["bonusCredit"] != 0 && $earnings >= $readPaymentMethod["bonusCreditMinAmount"]) {
                  $credit = floor($earnings*(($readPaymentMethod["bonusCredit"]+100)/100));
                }
              }
              else {
                if ($earnings >= $readSettings["bonusCreditMinAmount"]) {
                  $credit = floor($earnings*(($readSettings["bonusCredit"]+100)/100));
                }
              }
            }
            else {
              $credit = 0;
              $creditPackage = $db->prepare("SELECT * FROM CreditPackages WHERE id = ?");
              $creditPackage->execute(array($earnings));
              $readCreditPackage = $creditPackage->fetch();
              if ($creditPackage->rowCount() > 0) {
                $credit = $readCreditPackage["amount"] + $readCreditPackage["bonus"];
  
                $discountedPriceStatus = ($readCreditPackage["discountedPrice"] != 0 && ($readCreditPackage["discountExpiryDate"] > date("Y-m-d H:i:s") || $readCreditPackage["discountExpiryDate"] == '1000-01-01 00:00:00'));
                $earnings = $discountedPriceStatus ? $readCreditPackage["discountedPrice"] : $readCreditPackage["price"];
                
                if ($readCreditPackage["stock"] != -1) {
                  $updateCreditPackage = $db->prepare("UPDATE CreditPackages SET stock = stock - ? WHERE id = ?");
                  $updateCreditPackage->execute(array(1, $readCreditPackage["id"]));
                }
              }
              else {
                $earnings = 0;
              }
            }
        
            $insertCreditHistory = $db->prepare("INSERT INTO CreditHistory (accountID, paymentID, paymentAPI, paymentStatus, type, earnings, price, creationDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $insertCreditHistory->execute(array($readAccount["id"], $paymentID, $readPaymentSettings["slug"], 1, $paymentType, $earnings/$readSettings["creditMultiplier"], $credit, date("Y-m-d H:i:s")));
        
            $insertNotifications = $db->prepare("INSERT INTO Notifications (accountID, type, variables, creationDate) VALUES (?, ?, ?, ?)");
            $insertNotifications->execute(array($readAccount["id"], 3, $earnings, date("Y-m-d H:i:s")));
        
            $updateAccounts = $db->prepare("UPDATE Accounts SET credit = credit + ? WHERE id = ?");
            $updateAccounts->execute(array($credit, $readAccount["id"]));
        
            if ($readSettings["webhookCreditURL"] != '0') {
              $postFields = array(
                "appKey"    => APP_KEY,
                "type"      => "credit",
                'username'  => $readAccount["realname"],
                'credit'    => $credit,
                'earnings'  => $earnings,
              );
              $curlURL = "$siteURL/apps/main/public/ajax/webhook.php";
              $curlOptions = array(
                CURLOPT_TIMEOUT => 1
              );
              $curl = new \LeaderOS\Http\CurlPost($curlURL, $curlOptions);
              try {
                $curl($postFields);
              } catch (\RuntimeException $ex) {
                //die(sprintf('HTTP hatası: %s Kod: %d', $ex->getMessage(), $ex->getCode()));
              }
            }
        
            if ($readSettings["oneSignalAppID"] != '0' && $readSettings["oneSignalAPIKey"] != '0') {
              $postFields = array(
                "appKey"    => APP_KEY,
                "type"      => "credit",
                'username'  => $readAccount["realname"],
                'credit'    => $credit,
                'earnings'  => $earnings,
              );
              $curlURL = "$siteURL/apps/main/public/ajax/onesignal.php";
              $curlOptions = array(
                CURLOPT_TIMEOUT => 1
              );
              $curl = new \LeaderOS\Http\CurlPost($curlURL, $curlOptions);
              try {
                $curl($postFields);
              } catch (\RuntimeException $ex) {
                //die(sprintf('HTTP hatası: %s Kod: %d', $ex->getMessage(), $ex->getCode()));
              }
            }
        
            $paymentStatus = 'COMPLETED';
        
            if ($readPaymentSettings["slug"] == "shopier" || $readPaymentSettings["slug"] == "weepay") {
              go("/kredi/yukle/basarili");
            }
        
            return true;
          }
          else {
            if ($readSettings["debugModeStatus"] == 1) {
              die("Bu odeme zaten tamamlanmis!");
            }
            else {
              return false;
            }
          }
        }
        else {
          if ($readSettings["debugModeStatus"] == 1) {
            die("Hesap bulunamadi!");
          }
          else {
            return false;
          }
        }
      }
      else {
        die("Hatali odeme methodu ID'si!");
      }
    }
    else {
      if ($readSettings["debugModeStatus"] == 1) {
        die("Odeme isleme alinmamis!");
      }
      else {
        return false;
      }
    }
  }

  if ($_POST && get("api")) {
    $paymentSettings = $db->prepare("SELECT * FROM PaymentSettings WHERE status = ? AND slug = ?");
    $paymentSettings->execute(array(1, get("api")));
    $readPaymentSettings = $paymentSettings->fetch();
    if ($paymentSettings->rowCount() > 0) {
      $paymentStatus = 'READY_FOR_PROCESS';
      $readVariables = json_decode($readPaymentSettings["variables"], true);
      if ($readPaymentSettings["slug"] == 'batihost') {
        if (!empty($readVariables["batihostID"]) && !empty($readVariables["batihostEmail"]) && !empty($readVariables["batihostToken"])) {
          if (get("type") == 1 || get("type") == 2) {
            if (post("guvenlik") == $readVariables["batihostToken"]) {
              $extraData = explode('_', post("user"));
              $paymentStatus = 'IN_PROCESS';
              $paymentID = (string)post("transid");
              $paymentType = (int)get("type");
              $earnings = (int)$extraData[1];
              $accountID = (int)$extraData[0];
              $paymentMethodID = (int)$extraData[2];
            }
            else {
              die("Token uyusmuyor!");
            }
          }
          else {
            die("Odeme tipi hatali!");
          }
        }
        else {
          die("Islem guvenlik kontrolunden gecemedi!");
        }
      }
      else if ($readPaymentSettings["slug"] == 'paywant') {
        if (!empty($readVariables["paywantAPIKey"]) && !empty($readVariables["paywantAPISecretKey"])) {
          $hash = base64_encode(hash_hmac('sha256', post("SiparisID").'|'.post("ExtraData").'|'.post("UserID").'|'.post("ReturnData").'|'.post("Status").'|'.post("OdemeKanali").'|'.post("OdemeTutari").'|'.post("NetKazanc").$readVariables["paywantAPIKey"], $readVariables["paywantAPISecretKey"], true));
          if (post("Hash") == $hash) {
            if (post("Status") == '100') {
              $paymentStatus = 'IN_PROCESS';
              $extraData = post("ExtraData");
              $paymentID = (string)post("SiparisID");
              $paymentType = (int)((int)post("OdemeKanali") == 1 || (int)post("OdemeKanali") == 2) ? (int)post("OdemeKanali") : 2;
              $earnings = (int)$extraData;
              $accountID = (int)post("UserID");
  
              $findPaymentMethodID = $db->prepare("SELECT * FROM Payment WHERE apiID = ? AND type = ? LIMIT 1");
              $findPaymentMethodID->execute(array("paywant", $paymentType));
              $findPaymentMethodID = $findPaymentMethodID->fetch();
              $paymentMethodID = (int)$findPaymentMethodID["id"];
            }
            else {
              // 101: Ödeme İptal Edildi
              die("Odeme iptal edildi!");
            }
          }
          else {
            die("Token hatali!");
          }
        }
        else {
          die("Islem guvenlik kontrolunden gecemedi!");
        }
      }
      else if ($readPaymentSettings["slug"] == 'rabisu') {
        if (!empty($readVariables["rabisuID"]) && !empty($readVariables["rabisuToken"])) {
          if (get("type") == 1 || get("type") == 2) {
            if (post("bayi_token") == $readVariables["rabisuToken"]) {
              $paymentStatus = 'IN_PROCESS';
              $extraData = explode('_', post("oyuncu_adi"));
              $paymentID = (string)post("islemcode");
              $paymentType = (int)get("type");
              $earnings = (int)$extraData[1];
              $accountID = (int)$extraData[0];
              $paymentMethodID = (int)$extraData[2];
            }
            else {
              die("Token hatali!");
            }
          }
          else {
            die("Odeme tipi hatali!");
          }
        }
        else {
          die("Islem guvenlik kontrolunden gecemedi!");
        }
      }
      else if ($readPaymentSettings["slug"] == 'shopier') {
        if (!empty($readVariables["shopierAPIKey"]) && !empty($readVariables["shopierAPISecretKey"])) {
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Enums/Currency.php");
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Enums/Language.php");
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Enums/ProductType.php");
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Enums/WebsiteIndex.php");
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Exceptions/NotRendererClassException.php");
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Exceptions/RendererClassNotFoundException.php");
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Exceptions/RequiredParameterException.php");
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Models/BaseModel.php");
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Models/Address.php");
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Models/BillingAddress.php");
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Models/Buyer.php");
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Models/ShippingAddress.php");
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Models/ShopierParams.php");
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Models/ShopierResponse.php");
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Renderers/AbstractRenderer.php");
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Renderers/FormRenderer.php");
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Renderers/AutoSubmitFormRenderer.php");
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Renderers/ButtonRenderer.php");
          require_once(__ROOT__."/apps/main/private/packages/api/shopier/Shopier.php");

          $shopierResponse = \Shopier\Models\ShopierResponse::fromPostData();
          if ($shopierResponse->hasValidSignature($readVariables["shopierAPISecretKey"])) {
            $paymentStatus = 'IN_PROCESS';
            $extraData = explode('_', post("platform_order_id"));
            $paymentID = (string)post("payment_id");
            $paymentType = 2;
            $earnings = (int)$extraData[1];
            $accountID = (int)$extraData[0];
            $paymentMethodID = (int)$extraData[2];
          }
          else {
            go("/kredi/yukle/basarisiz");
          }
        }
        else {
          die("Islem guvenlik kontrolunden gecemedi!");
        }
      }
      else if ($readPaymentSettings["slug"] == 'keyubu') {
        if (!empty($readVariables["keyubuID"]) && !empty($readVariables["keyubuToken"])) {
          if (post("token") == $readVariables["keyubuToken"]) {
            if (post("status") == 'success') {
              $paymentStatus = 'IN_PROCESS';
              $extraData = explode('_', post("return_id"));
              $paymentID = (string)post("trans_id");
              $paymentType = (int)(((int)post("method") == 1) ? 2 : (((int)post("method") == 2) ? 1 : 2));
              $earnings = (int)$extraData[1];
              $accountID = (int)$extraData[0];
              $paymentMethodID = (int)$extraData[2];
            }
            else {
              die("Odeme basarisiz!");
            }
          }
          else {
            die("Token hatali!");
          }
        }
        else {
          die("Islem guvenlik kontrolunden gecemedi!");
        }
      }
      else if ($readPaymentSettings["slug"] == 'shipy') {
        if (!empty($readVariables["shipyAPIKey"])) {
          $allowedIps = json_decode(file_get_contents('https://shipy.net/security/whitelist'), true);
          if (in_array(getIP(), $allowedIps)) {
            $hashtr = post("paymentID").post("returnID").post("paymentType").post("paymentAmount").post("paymentCurrency").$readVariables["shipyAPIKey"];
            $hashbytes = mb_convert_encoding($hashtr, "ISO-8859-9");
            $hash = base64_encode(sha1($hashbytes, true));
            if (post("paymentHash") == $hash) {
              $paymentStatus = 'IN_PROCESS';
              $extraData = explode('_', post("returnID"));
              $paymentID = (string)post("paymentID");
              $paymentType = (int)(post("paymentType") == 'mobile') ? '1' : '2';
              $earnings = (int)$extraData[1];
              $accountID = (int)$extraData[0];
              $paymentMethodID = (int)$extraData[2];
            }
            else {
              die("Islem guvenlik kontrolunden gecemedi!");
            }
          }
          else {
            die("IP adresi hatali!");
          }
        }
        else {
          die("Islem guvenlik kontrolunden gecemedi!");
        }
      }
      else if ($readPaymentSettings["slug"] == 'slimmweb') {
        /*if (!empty($readVariables["slimmwebPaymentID"]) && !empty($readVariables["slimmwebToken"])) {
          require_once(__ROOT__."/apps/main/private/packages/class/curlpost/curlpost.php");
          $curlURL = 'https://musteri.slimmweb.com/pay/control.php';
          $postFields = array(
            'odemeID' => $readVariables["slimmwebPaymentID"],
            'token'   => $readVariables["slimmwebToken"]
          );
          $curl = new \LeaderOS\Http\CurlPost($curlURL);
          try {
            $result = json_decode($curl($postFields), true);
            if (count($result)) {
              foreach ($result as $readResult) {
                $paymentStatus = 'IN_PROCESS';
                $extraData = explode('_', $readResult["return_id"]);
                $paymentID = (string)$extraData[2];
                $paymentType = 1;
                $earnings = (int)$extraData[1];
                $accountID = (int)$extraData[0];
                completePayment($accountID, $paymentID, $paymentType, $earnings);
              }
            }
          } catch (\RuntimeException $ex) {
            die(sprintf('HTTP hatası: %s Kod: %d', $ex->getMessage(), $ex->getCode()));
          }
        }
        else {
          die("Islem guvenlik kontrolunden gecemedi!");
        }*/
        die("Slimmweb ödeme sistemi şuanda kullanım dışıdır.");
      }
      else if ($readPaymentSettings["slug"] == 'paytr') {
        if (!empty($readVariables["paytrID"]) && !empty($readVariables["paytrAPIKey"]) && !empty($readVariables["paytrAPISecretKey"])) {
          if (isset($_POST["status"]) && isset($_POST["merchant_oid"]) && isset($_POST["total_amount"])) {
            $hash = base64_encode(hash_hmac('SHA256', post("merchant_oid").$readVariables["paytrAPISecretKey"].post("status").post("total_amount"), $readVariables["paytrAPIKey"], true));
            if (post("hash") == $hash) {
              if (post("status") == "success") {
                $paymentStatus = 'IN_PROCESS';
                $extraData = explode('i', post("merchant_oid"));
                $paymentID = (string)$extraData[2];
                $paymentType = 2;
                $earnings = (int)$extraData[1];
                $accountID = (int)$extraData[0];
                $paymentMethodID = (int)$extraData[3];
              }
            }
            else {
              die("Islem guvenlik kontrolunden gecemedi!");
            }
          }
          else {
            die("Gerekli degerler gelmedi!");
          }
        }
        else {
          die("Islem guvenlik kontrolunden gecemedi!");
        }
      }
      else if ($readPaymentSettings["slug"] == 'paylith') {
        /*if (!empty($readVariables["paylithAPIKey"]) && !empty($readVariables["paylithAPISecretKey"])) {
          $conversationId = post("conversationId");
          $orderId = post("orderId");
          $paymentAmount = post("paymentAmount");
          $status = post("status");
          $userId = post("userId");

          $hash = hash_hmac('md5', hash_hmac('sha256', "$conversationId|$orderId|$paymentAmount|$status|$userId".$readVariables["paylithAPISecretKey"], $readVariables["paylithAPIKey"]), $readVariables["paylithAPIKey"]);
          if (post("hash") == $hash) {
            if ($status == "SUCCESS") {
              $paymentStatus = 'IN_PROCESS';
              $extraData = explode('_', $conversationId);
              $paymentID = (string)$orderId;
              $paymentType = 2;
              $earnings = (int)$extraData[1];
              $accountID = (int)$userId;
            }
          }
          else {
            die("Islem guvenlik kontrolunden gecemedi!");
          }
        }
        else {
          die("Islem guvenlik kontrolunden gecemedi!");
        }*/
        die("Paylith ödeme sistemi şuanda kullanım dışıdır.");
      }
      else if ($readPaymentSettings["slug"] == 'paymax') {
        if (isset($_POST['status']) && isset($_POST['paymentStatus']) && isset($_POST['hash']) && isset($_POST['paymentCurrency']) && isset($_POST['paymentAmount']) && isset($_POST['paymentType']) && isset($_POST['orderId']) && isset($_POST['shopCode']) && isset($_POST['orderPrice']) && isset($_POST['productsTotalPrice']) && isset($_POST['productType']) && isset($_POST['callbackOkUrl']) && isset($_POST['callbackFailUrl'])) {
          $hash_string = post("orderId").post("paymentCurrency").post("orderPrice").post("productsTotalPrice").post("productType").$readVariables["paymaxStoreCode"].$readVariables["paymaxHash"];
          $MY_HASH = base64_encode(pack('H*', sha1($hash_string)));
          if ($MY_HASH === post("hash")) {
            if (post("paymentStatus") == 'paymentOk') {
              $paymentStatus = 'IN_PROCESS';
              $extraData = explode('_', post("conversationId"));
              $paymentID = (string)post("orderId");
              $paymentType = post("paymentType") == 'MOBIL_ODEME' ? 1 : 2;
              $earnings = (int)$extraData[1];
              $accountID = (int)$extraData[0];
              $paymentMethodID = (int)$extraData[2];
            }
            else {
              die("Odeme basarisiz!");
            }
          }
          else {
            die("Token hatali!");
          }
        }
        else {
          die("Islem guvenlik kontrolunden gecemedi!");
        }
      }
      else if ($readPaymentSettings["slug"] == 'weepay') {
        if (isset($_POST["paymentStatus"]) && isset($_POST["paymentId"])) {
          if (post("paymentStatus") == true) {
            require_once(__ROOT__."/apps/main/private/packages/api/weepay/weepayBootstrap.php");
            weepayBootstrap::initialize();
            
            $request = new \weepay\Request\GetPaymentRequest();
            $request->setPaymentId(post("paymentId"));
            $request->setLocale(\weepay\Model\Locale::TR);
  
            $options = new \weepay\Auth();
            $options->setBayiID($readVariables["weepayID"]);
            $options->setApiKey($readVariables["weepayAPIKey"]);
            $options->setSecretKey($readVariables["weepayAPISecretKey"]);
            $options->setBaseUrl("https://api.weepay.co");
            
            $getPaymentRequest = \weepay\Model\GetPaymentRequestInitialize::create($request, $options);
            if ($getPaymentRequest->getStatus() == 'success' && $getPaymentRequest->getPaymentStatus() == 'SUCCESS') {
              $paymentStatus = 'IN_PROCESS';
              $extraData = explode('_', post("orderId"));
              $paymentID = (string)post("paymentId");
              $paymentType = 2;
              $earnings = (int)$extraData[1];
              $accountID = (int)$extraData[0];
              $paymentMethodID = (int)$extraData[2];
            }
            else {
              go("/kredi/yukle/basarisiz");
            }
          }
          else {
            go("/kredi/yukle/basarisiz");
          }
        }
        else {
          go("/kredi/yukle/basarisiz");
        }
      }
      else {
        die("Odeme yonetmi hatali!");
      }

      // Ödemeyi tamamla
      completePayment($accountID, $paymentID, $paymentType, $earnings, $paymentMethodID);
      die("OK");
    }
    else {
      die("Odeme yontemi bulunamadi veya devre disi!");
    }
  }
  else {
    die("POST verisi bulunamadi!");
  }
