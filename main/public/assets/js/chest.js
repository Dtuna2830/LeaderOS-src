
var deliverButton = $(".deliverButton");
var chestDecreaseButton = $(".chestDecreaseButton");
var chestIncreaseButton = $(".chestIncreaseButton");

chestDecreaseButton.on("click", function() {
  var target = $($(this).data("target"));
  var currentValue = parseInt(target.val());
  var minValue = parseInt(target.attr("min"));
  var newValue = currentValue - 1;
  if (newValue >= minValue) {
    target.val(newValue);
  }
});
chestIncreaseButton.on("click", function() {
  var target = $($(this).data("target"));
  var currentValue = parseInt(target.val());
  var maxValue = parseInt(target.attr("max"));
  var newValue = currentValue + 1;
  if (newValue <= maxValue) {
    target.val(newValue);
  }
});

deliverButton.on("click", function() {
  var chestID = $(this).attr('data-chest');
  var amountChangerInput = $(this).parents("#chest-"+chestID).find("input");
  var maxAmount = amountChangerInput.attr("max");
  var amount = amountChangerInput.val();
  swal.fire({
    title: "UYARI!",
    text: "Lütfen onaylamadan önce sunucuda olduğunuza emin olun aksi taktirde ürün size ulaşmayabilir!",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#02b875",
    cancelButtonColor: "#f5365c",
    cancelButtonText: "İptal",
    confirmButtonText: "Onayla",
    reverseButtons: true
  }).then(function(isAccepted) {
    if (isAccepted.value) {
      swal.fire({
        title: "UYARI!",
        html: "<p>Teslim işlemi yapılıyor lütfen bekleyin ve sayfadan ayrılmayın...</p>",
        type: "warning",
        allowOutsideClick: false,
        showConfirmButton: false
      });
      $.ajax({
        type: "POST",
        url: "/apps/main/public/ajax/chest.php?amount=" + amount,
        data: {chestID: chestID},
        success: function(result) {
          if (result == "error") {
            swal.fire({
              title: "HATA!",
              text: "Beklenmedik bir hata oluştu, lütfen daha sonra tekrar deneyiniz.",
              type: "error",
              confirmButtonColor: "#02b875",
              confirmButtonText: "Tamam"
            });
          }
          else if (result == "error_login") {
            swal.fire({
              title: "HATA!",
              text: "Satın alım işlemi için giriş yapmalısınız.",
              type: "error",
              confirmButtonColor: "#02b875",
              confirmButtonText: "Tamam"
            }).then(function() {
              window.location = '/giris-yap';
            });
          }
          else if (result == "error_amount") {
            swal.fire({
              title: "HATA!",
              text: "En fazla " + maxAmount + " adet teslim alabilirsiniz!",
              type: "error",
              confirmButtonColor: "#02b875",
              confirmButtonText: "Tamam"
            });
          }
          else if (result == "error_connection") {
            swal.fire({
              title: "HATA!",
              text: "Sunucuya bağlanırken bir sorun oluştu! Lütfen daha sonra tekrar deneyiniz.",
              type: "error",
              confirmButtonColor: "#02b875",
              confirmButtonText: "Tamam"
            });
          }
          else {
            swal.fire({
              title: "BAŞARILI!",
              text: "Ürün başarılı bir şekilde teslim edildi!",
              type: "success",
              confirmButtonColor: "#02b875",
              confirmButtonText: "Tamam"
            }).then(function() {
              window.location = '/sandik';
            });
          }
        }
      });
    }
  });
});
