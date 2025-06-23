// Toggle Function for Images
jQuery.fn.ftgToggleClick = function(func1, func2) {
    var funcs = [func1, func2];
    this.data( 'toggleclicked', 0 );
    this.click(
        function() {
            var data = jQuery( this ).data();
            var tc   = data.toggleclicked;
            jQuery.proxy( funcs[tc], this )();
            data.toggleclicked = (tc + 1) % 2;
        }
    );
    return this;
};

jQuery( document ).ready(
    function () {

        jQuery( '#img1plupload-thumbs img, #img1plupload-thumbs .ft-gallery-myCheckbox span' ).ftgToggleClick(
            function ( func1, func2 ) {
                // event.preventDefault(); // stop post action
                if (jQuery( "#img1plupload-thumbs input" ).length > 0) {
                    jQuery( ".wp-core-ui .button-primary.ft-gallery-download-selection-option" ).show();
                    jQuery( '#ftg-galleries-in-album .ft-remove-gallery-to-album, #ftg-galleries-in-album .ft-add-gallery-to-album' ).attr( 'disabled', false );
                }
                jQuery( this ).parents( '.thumb' ).find( 'input:checkbox' ).attr( 'checked', 'checked' );
                jQuery( this ).parents( '.thumb' ).addClass( 'ft-gallery-checked' );
            },
            function () {
                jQuery( this ).parents( '.thumb' ).find( 'input:checkbox' ).removeAttr( 'checked' );
                jQuery( this ).parents( '.thumb' ).removeClass( 'ft-gallery-checked' );
                if ( ! jQuery( "#img1plupload-thumbs .ft-gallery-myCheckbox" ).parents( '.thumb' ).hasClass( 'ft-gallery-checked' ) ) {

                    jQuery( ".wp-core-ui .button-primary.ft-gallery-download-selection-option" ).hide();
                    jQuery( '#ftg-galleries-in-album .ft-remove-gallery-to-album, #ftg-galleries-in-album .ft-add-gallery-to-album' ).attr( 'disabled', true );
                }
            }
        );

        jQuery( '.post-type-ft_gallery_albums #fts-gallery-checkAll' ).ftgToggleClick(
            function ( func1, func2 ) {
                // event.preventDefault(); // stop post action
                jQuery( '#img1plupload-thumbs input:checkbox' ).attr( 'checked', 'checked' );
                jQuery( "#img1plupload-thumbs .ft-gallery-myCheckbox" ).parents( '.thumb' ).addClass( 'ft-gallery-checked' );
                jQuery( this ).html( 'Clear All' );
                jQuery( ".wp-core-ui .button-primary.ft-gallery-download-selection-option" ).show();
                jQuery( '#ftg-galleries-in-album .ft-remove-gallery-to-album, #ftg-galleries-in-album .ft-add-gallery-to-album' ).attr( 'disabled', false );
            },
            function () {
                jQuery( '#img1plupload-thumbs input:checkbox' ).removeAttr( 'checked' );
                jQuery( ".ft-gallery-myCheckbox" ).parents( '.thumb' ).removeClass( 'ft-gallery-checked' );
                jQuery( ".wp-core-ui .button-primary.ft-gallery-download-selection-option" ).hide();
                jQuery( '#ftg-galleries-in-album .ft-remove-gallery-to-album, #ftg-galleries-in-album .ft-add-gallery-to-album' ).attr( 'disabled', true );
                jQuery( this ).html( 'Select All' );
            }
        );

        jQuery( '.post-type-ft_gallery_albums #img2plupload-thumbs img, .post-type-ft_gallery_albums #img2plupload-thumbs .ft-gallery-myCheckbox span' ).ftgToggleClick(
            function ( func1, func2 ) {
                // event.preventDefault(); // stop post action
                if (jQuery( "#img2plupload-thumbs input" ).length > 0) {
                    jQuery( ".wp-core-ui .button-primary.ft-gallery-download-selection-option" ).show();
                    jQuery( '#ftg-albums-available .ft-remove-gallery-to-album, .ft-add-gallery-to-album' ).attr( 'disabled', false );
                }
                jQuery( this ).parents( '.thumb' ).find( 'input:checkbox' ).attr( 'checked', 'checked' );
                jQuery( this ).parents( '.thumb' ).addClass( 'ft-gallery-checked' );
            },
            function () {
                jQuery( this ).parents( '.thumb' ).find( 'input:checkbox' ).removeAttr( 'checked' );
                jQuery( this ).parents( '.thumb' ).removeClass( 'ft-gallery-checked' );
                if ( ! jQuery( "#img2plupload-thumbs .ft-gallery-myCheckbox" ).parents( '.thumb' ).hasClass( 'ft-gallery-checked' ) ) {

                    jQuery( ".wp-core-ui .button-primary.ft-gallery-download-selection-option" ).hide();
                    jQuery( '#ftg-albums-available .ft-remove-gallery-to-album, .ft-add-gallery-to-album' ).attr( 'disabled', true );
                }
            }
        );

        jQuery( '.post-type-ft_gallery_albums #fts-gallery-checkAll2' ).ftgToggleClick(
            function ( func1, func2 ) {
                // event.preventDefault(); // stop post action
                jQuery( '#img2plupload-thumbs input:checkbox' ).attr( 'checked', 'checked' );
                jQuery( "#img2plupload-thumbs .ft-gallery-myCheckbox" ).parents( '.thumb' ).addClass( 'ft-gallery-checked' );
                jQuery( this ).html( 'Clear All' );
                jQuery( ".wp-core-ui .button-primary.ft-gallery-download-selection-option" ).show();
                jQuery( '#ftg-albums-available .ft-remove-gallery-to-album, .ft-add-gallery-to-album' ).attr( 'disabled', false );
            },
            function () {
                jQuery( '#img2plupload-thumbs input:checkbox' ).removeAttr( 'checked' );
                jQuery( "#img2plupload-thumbs .ft-gallery-myCheckbox" ).parents( '.thumb' ).removeClass( 'ft-gallery-checked' );
                jQuery( ".wp-core-ui .button-primary.ft-gallery-download-selection-option" ).hide();
                jQuery( '#ftg-albums-available .ft-remove-gallery-to-album, .ft-add-gallery-to-album' ).attr( 'disabled', true );
                jQuery( this ).html( 'Select All' );
            }
        );

    }
);

