<?php

defined('PHPFOX') or exit('No dice!');
class ceofox_Component_Block_ceofoxnews extends Phpfox_Component
{
    public function process()   
    {
       $aNews = $this->getParam('aNews');
       if(!count($aNews))
       {
           return false;
       }
       $this->template()->assign(
            array('aNews' =>$aNews)
       );
    }
}
?>
