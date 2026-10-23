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

class CssStyleRepository implements ResetInterface
{
    /**
     * @var array<int, array<string, mixed>>|null
     */
    private array|null $styles = null;

    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * Returns all styles with the data of their group, keyed by ID and ordered
     * by group and manual sorting.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAll(): array
    {
        if (null !== $this->styles) {
            return $this->styles;
        }

        try {
            $rows = $this->connection->fetchAllAssociative(
                'SELECT s.*, g.title AS groupTitle, g.description AS groupDescription, g.multiple AS groupMultiple, g.limitUserGroups AS groupLimitUserGroups, g.userGroups AS groupUserGroups
                FROM tl_css_style_selector s
                LEFT JOIN tl_css_style_group g ON g.id = s.pid
                ORDER BY g.sorting, g.title, s.sorting, s.styleDesignation',
            );
        } catch (InvalidFieldNameException|TableNotFoundException) {
            // The database has not been migrated yet
            $rows = [];
        }

        $this->styles = [];

        foreach ($rows as $row) {
            $row['id'] = (int) $row['id'];
            $row['pid'] = (int) $row['pid'];

            // Styles without a group behave like a group with multiple selection
            $row['groupMultiple'] = !$row['pid'] || $row['groupMultiple'];

            $this->styles[$row['id']] = $row;
        }

        return $this->styles;
    }

    /**
     * Returns the styles available for the given type (and element type, e.g.
     * the content element type). If user groups are given, styles of groups
     * that are limited to other user groups are skipped.
     *
     * @param array<int>|null $userGroups
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAvailable(string $type, string|null $elementType = null, array|null $userGroups = null): array
    {
        if (!\in_array($type, CssStyleSelectorModel::getAvailableTypes(), true)) {
            return [];
        }

        $disableField = CssStyleSelectorModel::getDisableField($type);
        $restriction = CssStyleSelectorModel::TYPE_RESTRICTIONS[$type] ?? null;

        return array_filter(
            $this->findAll(),
            static function (array $style) use ($disableField, $restriction, $elementType, $userGroups): bool {
                if ($style[$disableField]) {
                    return false;
                }

                if (null !== $restriction) {
                    [$limitField, $allowedField] = $restriction;

                    if ($style[$limitField] && !\in_array($elementType, StringUtil::deserialize($style[$allowedField], true), true)) {
                        return false;
                    }
                }

                if (null !== $userGroups && $style['groupLimitUserGroups']) {
                    $allowedGroups = array_map(intval(...), StringUtil::deserialize($style['groupUserGroups'], true));

                    if (!array_intersect($userGroups, $allowedGroups)) {
                        return false;
                    }
                }

                return true;
            },
        );
    }

    /**
     * @param array<int>|null $userGroups
     *
     * @return array<int, string>
     */
    public function findOptions(string $type, string|null $elementType = null, array|null $userGroups = null): array
    {
        return array_map(
            static fn (array $style): string => (string) $style['styleDesignation'],
            $this->findAvailable($type, $elementType, $userGroups),
        );
    }

    /**
     * @param array<int|string> $ids
     *
     * @return array<int, string>
     */
    public function findDesignationsByIds(array $ids): array
    {
        return array_map(
            static fn (array $style): string => (string) $style['styleDesignation'],
            $this->findByIds($ids),
        );
    }

    /**
     * @param array<int|string> $ids
     *
     * @return list<string>
     */
    public function findCssClassesByIds(array $ids): array
    {
        return self::splitClasses(implode(' ', array_column($this->findByIds($ids), 'cssClasses')));
    }

    /**
     * Returns every CSS class managed by any style.
     *
     * @return list<string>
     */
    public function findAllCssClasses(): array
    {
        return self::splitClasses(implode(' ', array_column($this->findAll(), 'cssClasses')));
    }

    public function reset(): void
    {
        $this->styles = null;
    }

    /**
     * @return list<string>
     */
    public static function splitClasses(string|null $classes): array
    {
        $split = preg_split('/\s+/', trim((string) $classes), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_values(array_unique($split));
    }

    /**
     * @param array<int|string> $ids
     *
     * @return array<int, array<string, mixed>>
     */
    private function findByIds(array $ids): array
    {
        return array_intersect_key($this->findAll(), array_flip(array_map(intval(...), $ids)));
    }
}
