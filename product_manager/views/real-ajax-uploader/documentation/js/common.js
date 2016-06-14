$(document).ready(function(){

	
	//metro boxes
	$('.metro-box, .post-box').click(function(e) {
		$(this).metroClick(e);
		//if ($('a').index($(this)) < 3)    return false;
	});

	//pricing boxes hover
	$('.price-box').hover(
			function(){ 
				$('.price-box').removeClass('featured-box');
				$(this).addClass('featured-box');
			},
			function(){ }
	);
  
	// mobile menu
	$('#main-menu-select').change(function() {
		link = $(this).val();
		if (!link) {
			return;
		}
		document.location.href = link;
	});


	// to top button
  	$(window).scroll(function(){
  		if ($(window).scrollTop() > 100)	$('#totop').css('visibility','visible');
  		if ($(window).scrollTop() < 100)	$('#totop').css('visibility','hidden');
  		
  		if ($(window).scrollTop() > 192 )	$('#main-navigation').css('position','fixed');
  		if ($(window).scrollTop() < 192 )	$('#main-navigation').css('position','relative');
  		
  		if ($(window).scrollTop() > 300 )	$("#left_menu_doc").css('position','fixed');
  		if ($(window).scrollTop() < 300 )	$("#left_menu_doc").css('position','relative');
  	});
  
    $("#totop a").click(function(){
    	$('html, body').animate({ scrollTop: 0 }, 'slow');
    });

  // metro boxes hover
  $('.metro-box').hover(
    function(){ 
      $('.metro-box').removeClass('metro-box-hover');
      $(this).addClass('metro-box-hover');
    },
    function(){ 
      $(this).removeClass('metro-box-hover');
    }
  )

  // main dropdown menu
  $('ul#main-navigation li').hover(function(){
      $(this).children('ul').delay(20).stop().fadeIn(200);
    }, function(){
      $(this).children('ul').delay(20).stop().fadeOut(200);
  });

  // links & icons hover effects
  $('.tagcloud a, #clients-carousel a, #social a').css('opacity', '1');
  $('.tagcloud a, #clients-carousel a, #social a').hover(
    function () {
      $(this).stop().animate({ opacity: .5 }, 'normal');
    },
    function () {
      $(this).stop().animate({ opacity: 1 }, 'normal');
  });
    
  $('.over').css('opacity', '0');
  $('.over').hover(
    function () {
       $(this).stop().animate({ opacity: 1 }, 'slow');
    },
    function () {  
       $(this).stop().animate({ opacity: 0 }, 'slow');
  });


  // load captcha question for contact form
  if ($('#captcha-img').length) {
    $.get('_captcha.php?generate', function(response) {
      $('#captcha-img').html(response);
    }, 'html');
  }
  

});


(function($){
  $.fn.simpleFaq = function() {  
    return this.each(function() {
      var $this = $(this);
      $('dd', $this).hide();
      $('dt', $this).bind('click', function(){
        $(this).toggleClass('open').next().slideToggle();
      });
    });
  };
})(jQuery); // simple faq


// handle contact form AJAX response
function contactFormResponse(response) {
  if (response.responseStatus == 'err') {
    if (response.responseMsg == 'ajax') {
      alert('Error - this script can only be invoked via an AJAX call.');
    } else if (response.responseMsg == 'notsent') {
      alert('We are having some mail server issues. Please refresh the page or try again later.');
    } else {
      alert('Undocumented error. Please refresh the page and try again.');
    }
  } else if (response.responseStatus == 'ok') {
    alert('Thank you for contacting us! We\'ll get back to you ASAP.');
  } else {
    alert('Undocumented error. Please refresh the page and try again.');
  }
  
  location.reload(true);
} // contactFormResponse