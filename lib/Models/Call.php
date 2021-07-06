<?php

namespace WHMCS\Module\Addon\Simotel\Models;

use Baloot\EloquentHelper;
use Carbon\CarbonInterval;
use WHMCS\Module\Addon\Simotel\WhmcsOperations;

class Call extends \Illuminate\Database\Eloquent\Model
{
    use EloquentHelper;

    protected $table = "mod_simotel_calls";
    protected $fillable = ["unique_id", "src", "client_id", "admin_id", "status", "dst", "direction"];

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
    public function getBillsecShortAttribute()
    {
        $seconds=$this["billsec"];
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds - ($hours * 60 * 60)) / 60);
        $seconds = $seconds - $hours * 3600 - $minutes * 60;
        return sprintf("%d:%d:%d",
            $hours,
            $minutes,
            $seconds
        );

    }

    public function getStatusIconUrlAttribute()
    {
        switch ($this->status) {
            case "Ringing":
                $icon = "ringing";
                break;
            case "NO ANSWER":
                $icon = "noAnswer";
                break;
            case "InUse":
                $icon = "inUse";
                break;
            case "BUSY":
                $icon = "busy";
                break;
            case "ANSWERED":
                $icon = "answered";
                break;
        }
        global $CONFIG;
        return $CONFIG["SystemUrl"] . "/modules/addons/simotel/templates/images/cdr/{$icon}.png";


    }
    public function getDirectionIconUrlAttribute()
    {
        global $CONFIG;
        return $CONFIG["SystemUrl"] . "/modules/addons/simotel/templates/images/cdr/{$this->direction}.png";

    }
    public function getRecordedIconUrlAttribute()
    {
        global $CONFIG;
        return $CONFIG["SystemUrl"] . "/modules/addons/simotel/templates/images/recorded.png";

    }
    public function getAudioUrlAttribute()
    {
        return WhmcsOperations::getAdminPanelUrl("/addonmodules.php?module=simotel&action=downloadAudio&call_id=".$this->id);
    }
    public function getHasAudioAttribute()
    {
        return $this->record != null;
    }
}
