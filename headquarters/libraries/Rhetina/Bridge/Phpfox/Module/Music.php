<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Rhetina\Bridge\Phpfox\Module;

use Rhetina\Bridge\Phpfox\Component\Module;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

class Music extends Module
{

    public function upload($result)
    {

        if ($_REQUEST['section_module'] == 'music') {
            $result['callback'] = '';
            if ( !Phpfox::getUserParam( 'music.can_upload_music_public' ) ) {
                return array( "error" => 'You can not upload public music' );
            }

            $aVals = Phpfox::getLib( 'request' )->getArray( 'val' );

            if ( empty( $aVals ) ) {
                return array( 'error' => 'Invalid sent values' );
            }

            if ( trim( $aVals['music_title'] ) == '' ) {
                if ( isset( $_REQUEST['newName'] ) && $_REQUEST['newName'] != '' ) {
                    $aVals['music_title'] = $_REQUEST['newName'];
                } else {
                    $aVals['music_title'] = preg_replace( "/\.[^$]*/", "",
                            $_REQUEST['qqfilename'] );
                }
            }

            $aVals['title'] = $aVals['music_title'];
            $_FILES['mp3']['name'] = $_REQUEST['qqfilename'];

            if ( $aSong = Phpfox::getService( 'music.process' )->upload( $aVals,
                    (isset( $aVals['album_id'] ) ? (int) $aVals['album_id'] : 0 ) ) ){
                Phpfox::addMessage( Phpfox::getPhrase( 'music.song_successfully_uploaded' ) );
                $result['callback'] = 'window.location.href = \'' . Phpfox::getLib( 'url' )->permalink( 'music',
                                $aSong['song_id'], $aSong['title'] ) . '\';';
                $result['success'] = true;
            } else {
                if ( isset( $aVals['music_title'] ) ) {
                    $result['callback'] .= 'window.parent.$Core.resetActivityFeedError(\'' . implode( '<br />',
                                    Phpfox_Error::get() ) . '\');';
                } else {
                    $result['callback'] .= 'window.parent.$(\'#js_music_upload_song\').show(); window.parent.$(\'.js_upload_song\').remove();';
                    $result['callback'] .= 'window.parent.alert(\'' . implode( '<br />',
                                    Phpfox_Error::get() ) . '\');';
                }
            }

            return $result;
        }
    }

    public function frameProcess($result)
    {

        if ( !Phpfox::isUser() ) {
            return array( "error" => 'We only allow logged in users the ability to upload music' );
        }

        if ( !Phpfox::getUserParam( 'music.can_upload_music_public' ) ) {
            return array( "error" => 'You can not upload public music' );
        }

        $sModule = Phpfox::getLib( 'request' )->get( 'module', false );
        $iItem = Phpfox::getLib( 'request' )->getInt( 'item', false );

        $aCallback = false;
        if ( $sModule !== false && $iItem !== false && Phpfox::hasCallback( $sModule,
                        'getMusicDetails' ) ){
            if ( ($aCallback = Phpfox::callback( $sModule . '.getMusicDetails',
                            array( 'item_id' => $iItem ) ) ) ){
                if ( $sModule == 'pages' && !Phpfox::getService( 'pages' )->hasPerm( $iItem,
                                'music.share_music' ) ){
                    return array( 'error' => 'Unable to view this item due to privacy settings.' );
                }
            }
        }

        if ( trim( $aVals['music_title'] ) == '' ) {
            if ( isset( $_REQUEST['newName'] ) && $_REQUEST['newName'] != '' ) {
                $aVals['music_title'] = $_REQUEST['newName'];
            } else {
                $aVals['music_title'] = pathinfo( $_REQUEST['qqfilename'],
                        PATHINFO_FILENAME );
            }
        }

        $result['filename'] = $aVals['music_title'];

        $aVals['title'] = $aVals['music_title'];
        $_FILES['mp3']['name'] = $_REQUEST['qqfilename'];

        $aVals = Phpfox::getLib( 'request' )->getArray( 'val' );

        if ( !empty( $aVals ) ) {
            if ( ($aSong = Phpfox::getService( 'music.process' )->upload( $aVals,
                    (isset( $aVals['album_id'] ) ? (int) $aVals['album_id'] : 0 ) ) ) ){

                $iFeedId = Phpfox::getService( 'feed.process' )->getLastId();

                if ( Phpfox::isModule( 'video' ) && Phpfox::getParam( 'video.convert_servers_enable' ) ) {
                    $result['callback'] .= 'document.domain = "' . Phpfox::getParam( 'video.convert_js_parent' ) . '";';
                }
                (($sPlugin = Phpfox_Plugin::get( 'rhetinauploader.music.component_controller_upload_feed' )) ? eval( $sPlugin ) : false);
                $result['callback'] .= 'window.parent.$.ajaxCall(\'music.displayFeed\', \'id=' . $iFeedId . '&song_id=' . $aSong['song_id'] . '\', \'GET\');';
            } else {
                $result['error'] = implode( '', Phpfox_Error::get() );
                $result['callback'] .= 'window.parent.$Core.resetActivityFeedError(\'' . implode( '<br />',
                                Phpfox_Error::get() ) . '\');';

                return $result;
            }
        }

        $result['success'] = true;

        return $result;
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
        if ( $sPlugin = Phpfox_Plugin::get( 'rhetinauploader.service_music__call' ) ) {
            return eval( $sPlugin );
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger( 'Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()',
                E_USER_ERROR );
    }

}
