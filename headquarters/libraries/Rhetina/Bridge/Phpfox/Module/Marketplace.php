<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Rhetina\Bridge\Phpfox\Module;

use Rhetina\Bridge\Phpfox\Component\Module;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

class Marketplace extends Module
{

    public function upload($result)
    {

        // Make sure the user group is actually allowed to upload an image
        if ( !Phpfox::getUserParam( 'photo.can_upload_photos' ) ) {
            return array( 'error' => 'You are not allowed to upload images' );
        }

        if ( $_REQUEST['section_module'] == 'marketplace' && isset( $_FILES['image'] ) ) {
            $result['callback'] = '';

            Phpfox::getUserParam( 'marketplace.can_create_listing', true );

            $result['file'] = $_FILES['image'];

            if ( isset( $_REQUEST['qqfilename'] ) ) {
                $_FILES['image']['name'][0] = $_REQUEST['qqfilename'];
            }

            $oImage = Phpfox::getLib( 'image' );
            $oFile = Phpfox::getLib( 'file' );

            $aSizes = array( 50, 120, 200, 400 );

            $iFileSizes = 0;
            foreach ($_FILES['image']['error'] as $iKey => $sError) {
                if ($sError == UPLOAD_ERR_OK) {
                    if ( $aImage = $oFile->load( 'image[' . $iKey . ']',
                            array( 'jpg', 'gif', 'png', 'jpeg' ),
                            (Phpfox::getUserParam( 'marketplace.max_upload_size_listing' ) === 0 ? null : (Phpfox::getUserParam( 'marketplace.max_upload_size_listing' ) / 1024) )
                            )
                    ){
                        $sFileName = Phpfox::getLib( 'file' )->upload( 'image[' . $iKey . ']',
                                Phpfox::getParam( 'marketplace.dir_image' ),
                                $_REQUEST['item_id'] );

                        $iFileSizes += filesize( Phpfox::getParam( 'marketplace.dir_image' ) . sprintf( $sFileName,
                                        '' ) );

                        $result['ImageDbSave'] = Phpfox::getLib('database')->insert( Phpfox::getT( 'marketplace_image' ),
                                array( 'listing_id' => $_REQUEST['item_id'], 'image_path' => $sFileName,
                            'server_id' => Phpfox::getLib( 'request' )->getServer( 'PHPFOX_SERVER_ID' ) ) );

                        foreach ($aSizes as $iSize) {
                            $oImage->createThumbnail( Phpfox::getParam( 'marketplace.dir_image' ) . sprintf( $sFileName,
                                            '' ),
                                    Phpfox::getParam( 'marketplace.dir_image' ) . sprintf( $sFileName,
                                            '_' . $iSize ), $iSize, $iSize );
                            $oImage->createThumbnail( Phpfox::getParam( 'marketplace.dir_image' ) . sprintf( $sFileName,
                                            '' ),
                                    Phpfox::getParam( 'marketplace.dir_image' ) . sprintf( $sFileName,
                                            '_' . $iSize . '_square' ), $iSize,
                                    $iSize, false );

                            $iFileSizes += filesize( Phpfox::getParam( 'marketplace.dir_image' ) . sprintf( $sFileName,
                                            '_' . $iSize ) );

                            $result['thumbnails'][$iSize] = Phpfox::getParam( 'marketplace.url_image' ) . sprintf( $sFileName,
                                            '_' . $iSize );
                        }
                    }
                }
            }

            if ($iFileSizes === 0) {
                return array( 'error' => 'Error on saving these files' );
            }

            $result['DbSave'] = Phpfox::getLib('database')->update( Phpfox::getT( 'marketplace' ),
                    array( 'image_path' => $sFileName, 'server_id' => Phpfox::getLib( 'request' )->getServer( 'PHPFOX_SERVER_ID' ) ),
                    'listing_id = ' . $_REQUEST['item_id'] );

            (($sPlugin = Phpfox_Plugin::get( 'rhetinauploader.marketplace.service_process_update__1' )) ? eval( $sPlugin ) : false);

            // Update user space usage
            Phpfox::getService( 'user.space' )->update( Phpfox::getUserId(),
                    'marketplace', $iFileSizes );

            if ($_REQUEST['action'] == 'addNew') {
                $result['callback'] .= 'window.location.href = \''
                        . Phpfox::getLib( 'url' )->permalink( 'marketplace',
                                $_REQUEST['item_id'] ) . '\';';
            }

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
        if ( $sPlugin = Phpfox_Plugin::get( 'rhetinauploader.service_marketplace__call' ) ) {
            return eval( $sPlugin );
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger( 'Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()',
                E_USER_ERROR );
    }

}
