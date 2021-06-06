<?php

namespace WHMCS\Module\Addon\Simotel;


use Smarty;
use WHMCS\Database\Capsule;

class WhmcsOperations
{

    private static $configs;
    private static $adminOptions;

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
    public static function getAdminExten($adminId = null)
    {
        $adminId = $adminId ? $adminId : self::getCurrentAdminId();
        $options = new Options();
        return $options->get("exten",$adminId);
    }


    public static function getAdminByExten($exten)
    {
        $options = new Options();
        return $options->findAdmin("exten",$exten);
    }


    /**
     * get simotel module configs
     * @return mixed
     */
    public static function getConfig()
    {
        if (self::$configs)
            return self::$configs;

        $options = new Options();
        $publicConfigs = $options->getPublicOptions()->toArray();

        $configData = Capsule::table('tbladdonmodules')->whereModule("simotel")->get();
        $config = $configData->pluck("value", "setting")->toArray();

        return self::$configs = array_merge($config, $publicConfigs);
    }

    /**
     * get simotel module configs
     * @param null $adminId
     * @return mixed
     */
    public static function getAdminOptions($adminId = null)
    {
        if (self::$adminOptions)
            return self::$adminOptions;

        $adminId = $adminId ? $adminId : self::getCurrentAdminId();
        $options = new Options();
        return self::$adminOptions = $options->getAdminOptions($adminId);
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
            $client = Capsule::table('tblclients')->where($field,"like", "%$phoneNumber%")->first();
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

        $permission = explode(",", $details["allowedpermissions"]);
        $hasPermission = collect($permission)->contains("Configure Addon Modules");

        return $hasPermission;
    }

    public static function getCurrentAdminDetails()
    {
        $command = 'GetAdminDetails';
        $results = localAPI($command);
        return $results;
    }

    public static function dd($var)
    {
        echo "<pre>" . var_export($var,true) . "</pre>";
    }

}
