<?php

declare(strict_types=1);

/*
 * This file is part of the CssStyleSelector Bundle.
 *
 * (c) Daniel Kiesel <https://github.com/iCodr8>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Craffft\CssStyleSelectorBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

/**
 * Moves style groups without a category (e.g. from before the categories or
 * inserted by a theme installer) into the category "General". The groups keep
 * their order.
 */
class StyleCategoryMigration extends AbstractMigration
{
    public const CATEGORY_TITLE = 'General';

    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_css_style_group', 'tl_css_style_category'])) {
            return false;
        }

        $columns = array_change_key_case($schemaManager->listTableColumns('tl_css_style_group'));

        if (!isset($columns['pid'], $columns['sorting'])) {
            return false;
        }

        return (bool) $this->connection->fetchOne('SELECT COUNT(*) FROM tl_css_style_group WHERE pid = 0');
    }

    public function run(): MigrationResult
    {
        $categoryId = $this->connection->fetchOne('SELECT id FROM tl_css_style_category WHERE title = ? ORDER BY id LIMIT 1', [self::CATEGORY_TITLE]);

        if (false === $categoryId) {
            $sorting = (int) $this->connection->fetchOne('SELECT MAX(sorting) FROM tl_css_style_category');

            $this->connection->insert('tl_css_style_category', [
                'tstamp' => time(),
                'sorting' => $sorting + 128,
                'title' => self::CATEGORY_TITLE,
            ]);

            $categoryId = $this->connection->lastInsertId();
        }

        $count = $this->connection->executeStatement('UPDATE tl_css_style_group SET pid = ? WHERE pid = 0', [$categoryId]);

        return $this->createResult(true, \sprintf('Moved %d CSS style groups into the category "%s".', $count, self::CATEGORY_TITLE));
    }
}
