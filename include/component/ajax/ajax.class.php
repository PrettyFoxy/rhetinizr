<?php

defined('PHPFOX') or exit('NO DICE!');

class Rhetinizr_Component_Ajax_Ajax extends Phpfox_Ajax
{
    public function verifyRequest()
    {
        $iPluginId = $this->get('plugin_id');
        $oCore = phpfox::getService('rhetinizr.beating');
        if ($iPluginId) {
            $aPlugin = $oCore->getPlugin($iPluginId);
            if (isset($aPlugin['plugin_id'])) {
                phpfox::getBlock('rhetinizr.verifyrequest',
                        array(
                    'aPlugin' => $aPlugin,
                ));
            }
        }
    }

    public function checkForUpdates()
    {
        $iPluginId = $this->get('plugin_id');
        $oCore = phpfox::getService('rhetinizr.beating');
        if ($iPluginId) {
            $aPlugin = $oCore->getPlugin($iPluginId);
            if (isset($aPlugin['plugin_id'])) {
                $aResult = $oCore->version($aPlugin);
                $sVersion = $aResult['version'];
                $bStatus = version_compare($aPlugin['version'], $sVersion);
                echo json_encode(array(
                    'status' => $bStatus,
                    'version' => $sVersion
                ));
            }
        }
    }

    public function quickView()
    {
        $oCore = phpfox::getService('rhetinizr.beating');
        $sUrl = $this->get('url');
        if (!empty($sUrl)) {
            $aResult = $oCore->quickPreview($sUrl);
            phpfox::getBlock('rhetinizr.preview',
                    array(
                'aResult' => $aResult,
            ));
        }
    }

    public function getPlugins()
    {
        $oCore = phpfox::getService('rhetinizr.beating');
        $aPlugins = $oCore->getCFPlugins();
        if (count($aPlugins)) {
            phpfox::getBlock('rhetinizr.products',
                    array(
                'aPlugins' => $aPlugins,
            ));
        }
    }

    public function getNews()
    {
        $oCore = phpfox::getService('rhetinizr.beating');
        $aNews = $oCore->getNews();
        if (count($aNews)) {
            phpfox::getBlock('rhetinizr.news',
                    array(
                'aNews' => $aNews,
            ));
        }
    }

    public function verify()
    {
        $iPluginId = $this->get('plugin_id');
        $sLicense = $this->get('license_key');

        $oCore = phpfox::getService('rhetinizr.beating');
        if ($iPluginId) {
            $aPlugin = $oCore->getPlugin($iPluginId);
            if (isset($aPlugin['plugin_id'])) {
                $aPlugin['ls'] = $sLicense;
                $aResult = $oCore->verify($aPlugin);
                echo json_encode($aResult);
            }
        }
    }

    public function mycheck()
    {
        $oCore = phpfox::getService('rhetinizr.beating');
        $oCore->returncheck();
    }
}
