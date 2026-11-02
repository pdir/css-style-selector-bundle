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

use Contao\DataContainer;
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_css_style_group'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_css_style_category',
        'switchToEdit' => true,
        'enableVersioning' => true,
        'markAsCopy' => 'title',
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid,sorting' => 'index',
            ],
        ],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_PARENT,
            'fields' => ['sorting'],
            'headerFields' => ['title', 'description'],
            'panelLayout' => 'filter;search,limit',
        ],
        'label' => [
            'fields' => ['title', 'description'],
            'format' => '%s <span class="tl_gray">%s</span>',
        ],
        'operations' => ['edit', 'copy', 'cut', 'delete', 'show'],
    ],

    // Palettes
    'palettes' => [
        '__selector__' => ['inContent', 'inModule', 'inFormField', 'limitUserGroups'],
        'default' => '{title_legend},title,description,multiple;{styles_legend},styles;{publish_legend},inLayout,inPage,inArticle,inModule,inNews,inCalendarEvent,inForm,inFormField,inContent;{permissions_legend:hide},limitUserGroups',
    ],

    // Subpalettes
    'subpalettes' => [
        'inContent' => 'contentElements',
        'inModule' => 'modules',
        'inFormField' => 'formFields',
        'limitUserGroups' => 'userGroups',
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
        ],
        'pid' => [
            'foreignKey' => 'tl_css_style_category.title',
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'sorting' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'title' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'description' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'multiple' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        // One row per style: CSS class(es) and title. The row wizard is
        // available since Contao 5.5, older versions use the key value wizard
        // (both store the rows as "key" and "value").
        'styles' => [
            'exclude' => true,
            'inputType' => isset($GLOBALS['BE_FFL']['rowWizard']) ? 'rowWizard' : 'keyValueWizard',
            'fields' => [
                'key' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_css_style_group']['styleClass'],
                    'inputType' => 'text',
                    'eval' => ['decodeEntities' => true],
                ],
                'value' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_css_style_group']['styleTitle'],
                    'inputType' => 'text',
                ],
            ],
            'eval' => ['mandatory' => true, 'tl_class' => 'clr'],
            'sql' => 'blob NULL',
        ],
        'inLayout' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'clr'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'inPage' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'clr'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'inArticle' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'clr'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'inModule' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'modules' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'reference' => &$GLOBALS['TL_LANG']['FMD'],
            'eval' => ['multiple' => true, 'tl_class' => 'clr'],
            'sql' => 'blob NULL',
        ],
        'inNews' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'clr'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'inCalendarEvent' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'clr'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'inForm' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'clr'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'inFormField' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'formFields' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'reference' => &$GLOBALS['TL_LANG']['FFL'],
            'eval' => ['multiple' => true, 'tl_class' => 'clr'],
            'sql' => 'blob NULL',
        ],
        'inContent' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'contentElements' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'reference' => &$GLOBALS['TL_LANG']['CTE'],
            'eval' => ['multiple' => true, 'tl_class' => 'clr'],
            'sql' => 'blob NULL',
        ],
        'limitUserGroups' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'userGroups' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'foreignKey' => 'tl_user_group.name',
            'eval' => ['mandatory' => true, 'multiple' => true, 'tl_class' => 'clr'],
            'sql' => 'blob NULL',
            'relation' => ['type' => 'hasMany', 'load' => 'lazy'],
        ],
    ],
];
