<?php

namespace WHMCS\Module\Addon\Simotel\PBX;


/**
 *
 */
trait Errors
{
    private $errors = [];

    /**
     * @return bool
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }


    protected function addError($msg): bool
    {
        if (is_array($msg))
            $this->errors = array_merge($this->errors, $msg);
        else
            $this->errors[] = $msg;
        return false;
    }

    public function errors()
    {
        return $this->errors;
    }

}
