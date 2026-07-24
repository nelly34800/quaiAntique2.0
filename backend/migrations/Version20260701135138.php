<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260701135138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, guest_number SMALLINT NOT NULL, order_date DATE NOT NULL, order_hour DATETIME NOT NULL, allergy VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, restaurant_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E00CEDDEB1E7706E (restaurant_id), INDEX IDX_E00CEDDEA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE food (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, price SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE food_category (food_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_2E013E83BA8E87C4 (food_id), INDEX IDX_2E013E8312469DE2 (category_id), PRIMARY KEY (food_id, category_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, image VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu_food (menu_id INT NOT NULL, food_id INT NOT NULL, INDEX IDX_1C77D9B9CCD7E912 (menu_id), INDEX IDX_1C77D9B9BA8E87C4 (food_id), PRIMARY KEY (menu_id, food_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, restaurant_id INT NOT NULL, INDEX IDX_16DB4F89B1E7706E (restaurant_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE restaurant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(32) NOT NULL, description LONGTEXT NOT NULL, opening_day VARCHAR(32) NOT NULL, closing_day VARCHAR(32) NOT NULL, am_opening_time VARCHAR(32) NOT NULL, pm_opening_time VARCHAR(32) NOT NULL, am_closing_time VARCHAR(32) NOT NULL, pm_closing_time VARCHAR(32) NOT NULL, max_guest SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(64) NOT NULL, last_name VARCHAR(64) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, allergy VARCHAR(255) DEFAULT NULL, guest_number SMALLINT DEFAULT NULL, api_token VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE food_category ADD CONSTRAINT FK_2E013E83BA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE food_category ADD CONSTRAINT FK_2E013E8312469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_food ADD CONSTRAINT FK_1C77D9B9CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_food ADD CONSTRAINT FK_1C77D9B9BA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEB1E7706E');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEA76ED395');
        $this->addSql('ALTER TABLE food_category DROP FOREIGN KEY FK_2E013E83BA8E87C4');
        $this->addSql('ALTER TABLE food_category DROP FOREIGN KEY FK_2E013E8312469DE2');
        $this->addSql('ALTER TABLE menu_food DROP FOREIGN KEY FK_1C77D9B9CCD7E912');
        $this->addSql('ALTER TABLE menu_food DROP FOREIGN KEY FK_1C77D9B9BA8E87C4');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89B1E7706E');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE food');
        $this->addSql('DROP TABLE food_category');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE menu_food');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP TABLE restaurant');
        $this->addSql('DROP TABLE user');
    }
}
