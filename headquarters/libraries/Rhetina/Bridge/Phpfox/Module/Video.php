<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Rhetina\Bridge\Phpfox\Module;

use Rhetina\Bridge\Phpfox\Component\Module;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

class Video extends Module
{

    public function upload($result)
    {

        // upload photo preview for videos
        if ($_REQUEST['section_module'] == 'video' && $_REQUEST['section_controller'] == 'video.edit') {
            if (!isset($_FILES['image']['name']) || ($_FILES['image']['name'] == ''))
                return array('error' => 'Not a valid image');

            $result['file'] = $_FILES['image'];
            if (isset($_REQUEST['qqfilename'])) {
                $_FILES['image']['name'][0] = $_REQUEST['qqfilename'];
            }
            $aImage = Phpfox::getLib('file')->load('image', array('jpg', 'gif', 'png',
                'jpeg'), (Phpfox::getUserParam('video.max_size_for_video_photos') === 0 ? null : (Phpfox::getUserParam('video.max_size_for_video_photos') / 1024))
            );

            if ($aImage === false) {
                return array('error' => 'Invalid image');
            }

            $iFileSizes = 0;

            $oImage = Phpfox::getLib('image');

            $sFileName = Phpfox::getLib('file')->upload('image', Phpfox::getParam('video.dir_image'), $_REQUEST['item_id']);

            $aSql['image_path'] = $sFileName;
            $aSql['server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');

            $iSize = 120;
            $oImage->createThumbnail(Phpfox::getParam('video.dir_image') . sprintf($sFileName, ''), Phpfox::getParam('video.dir_image') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);
            $iFileSizes += filesize(Phpfox::getParam('video.dir_image') . sprintf($sFileName, '_' . $iSize));

            $result['thumbnail'] = Phpfox::getParam('video.url_image') . sprintf($sFileName, '_' . $iSize);
            Phpfox::getLib('file')->unlink(Phpfox::getParam('video.dir_image') . sprintf($sFileName, ''));

            // Update user space usage
            Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'video', $iFileSizes);

            $result['uploadResult'] = Phpfox::getLib('database')->update(
                    Phpfox::getT('video'), $aSql, 'video_id = ' . (int) $_REQUEST['item_id']
            );

            $result['upload'] = $aSql;

            $result['success'] = true;
        } elseif ($_REQUEST['section_module'] == 'video' && $_REQUEST['section_controller'] == 'video.add') {
            $result = $this->frameProcess($result);
        }

