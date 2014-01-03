<?php

namespace Veles\HomeWorkBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;
use Veles\HomeWorkBundle\Entity\Gbook;

class LoadGbookData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $coments = Yaml::parse($this->getYmlFile());

        foreach ($coments['comments'] as $key => $value) {
            $gbook = new Gbook();

            $gbook
                ->setName($value['name'])
                ->setEmail($value['email'])
                ->setMessage($value['message'])
            ;

            $manager->persist($gbook);
            $this->addReference($key, $gbook);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }

    protected function getYmlFile()
    {
        return __DIR__ . '/appData/comments.yml';
    }
}
?>