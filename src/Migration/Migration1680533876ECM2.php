<?php

declare(strict_types=1);

namespace HMnetProductExtraCosts\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1680533876ECM2 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1680533876;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `hmnet_extracosts_extension` (
    `id` BINARY(16) NOT NULL,
    `product_id` BINARY(16) NULL,
    `json` TEXT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS `hmnet_extracosts_extension`;
CREATE TABLE IF NOT EXISTS `hmnet_extracosts_extension` (
    `id` BINARY(16) NOT NULL,
    `product_id` BINARY(16) NULL,
    `json` TEXT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);
    }
}
