<?php

class Rhetinizr_Service_Install extends Phpfox_Service
{
    public function on() {

    }

    public function off() {

    }

    public function __call($sMethod, $aArguments)
    {
        if ($sPlugin = Phpfox_Plugin::get('rhetinaheart.service_install__call'))
        {
            return eval($sPlugin);
        }
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
