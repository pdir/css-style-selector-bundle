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

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_css_style_group']['title_legend'] = 'Group';
$GLOBALS['TL_LANG']['tl_css_style_group']['permissions_legend'] = 'Permissions';

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_css_style_group']['title'] = ['Title', 'Please enter the title of the group, e.g. "Background" or "Spacing".'];
$GLOBALS['TL_LANG']['tl_css_style_group']['multiple'] = ['Multiple selection', 'Allow several styles of this group at the same time. Without this option, only one style per group can be selected.'];
$GLOBALS['TL_LANG']['tl_css_style_group']['limitUserGroups'] = ['Limit to user groups', 'Only offer the styles of this group to members of certain user groups. Administrators always see all styles.'];
$GLOBALS['TL_LANG']['tl_css_style_group']['userGroups'] = ['User groups', 'Please choose the user groups that may use the styles of this group.'];

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_css_style_group']['new'] = ['New group', 'Create a new style group'];
$GLOBALS['TL_LANG']['tl_css_style_group']['edit'] = ['Edit group', 'Edit style group ID %s'];
$GLOBALS['TL_LANG']['tl_css_style_group']['children'] = ['Edit styles', 'Edit the styles of group ID %s'];
$GLOBALS['TL_LANG']['tl_css_style_group']['copy'] = ['Duplicate group', 'Duplicate style group ID %s'];
$GLOBALS['TL_LANG']['tl_css_style_group']['delete'] = ['Delete group', 'Delete style group ID %s'];
$GLOBALS['TL_LANG']['tl_css_style_group']['show'] = ['Show group', 'Show style group ID %s'];
