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

class CssStyleListener
{
    /**
     * Shows the name, the CSS classes, the preview color and the areas the
     * style is disabled in or limited to.
     *
     * @return RecordLabel|string
     */
    #[AsCallback(table: 'tl_css_style_selector', target: 'list.label.label')]
    public function listStyle(array $row): mixed
    {
        $lang = $GLOBALS['TL_LANG']['tl_css_style_selector'] ?? [];
        $disabled = [];
        $limited = [];

        foreach (CssStyleSelectorModel::getAvailableTypes() as $type) {
            $name = $lang[$type.'Enabled'][0] ?? $type;

            if ($row[CssStyleSelectorModel::getDisableField($type)] ?? false) {
                $disabled[] = $name;
            } elseif ($row[CssStyleSelectorModel::TYPE_RESTRICTIONS[$type][0] ?? ''] ?? false) {
                $limited[] = $name;
            }
        }

        $html = '';

        if (preg_match('/^[0-9a-f]{3}(?:[0-9a-f]{3})?$/i', (string) ($row['previewColor'] ?? ''))) {
            $html .= '<span style="display:inline-block;width:12px;height:12px;margin-right:6px;vertical-align:-1px;border:1px solid var(--form-border,#ccc);border-radius:2px;background-color:#'.$row['previewColor'].'"></span>';
        }

        $html .= StringUtil::specialchars((string) $row['styleDesignation']);
        $html .= ' <span class="label-info">['.StringUtil::specialchars((string) $row['cssClasses']).']</span>';

        $info = [];

        if ($disabled) {
            $info[] = ($lang['disabledIn'] ?? 'Disabled in').': '.implode(', ', $disabled);
        }

        if ($limited) {
            $info[] = ($lang['limited'] ?? 'limited').': '.implode(', ', $limited);
        }

        if ('' !== (string) ($row['description'] ?? '')) {
            array_unshift($info, (string) $row['description']);
        }

        if ($info) {
            $html .= ' <span class="tl_gray">– '.StringUtil::specialchars(implode(' · ', $info)).'</span>';
        }

        // Contao 6 expects a RecordLabel to render HTML, Contao 5 a string
        return class_exists(RecordLabel::class) ? RecordLabel::fromHtml($html) : $html;
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
