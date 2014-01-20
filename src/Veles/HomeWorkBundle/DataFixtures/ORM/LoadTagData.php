<?php

namespace Veles\HomeWorkBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;
use Veles\HomeWorkBundle\Entity\Tag;

class LoadTagData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $tags_data = Yaml::parse($this->getYmlFile());

        foreach ($tags_data['tags'] as $key => $value) {
            $tag = new Tag();

            $tag
                ->setTitle($value['title'])
            ;

            $manager->persist($tag);
            $this->addReference($key, $tag);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4;
    }

    protected function getYmlFile()
    {
        return __DIR__ . '/appData/tags.yml';
    }
}
?>