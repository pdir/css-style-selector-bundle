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

// The field is added to the palettes, gets its options and is applied to the
// CSS class field by Craffft\CssStyleSelectorBundle\EventListener\DataContainer\CssStyleSelectorFieldListener
$GLOBALS['TL_DCA']['tl_form_field']['fields']['cssStyleSelector'] = [
    'label' => &$GLOBALS['TL_LANG']['MSC']['cssStyleSelector'],
    'exclude' => true,
    'inputType' => 'cssStyleSelector',
    'eval' => ['multiple' => true, 'isAssociative' => true, 'tl_class' => 'clr'],
    'sql' => 'blob NULL',
];
