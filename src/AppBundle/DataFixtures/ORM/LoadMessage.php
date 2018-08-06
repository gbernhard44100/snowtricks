<?php

// src/GB/TricksBundle/DataFixtures/ORM/LoadMessage.php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Message;
use Symfony\Component\Yaml\Yaml;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use AppBundle\DataFixtures\ORM\LoadTrick;
use AppBundle\DataFixtures\ORM\LoadUser;

class LoadMessage implements FixtureInterface, DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $messages = Yaml::parse(file_get_contents(__DIR__ . '/MessageData.yml'));
        $trickRepository = $manager->getRepository('AppBundle:Trick');
        $userRepository = $manager->getRepository('AppBundle:User');

        foreach ($messages as $message) {
            $messageToPersist = new Message();
            $messageToPersist->setContent($message['content']);

            $user = $userRepository->find($message['user_id']);
            $messageToPersist->setUser($user);

            $trick = $trickRepository->find($message['trick_id']);
            $messageToPersist->setTrick($trick);

            $manager->persist($messageToPersist);

            $messageToPersist->setDate(new \DateTime(date("Y-m-d H:i:s", $message['date'])));
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadUser::class,
            LoadTrick::class,
        );
    }

}
