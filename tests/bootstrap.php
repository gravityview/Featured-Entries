<?php
ini_set( 'display_errors', 'on' );
error_reporting( E_ALL );

$_gv_plugin_dir = getenv( 'GV_PLUGIN_DIR' ) ? : '/tmp/gravityview';
$_gv_featured_entries_dir = __DIR__;

// Load the GV testing enviornment.
require_once $_gv_plugin_dir . '/tests/bootstrap.php';
require_once $_gv_featured_entries_dir . '/../featured-entries.php';
gv_extension_featured_entries_load();
