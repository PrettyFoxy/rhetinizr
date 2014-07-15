<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */



// If you don't want to setup permissions the proper way,
//      just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

$loader = require_once __DIR__ . '/../backoffice/bootstrap.php.cache';


// what if we are not in Phpfox mode
if (false === defined( 'PHPFOX' )) {

  define( 'PHPFOX', true );
  define( 'PHPFOX_DS', DIRECTORY_SEPARATOR );
  define( 'PHPFOX_START_TIME', array_sum( explode( ' ', microtime() ) ) );

  $finder = new Symfony\Component\Finder\Finder();

  $finder->files()
    ->name( 'init.inc.php' )
    ->ignoreUnreadableDirs()
    ->depth( '== 1' )
    ->in( __DIR__ . '/../../../../' );

  foreach ($finder as $file) {
    define( 'PHPFOX_DIR', dirname(dirname($file->getRealpath())) . '/' );
    require_once( $file->getRealpath() );
  }
}
return $loader;