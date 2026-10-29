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
use Contao\StringUtil;
use Craffft\CssStyleSelectorBundle\Models\CssStyleSelectorModel;
use Craffft\CssStyleSelectorBundle\Repository\CssStyleRepository;
use Doctrine\DBAL\Connection;

/**
 * Moves the styles of the former table tl_css_style_selector into their group
 * (one row per style with CSS classes and title) and publishes each group in
 * the areas and for the element types its styles were available for. The
 * selection of the records (style IDs) is replaced by the CSS classes of the
 * styles. The table tl_css_style_selector is not removed, the database update
 * offers to drop it.
 */
class StyleRowsMigration extends AbstractMigration
{
    private const RECORD_TABLES = ['tl_article', 'tl_calendar_events', 'tl_content', 'tl_form', 'tl_form_field', 'tl_layout', 'tl_module', 'tl_news', 'tl_page'];

    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_css_style_selector', 'tl_css_style_group'])) {
            return false;
        }

        $styleColumns = array_change_key_case($schemaManager->listTableColumns('tl_css_style_selector'));
        $groupColumns = array_change_key_case($schemaManager->listTableColumns('tl_css_style_group'));

        if (!isset($styleColumns['pid'], $styleColumns['cssclasses'], $groupColumns['styles'], $groupColumns['incontent'])) {
            return false;
        }

        // Wait for the StyleGroupMigration, which moves styles without a group into a group
        if ($this->connection->fetchOne('SELECT COUNT(*) FROM tl_css_style_selector WHERE pid = 0')) {
            return false;
        }

        return (bool) $this->connection->fetchOne('SELECT COUNT(*) FROM tl_css_style_group g WHERE g.styles IS NULL AND EXISTS (SELECT 1 FROM tl_css_style_selector s WHERE s.pid = g.id)');
    }

    public function run(): MigrationResult
    {
        $styles = $this->connection->fetchAllAssociative('SELECT * FROM tl_css_style_selector ORDER BY pid, sorting, styleDesignation');
        $groupIds = $this->connection->fetchFirstColumn('SELECT id FROM tl_css_style_group WHERE styles IS NULL');
        $values = [];
        $converted = 0;

        foreach ($styles as $style) {
            $values[(int) $style['id']] = implode(' ', CssStyleRepository::splitClasses((string) $style['cssClasses']));
        }

        foreach ($groupIds as $groupId) {
            $groupStyles = array_values(array_filter($styles, static fn (array $style): bool => (int) $style['pid'] === (int) $groupId && '' !== $values[(int) $style['id']]));

            if (!$groupStyles) {
                continue;
            }

            $data = ['styles' => serialize(array_map(
                static fn (array $style): array => ['key' => $values[(int) $style['id']], 'value' => (string) $style['styleDesignation']],
                $groupStyles,
            ))];

            foreach (CssStyleSelectorModel::getAvailableTypes() as $type) {
                $enabled = array_filter($groupStyles, static fn (array $style): bool => !$style[CssStyleSelectorModel::getDisableField($type)]);
                $data[CssStyleSelectorModel::getEnableField($type)] = $enabled ? 1 : 0;

                $restriction = CssStyleSelectorModel::TYPE_RESTRICTIONS[$type] ?? null;

                if (null === $restriction || !$enabled) {
                    continue;
                }

                // Limited only if every style was limited, to the types of all styles
                $limitField = 'limit'.ucfirst($restriction);
                $allowed = [];

                foreach ($enabled as $style) {
                    if (!($style[$limitField] ?? false)) {
                        $allowed = [];
                        break;
                    }

                    $allowed = array_merge($allowed, StringUtil::deserialize($style[$restriction] ?? null, true));
                }

                $data[$restriction] = $allowed ? serialize(array_values(array_unique($allowed))) : null;
            }

            $this->connection->update('tl_css_style_group', $data, ['id' => (int) $groupId]);
            ++$converted;
        }

        $records = $this->convertSelections($values);

        return $this->createResult(true, \sprintf('Moved the CSS styles into %d style groups and converted the style selection of %d records.', $converted, $records));
    }

    /**
     * @param array<int, string> $values style ID => CSS classes
     */
    private function convertSelections(array $values): int
    {
        $schemaManager = $this->connection->createSchemaManager();
        $count = 0;

        foreach (self::RECORD_TABLES as $table) {
            if (!$schemaManager->tablesExist([$table]) || !isset(array_change_key_case($schemaManager->listTableColumns($table))['cssstyleselector'])) {
                continue;
            }

            $rows = $this->connection->fetchAllAssociative(\sprintf('SELECT id, cssStyleSelector FROM %s WHERE cssStyleSelector IS NOT NULL', $table));

            foreach ($rows as $row) {
                $selection = [];

                foreach (StringUtil::deserialize($row['cssStyleSelector'], true) as $id) {
                    if ('' !== ($values[(int) $id] ?? '')) {
                        $selection[] = $values[(int) $id];
                    }
                }

                $selection = array_values(array_unique($selection));

                $this->connection->update($table, ['cssStyleSelector' => $selection ? serialize($selection) : null], ['id' => $row['id']]);
                ++$count;
            }
        }

        return $count;
    }
}
