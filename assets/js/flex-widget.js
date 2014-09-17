/*global _:false, ajaxurl:false, wp:false */

window.FlexWidget = window.FlexWidget || {};

(function( window, $, _, wp, undefined ) {
	'use strict';

	var FlexWidget = window.FlexWidget,
		Attachment = wp.media.model.Attachment,
		frames = [],
		Control, l10n;

	// Link any localized strings.
	l10n = FlexWidget.l10n = FlexWidget.l10n || {};

	/**
	 * Control module object.
	 */
	Control = function( el, options ) {
		var defaults, selector, settings;

		this.$el = $( el );

		selector = this.$el.data( 'target' ) || '.flex-widget-control-target';
		if ( 0 === selector.indexOf( '#' ) ) {
			this.$target = $( selector );
		} else {
			// Search within the context of the control.
			this.$target = this.$el.find( selector );
		}

		defaults = {
			frame: {
				id: 'flex-widget',
				title: this.$el.data( 'title' ) || l10n.frameTitle,
				updateText: this.$el.data( 'update-text' ) || l10n.frameUpdateText,
				multiple: this.$el.data( 'select-multiple' ) || false
			},
			mediaType: this.$el.data( 'media-type' ) || 'image',
			returnProperty: this.$el.data( 'return-property' ) || 'id'
		};

		options = options || {};
		options.frame = options.frame || {};
		this.settings = _.extend( {}, defaults, options );
		this.settings.frame = _.extend( {}, defaults.frame, options.frame );

		/**
		 * Initialize a media frame.
		 *
		 * @returns {wp.media.view.MediaFrame.Select}
		 */
		this.frame = function() {
			var frame = frames[ this.settings.frame.id ];

			if ( frame ) {
				frame.control = this;
				return frame;
			}

			frame = wp.media({
				title: this.settings.frame.title,
				library: {
					type: this.settings.mediaType
				},
				button: {
					text: this.settings.frame.updateText
				},
				multiple: this.settings.frame.multiple
			});

			frame.control = this;
			frames[ this.settings.frame.id ] = frame;

			// Update the selected image in the media library based on the image in the control.
			frame.on( 'open', function() {
				var selection = this.get( 'library' ).get( 'selection' ),
					attachment, ids;

				if ( frame.control.$target.length ) {
					ids = frame.control.$target.val();
					// @todo Make sure the ids aren't already in the selection.
					if ( ids && '' !== ids && -1 !== ids && '0' !== ids ) {
						attachment = Attachment.get( ids );
						attachment.fetch();
					}
				}

				selection.reset( attachment ? [ attachment ] : [] );
			});

			// Update the control when an image is selected from the media library.
			frame.state( 'library' ).on( 'select', function() {
				var selection = this.get( 'selection' );
				frame.control.setAttachments( selection );
				frame.control.$el.trigger( 'selectionChange.flexwidget', [ selection ] );
			});

			return frame;
		};

		/**
		 * Set the control's attachments.
		 *
		 * @param {Array} attachments An array of wp.media.model.Attachment objects.
		 */
		this.setAttachments = function( attachments ) {
			var prop = this.$el.data( 'return-property' ) || 'id';

			// Insert the selected attachment ids into the target element.
			if ( this.$target.length ) {
				this.$target.val( attachments.pluck( prop ) ).trigger( 'change' );
			}
		};
	};

	_.extend( FlexWidget, {
		/**
		 * Retrieve a media selection control object.
		 *
		 * @param {Object} el HTML element.
		 *
		 * @returns {Control}
		 */
		getControl: function( el ) {
			var control, $control;

			$control = $( el ).closest( '.flex-widget-control' );
			control = $control.data( 'media-control' );

			if ( ! control ) {
				control = new Control( $control );
				$control.data( 'media-control', control );
			}

			return control;
		},

		/**
		 * Update a dropdown field with size options.
		 *
		 * @param {Object} field Dropdown field element.
		 * @param {Array} sizes
		 */
		updateSizeDropdownOptions: function( field, sizes ) {
			var $field = field,
				currentValue, name, options;

			if ( ! ( $field instanceof $ ) ) {
				$field = $( $field );
			}

			if ( sizes ) {
				_.each( sizes, function( size, key ) {
					var name = l10n.imageSizeNames[ key ] || '';
					options += '<option value="' + key + '">' + name + ' (' + size.width + '&times;' + size.height + ')</option>';
				});
			}

			if ( ! options ) {
				name = l10n.imageSizeNames['full'] || l10n.fullSizeLabel;
				options = '<option value="full">' + name + '</option>';
			}

			// Try to maintain the previously selected size if it still exists.
			currentValue = $field.val();
			$field.html( options ).val( currentValue ).removeAttr( 'disabled' );
		}
	});

	// Document ready.
	jQuery(function( $ ) {
		var $body = $( 'body' );
		
		// Open the media frame when the choose button or image are clicked.
		$body.on( 'click', '.flex-widget-control-choose, .flex-widget-form img', function( e ) {
			e.preventDefault();
			FlexWidget.getControl( this ).frame().open();
		});

		// Update the image preview and size dropdown in a widget when an image is selected.
		$body.on( 'selectionChange.flexwidget', '.flex-widget-control', function( e, selection ) {
			var $control = $( e.target ),
				$sizeField = $control.closest( '.flex-widget-form' ).find( 'select.image-size' ),
				model = selection.first(),
				sizes = model.get( 'sizes' ),
				size, image;

			if ( sizes ) {
				size = sizes['post-thumbnail'] || sizes.medium;
			}

			if ( $sizeField.length ) {
				FlexWidget.updateSizeDropdownOptions( $sizeField, sizes );
			}

			size = size || model.toJSON();
			image = $( '<img />', { src: size.url });

			$control.find( 'img' ).remove().end()
				.prepend( image )
				.addClass( 'has-image' )
				.find( 'a.flex-widget-control-choose' ).removeClass( 'button-hero' );
		});

		// Wire up the toggle checkboxes in the screen options tab.
		$( '.flex-widget-field-toggle' ).on( 'click', function() {
			var $this = $( this ),
				field = $this.val(),
				$hiddenFields = $( '.flex-widget-field-toggle:not(:checked)' );

			$( '.flex-widget-field-' + field ).toggleClass( 'is-hidden', ! $this.prop( 'checked' ) );

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'flex_widget_preferences',
					hidden: $hiddenFields.map(function() { return this.value; }).get().join( ',' ),
					nonce: FlexWidget.screenOptionsNonce
				},
				success: function( data ) {
					if ( 'nonce' in data ) {
						FlexWidget.screenOptionsNonce = data.nonce;
					}
				}
			});
		});
	});
})( this, jQuery, _, wp );
