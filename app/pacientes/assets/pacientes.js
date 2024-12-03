let cpf_valido = false;

$(function(){

		console.log("Autocomplete initialized");
		$( "#paciente" ).autocomplete({
		source: function (request, response) {
			console.log("Source function called with term:", request.term);
			$.ajax({
				url: 'admin/pacientes/getPacienteAjax/',
				dataType: "json",
				type: 'POST',
				data: {
					term: request.term,
				},
				success: function (data) {
					console.log("AJAX success, data received:", data);
					response($.map(data, function(item) {
						return {
							label: item.nome,
							value: item.nome,
							data: item
						};
					}));
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log("AJAX error:", textStatus, errorThrown);
				}
			});
		},
		minLength: 3,
		select: function (event, ui) {
			console.log("Item selected:", ui.item);
			// Preencher os campos do formulário com os dados do paciente selecionado
			$('#nome').val(ui.item.data.nome);
			$('#codigo_paciente').val(ui.item.data.codigo_paciente);
			$('#data_nascimento').val(ui.item.data.data_nascimento);
			$('#cpf').val(ui.item.data.cpf);
			$('#email').val(ui.item.data.email);
			$('#telefone').val(ui.item.data.telefone);
			$('#celular').val(ui.item.data.celular);
			$('#id_pacientes').val(ui.item.data.id_pacientes);
		}
	});

	$('#cpf').blur(function() {
		var cpf = $(this).val();
		if (cpf) {
			console.log("CPF blur event triggered, CPF:", cpf);
			$.ajax({
				url: 'admin/pacientes/checkCpfAjax/',
				type: 'POST',
				data: { cpf: cpf },
				success: function(response) {
					console.log("AJAX success, response received:", response);
					const cpf_existe = response.trim();
					if (cpf_existe=="true") {
						alert("Esse paciente já está cadastrado no sistema.");
						$('#cpf').val('').css('border-color', 'red');
						$('#salvar').prop('disabled', true);
					} else {
						console.log("CPF disponível");
						$('#cpf').css('border-color', '');
						$('#salvar').prop('disabled', false);}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log("AJAX error:", textStatus, errorThrown);
				}
			});
		}
	});

		$('#data_nascimento').mask('99/99/9999');
		$('#cpf').mask('999.999.999-99');
	
		function isValidCPF(cpf) {
			cpf = cpf.replace(/[^\d]+/g,'');
			if(cpf == '') return false;
			if (cpf.length != 11 || 
				cpf == "00000000000" || 
				cpf == "11111111111" || 
				cpf == "22222222222" || 
				cpf == "33333333333" || 
				cpf == "44444444444" || 
				cpf == "55555555555" || 
				cpf == "66666666666" || 
				cpf == "77777777777" || 
				cpf == "88888888888" || 
				cpf == "99999999999")
				return false;
			add = 0;
			for (i=0; i < 9; i ++)
				add += parseInt(cpf.charAt(i)) * (10 - i);
				rev = 11 - (add % 11);
				if (rev == 10 || rev == 11)
					rev = 0;
				if (rev != parseInt(cpf.charAt(9)))
					return false;
			add = 0;
			for (i = 0; i < 10; i ++)
				add += parseInt(cpf.charAt(i)) * (11 - i);
			rev = 11 - (add % 11);
			if (rev == 10 || rev == 11)
				rev = 0;
			if (rev != parseInt(cpf.charAt(10)))
				return false;
			return true;
		}
	
		function isValidDate(dateString) {
			var regEx = /^\d{2}\/\d{2}\/\d{4}$/;
			if(!dateString.match(regEx)) return false;  // Invalid format
			var d = new Date(dateString.split('/').reverse().join('-'));
			var dNum = d.getTime();

			console.log("Date:", d, "dNum:", dNum);

			if(!dNum && dNum !== 0) return false; // NaN value, Invalid date
			return d.toISOString().slice(0,10) === dateString.split('/').reverse().join('-');
		}
	
	
		$('#cpf').blur(function() {
			var cpf = $(this).val();
			if (cpf) {
				$.ajax({
					url: 'admin/pacientes/checkCpfAjax/',
					type: 'POST',
					data: { cpf: cpf },
					success: function(response) {
						if (response.exists) {
							alert("Esse paciente já está cadastrado no sistema.");
						} else {
							console.log("CPF disponível");
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log("AJAX error:", textStatus, errorThrown);
					}
				});
			}
		});
	
		$('#form1').submit(function(event) {

			event.preventDefault();

			const cpf = $('#cpf').val();
			const dataNascimento = $('#data_nascimento').val();
			let isValid = true;

			if (!isValidCPF(cpf)) {
				$('#cpf').css('border-color', 'red');
				isValid = false;
			} else {
				$('#cpf').css('border-color', '');
			}
	
			if (!isValidDate(dataNascimento)) {
				$('#data_nascimento').css('border-color', 'red');
				isValid = false;
			} else {
				$('#data_nascimento').css('border-color', '');
			}
	
			if (!isValid) {
				alert('Por favor, corrija os campos destacados em vermelho.');
			}else{
				$('#form1').unbind('submit').submit();
			}
		});



		console.log("Pacientes.js loaded");


})
