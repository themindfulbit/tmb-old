// theme js

$(document).ready(function(){

	$(".fancybox").fancybox();
	$('.content pre').addClass('prettyprint linenums');
	$('.content code').addClass('prettyprint');
	$('.content pre code').removeClass('prettyprint');

	// Video rescaling fix : http://www.netmagazine.com/tutorials/create-fluid-width-videos

		// Find all YouTube videos
		var $allVideos = $("iframe[src^='http://player.vimeo.com'], iframe[src^='http://www.youtube.com'], iframe[src^='http://embed.spotify.com']"),
		 
		    // The element that is fluid width
		    $fluidEl = $(".post-body");
		 
		// Figure out and save aspect ratio for each video
		$allVideos.each(function() {
		 
		  $(this)
		    .data('aspectRatio', this.height / this.width)
		 
		    // and remove the hard coded width/height
		    .removeAttr('height')
		    .removeAttr('width');
		 
		});
		 
		// When the window is resized
		$(window).resize(function() {
		 
		  var newWidth = $fluidEl.width();
		 
		  // Resize all videos according to their own aspect ratio
		  $allVideos.each(function() {
		 
		    var $el = $(this);
		    $el
		      .width(newWidth)
		      .height(newWidth * $el.data('aspectRatio'));
		 
		  });
		 
		// Kick off one resize to fix all videos on page load
		}).resize();

	// Fancybox Thumbnail Helper

	$(".fancybox-thumb").fancybox({
		prevEffect	: 'none',
		nextEffect	: 'none',
		helpers	: {
			thumbs	: {
				width	: 50,
				height	: 50
			}
		}
	});

});