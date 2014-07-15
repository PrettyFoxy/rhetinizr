<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Rhetina\Bridge\Phpfox\Module;

use Rhetina\Bridge\Phpfox\Component\Module;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

class Photo extends Module
{
    public function frameProcess($result)
    {
        return $this->upload( $result );
    }

    public function upload($result)
    {
        $result['callback'] = '';

        if ( !isset( $_REQUEST['section_module'] ) || $_REQUEST['section_module'] != 'photo' ) {
            return array( "error" => 'You\'re not in the right place' );
        }

        // We only allow users the ability to upload images.
        if ( !Phpfox::isUser() ) {
            return array( "error" => 'We only allow users the ability to upload images' );
        }

        // Make sure the user group is actually allowed to upload an image
        if ( !Phpfox::getUserParam( 'photo.can_upload_photos' ) ) {
            return array( "error" => 'You\'re not allowed to upload images' );
        }

        if ( isset( $_REQUEST['picup'] ) ) {
            $_FILES['Filedata'] = $_FILES['image'];
            unset( $_FILES['image'] );
        }
        if ( isset( $_FILES['Filedata'] ) && !isset( $_FILES['image'] ) ) { // photo.enable_mass_uploader == true
            $_FILES['image'] = array(); //$_FILES['Filedata'];
            $_FILES['image']['error']['image'] = UPLOAD_ERR_OK;
            $_FILES['image']['name']['image'] = $_FILES['Filedata']['name'];
            $_FILES['image']['type']['image'] = $_FILES['Filedata']['type'];
            $_FILES['image']['tmp_name']['image'] = $_FILES['Filedata']['tmp_name'];
            $_FILES['image']['size']['image'] = $_FILES['Filedata']['size'];
        }

        $fn = (isset( $_SERVER['HTTP_X_FILENAME'] ) ? $_SERVER['HTTP_X_FILENAME'] : false);
        if ($fn) {
            define( 'PHPFOX_HTML5_PHOTO_UPLOAD', true );

            $sHTML5TempFile = PHPFOX_DIR_CACHE . 'image_' . md5( PHPFOX_DIR_CACHE . $fn . uniqid() );

            file_put_contents(
                    $sHTML5TempFile, file_get_contents( 'php://input' )
            );
            $_FILES['image'] = array(
                'name' => array( $fn ),
                'type' => array( 'image/jpeg' ),
                'tmp_name' => array( $sHTML5TempFile ),
                'error' => array( 0 ),
                'size' => array( filesize( $sHTML5TempFile ) )
            );
        }

        // If no images were uploaded lets get out of here.
        if ( !isset( $_FILES['image'] ) ) {
            return array( "error" => 'No images were uploaded' );
        }

        if ( ($iFlood = Phpfox::getUserParam( 'photo.flood_control_photos' )) !== 0 ) {
            $aFlood = array(
                'action' => 'last_post', // The SPAM action
                'params' => array(
                    'field' => 'time_stamp', // The time stamp field
                    'table' => Phpfox::getT( 'photo' ), // Database table we plan to check
                    'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                    'time_stamp' => $iFlood * 60 // Seconds);
                )
            );

            // actually check if flooding
            if ( Phpfox::getLib( 'spam' )->check( $aFlood ) ) {
                $result['error'] = Phpfox::getPhrase( 'photo.uploading_photos_a_little_too_soon' ) . ' ' . Phpfox::getLib( 'spam' )->getWaitTime();
                Phpfox_Error::set( Phpfox::getPhrase( 'photo.uploading_photos_a_little_too_soon' ) . ' ' . Phpfox::getLib( 'spam' )->getWaitTime() );
            }

            if ( !Phpfox_Error::isPassed() ) {
                // Output JavaScript
                if (!$bIsInline) {
                    $result['callback'] .= 'window.parent.document.getElementById(\'js_progress_cache_holder\').style.display = \'none\';';
                    $result['callback'] .= 'window.parent.document.getElementById(\'js_photo_form_holder\').style.display = \'block\';';
                    $result['callback'] .= 'window.parent.document.getElementById(\'js_upload_error_message\').innerHTML = \'<div class="error_message">' . implode( '',
                                    Phpfox_Error::get() ) . '</div>\';';
                } else {
                    if ( isset( $aVals['is_cover_photo'] ) ) {
                        $result['callback'] .= 'window.parent.$(\'#js_cover_photo_iframe_loader_error\').html(\'<div class="error_message">' . implode( '',
                                        Phpfox_Error::get() ) . '</div>\');';
                    } else {
                        $result['callback'] .= 'window.parent.$Core.resetActivityFeedError(\'' . implode( '',
                                        Phpfox_Error::get() ) . '\');';
                    }
                }

                return $result;
            }
        }

        $oFile = Phpfox::getLib( 'file' );
        $oImage = Phpfox::getLib( 'image' );
        $aVals = Phpfox::getLib( 'request' )->get( 'val' );
        if ( defined( 'PHPFOX_HTML5_PHOTO_UPLOAD' ) ) {
            $aParts = explode( '&', $_SERVER['HTTP_X_POST_FORM'] );
            foreach ($aParts as $sPart) {
                $aReq = explode( '=', $sPart );
                if ( substr( $aReq[0], 0, 3 ) == 'val' ) {
                    $aVals[preg_replace( '/val\[(.*?)\]/i', '\\1', $aReq[0] )] = (isset( $aReq[1] ) ? $aReq[1] : '');
                }
            }
        }
        if ( !is_array( $aVals ) ) {
            $aVals = array();
        }

        $result['vals'] = $aVals;

        $bIsInline = false;
        if ( isset( $aVals['action'] ) && $aVals['action'] == 'upload_photo_via_share' ) {
            $bIsInline = true;
        }

        $oServicePhotoProcess = Phpfox::getService( 'photo.process' );
        $aImages = array();
        $aFeed = array();
        $iFileSizes = 0;
        $iCnt = 0;

        (($sPlugin = Phpfox_Plugin::get( 'rhetinauploader.photo.component_controller_frame_start' )) ? eval( $sPlugin ) : false);

        if ( !empty( $aVals['album_id'] ) ) {
            $aAlbum = Phpfox::getService( 'photo.album' )->getAlbum( Phpfox::getUserId(),
                    $aVals['album_id'], true );
        }

        if ( isset( $_REQUEST['status_info'] ) && !empty( $_REQUEST['status_info'] ) ) {
            $aVals['description'] = $_REQUEST['status_info'];
        }

        if ( isset( $_REQUEST['qqfilename'] ) ) {
            $_FILES['image']['name'][0] = $_REQUEST['qqfilename'];
        }

        foreach ($_FILES['image']['error'] as $iKey => $sError) {
            if ($sError == UPLOAD_ERR_OK) {
                if ( $aImage = $oFile->load( 'image[' . $iKey . ']',
                        array(
                    'jpg',
                    'gif',
                    'png'
                        ),
                        (Phpfox::getUserParam( 'photo.photo_max_upload_size' ) === 0 ? null : (Phpfox::getUserParam( 'photo.photo_max_upload_size' ) / 1024) )
                        )
                ){
                    if ( isset( $aVals['action'] ) && $aVals['action'] == 'upload_photo_via_share' ) {
                        $aVals['description'] = (isset( $aVals['is_cover_photo'] ) ? null : $aVals['status_info']);
                        $aVals['type_id'] = (isset( $aVals['is_cover_photo'] ) ? '2' : '1');
                    }

                    if ( $iId = $oServicePhotoProcess->add( Phpfox::getUserId(),
                            array_merge( $aVals, $aImage ) ) ){
                        $iCnt++;
                        $aPhoto = Phpfox::getService( 'photo' )->getForProcess( $iId );

                        // Move the uploaded image and return the full path to that image.
                        $sFileName = $oFile->upload( 'image[' . $iKey . ']',
                                Phpfox::getParam( 'photo.dir_photo' ),
                                (Phpfox::getParam( 'photo.rename_uploaded_photo_names' ) ? Phpfox::getUserBy( 'user_name' ) . '-' . $aPhoto['title'] : $iId ),
                                (Phpfox::getParam( 'photo.rename_uploaded_photo_names' ) ? array() : true )
                        );

                        if (!$sFileName) {
                            return array( 'error' => implode( '',
                                        Phpfox_Error::get() ) );
                        }

                        // Get the original image file size.
                        $iFileSizes += filesize( Phpfox::getParam( 'photo.dir_photo' ) . sprintf( $sFileName,
                                        '' ) );

                        // Get the current image width/height
                        $aSize = getimagesize( Phpfox::getParam( 'photo.dir_photo' ) . sprintf( $sFileName,
                                        '' ) );

                        // Update the image with the full path to where it is located.
                        $aUpdate = array(
                            'destination' => $sFileName,
                            'width' => $aSize[0],
                            'height' => $aSize[1],
                            'server_id' => Phpfox::getLib( 'request' )->getServer( 'PHPFOX_SERVER_ID' ),
                            'allow_rate' => (empty( $aVals['album_id'] ) ? '1' : '0'),
                            'description' => (empty( $aVals['description'] ) ? null : $aVals['description'])
                        );
                        if ( isset( $aVals['category_id'] ) || isset( $aVals['category_id[]'] ) ) {
                            $aUpdate['category_id'] = $aVals['category_id'] || $aVals['category_id[]'];
                        }

                        $oServicePhotoProcess->update( Phpfox::getUserId(),
                                $iId, $aUpdate );

                        // Assign vars for the template.
                        $aImages[] = array(
                            'photo_id' => $iId,
                            // 'album' => (isset($aAlbum) ? $aAlbum : null),
                            'server_id' => Phpfox::getLib( 'request' )->getServer( 'PHPFOX_SERVER_ID' ),
                            'destination' => $sFileName,
                            'name' => $aImage['name'],
                            'ext' => $aImage['ext'],
                            'size' => $aImage['size'],
                            'width' => $aSize[0],
                            'height' => $aSize[1],
                            'completed' => 'false'
                        );

                        (($sPlugin = Phpfox_Plugin::get( 'rhetinauploader.photo.component_controller_frame_process_photo' )) ? eval( $sPlugin ) : false);
                    }
                }
            }
        }

        $iFeedId = 0;

        // Make sure we were able to upload some images
        if ( count( $aImages ) ) {
            if ( defined( 'PHPFOX_IS_HOSTED_SCRIPT' ) ) {
                unlink( Phpfox::getParam( 'photo.dir_photo' ) . sprintf( $sFileName,
                                '' ) );
            }

            $aCallback = (!empty( $aVals['callback_module'] ) ? Phpfox::callback( $aVals['callback_module'] . '.addPhoto',
                                    $aVals['callback_item_id'] ) : null);

            $sAction = (isset( $aVals['action'] ) ? $aVals['action'] : 'view_photo');

            // Have we posted an album for these set of photos?
            if ( isset( $aVals['album_id'] ) && !empty( $aVals['album_id'] ) ) {
                $aAlbum = Phpfox::getService( 'photo.album' )->getAlbum( Phpfox::getUserId(),
                        $aVals['album_id'], true );

                // Set the album privacy
                Phpfox::getService( 'photo.album.process' )->setPrivacy( $aVals['album_id'] );

                // Check if we already have an album cover
                if ( !Phpfox::getService( 'photo.album.process' )->hasCover( $aVals['album_id'] ) ) {
                    // Set the album cover
                    Phpfox::getService( 'photo.album.process' )->setCover( $aVals['album_id'],
                            $iId );
                }

                // Update the album photo count
                if ( !Phpfox::getUserParam( 'photo.photo_must_be_approved' ) ) {
                    Phpfox::getService( 'photo.album.process' )->updateCounter( $aVals['album_id'],
                            'total_photo', false, count( $aImages ) );
                }

                if ( !$bIsInline )
                    $sAction = 'view_album';
            }

            // Update the user space usage
            Phpfox::getService( 'user.space' )->update( Phpfox::getUserId(),
                    'photo', $iFileSizes );

            (($sPlugin = Phpfox_Plugin::get( 'rhetinauploader.photo.component_controller_frame_process_photos_done' )) ? eval( $sPlugin ) : false);

            if ( isset( $aVals['page_id'] ) && $aVals['page_id'] > 0 ) {
                if ( Phpfox::getService( 'pages.process' )->setCoverPhoto( $aVals['page_id'],
                                $iId, true ) ){
                    $aVals['is_cover_photo'] = 1;
                } else {
                    $result['error'] = implode( Phpfox_Error::get() );
                    $result['callback'] .= 'alert("Something went wrong: ' . implode( Phpfox_Error::get() ) . '");';
                }
            }

            if ( isset( $_REQUEST['picup'] ) ) {
                //exit();
            } else {
                $sExtra = '';
                if ( !empty( $aVals['start_year'] ) && !empty( $aVals['start_month'] ) && !empty( $aVals['start_day'] ) ) {
                    $sExtra .= '&start_year= ' . $aVals['start_year'] . '&start_month= ' . $aVals['start_month'] . '&start_day= ' . $aVals['start_day'] . '';
                }

                $callback_args = '';

                $callback_args .= ((isset( $aVals['core']['is_user_profile'] ) && !empty( $aVals['core']['is_user_profile'] )) ? 'is_user_profile=' . $aVals['core']['is_user_profile'] . '&' : 'is_user_profile=0&');
                $callback_args .= ((isset( $aVals['core']['profile_user_id'] ) && !empty( $aVals['core']['profile_user_id'] )) ? 'profile_user_id=' . $aVals['core']['profile_user_id'] . '&' : 'profile_user_id=0&');
                $callback_args .= ((isset( $aVals['page_id'] ) && !empty( $aVals['page_id'] )) ? 'is_page=1&' : '');
                $callback_args .= 'js_disable_ajax_restart=true' . $sExtra . '&twitter_connection=' . ((isset( $aVals['connection'] ) && isset( $aVals['connection']['twitter'] )) ? $aVals['connection']['twitter'] : '0');
                $callback_args .= '&facebook_connection=' . (isset( $aVals['connection']['facebook'] ) ? $aVals['connection']['facebook'] : '0');
                $callback_args .= '&custom_pages_post_as_page=' . Phpfox::getLib( 'request' )->get( 'custom_pages_post_as_page' );
                $callback_args .= '&photos=' . urlencode( json_encode( $aImages ) );
                $callback_args .= '&action=' . $sAction . '' . (isset( $iFeedId ) ? '&feed_id=' . $iFeedId : '');
                $callback_args .= '' . ($aCallback !== null ? '&callback_module=' . $aCallback['module'] . '&callback_item_id=' . $aCallback['item_id'] : '');
                $callback_args .= '&parent_user_id=' . (isset( $aVals['parent_user_id'] ) ? (int) $aVals['parent_user_id'] : 0) . '&is_cover_photo=' . (isset( $aVals['is_cover_photo'] ) ? '1' : '0') . ((isset( $aVals['page_id'] ) && $aVals['page_id'] > 0) ? '&page_id=' . $aVals['page_id'] : '');

                if ( isset( $_REQUEST['uploader'] ) && $_REQUEST['uploader'] == 'rhetinauploader' ) {

                    $result = $this->process( $callback_args, $result );
                } else {
                    if ( $bIsInline && Phpfox::isModule( 'video' ) && Phpfox::getParam( 'video.convert_servers_enable' ) ) {
                        $result['callback'] .= 'document.domain = "' . Phpfox::getParam( 'video.convert_js_parent' ) . '";';
                    }

                    if ( !defined( 'PHPFOX_HTML5_PHOTO_UPLOAD' ) ) {
                        $result['callback'] .= 'window.parent.';
                    }
                    $result['callback'] .= '$.ajaxCall(\'photo.process\', \'' . $callback_args . '\');';
                }
            }

            (($sPlugin = Phpfox_Plugin::get( 'rhetinauploader.photo.component_controller_frame_process_photos_done_javascript' )) ? eval( $sPlugin ) : false);
        } else {
            // Output JavaScript
            if ( defined( 'PHPFOX_HTML5_PHOTO_UPLOAD' ) ) {
                unlink( $sHTML5TempFile );
                header( 'HTTP/1.1 500 Internal Server Error' );
            }

            if (!$bIsInline) {
                $result['callback'] .= 'window.parent.$(\'#js_progress_cache_holder\').hide();';
                $result['callback'] .= 'window.parent.document.getElementById(\'js_photo_form_holder\').style.display = \'block\';';
                $result['callback'] .= 'window.parent.document.getElementById(\'js_upload_error_message\').innerHTML = \'<div class="error_message">' . implode( '',
                                Phpfox_Error::get() ) . '</div>\';';
            } else {
                if ( Phpfox::isModule( 'video' ) && Phpfox::getParam( 'video.convert_servers_enable' ) ) {
                    $result['callback'] .= 'document.domain = "' . Phpfox::getParam( 'video.convert_js_parent' ) . '";';
                }
                if ( isset( $aVals['is_cover_photo'] ) ) {
                    $result['callback'] .= 'window.parent.$(\'#js_cover_photo_iframe_loader_upload\').hide();';
                    $result['callback'] .= 'window.parent.$(\'#js_activity_feed_form\').show();';
                    $result['callback'] .= 'window.parent.$(\'#js_cover_photo_iframe_loader_error\').html(\'<div class="error_message">' . implode( '',
                                    Phpfox_Error::get() ) . '</div>\');';
                } else {
                    $result['callback'] .= 'window.parent.$Core.resetActivityFeedError(\'' . implode( '',
                                    Phpfox_Error::get() ) . '\');';
                }
            }
        }

        $result['success'] = true;

        return $result;
    }

