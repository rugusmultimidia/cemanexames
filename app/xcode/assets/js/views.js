// JavaScript Document

$(function(){
	
			
	

	if($("table.draggable").length > 0) {
		 $("table.draggable").rowSorter({
			handler: ".drag-handle",
			onDrop: function(tbody, row, new_index, old_index) {
				//$("#log").html(old_index + ". row moved to " + new_index);
			}
		});
	}
		
	$('input').focus(function(){
		
		$(this).removeClass('error');
	});
	
})