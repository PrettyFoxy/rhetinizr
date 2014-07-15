<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Rhetina\Component\Application;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Application Class
 *
 * @author Alexandru Furculita <alex@rhetina.com>
 */
class Application extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
//            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
//            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
//            new Symfony\Bundle\TwigBundle\TwigBundle(),
//            new Symfony\Bundle\MonologBundle\MonologBundle(),
//            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
//            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
//            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
//            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
        );

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    public function getName()
    {
        return 'Rhetina';
    }
}
