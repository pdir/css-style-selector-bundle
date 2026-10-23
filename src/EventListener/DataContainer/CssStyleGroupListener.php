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
use Doctrine\DBAL\Connection;

class CssStyleGroupListener
{
    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * Adds new and duplicated groups at the end of the list.
     */
    #[AsCallback(table: 'tl_css_style_group', target: 'config.oncreate')]
    public function onCreate(string $table, int|string $insertId): void
    {
        $this->appendGroup((int) $insertId);
    }

    #[AsCallback(table: 'tl_css_style_group', target: 'config.oncopy')]
    public function onCopy(int|string $insertId): void
    {
        $this->appendGroup((int) $insertId);
    }

    private function appendGroup(int $id): void
    {
        $sorting = (int) $this->connection->fetchOne('SELECT MAX(sorting) FROM tl_css_style_group WHERE id != ?', [$id]);

        $this->connection->update('tl_css_style_group', ['sorting' => $sorting + 128], ['id' => $id]);
    }
}
