<?php

namespace WHMCS\Module\Addon\Simotel\Models;

use WHMCS\Module\Addon\Simotel\WhmcsOperations;

class Client extends \Illuminate\Database\Eloquent\Model
{
    protected $table = "tblclients";

    public function getFullnameAttribute()
    {
        return $this->firstname . " " . $this->lastname;
    }

    public function getProfileUrlAttribute()
    {
        $adminPanelUrl=WhmcsOperations::getAdminPanelUrl();
        return $adminPanelUrl . "/clientssummary.php?userid={$this->id}";
    }


}
