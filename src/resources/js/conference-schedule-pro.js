/* eslint-disable template-curly-spacing */
/**
 * Configures Conference Schedule Pro Object.
 *
 * @since TBD
 *
 * @type {PlainObject}
 */
const conferenceSchedulePro = {};

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
		// Tabs.
		scheduleTabContainer: '.wpcsp-tabs',
		scheduleTabRole: '[role="tab"]',
		scheduleTabRoleList: '[role="tablist"]',
	};

	/**
	 * Setup Tabs.
	 *
	 * @since TBD
	 */
	obj.setupTabs = function() {
		const container = document.querySelector( obj.selectors.scheduleTabContainer );
		if ( !container ) {
			return;
		}

		const tabs = container.querySelectorAll( obj.selectors.scheduleTabRole );
		const tabList = container.querySelector( obj.selectors.scheduleTabRoleList );

		// Add a click event handler to each tab
		tabs.forEach( tab => {
			tab.addEventListener( 'click', obj.changeTabs );
		} );

		// Enable arrow navigation between tabs in the tab list
		let tabFocus = 0;

		tabList.addEventListener( 'keydown', e => {
			// Move right
			if ( e.keyCode === 39 || e.keyCode === 37 ) {
				tabs[tabFocus].setAttribute( 'tabindex', -1 );
				if ( e.keyCode === 39 ) {
					tabFocus++;
					// If we're at the end, go to the start
					if ( tabFocus >= tabs.length ) {
						tabFocus = 0;
					}
					// Move left
				} else if ( e.keyCode === 37 ) {
					tabFocus--;
					// If we're at the start, move to the end
					if ( tabFocus < 0 ) {
						tabFocus = tabs.length - 1;
					}
				}

				tabs[tabFocus].setAttribute( 'tabindex', 0 );
				tabs[tabFocus].focus();
			}
		} );
	};

	/**
	 * Change tabs.
	 *
	 * @since TBD
	 *
	 * @param event {Event} The event object.
	 */
	obj.changeTabs = function( event ) {
		const target = event.target;
		const parent = target.parentNode;
		const grandparent = parent.parentNode;

		// Remove all current selected tabs
		parent
			.querySelectorAll( '[aria-selected="true"]' )
			.forEach( t => t.setAttribute( 'aria-selected', false ) );

		// Set this tab as selected
		target.setAttribute( 'aria-selected', true );

		// Hide all tab panels
		grandparent
			.querySelectorAll( '[role="tabpanel"]' )
			.forEach( p => p.setAttribute( 'hidden', true ) );

		// Show the selected panel
		grandparent.parentNode
			.querySelector( `#${target.getAttribute( 'aria-controls' )}` )
			.removeAttribute( 'hidden' );

		// Set query parameter
		if ( 'URLSearchParams' in window ) {
			var searchParams = new URLSearchParams( window.location.search )
			searchParams.set( "wpcs-tab", target.getAttribute( 'data-id' ) );
			var newRelativePathQuery = window.location.pathname + '?' + searchParams.toString();
			history.pushState( null, '', newRelativePathQuery );
		}
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
		obj.setupTabs();
		obj.bindEvents();
	};

	// Configure on document ready
	$( obj.ready );
})( jQuery, conferenceSchedulePro );
