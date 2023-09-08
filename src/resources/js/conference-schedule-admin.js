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

	/**
	 * Selectors used for configuration and setup.
	 *
	 * @since TBD
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		// Sessions.
		integrationList: '#wpcs-session-date',

		// Admin Sorting.
		sortingOrder: '.sponsor-order',
	};

	/**
	 * Setup Datepicker for sessions.
	 *
	 * @since TBD
	 */
	obj.setupDatePicker = function() {
		$( obj.selectors.integrationList ).datepicker( {
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true
		} );
	};

	/**
	 * Setup reordering for speakers and sponsors.
	 *
	 * @since TBD
	 */
	obj.setupReorder = function() {
		$( obj.selectors.sortingOrder ).sortable();
	};

	/**
	 * Bind the integration events.
	 *
	 * @since TBD
	 */
	obj.bindEvents = function() {};

	/**
	 * Unbind the integration events.
	 *
	 * @since TBD
	 */
	obj.unbindEvents = function() {};

	/**
	 * Handles the initialization of the admin when Document is ready.
	 *
	 * @since TBD
	 */
	obj.ready = function() {
		obj.setupDatePicker();
		obj.setupReorder();
		obj.bindEvents();
	};

	// Configure on document ready
	$( obj.ready );
})( jQuery, conferenceScheduleProAdmin );
