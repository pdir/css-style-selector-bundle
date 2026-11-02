# Changelog

[//]: <> (
Types of changes
    Added for new Addeds.
    Changed for changes in existing functionality.
    Deprecated for soon-to-be removed Addeds.
    Removed for now removed Addeds.
    Fixed for any bug fixes.
    Security in case of vulnerabilities.
)


## [2.1.0](https://github.com/pdir/css-style-selector-bundle/tree/2.1.0) – 2026-11-03

- [Added] Style categories and groups: each group is shown as a select menu (single or multiple selection), the select menus are shown side by side below a heading per category
- [Added] Groups are sorted by drag and drop within their category and can be moved into another category; categories have a sorting value
- [Added] Optional description per category and group
- [Added] Limit style groups to certain back end user groups; styles selected by other users are kept
- [Added] The selected styles are shown in the list views (content elements, modules, pages, …)
- [Changed] The styles are rows of their group (CSS classes and title) instead of records of their own, so all styles of a group are edited on one page
- [Changed] Areas and element types are set per group ("Publication") instead of per style; without selected element types, a group applies to all types
- [Changed] The selection of a record stores the CSS classes of the selected styles instead of their IDs, so it survives copying, exporting and importing records
- [Changed] The CSS class field only shows the manually entered classes; the classes of the selected styles are still saved in the field, so templates and the front end are unchanged
- [Changed] Run `contao:migrate` after updating: existing styles become rows of their group (styles without a group go into the group "Styles" with multiple selection, groups without a category into the category "General"), the areas and element types of the styles are applied to their group and the selection of the records is converted; the table `tl_css_style_selector` can be dropped afterwards
- [Changed] The style selection is shown in its own "CSS style" legend above the expert legend (the legend of the CSS class field) instead of inside the collapsed expert legend
- [Changed] The style selection is hidden if no styles are available for the edited record
- [Changed] Styles that are no longer available for a record (e.g. after changing the element type) are deselected on save
- [Fixed] Saving a record without the style field (e.g. from a front end editor) no longer removes styles and their CSS classes
- [Fixed] CSS classes of styles that are not selected are no longer removed when saving

## [2.0.0](https://github.com/pdir/css-style-selector-bundle/tree/2.0.0) – 2026-11-02

- [Added] Support for Contao 6
- [Added] Limit styles to certain content element, module and form field types
- [Changed] Require PHP 8.1 and Contao 5.3 or higher
- [Changed] Use Doctrine DBAL, services and `#[AsCallback]` attributes instead of `Contao\Database`, `Contao\Input` and DCA closures
- [Changed] The CSS classes are now applied in an `onsubmit_callback`, so they also work in "edit multiple" mode and no longer manipulate the POST data
- [Changed] The field is added to every palette containing the CSS class field on load, which covers palettes of other extensions as well
- [Changed] Checkbox columns use the `boolean` type, run the database migration after updating
- [Fixed] Deselected styles were not removed from form fields and calendar events
- [Fixed] The CSS class input used a non-existent `rgxp`; the value is now normalized instead
- [Removed] Support for Contao 4.13 and the `CssStyleSelectorUtil` class as well as the static finder methods of `CssStyleSelectorModel` (use `CssStyleRepository`)

## [1.7.1](https://github.com/pdir/css-style-selector-bundle/tree/1.7.1) – 2022-12-08

- [Fixed] Error when saving a form field in Contao 5 ([#63](https://github.com/contao-themes-net/nature-theme-bundle/issues/63))
- [Changed] Sort styles naturally ([🤗 fritzmg](https://github.com/pdir/css-style-selector-bundle/commit/5201a90d01c26a2bfca9cb876f1ddad9580ad3e9))

## [1.7.0](https://github.com/pdir/css-style-selector-bundle/tree/1.7.0) – 2022-09-02

- [Changed] Allow Contao 5
