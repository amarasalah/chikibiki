( function( $ ) {
	"use strict";

	// Theme customized media lib
	window.loftoceanMedia = {
		input: '',
		frame: '',
		frames: {},
		mediaFrame: function() {
			if ( ! this.frames[ this.frame ] ) {
				this.frames[ this.frame ] = wp.media( {
					id: 'loftocean-media-uploader',
					// frame: 'post',
					// state: 'insert',
					editing: true,
					library: {
						type : 'image' == this.frame ? 'image' : ['image', 'video']
					},
					multiple: false  // Set this to true to allow multiple files to be selected
				} )
				.on( 'select', function() {
					var media = loftoceanMedia.frames[ loftoceanMedia.frame ].state().get( 'selection' ).first().toJSON();
					loftoceanMedia.input.trigger( 'changed.loftocean.media', media );
					loftoceanMedia.input = ''; // reset input
				} )
				.on( 'open', function() {
					var selection = loftoceanMedia.frames[ loftoceanMedia.frame ].state().get( 'selection' ),
						imageID  = loftoceanMedia.input.val();
					selection.reset();
					if ( imageID && ( '' !== imageID ) ) {
						var attachment = wp.media.attachment( imageID );
						attachment.fetch();
						selection.add( attachment ? [ attachment ] : [] );
					}
				} );
			}
			return this.frames[ this.frame ];
		},
		open: function( $input, frame ) {
			this.input = $input.first();
			this.frame = frame || 'image';
			this.mediaFrame().open();
		}
	};
} ) ( jQuery );
