<?php

defined('PHPFOX') or exit('No dice!');
class ceofox_Component_Block_preview extends Phpfox_Component
{
    public function process()   
    {
       $aResult = $this->getParam('aResult');
       $this->template()->assign(
        array(
            'aResult'=>$aResult,
            'sCoreUrl' =>phpfox::getParam('core.path')
        )
       );
    }
}
?>
