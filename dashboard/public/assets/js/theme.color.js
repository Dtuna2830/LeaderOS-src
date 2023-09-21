$(document).ready(function() {
  $("#selectColor").change(function() {
    if ($(this).val() == 0) {
      $("#colorSettingsBlock").css("display", "block");
      $("#colorSettingsButton").css("display", "inline-block");
    }
    else {
      $("#colorSettingsBlock").css("display", "none");
      $("#colorSettingsButton").css("display", "none");
    }
  });
  $("#colorSettingsButton").on("click", function() {
    var dialog = confirm('Sıfırladığınızda mevcut ayarlarınız silinecektir. Sıfırlamak istediğinize emin misiniz?');
    if (dialog == true) {
      var extraColors = jQuery.parseJSON($("input[name='extraColors']").val());
      $.each(extraColors, function(key, value) {
        $("input[name='" + key + "']").val(value).parent("[data-toggle='colorPicker']").colorpicker('setValue', value);
      });
    }
  });
});
