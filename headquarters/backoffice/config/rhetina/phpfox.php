<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;


if (true === defined ('PHPFOX')) {
  $phpfoxInfo = array(
    'site_url' => \PHPFOX::getParam('core.path'),
    'host' =>  \PHPFOX::getParam('core.host'),
    'folder' =>  \PHPFOX::getParam('core.folder'),
    'module_url' => \Phpfox::getParam('core.url_module'),
    'core.dir_cache' => \Phpfox::getParam('core.dir_cache'),
    'core.url_file' => \Phpfox::getParam('core.url_file'),
    'core.dir_file' => \Phpfox::getParam('core.dir_file'),
    'core.dir_static' => \Phpfox::getParam('core.dir_static'),
    'core.url_static' => \Phpfox::getParam('core.url_static')
  );
} else {
  $phpfoxInfo = array(
    'site_url' => '/',
    'host' =>  '/',
    'folder' =>  '/',
    'module_url' => '/',
    'core.dir_cache' => '/../../../../../../file/cache/',
    'core.url_file' => '/',
    'core.dir_file' => '/../../../../../../file/',
  );
}
/*
 * Sometimes the keys and salts contains more than one "%"
 * Symfony will interpret this as being a reference to another parameter
 * To avoid that, we need to escape those values
 * We use the escapeValue() method offered by Symfony\Component\DependencyInjection\ParameterBag\ParameterBag
 * It is easy to escape such strings, but let's use something that is there already
 */
$parameterBag = new ParameterBag();

foreach ( $phpfoxInfo as $root => $values ) {
  if ( is_array( $values ) ) {
    foreach ( $values as $name => $value ) {
      $value = $parameterBag->escapeValue( $value );
      $container->setParameter( "phpfox.$root.$name", $value );
      unset( $phpfoxInfo[$root][$name] );
    }
  } else {
    $value = $parameterBag->escapeValue( $values );
    $container->setParameter( "phpfox.$root", $value );
    unset( $phpfoxInfo[$root] );
  }
}
unset( $phpfoxInfo );
unset( $parameterBag );