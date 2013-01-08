<?php

/**
 *  Main CMS module
 *
 *  @package Monstra
 *  @author Romanenko Sergey / Awilum [awilum@msn.com]
 *  @copyright 2012 Romanenko Sergey / Awilum
 *  @version $Id$
 *  @since 1.0.0
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *  Monstra is free software. This version may have been modified pursuant
 *  to the GNU General Public License, and as distributed it includes or
 *  is derivative of works licensed under the GNU General Public License or
 *  other free or open source software licenses.
 *  See COPYING.txt for copyright notices and details.
 */



// Main engine defines
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', rtrim(dirname(__FILE__), '\\/'));
define('BACKEND', false);
define('MONSTRA_ACCESS', true);

/* TEMP CODE BEGIN */
function byteFormat($size)
{
    $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
    return @round($size/pow(1024, ($i=floor(log($size, 1024)))), 2).' '.$unit[$i];
}
$_start = memory_get_usage();
/* TEMP CODE END */

// First check for installer then go
if (file_exists('install.php')) {
    if (isset($_GET['install'])) {
        if ($_GET['install'] == 'done') {
            // Try to delete install file if not delete manually
            @unlink('install.php');
            // Redirect to main page
            header('location: index.php');
        }
    } else {
        include 'install.php';
    }
} else {

    // Load Engine init file
    require_once ROOT. DS . 'engine'. DS . '_init.php';

    // Check for maintenance mod
    if ('on' == Option::get('maintenance_status')) {

        // Set maintenance mode for all except admin and editor
        if ((Session::exists('user_role')) and (Session::get('user_role') == 'admin' or Session::get('user_role') == 'editor')) {
            // Monstra show this page :)
        } else {
            die (Text::toHtml(Option::get('maintenance_message')));
        }
    }

    // Frontend pre render
    Action::run('frontend_pre_render');

    // Load site template
    require MINIFY . DS . 'theme.' . Site::theme() . '.' . Site::template() . '.template.php';

    // Frontend pre render
    Action::run('frontend_post_render');

    // Flush (send) the output buffer and turn off output buffering
    ob_end_flush();
}

/* TEMP CODE BEGIN */
echo byteFormat(memory_get_usage() - $_start);
/* TEMP CODE END */