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

namespace Craffft\CssStyleSelectorBundle\Models;

/**
 * Areas (types) the style groups can be published in.
 *
 * The styles used to be records of the table tl_css_style_selector; since
 * version 2.1 they are rows of their group (tl_css_style_group.styles), the
 * table is only read by the StyleRowsMigration.
 */
class CssStyleSelectorModel
{
    public const TYPE_ARTICLE = 'article';
    public const TYPE_CALENDAR_EVENTS = 'calendarEvent';
    public const TYPE_CONTENT = 'content';
    public const TYPE_FORM = 'form';
    public const TYPE_FORM_FIELD = 'formField';
    public const TYPE_LAYOUT = 'layout';
    public const TYPE_MODULE = 'module';
    public const TYPE_NEWS = 'news';
    public const TYPE_PAGE = 'page';

    /**
     * @deprecated Use TYPE_MODULE instead
     */
    public const TYPE_MODEL = self::TYPE_MODULE;

    /**
     * Types whose groups can additionally be limited to certain element types,
     * mapped to the group field holding the allowed types (empty = all types).
     */
    public const TYPE_RESTRICTIONS = [
        self::TYPE_CONTENT => 'contentElements',
        self::TYPE_MODULE => 'modules',
        self::TYPE_FORM_FIELD => 'formFields',
    ];

    /**
     * @return list<string>
     */
    public static function getAvailableTypes(): array
    {
        return [
            self::TYPE_ARTICLE,
            self::TYPE_CALENDAR_EVENTS,
            self::TYPE_CONTENT,
            self::TYPE_FORM,
            self::TYPE_FORM_FIELD,
            self::TYPE_LAYOUT,
            self::TYPE_MODULE,
            self::TYPE_NEWS,
            self::TYPE_PAGE,
        ];
    }

    /**
     * Field of the group that publishes it in the given area.
     */
    public static function getEnableField(string $type): string
    {
        return 'in'.ucfirst($type);
    }

    /**
     * Field of the former style records that disabled them in the given area.
     */
    public static function getDisableField(string $type): string
    {
        return 'disableIn'.ucfirst($type);
    }
}
