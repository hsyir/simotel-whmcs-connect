<?php

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\Simotel\Admin\AdminDispatcher;
use WHMCS\Module\Addon\Simotel\Client\ClientDispatcher;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require(__DIR__ . "/vendor/autoload.php");


/**
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
        'version' => '2.3',
        'fields' => [
            'PhoneFields' => [
                'FriendlyName' => 'Phone Fields',
                'Type' => 'text',
                'Size' => '50',
                'Default' => 'phonenumber,address2',
                'Description' => 'Comma separated database fields name ',
            ],
            'WsAppKey' => [
                'FriendlyName' => 'Pusher app key',
                'Type' => 'text',
                'Size' => '25',
                'Default' => '',
                'Description' => '',
            ],
            'WsAppId' => [
                'FriendlyName' => 'Pusher app ID',
                'Type' => 'text',
                'Size' => '25',
                'Default' => '',
                'Description' => '',
            ],
            'WsHost' => [
                'FriendlyName' => 'Pusher Server Host',
                'Type' => 'text',
                'Size' => '25',
                'Default' => '',
                'Description' => '',
            ],
            'WsPort' => [
                'FriendlyName' => 'Pusher Server Port',
                'Type' => 'text',
                'Size' => '25',
                'Default' => '',
                'Description' => '',
            ],
            'WsCluster' => [
                'FriendlyName' => 'Pusher Cluster',
                'Type' => 'text',
                'Size' => '25',
                'Default' => '',
                'Description' => '',
            ],
            'WsSecret' => [
                'FriendlyName' => 'Pusher Secret',
                'Type' => 'password',
                'Size' => '25',
                'Default' => '',
                'Description' => '',
            ],
            'PopUpTime' => [
                'FriendlyName' => 'CallerId show time',
                'Type' => 'text',
                'Size' => '5',
                'Default' => '30',
                'Description' => 'Seconds',
            ],
            'BlockPattern' => [
                'FriendlyName' => 'Regx block pattern',
                'Type' => 'text',
                'Size' => '25',
                'Default' => '/^[0-9]{3}$/',
                'Description' => 'Regular expression - block unwanted incoming calls',
            ],
            'CustomAdminPath' => [
                'FriendlyName' => 'Custom admin path',
                'Type' => 'text',
                'Size' => '50',
                'Default' => '',
            ],
         /*   'SimotelConnectTimeout' => [
                'FriendlyName' => 'Simotel Connect Timeout',
                'Type' => 'text',
                'Size' => '10',
                'Default' => '10',
                'Description' => 'Seconds - click to dial',
            ],
            'SimotelResponseTimeout' => [
                'FriendlyName' => 'Simotel Response Timeout',
                'Type' => 'text',
                'Size' => '10',
                'Default' => '5',
                'Description' => 'Seconds - click to dial',
            ],*/
            'PhoneNumberRegx' => [
                'FriendlyName' => 'Regx phone numbers',
                'Type' => 'text',
                'Size' => '25',
                'Default' => '/09[0-9]{9}/g',
                'Description' => 'Discover phone numbers on whole document',
            ],
        ]
    ];
}

/**
 * @return array Optional success/failure message
 */
function simotel_activate()
{

    // Create custom tables and schema required by your module
    try {
        Capsule::schema()
            ->create(
                'mod_simotel_options',
                function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->increments('id');
                    $table->integer('admin_id')->nullable();
                    $table->string('key');
                    $table->text('value');
                }
            );
        Capsule::schema()
            ->create(
                'mod_simotel_calls',
                function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->increments('id');
                    $table->string('unique_id',20)->unique();
                    $table->dateTime('start_at')->nullable();
                    $table->dateTime('end_at')->nullable();
                    $table->string('src')->nullable();
                    $table->string('dst')->nullable();
                    $table->string('record')->nullable();
                    $table->string('status')->nullable();
                    $table->integer('billsec')->nullable();
                    $table->integer('admin_id')->nullable();
                    $table->integer('client_id')->nullable();
                    $table->string('comment')->nullable();
                    $table->string('direction')->nullable();
                    $table->string('server_profile')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                }
            );
        return [
            // Supported values here include: success, error or info
            'status' => 'success',
            'description' => 'Ok.',
        ];
    } catch (\Exception $e) {
        return [
            // Supported values here include: success, error or info
            'status' => "error",
            'description' => 'Unable to create mod_simotel_options: ' . $e->getMessage(),
        ];
    }
}

/**
 * @return array Optional success/failure message
 */
function simotel_deactivate()
{
    // Undo any database and schema modifications made by your module here
    try {
        Capsule::schema()
            ->dropIfExists('mod_simotel_options');

        Capsule::schema()
            ->dropIfExists('mod_simotel_calls');

        return [
            // Supported values here include: success, error or info
            'status' => 'success',
            'description' => 'This is a demo module only. '
                . 'In a real module you might report a success here.',
        ];
    } catch (\Exception $e) {
        return [
            // Supported values here include: success, error or info
            "status" => "error",
            "description" => "Unable to drop mod_addonexample: {$e->getMessage()}",
        ];
    }
}

/**
 * @return void
 */
function simotel_upgrade($vars)
{
}

/**
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
 * @param array $vars
 *
 * @return string
 */
function simotel_sidebar($vars)
{
    $configs = \WHMCS\Module\Addon\Simotel\WhmcsOperations::getConfig();
    $adminUrl = \WHMCS\Module\Addon\Simotel\WhmcsOperations::getAdminPanelUrl();
    $sidebar = "<div class='sidebar-header'>
    <i class='fas fa-box-alt'></i>
    منو سیموتل
    </div> 
    <ul class='menu'>
        <li><a href='$adminUrl/addonmodules.php?module=simotel&action=moduleConfigForm'>تنظیمات سیستم</a></li>
        <li><a href='$adminUrl/addonmodules.php?module=simotel'>تنظیمات کاربر</a></li>
        <li><a href='$adminUrl/addonmodules.php?module=simotel&action=cdrReport'>ریز مکالمات</a></li>
        <li><a href='$adminUrl/addonmodules.php?module=simotel&action=adminsList'>تنظیمات همکاران</a></li>
    </ul>
    
    ";
    return $sidebar;
}

/**
 * @return array
 */
function simotel_clientarea($vars)
{
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    $dispatcher = new ClientDispatcher();
    return $dispatcher->dispatch($action, $vars);
}
