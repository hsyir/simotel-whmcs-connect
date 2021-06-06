<?php
namespace WHMCS\Module\Addon\Simotel\Models;

use Baloot\EloquentHelper;
use Carbon\CarbonInterval;

class Call extends \Illuminate\Database\Eloquent\Model
{
    use EloquentHelper;
    protected $table="mod_simotel_calls";
    protected $fillable=["unique_id", "src", "client_id" , "admin_id","status","dst"];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function getBillsecMinutesAttribute()
    {
        return $this["billsec"] ? CarbonInterval::seconds($this['billsec'])->cascade()->forHumans() : "0";
    }
}
