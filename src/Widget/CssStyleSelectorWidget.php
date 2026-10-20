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

namespace Craffft\CssStyleSelectorBundle\Widget;

use Contao\CoreBundle\Image\Studio\Studio;
use Contao\StringUtil;
use Contao\System;
use Contao\Widget;
use Craffft\CssStyleSelectorBundle\Repository\CssStyleRepository;

/**
 * Shows the available styles as tiles, grouped by style group. Groups with
 * single selection use radio buttons, the others use checkboxes.
 *
 * @property bool $mandatory
 */
class CssStyleSelectorWidget extends Widget
{
    protected $blnSubmitInput = true;

    protected $strTemplate = 'be_widget';

    private static bool $stylesAdded = false;

    /**
     * @param string $strKey
     * @param mixed  $varValue
     */
    public function __set($strKey, $varValue)
    {
        if ('options' === $strKey) {
            $this->arrOptions = StringUtil::deserialize($varValue, true);

            return;
        }

        parent::__set($strKey, $varValue);
    }

    public function generate(): string
    {
        $selected = array_map(intval(...), StringUtil::deserialize($this->varValue, true));
        $html = '';

        foreach ($this->getGroups() as $groupId => $group) {
            $multiple = $group['multiple'];
            $name = $this->strName.'['.$groupId.']'.($multiple ? '[]' : '');
            $options = '';

            if (!$multiple) {
                $hasSelection = (bool) array_intersect($selected, array_keys($group['styles']));
                $options .= $this->generateOption('radio', $name, '', $GLOBALS['TL_LANG']['MSC']['cssStyleSelectorNone'] ?? '–', '', !$hasSelection);
            }

            foreach ($group['styles'] as $id => $style) {
                $options .= $this->generateOption(
                    $multiple ? 'checkbox' : 'radio',
                    $name,
                    (string) $id,
                    (string) $style['styleDesignation'],
                    (string) ($style['description'] ?? ''),
                    \in_array($id, $selected, true),
                    $this->generatePreview($style),
                );
            }

            $html .= \sprintf(
                '<fieldset class="css-style-selector__group"><legend>%s</legend><div class="css-style-selector__options">%s</div></fieldset>',
                StringUtil::specialchars($group['title'] ?: ($GLOBALS['TL_LANG']['MSC']['cssStyleSelectorUngrouped'] ?? '')),
                $options,
            );
        }

        return self::getStyles().\sprintf(
            '<div id="ctrl_%s" class="css-style-selector%s">%s</div>',
            $this->strId,
            $this->strClass ? ' '.$this->strClass : '',
            $html,
        );
    }

    /**
     * @param mixed $varInput
     *
     * @return list<string>
     */
    protected function validator($varInput): array
    {
        $allowed = array_map(static fn (array $option): int => (int) $option['value'], $this->arrOptions);
        $styles = $this->getRepository()->findAll();
        $selected = [];
        $selectedGroups = [];

        foreach (\is_array($varInput) ? $varInput : [] as $values) {
            foreach ((array) $values as $id) {
                $id = (int) $id;

                if (!\in_array($id, $allowed, true) || !isset($styles[$id])) {
                    continue;
                }

                $groupId = $styles[$id]['pid'];

                // Only one style per group unless the group allows multiple styles
                if (!$styles[$id]['groupMultiple'] && isset($selectedGroups[$groupId])) {
                    continue;
                }

                $selectedGroups[$groupId] = true;
                $selected[] = $id;
            }
        }

        // Keep styles the current user cannot see, e.g. styles of user groups
        foreach (StringUtil::deserialize($this->varValue, true) as $id) {
            if (!\in_array((int) $id, $allowed, true)) {
                $selected[] = (int) $id;
            }
        }

        if ($this->mandatory && !$selected) {
            $this->addError(\sprintf($GLOBALS['TL_LANG']['ERR']['mandatory'], $this->strLabel));
        }

        return array_map(strval(...), array_values(array_unique($selected)));
    }

