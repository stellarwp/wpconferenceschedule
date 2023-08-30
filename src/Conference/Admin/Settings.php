<?php
/**
 * Organizes Conference Schedule Settings.
 *
 * @since   TBD
 * @package TEC\Conference\Admin
 */

namespace TEC\Conference\Admin;

/**
 * Class Settings
 *
 * Handles the settings for the conference schedule.
 *
 * @since   TBD
 * @package TEC\Conference\Admin
 */
class Settings extends Menu {

	/**
	 * Registers options page for settings.
	 *
	 * @since TBD
	 */
	public function options_page() {
		add_submenu_page(
			$this->get_menu_slug(),
			'Settings',
			'Settings',
			'manage_options',
			'wp-conference-schedule-settings',
			[ $this, 'options_page_html' ]
		);
	}

	/**
	 * Initializes settings and fields.
	 *
	 * @since TBD
	 */
	public function init() {
		// Register a info section in the "wpcs" page.
		add_settings_section( 'wpcs_section_info', __( 'Share The Love!', 'wpcs' ), [ $this, 'section_info_cb' ], 'wpcs' );

		// Register a settings section in the "wpcs" page.
		add_settings_section( 'wpcs_section_settings', __( 'General Settings', 'wpcs' ), [ $this, 'section_settings_cb' ], 'wpcs' );

		// Register byline setting for "wpcs" page.
		register_setting( "wpcs", "wpcs_field_byline" );

		// Register byline field in the "wpcs_section_info" section, inside the "wpcs" page.
		add_settings_field( "wpcs_field_byline", "Show The WP Conference Schedule link", [ $this, "field_byline_cb" ], "wpcs", "wpcs_section_info" );

		// Register schedule page URL setting for "wpcs" page.
		register_setting( "wpcs", "wpcs_field_schedule_page_url" );

		// Register schedule page URL field in the "wpcs_section_info" section, inside the "wpcs" page.
		add_settings_field( "wpcs_field_schedule_page_url", "Schedule Page URL", [ $this, "field_schedule_page_url_cb" ], "wpcs", "wpcs_section_settings" );

		// Register schedule page URL setting for "wpcs" page.
		register_setting( "wpcs", "wpcsp_field_speakers_page_url", [ $this, "sanitize_field_speakers_page_url" ] );

		// Register schedule page URL field in the "wpcs_section_info" section, inside the "wpcs" page.
		add_settings_field( "wpcsp_field_speakers_page_url", "Speakers Page URL", [ $this, "field_speakers_page_url_cb" ], "wpcs", "wpcs_section_settings" );

		// Register schedule page URL setting for "wpcs" page.
		register_setting( "wpcs", "wpcsp_field_sponsor_page_url", [ $this, "sanitize_field_sponsor_page_url" ] );

		// Register schedule page URL field in the "wpcs_section_info" section, inside the "wpcs" page.
		add_settings_field( "wpcsp_field_sponsor_page_url", "Sponsor URL Redirect", [ $this, "field_sponsor_page_url_cb" ], "wpcs", "wpcs_section_settings" );
	}

	/**
	 * Settings section callback for sharing information.
	 *
	 * @since TBD
	 *
	 * @param array $args Arguments passed to the callback.
	 */
	public function section_info_cb( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>">
			<?php esc_html_e( 'Show your thanks to WP Conference Schedule for creating an amazing free plugin by giving them recognition with a small text-only link at the bottom of your conference schedule.', 'wpcs' ); ?>
		</p>
		<?php
	}

	/**
	 * Settings section callback for general settings.
	 *
	 * @since TBD
	 *
	 * @param array $args Arguments passed to the callback.
	 */
	public function section_settings_cb( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>">
			<?php esc_html_e( '', 'wpcs' ); ?>
		</p>
		<?php
	}

	/**
	 * Byline field callback.
	 *
	 * @since TBD
	 */
	public function field_byline_cb() {
		?>
		<input type="checkbox" name="wpcs_field_byline" value="1" <?php checked( 1, get_option( 'wpcs_field_byline' ), true ); ?> />
		<label for="wpcs_field_byline"></label>
		<?php
	}

	/**
	 * Schedule page URL field callback.
	 *
	 * @since TBD
	 */
	public function field_schedule_page_url_cb() {
		?>
		<input type="text" name="wpcs_field_schedule_page_url" value="<?php echo get_option( 'wpcs_field_schedule_page_url' ); ?>" style="width: 450px;">
		<p class="description">The URL of the page that your conference schedule is embedded on.</p>
		<?php
	}

	/**
	 * Speakers page URL field callback.
	 *
	 * @since TBD
	 */
	public function field_speakers_page_url_cb() {
		?>
		<input type="text" name="wpcsp_field_speakers_page_url" value="<?php echo get_option( 'wpcsp_field_speakers_page_url' ); ?>" style="width: 450px;">
		<p class="description">The URL of the page that your speakers are embedded on.</p>
		<?php
	}

	/**
	 * Sanitize the speakers page URL value before being saved to database.
	 *
	 * @since TBD
	 *
	 * @param string $speakers_page_url The URL for the speakers page.
	 *
	 * @return string Sanitized URL.
	 */
	public function sanitize_field_speakers_page_url( $speakers_page_url ) {
		return sanitize_text_field( $speakers_page_url );
	}

	/**
	 * Sponsor page url callback.
	 *
	 * @since TBD
	 */
	public function field_sponsor_page_url_cb() {
		$sponsor_url = get_option( 'wpcsp_field_sponsor_page_url' );
		?>
		<select name="wpcsp_field_sponsor_page_url" id="sponsors_url">
			<option value="sponsor_page" <?php if ( $sponsor_url == "sponsor_page" ) {
				echo "selected";
			} ?>>Redirect to Sponsor Page
			</option>
			<option value="sponsor_site" <?php if ( $sponsor_url == "sponsor_site" ) {
				echo "selected";
			} ?>>Redirect to Sponsor Site
			</option>
		</select>
		<p class="description">The location to redirect sponsor links to on the session single page.</p>
		<?php
	}

	/**
	 * Sanitize the sponsor page URL value before being saved to database.
	 *
	 * @since TBD
	 *
	 * @param string $redirect The redirect option for the sponsor link.
	 *
	 * @return string Sanitized redirect option.
	 */
	public function sanitize_field_sponsor_page_url( $redirect ) {
		if (
			$redirect === 'sponsor_page'
			|| $redirect === 'sponsor_site'
		) {
			return $redirect;
		}

		return '';
	}

	/**
	 * Displays the settings form.
	 *
	 * @since TBD
	 */
	public function options_page_html() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Check if the user have submitted the settings.
		// WordPress will add the "settings-updated" $_GET parameter to the url.
		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'wpcs_messages', 'wpcs_message', __( 'Settings Saved', 'wpcs' ), 'updated' );
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				// output security fields for the registered setting "wpcs"
				settings_fields( 'wpcs' );
				// output setting sections and their fields
				// (sections are registered for "wpcs", each field is registered to a specific section)
				do_settings_sections( 'wpcs' );
				// output save settings button
				submit_button( 'Save Settings' );
				?>
			</form>
		</div>
		<?php
	}
}
