<?php
/*
Plugin Name: XML-RPC Update Check
Plugin URI: http://trog.qgl.org/xmlrpcuc
Description: Provide an XML-RPC mechanism to check for WP site updates
Version: 0.1
Author: David Harrison
Author URI: http://trog.qgl.org/xmlrpcupcheck
License: GPL2
*/

function xmlrpcPluginUpdateCheck( $args ) 
{
	global $wp_xmlrpc_server, $wp_version;
	$wp_xmlrpc_server->escape( $args );

	$username = $args[0];
	$password = $args[1];

	if ( ! $user = $wp_xmlrpc_server->login( $username, $password ) )
		return $wp_xmlrpc_server->error;

	// From wp-updates-notifier.php by Scott Cariss: 
	do_action( "wp_update_plugins" ); // force WP to check plugins for updates
	$update_plugins = get_site_transient( 'update_plugins' ); // get information of updates

	$plugins_need_update = $update_plugins->response; // plugins that need updating

	$active_plugins = array_flip( get_option( 'active_plugins' ) ); // find which plugins are active
	$plugins_need_update = array_intersect_key( $plugins_need_update, $active_plugins ); // only keep plugins that are active

	$resp = sizeof($plugins_need_update);

	if ($resp > 0)
		return true;
	else 
		return false;

//	return $resp;

	//
	// This stuff prints the real/pretty name of the plugin and gets the version instead of it's internal name. 
	// Just leaving it in in case it's useful later. 
	/*
	require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' ); // Required for plugin API
	require_once( ABSPATH . WPINC . '/version.php' ); // Required for WP core version
	foreach ( $plugins_need_update as $key => $data ) 
	{ 
		$plugin_info = get_plugin_data( WP_PLUGIN_DIR . "/" . $key ); 
		$info = plugins_api( 'plugin_information', array( 'slug' => $data->slug ) ); 
		print sprintf( __( "Plugin: %s is out of date. Please update from version %s to %s", "wp-updates-notifier" ), $plugin_info['Name'], $plugin_info['Version'], $data->new_version ) . "\n";
	}

	// fields: $id, $slug, $plugin, $new_version, $url, $package
	return $plugins_need_update;
	*/
}

function xmlrpcCoreUpdateCheck( $args ) 
{
	global $wp_xmlrpc_server, $wp_version;
	$wp_xmlrpc_server->escape( $args );

	$username = $args[0];
	$password = $args[1];

	if ( ! $user = $wp_xmlrpc_server->login( $username, $password ) )
		return $wp_xmlrpc_server->error;

	do_action( "wp_version_check" ); // force WP to check its core for updates
	$update_core = get_site_transient( "update_core" ); // get information of updates
	if ( 'upgrade' == $update_core->updates[0]->response ) // is WP core update available?
		return true;
	else
		return false;
}

function xmlrpcThemeUpdateCheck( $args ) 
{
	global $wp_xmlrpc_server, $wp_version;
	$wp_xmlrpc_server->escape( $args );

	$username = $args[0];
	$password = $args[1];

	if ( ! $user = $wp_xmlrpc_server->login( $username, $password ) )
		return $wp_xmlrpc_server->error;

	do_action( "wp_update_themes" ); // force WP to check for theme updates
	$update_themes = get_site_transient( 'update_themes' ); // get information of updates

	if ( !empty( $update_themes->response ) ) // any theme updates available?
		return true;
	else
		return false;
}

// Returns an array of true/falses
function xmlrpcUpdatesCheck( $args ) 
{
	global $wp_xmlrpc_server, $wp_version;
	$wp_xmlrpc_server->escape( $args );

	$username = $args[0];
	$password = $args[1];

	if ( ! $user = $wp_xmlrpc_server->login( $username, $password ) )
		return $wp_xmlrpc_server->error;

	$updates = array();
	$updates['core'] = xmlrpcCoreUpdateCheck($args);
	$updates['plugins'] = xmlrpcPluginUpdateCheck($args);
	$updates['themes'] = xmlrpcThemeUpdateCheck($args);

	return $updates;
}

function trog_new_xmlrpc_methods( $methods ) 
{
	$methods['trog.xmlrpcPluginUpdateCheck'] = 'xmlrpcPluginUpdateCheck';
	$methods['trog.xmlrpcCoreUpdateCheck'] = 'xmlrpcCoreUpdateCheck';
	$methods['trog.xmlrpcThemeUpdateCheck'] = 'xmlrpcThemeUpdateCheck';
	$methods['trog.xmlrpcUpdatesCheck'] = 'xmlrpcUpdatesCheck';

	return $methods;   
}
add_filter( 'xmlrpc_methods', 'trog_new_xmlrpc_methods');
