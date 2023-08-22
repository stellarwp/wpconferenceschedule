<?php
/**
 * The main Conference Schedule plugin service provider: it bootstraps the plugin code.
 *
 * @since   TBD
 *
 * @package TEC\Conference
 */

namespace TEC\Conference;

use TEC\Conference\Post_Types\Provider as Post_Types_Provider;
use TEC\Conference\Admin\Provider as Admin_Provider;
use Tribe__Autoloader;
use Tribe__Main;

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
	 * @var string tribe-common VERSION regex
	 */
	private $common_version_regex = "/const\s+VERSION\s*=\s*'([^']+)'/m";

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
		$this->container = $container ?: tribe();
	}

	/**
	 * To avoid duplication of our own methods and to provide a underlying system
	 * Conference Schedule maintains a Library called Common to store a base for our plugins
	 *
	 * Currently we will read the File `common/package.json` to determine which version
	 * of the Common Lib we will pass to the Auto-Loader of PHP.
	 *
	 * In the past we used to parse `common/src/Tribe/Main.php` for the Common Lib version.
	 *
	 * @link https://github.com/moderntribe/tribe-common
	 * @see  self::init_autoloading
	 *
	 * @since TBD
	 */
	public function maybe_set_common_lib_info() {
		// if there isn't a tribe-common version, bail with a notice
		$common_version = file_get_contents( $this->plugin_path . 'common/src/Tribe/Main.php' );
		if ( ! preg_match( $this->common_version_regex, $common_version, $matches ) ) {
			return add_action( 'admin_head', [ $this, 'missing_common_libs' ] );
		}

		$common_version = $matches[1];

		/**
		 * If we don't have a version of Common or a Older version of the Lib
		 * overwrite what should be loaded by the auto-loader
		 */
		if (
			empty( $GLOBALS['tribe-common-info'] )
			|| version_compare( $GLOBALS['tribe-common-info']['version'], $common_version, '<' )
		) {
			$GLOBALS['tribe-common-info'] = [
				'dir'     => "{$this->plugin_path}common/src/Tribe",
				'version' => $common_version,
			];
		}
	}

	/**
	 * To allow easier usage of classes on our files we have a AutoLoader that will match
	 * class names to it's required file inclusion into the Request.
	 *
	 * @since TBD
	 */
	protected function init_autoloading() {
		$autoloader = $this->get_autoloader_instance();

		// Deprecated classes are registered in a class to path fashion.
		foreach ( glob( $this->plugin_path . 'src/deprecated/*.php', GLOB_NOSORT ) as $file ) {
			$class_name = str_replace( '.php', '', basename( $file ) );
			$autoloader->register_class( $class_name, $file );
		}

		$autoloader->register_autoloader();
	}

	/**
	 * Returns the autoloader singleton instance to use in a context-aware manner.
	 *
	 * @since TBD
	 *
	 * @return \Tribe__Autoloader The singleton common Autoloader instance.
	 */
	public function get_autoloader_instance() {
		if ( ! class_exists( 'Tribe__Autoloader', false ) ) {
			require_once $GLOBALS['tribe-common-info']['dir'] . '/Autoloader.php';

			Tribe__Autoloader::instance()->register_prefixes( [
				'Tribe__' => $GLOBALS['tribe-common-info']['dir'],
			] );
		}

		return Tribe__Autoloader::instance();
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

		// Set common lib information, needs to happen file load
		$this->maybe_set_common_lib_info();

		/**
		 * Before any methods from this plugin are called, we initialize our Autoloading
		 * After this method we can use any `Tribe__` classes
		 */
		$this->init_autoloading();

		add_filter( 'tec_common_parent_plugin_file', [ $this, 'include_parent_plugin_path_to_common' ] );

		Tribe__Main::instance();

		add_action( 'tribe_common_loaded', [ $this, 'bootstrap' ], 0 );
	}

	/**
	 * Adds our main plugin file to the list of paths.
	 *
	 * @since TBD
	 *
	 * @param array<string> $paths The paths to TCMN parent plugins.
	 *
	 * @return array<string>
	 */
	public function include_parent_plugin_path_to_common( $paths ): array {
		$paths[] = CONFERENCE_SCHEDULE_FILE;

		return $paths;
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
		// Load Composer autoload file only if we've not included this file already.
		require_once dirname( CONFERENCE_SCHEDULE_FILE ) . '/vendor/autoload.php';

		$autoloader = Tribe__Autoloader::instance();

		// For namespaced classes.
		$autoloader->register_prefix(
			'\\TEC\\Conference\\',
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
