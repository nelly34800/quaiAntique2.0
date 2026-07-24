<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture
{
    public function __construct(
          private UserPasswordHasherInterface $passwordHasher
      ) {}

    public function load(ObjectManager $manager): void
    {
         $admin = new User();
          $admin->setLastName("Michant");
          $admin->setFirstName("Arnaud");
          $admin->setEmail("admin@mail.test");
          $admin->setRoles(['ROLE_ADMIN']);
          $admin->setPassword(
            $this->passwordHasher->hashPassword(
                $admin, 'Admin12345!'
            )
        );
        $admin->setCreatedAt(new \DateTimeImmutable());
        $this->addReference('admin', $admin);

        $manager->persist($admin);

        $manager->flush();
    }
}
