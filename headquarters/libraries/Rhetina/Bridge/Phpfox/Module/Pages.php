<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Rhetina\Bridge\Phpfox\Module;

use Rhetina\Bridge\Phpfox\Component\Module;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

class Pages extends Module
{

    public function upload($result)
    {
        // Make sure the user group is actually allowed to upload an image
        if ( !Phpfox::getUserParam( 'photo.can_upload_photos' ) ) {
            $result['error'] = 'You are not allowed to upload images';
            die( json_encode( $result ) );
        }

        if ( $_REQUEST['section_module'] == 'pages' && isset( $_FILES['image']['name'] ) && ($_FILES['image']['name'] != '') ) {

            Phpfox::isUser( true );
            Phpfox::getUserParam( 'pages.can_add_new_pages', true );

            $result['file'] = $_FILES['image'];

            if ( isset( $_REQUEST['qqfilename'] ) ) {
                $_FILES['image']['name'][0] = $_REQUEST['qqfilename'];
            }

            $aImage = Phpfox::getLib( 'file' )->load( 'image',
                    array( 'jpg', 'gif', 'png', 'jpeg' ),
                    (Phpfox::getUserParam( 'pages.max_upload_size_pages' ) === 0 ? null : (Phpfox::getUserParam( 'pages.max_upload_size_pages' ) / 1024) )
            );

            if ($aImage === false) {
                $result['error'] = 'Invalid image';

                return json_encode( $result );
            }

            $aPage = Phpfox::getService( 'pages' )->getForEdit( $_REQUEST['item_id'] );
            if ( !empty( $aPage['image_path'] ) ) {
                Phpfox::getService( 'pages.process' )->deleteImage( $aPage );
            }
            unset( $aPage );

            $oImage = Phpfox::getLib( 'image' );

            $sFileName = Phpfox::getLib( 'file' )->upload( 'image',
                    Phpfox::getParam( 'pages.dir_image' ), $_REQUEST['item_id'] );
            $iFileSizes = filesize( Phpfox::getParam( 'pages.dir_image' ) . sprintf( $sFileName,
                            '' ) );

            $aUpdate['image_path'] = $sFileName;
            $aUpdate['image_server_id'] = Phpfox::getLib( 'request' )->getServer( 'PHPFOX_SERVER_ID' );

            $iSize = 50;
            $oImage->createThumbnail( Phpfox::getParam( 'pages.dir_image' ) . sprintf( $sFileName,
                            '' ),
                    Phpfox::getParam( 'pages.dir_image' ) . sprintf( $sFileName,
                            '_' . $iSize ), $iSize, $iSize );
            $iFileSizes += filesize( Phpfox::getParam( 'pages.dir_image' ) . sprintf( $sFileName,
                            '_' . $iSize ) );
            $result['thumbnails'][$iSize] = Phpfox::getParam( 'pages.dir_image' ) . sprintf( $sFileName,
                            '_' . $iSize );

            $iSize = 120;
            $oImage->createThumbnail( Phpfox::getParam( 'pages.dir_image' ) . sprintf( $sFileName,
                            '' ),
                    Phpfox::getParam( 'pages.dir_image' ) . sprintf( $sFileName,
                            '_' . $iSize ), $iSize, $iSize );
            $iFileSizes += filesize( Phpfox::getParam( 'pages.dir_image' ) . sprintf( $sFileName,
                            '_' . $iSize ) );
            $result['thumbnails'][$iSize] = Phpfox::getParam( 'pages.dir_image' ) . sprintf( $sFileName,
                            '_' . $iSize );

            $iSize = 200;
            $oImage->createThumbnail( Phpfox::getParam( 'pages.dir_image' ) . sprintf( $sFileName,
                            '' ),
                    Phpfox::getParam( 'pages.dir_image' ) . sprintf( $sFileName,
                            '_' . $iSize ), $iSize, $iSize );
            $iFileSizes += filesize( Phpfox::getParam( 'pages.dir_image' ) . sprintf( $sFileName,
                            '_' . $iSize ) );

            $result['thumbnails'][$iSize] = Phpfox::getParam( 'pages.dir_image' ) . sprintf( $sFileName,
                            '_' . $iSize );

            define( 'PHPFOX_PAGES_IS_IN_UPDATE', true );

            $aUser = Phpfox::getLib('database')->select( 'user_id' )
                    ->from( Phpfox::getT( 'user' ) )
                    ->where( 'profile_page_id = ' . (int) $_REQUEST['item_id'] )
                    ->execute( 'getSlaveRow' );

            Phpfox::getService( 'user.process' )->uploadImage( $aUser['user_id'],
                    true,
                    Phpfox::getParam( 'pages.dir_image' ) . sprintf( $sFileName,
                            '' ) );

            unset( $aUser );

            // Update user space usage
            Phpfox::getService( 'user.space' )->update( Phpfox::getUserId(),
                    'pages', $iFileSizes );

            $result['uploadResult'] = Phpfox::getLib('database')->update( Phpfox::getT( 'pages' ),
                    $aUpdate, 'page_id = ' . (int) $_REQUEST['item_id'] );

            $result['upload'] = $aUpdate;
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
        if ( $sPlugin = Phpfox_Plugin::get( 'rhetinauploader.service_pages__call' ) ) {
            return eval( $sPlugin );
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger( 'Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()',
                E_USER_ERROR );
    }

}
