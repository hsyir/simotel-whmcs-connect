<?php
/**
 * WHMCS SDK Sample Addon Module
 *
 * An addon module allows you to add additional functionality to WHMCS. It
 * can provide both client and admin facing user interfaces, as well as
 * utilise hook functionality within WHMCS.
 *
 * This sample file demonstrates how an addon module for WHMCS should be
 * structured and exercises all supported functionality.
 *
 * Addon Modules are stored in the /modules/addons/ directory. The module
 * name you choose must be unique, and should be all lowercase, containing
 * only letters & numbers, always starting with a letter.
 *
 * Within the module itself, all functions must be prefixed with the module
 * filename, followed by an underscore, and then the function name. For this
 * example file, the filename is "simotel" and therefore all functions
 * begin "simotel_".
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://developers.whmcs.com/addon-modules/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

/**
 * Require any libraries needed for the module to function.
 * require_once __DIR__ . '/path/to/library/loader.php';
 *
 * Also, perform any initialization required by the service's library.
 */

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\Simotel\Admin\AdminDispatcher;
use WHMCS\Module\Addon\Simotel\Client\ClientDispatcher;


require(__DIR__ . "/vendor/autoload.php");

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Define addon module configuration parameters.
 *
 * Includes a number of required system fields including name, description,
 * author, language and version.
 *
 * Also allows you to define any configuration parameters that should be
 * presented to the user when activating and configuring the module. These
 * values are then made available in all module function calls.
 *
 * Examples of each and their possible configuration parameters are provided in
 * the fields parameter below.
 *
 * @return array
 */
function simotel_config()
{
    return [
        // Display name for your module
        'name' => 'Simotel-WHMCS connect',
        // Description displayed within the admin interface
        'description' => '',
        // Module author name
        'author' => 'Hossein Yaghmaee',
        // Default language
        'language' => 'english',
        // Version number
        'version' => '1.0',
        'fields' => [
            // a text field type allows for single line text input

            'RootWebUrl' => [
                'FriendlyName' => 'Whmcs root web url',
                'Type' => 'text',
                'Size' => '50',
                'Default' => '',
                'Description' => '',
            ],
            'AdminWebUrl' => [
                'FriendlyName' => 'Whmcs admin panel web url',
                'Type' => 'text',
                'Size' => '50',
                'Default' => '',
                'Description' => '',
            ],
            'PhoneFields' => [
                'FriendlyName' => 'Phone Fields',
                'Type' => 'text',
                'Size' => '50',
                'Default' => 'phonenumber,address2',
                'Description' => 'Comma separated fields name ',
            ],
            'WsAppKey' => [
                'FriendlyName' => 'Web socket app key',
                'Type' => 'text',
                'Size' => '25',
                'Default' => '',
                'Description' => '',
            ],
            'WsAppId' => [
                'FriendlyName' => 'Web socket app ID',
                'Type' => 'text',
                'Size' => '25',
                'Default' => '',
                'Description' => '',
            ],
            'WsHost' => [
                'FriendlyName' => 'Web Socket Server Host',
                'Type' => 'text',
                'Size' => '25',
                'Default' => '',
                'Description' => '',
            ],
            'WsPort' => [
                'FriendlyName' => 'Web Socket Server Port',
                'Type' => 'text',
                'Size' => '25',
                'Default' => '',
                'Description' => '',
            ],
            'WsCluster' => [
                'FriendlyName' => 'Web Socket Cluster',
                'Type' => 'text',
                'Size' => '25',
                'Default' => '',
                'Description' => '',
            ],
            'WsSecret' => [
                'FriendlyName' => 'Web Socket Secret',
                'Type' => 'password',
                'Size' => '25',
                'Default' => '',
                'Description' => '',
            ],
            'PopUpTime' => [
                'FriendlyName' => 'Caller Id pop up time to hide',
                'Type' => 'text',
                'Size' => '5',
                'Default' => '30',
                'Description' => 'Seconds',
            ],
        ]
    ];
}

/**
 * Activate.
 *
 * Called upon activation of the module for the first time.
 * Use this function to perform any database and schema modifications
 * required by your module.
 *
 * This function is optional.
 *
 * @see https://developers.whmcs.com/advanced/db-interaction/
 *
 * @return array Optional success/failure message
 */
function simotel_activate()
{
    // Create custom tables and schema required by your module

}

/**
 * Deactivate.
 *
 * Called upon deactivation of the module.
 * Use this function to undo any database and schema modifications
 * performed by your module.
 *
 * This function is optional.
 *
 * @see https://developers.whmcs.com/advanced/db-interaction/
 *
 * @return array Optional success/failure message
 */
function simotel_deactivate()
{
}

/**
 * Upgrade.
 *
 * Called the first time the module is accessed following an update.
 * Use this function to perform any required database and schema modifications.
 *
 * This function is optional.
 *
 * @see https://laravel.com/docs/5.2/migrations
 *
 * @return void
 */
function simotel_upgrade($vars)
{
}

/**
 * Admin Area Output.
 *
 * Called when the addon module is accessed via the admin area.
 * Should return HTML output for display to the admin user.
 *
 * This function is optional.
 *
 * @see AddonModule\Admin\Controller::index()
 *
 * @return string
 */
function simotel_output($vars)
{
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

    $dispatcher = new AdminDispatcher();
    $response = $dispatcher->dispatch($action, $vars);
    echo $response;
}

/**
 * Admin Area Sidebar Output.
 *
 * Used to render output in the admin area sidebar.
 * This function is optional.
 *
 * @param array $vars
 *
 * @return string
 */
function simotel_sidebar($vars)
{
    $sidebar = '<p>Sidebar output HTML goes here</p>';
    return $sidebar;
}

/**
 * Client Area Output.
 *
 * Called when the addon module is accessed via the client area.
 * Should return an array of output parameters.
 *
 * This function is optional.
 *
 * @see AddonModule\Client\Controller::index()
 *
 * @return array
 */
function simotel_clientarea($vars)
{
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

    $dispatcher = new ClientDispatcher();
    return $dispatcher->dispatch($action, $vars);
}
