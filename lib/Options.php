<?php

namespace WHMCS\Module\Addon\Simotel;


use WHMCS\Database\Capsule;

/**
 *
 */
class Options
{
    public function get($key, $adminId = null, $default = null)
    {
        $option = $this->findOption($key, $adminId);
        return $option ? $option->value : $default;
    }

    public function set($key, $value, $adminId = null)
    {
        if ($option = $this->findOption($key, $adminId)) {
            $this->update($option->id, $value);
            return $option;
        }
        $this->storeNewItem($key, $value, $adminId);
    }

    private function update($id, $value)
    {
        $affected = Capsule::table('mod_simotel_options')
            ->where('id', $id)
            ->update(['value' => $value]);
    }

    private function findOption($key, $adminId)
    {
        return Capsule::table('mod_simotel_options')
            ->whereKey($key)
            ->when($adminId, function ($q) use ($adminId) {
                return $q->whereAdminId($adminId);
            })
            ->first();
    }

    private function storeNewItem($key, $value, $adminId = null)
    {
        try {
            Capsule::table('mod_simotel_options')->insert(
                ["key" => $key, "value" => $value, "admin_id" => $adminId]
            );
        } catch (\Exception $exception) {
            die($exception->getMessage());
        }
    }

    public function getPublicOptions()
    {
        return Capsule::table('mod_simotel_options')
            ->where("admin_id",null)
            ->get()->pluck("value","key");
    }

    public function getAdminOptions($adminId)
    {
        $jsonOptions = $this->get("adminOptions",$adminId,[]);
        return json_decode($jsonOptions);
    }

    public function setAdminOptions($adminId,$optionsArray)
    {
        $jsonOptions = json_encode($optionsArray);
        $this->set("adminOptions",$jsonOptions,$adminId);
    }
}
