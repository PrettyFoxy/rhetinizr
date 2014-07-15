<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Rhetina\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class RequirejsConfigBuilderOptionsCompilerPass
 *
 * @package Rhetina\Bundle\CoreBundle\DependencyInjection\Compiler
 */
class RequirejsConfigBuilderOptionsCompilerPass implements CompilerPassInterface
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
        if (false === $container->hasDefinition( 'rhetina.module.registrar' )) {
            return;
        }
        if (false === $container->hasDefinition( 'hearsay_require_js.configuration_builder' )) {
            return;
        }

        $configurationBuilder = $container
            ->getDefinition( 'hearsay_require_js.configuration_builder' );

        /** @var $modules Bundle[] */
        $modules = $container->get( "rhetina.module.registrar" )->getModules();

        $options = array();

        foreach ($modules as $bundle) {
            if (false === method_exists( $bundle, 'getRequirejsConfiguration' )) {
                continue;
            }

            $configs = $bundle->getRequirejsConfiguration();


            if (is_array( $configs )) {
                foreach ($configs as $key => $value) {
                    $options[$key][] = $value;
                }
            }

        }
        foreach ($options as $option => $settingValue) {
            $configurationBuilder->addMethodCall( 'addOption', array( $option, $settingValue ) );
        }
    }

}