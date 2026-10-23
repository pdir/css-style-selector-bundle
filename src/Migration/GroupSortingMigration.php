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
 * Numbers groups without a sorting value (e.g. from before the group sorting
 * or inserted by a theme installer) in alphabetical order, which was the order
 * of the groups before.
 */
class GroupSortingMigration extends AbstractMigration
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_css_style_group'])) {
            return false;
        }

        $columns = array_change_key_case($schemaManager->listTableColumns('tl_css_style_group'));

        if (!isset($columns['sorting'])) {
            return false;
        }

        return (bool) $this->connection->fetchOne('SELECT COUNT(*) FROM tl_css_style_group WHERE sorting = 0');
    }

    public function run(): MigrationResult
    {
        $sorting = (int) $this->connection->fetchOne('SELECT MAX(sorting) FROM tl_css_style_group');
        $ids = $this->connection->fetchFirstColumn('SELECT id FROM tl_css_style_group WHERE sorting = 0 ORDER BY title, id');

        foreach ($ids as $id) {
            $sorting += 128;

            $this->connection->update('tl_css_style_group', ['sorting' => $sorting], ['id' => $id]);
        }

        return $this->createResult(true, \sprintf('Added a sorting value to %d CSS style groups.', \count($ids)));
    }
}
