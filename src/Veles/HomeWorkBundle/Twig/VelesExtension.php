<?php

namespace Veles\HomeWorkBundle\Twig;

class VelesExtension extends \Twig_Extension {

    public function getName()
    {
        return 'cut_string';
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('cutString', array($this, 'cutString')),
        );
    }

    public function cutString($number, $length = 150)
    {
        $price = substr($number, 0, $length)." ... ";

        return $price;
    }
}