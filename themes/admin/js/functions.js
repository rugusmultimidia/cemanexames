// JavaScript Document

$(function(){
	
	//del ajax
   $(document).on('click','.del',function(){
		
		var url = $(this).attr('data-controller');
		
		var id = $(this).attr('data-id');
		
		var r = confirm("Tem certeza que deseja excluir?");
		
		var line = $(this).closest('.line');
		
		if (r==true){
			
			$.ajax({
			 type: "POST",
			 url: url,
			 data: {id:id},
			 success: function(data){   
					line.hide();
				
			 }
			});
	
		}
		
	});
	
	//multiple selects.
	//$(".chosen-select").chosen({no_results_text: "Texto nao encontrado."}); 
 	
	$('._tooltip').tipsy({gravity: 's'});

	$('#EmpresaSelect').select2();
	$('#colaboradores').select2();
	$('select[multiple]').select2();
	$('select.select2').select2();
	
	var checkboxClass = 'icheckbox_flat-blue';
	var radioClass = 'iradio_flat-blue';
	$('input[type=checkbox],input[type=radio]').iCheck({
    	checkboxClass: checkboxClass,
    	radioClass: radioClass
	});
	
	/*------ Empresa Select -------*/
	
	
	
	$('.modal-label-button-enterprise').click(function(){
		
		var id = $('#EmpresaSelect').val();
		
		if(id > 0) {

			$.ajax({
				 type: "POST",
				 url: 'pessoa_juridica/activeEnterprise',
				 data: {id:id},
				 success: function(data){ 				
					window.location.href = '';
				 }
			});

		}
				
	});
	
	
	/*------ Mask --------*/
	
	$('input#cpf').mask('999.999.999-99');
	$('input#telefone').mask('(99) 9999-9999?9');
	$('input#cep').mask('99999-999');
	$('input#data_de_entrada').mask('99/99/9999');
	$('input#data_de_saida').mask('99/99/9999');
	$('input#data_de_nascimento').mask('99/99/9999');
	$('.data').mask('99/99/9999');
	
	$('input#cbo').mask('9999-99');
}); 