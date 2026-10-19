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

$GLOBALS['TL_DCA']['tl_css_style_selector'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'markAsCopy' => 'styleDesignation',
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTED,
            'fields' => ['styleDesignation'],
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'panelLayout' => 'filter;search,limit',
        ],
        'label' => [
            'fields' => ['styleDesignation', 'cssClasses', 'articleEnabled', 'calendarEventEnabled', 'contentEnabled', 'formEnabled', 'formFieldEnabled', 'layoutEnabled', 'moduleEnabled', 'newsEnabled', 'pageEnabled'],
            'showColumns' => true,
        ],
        'operations' => ['edit', 'copy', 'delete', 'show'],
    ],

    // Palettes
    'palettes' => [
        '__selector__' => ['limitContentElements', 'limitModules', 'limitFormFields'],
        'default' => '{style_legend},styleDesignation;{css_legend},cssClasses;{permissions_legend},disableInArticle,disableInContent,disableInCalendarEvent,disableInForm,disableInFormField,disableInLayout,disableInModule,disableInNews,disableInPage;{restriction_legend},limitContentElements,limitModules,limitFormFields',
    ],

    // Subpalettes
    'subpalettes' => [
        'limitContentElements' => 'contentElements',
        'limitModules' => 'modules',
        'limitFormFields' => 'formFields',
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'styleDesignation' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'cssClasses' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'disableInArticle' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'disableInCalendarEvent' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'disableInContent' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'disableInForm' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'disableInFormField' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'disableInLayout' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'disableInModule' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'disableInNews' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'disableInPage' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'limitContentElements' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'contentElements' => [
            'exclude' => true,
            'inputType' => 'select',
            'reference' => &$GLOBALS['TL_LANG']['CTE'],
            'eval' => ['mandatory' => true, 'multiple' => true, 'chosen' => true, 'tl_class' => 'clr'],
            'sql' => 'blob NULL',
        ],
        'limitModules' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'modules' => [
            'exclude' => true,
            'inputType' => 'select',
            'reference' => &$GLOBALS['TL_LANG']['FMD'],
            'eval' => ['mandatory' => true, 'multiple' => true, 'chosen' => true, 'tl_class' => 'clr'],
            'sql' => 'blob NULL',
        ],
        'limitFormFields' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'formFields' => [
            'exclude' => true,
            'inputType' => 'select',
            'reference' => &$GLOBALS['TL_LANG']['FFL'],
            'eval' => ['mandatory' => true, 'multiple' => true, 'chosen' => true, 'tl_class' => 'clr'],
            'sql' => 'blob NULL',
        ],
        // Virtual fields for the list view columns
        'articleEnabled' => [],
        'calendarEventEnabled' => [],
        'contentEnabled' => [],
        'formEnabled' => [],
        'formFieldEnabled' => [],
        'layoutEnabled' => [],
        'moduleEnabled' => [],
        'newsEnabled' => [],
        'pageEnabled' => [],
    ],
];
