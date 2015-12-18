$(function() {
	$( "#who" ).autocomplete({
		source: function( request, response ) {
			$.ajax({
				url: "/admin/persons/" + encodeURIComponent(request.term),
				dataType: "json",
				success: function( data ) {
					response( data );
				},
				error: function( data ) {
					console.log("Error calling!");
				}
			});
		},
		minLength: 1,
		focus: function( event, ui ) {
			$("#who").val(ui.item.name);
			return false;
		},
		select: function( event, ui ) {
			$("#who").val(ui.item.name);
			$("#what").val(ui.item.position).effect("highlight");
			$(".singleupload").html('<img src="/resources/images/persons/' + ui.item.image + '">');
			$("#imageName").val(ui.item.image).effect("hightlight");
			
			return false;
		},
		open: function() {
			$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
		},
		close: function() {
			$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
		}
	})
	.autocomplete( "instance" )._renderItem = function( ul, item ) {
		return $( "<li>" )
			.append( "<a><b>" + item.name + "</b><br><small>" + item.position + "</small></a>" )
			.appendTo( ul );
	};


	$('#uploadbox').singleupload({
		action: '/admin/persons/imageUpload',
		inputId: 'singleupload_input',
		onError: function(data) {
			console.log("Upload failed"); // @TODO: Better error handling
			console.log(data);
		}
		,onSuccess: function(url, data) {
			console.log(url);
			$("#imageName").val(url.name);
		}
		,onProgress: function(loaded, total) {
			console.log(loaded + " av " + total);
		}
	});

});