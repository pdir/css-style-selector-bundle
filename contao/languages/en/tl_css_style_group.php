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
$GLOBALS['TL_LANG']['tl_css_style_group']['styles_legend'] = 'Styles';
$GLOBALS['TL_LANG']['tl_css_style_group']['publish_legend'] = 'Publication';
$GLOBALS['TL_LANG']['tl_css_style_group']['permissions_legend'] = 'Permissions';

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_css_style_group']['title'] = ['Title', 'Please enter the title of the group, e.g. "Background" or "Button color".'];
$GLOBALS['TL_LANG']['tl_css_style_group']['description'] = ['Description', 'An optional hint for editors, shown below the select menu.'];
$GLOBALS['TL_LANG']['tl_css_style_group']['multiple'] = ['Multiple selection', 'Allow several styles of this group at the same time. Without this option, only one style per group can be selected.'];
$GLOBALS['TL_LANG']['tl_css_style_group']['styles'] = ['CSS classes', 'One row per style: one or more CSS classes (separated by spaces) and the title shown to editors.'];
$GLOBALS['TL_LANG']['tl_css_style_group']['styleClass'] = ['CSS class'];
$GLOBALS['TL_LANG']['tl_css_style_group']['styleTitle'] = ['Title'];
$GLOBALS['TL_LANG']['tl_css_style_group']['inLayout'] = ['Layouts', 'Offer the group in page layouts.'];
$GLOBALS['TL_LANG']['tl_css_style_group']['inPage'] = ['Pages', 'Offer the group in pages.'];
$GLOBALS['TL_LANG']['tl_css_style_group']['inArticle'] = ['Articles', 'Offer the group in articles.'];
$GLOBALS['TL_LANG']['tl_css_style_group']['inModule'] = ['Modules', 'Offer the group in front end modules.'];
$GLOBALS['TL_LANG']['tl_css_style_group']['modules'] = ['Module types', 'Offer the group for these module types only. Without a selection, it applies to all modules.'];
$GLOBALS['TL_LANG']['tl_css_style_group']['inNews'] = ['News', 'Offer the group in news.'];
$GLOBALS['TL_LANG']['tl_css_style_group']['inCalendarEvent'] = ['Events', 'Offer the group in events.'];
$GLOBALS['TL_LANG']['tl_css_style_group']['inForm'] = ['Forms', 'Offer the group in forms.'];
$GLOBALS['TL_LANG']['tl_css_style_group']['inFormField'] = ['Form fields', 'Offer the group in form fields.'];
$GLOBALS['TL_LANG']['tl_css_style_group']['formFields'] = ['Form field types', 'Offer the group for these form field types only. Without a selection, it applies to all form fields.'];
$GLOBALS['TL_LANG']['tl_css_style_group']['inContent'] = ['Content elements', 'Offer the group in content elements.'];
$GLOBALS['TL_LANG']['tl_css_style_group']['contentElements'] = ['Element types', 'Offer the group for these content elements only. Without a selection, it applies to all content elements.'];
$GLOBALS['TL_LANG']['tl_css_style_group']['limitUserGroups'] = ['Limit to user groups', 'Only offer the styles of this group to members of certain user groups. Administrators always see all styles.'];
$GLOBALS['TL_LANG']['tl_css_style_group']['userGroups'] = ['User groups', 'Please choose the user groups that may use the styles of this group.'];

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_css_style_group']['new'] = ['New group', 'Create a new style group'];
$GLOBALS['TL_LANG']['tl_css_style_group']['edit'] = ['Edit group', 'Edit style group ID %s'];
$GLOBALS['TL_LANG']['tl_css_style_group']['copy'] = ['Duplicate group', 'Duplicate style group ID %s'];
$GLOBALS['TL_LANG']['tl_css_style_group']['cut'] = ['Move group', 'Move style group ID %s'];
$GLOBALS['TL_LANG']['tl_css_style_group']['delete'] = ['Delete group', 'Delete style group ID %s'];
$GLOBALS['TL_LANG']['tl_css_style_group']['show'] = ['Show group', 'Show style group ID %s'];
