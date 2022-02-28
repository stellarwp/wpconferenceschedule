( function(wp) {

	var registerBlockType = wp.blocks.registerBlockType;
	var InspectorControls = wp.editor.InspectorControls;
	var TextControl = wp.components.TextControl;
	var SelectControl = wp.components.SelectControl;
	var CheckboxControl = wp.components.CheckboxControl;
	var withState = wp.compose.withState;
	var el = wp.element.createElement;
	var ServerSideRender = wp.components.ServerSideRender;
	var DatePicker = wp.components.DateTimePicker;
	var __ = wp.i18n.__;
	//var RichText = wp.editor.RichText;
	//var AlignmentToolbar = wp.editor.AlignmentToolbar;
	//var BlockControls = wp.editor.BlockControls;

	function wpcs_dateFormatted(date){
		if(date == null){
			var date = new Date();
		} else {
			var date = new Date(date);
		}
		var dd = String(date.getDate()).padStart(2, '0');
		var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
		var yyyy = date.getFullYear();

		date = yyyy + '-' + mm + '-' + dd;
		return date;
	}

	const trackTermsArray = [];
	wp.apiFetch({path: "/wp/v2/session_track"}).then(posts => {
		posts.forEach(function(val, key){
			trackTermsArray.push({id:val.id,name:val.name,slug:val.slug});
		});
	});
	
	registerBlockType( 'wpcs/schedule-block', {
		title: 'Conference Schedule',
		icon: 'schedule',
		category: 'common',
		supports: {
			align: true,
			align: ['wide','full']
		},
		attributes: {
			date: {type: 'string', default: wpcs_dateFormatted(null)},
			color_scheme: {type: 'string', default: 'light'},
			layout: {type: 'string', default: 'table'},
			row_height: {type: 'string', default: 'match'},
			session_link: {type: 'string', default: 'permalink'},
			tracks: {type: 'string', default: null}
		},

		edit: function( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			//var setState = props.setState;
			//var status = props.status;

			var date = props.attributes.date;
			var color_scheme = props.attributes.color_scheme;
			var layout = props.attributes.layout;
			var row_height = props.attributes.row_height;
			var session_link = props.attributes.session_link;
			var tracks = props.attributes.tracks;
			if(tracks != null){
				var tracksArray = tracks.split(',');
			}else{
				var tracksArray = [];
			}

			trackCheckboxes = [];
			for(i=0;i<trackTermsArray.length;i++){
				if(i==0){
					var heading = 'Tracks';
				}else{
					var heading = null;
				}
   			trackCheckboxes.push(
					el(CheckboxControl, {
						key: trackTermsArray[i].slug,
						label: trackTermsArray[i].name,
						name: 'tracks[]',
						value: trackTermsArray[i].slug,
						checked: tracksArray.includes(trackTermsArray[i].slug),
						heading: heading,
						onChange: function(e){
							var track = event.target.value;
							var index = tracksArray.indexOf(track);
							if(index > -1){
								tracksArray.splice(index, 1);
							}else{
								tracksArray.push(track);
							}
							setAttributes({tracks: tracksArray.join()});
						}
					}),
				);
  		}

			return [
				el(ServerSideRender, {
						block: "wpcs/schedule-block",
						attributes: props.attributes
				} ),
				el(InspectorControls, {},
					el(DatePicker, {
							currentDate : date,
							locale: 'en',
							onChange: function(value){
								setAttributes({date: wpcs_dateFormatted(value)});
							},
							selected: date
						}
					),
					el(SelectControl, {
						label: 'Color Scheme',
						value: color_scheme,
						options: [
							{value: 'light', label: 'Light'},
							{value: 'dark', label: 'Dark'},
						],
						onChange: function(value){
							setAttributes({color_scheme: value});
						}
					}),
					el(SelectControl, {
						label: 'Layout',
						value: layout,
						options: [
							{value: 'table', label: 'Table'},
							{value: 'grid', label: 'Grid'},
						],
						onChange: function(value){
							setAttributes({layout: value});
						}
					}),
					el(SelectControl, {
						label: 'Row height',
						value: row_height,
						options: [
							{value: 'match', label: 'Match'},
							{value: 'auto', label: 'Auto'},
						],
						onChange: function(value){
							setAttributes({row_height: value});
						}
					}),
					el(SelectControl, {
						label: 'Session Link',
						value: session_link,
						options: [
							{value: 'permalink', label: 'Permalink'},
							{value: 'anchor', label: 'Anchor'},
							{value: 'none', label: 'None'},
						],
						onChange: function(value){
							setAttributes({session_link: value});
						}
					}),
					trackCheckboxes,
				),
			];
		},

		save: function(props) {
			return null
		}

	});
}(window.wp));