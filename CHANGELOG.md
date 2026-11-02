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
