<?php 
/**
 * @package NexVis\WordPress
 */
declare( strict_types = 1 );
namespace NexVis\WordPress;

abstract class WP_NVT_Plugin_Updater {

	/**
	 * The current version of the plugin.
	 *
	 * @var string
	 */
	private $current_version;
	private $latest_version;
	/**
	 * The plugin directory.
	 *
	 * @var string
	 */
	private $directory;
	
	/**
	 * The plugin slug.
	 *
	 * @var string
	 */
	private $slug;

	/**
	 * The constructor.
	 *
	 * @param string $current_version The current plugin version.
	 * @param string $slug            The slug of the plugin.
	 */
	public function __construct( string $current_version, string $directory, string $slug )
	{
		$this->current_version = $current_version;
		$this->directory = $directory;
		$this->slug = $slug;
	}

	/**
	 * Returns the latest version of the plugin.
	 *
	 * @return string|WP_Error The version number or instance of WP_Error.
	 */
	abstract protected function get_latest_version();
	
	/**
	 * Returns the latest version of the plugin.
	 *
	 * @return string|WP_Error The version number or instance of WP_Error.
	 */
	abstract protected function is_latest_version_available();

	/**
	 * Returns the url for the plugin.
	 *
	 * @return string The url.
	 */
	abstract protected function get_url();

	/**
	 * Returns the package url for the plugin.
	 *
	 * @return string The package url.
	 */
	abstract protected function get_package_url();
	
	/**
	 * Returns the commits URL of github repository.
	 *
	 * @return string The package url.
	 */
	abstract protected function get_commits_url();
	
	/**
	 * Returns the package url for the private repository.
	 *
	 * @return string The package url.
	 */
	abstract protected function get_private_package();

	/**
	 * Hook into the 'update_plugins' transients.
	 */
	public function init()
	{
		add_filter( 'transient_update_plugins', array( $this,  'filter_plugin_update_data' ) );
		add_filter( 'site_transient_update_plugins', array( $this,  'filter_plugin_update_data' ) );
		register_activation_hook( $this->directory . '/' . $this->slug . '.php', array( $this, 'filter_upgrader_post_install') );
		add_filter( 'upgrader_post_install', array( $this, 'filter_upgrader_post_install'), 10, 2 );
	}

	/**
	 * Filters the 'update_plugins' transients so that the plugin can be updated without using the WordPress.org repository.
	 *
	 * @param object $update_plugins The object detailing which plugins have updates available.
	 */
	public function filter_plugin_update_data( $update_plugins )
	{
		if ( ! is_object( $update_plugins ) ) {
			return $update_plugins;
		}
		// Exit if the plugin is not contained in the 'checked' array.
		if ( ! isset( $update_plugins->checked[ $this->directory . '/' . $this->slug . '.php' ] ) ) {
			return $update_plugins;
		}
		
		if ( ! isset( $update_plugins->response ) || ! is_array( $update_plugins->response ) ) {
			$update_plugins->response = array();
		}
		//print "<pre>Updates: "; print_r($update_plugins); print "</pre>";
		// Only set the response if the plugin has a new release.
		$this->latest_version = $this->get_latest_version();
        //if ( version_compare( $this->current_version, $this->latest_version ) ) {
		if( time() > ($update_plugins->last_checked+(60*5)) ){
			if ( $this->is_latest_version_available() ) {
				$update_plugins->response[ $this->directory . '/' . $this->slug . '.php'] = $this->get_plugin_response_data();
			}
		}
		
		return $update_plugins;
	}

	/**
	 * Gets the plugin response data to use if there is a new version of the plugin.
	 *
	 * @return object The response object providing the plugin details: slug, version, url, and package location.
	 */
	protected function get_plugin_response_data()
	{
		return (object) array(
			'slug'         => $this->slug,
			'new_version'  => $this->latest_version,
			'url'          => $this->get_url(),
			'package'      => $this->get_package_url(),
		);
	}
	
	function filter_upgrader_post_install(  $response = null, $hook_extra = null, $result = null ){
		//print_r("What? post intall/activate");
		update_option("_last_updated_".$this->slug, time());
		return $response;
	}
	
	function get_last_update_time(){
		//print_r("What? get time");
		$last_updated = get_option("_last_updated_".$this->slug);
		return $last_updated;
	}
	
	protected function get_current_version(){
		return $this->current_version;
	}
}

?>