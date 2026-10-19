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

use Craffft\CssStyleSelectorBundle\Models\CssStyleSelectorModel;

$GLOBALS['BE_MOD']['design']['cssStyleSelector'] = [
    'tables' => ['tl_css_style_selector'],
];

$GLOBALS['TL_MODELS']['tl_css_style_selector'] = CssStyleSelectorModel::class;
