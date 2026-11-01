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

use Contao\BackendUser;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DataContainer\RecordLabel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\StringUtil;
use Contao\System;
use Craffft\CssStyleSelectorBundle\Models\CssStyleSelectorModel;
use Craffft\CssStyleSelectorBundle\Repository\CssStyleRepository;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Handles the "cssStyleSelector" field in all supported tables: adds it to the
 * palettes, provides the options, shows the selected styles in the list views
 * and writes the CSS classes of the selected styles into the table's own CSS
 * class field. In the back end, the CSS class field does not show the classes
 * of the selected styles.
 */
#[AsCallback(table: 'tl_article', target: 'config.onload', method: 'onLoad', priority: -100)]
#[AsCallback(table: 'tl_calendar_events', target: 'config.onload', method: 'onLoad', priority: -100)]
#[AsCallback(table: 'tl_content', target: 'config.onload', method: 'onLoad', priority: -100)]
#[AsCallback(table: 'tl_form', target: 'config.onload', method: 'onLoad', priority: -100)]
#[AsCallback(table: 'tl_form_field', target: 'config.onload', method: 'onLoad', priority: -100)]
#[AsCallback(table: 'tl_layout', target: 'config.onload', method: 'onLoad', priority: -100)]
#[AsCallback(table: 'tl_module', target: 'config.onload', method: 'onLoad', priority: -100)]
#[AsCallback(table: 'tl_news', target: 'config.onload', method: 'onLoad', priority: -100)]
#[AsCallback(table: 'tl_page', target: 'config.onload', method: 'onLoad', priority: -100)]
#[AsCallback(table: 'tl_article', target: 'fields.cssStyleSelector.options', method: 'getOptions')]
#[AsCallback(table: 'tl_calendar_events', target: 'fields.cssStyleSelector.options', method: 'getOptions')]
#[AsCallback(table: 'tl_content', target: 'fields.cssStyleSelector.options', method: 'getOptions')]
#[AsCallback(table: 'tl_form', target: 'fields.cssStyleSelector.options', method: 'getOptions')]
#[AsCallback(table: 'tl_form_field', target: 'fields.cssStyleSelector.options', method: 'getOptions')]
#[AsCallback(table: 'tl_layout', target: 'fields.cssStyleSelector.options', method: 'getOptions')]
#[AsCallback(table: 'tl_module', target: 'fields.cssStyleSelector.options', method: 'getOptions')]
#[AsCallback(table: 'tl_news', target: 'fields.cssStyleSelector.options', method: 'getOptions')]
#[AsCallback(table: 'tl_page', target: 'fields.cssStyleSelector.options', method: 'getOptions')]
#[AsCallback(table: 'tl_article', target: 'fields.cssID.load', method: 'hideManagedClasses')]
#[AsCallback(table: 'tl_calendar_events', target: 'fields.cssClass.load', method: 'hideManagedClasses')]
#[AsCallback(table: 'tl_content', target: 'fields.cssID.load', method: 'hideManagedClasses')]
#[AsCallback(table: 'tl_form', target: 'fields.attributes.load', method: 'hideManagedClasses')]
#[AsCallback(table: 'tl_form_field', target: 'fields.class.load', method: 'hideManagedClasses')]
#[AsCallback(table: 'tl_layout', target: 'fields.cssClass.load', method: 'hideManagedClasses')]
#[AsCallback(table: 'tl_module', target: 'fields.cssID.load', method: 'hideManagedClasses')]
#[AsCallback(table: 'tl_news', target: 'fields.cssClass.load', method: 'hideManagedClasses')]
#[AsCallback(table: 'tl_page', target: 'fields.cssClass.load', method: 'hideManagedClasses')]
#[AsCallback(table: 'tl_article', target: 'fields.cssStyleSelector.save', method: 'rememberSelection')]
#[AsCallback(table: 'tl_calendar_events', target: 'fields.cssStyleSelector.save', method: 'rememberSelection')]
#[AsCallback(table: 'tl_content', target: 'fields.cssStyleSelector.save', method: 'rememberSelection')]
#[AsCallback(table: 'tl_form', target: 'fields.cssStyleSelector.save', method: 'rememberSelection')]
#[AsCallback(table: 'tl_form_field', target: 'fields.cssStyleSelector.save', method: 'rememberSelection')]
#[AsCallback(table: 'tl_layout', target: 'fields.cssStyleSelector.save', method: 'rememberSelection')]
#[AsCallback(table: 'tl_module', target: 'fields.cssStyleSelector.save', method: 'rememberSelection')]
#[AsCallback(table: 'tl_news', target: 'fields.cssStyleSelector.save', method: 'rememberSelection')]
#[AsCallback(table: 'tl_page', target: 'fields.cssStyleSelector.save', method: 'rememberSelection')]
#[AsCallback(table: 'tl_article', target: 'config.onsubmit', method: 'applyCssClasses', priority: -100)]
#[AsCallback(table: 'tl_calendar_events', target: 'config.onsubmit', method: 'applyCssClasses', priority: -100)]
#[AsCallback(table: 'tl_content', target: 'config.onsubmit', method: 'applyCssClasses', priority: -100)]
#[AsCallback(table: 'tl_form', target: 'config.onsubmit', method: 'applyCssClasses', priority: -100)]
#[AsCallback(table: 'tl_form_field', target: 'config.onsubmit', method: 'applyCssClasses', priority: -100)]
#[AsCallback(table: 'tl_layout', target: 'config.onsubmit', method: 'applyCssClasses', priority: -100)]
#[AsCallback(table: 'tl_module', target: 'config.onsubmit', method: 'applyCssClasses', priority: -100)]
#[AsCallback(table: 'tl_news', target: 'config.onsubmit', method: 'applyCssClasses', priority: -100)]
#[AsCallback(table: 'tl_page', target: 'config.onsubmit', method: 'applyCssClasses', priority: -100)]
class CssStyleSelectorFieldListener implements ResetInterface
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

    private const TREE_MODES = [DataContainer::MODE_TREE, DataContainer::MODE_TREE_EXTENDED];

    /**
     * The selection before saving, for records whose style field was submitted.
     *
     * @var array<string, array<int, array<int|string>>>
     */
    private array $previousSelection = [];

    public function __construct(
        private readonly Connection $connection,
        private readonly CssStyleRepository $repository,
        private readonly RequestStack $requestStack,
        private readonly Security $security,
    ) {
    }

    public function onLoad(DataContainer $dc): void
    {
        if (!isset(self::TABLES[$dc->table], $GLOBALS['TL_DCA'][$dc->table]['fields'][self::FIELD])) {
            return;
        }

        $this->addToPalettes($dc);
        $this->addStylesToListLabels($dc->table);
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
     * Returns the styles the current user may select for the record.
     *
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

        $user = $this->security->getUser();
        $userGroups = $user instanceof BackendUser && !$user->isAdmin ? array_map(intval(...), (array) $user->groups) : null;

        return $this->repository->findOptions($type, $elementType, $userGroups);
    }

    public function reset(): void
    {
        $this->previousSelection = [];
    }

    /**
     * Removes the classes of the selected styles from the CSS class field, so
     * editors only see (and edit) the other classes. They are added again on
     * submit. Classes of styles that are not selected stay visible, because
     * they would otherwise get lost when the form is saved.
     */
    public function hideManagedClasses(mixed $value, DataContainer $dc): mixed
    {
        if (!isset(self::TABLES[$dc->table], $GLOBALS['TL_DCA'][$dc->table]['fields'][self::FIELD])) {
            return $value;
        }

        $managed = $this->repository->findCssClassesByIds(StringUtil::deserialize($dc->getCurrentRecord()[self::FIELD] ?? null, true));

        if (!self::TABLES[$dc->table][2]) {
            return implode(' ', array_diff(CssStyleRepository::splitClasses((string) $value), $managed));
        }

        $cssId = StringUtil::deserialize($value, true);
        $cssId[1] = implode(' ', array_diff(CssStyleRepository::splitClasses((string) ($cssId[1] ?? '')), $managed));

        return serialize([$cssId[0] ?? '', $cssId[1]]);
    }

    /**
     * Marks the record for synchronizing. A save that does not contain the style
     * field, e.g. from a front end editor or an API, must not change the styles.
     */
    public function rememberSelection(mixed $value, DataContainer $dc): mixed
    {
        $this->previousSelection[$dc->table][(int) $dc->id] ??= StringUtil::deserialize($dc->getCurrentRecord()[self::FIELD] ?? null, true);

        return $value;
    }

    /**
     * Synchronizes the CSS classes of the selected styles with the CSS class
     * field after all fields have been saved: the classes of the previously
     * selected styles are removed first, then the classes of the selected styles
     * are added. Other classes are kept, even if they belong to a style. Styles
     * that are no longer available for the record (e.g. after changing the
     * element type) are deselected. User group permissions are not checked
     * here, so styles selected by other users are kept.
     */
    public function applyCssClasses(DataContainer $dc): void
    {
        $table = $dc->table;

        if (!$dc->id || !isset(self::TABLES[$table], $GLOBALS['TL_DCA'][$table]['fields'][self::FIELD], $this->previousSelection[$table][(int) $dc->id])) {
            return;
        }

        $previousIds = $this->previousSelection[$table][(int) $dc->id];
        unset($this->previousSelection[$table][(int) $dc->id]);

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
        $availableIds = array_keys($this->repository->findAvailable($type, null !== $typeField ? ($row[$typeField] ?? null) : null));
        $selectedIds = array_values(array_filter($storedIds, static fn ($id): bool => \in_array((int) $id, $availableIds, true)));

        $cssId = $serialized ? StringUtil::deserialize($row[$classField], true) : null;
        $currentClasses = $serialized ? (string) ($cssId[1] ?? '') : (string) $row[$classField];

        $classes = array_diff(CssStyleRepository::splitClasses($currentClasses), $this->repository->findCssClassesByIds([...$previousIds, ...$storedIds]));
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

    /**
     * Appends the names of the selected styles to the record labels in the
     * list view by decorating the existing label or child record callback.
     */
    public function addStylesToListLabels(string $table): void
    {
        // The onload callback can run more than once per request
        if ($GLOBALS['TL_DCA'][$table]['config']['cssStyleSelectorLabels'] ?? false) {
            return;
        }

        $GLOBALS['TL_DCA'][$table]['config']['cssStyleSelectorLabels'] = true;

        $list = &$GLOBALS['TL_DCA'][$table]['list'];
        $mode = $list['sorting']['mode'] ?? DataContainer::MODE_SORTED;

        // Contao 5.3 renders the records of parent views with a child_record_callback
        if (isset($list['sorting']['child_record_callback'])) {
            $list['sorting']['child_record_callback'] = $this->decorate($list['sorting']['child_record_callback']);

            return;
        }

        if (($list['label']['showColumns'] ?? false) && !\in_array($mode, [DataContainer::MODE_PARENT, ...self::TREE_MODES], true)) {
            return;
        }

        if (isset($list['label']['label_callback'])) {
            $list['label']['label_callback'] = $this->decorate($list['label']['label_callback']);
        } elseif (!\in_array($mode, self::TREE_MODES, true)) {
            $list['label']['label_callback'] = $this->decorate(null);
        }
    }

    private function decorate(array|callable|null $callback): \Closure
    {
        return function (array $row, ...$args) use ($callback): mixed {
            if (null === $callback) {
                // No callback yet: the second argument is the generated label
                $label = class_exists(RecordLabel::class) ? RecordLabel::fromHtml((string) ($args[0] ?? '')) : (string) ($args[0] ?? '');
            } elseif (\is_array($callback)) {
                $label = System::importStatic($callback[0])->{$callback[1]}($row, ...$args);
            } else {
                $label = $callback($row, ...$args);
            }

            $names = $this->repository->findDesignationsByIds(StringUtil::deserialize($row[self::FIELD] ?? null, true));

            if (!$names) {
                return $label;
            }

            $badge = ' <span class="label-info css-style-selector-badge">['.StringUtil::specialchars(implode(', ', $names)).']</span>';

            if ($label instanceof RecordLabel) {
                $label->htmlLabel = ($label->htmlLabel ?? StringUtil::specialchars($label->label)).$badge;
                $label->label .= ' ['.implode(', ', $names).']';
            } elseif (\is_string($label)) {
                // Child records contain the preview, so the badge goes into the first line
                $label = preg_replace('/(<\/div>)/', $badge.'$1', $label, 1, $count) ?? $label;

                if (!$count) {
                    $label .= $badge;
                }
            } elseif (\is_array($label) && \is_string($label[0] ?? null)) {
                $label[0] .= $badge;
            }

            return $label;
        };
    }
}
