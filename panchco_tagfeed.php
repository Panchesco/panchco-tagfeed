<?php
/**
 * Plugin Name: Panchco Tag Feed Widget
 * Plugin URI:
 * Description: Display an Instagram Tag Feed in a widget.
 * Author: Richard Whitmer
 * Version: 1
 * Author URI: http://panchesco.com
 
 Copyright 2014  Richard Whitmer  (email : panchesco@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


	require_once(__DIR__ . '/panchco_tagfeedwidget.php');


	class PanchcoTagFeed {
		
			public	static $endpoint		= "https://api.instagram.com/v1/tags/%s/media/recent?client_id=%s&count=%s";
			public	static $ig_username		= null;
			public	static $media_count		= null;
			public	static $client_id		= null;
			public	static $client_secret	= null;
			public	static $hashtag			= null;
			public	static $count			= 0;	
			public	static $resolution		= 'Standard';
			public 	static $options			= array();	
			public	static $response		= array();
			public	static $website_url		= null;
			public	static $redirect_uri	= null;
			
			
			/** 
			 * Initialization.
			 */
			 public static function Init()
			 {
			 
			 	// Get the current options.
			 	self::$options = get_option('panchco_tagfeed');
			 	
			 	// Set properties.
			 	foreach(self::$options as $key=>$row){
				 	self::$$key = $row;
			 	}

			 	/** Setting section 1. **/
			 	add_settings_section(
			 	'settings_section_1',
			 	'Instagram Client Info & Settings<br><p>Visit <a target="_blank" href="http://instagram.com/developer/clients/manage/">
			 	http://instagram.com/developer/clients/manage/</a>
			 	to register a new client.<br>Then return to this page to save your client settings.
			 	</p>',
			 	'',
			 	'panchco_tagfeed'
			 	);
			 
				 add_settings_field(
				 'ig_username',
			       'Instagram Username',
			       'PanchcoTagFeed::ig_username_input',
			       'panchco_tagfeed',
			       'settings_section_1'
			    );
			    
			    
				// Client ID.
				add_settings_field(
				
				'client_id',
				   'Client ID',
				   'PanchcoTagFeed::client_id_input',
				   'panchco_tagfeed',
				   'settings_section_1'
				);
				
				// Client Secret.
				add_settings_field(
				
				   'client_secret',
				   'Client Secret',
				   'PanchcoTagFeed::client_secret_input',
				   'panchco_tagfeed',
				   'settings_section_1'
				);	
				
				// Client Secret.
				add_settings_field(
				
				   'hashtag',
				   'Hashtag',
				   'PanchcoTagFeed::hashtag_input',
				   'panchco_tagfeed',
				   'settings_section_1'
				);	
				
				// Client Secret.
				add_settings_field(
				
				   'resolution',
				   'Display Resolution',
				   'PanchcoTagFeed::resolution_input',
				   'panchco_tagfeed',
				   'settings_section_1'
				);
				
				// Client Secret.
				add_settings_field(
				
				   'count',
				   'Count',
				   'PanchcoTagFeed::count_input',
				   'panchco_tagfeed',
				   'settings_section_1'
				);	    


			    // Register the fields field with our settings group.
				register_setting( 'settings_group', 'panchco_tagfeed');
				
				
				 
			 }
			 
			 
			 
		/** IG Username Input **/
		function ig_username_input() {
			echo( '<input type="text" name="panchco_tagfeed[ig_username]" id="panchco_tagfeed[ig_username]" value="' . self::$options['ig_username']  .'" />' );
		}
		
		/** Client ID Input **/
		function client_id_input() {
			echo( '<input type="text" name="panchco_tagfeed[client_id]" id="panchco_tagfeed[client_id]" value="' . self::$options['client_id']  .'" />' );
		}
		
		
		/** Client Secret Input **/
		function client_secret_input() {
		    echo( '<input type="text" name="panchco_tagfeed[client_secret]" id="panchco_tagfeed[client_secret]" value="'. self::$options['client_secret'] .'" />' );
		}	 
		
		
		/** Hashtag Input **/
		function hashtag_input() {
		    echo( '<input type="text" name="panchco_tagfeed[hashtag]" id="panchco_tagfeed[hashtag]" value="'. self::$options['hashtag'] .'" />' );
		}
		
		/** Resolution Input **/
		function resolution_input() {
		    
		    echo '
		    <select name="panchco_tagfeed[resolution]" id="panchco_tagfeed[resolution]">
		    	<option value="Thumbnail"' . self::selected('Thumbnail',self::$resolution) . '>Thumbnail</option>
		    	<option value="Low Resolution"' . self::selected('Low Resolution',self::$resolution)  . '>Low Resolution</option>
		    	<option value="Standard"' . self::selected('Standard',self::$resolution)  . '>Standard</option>
		    </select>
		    ';
		    
		}
		
		
		/** Count Input **/
		function count_input() {
		    echo( '<input type="text" name="panchco_tagfeed[count]" id="panchco_tagfeed[count]" value="'. self::$options['count'] .'" />' );
		}
		
		
		/** Select this option? **/
		public static function selected($option,$value)
		{
			if($option==$value)
			{
				return ' selected ';
			} else {
				return '';
			}
		}
			 
		/** Add Settings Page **/
		public  function settingsMenu() {
			
			   		add_options_page(

			   		   'TagFeed Settings',
			   		   'Panchco TagFeed',
			   		   'manage_options',
			   		   'panchco_tagfeed',
			   		   'PanchcoTagFeed::settingsPage'
			   		);
 
			}
			
		/** Add Settings Page **/
		public  static function settingsPage() {

			
			   		?>
			   		
			   		<h1>TagFeed Settings Page</h1>
			   		
			   		<form method="post" action="options.php">
			   		
			   		<?php
			   		
			   		// Output the settings sections.
				    do_settings_sections( 'panchco_tagfeed' );
				 
				    // Output the hidden fields, nonce, etc.
				    settings_fields( 'settings_group' );
				    
				    submit_button();
			   		
			   		?>
			   		
			   		</form>
			   		
			   		
			   		<?php

			   					   		
			   		self::thumbnails();

			}
			
			
			public static function thumbnails()
			{
				
				self::$response	= self::tagsMediaRecent();
				
				if(self::$response->meta->code==200) { ?> 
				
			   		<style>
			   		
			   			.thumbnails
			   			{
				   			clear: both;
				   			max-width: 750px;
			   			}
			   		
			   			.thumbnails img
			   			{
				   			display: block;
				   			float: left;
				   			margin: 0 8px 8px 0;
				   			max-width: 150px;
				   			height: auto;
			   			}
			   			
			   			.widget-content .thumbnails img
			   			{
				   			max-width: 70px;
				   			height: auto;
				   			margin: 0 6px 6px 0;
			   			}
			   			
			   			.clearfix
			   			{
				   			width: 100%;
				   			float: none;
				   			clear:both;
			   			}
			   			
			   			#content-sidebar .thumbnails img
			   			{
			   			
			   				display: block;
				   			float: left;
				   			height: auto;
			   				max-width: 70px;
				   			height: auto;
				   			margin: 0 6px 6px 0;
				   			
			   			}
			   		
			   		</style>
			   		<div class="thumbnails">
			   		<h6>#<?php echo self::$hashtag;?></h6>
			   		<?php foreach(self::$response->data as $row) { ?>
			   			<div class="item">
			   				<a class="<?php echo $row->type ;?>" href="<?php echo $row->link ;?>embed/" target="_blank" title="<?php echo ucfirst($row->type) . ' by ' . $row->user->username ;?>">
				   				<img src="<?php echo $row->images->thumbnail->url ;?>">
			   				</a>
			   			</div>
			   			
			   		<?php } ?>
			   		<div class="clearfix"><!-- --></div>
			   		</div><!-- /.thumbnails -->
				<?php }  else { ?>
				<?php echo '<p>' . self::$response->meta->error_message . '</p>';?>
				<?php } ?>
				<?php 
			}
			
			
			/**
			 * Things we'll do when the plugin is activated.
			 */
			public static function install()
			{
				add_option('panchco_tagfeed',
								array(
									'ig_username' => '',
									'client_id' => '',
									'client_secret' => '',
									'website_url' => get_site_url(),
									'redirect_uri' => get_site_url(),
									'hashtag' => '',
									'resolution' => 'thumbnail',
									'count' => '20'
								)
							);		
			}
			
			
			/**
			 * Things we'll do when the plugin is de-activated.
			 */
			public static function uninstall()
			{
				delete_option('panchco_tagfeed');			
			}
			

					/**
				    * CURL handling.
				    * @param $uri string
				    * @return object
				    */
				    public static function getCurl($url) {
						    if(function_exists('curl_init')) {
						        $ch = curl_init();
						        curl_setopt($ch, CURLOPT_URL,$url);
						        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						        curl_setopt($ch, CURLOPT_HEADER, 0);
						        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
						        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
						        $output = curl_exec($ch);
						        echo curl_error($ch);
						        curl_close($ch);
						        return $output;
						    } else{
						        return file_get_contents($url);
						    }
						}
			
			
			
					/**
				    * CURL handling.
				    * @param $uri string
				    * @param $fields array of urlencoded strings
				    * @return object
				    */
				    public static function postCurl($url,$fields) {
						    if(function_exists('curl_init')) {
								//url-ify the data for the POST
								foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
								rtrim($fields_string, '&');
								
								//open connection
								$ch = curl_init();
								
								//set the url, number of POST vars, POST data
								curl_setopt($ch,CURLOPT_URL, $url);
								curl_setopt($ch,CURLOPT_POST, count($fields));
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						        curl_setopt($ch, CURLOPT_HEADER, 0);
						        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
						        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
								curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
						        $output = curl_exec($ch);
						        echo curl_error($ch);
						        curl_close($ch);
						        return $output;
						    } else{
						        return file_get_contents($url);
						    }
						}			
			
			
			
			
			
				/**
				* Create the endpoint based on default or instance-assigned properties.
				* @return string
				*/
				public function endpoint()
				{
					     return sprintf(self::$endpoint,self::$hashtag,self::$client_id,self::$count);
				}
				

				
				/**
				 * Return response as a php object.
				 * @return mixed str/obj
				 */
				 public static function response()
				 {	
						$response = self::tagsMediaRecent();

						if(isset($response->meta->error_message))
						{
						  	exit($response->meta->error_message);
						}
						
					return $response;
				 }
				

				/**
				 * Get a list of recently tagged media.
				 * @param $tag string
				 * @client_id string
				 * @param $max_tag_id string
				 * @return array
				 */
				 public static function tagsMediaRecent()
				 {
				 
				 				$endpoint = self::endpoint();
				 				
								// Get the response
								$response = json_decode(self::getCurl($endpoint));

							return $response;
				 }
				 
				 
				 /**
				  ** Set tag data for current tag to instance.
				  ** @return integer
				  */
				  public function mediaCount()
				  {
					  
					  $response = json_decode(self::tagData());
					  

				   	 if(isset($response->meta->error_message))
					  {
					  	exit($response->meta->error_message);
					  }	
				   			
					  
					  if(isset($response->data->media_count))
					  {
						  return $response->data->media_count;
					  }
					  
					  return 0;

				  }
				 
				 /**
				   * Get information about current tag object.
				   * @return string
				   */
				   public function tagData()
				   {				   			
				   			$endpoint = 'https://api.instagram.com/v1/tags/'. self::$hashtag . '?client_id=' . self::$client_id;
				   			
				   			$response = CurlHelper::getCurl($endpoint);

				   		return json_decode($response);
				   }	
				   
				   	   
				   
		/**
		 * Add settings link on activation page.
		 * VIA http://www.wphub.com/adding-plugin-action-links/
		 */
		public static function actionLinks($links, $file) {
		    static $this_plugin;
		    
		    if (!$this_plugin) {
		        $this_plugin = plugin_basename(__FILE__);
		    }
		 
		    // check to make sure we are on the correct plugin
		    if ($file == $this_plugin) {
		        // the anchor tag and href to the URL we want. For a "Settings" link, this needs to be the url of your settings page
		        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=panchco_tagfeed">Settings</a>';
		        // add the link to the list
		        array_unshift($links, $settings_link);
		    }
		 
		    return $links;
		}		
			
			

	}


				
		// Register Actions
		add_action('admin_init',array('PanchcoTagFeed','Init'));
		add_action('admin_menu',array('PanchcoTagFeed','settingsMenu'));
		add_filter('plugin_action_links', 'PanchcoTagFeed::actionLinks', 10, 2);
		

		// Activate & Deactivate Hooks
		register_activation_hook( __FILE__, array( 'PanchcoTagFeed', 'install' ) );
		register_deactivation_hook( __FILE__, array( 'PanchcoTagFeed', 'uninstall' ) );
	
		