$(document).ready(function(){
  $('.navbar-toggle').click(function(){
    $('body').toggleClass('no-scroll');
    $(this).toggleClass('open');
    $('#main-navbar').toggleClass('open');
    $('.navbar').toggleClass('open');
  });

  jQuery('input[name="builder[]"]').change(function(){
		var emailTo = [];
		var builder = [];

		jQuery.each(jQuery('input[name="builder[]"]:checked'), function(){
			emailTo.push(jQuery(this).attr('data-mailto'));
			builder.push(jQuery(this).val());
		});

		jQuery('#mailto').val(emailTo.join(', '));
		jQuery('#builder').val(builder.join(', '));
	});

  $('#homepage-carousel').carousel({
    interval: 4000,
  });
});
