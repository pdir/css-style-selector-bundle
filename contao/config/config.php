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

use Craffft\CssStyleSelectorBundle\Models\CssStyleCategoryModel;
use Craffft\CssStyleSelectorBundle\Models\CssStyleGroupModel;
use Craffft\CssStyleSelectorBundle\Widget\CssStyleSelectorWidget;

$GLOBALS['BE_MOD']['design']['cssStyleSelector'] = [
    'tables' => ['tl_css_style_category', 'tl_css_style_group'],
];

$GLOBALS['BE_FFL']['cssStyleSelector'] = CssStyleSelectorWidget::class;

$GLOBALS['TL_MODELS']['tl_css_style_category'] = CssStyleCategoryModel::class;
$GLOBALS['TL_MODELS']['tl_css_style_group'] = CssStyleGroupModel::class;
