// JavaScript Document

$(function(){
	
	
	
	$('.call_modal_add').on('click', function(){	  	 
		
		var vocabulario = $(this).attr('data-id_vocabulario');
		
		$('.modal .modal-body').load('admin/taxonomia/add_termo/vocabulario/'+vocabulario, '', function(){
			  
			  $('.modal-title').text('Cadastrar termo');
			
			  $('.modal').modal({
			  	keyboard: true
			  });
			  
		});
		
	});	
	
	$('.call_modal_edit').on('click', function(){	  	 
		
		var id = $(this).attr('data-id');
		
		var vocabulario = $(this).attr('data-id_vocabulario');
		
		$('.modal .modal-body').load('admin/taxonomia/edit_termo/id/'+id+'/vocabulario/'+vocabulario, '', function(){
			  
			  $('.modal-title').text('Editar termo');
			
			  $('.modal').modal({
			  	keyboard: true
			  });
			  
		});
		
	});
	
	$('.modal-footer .btn-primary').on('click', function(){	  	 
	
		$('#add').submit();
	});	
	
	//admin/taxonomia/add_termo/vocabulario/<?php print $view_id_vocabulario; ?>
})