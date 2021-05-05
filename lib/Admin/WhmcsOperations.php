<?php

namespace WHMCS\Module\Addon\Simotel\Admin;


use Smarty;
use WHMCS\Database\Capsule;

class WhmcsOperations
{
    public static function render($tplFile, $params = [])
    {
        $smarty = new Smarty;
        foreach ($params as $key => $value)
            $smarty->assign($key, $value);
        $smarty->caching = false;
        $smarty->compile_dir = $GLOBALS['templates_compiledir'];
        return $smarty->fetch(__DIR__ . "/../../templates/{$tplFile}.tpl");
    }

    public static function getCurrentAdminId()
    {
        $command = 'GetAdminDetails';
        $results = localAPI($command);
        if ($results['result'] == 'success') {
            return $results["adminid"];
        } else {
            return null;
        }
    }

    public static function getCurrentAdminExten()
    {
        $adminId = self::getCurrentAdminId();
        $admin = Capsule::table('tbladmins')->find($adminId);

        return $admin->signature;
    }


    public static function getConfig()
    {
        $configData = Capsule::table('tbladdonmodules')->whereModule("simotel")->get();
        $config = $configData->pluck("value","setting")->toArray();
        return $config;
    }


    public static function getClientsByPhoneNumber($phoneNumber){
        return Capsule::table('tblclients')->wherePhonenumber($phoneNumber)->get();
    }

    public static function getFirstClientByPhoneNumber($phoneNumber){
        return Capsule::table('tblclients')->wherePhonenumber($phoneNumber)->first();
    }



}
