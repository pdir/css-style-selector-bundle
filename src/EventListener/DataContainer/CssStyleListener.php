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

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\System;
use Craffft\CssStyleSelectorBundle\Models\CssStyleSelectorModel;
use Craffft\CssStyleSelectorBundle\Repository\CssStyleRepository;

class CssStyleListener
{
    #[AsCallback(table: 'tl_css_style_selector', target: 'list.label.label')]
    public function listColumns(array $row, string $label, DataContainer $dc, array $args): array
    {
        // The first two columns are the designation and the CSS classes
        foreach (CssStyleSelectorModel::getAvailableTypes() as $i => $type) {
            $column = $i + 2;

            if ($row[CssStyleSelectorModel::getDisableField($type)]) {
                $args[$column] = $GLOBALS['TL_LANG']['MSC']['no'];
            } elseif ($row[CssStyleSelectorModel::TYPE_RESTRICTIONS[$type][0] ?? ''] ?? false) {
                $args[$column] = $GLOBALS['TL_LANG']['tl_css_style_selector']['limited'] ?? $GLOBALS['TL_LANG']['MSC']['yes'];
            } else {
                $args[$column] = $GLOBALS['TL_LANG']['MSC']['yes'];
            }
        }

        return $args;
    }

    #[AsCallback(table: 'tl_css_style_selector', target: 'fields.cssClasses.save')]
    public function normalizeCssClasses(mixed $value): string
    {
        return implode(' ', CssStyleRepository::splitClasses((string) $value));
    }

    #[AsCallback(table: 'tl_css_style_selector', target: 'fields.contentElements.options')]
    public function getContentElements(): array
    {
        return array_map(array_keys(...), $GLOBALS['TL_CTE'] ?? []);
    }

    #[AsCallback(table: 'tl_css_style_selector', target: 'fields.modules.options')]
    public function getModules(): array
    {
        System::loadLanguageFile('modules');

        return array_map(array_keys(...), $GLOBALS['FE_MOD'] ?? []);
    }

    #[AsCallback(table: 'tl_css_style_selector', target: 'fields.formFields.options')]
    public function getFormFields(): array
    {
        return array_keys($GLOBALS['TL_FFL'] ?? []);
    }
}
