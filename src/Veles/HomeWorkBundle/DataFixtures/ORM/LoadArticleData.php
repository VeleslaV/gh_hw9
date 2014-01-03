<?php

namespace Veles\HomeWorkBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Yaml\Yaml;
use Veles\HomeWorkBundle\Entity\Article;

class LoadArticleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $articles = Yaml::parse($this->getYmlFile());

        foreach ($articles['articles'] as $key => $value) {
            $article = new Article();

            $article
                ->setCategory($this->getReference($value['category']))
                ->setTitle($value['title'])
                ->setImg($value['img'])
                ->setBody($value['body'])
                ->setCreated(new \DateTime())
                ->setTags($this->getReferencesFromArray($value['tags']))
                ->setViews($value['views'])
            ;

            $manager->persist($article);
            $this->addReference($key, $article);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 5;
    }

    protected function getReferencesFromArray(array $array)
    {
        $outputReferences = new ArrayCollection();

        foreach ($array as $reference) {
            $outputReferences->add($this->getReference($reference));
        }

        return $outputReferences;
    }

    protected function getYmlFile()
    {
        return __DIR__ . '/appData/articles.yml';
    }
}
?>