<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Class AppKernel
 */
class AppKernel extends Kernel
{
  /**
   * {@inheritdoc}
   */
  public function init()
  {
    // Please read http://symfony.com/doc/2.0/book/installation.html#configuration-and-setup
    bcscale( 3 );

    parent::init();
  }

  /**
   * @return array
   */
  public function registerBundles()
  {
    $bundles = array(
      new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
      new Symfony\Bundle\SecurityBundle\SecurityBundle(),
      new Symfony\Bundle\TwigBundle\TwigBundle(),
      new Symfony\Bundle\MonologBundle\MonologBundle(),
      new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
      new Symfony\Bundle\AsseticBundle\AsseticBundle(),
      new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
      new JMS\AopBundle\JMSAopBundle(),
      new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
      // Doctrine
      new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
      new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
      // KNP HELPER BUNDLES
      new Knp\Bundle\MenuBundle\KnpMenuBundle(),
      new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
      // new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),

      // USER
      new FOS\UserBundle\FOSUserBundle(),
      new Sonata\UserBundle\SonataUserBundle( 'FOSUserBundle' ),
      new Rhetina\Bundle\Sonata\UserBundle\RhetinaSonataUserBundle(),
      new JMS\SerializerBundle\JMSSerializerBundle(),
      // API
      new FOS\RestBundle\FOSRestBundle(),
      new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
      // Rhetina Main Bundles
      new Rhetina\Bundle\PhpfoxTamerBundle\PhpfoxTamerBundle(),
      new Rhetina\Bundle\CoreBundle\RhetinaCoreBundle(),
      new Rhetina\Bundle\ApiBundle\RhetinaApiBundle(),
      new Sonata\MarkItUpBundle\SonataMarkItUpBundle(),
      new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
      // SONATA CORE & HELPER BUNDLES
      new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
      new Sonata\jQueryBundle\SonatajQueryBundle(),
      new Sonata\CoreBundle\SonataCoreBundle(),
      new Sonata\IntlBundle\SonataIntlBundle(),
      new Sonata\FormatterBundle\SonataFormatterBundle(),
      new Sonata\CacheBundle\SonataCacheBundle(),
      new Sonata\BlockBundle\SonataBlockBundle(),
      new Sonata\ClassificationBundle\SonataClassificationBundle(),
      new Rhetina\Bundle\Sonata\ClassificationBundle\RhetinaSonataClassificationBundle(),
      new Sonata\NotificationBundle\SonataNotificationBundle(),
      new Rhetina\Bundle\Sonata\NotificationBundle\RhetinaSonataNotificationBundle(),
      // Search Integration
      //new FOS\ElasticaBundle\FOSElasticaBundle(),

      // CMF Integration
      //new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),


      // MEDIA
      new Sonata\MediaBundle\SonataMediaBundle(),
      new Rhetina\Bundle\Sonata\MediaBundle\RhetinaSonataMediaBundle(),
      // new Liip\ImagineBundle\LiipImagineBundle(),

      new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle()
    );

    $bundles = Rhetina\Bundle\PhpfoxTamerBundle\PhpfoxTamerBundle::registerModuleBundles( $bundles );

    if (in_array( $this->getEnvironment(), array( 'dev', 'test' ) )) {
      $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
      $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
      $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
    }

    return $bundles;
  }

  /**
   * @param LoaderInterface $loader
   */
  public function registerContainerConfiguration( LoaderInterface $loader )
  {
    $loader->load( __DIR__ . '/config/config_' . $this->getEnvironment() . '.yml' );
  }

  /**
   * @return string
   */
  public function getCacheDir()
  {
    /*
     * Use use cache/theme/ folder because this one is cleared when the cache is flushed from admincp
     * The other folders inside the cache folder remains intact
     */
    if (false === defined('PHPFOX')) {
      return __DIR__ . '/../../../../file/rhetina/cache/' . $this->environment;
    } else {
      return \Phpfox::getParam('core.dir_file') . 'rhetina/cache/' . $this->environment;
    }
  }

  /**
   * @return string
   */
  public function getLogDir()
  {
    if (false === defined('PHPFOX')) {
      return __DIR__ . '/../../../../file/rhetina/logs/' . $this->environment;
    } else {
    return \Phpfox::getParam('core.dir_file'). 'rhetina/logs/' . $this->environment;
    }
  }

}
