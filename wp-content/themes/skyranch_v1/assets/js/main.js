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
  location = 'http://skyranchco.com/thank-you/';
}, false);

// Select all links with hashes
$('a[href*="#"]')
  // Remove links that don't actually link to anything
  .not('[href="#"]')
  .not('[href="#0"]')
  .click(function(event) {
    // On-page links
    if (
      location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname
    ) {
      // Figure out element to scroll to
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
      // Does a scroll target exist?
      if (target.length) {
        // Only prevent default if animation is actually gonna happen
        event.preventDefault();
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 1000, function() {
          // Callback after animation
          // Must change focus!
          var $target = $(target);
          $target.focus();
          if ($target.is(":focus")) { // Checking if the target was focused
            return false;
          } else {
            $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
            $target.focus(); // Set focus again
          }
        });
      }
    }
  });
