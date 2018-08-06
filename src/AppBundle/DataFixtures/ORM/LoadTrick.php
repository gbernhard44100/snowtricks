<?php

// src/GB/TricksBundle/DataFixtures/ORM/LoadTrick.php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Trick;
use AppBundle\Entity\Video;
use AppBundle\Entity\Picture;
use Symfony\Component\Yaml\Yaml;

class LoadTrick implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $tricks = Yaml::parse(file_get_contents(__DIR__ . '/TricksData.yml'));

        foreach ($tricks as $trick) {
            $trickToPersist = new Trick();
            $trickToPersist->setName($trick['name']);
            $trickToPersist->setDescription($trick['description']);
            $trickToPersist->setCategory($trick['category']);

            $pictureUrls = $trick['picture'];
            if (!empty($pictureUrls)) {
                foreach ($pictureUrls as $pictureUrl) {
                    $picture = new Picture();
                    $picture->setUrl($pictureUrl);
                    $manager->persist($picture);
                    $trickToPersist->addPicture($picture);
                }
            }

            $videoUrls = $trick['video'];
            if (!empty($videoUrls)) {
                foreach ($videoUrls as $videoUrl) {
                    $video = new Video();
                    $video->setUrl($videoUrl);
                    $manager->persist($video);
                    $trickToPersist->addVideo($video);
                }
            }

            $manager->persist($trickToPersist);
        }

        $manager->flush();
    }

}
