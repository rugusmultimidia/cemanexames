// JavaScript Document



$(function(){			

	$('.login-link-admin').click(function(){
	

		var user = $('.user').val();
		var pass = $('.pass').val();		

		$('.message').hide();			

		$('.spinner').fadeIn('fast');
		

		$.post(window.location.href,{user:user, pass:pass}, function(data){
					

					if(data == true){						

						window.location.href = 'http://examesceman/admin/';					

					} else {					

						$('.spinner').delay(500).fadeOut('fast', function(){							

							$('.message').html(data).fadeIn('fast');						

						});						

					}					

		});	

	});

	$('.login-link').click(function(){
	

		var user = $('.user').val();
		var pass = $('.pass').val();		

		$('.message').hide();			

		$('.spinner').fadeIn('fast');
		

		$.post(window.location.href,{user:user, pass:pass}, function(data){
					

					if(data == true){						

						window.location.href = '';					

					} else {					

						$('.spinner').delay(500).fadeOut('fast', function(){							

							$('.message').html(data).fadeIn('fast');						

						});						

					}					

		});	

	});
	

})
