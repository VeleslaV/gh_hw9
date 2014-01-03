<?php

namespace Veles\HomeWorkBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;
use Veles\HomeWorkBundle\Entity\Page;

class LoadPageData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $pages_data = Yaml::parse($this->getYmlFile());

        foreach ($pages_data['pages'] as $key => $value) {
            $pages = new Page();

            $pages
                ->setName($key)
                ->setTitle($value['title'])
                ->setDescription($value['description'])
            ;

            $manager->persist($pages);
            $this->addReference($key, $pages);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }

    protected function getYmlFile()
    {
        return __DIR__ . '/appData/pages.yml';
    }
}
?>