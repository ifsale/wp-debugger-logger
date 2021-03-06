<?php
	/**
	* Plugin Name: PhpToolCase Debugger & Logger
	* Plugin URI: http://phptoolcase.com/guides/ptc-debug-guide.html
	* Description: A PHP Debugger & Logger to speed up plugins development. Features include: log messages and sql queries, watch for variable changes and time execution, code coverage analysis to view executed lines. Visit the <a href="http://phptoolcase.com/guides/ptc-debug-guide.html">Home Page</a> for more info.
	* Version: 0.7
	* Author: Carlo Pietrobattista
	* Author URI: http://phptoolcase.com
	* License: GPL2
	*/
	
	/*  Copyright 2013 Carlo Pietrobattista  (email: carlo@salapc.com)

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

	// change this for the watch , trace and code coverage utilities according to the environment 
	declare( ticks = 100 );		// the lower the number, the slower it will take
	
	// Make sure we don't expose any info if called directly
	if ( !function_exists( 'add_action' ) ) 
	{
		echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
		exit;
	}
	
	// require the handyman component to autoload classes
	require_once( 'PhpToolCase/PtcHm.php' );			

	use PhpToolCase\PtcHandyMan;
	use PhpToolCase\PtcDb;
	
	PtcHandyMan::addDirs( array
	(
		dirname( __FILE__ ) ,
		'PhpToolCase'	=>	dirname(  __FILE__ ) . '/PhpToolCase' ,
		'PtcDebug'	=>   dirname( __FILE__ )
	) );
	
	PtcHandyMan::addSeparator( '-' );						// wordpress separator for class names
	PtcHandyMan::addConvention( 'class{SEP}{CLASS}' );		// wordpress name convention for classes
	PtcHandyMan::register( );							// register the autoloader

	// add the db connection
	PtcDb::add( array
	(
		'name'				=>	DB_USER ,
		'pass'				=>	DB_PASSWORD ,
		'db'					=>	DB_NAME ,
		'query_builder'			=>	true ,
		'query_builder_class'	=>	'PhpToolCase\PtcQueryBuilder'
	) );

	//WP_PtcDebug::load();
	add_action( 'init' , array( 'WP_PtcDebug' , 'load' ) , 0 ); 
	
	register_activation_hook( __FILE__ , array( 'WP_PtcDebug' , 'install' ) );
	if ( is_admin( ) ){ add_action( 'admin_menu' , array( 'WP_PtcDebug' , 'admin' ) ); }
	add_action( 'shutdown' , array( 'WP_PtcDebug' , 'wpQueries' ) );
	add_action( 'activated_plugin', array( 'WP_PtcDebug' , 'thisPluginFirst' ) );
	
