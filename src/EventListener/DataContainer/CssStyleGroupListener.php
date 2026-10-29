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

use Contao\CoreBundle\DataContainer\RecordLabel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\StringUtil;
use Contao\System;
use Craffft\CssStyleSelectorBundle\Models\CssStyleSelectorModel;
use Craffft\CssStyleSelectorBundle\Repository\CssStyleRepository;

class CssStyleGroupListener
{
    /**
     * Shows the title, the description, the number of styles and the areas
     * the group is published in.
     *
     * @return RecordLabel|string
     */
    #[AsCallback(table: 'tl_css_style_group', target: 'list.label.label')]
    public function listGroup(array $row): mixed
    {
        $lang = $GLOBALS['TL_LANG']['tl_css_style_group'] ?? [];
        $areas = [];

        foreach (CssStyleSelectorModel::getAvailableTypes() as $type) {
            $field = CssStyleSelectorModel::getEnableField($type);

            if (!($row[$field] ?? false)) {
                continue;
            }

            $area = $lang[$field][0] ?? $type;
            $limit = \count(StringUtil::deserialize($row[CssStyleSelectorModel::TYPE_RESTRICTIONS[$type] ?? ''] ?? null, true));

            $areas[] = $limit ? $area.' ('.$limit.')' : $area;
        }

        $styles = CssStyleRepository::parseStyles($row['styles'] ?? null);

        $html = StringUtil::specialchars((string) $row['title']);
        $html .= ' <span class="label-info">['.\count($styles).' × '.StringUtil::specialchars(implode(', ', array_slice(array_keys($styles), 0, 3)).(\count($styles) > 3 ? ', …' : '')).']</span>';

        $info = array_filter([(string) ($row['description'] ?? ''), implode(', ', $areas)]);

        if ($info) {
            $html .= ' <span class="tl_gray">– '.StringUtil::specialchars(implode(' · ', $info)).'</span>';
        }

        // Contao 6 expects a RecordLabel to render HTML, Contao 5 a string
        return class_exists(RecordLabel::class) ? RecordLabel::fromHtml($html) : $html;
    }

    /**
     * Normalizes the CSS classes and removes empty rows.
     */
    #[AsCallback(table: 'tl_css_style_group', target: 'fields.styles.save')]
    public function normalizeStyles(mixed $value): string|null
    {
        $rows = [];

        foreach (StringUtil::deserialize($value, true) as $row) {
            $class = implode(' ', CssStyleRepository::splitClasses((string) ($row['key'] ?? '')));

            if ('' === $class) {
                continue;
            }

            $title = trim((string) ($row['value'] ?? ''));
            $rows[] = ['key' => $class, 'value' => '' !== $title ? $title : $class];
        }

        return $rows ? serialize($rows) : null;
    }

    #[AsCallback(table: 'tl_css_style_group', target: 'fields.contentElements.options')]
    public function getContentElements(): array
    {
        return array_map(array_keys(...), $GLOBALS['TL_CTE'] ?? []);
    }

    #[AsCallback(table: 'tl_css_style_group', target: 'fields.modules.options')]
    public function getModules(): array
    {
        System::loadLanguageFile('modules');

        return array_map(array_keys(...), $GLOBALS['FE_MOD'] ?? []);
    }

    #[AsCallback(table: 'tl_css_style_group', target: 'fields.formFields.options')]
    public function getFormFields(): array
    {
        return array_keys($GLOBALS['TL_FFL'] ?? []);
    }
}
