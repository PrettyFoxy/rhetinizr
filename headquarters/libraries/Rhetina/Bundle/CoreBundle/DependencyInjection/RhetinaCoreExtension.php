<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Rhetina\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class RhetinaCoreExtension
 *
 * @package Rhetina\Bundle\CoreBundle\DependencyInjection
 */
class RhetinaCoreExtension extends Extension implements PrependExtensionInterface
{
  /**
   * Yaml config files to load
   *
   * @var array
   */
  protected $resources = array(
    'config' => 'config.yml',
    //  'facebook' => 'facebook.yml',
  );

  /**
   * Loads the services based on your application configuration.
   *
   * @param array            $configs
   * @param ContainerBuilder $container
   */
  public function load( array $configs, ContainerBuilder $container )
  {
    $configuration = new Configuration();
    $processor     = new Processor();
    $config        = $processor->processConfiguration( $configuration, $configs );

    $loader = $this->getFileLoader( $container );
    $loader->load( $this->resources['config'] );

//    foreach ($configs as $config) {
//      if (!empty($config['facebook'])) {
//        $loader->load($this->resources['facebook']);
//        break;
//      }
//    }
  }

  /**
   * Get File Loader
   *
   * @param ContainerBuilder $container
   */
  public function getFileLoader( $container )
  {
    return new YamlFileLoader( $container, new FileLocator( __DIR__ . '/../Resources/config' ) );
  }

  public function getAlias()
  {
    return 'rhetina_core';
  }

  /**
   * Allow an extension to prepend the extension configurations.
   *
   * @param ContainerBuilder $container
   */
  public function prepend( ContainerBuilder $container )
  {
//
//    // get all Bundles
//    $bundles = $container->getParameter( 'kernel.bundles' );
//    // determine if HearsayRequireJSBundle is registered
//    if ( isset( $bundles['HearsayRequireJSBundle'] ) ) {
//      $configs                            = $container->getExtensionConfig( 'hearsay_require_js' );
//      $configs['options'][]['packages'][] =
//        array(
//          'name'     => 'rhetina_uploadr',
//          'main'     => 'main',
//          'location' => '%phpfox.folder%module/rhetinauploadr/headquarters/frontoffice/javascripts'
//        );
//      $configs['options'][]['deps'][]     = 'rhetina_uploadr';
//      $container->prependExtensionConfig( 'hearsay_require_js', array( 'options' => $configs['options'] ) );
//    }
  }

}