function ft_gallery_add_galleries_to_album(album_id){

    // Selected Media
    var addselectedgalleries = [];
    jQuery( '#ftg-tab-content1 li.out-album div.ft-gallery-select-thumbn input[type=checkbox]' ).each(
        function () {
            if (jQuery( this ).attr( 'checked' )) {
                addselectedgalleries.push( jQuery( this ).attr( 'rel' ) );
            }
        }
    );

    if (addselectedgalleries.length) {
        addselectedgalleries = JSON.stringify( addselectedgalleries );
    }

    jQuery.ajax(
        {
            data: {
                action: "ft_gallery_add_galleries_to_album",
                AlbumID: album_id,
                addselectedGalleries: addselectedgalleries
            },
            type: 'POST',
            async: true,
            url: ftgallerytoWooAjax.ajaxurl,
            beforeSend: function () {
                console.log( 'Selected Galleries: ' + addselectedgalleries );
                jQuery( '.ft-gallery-notice' ).empty().removeClass( 'ftg-block' );
                jQuery( '.ft-gallery-notice' ).removeClass( 'updated' ).addClass( 'ftg-block' );
                jQuery( '.ft-gallery-notice' ).prepend( '<div class="fa fa-cog fa-spin fa-3x fa-fw ft-gallery-loader"></div>' );
            },
            success: function (response) {
                console.log( 'Well Done and got this from sever: ' + response );

                // 'Woocommerce Product created from Image(s)! '
                jQuery( '.ft-gallery-notice' ).html( response );
                jQuery( '.ft-gallery-notice' ).addClass( 'updated' );
                jQuery( '.ft-gallery-notice' ).append( '<div class="ft-gallery-notice-close"></div>' );

                jQuery( '.ft_gallery_download_button' ).removeAttr( 'disabled' ).removeClass( 'ft_gallery_download_button_loading' );

                return false;
            }
        }
    ); // end of ajax()
    return false;
} // end of form.submit



function ft_gallery_delete_galleries_from_album(album_id){
    // Selected Media
    var deleteselectedgalleries = [];
    jQuery( '#ftg-tab-content1 li.in-album div.ft-gallery-select-thumbn input[type=checkbox]' ).each(
        function () {
            if (jQuery( this ).attr( 'checked' )) {
                deleteselectedgalleries.push( jQuery( this ).attr( 'rel' ) );
            }
        }
    );

    if (deleteselectedgalleries.length) {
        deleteselectedgalleries = JSON.stringify( deleteselectedgalleries );
    }

    jQuery.ajax(
        {
            data: {
                action: "ft_gallery_delete_galleries_from_album",
                AlbumID: album_id,
                deleteselectedGalleries: deleteselectedgalleries
            },
            type: 'POST',
            async: true,
            url: ftgallerytoWooAjax.ajaxurl,
            beforeSend: function () {
                jQuery( '.ft-gallery-notice' ).empty().removeClass( 'ftg-block' );
                jQuery( '.ft-gallery-notice' ).removeClass( 'updated' ).addClass( 'ftg-block' );
                jQuery( '.ft-gallery-notice' ).prepend( '<div class="fa fa-cog fa-spin fa-3x fa-fw ft-gallery-loader"></div>' );
            },
            success: function (response) {
                console.log( 'Well Done and got this from sever: ' + response );

                // 'Woocommerce Product created from Image(s)! '
                jQuery( '.ft-gallery-notice' ).html( response );
                jQuery( '.ft-gallery-notice' ).addClass( 'updated' );
                jQuery( '.ft-gallery-notice' ).append( '<div class="ft-gallery-notice-close"></div>' );

                jQuery( '.ft_gallery_download_button' ).removeAttr( 'disabled' ).removeClass( 'ft_gallery_download_button_loading' );

                return false;
            }
        }
    ); // end of ajax()
    return false;
} // end of form.submit
