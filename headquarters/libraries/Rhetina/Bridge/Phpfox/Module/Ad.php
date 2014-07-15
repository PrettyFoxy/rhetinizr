<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Rhetina\Bridge\Phpfox\Module;

use Rhetina\Bridge\Phpfox\Component\Module;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

class Ad extends Module
{

    public function upload($result)
    {
        if ( !Phpfox::isUser() ) {
            $result['error'] = 'You have to be logged in to upload images';

            return $result;
        }
        // Make sure the user group is actually allowed to upload an image
        if ( !Phpfox::getUserParam( 'photo.can_upload_photos' ) ) {
            $result['error'] = 'You are not allowed to upload images';

            return $result;
        }

        if ($_REQUEST['section_module'] == 'ad') {

            $aImage = Phpfox::getLib( 'file' )->load( 'image',
                    array( 'jpg', 'gif', 'png' ) );

            if ($aImage === false) {
                $result['error'] = 'No image';

                return $result;
            }

            $aParts = explode( 'x',
                    Phpfox::getLib( 'request' )->get( 'ad_size' ) );

            if ( $sFileName = Phpfox::getLib( 'file' )->upload( 'image',
                    Phpfox::getParam( 'ad.dir_image' ),
                    Phpfox::getUserId() . uniqid() ) ){
                Phpfox::getLib( 'image' )->createThumbnail(
                        Phpfox::getParam( 'ad.dir_image' ) . sprintf( $sFileName,
                                '' ),
                        Phpfox::getParam( 'ad.dir_image' ) . sprintf( $sFileName,
                                '_thumb' ),
                        (Phpfox::getParam( 'ad.multi_ad' ) ? 100 : ($aParts[0] / 3) ),
                        (Phpfox::getParam( 'ad.multi_ad' ) ? 72 : ($aParts[1] - 20) ) );

                Phpfox::getLib( 'file' )->unlink( Phpfox::getParam( 'ad.dir_image' ) . sprintf( $sFileName,
                                '' ) );
                rename( Phpfox::getParam( 'ad.dir_image' ) . sprintf( $sFileName,
                                '_thumb' ),
                        Phpfox::getParam( 'ad.dir_image' ) . sprintf( $sFileName,
                                '' ) );

                $result['thumbnail'] = array(
                    'url' => Phpfox::getParam( 'ad.url_image' ) . sprintf( $sFileName,
                            '' ),
                    'name' => sprintf( $sFileName, '' )
                );
            }

            $result['success'] = true;

            return $result;
        }
    }

    public function delete($result)
    {
        return $result;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod    is the name of the method
     * @param array  $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ( $sPlugin = Phpfox_Plugin::get( 'rhetinauploader.service_ad__call' ) ) {
            return eval( $sPlugin );
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger( 'Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()',
                E_USER_ERROR );
    }

}
