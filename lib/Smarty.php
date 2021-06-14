<?php

namespace WHMCS\Module\Addon\Simotel;


use Smarty as SmartyClass;
use WHMCS\Database\Capsule;
use \WHMCS\Module\Addon\Simotel\WhmcsOperations;

class Smarty
{
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
        if (!file_exists($file))
            return "Simotel: can't find '$tplFile' " ;

        $smarty = new SmartyClass;

        foreach ($params as $key => $value)
            $smarty->assign($key, $value);

        $smarty->assign("configs", WhmcsOperations::getConfig());
        $smarty->assign("request", new Request);

        $smarty->caching = false;
        $smarty->compile_dir = $GLOBALS['templates_compiledir'];

        return $smarty->fetch($file);
    }
}
