/* eslint-disable template-curly-spacing */
/**
 * Configures Conference Schedule Pro Admin Object.
 *
 * @since TBD
 *
 * @type {PlainObject}
 */
const conferenceScheduleProAdmin= {};

(function( $, obj ) {
	'use-strict';
	const $document = $( document );

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since TBD
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		integrationContainer: '.tec-automator-settings',
		integrationAdd: '.tec-automator-settings__add-api-key-button',
		messageWrap: '.tec-automator-settings-message__wrap',

		// Individual Keys.
		integrationList: '.tec-automator-settings-items__wrap',

	};

	/**
	 * Setup Datepicker for sessions.
	 *
	 * @since TBD
	 */
	obj.setupDatePicker = function() {
		$( '#wpcs-session-date' ).datepicker( {
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true
		} );
	};

	/**
	 * Bind the integration events.
	 *
	 * @since TBD
	 */
	obj.bindEvents = function() {
/*		$document
			.on( 'click', obj.selectors.endpointEnableButton, obj.handleEndpointAction );*/
	};

	/**
	 * Unbind the integration events.
	 *
	 * @since TBD
	 */
	obj.unbindEvents = function() {};

	/**
	 * Handles the initialization of the admin when Document is ready
	 *
	 * @since TBD
	 */
	obj.ready = function() {
		obj.setupDatePicker();
		obj.bindEvents();
	};

	// Configure on document ready
	$( obj.ready );
})( jQuery, conferenceScheduleProAdmin );
