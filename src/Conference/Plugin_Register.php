<?php
/**
 * Handles the Conference Schedule plugin dependency manifest registration.
 *
 * @since   TBD
 *
 * @package TEC\Conference
 */

namespace TEC\Conference;

use Tribe__Abstract_Plugin_Register as Abstract_Plugin_Register;

/**
 * Class Plugin_Register.
 *
 * @since   TBD
 *
 * @package TEC\Conference
 *
 * @see     Tribe__Abstract_Plugin_Register For the plugin dependency manifest registration.
 */
class Plugin_Register extends Abstract_Plugin_Register {
	/**
	 * The version of the plugin.
	 * Replaced the Plugin::VERSION constant, which now is an alias to this one.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public const VERSION  = Plugin::VERSION;

	/**
	 * Configures the base_dir property which is the path to the plugin bootstrap file.
	 *
	 * @since TBD
	 *
	 * @param string $file Which is the path to the plugin bootstrap file.
	 */
	public function set_base_dir( string $file ): void {
		$this->base_dir = $file;
	}

	/**
	 * Gets the previously configured base_dir property.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	public function get_base_dir(): string {
		return $this->base_dir;
	}

	/**
	 * Gets the main class of the Plugin, stored on the main_class property.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	public function get_plugin_class(): string {
		return $this->main_class;
	}

	/**
	 * File path to the main class of the plugin.
	 *
	 * @since TBD
	 *
	 * @var string The path to the main class of the plugin.
	 */
	protected $base_dir;

	/**
	 * Alias to the VERSION constant.
	 *
	 * @since TBD
	 *
	 * @var string The version of the plugin.
	 */
	protected $version = self::VERSION;

	/**
	 * Fully qualified name of the main class of the plugin.
	 * Do not use the Plugin::class constant here, we need this value without loading the Plugin class.
	 *
	 * @since TBD
	 *
	 * @var string The main class of the plugin.
	 */
	protected $main_class = '\TEC\Conference\Plugin';

	/**
	 * An array of dependencies for the plugin.
	 *
	 * @since TBD
	 *
	 * @var array<string,mixed>
	 */
	protected $dependencies = [
		'parent-dependencies' => [],
	];
}
