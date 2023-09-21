var checkConnectSpinner = $("#testSMTP .spinner-grow");
var checkConnectButton = $("#testSMTP span");

checkConnectButton.on("click", function() {
  var inputSMTPServer = $("#inputSMTPServer").val();
  var inputSMTPPort = $("#inputSMTPPort").val();
  var inputSMTPUsername = $("#inputSMTPUsername").val();
  var inputSMTPPassword = $("#inputSMTPPassword").val();
  var selectSMTPSecure = $("#selectSMTPSecure").val();

  checkConnectSpinner.css("display", "inline-block");
  checkConnectButton.text("Kontrol Ediliyor...");
  $.ajax({
    type: "POST",
    url: "/apps/dashboard/public/ajax/check.php?action=connect&category=smtp",
    data: {smtpServer: inputSMTPServer, smtpPort: inputSMTPPort, smtpSecure: selectSMTPSecure, smtpUsername: inputSMTPUsername, smtpPassword: inputSMTPPassword},
    success: function(result) {
      if (result == 'true') {
        checkConnectSpinner.css("display", "none");
        checkConnectButton.text("Bağlantıyı Kontrol Et");
        swal.fire({
          type: "success",
          title: "BAŞARILI!",
          text: "SMTP bağlantısı kuruldu!",
          confirmButtonColor: "#02b875",
          confirmButtonText: "Tamam"
        });
      }
      else {
        checkConnectSpinner.css("display", "none");
        checkConnectButton.text("Bağlantıyı Kontrol Et");
        swal.fire({
          type: "error",
          title: "HATA!",
          html: "<p>SMTP bağlantısı kurulamadı! Hata: " + result + "</p>",
          confirmButtonColor: "#02b875",
          confirmButtonText: "Tamam"
        });
      }
    }
  });
});
