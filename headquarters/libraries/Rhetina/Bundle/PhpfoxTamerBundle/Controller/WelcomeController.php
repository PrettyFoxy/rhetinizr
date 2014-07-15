<?php

namespace Rhetina\Bundle\PhpfoxTamerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WelcomeController extends Controller
{
    public function indexAction()
    {
        /*
         * The action's view can be rendered using render() method
         * or @Template annotation as demonstrated in DemoController.
         *
         */

        return $this->render('PhpfoxTamerBundle:Welcome:index.html.twig');
    }
}
