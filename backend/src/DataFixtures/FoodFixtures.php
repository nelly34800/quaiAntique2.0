<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Food;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FoodFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
      {
          return [
              CategoryFixtures::class,
          ];
      }

    public function load(ObjectManager $manager): void
    {
        $cheese = new Food();
        $cheese->setTitle("Salade de chèvre chaud sur toast");
        $cheese->setDescription("Jeunes pousses, toasts croustillants au chèvre fondant, vinaigrette maison au miel.");
        $cheese->setPrice(12);
        $cheese->setCreatedAt(new \DateTimeImmutable());
        $starter = $this->getReference(CategoryFixtures::STARTER, Category::class);
        $cheese->addCategory($starter);

        $manager->persist($cheese);

        $foieGras = new Food();
        $foieGras->setTitle("Foie gras maison");
        $foieGras->setDescription("Foie gras de canard mi-cuit accompagné d’un chutney de figues maison.");
        $foieGras->setPrice(18);
        $foieGras->setCreatedAt(new \DateTimeImmutable());
        $starter = $this->getReference(CategoryFixtures::STARTER, Category::class);
        $foieGras->addCategory($starter);

        $manager->persist($foieGras);

        $salmon = new Food();
        $salmon->setTitle("Tartare de saumon aux agrumes");
        $salmon->setDescription("Tartare de saumon aux agrumes avec vinaigrette légère à l'huile d’olive,");
        $salmon->setPrice(12);
        $salmon->setCreatedAt(new \DateTimeImmutable());
        $starter = $this->getReference(CategoryFixtures::STARTER, Category::class);
        $salmon->addCategory($starter);

        $manager->persist($salmon);

        $beef = new Food();
        $beef->setTitle("Filet de bœuf sauce au poivre");
        $beef->setDescription("Filet de bœuf sauce au poivre accompagné de frites maison");
        $beef->setPrice(29);
        $beef->setCreatedAt(new \DateTimeImmutable());
        $dish = $this->getReference(CategoryFixtures::DISH, Category::class);
        $beef->addCategory($dish);

        $manager->persist($beef);

        $duck = new Food();
        $duck->setTitle("Magret de canard miel-romarin");
        $duck->setDescription("Magret de canard miel-romarin accompagné de pommes de terres grenailles");
        $duck->setPrice(25);
        $duck->setCreatedAt(new \DateTimeImmutable());
        $dish = $this->getReference(CategoryFixtures::DISH, Category::class);
        $duck->addCategory($dish);

        $manager->persist($duck);

        $mushroom = new Food();
        $mushroom->setTitle("Risotto aux champignons");
        $mushroom->setDescription("Risotto aux champignons avec riz rond de Camargue");
        $mushroom->setPrice(19);
        $mushroom->setCreatedAt(new \DateTimeImmutable());
        $dish = $this->getReference(CategoryFixtures::DISH, Category::class);
        $mushroom->addCategory($dish);

        $manager->persist($mushroom);

        $cream = new Food();
        $cream->setTitle("Crème brûlée à la vanille");
        $cream->setDescription("Crème brûlée à la vanille de Madagascar caramélisée à la minute.");
        $cream->setPrice(8);
        $cream->setCreatedAt(new \DateTimeImmutable());
        $dessert = $this->getReference(CategoryFixtures::DESSERT, Category::class);
        $cream->addCategory($dessert);

        $manager->persist($cream);

        $chocolate = new Food();
        $chocolate->setTitle("Fondant au chocolat");
        $chocolate->setDescription("Fondant au chocolat maison");
        $chocolate->setPrice(7);
        $chocolate->setCreatedAt(new \DateTimeImmutable());
        $dessert = $this->getReference(CategoryFixtures::DESSERT, Category::class);
        $chocolate->addCategory($dessert);

        $manager->persist($chocolate);

        $tart = new Food();
        $tart->setTitle("Tarte Tatin maison");
        $tart->setDescription("Tarte Tatin maison avec des pommes locales accompagné de sa boule de glace vanille");
        $tart->setPrice(9);
        $tart->setCreatedAt(new \DateTimeImmutable());
        $dessert = $this->getReference(CategoryFixtures::DESSERT, Category::class);
        $tart->addCategory($dessert);

        $manager->persist($tart);

        $white = new Food();
        $white->setTitle("Vin blanc");
        $white->setDescription("Vin blanc d'Alsace");
        $white->setPrice(7);
        $white->setCreatedAt(new \DateTimeImmutable());
        $wine = $this->getReference(CategoryFixtures::WINE, Category::class);
        $white->addCategory($wine);

        $manager->persist($white);

        $red = new Food();
        $red->setTitle("Vin rouge");
        $red->setDescription("Vin rouge d'un producteur de la région");
        $red->setPrice(8);
        $red->setCreatedAt(new \DateTimeImmutable());
        $wine = $this->getReference(CategoryFixtures::WINE, Category::class);
        $red->addCategory($wine);

        $manager->persist($red);

        $volvic = new Food();
        $volvic->setTitle("Eau plate");
        $volvic->setDescription("marque Volvic");
        $volvic->setPrice(3);
        $volvic->setCreatedAt(new \DateTimeImmutable());
        $soft = $this->getReference(CategoryFixtures::SOFT, Category::class);
        $volvic->addCategory($soft);

        $manager->persist($volvic);

        $pellegrino = new Food();
        $pellegrino->setTitle("Eau gazeuse");
        $pellegrino->setDescription("marque San Pellegrino");
        $pellegrino->setPrice(3);
        $pellegrino->setCreatedAt(new \DateTimeImmutable());
        $soft = $this->getReference(CategoryFixtures::SOFT, Category::class);
        $pellegrino->addCategory($soft);

        $manager->persist($pellegrino);

        $manager->flush();
    }
}