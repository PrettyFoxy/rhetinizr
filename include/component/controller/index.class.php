<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

class Rhetinizr_Component_Controller_Index extends Phpfox_Component
{

    public function process()
    {
        $this->url()->send('admincp.rhetinizr.dashboard');
    }
}