        return $result;
    }

    public function frameProcess($result)
    {
        if (!Phpfox::isUser()) {
            return array("error" => 'We only allow logged in users the ability to upload videos');
        }

        if (!Phpfox::getParam('video.allow_video_uploading')) {
            return array("error" => 'We do not allow video uploading for the moment');
        }

        if (!Phpfox::getUserParam('video.can_upload_videos')) {
            return array("error" => 'You don\'t have the permission to upload videos');
        }

        if (isset($_FILES['Filedata']) && !isset($_FILES['video'])) {
            $_FILES['video'] = $_FILES['Filedata'];
        }

        $bIsInline = false;
        $aVals = Phpfox::getLib('request')->get('val');

        if (trim($aVals['video_title']) == '') {
            if (isset($_REQUEST['newName']) && $_REQUEST['newName'] != '') {
                $aVals['video_title'] = $_REQUEST['newName'];
            } else {
                $aVals['video_title'] = pathinfo($_REQUEST['qqfilename'], PATHINFO_FILENAME);
            }
        }

        $_FILES['video']['name'] = $_REQUEST['qqfilename'];

        $aVals['title'] = $aVals['video_title'];

        if (isset($aVals['video_inline'])) {
            $bIsInline = true;
        }

        if (!isset($_FILES['video'])) {
            $result['error'] = Phpfox::getPhrase('video.upload_failed_file_is_too_large');
            if (!$bIsInline) {
                $result['callback'] .= 'if (window.parent.$Core.exists(\'#js_video_upload_error\')) {';
                $result['callback'] .= 'window.parent.document.getElementById(\'js_video_upload_error\').style.display = \'block\';';
                $result['callback'] .= 'window.parent.document.getElementById(\'js_video_upload_message\').innerHTML = \'' . Phpfox::getPhrase('video.upload_failed_file_is_too_large') . '\';';
                $result['callback'] .= 'window.parent.document.getElementById(\'js_upload_inner_form\').style.display = \'block\';';
                $result['callback'] .= 'window.parent.document.getElementById(\'js_video_detail\').style.display = \'none\';';
                $result['callback'] .= 'window.parent.document.getElementById(\'js_video_process\').style.display = \'none\';';
                $result['callback'] .= '} else {';
                $result['callback'] .= 'window.parent.$Core.resetActivityFeedError(\'' . Phpfox::getPhrase('video.upload_failed_file_is_too_large') . '\');';
                $result['callback'] .= '}';
            } else {
                $result['callback'] .= 'window.parent.$Core.resetActivityFeedError(\'' . Phpfox::getPhrase('video.upload_failed_file_is_too_large') . '\');';
            }

            return $result;
        }

        if (($iFlood = Phpfox::getUserParam('video.flood_control_videos')) !== 0) {
            $aFlood = array(
                'action' => 'last_post', // The SPAM action
                'params' => array(
                    'field' => 'time_stamp', // The time stamp field
                    'table' => Phpfox::getT('video'), // Database table we plan to check
                    'condition' => 'view_id = 0 AND user_id = ' . Phpfox::getUserId(), // Database WHERE query
                    'time_stamp' => $iFlood * 60 // Seconds);
                )
            );

            // actually check if flooding
            if (Phpfox::getLib('spam')->check($aFlood)) {
                $result['error'] = Phpfox::getPhrase('video.you_are_uploading_a_video_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime();
            }
        }

        if (!Phpfox_Error::isPassed()) {
            if (!empty($_FILES['video']['tmp_name'])) {
                Phpfox::getService('video.process')->delete();
            }
            $result['error'] = implode('<br />', Phpfox_Error::get());
            if (!$bIsInline) {
                $result['callback'] .= 'window.parent.document.getElementById(\'js_video_upload_error\').style.display = \'block\';';
                $result['callback'] .= 'window.parent.document.getElementById(\'js_video_upload_message\').innerHTML = \'' . implode('<br />', Phpfox_Error::get()) . '\';';
                $result['callback'] .= 'window.parent.document.getElementById(\'js_upload_inner_form\').style.display = \'block\';';
                $result['callback'] .= 'window.parent.document.getElementById(\'js_video_detail\').style.display = \'none\';';
                $result['callback'] .= 'window.parent.document.getElementById(\'js_video_process\').style.display = \'none\';';
            } else {
                $result['callback'] .= 'window.parent.$Core.resetActivityFeedError(\'' . implode('<br />', Phpfox_Error::get()) . '\');';
            }

            return $result;
        }

        $iId = (int) Phpfox::getService('video.process')->add($aVals);

        if ($iId > 0) {
            if (Phpfox::getParam('video.vidly_support')) {
                $aVideo = Phpfox::getService('video')->getVideo($iId, true);

                Phpfox::getLib('cdn')->put(Phpfox::getParam('video.dir') . sprintf($aVideo['destination'], ''));

                Phpfox::getLib('database')->insert(Phpfox::getT('vidly_url'), array(
                    'video_id' => $aVideo['video_id'],
                    'video_url' => rtrim(Phpfox::getParam('core.rackspace_url'), '/') . '/file/video/' . sprintf($aVideo['destination'], ''),
                    'upload_video_id' => '0'
                        )
                );

                $mReturn = Phpfox::getService('video')->vidlyPost('AddMedia', array(
                    'Source' => array(
                        'SourceFile' => rtrim(Phpfox::getParam('core.rackspace_url'), '/') . '/file/video/' . sprintf($aVideo['destination'], ''),
                        'CDN' => 'RS'
                    )
                        ), 'vidid_' . $aVideo['video_id'] . '/'
                );

                $result['callback'] .= 'window.parent.location.href = \'' . Phpfox::permalink('video', $iId, $aVideo['title']) . '\';';
            } else {
                if (!$bIsInline) {
                    $sAlert = Phpfox::getLib('ajax')->alert(Phpfox::getLib('image.helper')->display(array(
                                'theme' => 'ajax/add.gif', 'class' => 'v_middle')) . ' ' . Phpfox::getPhrase('video.your_video_has_successfully_been_uploaded_please_standby_while_we_convert_your_video'), Phpfox::getPhrase('video.converting_video'), 600, 150, false, true);

                    $result['callback'] .= str_replace('tb_show', 'window.parent.tb_show', str_replace('$.ajaxBox', 'window.parent.$.ajaxBox', $sAlert));
                }
                $result['callback'] .= 'window.parent.$.ajaxCall(\'video.convert\', \'attachment_id=' . $iId . '&twitter_connection=' . (isset($aVals['connection']['twitter']) ? $aVals['connection']['twitter'] : '0') . '&facebook_connection=' . ((isset($aVals['connection']) && isset($aVals['connection']['facebook'])) ? $aVals['connection']['facebook'] : '0') . '&' . ($bIsInline ? 'inline=true' : 'full=true') . '&custom_pages_post_as_page=' . Phpfox::getLib('request')->get('custom_pages_post_as_page') . '\', \'GET\');';
            }
        } else {
            if (!empty($_FILES['video']['tmp_name'])) {
                Phpfox::getService('video.process')->delete(Phpfox::getLib('request')->get('video_id'));
            }
            $result['error'] = implode('<br />', Phpfox_Error::get());
            if (!$bIsInline) {
                $result['callback'] .= 'window.parent.document.getElementById(\'js_video_upload_error\').style.display = \'block\';';
                $result['callback'] .= 'window.parent.document.getElementById(\'js_video_upload_message\').innerHTML = \'' . implode('<br />', Phpfox_Error::get()) . '\';';
                $result['callback'] .= 'window.parent.document.getElementById(\'js_upload_inner_form\').style.display = \'block\';';
                $result['callback'] .= 'window.parent.document.getElementById(\'js_video_detail\').style.display = \'none\';';
                $result['callback'] .= 'window.parent.document.getElementById(\'js_video_process\').style.display = \'none\';';
            } else {
                // echo 'window.parent.$(\'.activity_feed_form_share_process\').hide(); window.parent.$(\'.activity_feed_form_button .button\').removeClass(\'button_not_active\'); window.parent.$bButtonSubmitActive = true;';
                $result['callback'] .= 'window.parent.$Core.resetActivityFeedError(\'' . implode('<br />', Phpfox_Error::get()) . '\');';
            }

            return $result;
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
        if ($sPlugin = Phpfox_Plugin::get('rhetinauploader.service_video__call')) {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

}
