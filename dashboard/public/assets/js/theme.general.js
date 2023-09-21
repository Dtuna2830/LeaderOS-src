$(document).ready(function() {
  $("#selectSliderStatus").change(function() {
    if ($(this).val() == 0) {
      $("#sliderOptions").css("display", "none");
    }
    else {
      $("#sliderOptions").css("display", "block");
    }
  });
  $("#selectSidebarStatus").change(function() {
    if ($(this).val() == 0) {
      $("#sidebarOptions").css("display", "none");
    }
    if ($(this).val() == 1) {
      $("#sidebarOptions").css("display", "block");
    }
  });
  $("#selectDiscordWidgetStatus").change(function() {
    if ($(this).val() == 0) {
      $("#discordWidgetOptions").css("display", "none");
    }
    if ($(this).val() == 1) {
      $("#discordWidgetOptions").css("display", "block");
    }
  });

  $("#selectHeaderTheme").change(function() {
    if ($(this).val() == 1) {
      $("#headerThemeOptions").css("display", "none");
    }
    else if ($(this).val() == 2 || $(this).val() == 3) {
      $("#headerThemeOptions").css("display", "block");
    }
    else {
      $("#headerThemeOptions").css("display", "none");
    }
  });
});
