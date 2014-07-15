<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Rhetina\Bundle\PhpfoxTamerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * PhpfoxTamerBundle Class
 *
 * @author Alexandru Furculita <alex@rhetina.com>
 */
class PhpfoxTamerBundle extends Bundle
{
  public function boot()
  {

  }

  public function build( ContainerBuilder $container )
  {
    parent::build( $container );
  }

  public static function registerModuleBundles( array $bundles = array() )
  {
    if (false === defined( 'PHPFOX' )) {
      static::loadBundles();
    } else {
      ( ( $sPlugin = \Phpfox_Plugin::get( 'rhetinizr.registerbundle' ) ) ? eval( $sPlugin ) : false );
    }

    return $bundles;

  }

  public static function loadBundles()
  {
    $finder = new \Symfony\Component\Finder\Finder();

    $finder->files()
      ->name( 'rhetinizr.registerbundle.php' )
      ->ignoreUnreadableDirs()
      ->depth( '== 2' )
      ->in( __DIR__ . '/../../../../../../' );

    foreach ($finder as $file) {

      include $file->getRealpath();
    }

  }

  public static function loadPhpfox()
  {
    // what if we are not in Phpfox mode
    if (false === defined( 'PHPFOX' )) {

      echo 'Searching';
      define( 'PHPFOX', true );
      define( 'PHPFOX_DS', DIRECTORY_SEPARATOR );
      define( 'PHPFOX_START_TIME', array_sum( explode( ' ', microtime() ) ) );

      $finder = new \Symfony\Component\Finder\Finder();

      $finder->files()
        ->name( 'init.inc.php' )
        ->ignoreUnreadableDirs()
        ->depth( '== 2' )
        ->in( __DIR__ . '/../../../../../../../../' );

      foreach ($finder as $file) {

        define( 'PHPFOX_DIR', dirname( dirname( $file->getRealpath() ) ) . '/' );
        //require_once( $file->getRealpath() );

        defined( 'PHPFOX' ) or exit( 'NO DICE!' );
        if (!isset( $_SERVER['HTTP_USER_AGENT'] )) {
          $_SERVER['HTTP_USER_AGENT'] = '';
        }


// Require the needed setting and class files
        if (file_exists( PHPFOX_DIR . 'include' . PHPFOX_DS . 'setting' . PHPFOX_DS . 'dev.sett.php' ) && !defined(
            'PHPFOX_DEBUG'
          )
        ) {
          require_once( PHPFOX_DIR . 'include' . PHPFOX_DS . 'setting' . PHPFOX_DS . 'dev.sett.php' );
        } elseif (file_exists( PHPFOX_DIR . 'file' . PHPFOX_DS . 'log' . PHPFOX_DS . 'debug.php' )) {
          require_once( PHPFOX_DIR . 'file' . PHPFOX_DS . 'log' . PHPFOX_DS . 'debug.php' );
        }

        require_once( PHPFOX_DIR . 'include' . PHPFOX_DS . 'setting' . PHPFOX_DS . 'constant.sett.php' );
        if (php_sapi_name() == 'litespeed') {
          ini_set( 'session.save_handler', 'files' );
          ini_set( 'session.save_path', PHPFOX_DIR_FILE . 'session' . PHPFOX_DS );
        }


        require( PHPFOX_DIR_LIB_CORE . 'phpfox' . PHPFOX_DS . 'phpfox.class.php' );
        require( PHPFOX_DIR_LIB_CORE . 'error' . PHPFOX_DS . 'error.class.php' );
        require( PHPFOX_DIR_LIB_CORE . 'module' . PHPFOX_DS . 'service.class.php' );
        require( PHPFOX_DIR_LIB_CORE . 'module' . PHPFOX_DS . 'component.class.php' );

// No need to load the debug class if the debug is disabled
        if (PHPFOX_DEBUG) {
          require_once( PHPFOX_DIR_LIB_CORE . 'debug' . PHPFOX_DS . 'debug.class.php' );
        } // http://www.phpfox.com/tracker/view/14329/
        else {
          foreach ($_COOKIE AS $sKey => $sValue) {
            if (preg_match( '/js_console/i', $sKey )) {
              setcookie(
                $sKey,
                "0",
                time() - ( 3600 * 24 ),
                Phpfox::getParam( 'core.cookie_path' ),
                Phpfox::getParam( 'core.cookie_domain' )
              );
            }
          }
        }

        set_error_handler( array( 'Phpfox_Error', 'errorHandler' ) );

        // Default time to GMT
        if (function_exists( 'date_default_timezone_set' )) {
          date_default_timezone_set( 'GMT' );

          define( 'PHPFOX_TIME', time() );
        } else {
          define( 'PHPFOX_TIME', strtotime( gmdate( "M d Y H:i:s", time() ) ) );
        }

        \Phpfox::getLib( 'setting' )->set();

        require( PHPFOX_DIR_LIB_CORE . 'plugin' . PHPFOX_DS . 'plugin.class.php' );

        \Phpfox_Plugin::set();

        ( ( $sPlugin = \Phpfox_Plugin::get( 'init' ) ) ? eval( $sPlugin ) : false );

      }
    }
  }

}
