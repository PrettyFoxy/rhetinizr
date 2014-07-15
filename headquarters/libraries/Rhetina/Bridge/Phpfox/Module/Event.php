<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Rhetina\Bridge\Phpfox\Module;

use Rhetina\Bridge\Phpfox\Component\Module;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

class Event extends Module
{
    public function upload($result)
    {
        if (!Phpfox::isUser()) {
            $result['error'] = 'You have to be logged in to upload images';

            return $result;
        }
        // Make sure the user group is actually allowed to upload an image
        if (!Phpfox::getUserParam('photo.can_upload_photos')) {
            $result['error'] = 'You are not allowed to upload images';

            return $result;
        }

        if ($_REQUEST['section_module'] == 'event' && isset($_FILES['image']['name']) && ($_FILES['image']['name'] != '')) {

            $result['file'] = $_FILES['image'];

            if (isset($_REQUEST['qqfilename'])) {
                $_FILES['image']['name'][0] = $_REQUEST['qqfilename'];
            }

            $aImage = Phpfox::getLib('file')->load('image',
                    array('jpg', 'gif', 'png', 'jpeg'),
                    (Phpfox::getUserParam('event.max_upload_size_event') === 0 ? null : (Phpfox::getUserParam('event.max_upload_size_event') / 1024))
            );

            if ($aImage === false) {
                $result['error'] = 'Invalid image';

                return $result;
            }

            $oImage = Phpfox::getLib('image');

            $sFileName = Phpfox::getLib('file')->upload('image',
                    Phpfox::getParam('event.dir_image'), $_REQUEST['item_id']);

            $iFileSizes = filesize(Phpfox::getParam('event.dir_image') . sprintf($sFileName,
                            ''));

            $aSql['image_path'] = $sFileName;
            $aSql['server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');

            $result['thumbnails'] = array();

            $iSize = 50;
            $oImage->createThumbnail(Phpfox::getParam('event.dir_image') . sprintf($sFileName,
                            ''),
                    Phpfox::getParam('event.dir_image') . sprintf($sFileName,
                            '_' . $iSize), $iSize, $iSize);
            $iFileSizes += filesize(Phpfox::getParam('event.dir_image') . sprintf($sFileName,
                            '_' . $iSize));
            $result['thumbnails'][$iSize] = Phpfox::getParam('event.url_image') . sprintf($sFileName,
                            '_' . $iSize);

            $iSize = 120;
            $oImage->createThumbnail(Phpfox::getParam('event.dir_image') . sprintf($sFileName,
                            ''),
                    Phpfox::getParam('event.dir_image') . sprintf($sFileName,
                            '_' . $iSize), $iSize, $iSize);
            $iFileSizes += filesize(Phpfox::getParam('event.dir_image') . sprintf($sFileName,
                            '_' . $iSize));
            $result['thumbnails'][$iSize] = Phpfox::getParam('event.url_image') . sprintf($sFileName,
                            '_' . $iSize);

            $iSize = 200;
            $oImage->createThumbnail(Phpfox::getParam('event.dir_image') . sprintf($sFileName,
                            ''),
                    Phpfox::getParam('event.dir_image') . sprintf($sFileName,
                            '_' . $iSize), $iSize, $iSize);
            $iFileSizes += filesize(Phpfox::getParam('event.dir_image') . sprintf($sFileName,
                            '_' . $iSize));
            $result['thumbnails'][$iSize] = Phpfox::getParam('event.url_image') . sprintf($sFileName,
                            '_' . $iSize);

            // Update user space usage
            Phpfox::getService('user.space')->update(Phpfox::getUserId(),
                    'event', $iFileSizes);

            $result['uploadResult'] = Phpfox::getLib('database')->update(Phpfox::getT('event'),
                    $aSql, 'event_id = ' . (int) $_REQUEST['item_id']);

            $result['upload'] = $aSql;
            $result['success'] = true;

            return $result;
        }
    }

    public function delete($result)
    {
        return $result;
    }

    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ( $sPlugin = Phpfox_Plugin::get( 'rhetinauploader.service_event__call' ) ) {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()',
                E_USER_ERROR);
    }

}
