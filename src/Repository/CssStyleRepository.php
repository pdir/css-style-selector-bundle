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

namespace Craffft\CssStyleSelectorBundle\Repository;

use Contao\StringUtil;
use Craffft\CssStyleSelectorBundle\Models\CssStyleSelectorModel;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\InvalidFieldNameException;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Reads the style groups. Each group holds its styles as rows of CSS classes
 * and title; a selected style is stored by its CSS classes (the "style value"),
 * so selections survive copying and importing records.
 */
class CssStyleRepository implements ResetInterface
{
    /**
     * @var array<int, array<string, mixed>>|null
     */
    private array|null $groups = null;

    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * Returns all groups with their styles (style value => title) and the
     * data of their category, keyed by ID and ordered by category and group.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findGroups(): array
    {
        if (null !== $this->groups) {
            return $this->groups;
        }

        try {
            $rows = $this->connection->fetchAllAssociative(
                'SELECT g.*, c.title AS categoryTitle, c.description AS categoryDescription
                FROM tl_css_style_group g
                LEFT JOIN tl_css_style_category c ON c.id = g.pid
                ORDER BY c.sorting, c.title, g.sorting, g.title',
            );
        } catch (InvalidFieldNameException|TableNotFoundException) {
            // The database has not been migrated yet
            $rows = [];
        }

        $this->groups = [];

        foreach ($rows as $row) {
            $row['id'] = (int) $row['id'];
            $row['pid'] = (int) $row['pid'];
            $row['multiple'] = (bool) $row['multiple'];
            $row['styles'] = self::parseStyles($row['styles'] ?? null);

            if ($row['styles']) {
                $this->groups[$row['id']] = $row;
            }
        }

        return $this->groups;
    }

    /**
     * Returns the groups available for the given type (and element type, e.g.
     * the content element type). If user groups are given, groups that are
     * limited to other user groups are skipped.
     *
     * @param array<int>|null $userGroups
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAvailableGroups(string $type, string|null $elementType = null, array|null $userGroups = null): array
    {
        if (!\in_array($type, CssStyleSelectorModel::getAvailableTypes(), true)) {
            return [];
        }

        $enableField = CssStyleSelectorModel::getEnableField($type);
        $restriction = CssStyleSelectorModel::TYPE_RESTRICTIONS[$type] ?? null;

        return array_filter(
            $this->findGroups(),
            static function (array $group) use ($enableField, $restriction, $elementType, $userGroups): bool {
                if (!($group[$enableField] ?? false)) {
                    return false;
                }

                if (null !== $restriction) {
                    $allowed = StringUtil::deserialize($group[$restriction] ?? null, true);

                    if ($allowed && !\in_array($elementType, $allowed, true)) {
                        return false;
                    }
                }

                if (null !== $userGroups && $group['limitUserGroups']) {
                    $allowedGroups = array_map(intval(...), StringUtil::deserialize($group['userGroups'], true));

                    if (!array_intersect($userGroups, $allowedGroups)) {
                        return false;
                    }
                }

                return true;
            },
        );
    }

    /**
     * Returns the selected style values that belong to the given groups; for
     * groups with single selection only the first one.
     *
     * @param array<int, array<string, mixed>> $groups
     * @param array<string>                    $values
     *
     * @return list<string>
     */
    public static function filterSelection(array $groups, array $values): array
    {
        $selected = [];

        foreach ($groups as $group) {
            $matches = array_values(array_intersect(array_map(strval(...), array_keys($group['styles'])), $values));

            array_push($selected, ...($group['multiple'] ? $matches : \array_slice($matches, 0, 1)));
        }

        return array_values(array_unique($selected));
    }

    /**
     * Returns the titles of the given style values.
     *
     * @param array<string> $values
     *
     * @return list<string>
     */
    public function findTitles(array $values): array
    {
        $titles = [];

        foreach ($this->findGroups() as $group) {
            foreach ($group['styles'] as $value => $title) {
                if (\in_array((string) $value, $values, true)) {
                    $titles[(string) $value] ??= $title;
                }
            }
        }

        return array_values($titles);
    }

    public function reset(): void
    {
        $this->groups = null;
    }

    /**
     * Returns the styles of a group (serialized rows of "key" = CSS classes
     * and "value" = title) as style value => title.
     *
     * @return array<string, string>
     */
    public static function parseStyles(mixed $rows): array
    {
        $styles = [];

        foreach (StringUtil::deserialize($rows, true) as $row) {
            $value = implode(' ', self::splitClasses((string) ($row['key'] ?? '')));

            if ('' !== $value) {
                $styles[$value] ??= trim((string) ($row['value'] ?? '')) ?: $value;
            }
        }

        return $styles;
    }

    /**
     * @return list<string>
     */
    public static function splitClasses(string|null $classes): array
    {
        $split = preg_split('/\s+/', trim((string) $classes), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_values(array_unique($split));
    }
}
