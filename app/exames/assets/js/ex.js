$(function(){	

	// Variable to store your files
	var files;	

	// Add events
	$('#__pdf').on('change', prepareUpload);	

	// Grab the files and set them to our variable

	function prepareUpload(event){

	  files = event.target.files;

	}

	

	var fileName;

	var dataJson;

	

	function upload(event){		

		$('.load').show();

		var data = new FormData();

		$.each(files, function(key, value){

			data.append(key, value);

		});

		 $.ajax({

			url: 'admin/exames/fileupload',
			type: 'POST',
			data: data,
			cache: false,
			dataType: 'json',
			processData: false, // Don't process the files
			contentType: false, // Set content type to false as jQuery will tell the server its a query string request
			success: function(data, textStatus, jqXHR){					

				fileName = data.filename;

				$('.pdf_hidden').val(fileName);				

				if(fileName == '') {				

					alert('Por favor selecione um PDF.');				

					$('.msg-status').text('Falha no upload tente novamente.');				

					return false;	

				}			

				// Success so call function to process the form
				if($('#has_assign').is(':checked')){
                	generate();				
            	} else {
            		save();
            	}

			},
			error: function(jqXHR, textStatus, errorThrown){

				$('.msg-status').text('Falha no upload tente novamente.');
				$('.load').hide();

			}

		})	

	}	

	function generate(){

		$('.msg-status').text('Convertendo pdf...');	

		//var jsonObj = jQuery.parseJSON(dataJson); 		

		$.ajax({

			url: '../../cgi-local/index.php',

			type: 'POST',

			data: {title:$('#titulo').val(), pdf:fileName},

			cache: false,

			success: function(data, textStatus, jqXHR){	

					save();

			},

			error: function(jqXHR, textStatus, errorThrown){

			}

		});		

	}

	

	function save() {	

		$('.msg-status').text('Salvando dados...');			

		$('#exame_add').submit();		

	}

	$('.salvar-exame').click(function(){	
		

		if($('#pdf').val()) {

			event.preventDefault();
			$('.msg-status').text('Aguarde, efetuando upload do arquivo...');		

		 	$('#exame_add').on('submit', upload());		

		 } else {
		 	
		 }

	});

	$( "#paciente_" ).autocomplete({

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
	    	
	        $('#email').val(ui.item.data.email);
	        $('#codigo_paciente').val(ui.item.data.codigo_paciente);
	        $('#data_de_nascimento').val(ui.item.data.data_nascimento);
	        $('#id_paciente').val(ui.item.data.id_pacientes); 
	        $('#senha').val(ui.item.data.senha); 

	        $('.senha').show(); 
	        
	    }
	});


})
