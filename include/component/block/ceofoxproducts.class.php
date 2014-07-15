<?php

defined('PHPFOX') or exit('No dice!');
class ceofox_Component_Block_ceofoxproducts extends Phpfox_Component
{
    public function process()   
    {
       $aPlugins = $this->getParam('aPlugins');
       if(!count($aPlugins))
       {
           return false;
       }
       $this->template()->assign(
            array('aPlugins' =>$aPlugins)
       );
    }
}
?>
