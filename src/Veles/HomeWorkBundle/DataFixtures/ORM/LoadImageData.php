<?php

namespace Veles\HomeWorkBundle\DataFixtures\ORM;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;
use Veles\HomeWorkBundle\Entity\Image;
use Symfony\Component\Finder\Finder;
use Vlabs\MediaBundle\Tools\ExtensionGuesser;

class LoadImageData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $finder = new Finder;
        $fixturesPath = $this->getFixturesPath();
        $finder->files()->in($fixturesPath);

        $i = 0;
        foreach($finder as $file) {
            $referenceKey = $i."img";

            $imagine = new \Imagine\Imagick\Imagine();
            $img = new Image();
            $fixtureFile = $fixturesPath.$file->getFilename();
            
            $file_http = new File($fixtureFile);
            $mime = $file_http->getMimeType();

            $newImageName = sprintf('%s.%s', uniqid(), ExtensionGuesser::guess($mime));
            $newImageSrc = $this->getUploadDir().basename($newImageName);
            $imagine->open($fixtureFile)->save($newImageSrc);

            $img
                ->setContentType($mime)
                ->setCreatedAt(new \DateTime())
                ->setName($newImageName)
                ->setPath($this->getPath($newImageName))
                ->setSize($file->getSize())
            ;

            $manager->persist($img);
            $this->addReference($referenceKey, $img);
            $manager->flush();
            $i++;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 5;
    }

    protected function getYmlFile()
    {
        return __DIR__ . '/appData/images.yml';
    }

    protected function getUploadDir()
    {
        return __DIR__ .'/../../../../../web/media/images/';
    }

    protected function getPath($imageTitle)
    {
        return 'media/images/'.$imageTitle;
    }

    protected function getFixturesPath()
    {
        return __DIR__."/appData/images/";
    }
}
?>