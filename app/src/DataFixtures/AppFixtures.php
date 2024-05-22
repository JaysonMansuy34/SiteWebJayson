<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail(sprintf('user%d@example.com', $i));
            $user->setNom(sprintf('Nom%d', $i));
            $user->setPrenom(sprintf('Prenom%d', $i));
            $user->setTel(sprintf('060000000%d', $i));
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $user->setValidate($i % 2 === 0);

            $manager->persist($user);
        }

        $manager->flush();
    }
}

