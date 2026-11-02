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
 * @property int         $pid
 * @property int         $tstamp
 * @property int         $sorting
 * @property string      $title
 * @property string      $description
 * @property bool        $multiple
 * @property string|null $styles
 * @property bool        $inArticle
 * @property bool        $inCalendarEvent
 * @property bool        $inContent
 * @property bool        $inForm
 * @property bool        $inFormField
 * @property bool        $inLayout
 * @property bool        $inModule
 * @property bool        $inNews
 * @property bool        $inPage
 * @property string|null $contentElements
 * @property string|null $modules
 * @property string|null $formFields
 * @property bool        $limitUserGroups
 * @property string|null $userGroups
 */
class CssStyleGroupModel extends Model
{
    protected static $strTable = 'tl_css_style_group';
}
