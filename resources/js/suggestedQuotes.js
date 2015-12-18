
$(function() {

	$('.deleteSuggestion').click(function() {
		console.log("Delete confirm now!");
		var sqId = $(this).data('sqid');
		var row = $(this).parents('tr');

		swal({   
			title: "Vill du verkligen ta bort förslaget?",
			text: "Går inte att ångra osv... Ja, det vanliga.",
			type: "warning",
			showCancelButton: true,
			closeOnConfirm: false,
			showLoaderOnConfirm: true,
		}, function(){
			$.ajax({
				cache : false,
				type: 'POST',
				crossDomain:false,
				data: {doDelete: true},
				url: '/admin/delete/sq/' + sqId,
				success: function(data){
					swal("Ok", "Suggested quote deleted", "success");
					row.slideUp('slow');
				},
				error: function(xhr, status, error){
					var err = xhr.responseText;
					swal("Could not delete", "Failed to delete: " + err, "error");
				}
			});
		});
	});

});