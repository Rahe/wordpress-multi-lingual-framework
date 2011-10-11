jQuery(function () {
	/* used in plugin settings page */
	jQuery('.mlf_options').slideUp();

	jQuery('.mlf_section h3').click(function(){
		var el = jQuery(this);
		if( el.parent().next( '.mlf_options' ).css( 'display' )=='none' ) {	
				el.removeClass('inactive').addClass('active');
				el.children('img').removeClass('inactive').addClass('active' );
		} else {
			el.removeClass( 'active' ).addClass( 'inactive' );
			el.children( 'img' ).removeClass( 'active' ).addClass( 'inactive' );
		}
		
		el.parent().next('.mlf_options').slideToggle('slow');
	});

	/* used to present metabox of other versions of post */

	jQuery( ".translation_content" ).hide(); //Hide all content		
	jQuery( "ul.translation_tabs li:first" ).addClass("active").show(); //Activate first tab
	jQuery( ".translation_content:first").show(); //Show first tab content	

	jQuery( "ul.translation_tabs li").click( function() {
		var el = jQuery( this );
		jQuery("ul.translation_tabs li" ).removeClass( "active" ); //Remove any "active" class
		el.addClass( "active" ); //Add "active" class to selected tab
		jQuery( ".translation_content" ).hide(); //Hide all tab content

		var activeTab = el.find( "a" ).attr( "href" ); //Find the href attribute value to identify the active tab + content
		jQuery( activeTab ).fadeIn(); //Fade in the active ID content
		return false;
	});
});
