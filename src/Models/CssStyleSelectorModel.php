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

use Contao\Model;

/**
 * @property int         $id
 * @property int         $tstamp
 * @property string      $styleDesignation
 * @property string      $cssClasses
 * @property bool        $disableInArticle
 * @property bool        $disableInCalendarEvent
 * @property bool        $disableInContent
 * @property bool        $disableInForm
 * @property bool        $disableInFormField
 * @property bool        $disableInLayout
 * @property bool        $disableInModule
 * @property bool        $disableInNews
 * @property bool        $disableInPage
 * @property bool        $limitContentElements
 * @property string|null $contentElements
 * @property bool        $limitModules
 * @property string|null $modules
 * @property bool        $limitFormFields
 * @property string|null $formFields
 */
class CssStyleSelectorModel extends Model
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
     * Types whose styles can additionally be limited to certain element types,
     * mapped to the "limit" checkbox and the field holding the allowed types.
     */
    public const TYPE_RESTRICTIONS = [
        self::TYPE_CONTENT => ['limitContentElements', 'contentElements'],
        self::TYPE_MODULE => ['limitModules', 'modules'],
        self::TYPE_FORM_FIELD => ['limitFormFields', 'formFields'],
    ];

    protected static $strTable = 'tl_css_style_selector';

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

    public static function getDisableField(string $type): string
    {
        return 'disableIn'.ucfirst($type);
    }
}
