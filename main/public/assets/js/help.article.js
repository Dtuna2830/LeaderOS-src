var helpVote = $("#help-vote");
var helpVoteBtn = $("#help-vote button");

helpVoteBtn.on("click", function() {
  var helpID = helpVote.data("id");
  var helpVoteValue = $(this).data("value");
  $.ajax({
    type: "GET",
    url: "/apps/main/public/ajax/help-vote.php?id=" + helpID + "&vote=" + helpVoteValue,
    success: function(result) {
      if (result == "success") {
        helpVote.html('<span class="text-success">Geribildiriminiz için teşekkürler :)<span>');
      }
      else if (result == "error_login") {
        swal.fire({
          title: "HATA!",
          text: "Geribildirim göndermek için giriş yapmanız gerekiyor!",
          type: "error",
          confirmButtonColor: "#02b875",
          confirmButtonText: "Tamam"
        });
      }
      else {
        swal.fire({
          title: "HATA!",
          text: "Bir sorun oluştu! Lütfen daha sonra tekrar deneyiniz.",
          type: "error",
          confirmButtonColor: "#02b875",
          confirmButtonText: "Tamam"
        });
      }
    }
  });
});
