<?php 
namespace NexVis\WordPress{
	
	require ( plugin_dir_path( __FILE__ ) ) . 'plugin_updater.php';

	class Update_This_Plugin extends WP_NVT_Plugin_Updater {

		/**
		 * Return the latest version number of the plugin.
		 *
		 * @return string
		 */
		protected function is_latest_version_available()
		{
			// Do anything you need to provide the latest version number.
			// for example: perform a wp_remote_get() request to a Github repository retrieving the latest release or call out to your own server that hosts the plugin zip.
			//$plugin_commits = $this->get_commits_url();
			//echo $plugin_commits;
			//$response = wp_remote_get($plugin_commits);
			//$response_body = $response['body'];
			$response_body = $this->get_commits_curl();
			$response_data = json_decode($response_body, false);
			print "GITHUB Response: ";
			//print_r($response_data);
			
			//echo "here we are";
			//echo "<pre>"; print_r($response_data[0]);
			$sTimeLatest = $response_data[0]->commit->author->date; 
			//echo "<br>Latest Update";
			$iTimeLatest = strtotime($sTimeLatest);
			//echo $iTimeLatest;
			
			$last_updated = (int) $this->get_last_update_time();
			//echo "<br>Last Updated: ";
			//echo $last_updated;
			//print_r("What? new version?");
			if( $iTimeLatest > $last_updated){ //Plugin has update
				//print_r("What? Yes new version?");
				$version = true;
			}else {
				//print_r("What? No new version?");
				$version = $this->get_current_version();
				$version = false;
			}
			//die;
			//*/
			return $version;
		}
		
		/**
		 * Return the latest version number of the plugin.
		 *
		 * @return string
		 */
		protected function get_latest_version()
		{
			return $this->get_current_version();
		}

		/**
		 * Get the plugin url.
		 *
		 * @return string The URL.
		 */
		protected function get_url()
		{
			return 'https://github.com/benmen1980/update_from_github';
		}

		/**
		 * Get the package url.
		 *
		 * @return string The package URL.
		 */
		protected function get_package_url()
		{
			return 'https://github.com/benmen1980/update_from_github/archive/refs/heads/main.zip';
		}
		
		/**
		 * Get the plugin commits.
		 *
		 * @return string The URL.
		 */
		protected function get_commits_url()
		{
			return 'https://api.github.com/repos/benmen1980/update_from_github/commits';
		}
		
		/**
		 * Get the private plugin zip.
		 *
		 * @return string The URL.
		 */
		protected function get_private_package()
		{
			return 'https://api.github.com/repos/benmen1980/update_from_github/zipball';
		}
		
		protected function get_commits_curl(){
			$objCurl = curl_init();
			$url = $this->get_commits_url();
			$access_token = "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";
			$access_token = "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"; //Expired
			$access_token = "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"; //till 28-12-2021
			$url = $url."?access_token=".$access_token;
			
			//The repo we want to get
			curl_setopt($objCurl, CURLOPT_URL, $url);
			
			//To comply with https://developer.github.com/v3/#user-agent-required
			curl_setopt($objCurl, CURLOPT_USERAGENT, "mudassarijaz"); 
			
			//Skip verification (kinda insecure)
			curl_setopt($objCurl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($objCurl, CURLOPT_RETURNTRANSFER, true);
			
			//Get the response
			$response = curl_exec($objCurl);
			
			return $response;
		}
	}
}
?>