<?php
/**
 * The main Conference Schedule plugin service provider: it bootstraps the plugin code.
 *
 * @since   TBD
 *
 * @package Conference\Schedule
 */

namespace Conference\Schedule;

use Tribe__Autoloader;

/**
 * Class Plugin
 *
 * @since   TBD
 *
 * @package Conference\Schedule
 */
class Plugin {
	/**
	 * Stores the version for the plugin.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	const VERSION = Plugin_Register::VERSION;

	/**
	 * Stores the base slug for the plugin.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	const SLUG = 'conference-schedule';

	/**
	 * Stores the base slug for the extension.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	const FILE = CONFERENCE_SCHEDULE_FILE;

	/**
	 * The slug that will be used to identify HTTP requests the plugin should handle.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public static $request_slug = 'conference_schedule_request';


	/**
	 * @since TBD
	 *
	 * @var string Plugin Directory.
	 */
	public $plugin_dir;

	/**
	 * @since TBD
	 *
	 * @var string Plugin path.
	 */
	public $plugin_path;

	/**
	 * @since TBD
	 *
	 * @var string Plugin URL.
	 */
	public $plugin_url;

	/**
	 * Allows this class to be used as a singleton.
	 *
	 * Note this specifically doesn't have a typing, just a type hinting via Docblocks, it helps
	 * avoid problems with deprecation since this is loaded so early.
	 *
	 * @since 1.15.0
	 *
	 * @var \Tribe__Container
	 */
	protected $container;

	/**
	 * Sets the container for the class.
	 *
	 * Note this specifically doesn't have a typing for the container, just a type hinting via Docblocks, it helps
	 * avoid problems with deprecation since this is loaded so early.
	 *
	 * @since 1.14.0
	 *
	 * @param ?\Tribe__Container $container The container to use, if any. If not provided, the global container will be used.
	 *
	 */
	public function set_container( $container = null ): void {
		$this->container = $container ?: tribe();
	}

	/**
	 * Boots the plugin class and registers it as a singleton.
	 *
	 * Note this specifically doesn't have a typing for the container, just a type hinting via Docblocks, it helps
	 * avoid problems with deprecation since this is loaded so early.
	 *
	 * @since 1.14.0
	 *
	 * @param ?\Tribe__Container $container The container to use, if any. If not provided, the global container will be used.
	 */
	public static function boot( $container = null ): void {
		$plugin = new static();
		$plugin->register_autoloader();
		$plugin->set_container( $container );
		$plugin->container->singleton( static::class, $plugin );

		$plugin->register();
	}

	/**
	 * Setup the Extension's properties.
	 *
	 * This always executes even if the required plugins are not present.
	 */
	public function register() {
		conference_schedule_load_text_domain();
		// Set up the plugin provider properties.
		$this->plugin_path = trailingslashit( dirname( static::FILE ) );
		$this->plugin_dir  = trailingslashit( basename( $this->plugin_path ) );
		$this->plugin_url  = plugins_url( $this->plugin_dir, $this->plugin_path );

		$this->register_autoloader();

		// Register this provider as the main one and use a bunch of aliases.
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'conference-schedule', $this );
		$this->container->singleton( 'conference-schedule.plugin', $this );

		if ( ! $this->check_plugin_dependencies() ) {
			// If the plugin dependency manifest is not met, then bail and stop here.
			return;
		}

		$this->container->register( Hooks::class );
	}

	/**
	 * Register the Tribe Autoloader in Events Automator.
	 *
	 * @since 1.2.0
	 */
	protected function register_autoloader() {
		$autoloader = Tribe__Autoloader::instance();

		// For namespaced classes.
		$autoloader->register_prefix(
			'\\Conference\\Schedule\\',
			$this->plugin_path . '/src/Conference',
			'conference-schedule'
		);
	}

	/**
	 * Checks whether the plugin dependency manifest is satisfied or not.
	 *
	 * @since TBD
	 *
	 * @return bool Whether the plugin dependency manifest is satisfied or not.
	 */
	protected function check_plugin_dependencies(): bool {
		$this->register_plugin_dependencies();

		if ( ! tribe_check_plugin( static::class ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Registers the plugin and dependency manifest among those managed by Event Automator.
	 *
	 * @since TBD
	 */
	protected function register_plugin_dependencies() {
		$plugin_register = new Plugin_Register();
		$plugin_register->register_plugin();

		$this->container->singleton( Plugin_Register::class, $plugin_register );
		$this->container->singleton( 'conference-schedule.plugin_register', $plugin_register );
	}
}
