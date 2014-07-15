<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Rhetina\Bridge\Phpfox\Module;

use Rhetina\Bridge\Phpfox\Component\Module;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

class User extends Module
{
    public function upload($result)
    {
        // Make sure the user group is actually allowed to upload an image
        if (!Phpfox::getUserParam('photo.can_upload_photos')) {
            $result['error'] = 'You are not allowed to upload images';

            return $result;
        }

        if ($_REQUEST['section_module'] == 'user' && isset($_FILES['image']['name']) && ($_FILES['image']['name'] != '')) {

            $result['file'] = $_FILES['image'];
            if (isset($_REQUEST['qqfilename'])) {
                $_FILES['image']['name'][0] = $_REQUEST['qqfilename'];
            }
            $aImage = Phpfox::getLib('file')->load('image',
                    array('jpg', 'gif', 'png', 'jpeg'),
                    (Phpfox::getUserParam('user.max_upload_size_profile_photo') === 0 ? null : (Phpfox::getUserParam('user.max_upload_size_profile_photo') / 1024))
            );

            if ($aImage === false) {
                $result['error'] = 'Invalid image';

                return $result;
            }

            $force_crop = Phpfox::getUserParam('user.force_cropping_tool_for_photos');

            if (($aImage = Phpfox::getService('user.process')->uploadImage(
                    Phpfox::getUserId(), $force_crop ? false : true
                    )) !== false) {

                if ($force_crop) {
                    $result['callback'] .= 'window.location.href = \'' . Phpfox::getLib('url')->makeUrl('user.photo.process',
                                    array('step' => urlencode(base64_encode(serialize($aImage))))) . '\';';
                } else {
                    $result['callback'] .= 'window.location.href = \'' . Phpfox::getLib('url')->permalink('user.photo',
                                    null,
                                    Phpfox::getPhrase('user.profile_photo_successfully_uploaded')) . '\';';
                }
            }

            $result['uploadResult'] = $aImage;

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
        if ( $sPlugin = Phpfox_Plugin::get( 'rhetinauploader.service_user__call' ) ) {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()',
                E_USER_ERROR);
    }

}
