<?php
  define("__ROOT__", $_SERVER["DOCUMENT_ROOT"]);
  require_once(__ROOT__."/apps/main/private/config/settings.php");
  if (isset($_SESSION["login"])) {
    require_once(__ROOT__."/apps/main/private/packages/class/csrf/csrf.php");
    $csrf = new CSRF('csrf-sessions', 'csrf-token');
    if (isset($_POST["chargeCredit"]) && post("paymentID") && post("price")) {
      if ($csrf->validate('chargeCredit')) {
        if (
          ($readAccount["email"] != "your@email.com" && $readAccount["email"] != "guncelle@gmail.com") &&
          (post("firstName") && post("lastName") && post("phoneNumber"))
        ) {
          if (!isset($_POST["creditPackage"]) && (!is_numeric(post("price")) || post("price") <= 0 || (post("price") < $readSettings["minPay"] || post("price") > $readSettings["maxPay"]))) {
            go("/kredi/yukle");
          }
          
          $checkAccountContactInfo = $db->prepare("SELECT * FROM AccountContactInfo WHERE accountID = ?");
          $checkAccountContactInfo->execute(array($readAccount["id"]));
          
          if ($checkAccountContactInfo->rowCount() > 0) {
            $checkAccountContactInfo = $checkAccountContactInfo->fetch();
            foreach ($checkAccountContactInfo as $key => $value) {
              if ($key != "accountID") {
                $updateAccountContactInfo = $db->prepare("UPDATE AccountContactInfo SET $key = :$key WHERE accountID = :accountID AND $key != :$key");
                $updateAccountContactInfo->execute(array(":accountID" => $readAccount["id"], ":$key" => post($key)));
              }
            }
          }
          else {
            $insertAccountContactInfo = $db->prepare("INSERT INTO AccountContactInfo (accountID, firstName, lastName, phoneNumber) VALUES (?, ?, ?, ?)");
            $insertAccountContactInfo->execute(array($readAccount["id"], post("firstName"), post("lastName"), post("phoneNumber")));
          }
          
          $accountContactInfo = $db->prepare("SELECT * FROM AccountContactInfo WHERE accountID = ?");
          $accountContactInfo->execute(array($readAccount["id"]));
          $readAccountContactInfo = $accountContactInfo->fetch();
          $accountFullName = sprintf("%s %s", $readAccountContactInfo["firstName"], $readAccountContactInfo["lastName"]);
          $accountFirstName = $readAccountContactInfo["firstName"];
          $accountLastName = $readAccountContactInfo["lastName"];
          $accountPhoneNumber = $readAccountContactInfo["phoneNumber"];
          
          $payment = $db->prepare("SELECT P.*, PS.slug as apiSlug, PS.variables FROM Payment P INNER JOIN PaymentSettings PS ON P.apiID = PS.slug WHERE PS.status = ? AND P.id = ?");
          $payment->execute(array(1, post("paymentID")));
          $readPayment = $payment->fetch();
          
          $isCreditPackagePayment = false;
          if (isset($_POST["creditPackage"])) {
            $creditPackage = $db->prepare("SELECT * FROM CreditPackages WHERE id = ?");
            $creditPackage->execute(array(post("creditPackage")));
            $readCreditPackage = $creditPackage->fetch();
            $isCreditPackagePayment = $creditPackage->rowCount() > 0;
            
            if ($creditPackage->rowCount() > 0) {
              if ($readCreditPackage["stock"] == 0) {
                go("/kredi/yukle");
              }
              $discountedPriceStatus = ($readCreditPackage["discountedPrice"] != 0 && ($readCreditPackage["discountExpiryDate"] > date("Y-m-d H:i:s") || $readCreditPackage["discountExpiryDate"] == '1000-01-01 00:00:00'));
            }
          }
          
          $money = $isCreditPackagePayment ? ($discountedPriceStatus ? $readCreditPackage["discountedPrice"] : $readCreditPackage["price"]) : post("price")/$readSettings["creditMultiplier"];
          $credit = $isCreditPackagePayment ? $readCreditPackage["id"] : post("price");
          $paymentMethodID = $readPayment["id"];
          $productName = ($isCreditPackagePayment) ? $readCreditPackage["name"] : $credit.' '. $readSettings["creditText"];
          
          if ($payment->rowCount() > 0) {
            require_once(__ROOT__."/apps/main/private/packages/class/curlpost/curlpost.php");
            $siteURL = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"]);
            $readVariables = json_decode($readPayment["variables"], true);
            if ($readPayment["apiSlug"] == "batihost") {
              $postFields = array(
                'oyuncu'          => $readAccount["id"].'_'.$credit.'_'.$paymentMethodID,
                'amount'          => (int) ceil($money),
                'vipname'         => $productName,
                'batihostid'      => $readVariables["batihostID"],
                'raporemail'      => $readVariables["batihostEmail"],
                'odemeolduurl'    => $siteURL.'/kredi/yukle/basarili',
                'odemeolmadiurl'  => $siteURL.'/kredi/yukle/basarisiz',
                'posturl'         => $siteURL.'/islem/batihost/'.(($readPayment["type"] == 1) ? 'mobil' : (($readPayment["type"] == 2) ? 'kredi-karti' : 'mobil'))
              );
              if ($readPayment["type"] == 1) {
                $curlURL = 'https://batigame.com/vipgateway/viprec.php';
              }
              else if ($readPayment["type"] == 2) {
                $curlURL = 'https://batihost.com/vipgateway/viprec.php';
                $postFields = array_merge($postFields, array(
                  'odemeturu' => 'kredikarti'
                ));
              }
              else {
                $curlURL = 'https://batigame.com/vipgateway/viprec.php';
              }
              $curl = new \LeaderOS\Http\CurlPost($curlURL);
              try {
                echo $curl($postFields);
              } catch (\RuntimeException $ex) {
                  die(sprintf('HTTP hatası: %s Kod: %d', $ex->getMessage(), $ex->getCode()));
              }
            }
            else if ($readPayment["apiSlug"] == "paywant") {
              $hash = base64_encode(hash_hmac('sha256', $readAccount["realname"].'|'.$readAccount["email"].'|'.$readAccount["id"].$readVariables['paywantAPIKey'], $readVariables['paywantAPISecretKey'], true));
              $curlURL = 'http://api.paywant.com/gateway.php';
              $postFields = array(
                'proApi'        => true,
                'apiKey'        => $readVariables["paywantAPIKey"],
                'hash'          => $hash,
                'userID'        => $readAccount["id"],
                'returnData'    => $readAccount["realname"],
                'userEmail'     => $readAccount["email"],
                'userIPAddress' => getIP(),
                'productData'   => array(
                  'name'            => $productName,
                  'amount'          => $money*100,
                  "extraData"       => $credit,
                  'paymentChannel'  => (string)$readPayment["type"],
                  'commissionType'  => (int)$readVariables["paywantCommissionType"]
                )
              );
              $curl = new \LeaderOS\Http\CurlPost($curlURL);
              try {
                $result = json_decode($curl($postFields), true);
                if ($result["Status"] == 100) {
                  go($result["Message"]);
                }
                else {
                  if ($readSettings["debugModeStatus"] == 1) {
                    print_r($result);
                  }
                  else {
                    go("/kredi/yukle");
                  }
                }
              } catch (\RuntimeException $ex) {
                  die(sprintf('HTTP hatası: %s Kod: %d', $ex->getMessage(), $ex->getCode()));
              }
            }
            else if ($readPayment["apiSlug"] == "rabisu") {
              $curlURL = 'https://odeme.rabisu.com/odeme.php';
              $postFields = array(
                'oyuncu_adi'      => $readAccount["id"].'_'.$credit.'_'.$paymentMethodID,
                'fiyat'            => (int) ceil($money),
                'urun_adi'        => $productName,
                'bayi_id'         => $readVariables["rabisuID"],
                'yontem'          => (($readPayment["type"] == 1) ? 'mobil' : (($readPayment["type"] == 2) ? 'kart' : 'mobil')),
                'basarili_url'    => $siteURL.'/kredi/yukle/basarili',
                'basarisiz_url'   => $siteURL.'/kredi/yukle/basarisiz',
                'post_url'        => $siteURL.'/islem/rabisu/'.(($readPayment["type"] == 1) ? 'mobil' : (($readPayment["type"] == 2) ? 'kredi-karti' : 'mobil'))
              );
              $curl = new \LeaderOS\Http\CurlPost($curlURL);
              try {
                echo $curl($postFields);
              } catch (\RuntimeException $ex) {
                  die(sprintf('HTTP hatası: %s Kod: %d', $ex->getMessage(), $ex->getCode()));
              }
            }
            else if ($readPayment["apiSlug"] == "shopier") {
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

              $shopier = new \Shopier\Shopier($readVariables['shopierAPIKey'], $readVariables['shopierAPISecretKey']);

              // Satın alan kişi bilgileri
              $buyer = new \Shopier\Models\Buyer([
                'id'      => $readAccount["id"],
                'name'    => $accountFirstName,
                'surname' => $accountLastName,
                'email'   => $readAccount["email"],
                'phone'   => $accountPhoneNumber
              ]);

              // Fatura ve kargo adresi birlikte tanımlama
              // Ayrı ayrı da tanımlanabilir
              $address = new \Shopier\Models\Address([
                'address'   => 'Esentepe Mahallesi Eski Büyükdere Caddesi, Tekfen Tower No:209, 34343 4.Levent/Şişli',
                'city'      => 'İstanbul',
                'country'   => 'Türkiye',
                'postcode'  => '34343',
              ]);

              // shopier parametrelerini al
              $params = $shopier->getParams();

              // Geri dönüş sitesini ayarla
              $params->setWebsiteIndex(\Shopier\Enums\WebsiteIndex::SITE_1);

              // Satın alan kişi bilgisini ekle
              $params->setBuyer($buyer);

              // Fatura ve kargo adresini aynı şekilde ekle
              $params->setAddress($address);

              // Sipariş numarası ve sipariş tutarını ekle
              $extraData = $readAccount["id"].'_'.$credit.'_'.$paymentMethodID;
              $params->setOrderData($extraData, $money);

              // Sipariş edilen ürünü ekle
              $params->setProductData($productName, \Shopier\Enums\ProductType::DOWNLOADABLE_VIRTUAL);

              try {
                $renderer = $shopier->createRenderer(\Shopier\Renderers\AutoSubmitFormRenderer::class);
                $shopier->goWith($renderer);
              } catch (\Shopier\Exceptions\RequiredParameterException $e) {
                // die('Zorunlu parametrelerden bir ve daha fazlası eksik!');
                go("/kredi/yukle");
              } catch (\Shopier\Exceptions\NotRendererClassException $e) {
                // die('$shopier->createRenderer(...) metodunda verilen class adı AbstractRenderer sınıfından türetilmemiş!');
                go("/kredi/yukle");
              } catch (\Shopier\Exceptions\RendererClassNotFoundException $e) {
                // die('$shopier->createRenderer(...) metodunda verilen class bulunamadı!');
                go("/kredi/yukle");
              }
            }
            else if ($readPayment["apiSlug"] == "keyubu") {
              $curlURL = 'https://musteri.keyubu.com/gateway/odeme.php';
              $postFields = array(
                'odemeID'   => $readVariables["keyubuID"],
                'user_ip'   => getIP(),
                'amount'    => (int) ceil($money),
                'return_id' => $readAccount["id"].'_'.$credit.'_'.$paymentMethodID,
                'method'    => (($readPayment["type"] == 1) ? 2 : (($readPayment["type"] == 2) ? 1 : 2)),
                'callback'  => '/islem/keyubu/'.(($readPayment["type"] == 1) ? 'mobil' : (($readPayment["type"] == 2) ? 'kredi-karti' : 'mobil'))
              );
              $curl = new \LeaderOS\Http\CurlPost($curlURL);
              try {
                $result = json_decode($curl($postFields), true);
                if ($result["status"] == 'success') {
                  go('https://musteri.keyubu.com/gateway/odeme.php?token='.$result["token"]);
                }
                else {
                  if ($readSettings["debugModeStatus"] == 1) {
                    print_r($result);
                  }
                  else {
                    go("/kredi/yukle");
                  }
                }
              } catch (\RuntimeException $ex) {
                  die(sprintf('HTTP hatası: %s Kod: %d', $ex->getMessage(), $ex->getCode()));
              }
            }
            else if ($readPayment["apiSlug"] == "ininal") {
              go("/odeme/ininal");
            }
            else if ($readPayment["apiSlug"] == "papara") {
              go("/odeme/papara");
            }
            else if ($readPayment["apiSlug"] == "shipy") {
              $postFields = array(
                'usrIp'       => getIP(),
                'usrEmail'    => $readAccount["email"],
                'usrName'     => $accountFullName,
                'usrAddress'  => 'Esentepe Mahallesi Eski Büyükdere Caddesi, Tekfen Tower No:209, 34343 4.Levent/Şişli',
                'usrPhone'    => $accountPhoneNumber,
                'apiKey'      => $readVariables["shipyAPIKey"],
                'amount'      => $money,
                'returnID'    => $readAccount["id"].'_'.$credit.'_'.$paymentMethodID,
                'currency'    => 'TRY',
                'pageLang'    => 'TR',
                'mailLang'    => 'TR',
                'installment' => 0
              );
              if ($readPayment["type"] == 1) {
                $curlURL = 'https://api.shipy.dev/pay/mobile';
              }
              else if ($readPayment["type"] == 2) {
                $curlURL = 'https://api.shipy.dev/pay/credit_card';
              }
              else if ($readPayment["type"] == 3) {
                $curlURL = 'https://api.shipy.dev/pay/eft';
              }
              else {
                $curlURL = 'https://api.shipy.dev/pay/mobile';
              }
              $curl = new \LeaderOS\Http\CurlPost($curlURL);
              try {
                if ($readPayment["type"] == 1) {
                  echo $curl($postFields);
                }
                else {
                  $result = json_decode($curl($postFields), true);
                  if ($result["status"] == "success") {
                    go($result["link"]);
                  }
                  else {
                    if ($readSettings["debugModeStatus"] == 1) {
                      print_r($result);
                    }
                    else {
                      go("/kredi/yukle");
                    }
                  }
                }
              } catch (\RuntimeException $ex) {
                  die(sprintf('HTTP hatası: %s Kod: %d', $ex->getMessage(), $ex->getCode()));
              }
            }
            else if ($readPayment["apiSlug"] == "eft") {
              go("/odeme/eft");
            }
            else if ($readPayment["apiSlug"] == "paytr") {
              $paymentAmount = $money * 100;
              $noInstallment = 0;
              $maxInstallment = 0;
              $timeoutLimit = "30";
              $currency = "TL";
              $debugStatus = 0;
              $testModeStatus = 0;
              $orderID = $readAccount["id"].'i'.$credit.'i'.rand(100000, 999999).'i'.$paymentMethodID;
              $products = base64_encode(json_encode(array(
                array(substr($productName, 0, 50), $credit, 1),
              )));
              $paytrHash 	= $readVariables["paytrID"].getIP().$orderID.$readAccount["email"].$paymentAmount.$products.$noInstallment.$maxInstallment.$currency.$testModeStatus;
              $paytrToken = base64_encode(hash_hmac('SHA256', $paytrHash.$readVariables["paytrAPISecretKey"], $readVariables["paytrAPIKey"], true));
              $curlURL = 'https://www.paytr.com/odeme/api/get-token';
              $postFields = array(
                'merchant_id'				=> $readVariables["paytrID"],
                'merchant_oid' 			=> $orderID,
                'payment_amount'		=> $paymentAmount,
                'paytr_token'				=> $paytrToken,
                'user_basket'				=> $products,
                'no_installment'		=> $noInstallment,
                'max_installment'		=> $maxInstallment,
                'email'							=> $readAccount["email"],
                'user_name'					=> $accountFullName,
                'user_address'			=> "Esentepe Mahallesi Eski Büyükdere Caddesi, Tekfen Tower No:209, 34343 4.Levent/Şişli",
                'user_phone'				=> $accountPhoneNumber,
                'user_ip' 					=> getIP(),
                'merchant_ok_url'		=> $siteURL.'/kredi/yukle/basarili',
                'merchant_fail_url'	=> $siteURL.'/kredi/yukle/basarisiz',
                'timeout_limit'			=> $timeoutLimit,
                'currency'					=> $currency,
                'debug_on'					=> $debugStatus,
                'test_mode'					=> $testModeStatus
              );
              $curl = new \LeaderOS\Http\CurlPost($curlURL);
              try {
                $result = json_decode($curl($postFields), true);
                if ($result["status"] == 'success') {
                  $_SESSION["PAYTR_IFRAME_TOKEN"] = $result["token"];
                  go("/odeme/paytr");
                }
                else {
                  if ($readSettings["debugModeStatus"] == 1) {
                    print_r($result);
                  }
                  else {
                    go("/kredi/yukle");
                  }
                }
              } catch (\RuntimeException $ex) {
                die(sprintf('HTTP hatası: %s Kod: %d', $ex->getMessage(), $ex->getCode()));
              }
            }
            else if ($readPayment["apiSlug"] == "slimmweb") {
              //go('https://musteri.slimmweb.com/pay/odeme.php?odemeID='.$readVariables["slimmwebPaymentID"].'&amount='.post("price").'&return_id='.$readAccount["id"].'_'.post("price").'_'.generateSalt(12));
              die("Slimmweb ödeme sistemi şuanda kullanım dışıdır.");
            }
            else if ($readPayment["apiSlug"] == "paylith") {
              /*$conversationId = $readAccount["id"].'_'.post("price");
              $hashStr = [
                  'apiKey' => $readVariables["paylithAPIKey"],
                  'conversationId' => $conversationId,
                  'userId' => $readAccount["id"],
                  'userEmail' => $readAccount["email"],
                  'userIpAddress' => getIP(),
              ];
              ksort($hashStr);
              $hash = hash_hmac('sha256', implode('|', $hashStr) . $readVariables["paylithAPISecretKey"], $readVariables["paylithAPIKey"]);
              $paylithToken = hash_hmac('md5', $hash, $readVariables["paylithAPIKey"]);
              $curlURL = "https://api.paylith.com/v1/token";
              $postFields = array(
                "apiKey" => $readVariables["paylithAPIKey"],
                "conversationId" => $conversationId, // Ödeme eşleştirmesi için kullanılır.
                "productApi" => true,

                // Ödeme yapılacak ürün bilgisi
                "productData" => array(
                  "name" => post("price").' TL Kredi', // Ödeme yapılacak ürünün adı
                  "amount" => post("price")*100 // Ödeme yapılacak ürünün fiyatı * 100
                ),
                "token" => $paylithToken,
                "userEmail" => $readAccount["email"], // Üye işyeri tarafındaki kullanıcıya ait e-posta adresi.
                "userId" => $readAccount["id"], // Üye işyeri tarafındakı kullanıcıya ait ID.
                "userIpAddress" => getIP(), // Üye işyeri tarafındakı kullanıcının ip adresi.
                "userPhone" => $accountPhoneNumber, // Üye işyeri tarafındakı kullanıcının telefon numarası.
                "redirectUrl" => $siteURL.'/kredi/yukle/basarili'
              );
              $curl = new \LeaderOS\Http\CurlPost($curlURL);
              try {
                $result = json_decode($curl($postFields), true);
                if ($result["status"] == "success") {
                  go($result["paymentLink"]);
                }
                else {
                  if ($readSettings["debugModeStatus"] == 1) {
                    print_r($result);
                  }
                  else {
                    go("/kredi/yukle");
                  }
                }
              } catch (\RuntimeException $ex) {
                  die(sprintf('HTTP hatası: %s Kod: %d', $ex->getMessage(), $ex->getCode()));
              }*/
              die("Paylith ödeme sistemi şuanda kullanım dışıdır.");
            }
            else if ($readPayment["apiSlug"] == "paymax") {
              require_once(__ROOT__."/apps/main/private/packages/class/paymax/PaymaxAPI.php");
              
              $paymax = new PaymaxAPI($readVariables["paymaxUser"], $readVariables["paymaxKey"], $readVariables["paymaxStoreCode"], $readVariables["paymaxHash"]);
              $order_data = array(
                'productName' => $productName,
                'productData' => array(
                  array(
                    'productName'=> $productName,
                    'productPrice'=> $money,
                    'productType'=> 'DIJITAL_URUN',
                  ),
                ),
                'productType' => 'DIJITAL_URUN',
                'productsTotalPrice' => $money,
                'orderPrice' => $money,
                'currency' => 'TRY',
                'orderId' => 'PM-'.rand(100000, 999999),
                'locale' => 'tr',
                'conversationId' => $readAccount["id"].'_'.$credit.'_'.$paymentMethodID,
                'buyerName' => $accountFirstName,
                'buyerSurName' => $accountLastName,
                'buyerGsmNo' => $accountPhoneNumber,
                'buyerIp' => getIP(),
                'buyerMail' => $readAccount["email"],
                'buyerAdress' => '',
                'buyerCountry' => '',
                'buyerCity' => '',
                'buyerDistrict' => '',
                'callbackOkUrl' => $siteURL.'/kredi/yukle/basarili',
                'callbackFailUrl' => $siteURL.'/kredi/yukle/basarisiz',
              );
              $request = $paymax->create_payment_link($order_data);
  
              if ($request['status']=='success' && isset($request['payment_page_url'])) {
                go($request['payment_page_url']);
              }
              else {
                if ($readSettings["debugModeStatus"] == 1) {
                  print_r($request);
                }
                else {
                  go("/kredi/yukle");
                }
              }
            }
            else if ($readPayment["apiSlug"] == "weepay") {
              require_once(__ROOT__."/apps/main/private/packages/api/weepay/weepayBootstrap.php");
              weepayBootstrap::initialize();
  
              // Auth
              $options = new \weepay\Auth();
              $options->setBayiID($readVariables["weepayID"]);
              $options->setApiKey($readVariables["weepayAPIKey"]);
              $options->setSecretKey($readVariables["weepayAPISecretKey"]);
              $options->setBaseUrl("https://api.weepay.co");
  
              //Request
              $request = new \weepay\Request\FormInitializeRequest();
              $request->setOrderId($readAccount["id"].'_'.$credit.'_'.$paymentMethodID);
              $request->setIpAddress(getIP());
              $request->setPrice($money);
              $request->setCurrency(\weepay\Model\Currency::TL);
              $request->setLocale(\weepay\Model\Locale::TR);
              $request->setDescription(substr($productName, 0, 50));
              $request->setCallBackUrl($siteURL.'/islem/weepay');
              $request->setPaymentGroup(\weepay\Model\PaymentGroup::PRODUCT);
              $request->setPaymentChannel(\weepay\Model\PaymentChannel::WEB);
  
              //Customer
              $customer = new \weepay\Model\Customer();
              $customer->setCustomerId($readAccount["id"]); // Üye işyeri müşteri Id
              $customer->setCustomerName($accountFirstName); //Üye işyeri müşteri ismi
              $customer->setCustomerSurname($accountLastName); //Üye işyeri müşteri Soyisim
              $customer->setGsmNumber($accountPhoneNumber); //Üye işyeri müşteri Cep Tel
              $customer->setEmail($readAccount["email"]); //Üye işyeri müşteri ismi
              $customer->setIdentityNumber("11111111111"); //Üye işyeri müşteri TC numarası
              $customer->setCity("İstanbul"); //Üye işyeri müşteri il
              $customer->setCountry("Türkiye");//Üye işyeri müşteri ülke
              $request->setCustomer($customer);
  
              //Adresler
              // Fatura Adresi
              $BillingAddress = new \weepay\Model\Address();
              $BillingAddress->setContactName($accountFullName);
              $BillingAddress->setAddress("Esentepe Mahallesi Eski Büyükdere Caddesi, Tekfen Tower No:209, 34343 4.Levent/Şişli");
              $BillingAddress->setCity("İstanbul");
              $BillingAddress->setCountry("Türkiye");
              $BillingAddress->setZipCode("34343");
              $request->setBillingAddress($BillingAddress);
  
              // Sipariş Ürünleri
              $Products = array();
  
              // Birinci Ürün
              $product = new \weepay\Model\Product();
              $product->setName($productName);
              $product->setProductId($readAccount["id"].'_'.$credit.'_'.$paymentMethodID);
              $product->setProductPrice($money);
              $product->setItemType(\weepay\Model\ProductType::VIRTUAL);
              $Products[0] = $product;
              $request->setProducts($Products);
  
              $checkoutFormInitialize = \weepay\Model\CheckoutFormInitialize::create($request, $options);
              if ($checkoutFormInitialize->getStatus() == 'success') {
                go($checkoutFormInitialize->getPaymentPageUrl());
              }
              else {
                if ($readSettings["debugModeStatus"] == 1) {
                  print_r($checkoutFormInitialize);
                }
                else {
                  go("/kredi/yukle");
                }
              }
            }
            else {
              go("/kredi/yukle");
            }
          }
          else {
            go("/kredi/yukle");
          }
        }
        else {
          go("/kredi/yukle");
        }
      }
      else {
        echo goDelay("/kredi/yukle", 3);
        die('Sistemsel bir sorun oluştu. '. $readSettings["creditText"] .' Yükleme sayfasına yönlendiriliyorsunuz...');
      }
    }
    else {
      go("/kredi/yukle");
    }
  }
  else {
    go("/giris-yap");
  }
?>
