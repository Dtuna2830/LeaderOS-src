$(document).ready(function() {
  $("#selectStatus").change(function() {
    if ($(this).val() == 0) {
      $("#settingsBlock").css("display", "none");
    }
    if ($(this).val() == 1) {
      $("#settingsBlock").css("display", "block");
    }
  });
});
