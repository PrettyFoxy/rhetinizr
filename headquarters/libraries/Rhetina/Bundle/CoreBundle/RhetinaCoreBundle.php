<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Rhetina\Bundle\CoreBundle;

use Rhetina\Bundle\CoreBundle\DependencyInjection\Compiler\ModuleRegistrarCompilerPass;
use Rhetina\Bundle\CoreBundle\DependencyInjection\Compiler\RequirejsConfigBuilderOptionsCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class RhetinaCoreBundle
 *
 * @package Rhetina\Bundle\CoreBundle
 */
class RhetinaCoreBundle extends Bundle
{
  /**
   * {@inheritdoc}
   */
  public function build( ContainerBuilder $container )
  {
    parent::build( $container );
    $container->addCompilerPass( new ModuleRegistrarCompilerPass() );
    $container->addCompilerPass( new RequirejsConfigBuilderOptionsCompilerPass() );
  }
}
