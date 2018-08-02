<?php

// src/GB/UserBundle/DataFixtures/ORM/LoadUser.php

namespace GB\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use GB\UserBundle\Entity\User;
use Symfony\Component\Yaml\Yaml;

class LoadUser implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $users = Yaml::parse(file_get_contents(__DIR__ . '/UserData.yml'));

        foreach ($users as $user) {
            $userToPersist = new User();
            $userToPersist->setUserName($user['userName']);
            $userToPersist->setEmail($user['email']);
            $userToPersist->setPassword($user['password']);
            $userToPersist->setProfilPictureUrl($user['profilPictureUrl']);
            $userToPersist->setValidationToken($user['validationToken']);

            $manager->persist($userToPersist);
        }

        $manager->flush();
    }

}
