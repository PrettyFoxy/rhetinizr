<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

class Rhetinizr_Component_Controller_Admincp_Index extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $this->url()->send('admincp.rhetinizr.dashboard');
    }
}
