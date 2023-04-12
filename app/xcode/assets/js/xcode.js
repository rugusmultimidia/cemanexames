// JavaScript Document

$(function(){
	
			
	var global_fields = new Array();
	
	
	$('#field_name').blur(function(){
		
		var field_name = $('#field_name').val();
		
		var _field_machine_name = $.remove_especial_chars(field_name);
		
		$('#field_machine_name').val(_field_machine_name);
		
	});
	
	$('.add_field').click(function(){
		
		var field_name = $('#field_name').val();
		
		var field_type = $('#field_type').val();
		
		var field_machine_name = $('#field_machine_name').val();

		if(field_name == "") {
			$('#field_name').addClass('error');
			return false;
		}
		
		if(jQuery.inArray( field_machine_name, global_fields ) >= 0) {

			alert('Campo '+field_name+' ja existe!');
			return false;
			
		}

		global_fields[global_fields.length] = field_machine_name;
		
		var options = '';
		options = '<input type="hidden" class="'+field_machine_name+'_options" name="field['+field_machine_name+'][options]['+field_type+']" value="" />';
		
		var grid_system = '<td><select name="field['+field_machine_name+'][grid]" id="grid_system" class="form-control"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12" selected="selected">12</option></select></td>'
		
		
		$('.table_content').append('<tr id="'+field_machine_name+'">'+options+
		'<input type="hidden" class="input_field_name" name="field['+field_machine_name+'][field_name]" value="'+field_name+'" />'+
		'<input type="hidden" class="machine_name" name="field['+field_machine_name+'][field_machine_name]" value="'+field_machine_name+'" />'+
		'<input type="hidden" class="field_type" name="field['+field_machine_name+'][field_type]" value="'+field_type+'" />'+
		'<input type="hidden" class="field_multiple_values" name="field['+field_machine_name+'][field_multiple_values]" value="false" />'+
		'<td class="field_name">'+field_name+'</td><td>'+field_type+'</td>'+
		grid_system+
		'<td width="10%" style="text-align:center;"><input name="field['+field_machine_name+'][colum_list]" type="checkbox" value="true" /></td>'+
		'<td style="text-align:center;"><a href="javascript:void(0)" class="del_row"><i class="fa fa-ban"></i></a></td>'+
		'<td style="text-align:center;"><a href="javascript:void(0)" class="call_modal_field_options" data-machine-name="'+field_machine_name+'" data-name="'+field_name+'" title="Options"><i class="fa fa-edit"></i></a></td>'+
		'<td style="text-align:center;"><a href="javascript:void(0)" class="drag-handle" title="Drag to re-order"><i class="fa fa-arrows"></i></td></tr>');

		//$('#field_name').val('');
		
		
		if(field_type == 'select' || field_type == 'taxonomy' || field_type == 'entity' || field_type == 'radio' || field_type == 'markup' || field_type == 'checkbox') {	
			
			$('#'+field_machine_name).find('.call_modal_field_options').trigger('click');
		}
		
	});
	

	//open modal to add field options
	$(document).on('click', '.call_modal_field_options', function(){	  	 
		
		var html = '';
		var name = $(this).attr('data-name');
		var machine_name = $(this).attr('data-machine-name');
		var field_type = $('#'+machine_name).find('.field_type').val();	

		
		var field_controller = '<div class="col-xs-12"><label>Field Name</label><input type="text" value="'+name+'" class="form-control" id="'+machine_name+'"></div>';
		$('.modal-field').html(field_controller);
		
		if(field_type == 'textfield') {

			$('.modal-title').text('Opções de campo Texto');				
			$('.modal .modal-body').html(html);	
		}
		
		if(field_type == 'image') {
				
			$('.modal-title').text('Opções de campo Imagem');				
			$('.modal .modal-body').html(html);	
		}
		
		if(field_type == 'taxonomy') {
		
			html = $.post('admin/xcode/getOptions/',{machine_name:machine_name, field_type:field_type}, function(data){
			
				html = data;
				
				$('.modal-title').text('Opções de Taxonomia');
				
				$('.modal .modal-body').html(html);
			
			});
		}
		
		if(field_type == 'select'){		
			$.post('admin/xcode/getOptions/',{machine_name:machine_name, field_type:field_type}, function(data){
			
				html = data;

				$('.modal-title').text('Opções');
				
				$('.modal .modal-body').html(html);
				
				var current_value = $('.'+machine_name+'_options').val();
				$('.value_options').val(current_value);
			
			});
		}
		
		if(field_type == 'radio'){		
			$.post('admin/xcode/getOptions/',{machine_name:machine_name, field_type:field_type}, function(data){
			
				html = data;

				$('.modal-title').text('Opções');
				
				$('.modal .modal-body').html(html);
				
				var current_value = $('.'+machine_name+'_options').val();
				$('.value_options').val(current_value);
			
			});
		}
		
		if(field_type == 'checkbox'){		
			$.post('admin/xcode/getOptions/',{machine_name:machine_name, field_type:field_type}, function(data){
			
				html = data;

				$('.modal-title').text('Opções');
				
				$('.modal .modal-body').html(html);
				
				var current_value = $('.'+machine_name+'_options').val();
				$('.value_options').val(current_value);
			
			});
		}	
		
		if(field_type == 'markup'){		
			$.post('admin/xcode/getOptions/',{machine_name:machine_name, field_type:field_type}, function(data){
			
				html = data;

				$('.modal-title').text('Opções');
				
				$('.modal .modal-body').html(html);
				
				var current_value = $('.'+machine_name+'_options').val();
				$('.value_options').val(current_value);
			
			});
		}	
		
		if(field_type == 'entity'){			
			
			var current_value = $('.'+machine_name+'_options').val();		
			
			var field_multiple_values  = $('#'+machine_name+' .field_multiple_values').val();		
		
			$.post('admin/xcode/getOptions/',{machine_name:machine_name, field_type:field_type, current_value:current_value,field_multiple_values:field_multiple_values }, function(data){
			
				html = data;
				
				$('.modal-title').text('Opções');
				
				$('.modal .modal-body').html(html);				
				
				//$('.value_options').val(current_value);
			
			});
		}	

			
		$('.modal').modal({
			keyboard: true
		});

		
	});
	
	//add options to row
	$('.modal-footer .btn-primary').on('click', function(){	  	 
	
			var options = $('.value_options');			
			var field_name_value = $('.modal-field input').val();
			var machine_name = $('.modal-field input').attr('id');

			if(jQuery("#field_type_entity_value").length) {
			
				if(jQuery("#field_type_entity_value").find(':selected').attr('data-controller') != undefined) {
					
					var values = jQuery("#field_type_entity_value").find(':selected').attr('data-controller')+'@'+options.val()+'@'+jQuery('#field_type_entity').val();				
					
				} else {
					alert('selecione um campo');
					return false;
				}
			
			} else {
			
				var values = options.val();
			
			}
			
			if(jQuery('#multiple:checked').val() == "true") {
				$('#'+machine_name+' .field_multiple_values').val('true');						
			} else {
				$('#'+machine_name+' .field_multiple_values').val('false');				
			}
			
			
			//alter the field name
			//var new_machine_name = $.remove_especial_chars(field_name_value);	
			
			//new_machine_name = new_machine_name.substr(0, 20);	
			
			$('#'+machine_name+' .field_name').text(field_name_value);
			$('#'+machine_name+' .input_field_name').val(field_name_value);			
			//$('#'+machine_name+' .machine_name').val(new_machine_name);
			//$('#'+machine_name+' .call_modal_field_options').attr('data-machine-name', new_machine_name);
			$('#'+machine_name+' .call_modal_field_options').attr('data-name', field_name_value);
			
			
			//old machine name
			if($('#'+machine_name+' .input_machine_name_old').val() == ''){
				//$('#'+machine_name+' .input_machine_name_old').val(machine_name);
			}
			
			//adiciona os valores do campo text 
			$('.'+machine_name+'_options').val(values);
			
			$('.modal').modal('toggle');
			
			//altera o nome do id da row
			//$('tr#'+machine_name).attr('id', new_machine_name); 
			
	});	
	
	
	//entity field	
	$(document).on('change','#field_type_entity', function(){	  	 
	
			var value = $(this).val();
			
			$.post('admin/xcode/getControllerData/',{id:value}, function(data){
			
				$('#field_type_entity_value').html(data);
			
			});
			
			
	});	
	
	
	//delete fields rows
	
	$(document).on('click', '.del_row', function(){		
		
		var field_machine_name = $(this).closest('tr').find('.machine_name').val();
		
		global_fields.splice(global_fields.indexOf(field_machine_name), 1);

		$(this).closest('tr').remove();
		
		var storage = $(this).attr('data-storage');
		
		if(storage) {
		
			$('form').append('<input type="hidden" class="deleted_fields" name="field['+field_machine_name+'][deleted]" value="'+field_machine_name+'">');
		
		}
	
	});

	if($("table.draggable").length > 0) {
		 $("table.draggable").rowSorter({
			handler: ".drag-handle",
			onDrop: function(tbody, row, new_index, old_index) {
				//$("#log").html(old_index + ". row moved to " + new_index);
			}
		});
	}
	
	$.remove_especial_chars = function(s){

			var r=s.toLowerCase();
            r = r.replace(new RegExp("\\s", 'g'),"_");
            r = r.replace(new RegExp("[àáâãäå]", 'g'),"a");
            r = r.replace(new RegExp("æ", 'g'),"ae");
            r = r.replace(new RegExp("ç", 'g'),"c");
            r = r.replace(new RegExp("[èéêë]", 'g'),"e");
            r = r.replace(new RegExp("[ìíîï]", 'g'),"i");
            r = r.replace(new RegExp("ñ", 'g'),"n");                            
            r = r.replace(new RegExp("[òóôõö]", 'g'),"o");
            r = r.replace(new RegExp("œ", 'g'),"oe");
            r = r.replace(new RegExp("[ùúûü]", 'g'),"u");
            r = r.replace(new RegExp("[ýÿ]", 'g'),"y");
            r = r.replace(new RegExp("\\W", 'g'),"");
            return r;		
	}
	
	$('input').focus(function(){
		
		$(this).removeClass('error');
	});
	
})