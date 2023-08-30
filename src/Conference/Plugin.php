<?php
/**
 * The main Conference Schedule plugin service provider: it bootstraps the plugin code.
 *
 * @since   TBD
 *
 * @package TEC\Conference
 */

namespace TEC\Conference;

use TEC\Conference\Contracts\Container;
use TEC\Conference\Post_Types\Provider as Post_Types_Provider;
use TEC\Conference\Admin\Provider as Admin_Provider;

/**
 * Class Plugin
 *
 * @since   TBD
 *
 * @package TEC\Conference
 */
class Plugin {
	/**
	 * Stores the version for the plugin.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public const VERSION  = '1.0.0';

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
	 * The Sessions Post Type.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	const SESSION_POSTTYPE = 'wpcs_session';

	/**
	 * The Speakers Post Type.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	const SPEAKER_POSTTYPE = 'wpcsp_speaker';

	/**
	 * The Sponsors Post Type.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	const SPONSOR_POSTTYPE = 'wpcsp_sponsor';

	/**
	 * @var bool Prevent autoload initialization
	 */
	private $should_prevent_autoload_init = false;

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
	 * @since TBD
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
	 * @since TBD
	 *
	 * @param ?\Tribe__Container $container The container to use, if any. If not provided, the global container will be used.
	 */
	public function set_container( $container = null ): void {
		$this->container = $container ?: new Container();
	}

	/**
	 * Boots the plugin class and registers it as a singleton.
	 *
	 * Note this specifically doesn't have a typing for the container, just a type hinting via Docblocks, it helps
	 * avoid problems with deprecation since this is loaded so early.
	 *
	 * @since TBD
	 *
	 * @param ?\Tribe__Container $container The container to use, if any. If not provided, the global container will be used.
	 */
	public function boot( $container = null ): void {
		// Set up the plugin provider properties.
		$this->plugin_path = trailingslashit( dirname( static::FILE ) );
		$this->plugin_dir  = trailingslashit( basename( $this->plugin_path ) );
		$this->plugin_url  = plugins_url( $this->plugin_dir, $this->plugin_path );

		add_action( 'plugins_loaded', [ $this, 'bootstrap' ], 1 );
	}

	/**
	 * Plugins shouldn't include their functions before `plugins_loaded` because this will allow
	 * better compatibility with the autoloader methods.
	 *
	 * @since TBD
	 */
	public function bootstrap() {
		if ( $this->should_prevent_autoload_init ) {
			return;
		}
		$plugin = new static();
		$plugin->register_autoloader();
		$plugin->set_container();
		$plugin->container->singleton( static::class, $plugin );
		$plugin->register();
	}

	/**
	 * Setup the Extension's properties.
	 *
	 * This always executes even if the required plugins are not present.
	 *
	 * @since TBD
	 */
	public function register() {
		conference_schedule_load_text_domain();

		$this->register_autoloader();

		// Register this provider as the main one and use a bunch of aliases.
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'conference-schedule', $this );
		$this->container->singleton( 'conference-schedule.plugin', $this );


		if ( ! $this->check_plugin_dependencies() ) {
			// If the plugin dependency manifest is not met, then bail and stop here.
			//return;
		}

		$this->container->register( Post_Types_Provider::class );
		$this->container->register( Admin_Provider::class );
	}

	/**
	 * Register the Tribe Autoloader for Conference Schedule Pro.
	 *
	 * @since TBD
	 */
	protected function register_autoloader() {
		// Load Composer autoload and strauss autoloader.
		require_once dirname( CONFERENCE_SCHEDULE_FILE ) . '/vendor/vendor-prefixed/autoload.php';
		require_once dirname( CONFERENCE_SCHEDULE_FILE ) . '/vendor/autoload.php';
	}

	/**
	 * Registers the plugin and dependency manifest among those managed by Conference Schedule Pro.
	 *
	 * @since TBD
	 */
	protected function register_plugin_dependencies() {
		$plugin_register = new Plugin_Register();
		$plugin_register->register_plugin();

		$this->container->singleton( Plugin_Register::class, $plugin_register );
		$this->container->singleton( 'conference-schedule.plugin_register', $plugin_register );
	}

	/**
	 * Plugin activation callback
	 * @see register_activation_hook()
	 *
	 * @since TBD
	 */
	public static function activate() {
		//@todo added as a placeholder to be finished when adding capabilities for the cpts.
		//self::instance()->plugins_loaded();

		// call your CPT registration function here (it should also be hooked into 'init')
		//wpcs_register_post_types();
		//flush_rewrite_rules();
	}

	/**
	 * plugin deactivation callback
	 * @see register_deactivation_hook()
	 *
	 * @since TBD
	 * @param bool $network_deactivating
	 */
	public static function deactivate( $network_deactivating ) {
		//@todo added as a placeholder to be finished when adding capabilities for the cpts.
		flush_rewrite_rules();
	}
}
