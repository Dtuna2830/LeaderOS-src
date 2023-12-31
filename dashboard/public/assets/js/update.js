$("#updateButton").on("click", function() {
  $("#loader").addClass("is-loading");
  $("#spinner").css("display", "flex");
  $.ajax({
    type: "GET",
    url: "/apps/dashboard/public/ajax/update.php",
    success: function(result) {
      $("#loader").removeClass("is-loading");
      $("#spinner").css("display", "none");

      if (result) {
        $("#updateBlock").html('<span class="text-success">' + result + '</span>');
      }
      else {
        $("#updateBlock").html('<span class="text-danger">Güncelleme başarısız! Lütfen daha sonra tekrar deneyiniz veya leaderos.com.tr adresinden bizimle iletişime geçiniz.</span>');
      }
    }
  });
});
