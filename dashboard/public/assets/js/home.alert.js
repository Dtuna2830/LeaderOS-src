$(document).ready(function() {
  swal.fire({
    type: "error",
    title: "HATA!",
    text: alertText,
    confirmButtonColor: "#e63757",
    confirmButtonText: "Tamam",
    allowEscapeKey: false,
    allowOutsideClick: false
  }).then(function() {
    window.location = alertLocation;
  });
});