    // function taken from Ajax photo.process
    public function process($callback_args, $result)
    {
        $callback_args = explode( '&', $callback_args );
        $args = array();
        foreach ($callback_args as $callback_arg) {
            $callback_arg = explode( '=', $callback_arg );
            if ( !isset( $callback_arg[1] ) ) {
                $callback_arg[1] = '';
            }
            $args[$callback_arg[0]] = $callback_arg[1];
        }
        $aPostPhotos = $args['photos'];

        if ( is_array( $aPostPhotos ) ) {
            $aImages = array();
            foreach ($aPostPhotos as $aPostPhoto) {
                $aPart = json_decode( urldecode( $aPostPhoto ), true );
                $aImages[] = $aPart[0];
            }
        } else {

            $aImages = json_decode( urldecode( $aPostPhotos ), true );
        }

        $oImage = Phpfox::getLib( 'image' );
        $iFileSizes = 0;
        $iGroupId = 0;
        $bProcess = false;
        $bIsPicup = false;

        foreach ($aImages as $iKey => $aImage) {
            $aImage['destination'] = urldecode( $aImage['destination'] );
            if ( isset( $aImage['picup'] ) ) {
                $bIsPicup = true;
            }
            if ($aImage['completed'] == 'false') {
                $aPhoto = Phpfox::getService( 'photo' )->getForProcess( $aImage['photo_id'] );
                if ( isset( $aPhoto['photo_id'] ) ) {
                    if ( Phpfox::getParam( 'core.allow_cdn' ) ) {
                        Phpfox::getLib( 'cdn' )->setServerId( $aPhoto['server_id'] );
                    }

                    if ($aPhoto['group_id'] > 0) {
                        $iGroupId = $aPhoto['group_id'];
                    }

                    $sFileName = $aPhoto['destination'];

                    foreach ( Phpfox::getParam( 'photo.photo_pic_sizes' ) as
                                $iSize ) {
                        // Create the thumbnail
                        if ( $oImage->createThumbnail( Phpfox::getParam( 'photo.dir_photo' ) . sprintf( $sFileName,
                                                '' ),
                                        Phpfox::getParam( 'photo.dir_photo' ) . sprintf( $sFileName,
                                                '_' . $iSize ), $iSize, $iSize,
                                        true,
                                        ((Phpfox::getParam( 'photo.enabled_watermark_on_photos' ) && Phpfox::getParam( 'core.watermark_option' ) != 'none') ? (Phpfox::getParam( 'core.watermark_option' ) == 'image' ? 'force_skip' : true) : false ) ) === false ){

                            continue;
                        }

                        if ( Phpfox::getParam( 'photo.enabled_watermark_on_photos' ) ) {
                            $oImage->addMark( Phpfox::getParam( 'photo.dir_photo' ) . sprintf( $sFileName,
                                            '_' . $iSize ) );
                        }

                        // Add the new file size to the total file size variable
                        $iFileSizes += filesize( Phpfox::getParam( 'photo.dir_photo' ) . sprintf( $sFileName,
                                        '_' . $iSize ) );

                        if ( defined( 'PHPFOX_IS_HOSTED_SCRIPT' ) ) {
                            unlink( Phpfox::getParam( 'photo.dir_photo' ) . sprintf( $sFileName,
                                            '_' . $iSize ) );
                        }
                    }

                    //if (((Phpfox::getParam('photo.delete_original_after_resize') || !Phpfox::getParam('core.keep_files_in_server')) && $args['is_page') != 1) && !defined('PHPFOX_IS_HOSTED_SCRIPT'))
                    if ( ((Phpfox::getParam( 'core.allow_cdn' ) && (Phpfox::getParam( 'photo.delete_original_after_resize' ) || !Phpfox::getParam( 'core.keep_files_in_server' ))) && $args['is_page'] != 1) && !defined( 'PHPFOX_IS_HOSTED_SCRIPT' ) ) {
                        Phpfox::getLib( 'file' )->unlink( Phpfox::getParam( 'photo.dir_photo' ) . sprintf( $sFileName,
                                        '' ) );
                    } elseif ( Phpfox::getParam( 'photo.enabled_watermark_on_photos' ) ) {
                        $oImage->addMark( Phpfox::getParam( 'photo.dir_photo' ) . sprintf( $sFileName,
                                        '' ) );
                    }

                    $aImages[$iKey]['completed'] = 'true';

                    (($sPlugin = Phpfox_Plugin::get( 'rhetinauploader.photo.component_ajax_ajax_process__1' )) ? eval( $sPlugin ) : false);

                    break;
                }
            }
        }

        // Update the user space usage
        Phpfox::getService( 'user.space' )->update( Phpfox::getUserId(),
                'photo', $iFileSizes );

        if ( isset( $args['profile_user_id'] ) && !empty( $args['profile_user_id'] ) && $args['profile_user_id'] != Phpfox::getUserId() && Phpfox::isModule( 'notification' ) ) {
            Phpfox::getService( 'notification.process' )->add( 'feed_comment_profile',
                    $aPhoto['photo_id'], $args['profile_user_id'] );
        }

        $result['uploaded_images'] = urlencode( json_encode( $args ) );

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
        if ( $sPlugin = Phpfox_Plugin::get( 'rhetinauploader.service_photo__call' ) ) {
            return eval( $sPlugin );
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger( 'Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()',
                E_USER_ERROR );
    }

}
