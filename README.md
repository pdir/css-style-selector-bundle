Contao CSS Style Selector Bundle
================================

This Contao extension lets you define CSS styles (a name plus one or more CSS
classes) once in the back end and select them in articles, content elements,
modules, forms, form fields, page layouts, pages, news and events. The classes
of the selected styles are written into the CSS class field of the record.

### CSS styles can be defined here

![css selector navigation](https://raw.githubusercontent.com/pdir/css-style-selector-bundle/master/docs/css-style-selector-nav.png)

### The predefined styles can be selected here

![css selector navigation](https://raw.githubusercontent.com/pdir/css-style-selector-bundle/master/docs/css-style-selector-preview.png)


Requirements
------------

- PHP 8.1 or higher (Contao 6 requires PHP 8.4)
- Contao 5.3 or higher, including Contao 6


Installation
------------

Install the bundle via the Contao Manager or Composer and update the database
afterwards:

```bash
composer require pdir/css-style-selector-bundle
vendor/bin/contao-console contao:migrate
```


Usage
-----

Go to *Layout → CSS styles* and create your styles.

- **Description:** an optional short hint for editors, shown next to the name
  in the style selection.
- **Permissions:** disable a style for whole areas, e.g. "Disable in pages".
- **Restrictions:** limit a style to certain content element, module or form
  field types (e.g. only for `text` and `image` elements). If a restriction is
  active, the style is only offered for the selected types.

Editors find the style selection in its own "CSS style" section near the top
of the form. It is hidden if no styles are available for the record.

When a record is saved, the classes of all styles are removed from its CSS
class field and the classes of the selected styles are added again. Classes
you entered manually stay untouched, unless they are also used by a style.
