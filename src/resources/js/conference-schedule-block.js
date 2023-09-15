( ( wp ) => {

	const { registerBlockType } = wp.blocks;
	const { InspectorControls, ServerSideRender } = wp.editor;
	const { SelectControl, CheckboxControl, DateTimePicker } = wp.components;
	const apiFetch = wp.apiFetch;
	const { createElement: el } = wp.element;
	const { __ } = wp.i18n;

	/**
	 * Format a JavaScript Date object into a string in 'YYYY-MM-DD' format.
	 *
	 * @since TBD
	 *
	 * @param {Date} [date = new Date()] - The date to format, defaults to the current date.
	 *
	 * @return {string} The formatted date string.
	 */
	const dateFormatted = ( date = new Date() ) => {
		let dd = String( date.getDate() ).padStart( 2, '0' );
		let mm = String( date.getMonth() + 1 ).padStart( 2, '0' ); //January is 0!
		let yyyy = date.getFullYear();

		return `${yyyy}-${mm}-${dd}`;
	};

	let trackTermsArray = [];

	/**
	 * Fetch track terms from the WP API and populate the trackTermsArray with term details.
	 *
	 * @since TBD
	 */
	const fetchTrackTerms = () => {
		apiFetch( {path: "/wp/v2/session_track"} ).then( posts => {
			posts.forEach( ( val, key ) => {
				trackTermsArray.push( {id: val.id, name: val.name, slug: val.slug} );
			} );
		} );
	};

	fetchTrackTerms();

	registerBlockType( 'wpcs/schedule-block', {
		title: 'Conference Schedule',
		icon: 'schedule',
		category: 'common',
		supports: {
			align: [ 'wide', 'full' ]
		},
		attributes: {
			date: { type: 'string', default: dateFormatted() },
			color_scheme: { type: 'string', default: 'light' },
			layout: { type: 'string', default: 'table' },
			row_height: { type: 'string', default: 'match' },
			session_link: { type: 'string', default: 'permalink' },
			tracks: { type: 'string', default: null }
		},

		edit: ( props ) => {
			const { attributes, setAttributes } = props;
			const { date, color_scheme, layout, row_height, session_link, tracks } = attributes;
			let tracksArray = tracks ? tracks.split( ',' ) : [];

			const trackCheckboxes = trackTermsArray.map( ( term, index ) => {
				return el( CheckboxControl, {
					key: term.slug,
					label: term.name,
					name: 'tracks[]',
					value: term.slug,
					checked: tracksArray.includes( term.slug ),
					heading: index === 0 ? 'Tracks' : null,
					onChange: ( e ) => {
						const track = e.target.value;
						const index = tracksArray.indexOf( track );
						if ( index > -1 ) {
							tracksArray.splice( index, 1 );
						} else {
							tracksArray.push( track );
						}
						setAttributes( { tracks: tracksArray.join() } );
					}
				} );
			} );

			return [
				el( ServerSideRender, {
					block: "wpcs/schedule-block",
					attributes: attributes,
					key: 'server-side-render'
				} ),
				el( InspectorControls, { key: 'inspector-controls' },
					el( DateTimePicker, {
						currentDate: date,
						locale: 'en',
						onChange: ( value ) => setAttributes( { date: dateFormatted( new Date( value ) ) } ),
						selected: date,
						key: 'date-picker'
					} ),
					el( SelectControl, {
						label: 'Color Scheme',
						value: color_scheme,
						options: [
							{ value: 'light', label: 'Light' },
							{ value: 'dark', label: 'Dark' },
						],
						onChange: ( value ) => setAttributes( { color_scheme: value } ),
						key: 'color-scheme-select'
					} ),
					el( SelectControl, {
						label: 'Layout',
						value: layout,
						options: [
							{ value: 'table', label: 'Table' },
							{ value: 'grid', label: 'Grid' },
						],
						onChange: ( value ) => setAttributes( { layout: value } ),
						key: 'layout-select'
					} ),
					el( SelectControl, {
						label: 'Row height',
						value: row_height,
						options: [
							{ value: 'match', label: 'Match' },
							{ value: 'auto', label: 'Auto' },
						],
						onChange: ( value ) => setAttributes( { row_height: value } ),
						key: 'row-height-select'
					} ),
					el( SelectControl, {
						label: 'Session Link',
						value: session_link,
						options: [
							{ value: 'permalink', label: 'Permalink' },
							{ value: 'anchor', label: 'Anchor' },
							{ value: 'none', label: 'None' },
						],
						onChange: ( value ) => setAttributes( { session_link: value } ),
						key: 'session-link-select'
					} ),
					trackCheckboxes,
				),
			];
		},

		save: () => null

	} );
} )( window.wp );
