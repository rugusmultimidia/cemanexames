$(function(){

	$( "#paciente" ).autocomplete({

		source: function (request, response)
	    {
	        $.ajax(
	        {
	            url: 'admin/pacientes/getPacienteAjax/',
	            dataType: "json",
	            type : 'POST',
	            data:
	            {
	                term: request.term,
	            },
	            success: function (data)
	            {
	                response( $.map( data, function( item ) {

					    return {
					        label: item.nome,
					        value: item.nome,
					        data : item
					    }
					}));
	            }
	        });
	    },
	    minLength: 3,
	    select: function (event, ui)
	    {	//console.log(ui.item.data.data_nascimento);
	    	
	        ///$('#email').val(ui.item.data.email);
	        ///$('#codigo_paciente').val(ui.item.data.codigo_paciente);
	        ///$('#data_de_nascimento').val(ui.item.data.data_nascimento);
	        ///$('#id_paciente').val(ui.item.data.id_pacientes); 
	        ///$('#senha').val(ui.item.data.senha); 
///
	        ///$('.senha').show(); 
	        
	    }
	});



})