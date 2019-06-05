$(document).ready(function(){
  $('.navbar-toggle').click(function(){
    $('body').toggleClass('no-scroll');
    $(this).toggleClass('open');
    $('#main-navbar').toggleClass('open');
    $('.navbar').toggleClass('open');
  });

  $('input[name="builder[]"]').change(function(){
		var emailTo = [];
		var builder = [];

		$.each($('input[name="builder[]"]:checked'), function(){
			emailTo.push($(this).attr('data-mailto'));
			builder.push($(this).val());
		});

		$('#mailto').val(emailTo.join(', '));
		$('#builder').val(builder.join(', '));
	});

  $('#homepage-carousel').carousel({
    interval: 4000,
  });
});

document.addEventListener('wpcf7mailsent',function(event){
  location = 'http://skyranchcho.com/thank-you';
}, false);
