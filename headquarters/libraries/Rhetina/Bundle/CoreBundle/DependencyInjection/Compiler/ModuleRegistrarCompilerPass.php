<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Rhetina\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ModuleRegistrarCompilerPass implements CompilerPassInterface
{

  /**
   * You can modify the container here before it is dumped to PHP code.
   *
   * @param ContainerBuilder $container
   *
   * @api
   */
  public function process( ContainerBuilder $container )
  {
    if ( false === $container->hasDefinition( 'rhetina.module.registrar' ) ) {
      return;
    }

    $definition = $container->getDefinition( 'rhetina.module.registrar' );

    $taggedServices = $container->findTaggedServiceIds(
      'rhetina.module'
    );
    foreach ( $taggedServices as $id => $attributes ) {
      $definition->addMethodCall(
        'addModule',
        array( new Reference( $id ) )
      );
    }
  }

}