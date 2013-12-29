<?php

namespace Veles\HomeWorkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('VelesHomeWorkBundle:Default:index.html.twig', array('name' => $name));
    }
}
