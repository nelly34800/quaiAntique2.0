<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Restaurant;
use Doctrine\Persistence\ObjectManager;

class RestaurantFixtures extends Fixture
{

    public const QUAI = 'restaurant_quai_antique';

    public function load(ObjectManager $manager): void
    {
        $quaiAntique = new Restaurant();
        $quaiAntique->setName("Quai Antique");
        $quaiAntique->setDescription("Restaurant du chef Arnaud Michant");
        $quaiAntique->setOpeningDay("mardi");
        $quaiAntique->setClosingDay("dimanche");
        $quaiAntique->setAmOpeningTime("11h00");
        $quaiAntique->setAmClosingTime("14h00");
        $quaiAntique->setPmOpeningTime("18h00");
        $quaiAntique->setPmClosingTime("23h00");
        $quaiAntique->setMaxGuest(60);
        $quaiAntique->setCreatedAt(new \DateTimeImmutable());
        $this->addReference(self::QUAI, $quaiAntique);

        $manager->persist($quaiAntique);

        $manager->flush();
    }
}
