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
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

class CssStyleRepository
{
    private const TABLE = 'tl_css_style_selector';

    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * Returns the styles available for the given type (and element type, e.g.
     * the content element type), keyed by ID and sorted naturally.
     *
     * @return array<int, string>
     */
    public function findOptions(string $type, string|null $elementType = null): array
    {
        if (!\in_array($type, CssStyleSelectorModel::getAvailableTypes(), true)) {
            return [];
        }

        $restriction = CssStyleSelectorModel::TYPE_RESTRICTIONS[$type] ?? null;
        $columns = ['id', 'styleDesignation', ...($restriction ?? [])];

        $rows = $this->connection->fetchAllAssociative(
            \sprintf(
                'SELECT %s FROM %s WHERE %s = 0',
                implode(', ', array_map(fn (string $column): string => $this->connection->quoteIdentifier($column), $columns)),
                self::TABLE,
                $this->connection->quoteIdentifier(CssStyleSelectorModel::getDisableField($type)),
            ),
        );

        $options = [];

        foreach ($rows as $row) {
            if (null !== $restriction) {
                [$limitField, $allowedField] = $restriction;

                if ($row[$limitField] && !\in_array($elementType, StringUtil::deserialize($row[$allowedField], true), true)) {
                    continue;
                }
            }

            $options[(int) $row['id']] = (string) $row['styleDesignation'];
        }

        natcasesort($options);

        return $options;
    }

    /**
     * @param array<int|string> $ids
     *
     * @return list<string>
     */
    public function findCssClassesByIds(array $ids): array
    {
        $ids = array_values(array_unique(array_map(intval(...), $ids)));

        if (!$ids) {
            return [];
        }

        $classes = $this->connection->fetchFirstColumn(
            'SELECT cssClasses FROM '.self::TABLE.' WHERE id IN (?)',
            [$ids],
            [ArrayParameterType::INTEGER],
        );

        return self::splitClasses(implode(' ', $classes));
    }

    /**
     * Returns every CSS class managed by any style.
     *
     * @return list<string>
     */
    public function findAllCssClasses(): array
    {
        $classes = $this->connection->fetchFirstColumn('SELECT cssClasses FROM '.self::TABLE);

        return self::splitClasses(implode(' ', $classes));
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
