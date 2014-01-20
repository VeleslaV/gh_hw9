<?php

namespace Veles\HomeWorkBundle\Twig;

class TagSizeExtension extends \Twig_Extension
{
    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function getName()
    {
        return 'tag_size';
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('tagSize', array($this, 'tagSize')),
        );
    }

    public function tagSize($tag, $tId, $link)
    {
        $maxFontSize = 30;
        $minFontSize = 1;

        $qb = $this->em->createQueryBuilder()
            ->select('count(t.id)')
            ->from('VelesHomeWorkBundle:Tag','t')
            ->join('t.articles', 'b')
            ->where('t.id = :tid')->setParameter('tid', $tId)
            ->groupBy('t.id');

        $tagCount = $qb->getQuery()->getOneOrNullResult();
        $resultCount = $tagCount != null?$tagCount[1]:0;

        $tagWeight = $minFontSize + $resultCount * (($maxFontSize - $minFontSize) / 10);
        $string = "<a href=".$link." style='font-size: ".$tagWeight."pt;'>$tag</a> ";

        return $string;
    }
}