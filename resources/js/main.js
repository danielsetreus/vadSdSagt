$(function() {

	if ($("#flashNote").length) {
		console.log($('#flashNote').data('type'));

		if(!$('#flashNote').data('type'))
			var type = 'success';
		else
			var type = $('#flashNote').data('type');

		var n = noty({
			text: $('#flashNote').data('text'),
			timeout: 5000,
			type: type,
			layout: 'topRight',
			theme: 'relax'
		});
	}

	if ($("#quoteId").length) {
		window.history.pushState("", "", "/" + $('#quoteId').val());

		$(document).keydown(function(e){
			if (e.keyCode == 37) { 
				$('#prevButton').click();
				return false;
			} else if(e.keyCode == 39) {
				$('#nextButton').click();
				return false;
			}
		});
	}

	$('#prevButton').click(function() {
		console.log("Go to prev");
		document.getElementById("prevButton").click();
	});
	$('#nextButton').click(function() {
		console.log("Go to next");
		document.getElementById("nextButton").click();
	});

	$('#facebookShareOpen').click(function() {
		facebookShare($(this).data('url'));
	});

	$('#twitterShareOpen').click(function() {
		twitterShare($(this).data('text'), $(this).data('id'));
	});

	$('#permLinkOpen').click(function() {
		var link = $(this).data('url');
		swal({
			title: "Länka till citatet",
			type: 'input',
			inputValue: link
		});
	});

	function facebookShare(shareUrl) {
		var url = 'https://www.facebook.com/sharer/sharer.php?u=' + shareUrl;
		newwindow=window.open(url,'FB','height=500,width=550');
		if (window.focus) { newwindow.focus() }
		return false;
	}

	function twitterShare(text, id) {
		var url = "https://twitter.com/intent/tweet?text=" + text + "&url=http://vadsdsagt.se/" + id + "&hashtags=vadsdsagt";
		newwindow=window.open(url,'TW','height=500,width=550');
		if (window.focus) { newwindow.focus() }
		return false;
	}
});