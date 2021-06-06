<?php
namespace WHMCS\Module\Addon\Simotel\Models;

class Client extends \Illuminate\Database\Eloquent\Model
{
    protected $table="tblclients";

    public function getFullnameAttribute()
    {
        return $this->firstname . " " . $this->lastname;
    }
}
