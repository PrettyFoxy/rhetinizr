<?php

namespace Rhetina\Bundle\PhpfoxTamerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

/**
 * Class PhpfoxTamerExtension
 *
 * @package Rhetina\Bundle\PhpfoxTamerBundle\DependencyInjection
 */
class PhpfoxTamerExtension extends Extension
{
	public function load(array $configs, ContainerBuilder $container)
	{
		$loader = new YamlFileLoader( $container, new FileLocator( __DIR__ . '/../Resources/config' ) );
		$loader->load( 'services.yml' );
	}

	public function getAlias()
	{
		return 'phpfox_tamer';
	}
}
