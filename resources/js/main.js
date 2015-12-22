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

	$('#permLinkOpen').click(function() {
		var link = $(this).data('url');
		swal({
			title: "Länka till citatet",
			type: 'input',
			inputValue: link
		});
	});
});