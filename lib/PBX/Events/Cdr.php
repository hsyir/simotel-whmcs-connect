<?php
namespace WHMCS\Module\Addon\Simotel\PBX\Events;

use WHMCS\Module\Addon\Simotel\Models\Call;

class Cdr extends PbxEvent
{
    /**
     * @return bool
     */
    public function dispatch(): bool
    {
        try {
            $uniqueId = $this->request->unique_id;
            $call = Call::whereUniqueId($uniqueId)->first();
            if (!$call) {
                $this->addError("Call Data Not Found!");
                return false;
            }
            $call->record = $this->request->record;
            $call->billsec = $this->request->billsec;
            $call->status = $this->request->disposition;
            $call->start_at = $this->request->starttime;
            $call->end_at = $this->request->endtime;
            $call->save();

        } catch (\Exception $exception) {
            $this->addError($exception->getMessage());
            return false;
        }
        return true;
    }
}
