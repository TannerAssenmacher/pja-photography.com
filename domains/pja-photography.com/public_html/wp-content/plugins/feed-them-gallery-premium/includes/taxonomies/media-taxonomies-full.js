jQuery( document ).ready(
	function(){

	/*	var media = wp.media;

		/!*
		// for debug : trace every event triggered in the Region controller
		var originalTrigger = wp.media.view.MediaFrame.prototype.trigger;
		wp.media.view.MediaFrame.prototype.trigger = function(){
		console.log('MediaFrame Event: ', arguments[0]);
		originalTrigger.apply(this, Array.prototype.slice.call(arguments));
		}; //

		// for Network debug
		var originalAjax = media.ajax;
		media.ajax = function( action ) {
		console.log( 'media.ajax: action = ' + JSON.stringify( action ) );
		return originalAjax.apply(this, Array.prototype.slice.call(arguments));
		};
		*!/

		/!**
		 * Extended Filters dropdown with taxonomy term selection values
		 *!/
		if ( media ) {
			jQuery.each(
				mediaTaxonomies,
				function(key,label){

					media.view.AttachmentFilters[key] = media.view.AttachmentFilters.extend(
						{
							className: key,

							createFilters: function() {
								var filters = {};

								_.each(
									mediaTerms[key] || {},
									function( term ) {

										var query = {};

										query[key] = {
											taxonomy: key,
											term_id: parseInt( term.id, 10 ),
											term_slug: term.slug
										};

										filters[ term.slug ] = {
											text: term.label,
											props: query
										};
									}
								);

								this.filters = filters;
							}

						}
					);

					/!**
					 * Replace the media-toolbar with our own
					 *!/
					var myDrop = media.view.AttachmentsBrowser;

					media.view.AttachmentsBrowser = media.view.AttachmentsBrowser.extend(
						{
							createToolbar: function() {

								media.model.Query.defaultArgs.filterSource = 'filter-media-taxonomies';

								myDrop.prototype.createToolbar.apply( this,arguments );

								this.toolbar.set(
									key,
									new media.view.AttachmentFilters[key]({
										controller: this.controller,
										model:      this.collection.props,
										priority:   -80
									}).render()
								);
							}
						}
					);

				}
			);
		}
*/
		// Save Media Term(s)!
		jQuery( document ).on(
			'click',
			'button.save-media-term',
			() => {
            let addBtn = jQuery( this )[0].activeElement,
				term_names     = [],
				newtags        = document.querySelector( 'input#new-tag-ftg-tags' ).value.replace( /  +/ig, ' ' ).replace( /\s*,\s*!/ig, ',' ).split( "," );

            for (let newtag of newtags ) {
					term_names.push( newtag.trim() );
				}

            let data = {
					action: 'save-media-terms',
					term_names: term_names,
					attachment_id: addBtn.dataset.imageid,
					taxonomy: 'ftg-tags'
				}

				// Disable Input until Ajax returns!
				document.querySelector( 'input#new-tag-ftg-tags' ).disabled;
            jQuery.ajax(
                    {
						data,
					type: 'post',
					url: ssAjax.ajaxurl,
					success: (response) => {

						let termsArray = JSON.parse( response );

						console.log(termsArray);

						for (let term of termsArray ) {
							jQuery( ".popup-ftg-tags ul.tagchecklist" ).append( '<li class="ftg-term-li" data-termli="' + term.termId + '"><button type="button" id="delete-media-term-' + term.termId + '" data-termid="' + term.termId + '" data-imageid="' + data.attachment_id + '" class="delete-media-term ntdelbutton"><span class="remove-tag-icon" aria-hidden="true"></span><span class="screen-reader-text">Remove Tag: ' + term.termName + '</span></button>&nbsp; ' + term.termName + '</li>' );
						}

						const list_count    = document.querySelector( '.popup-ftg-tags ul.tagchecklist').childNodes.length;

						if( 0 !== list_count){
							jQuery( '.popup-ftg-tags ul.tagchecklist' ).show();
							jQuery( '.ftg-tags-none' ).hide();
						}

						document.querySelector( 'input#new-tag-ftg-tags' ).removeAttribute( 'disabled' );
						document.querySelector( 'input#new-tag-ftg-tags' ).value = '';

					},
					error: () => {
						alert( 'Error, please contact us at https://www.slickremix.com/support/ for help.' )
					}
					}
                ); // end of ajax()
			}
		);

		// Delete Media Term!
		jQuery( document ).on(
			'click',
			'.delete-media-term',
			() => {
				let removeTermBtn = jQuery( this )[0].activeElement,
				list_item         = document.querySelector( '[data-termli="' + removeTermBtn.dataset.termid + '"]' ),
				check             = confirm( 'You are about to delete this tag from this image.\n\n"Cancel" to stop, "OK" to delete.' );


            jQuery( '.ftg-tags-none' ).hide();
            jQuery( '.popup-ftg-tags ul.tagchecklist' ).show();
            if ( check !== true ) {
					return false;
				}
            jQuery.ajax(
					{
						data: {
							action: 'delete-media-term',
							term_id: removeTermBtn.dataset.termid,
							attachment_id: removeTermBtn.dataset.imageid,
							taxonomy: 'ftg-tags'
						},
						type: 'post',
						url: ssAjax.ajaxurl,
						success: (response) => {
							list_item.remove();

							var list_count    = document.querySelector( '.popup-ftg-tags ul.tagchecklist').childNodes.length;

							if( 0 === list_count){
								jQuery( '.popup-ftg-tags ul.tagchecklist' ).hide();
								jQuery( '.ftg-tags-none' ).show();
							}

							console.log( 'Tag Removed!' );
						},
						error: () => {
							alert( 'Error, please contact us at https://www.slickremix.com/support/ for help.' )
						}
					}
				); // end of ajax()
			}
		);
	}
);
