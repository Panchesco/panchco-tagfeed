<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
} 



class PanchcoTagFeedWidget extends WP_Widget
{


	function __construct()
	{
		$widget_options = array(
								'classname'	=> 'panchco-tag-feed-widget',
								'description' => 'Display items from Instagram\'s tag feed. API'
		);
		
		parent::WP_Widget('panchco_tag_feed_widget','Tag Feed Widget',$widget_options);
		
		// Register the stylesheet.
		//wp_register_style( 'gdrwigStylesheet', plugins_url('wp_gdrwig_feeds/assets/css/gdrwig-admin.css') );
	}
	
	
	/* This method is required */
	  function widget($args, $instance) 
	  {

		  $options = get_option('panchco_tagfeed');
		  foreach($options as $key=>$row)
		  {
			  PanchcoTagFeed::$$key = $row;
		  }
		  
		  PanchcoTagFeed::thumbnails(); 
		  
  	}
	
	function form($instance)
	{
	
		PanchcoTagFeed::thumbnails(); 

	}
	
	
	function register()
	{
		register_widget('PanchcoTagFeedWidget');
	}
	
	
}

add_action('widgets_init',array('PanchcoTagFeedWidget','register'));



