<?php

/**
 * Plugin Name: GMT Slack WP Rest API
 * Plugin URI: https://github.com/cferdinandi/gmt-slack-wp-rest-api/
 * GitHub Plugin URI: https://github.com/cferdinandi/gmt-slack-wp-rest-api/
 * Description: Provides a WP Rest API endpoint for inviting users to your Slack channel.
 * Version: 0.2.0
 * Author: Chris Ferdinandi
 * Author URI: http://gomakethings.com
 * License: GPLv3
 */


// Load includes
if ( !class_exists( 'Slack_Invite' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'includes/slack-api.php' );
}
require_once( plugin_dir_path( __FILE__ ) . 'includes/options.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/api.php' );