var modalBox = $("#modalBox");
var openBuyModal = $(".openBuyModal");

openBuyModal.on("click", function() {
  var openBuyModal = $(this);
  var productID = openBuyModal.attr("product-id");
  $.ajax({
    type: "GET",
    url: "/apps/main/public/ajax/gaming-modal.php?action=buy&id=" + productID,
    success: function(result) {
      if (result == false) {
        swal.fire({
          title: "HATA!",
          text: "Bir sorun oluştu! Lütfen daha sonra tekrar deneyiniz.",
          type: "error",
          confirmButtonColor: "#02b875",
          confirmButtonText: "Tamam"
        });
      }
      else {
       modalBox.html(result);
       $("#buyModal").modal("show");
      }
    }
  });
});
