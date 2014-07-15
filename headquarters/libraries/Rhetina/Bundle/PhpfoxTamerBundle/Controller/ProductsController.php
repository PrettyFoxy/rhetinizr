<?php

namespace Rhetina\Bundle\PhpfoxTamerBundle\Controller;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\Annotations\Prefix,
  FOS\RestBundle\Controller\Annotations\NamePrefix,
  FOS\RestBundle\Controller\Annotations\RouteResource,
  FOS\RestBundle\Controller\Annotations\View,
  FOS\RestBundle\Controller\Annotations\QueryParam,
  FOS\RestBundle\Controller\FOSRestController;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Prefix("phpfox")
 * @NamePrefix("_phpfox_")
 * Following annotation is redundant, since FosRestController implements ClassResourceInterface
 * so the Controller name is used to define the resource. However with this annotation its
 * possible to set the resource to something else unrelated to the Controller name
 * @RouteResource("Products")
 */
class ProductsController extends FosRestController
{
  /**
   * Get the list of all activated products
   *
   * @return array data
   *
   * @View()
   * @ApiDoc()
   */
  public function cgetActivatedAction()
  {
    if (false === $this->container->has( 'rhetina.module.registrar' )) {
      return;
    }

    $products = $this->container->get( 'rhetina.module.registrar' )->getModules();

    $richProducts = array();

    foreach ($products as $key => $product) {

      $productFolderName = str_replace( 'bundle', '', strtolower( $product->getName() ) );

      if (true === \Phpfox::isModule( $productFolderName )) {
        $composerContent = array();

        if (true === file_exists( $json = PHPFOX_DIR_MODULE . $productFolderName . '/composer.json' )) {
          $composerContent = json_decode( file_get_contents( $json ), true );
        }

        $composerContent['id']   = $productFolderName;
        $composerContent['name'] = $productFolderName;

        $richProducts[] = $composerContent;
      }
      unset( $products[$key] );
    }

    return $richProducts;
  }
}
