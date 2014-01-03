<?php

namespace Veles\HomeWorkBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Yaml\Yaml;
use Veles\HomeWorkBundle\Entity\Category;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $categories_data = Yaml::parse($this->getYmlFile());

        foreach ($categories_data['categories'] as $key => $value) {
            $categories = new Category();

            $categories
                ->setName($key)
                ->setTitle($value['title'])
                ->setDescription($value['description'])
            ;

            $manager->persist($categories);
            $this->addReference($key, $categories);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }

    protected function getYmlFile()
    {
        return __DIR__ . '/appData/categories.yml';
    }
}
?>