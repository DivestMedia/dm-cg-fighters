<?php if(!defined('ABSPATH')) die('Fatal Error');
/*
Plugin Name: DM Fighters Plugin
Plugin URI: #
Description: Divestmedia plugin for Fighters
Author: ljopleda@gmail.com
Version: 1.0
Author URI:
*/
define( 'DM_FIGHTERS_VERSION', '1.0' );
define( 'DM_FIGHTERS_MIN_WP_VERSION', '4.4' );
define( 'DM_FIGHTERS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DM_FIGHTERS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'DM_FIGHTERS_DEBUG' , true );

require_once(DM_FIGHTERS_PLUGIN_DIR . 'lib/class-dm-fighters-generate.php');
require_once(DM_FIGHTERS_PLUGIN_DIR . '/vendor/autoload.php');
require_once(DM_FIGHTERS_PLUGIN_DIR . 'lib/class-dm-fighters.php');
require_once(DM_FIGHTERS_PLUGIN_DIR . 'lib/class-dm-fights.php');

if(class_exists('DMFighters')){
  register_activation_hook(__FILE__, array('DMFighters', 'activate'));
  register_deactivation_hook(__FILE__, array('DMFighters', 'deactivate'));
  $DMFighters = new DMFighters();
}

if(class_exists('DMFights')){
  $DMFights = new DMFights();
}

// if(class_exists('DMFightersGenerate')){
//   $DMFightersGenerate = new DMFightersGenerate();
// }
