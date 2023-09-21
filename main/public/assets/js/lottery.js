$('.superwheel').superWheel({
	slices: slices,
	text: {
		size: 20,
		color: '#ffffff',
		offset: 14,
		letterSpacing: 4,
		orientation: 'h',
		arc: true
	},
	line: {
		width: 5,
		color: "#ffca19"
	},
	outer: {
		width: 10,
		color: "#ffca19"
	},
	inner: {
		width: 10,
		color: "#ffca19"
	},
	marker: {
		background: "#ed2024",
		animate: 1
	},
	width: 400,
	selector: "id"
});

var tick_audio = new Audio('/apps/main/public/assets/css/plugins/superwheel/tick.mp3');
var win_audio = new Audio('/apps/main/public/assets/css/plugins/superwheel/win.mp3');
var lose_audio = new Audio('/apps/main/public/assets/css/plugins/superwheel/lose.mp3');

function getData(ajaxurl) {
	return $.ajax({
		url: ajaxurl,
		type: 'GET',
		error: function() {
			if (wheelSpining) {
				wheelSpining = false;
			}
			swal.fire({
				title: 'BAŞARISIZ!',
				text: 'Sistemsel bir sorun oluştu, lütfen daha sonra tekrar deneyiniz.',
				type: 'error',
				confirmButtonColor: '#02b875',
				confirmButtonText: 'Tamam'
			});
		}
	});
};

var wheelSpining = false;

$('#playGame').on('click', function() {
	if (wheelSpining == false) {
		wheelSpining = true;
		getData('/apps/main/public/ajax/lottery.php?action=play&id=' + lotteryID).then(function(ajaxResult) {
			if (ajaxResult) {
				ajaxResult = jQuery.parseJSON(ajaxResult);
				if (ajaxResult["data"] == 'error_login') {
					swal.fire({
						title: 'BAŞARISIZ!',
						text: 'Lütfen giriş yapınız!',
						type: 'error',
						confirmButtonColor: '#02b875',
						confirmButtonText: 'Giriş Yap'
					}).then(function() {
						window.location = '/giris-yap';
					});
				} else if (ajaxResult["data"] == 'error_credit') {
					swal.fire({
						title: 'BAŞARISIZ!',
						text: 'Yetersiz '+creditText+', lütfen '+creditText+' yükleyip tekrar deneyiniz.',
						type: 'error',
						confirmButtonColor: '#02b875',
						confirmButtonText: creditText+' Yükle'
					}).then(function() {
						window.location = '/kredi/yukle';
					});
				} else if (ajaxResult["data"] == 'error_duration') {
					swal.fire({
						title: 'BAŞARISIZ!',
						text: 'Tekrar ücretsiz çevirmek için ' + ajaxResult["variable"] + ' tarihine kadar beklemeniz gerekmektedir.',
						type: 'error',
						confirmButtonColor: '#02b875',
						confirmButtonText: 'Tamam'
					});
				} else {
					$('.superwheel').superWheel('start', 'id', parseInt(ajaxResult["data"]));
				}
			}
		});
	}
});

$('.superwheel').superWheel('onStart', function(results) {
	$('#playGame').text('Çevriliyor...').attr('disabled', 'disabled').addClass('disabled').css('cursor', 'no-drop');
});

$('.superwheel').superWheel('onStep', function(results) {
	tick_audio.pause();
	tick_audio.currentTime = 0;
	tick_audio.play();
});

$('.superwheel').superWheel('onComplete', function(results) {
	tick_audio.pause();
	tick_audio.currentTime = 0;
	if (results.type === 3) {
		lose_audio.pause();
		lose_audio.currentTime = 0;
		lose_audio.volume = 0.25;
		lose_audio.play();
		swal.fire({
			title: 'BAŞARISIZ!',
			text: 'Üzgünüz bir dahaki sefere tekrar deneyiniz.',
			type: 'error',
			confirmButtonColor: '#02b875',
			confirmButtonText: 'Tamam'
		}).then(function() {
			lose_audio.pause();
			lose_audio.currentTime = 0;
		});
	} else {
		getData('/apps/main/public/ajax/lottery.php?action=credit').then(function(ajaxResult) {
			win_audio.pause();
			win_audio.currentTime = 0;
			win_audio.volume = 0.25;
			win_audio.play();
			swal.fire({
				title: 'BAŞARILI!',
				html: 'Başarılar, <strong>' + results.text + '</strong> adlı ödülü kazandınız. ' + (results.type == 2 ? 'Ürün sandığınıza eklenmiştir. ' : '') + 'Güncel '+creditText+': <strong>' + ajaxResult + ' '+creditText+'</strong>',
				type: 'success',
				confirmButtonColor: '#02b875',
				confirmButtonText: 'Tamam'
			}).then(function() {
				win_audio.pause();
				win_audio.currentTime = 0;
			});
		});
	}
	$('#playGame').text('Yeniden Oyna!').removeAttr('disabled').removeClass('disabled').css('cursor', 'pointer');
	wheelSpining = false;
});
