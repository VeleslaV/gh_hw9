<?php

namespace Veles\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('VelesUserBundle:Default:index.html.twig', array('name' => $name));
    }
}
