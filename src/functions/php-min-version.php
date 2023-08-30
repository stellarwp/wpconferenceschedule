<?php
/********************************************************************************
 *
 *
 * IMPORTANT NOTE
 *
 * This file uses a global namespace since we will share it on all plugins.
 *
 *
 ********************************************************************************/

// Only include these methods if they are not available already.
if ( ! function_exists( 'tribe_get_php_min_version' ) ) :
	/**
	 * Compares a given version to the required PHP version.
	 *
	 * Normally we use Constant: PHP_VERSION.
	 *
	 * @since  TBD
	 *
	 * @param  string $version  Which PHP version we are checking against.
	 *
	 * @return bool If given version is not compatible with our minimum.
	 */
	function tribe_is_not_min_php_version( $version = PHP_VERSION ) {
		return version_compare( $version, tribe_get_php_min_version(), '<' );
	}

	/**
	 * Which is our required PHP min version.
	 *
	 * @since  TBD
	 *
	 * @return string Which version of PHP we minimally require.
	 */
	function tribe_get_php_min_version() {
		return '7.4';
	}

	/**
	 * Returns the error message when php version min doesn't check.
	 *
	 * @since  TBD
	 *
	 * @return string Error message HTML.
	 */
	function tribe_not_php_version_message() {
		$names          = tribe_not_php_version_names();
		$count_names    = count( $names );
		$last_connector = esc_html_x( ' and ', 'Plugin A "and" Plugin B', 'conference-schedule' );
		$many_connector = esc_html_x( ', ', 'Plugin A"," Plugin B', 'conference-schedule' );

		if ( 1 === $count_names ) {
			$label_names = current( $names );
		} elseif ( 2 === $count_names ) {
			$label_names = current( $names ) . $last_connector . end( $names );
		} else {
			$last_name   = array_pop( $names );
			$label_names = implode( $many_connector, $names ) . $last_connector . $last_name;
		}

		/* Translators: %1$s - plugin name(s) %2$s - php version */
		return wp_kses_post(
			sprintf(
				_n(
					'<b>%1$s</b> requires <b>PHP %2$s</b> or higher.',
					'<b>%1$s</b> require <b>PHP %2$s</b> or higher.',
					$count_names,
					'conference-schedule'
				),
				esc_html( $label_names ),
				tribe_get_php_min_version()
			)
		) .
		'<br />' .
		esc_html__( 'To allow better control over dates, advanced security improvements and performance gain.', 'conference-schedule' ) .
		'<br />' .
		esc_html__( 'Contact your Host or your system administrator and ask to upgrade to the latest version of PHP.', 'conference-schedule' );
	}

	/**
	 * Fetches the name of the plugins that are not compatible with current PHP version.
	 *
	 * @since  TBD
	 *
	 * @return array Which plugins have incompatible PHP versions.
	 */
	function tribe_not_php_version_names() {
		/**
		 * Allow us to include more plugins without increasing the number of notices.
		 *
		 * @since  TBD
		 *
		 * @param array $names Name of the plugins that are not compatible.
		 */
		return apply_filters( 'tribe_not_php_version_names', [] );
	}

	/**
	 * Echoes out the error for the PHP min version as a WordPress admin Notice.
	 *
	 * @since  TBD
	 */
	function tribe_not_php_version_notice() {
		echo '<div id="message" class="error"><p>' . wp_kses_post( tribe_not_php_version_message() ) . '</p></div>';
	}

	/**
	 * Loads the Text domain for non-compatible PHP versions.
	 *
	 * @since  TBD
	 *
	 * @param string $domain Which domain we will try to translate to.
	 * @param string $file   Where to look for the lang folder.
	 */
	function tribe_not_php_version_textdomain( $domain, $file ) {
		load_plugin_textdomain(
			$domain,
			false,
			plugin_basename( $file ) . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR
		);
	}
endif;
