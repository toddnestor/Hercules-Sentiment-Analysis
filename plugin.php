<?php
/*
Plugin Name: Hercules Sentiment Analysis
Description: Adds a sentiment analysis tools that show the overall sentiment (positive, negative, neutral) of comments, posts, and titles so you can better target your audience and respond to their needs.
Author: Todd D. Nestor - todd.nestor@gmail.com
Author URI: http://toddnestor.com
Version: 1.2.0
License: GNU General Public License v3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
{
	echo "Oops! No direct access please :)";
	exit;
}


	require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'start.php' );

	$var_name = 'hercules_sentiment';

	global $$var_name;
	$$var_name                   = new \Hercules\Framework;
	$$var_name->plugin_directory = dirname( __FILE__ );
	$$var_name->InitiateAll();