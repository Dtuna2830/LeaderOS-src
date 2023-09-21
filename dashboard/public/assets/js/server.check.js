var checkConnectSpinner = $("#checkConnect .spinner-grow");
var checkConnectButton = $("#checkConnect a");

checkConnectButton.on("click", function() {
  var inputIP = $("#inputIP").val();
  var selectConsoleID = $("#selectConsoleID").val();
  var inputConsolePort = $("#inputConsolePort").val();
  var inputConsolePassword = $("#inputConsolePassword").val();

  checkConnectSpinner.css("display", "inline-block");
  checkConnectButton.text("Konsol bağlantısı kontrol ediliyor...");
  $.ajax({
    type: "POST",
    url: "/apps/dashboard/public/ajax/check.php?action=connect&category=console",
    data: {serverIP: inputIP, consoleID: selectConsoleID, consolePort: inputConsolePort, consolePassword: inputConsolePassword},
    success: function(result) {
      if (result == false) {
        checkConnectSpinner.css("display", "none");
        checkConnectButton.text("Konsol Bağlantısını Kontrol Et");
        swal.fire({
          type: "error",
          title: "HATA!",
          text: "Konsol bağlantısı kurulamadı!",
          confirmButtonColor: "#02b875",
          confirmButtonText: "Tamam"
        });
      }
      else {
        checkConnectSpinner.css("display", "none");
        checkConnectButton.text("Konsol Bağlantısını Kontrol Et");
        swal.fire({
          type: "success",
          title: "BAŞARILI!",
          text: "Konsol bağlantısı kuruldu!",
          confirmButtonColor: "#02b875",
          confirmButtonText: "Tamam"
        });
      }
    }
  });
});
