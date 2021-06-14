<?php
namespace WHMCS\Module\Addon\Simotel\Models;

class Admin extends \Illuminate\Database\Eloquent\Model
{
    protected $table="tbladmins";


    public function getFullnameAttribute()
    {
        return $this->firstname . " " . $this->lastname;
    }


}
