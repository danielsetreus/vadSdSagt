$(function() {

	$('.deleteQuote').click(function() {
		var qId = $(this).data('qid');
		var row = $(this).parents('tr');

		swal({   
			title: "Vill du verkligen ta bort citatet?",
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
				url: '/admin/quotes/' + qId + '/delete',
				success: function(data){
					swal("Ok", "Quote deleted", "success");
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