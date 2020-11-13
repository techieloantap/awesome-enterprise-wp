<?php
/*
Plugin Name: Awesome Enterprise WP
Plugin URI: http://www.getawesomestudio.com
Description: Awesome Enterprise Framework is a shortcode based low code platform along with massive collection beautifully designed, fully responsive and easy to use UI parts. 
Version: 3.0.0
Author: WPoets
Author URI: http://www.wpoets.com
License: GPLv3 or Later
*/


if (!defined('IS_WP'))define('IS_WP', true);
if (!defined('AWESOME_DEBUG'))define('AWESOME_DEBUG', false);

if (!defined('AWESOME_CORE_POST_TYPE'))define('AWESOME_CORE_POST_TYPE', 'awesome_core');
if (!defined('AWESOME_APPS_POST_TYPE'))define('AWESOME_APPS_POST_TYPE', 'aw2_app');

if (!defined('REQUEST_START_POINT'))define('REQUEST_START_POINT', '');

define('AWESOME_APP_BASE_PATH', SITE_URL . REQUEST_START_POINT);




$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'), false);
define('AWE_VERSION',$plugin_data['Version']);

require  __DIR__ .'/vendor/autoload.php';


$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
  'https://github.com/WPoets/awesome-enterprise-wp',
  __FILE__,
  'awesome-enterprise-wp'
);

//Optional: Check for automatical release
$myUpdateChecker->getVcsApi()->enableReleaseAssets();
/*********************** plugin-update-checker code end ***************************/



require_once AWESOME_PATH.'/libraries/util/util.php';
require_once AWESOME_PATH.'/includes/aw2_library.php';
//amit::sort out

require AWESOME_PATH.'/vendor/autoload.php';


require_once AWESOME_PATH.'/includes/awesome_flow.php';
require_once AWESOME_PATH.'/includes/awesome_app.php';
require_once AWESOME_PATH.'/includes/awesome_auth.php';
require_once AWESOME_PATH.'/includes/awesome-controllers.php';

require_once __DIR__ .'/includes/apps_setup_wp.php';
require_once __DIR__ .'/includes/app_seo.php';
require_once __DIR__ .'/includes/awesome-menus.php';
require_once __DIR__ .'/includes/app-rights.php';


require_once __DIR__ .'/includes/monoframe.php';
require_once __DIR__ .'/includes/wordpress-hooks.php';


define('HANDLERS_PATH', AWESOME_PATH.'/core-handlers');
define('EXTRA_HANDLERS_PATH', AWESOME_PATH.'/extra-handlers');

aw2_library::loader_handlers_from_path(HANDLERS_PATH,'structure','lang','cache','session');
aw2_library::loader_handlers_from_path(EXTRA_HANDLERS_PATH,'debug');
aw2_library::loader_handlers_from_path(HANDLERS_PATH,'utils');
aw2_library::loader_handlers_from_path(EXTRA_HANDLERS_PATH,'communication');
aw2_library::loader_handlers_from_path(HANDLERS_PATH,'database');
aw2_library::loader_handlers_from_path(EXTRA_HANDLERS_PATH,'wp');
aw2_library::loader_handlers_from_path(HANDLERS_PATH,'front-end');
aw2_library::loader_handlers_from_path(EXTRA_HANDLERS_PATH,'thrid-party');


//$awe = new AW_Studio();


