<?php // Simple Statistics for Feeds - Tracker

define('WP_USE_THEMES', false);
require(dirname(dirname(dirname(dirname(__FILE__)))) .'/wp-config.php');
require(ABSPATH .'/wp-load.php');

if (!defined('ABSPATH')) exit;

sfs_tracker();

exit;