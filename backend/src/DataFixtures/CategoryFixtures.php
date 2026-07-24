<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const STARTER = 'category_starter';
    public const DISH = 'category_dish';
    public const DESSERT = 'category_dessert';
    public const SOFT = 'category_soft';
    public const WINE = 'category_wine';

    public function load(ObjectManager $manager): void
    {
        $starter = new Category();
        $starter->setTitle("Entrée");
        $starter->setCreatedAt(new \DateTimeImmutable());
        $this->addReference(self::STARTER, $starter);

        $manager->persist($starter);

        $dish = new Category();
        $dish->setTitle("Plat principal");
        $dish->setCreatedAt(new \DateTimeImmutable());
        $this->addReference(self::DISH, $dish);

        $manager->persist($dish);

        $dessert = new Category();
        $dessert->setTitle("Dessert");
        $dessert->setCreatedAt(new \DateTimeImmutable());
        $this->addReference(self::DESSERT, $dessert);
        $manager->persist($dessert);

        $soft = new Category();
        $soft->setTitle("Boisson sans alcool");
        $soft->setCreatedAt(new \DateTimeImmutable());
        $this->addReference(self::SOFT, $soft);
        $manager->persist($soft);

        $wine = new Category();
        $wine->setTitle("Vin");
        $wine->setCreatedAt(new \DateTimeImmutable());
        $this->addReference(self::WINE, $wine);
        $manager->persist($wine);

        $manager->flush();
    }
}