jQuery(document).ready(function($) {
	
	// Prevent href="#" from scrolling to top of page
	$( 'a[href="#"]' ).click( function(e) {
		e.preventDefault();
	} );
	
	// Init bio "Read More"
	// Activate popovers
	$('.full-bio').popover();
	
	// Init Session description "Read More" (and Read Less)
	$('.session-read-more').click(function(e) {
		var id = e.target.id.substring(10);
		$("#entry-truncated-" + id).addClass("entry-hidden");
		$("#entry-full-" + id).removeClass("entry-hidden");
	});
	$('.session-read-less').click(function(e) {
		var id = e.target.id.substring(10);
		$("#entry-truncated-" + id).removeClass("entry-hidden");
		$("#entry-full-" + id).addClass("entry-hidden");
	});
});