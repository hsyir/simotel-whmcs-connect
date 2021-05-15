<?php

namespace WHMCS\Module\Addon\Simotel;


use Smarty;
use WHMCS\Database\Capsule;

class WhmcsOperations
{


    /**
     * get current login admin id
     * @return integer|null
     */
    public static function getCurrentAdminId()
    {
        $details = self::getCurrentAdminDetails();
        if ($details['result'] == 'success') {
            return $details["adminid"];
        } else {
            return null;
        }
    }

    /**
     * @return mixed
     */
    public static function getCurrentAdminExten()
    {
        $adminId = self::getCurrentAdminId();
        return self::getAdminExten($adminId);
    }

    /**
     * @param $adminId
     * @return mixed
     */
    public static function getAdminExten($adminId=null)
    {
        $adminId = $adminId?$adminId :self::getCurrentAdminId();

        $options = new Options();
        return $options->get("exten",$adminId);

    }


    /**
     * get simotel module configs
     * @return mixed
     */
    public static function getConfig()
    {
        $configData = Capsule::table('tbladdonmodules')->whereModule("simotel")->get();
        $config = $configData->pluck("value", "setting")->toArray();
        return $config;
    }

    /**
     * get first discovered whmcs client by phone number
     *
     * @param $phoneNumber
     * @return null
     */
    public static function getFirstClientByPhoneNumber($phoneNumber)
    {
        $config = WhmcsOperations::getConfig();
        $phoneFields = explode(",", $config["PhoneFields"]);

        foreach ($phoneFields as $field) {
            $client = Capsule::table('tblclients')->where($field, $phoneNumber)->first();
            if ($client)
                return $client;
        }
        return null;
    }

    /**
     * render smarty tpl file
     * @param $tplFile
     * @param array $params
     * @return false|string
     * @throws \SmartyException
     */
    public static function render($tplFile, $params = [])
    {
        $file = __DIR__ . "/../templates/{$tplFile}.tpl";
        if (!file_exists())
            $smarty = new Smarty;
        foreach ($params as $key => $value)
            $smarty->assign($key, $value);

        $configs = \WHMCS\Module\Addon\Simotel\WhmcsOperations::getConfig();
        $smarty->assign("configs", $configs);

        $smarty->caching = false;
        $smarty->compile_dir = $GLOBALS['templates_compiledir'];

        return $smarty->fetch($file);
    }


    public static function adminCanConfigureModuleConfigs()
    {

        $details = self::getCurrentAdminDetails();

        $permission = explode(",",$details["allowedpermissions"]);
        $hasPermission = collect($permission)->contains("Configure Addon Modules");

        return $hasPermission;

    }

    public static function getCurrentAdminDetails(){
        $command = 'GetAdminDetails';
        $results = localAPI($command);
        return $results;
    }

}
