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

namespace Craffft\CssStyleSelectorBundle\EventListener\DataContainer;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\StringUtil;
use Craffft\CssStyleSelectorBundle\Models\CssStyleSelectorModel;
use Craffft\CssStyleSelectorBundle\Repository\CssStyleRepository;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Handles the "cssStyleSelector" field in all supported tables: adds it to the
 * palettes, provides the options and writes the selected CSS classes into the
 * table's own CSS class field.
 */
#[AsCallback(table: 'tl_article', target: 'config.onload', method: 'addToPalettes', priority: -100)]
#[AsCallback(table: 'tl_calendar_events', target: 'config.onload', method: 'addToPalettes', priority: -100)]
#[AsCallback(table: 'tl_content', target: 'config.onload', method: 'addToPalettes', priority: -100)]
#[AsCallback(table: 'tl_form', target: 'config.onload', method: 'addToPalettes', priority: -100)]
#[AsCallback(table: 'tl_form_field', target: 'config.onload', method: 'addToPalettes', priority: -100)]
#[AsCallback(table: 'tl_layout', target: 'config.onload', method: 'addToPalettes', priority: -100)]
#[AsCallback(table: 'tl_module', target: 'config.onload', method: 'addToPalettes', priority: -100)]
#[AsCallback(table: 'tl_news', target: 'config.onload', method: 'addToPalettes', priority: -100)]
#[AsCallback(table: 'tl_page', target: 'config.onload', method: 'addToPalettes', priority: -100)]
#[AsCallback(table: 'tl_article', target: 'fields.cssStyleSelector.options', method: 'getOptions')]
#[AsCallback(table: 'tl_calendar_events', target: 'fields.cssStyleSelector.options', method: 'getOptions')]
#[AsCallback(table: 'tl_content', target: 'fields.cssStyleSelector.options', method: 'getOptions')]
#[AsCallback(table: 'tl_form', target: 'fields.cssStyleSelector.options', method: 'getOptions')]
#[AsCallback(table: 'tl_form_field', target: 'fields.cssStyleSelector.options', method: 'getOptions')]
#[AsCallback(table: 'tl_layout', target: 'fields.cssStyleSelector.options', method: 'getOptions')]
#[AsCallback(table: 'tl_module', target: 'fields.cssStyleSelector.options', method: 'getOptions')]
#[AsCallback(table: 'tl_news', target: 'fields.cssStyleSelector.options', method: 'getOptions')]
#[AsCallback(table: 'tl_page', target: 'fields.cssStyleSelector.options', method: 'getOptions')]
#[AsCallback(table: 'tl_article', target: 'config.onsubmit', method: 'applyCssClasses', priority: -100)]
#[AsCallback(table: 'tl_calendar_events', target: 'config.onsubmit', method: 'applyCssClasses', priority: -100)]
#[AsCallback(table: 'tl_content', target: 'config.onsubmit', method: 'applyCssClasses', priority: -100)]
#[AsCallback(table: 'tl_form', target: 'config.onsubmit', method: 'applyCssClasses', priority: -100)]
#[AsCallback(table: 'tl_form_field', target: 'config.onsubmit', method: 'applyCssClasses', priority: -100)]
#[AsCallback(table: 'tl_layout', target: 'config.onsubmit', method: 'applyCssClasses', priority: -100)]
#[AsCallback(table: 'tl_module', target: 'config.onsubmit', method: 'applyCssClasses', priority: -100)]
#[AsCallback(table: 'tl_news', target: 'config.onsubmit', method: 'applyCssClasses', priority: -100)]
#[AsCallback(table: 'tl_page', target: 'config.onsubmit', method: 'applyCssClasses', priority: -100)]
class CssStyleSelectorFieldListener
{
    public const FIELD = 'cssStyleSelector';

    public const LEGEND = 'css_style_selector_legend';

    /**
     * Table => [style type, CSS class field, whether the CSS class field is a
     * serialized [id, class] pair, field holding the element type].
     */
    private const TABLES = [
        'tl_article' => [CssStyleSelectorModel::TYPE_ARTICLE, 'cssID', true, null],
        'tl_calendar_events' => [CssStyleSelectorModel::TYPE_CALENDAR_EVENTS, 'cssClass', false, null],
        'tl_content' => [CssStyleSelectorModel::TYPE_CONTENT, 'cssID', true, 'type'],
        'tl_form' => [CssStyleSelectorModel::TYPE_FORM, 'attributes', true, null],
        'tl_form_field' => [CssStyleSelectorModel::TYPE_FORM_FIELD, 'class', false, 'type'],
        'tl_layout' => [CssStyleSelectorModel::TYPE_LAYOUT, 'cssClass', false, null],
        'tl_module' => [CssStyleSelectorModel::TYPE_MODULE, 'cssID', true, 'type'],
        'tl_news' => [CssStyleSelectorModel::TYPE_NEWS, 'cssClass', false, null],
        'tl_page' => [CssStyleSelectorModel::TYPE_PAGE, 'cssClass', false, null],
    ];

