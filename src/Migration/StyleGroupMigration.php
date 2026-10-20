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
 * Moves styles without a group (e.g. from version 2 or inserted by a theme
 * installer) into the group "Styles", which allows multiple selection like
 * before.
 */
class StyleGroupMigration extends AbstractMigration
{
    public const GROUP_TITLE = 'Styles';

    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_css_style_selector', 'tl_css_style_group'])) {
            return false;
        }

        $columns = array_change_key_case($schemaManager->listTableColumns('tl_css_style_selector'));

        if (!isset($columns['pid'], $columns['sorting'])) {
            return false;
        }

        return (bool) $this->connection->fetchOne('SELECT COUNT(*) FROM tl_css_style_selector WHERE pid = 0');
    }

    public function run(): MigrationResult
    {
        $groupId = $this->connection->fetchOne('SELECT id FROM tl_css_style_group WHERE title = ? ORDER BY id LIMIT 1', [self::GROUP_TITLE]);

        if (false === $groupId) {
            $this->connection->insert('tl_css_style_group', [
                'tstamp' => time(),
                'title' => self::GROUP_TITLE,
                'multiple' => 1,
            ]);

            $groupId = $this->connection->lastInsertId();
        }

        $sorting = (int) $this->connection->fetchOne('SELECT MAX(sorting) FROM tl_css_style_selector WHERE pid = ?', [$groupId]);
        $ids = $this->connection->fetchFirstColumn('SELECT id FROM tl_css_style_selector WHERE pid = 0 ORDER BY styleDesignation');

        foreach ($ids as $id) {
            $sorting += 128;

            $this->connection->update('tl_css_style_selector', ['pid' => $groupId, 'sorting' => $sorting], ['id' => $id]);
        }

        return $this->createResult(true, \sprintf('Moved %d CSS styles into the group "%s".', \count($ids), self::GROUP_TITLE));
    }
}
