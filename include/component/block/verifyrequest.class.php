<?php

defined('PHPFOX') or exit('No dice!');
class ceofox_Component_Block_verifyrequest extends Phpfox_Component
{
    public function process()   
    {
       $aPlugin = $this->getParam('aPlugin');
       if(!isset($aPlugin['plugin_id']))
       {
           return false;
       }
       $this->template()->assign(
        array(
            'aPlugin'=>$aPlugin,
        )
       );
    }
}
?>
