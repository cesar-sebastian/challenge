<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210516205158 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP CONSTRAINT fk_d34a04add614c7e7');
        $this->addSql('DROP INDEX uniq_d34a04add614c7e7');
        $this->addSql('ALTER TABLE product ADD price TEXT NOT NULL');
        $this->addSql('ALTER TABLE product DROP price_id');
        $this->addSql('COMMENT ON COLUMN product.price IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE product ADD price_id INT NOT NULL');
        $this->addSql('ALTER TABLE product DROP price');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT fk_d34a04add614c7e7 FOREIGN KEY (price_id) REFERENCES price (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_d34a04add614c7e7 ON product (price_id)');
    }
}
