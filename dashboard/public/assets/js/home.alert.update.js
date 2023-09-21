$(document).ready(function() {
  swal.fire({
    type: "warning",
    title: "UYARI!",
    html: alertText,
    confirmButtonColor: "#02b875",
    confirmButtonText: "Güncelle"
  }).then(function() {
    window.location = alertLocation;
  });
});
