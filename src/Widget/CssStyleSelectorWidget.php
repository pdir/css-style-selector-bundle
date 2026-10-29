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

use Contao\SelectMenu;
use Contao\StringUtil;
use Contao\System;
use Contao\Widget;
use Craffft\CssStyleSelectorBundle\Repository\CssStyleRepository;

/**
 * Shows one select menu per style group, side by side in a box per category
 * (with heading and number of selected styles if there are several). The
 * label and help text of the field are hidden, the legend already names it. Groups with multiple selection use
 * a multiple select menu. The value is the list of the selected style values
 * (the CSS classes of the styles).
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
        $selected = array_map(strval(...), StringUtil::deserialize($this->varValue, true));
        $categories = $this->getCategories();
        $html = '';

        foreach ($categories as $category) {
            $groups = '';
            $count = 0;

            foreach ($category['groups'] as $groupId => $group) {
                $groups .= $this->generateGroup($groupId, $group, $selected);
                $count += \count(array_intersect(array_map(strval(...), array_keys($group['styles'])), $selected));
            }

            $groups = '<div class="css-style-selector__groups">'.$groups.'</div>';

            // A single category needs no heading
            if (\count($categories) < 2 || '' === $category['title']) {
                $html .= '<div class="css-style-selector__category">'.$groups.'</div>';

                continue;
            }

            $html .= \sprintf(
                '<div class="css-style-selector__category"><div class="css-style-selector__category-head"><h3 class="css-style-selector__category-title">%s%s</h3>%s</div>%s</div>',
                StringUtil::specialchars($category['title']),
                $count ? ' <span class="css-style-selector__count">'.$count.'</span>' : '',
                '' !== $category['description'] ? '<p class="css-style-selector__category-description">'.StringUtil::specialchars($category['description']).'</p>' : '',
                $groups,
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
        $groups = $this->getAllowedGroups();
        $values = [];

        foreach ($groups as $groupId => $group) {
            $input = \is_array($varInput) ? ($varInput[$groupId] ?? []) : [];

            foreach ((array) $input as $value) {
                $values[] = (string) $value;
            }
        }

        // Only styles of the allowed groups, one per group unless the group allows multiple styles
        $selected = CssStyleRepository::filterSelection($groups, $values);

        // Keep styles the current user cannot see, e.g. styles of groups limited to other user groups
        $offered = array_map(strval(...), array_merge(...array_map(static fn (array $group): array => array_keys($group['styles']), array_values($groups)) ?: [[]]));

        foreach (StringUtil::deserialize($this->varValue, true) as $value) {
            if (!\in_array((string) $value, $offered, true)) {
                $selected[] = (string) $value;
            }
        }

        if ($this->mandatory && !$selected) {
            $this->addError(\sprintf($GLOBALS['TL_LANG']['ERR']['mandatory'], $this->strLabel));
        }

        return array_values(array_unique($selected));
    }

    /**
     * @param array<string, mixed> $group
     * @param list<string>         $selected
     */
    private function generateGroup(int $groupId, array $group, array $selected): string
    {
        $multiple = $group['multiple'];
        $options = [];

        if (!$multiple) {
            $options[] = ['value' => '', 'label' => $GLOBALS['TL_LANG']['MSC']['cssStyleSelectorNone'] ?? '-'];
        }

        foreach ($group['styles'] as $value => $title) {
            $options[] = ['value' => (string) $value, 'label' => StringUtil::specialchars($title)];
        }

        $values = array_values(array_intersect(array_map(strval(...), array_keys($group['styles'])), $selected));
        $id = $this->strId.'_'.$groupId;

        // The select menu adds the brackets of a multiple selection itself
        $select = new SelectMenu([
            'name' => $this->strName.'['.$groupId.']',
            'id' => $id,
            'options' => $options,
            'value' => $multiple ? $values : ($values[0] ?? ''),
            'multiple' => $multiple,
            'chosen' => true,
        ]);

        $description = StringUtil::specialchars((string) $group['description']);

        return \sprintf(
            '<div class="css-style-selector__group%s"><label class="css-style-selector__label" for="ctrl_%s">%s</label>%s%s</div>',
            $values ? ' is-selected' : '',
            StringUtil::specialchars($id),
            StringUtil::specialchars((string) $group['title']),
            $select->generate(),
            // Shortened to two lines, the full text is shown on hover
            '' !== $description ? '<p class="css-style-selector__help" title="'.$description.'">'.$description.'</p>' : '',
        );
    }

    /**
     * Groups offered in the options (filtered by the field listener), in the
     * order of the repository.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getAllowedGroups(): array
    {
        $ids = array_map(static fn (array $option): int => (int) $option['value'], $this->arrOptions);

        return array_filter(
            $this->getRepository()->findGroups(),
            static fn (array $group): bool => \in_array($group['id'], $ids, true),
        );
    }

    /**
     * @return array<int, array{title: string, description: string, groups: array<int, array<string, mixed>>}>
     */
    private function getCategories(): array
    {
        $categories = [];

        foreach ($this->getAllowedGroups() as $groupId => $group) {
            $categories[$group['pid']] ??= [
                'title' => (string) ($group['categoryTitle'] ?? ''),
                'description' => (string) ($group['categoryDescription'] ?? ''),
                'groups' => [],
            ];

            $categories[$group['pid']]['groups'][$groupId] = $group;
        }

        return $categories;
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
            .widget:has(>.css-style-selector)>h3,.widget:has(>.css-style-selector)>.tl_help{display:none}
            .css-style-selector{display:flex;flex-direction:column;gap:12px;margin:6px 0 10px}
            .css-style-selector__category{padding:12px 14px 14px;border:1px solid var(--content-border,#e6e6e8);border-left:3px solid var(--contao,#f47c00);border-radius:var(--border-radius,4px);background:var(--nested-bg,#f9f9fb)}
            .css-style-selector__category-head{display:flex;flex-wrap:wrap;align-items:baseline;gap:2px 10px;margin-bottom:10px}
            .css-style-selector__category-title{display:flex;align-items:center;gap:6px;margin:0;font-size:.9375rem;font-weight:600;color:var(--text,#222)}
            .css-style-selector__count{min-width:18px;padding:1px 6px;border-radius:9px;background:var(--contao,#f47c00);color:#fff;font-size:.6875rem;font-weight:600;line-height:16px;text-align:center}
            .css-style-selector__category-description{margin:0;color:var(--info,#7f7f7f);font-size:.75rem}
            .css-style-selector__groups{display:grid;grid-template-columns:repeat(auto-fill,minmax(min(100%,220px),1fr));gap:12px 16px;align-items:start}
            .css-style-selector__group{min-width:0}
            .css-style-selector__label{display:block;margin-bottom:4px;font-size:.8125rem;font-weight:400;color:var(--text,#222)}
            .css-style-selector__group.is-selected .css-style-selector__label{font-weight:600}
            .css-style-selector__group.is-selected .tl_select,.css-style-selector__group.is-selected .choices__inner{border-color:var(--contao,#f47c00)}
            .css-style-selector__help{display:-webkit-box;margin:4px 0 0;overflow:hidden;color:var(--info,#7f7f7f);font-size:.6875rem;line-height:1.35;-webkit-box-orient:vertical;-webkit-line-clamp:2}
            </style>
            HTML;
    }
}