    /**
     * @return array<int, array{title: string, multiple: bool, styles: array<int, array<string, mixed>>}>
     */
    private function getGroups(): array
    {
        $styles = $this->getRepository()->findAll();
        $groups = [];

        // The options are ordered by group and sorting and already filtered
        foreach ($this->arrOptions as $option) {
            $style = $styles[(int) $option['value']] ?? null;

            if (null === $style) {
                continue;
            }

            $groups[$style['pid']] ??= [
                'title' => (string) ($style['groupTitle'] ?? ''),
                'multiple' => (bool) $style['groupMultiple'],
                'styles' => [],
            ];

            $groups[$style['pid']]['styles'][$style['id']] = $style;
        }

        return $groups;
    }

    private function generateOption(string $type, string $name, string $value, string $label, string $description, bool $checked, string $preview = ''): string
    {
        return \sprintf(
            '<label class="css-style-selector__option"%s><input type="%s" name="%s" value="%s"%s>%s<span class="css-style-selector__text"><span class="css-style-selector__name">%s</span>%s</span></label>',
            $description ? ' title="'.StringUtil::specialchars($description).'"' : '',
            $type,
            StringUtil::specialchars($name),
            StringUtil::specialchars($value),
            $checked ? ' checked' : '',
            $preview,
            StringUtil::specialchars($label),
            $description ? '<span class="css-style-selector__description">'.StringUtil::specialchars($description).'</span>' : '',
        );
    }

    /**
     * @param array<string, mixed> $style
     */
    private function generatePreview(array $style): string
    {
        $image = '';

        if (!empty($style['previewImage'])) {
            try {
                /** @var Studio $studio */
                $studio = System::getContainer()->get('contao.image.studio');

                $figure = $studio
                    ->createFigureBuilder()
                    ->fromUuid((string) $style['previewImage'])
                    ->setSize([96, 96, 'box'])
                    ->buildIfResourceExists()
                ;

                if (null !== $figure) {
                    $image = '<img src="'.StringUtil::specialchars($figure->getImage()->getImageSrc()).'" alt="" loading="lazy">';
                }
            } catch (\Throwable) {
                // Do not break the form because of a broken preview image
            }
        }

        $color = (string) ($style['previewColor'] ?? '');
        $color = preg_match('/^[0-9a-f]{3}(?:[0-9a-f]{3})?$/i', $color) ? $color : '';

        if ('' === $image && '' === $color) {
            return '';
        }

        return \sprintf(
            '<span class="css-style-selector__preview"%s>%s</span>',
            '' !== $color ? ' style="background-color:#'.$color.'"' : '',
            $image,
        );
    }

    private function getRepository(): CssStyleRepository
    {
        /** @var CssStyleRepository */
        return System::getContainer()->get(CssStyleRepository::class);
    }

    private static function getStyles(): string
    {
        if (self::$stylesAdded) {
            return '';
        }

        self::$stylesAdded = true;

        return <<<'HTML'
            <style>
            .css-style-selector{display:flex;flex-direction:column;gap:12px;margin:3px 0}
            .css-style-selector__group{border:0;margin:0;padding:0;min-width:0}
            .css-style-selector__group>legend{font-weight:600;margin-bottom:6px;padding:0}
            .css-style-selector__options{display:flex;flex-wrap:wrap;gap:6px}
            .css-style-selector__option{display:flex;align-items:center;gap:8px;max-width:280px;padding:6px 10px;border:1px solid var(--form-border,#ccc);border-radius:var(--border-radius,4px);background:var(--form-bg,#fff);cursor:pointer;line-height:1.3}
            .css-style-selector__option:hover{background:var(--form-bg-hover,#f6f6f6)}
            .css-style-selector__option:has(input:checked){border-color:var(--contao,#f47c00);box-shadow:inset 0 0 0 1px var(--contao,#f47c00)}
            .css-style-selector__option:has(input:focus-visible){outline:2px solid var(--contao,#f47c00);outline-offset:1px}
            .css-style-selector__option input{margin:0;flex-shrink:0}
            .css-style-selector__preview{display:flex;align-items:center;justify-content:center;flex-shrink:0;width:32px;height:32px;overflow:hidden;border:1px solid var(--form-border,#ccc);border-radius:var(--border-radius,4px)}
            .css-style-selector__preview img{display:block;max-width:100%;max-height:100%}
            .css-style-selector__text{display:flex;flex-direction:column;min-width:0}
            .css-style-selector__description{color:var(--gray,#999);font-size:.875em}
            </style>
            HTML;
    }
}