    public function __construct(
        private readonly Connection $connection,
        private readonly CssStyleRepository $repository,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * Adds the field in its own legend after the first legend of every palette
     * that contains the CSS class field. This runs on load so that palettes
     * registered late (e.g. by RockSolid Custom Elements or other extensions)
     * are covered as well.
     */
    public function addToPalettes(DataContainer $dc): void
    {
        $table = $dc->table;

        if (!isset(self::TABLES[$table], $GLOBALS['TL_DCA'][$table]['fields'][self::FIELD])) {
            return;
        }

        // Do not show an empty selection if there are no styles for this record
        if ('edit' === $this->requestStack->getCurrentRequest()?->query->get('act') && !$this->getOptions($dc)) {
            return;
        }

        $GLOBALS['TL_LANG'][$table][self::LEGEND] ??= $GLOBALS['TL_LANG']['MSC']['cssStyleSelectorLegend'] ?? self::LEGEND;

        $classField = preg_quote(self::TABLES[$table][1], '/');

        foreach ($GLOBALS['TL_DCA'][$table]['palettes'] ?? [] as $name => $palette) {
            if (
                '__selector__' === $name
                || !\is_string($palette)
                || !preg_match('/(?:^|[,;])'.$classField.'(?:[,;]|$)/', $palette)
                || preg_match('/(?:^|[,;])'.self::FIELD.'(?:[,;]|$)/', $palette)
            ) {
                continue;
            }

            if (preg_match('/^\{([^},:]+)/', $palette, $matches)) {
                $palette = PaletteManipulator::create()
                    ->addLegend(self::LEGEND, $matches[1], PaletteManipulator::POSITION_AFTER)
                    ->addField(self::FIELD, self::LEGEND, PaletteManipulator::POSITION_APPEND)
                    ->applyToString($palette)
                ;
            } else {
                $palette = preg_replace('/(^|[,;])('.$classField.')(?=[,;]|$)/', '$1'.self::FIELD.',$2', $palette);
            }

            $GLOBALS['TL_DCA'][$table]['palettes'][$name] = $palette;
        }
    }

    /**
     * @return array<int, string>
     */
    public function getOptions(DataContainer|null $dc = null): array
    {
        if (!$dc || !isset(self::TABLES[$dc->table])) {
            return [];
        }

        [$type, , , $typeField] = self::TABLES[$dc->table];
        $elementType = null;

        if (null !== $typeField && $dc->id) {
            $elementType = $dc->getCurrentRecord()[$typeField] ?? null;
        }

        return $this->repository->findOptions($type, $elementType);
    }

    /**
     * Synchronizes the CSS classes of the selected styles with the CSS class
     * field after all fields have been saved: classes of all known styles are
     * removed first, then the classes of the selected styles are added. Styles
     * that are no longer available for the record (e.g. after changing the
     * element type) are deselected.
     */
    public function applyCssClasses(DataContainer $dc): void
    {
        $table = $dc->table;

        if (!$dc->id || !isset(self::TABLES[$table], $GLOBALS['TL_DCA'][$table]['fields'][self::FIELD])) {
            return;
        }

        [$type, $classField, $serialized, $typeField] = self::TABLES[$table];

        $row = $this->connection->fetchAssociative(
            \sprintf(
                'SELECT * FROM %s WHERE id = ?',
                $this->connection->quoteIdentifier($table),
            ),
            [$dc->id],
        );

        if (false === $row) {
            return;
        }

        $storedIds = StringUtil::deserialize($row[self::FIELD], true);
        $availableIds = array_keys($this->repository->findOptions($type, null !== $typeField ? ($row[$typeField] ?? null) : null));
        $selectedIds = array_values(array_filter($storedIds, static fn ($id): bool => \in_array((int) $id, $availableIds, true)));

        $cssId = $serialized ? StringUtil::deserialize($row[$classField], true) : null;
        $currentClasses = $serialized ? (string) ($cssId[1] ?? '') : (string) $row[$classField];

        $classes = array_diff(CssStyleRepository::splitClasses($currentClasses), $this->repository->findAllCssClasses());
        $classes = array_merge($classes, $this->repository->findCssClassesByIds($selectedIds));
        $newClasses = implode(' ', array_unique($classes));

        $data = [];

        if ($newClasses !== $currentClasses) {
            $data[$this->connection->quoteIdentifier($classField)] = $serialized ? serialize([$cssId[0] ?? '', $newClasses]) : $newClasses;
        }

        if ($selectedIds !== $storedIds) {
            $data[$this->connection->quoteIdentifier(self::FIELD)] = $selectedIds ? serialize($selectedIds) : null;
        }

        if (!$data) {
            return;
        }

        $this->connection->update($table, $data, ['id' => (int) $dc->id]);

        DataContainer::clearCurrentRecordCache($dc->id, $table);
    }
}
