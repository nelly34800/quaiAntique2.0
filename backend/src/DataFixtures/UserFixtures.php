<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const SOPHIE = 'client_sophie';
    public const JULIEN = 'client_julien';
    public const EMMA = 'client_emma';

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $sophie = new User();
        $sophie->setLastName("Martin");
        $sophie->setFirstName("Sophie");
        $sophie->setEmail("sophie@mail.test");
        $sophie->setRoles(['ROLE_USER']);
        $sophie->setAllergy("Gluten, arachides");
        $sophie->setGuestNumber(4);
        $sophie->setPassword(
            $this->passwordHasher->hashPassword(
                $sophie, 'Test12345!'
            )
        );
        $sophie->setCreatedAt(new \DateTimeImmutable());
        $this->addReference(self::SOPHIE, $sophie);

        $manager->persist($sophie);

        $julien = new User();
        $julien ->setLastName("Dupont");
        $julien ->setFirstName("Julien");
        $julien ->setEmail("julien@mail.test");
        $julien ->setRoles(['ROLE_USER']);
        $julien ->setAllergy("Fruits de mer");
        $julien ->setGuestNumber(5);
        $julien ->setPassword(
            $this->passwordHasher->hashPassword(
                $julien , 'Test12345!'
            )
        );
        $julien ->setCreatedAt(new \DateTimeImmutable());
        $this->addReference(self::JULIEN, $julien);

        $manager->persist($julien);


        $emma = new User();
        $emma->setLastName("Bernard");
        $emma->setFirstName("Emma");
        $emma->setEmail("emma@mail.test");
        $emma->setRoles(['ROLE_USER']);
        $emma->setGuestNumber(3);
        $emma->setPassword(
            $this->passwordHasher->hashPassword(
                $emma, 'Test12345!'
            )
        );
        $emma->setCreatedAt(new \DateTimeImmutable());
        $this->addReference(self::EMMA, $emma);

        $manager->persist($emma);

        $manager->flush();
    }
}
