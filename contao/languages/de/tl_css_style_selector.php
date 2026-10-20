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
$GLOBALS['TL_LANG']['tl_css_style_selector']['style_legend'] = 'Stil';
$GLOBALS['TL_LANG']['tl_css_style_selector']['css_legend'] = 'CSS';
$GLOBALS['TL_LANG']['tl_css_style_selector']['permissions_legend'] = 'Berechtigungen';
$GLOBALS['TL_LANG']['tl_css_style_selector']['restriction_legend'] = 'Einschränkungen';

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_css_style_selector']['styleDesignation'] = ['Stil Bezeichnung', 'Bitte geben Sie eine Stil Bezeichnung ein.'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['description'] = ['Beschreibung', 'Ein kurzer Hinweis für Redakteure, wofür der Stil gedacht ist. Er wird in der Stil-Auswahl hinter der Bezeichnung angezeigt.'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['cssClasses'] = ['CSS-Klassen', 'Bitte geben Sie eine oder mehrere CSS-Klassen mit jeweils einem Leerzeichen getrennt ein.'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['disableInArticle'] = ['In Artikeln deaktivieren', 'Bitte wählen Sie dieses Feld aus, um den Stil in den Artikeln zu deaktivieren.'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['disableInCalendarEvent'] = ['In Kalender Terminen deaktivieren', 'Bitte wählen Sie dieses Feld aus, um den Stil in den Kalender Terminen zu deaktivieren.'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['disableInContent'] = ['In Content Elementen deaktivieren', 'Bitte wählen Sie dieses Feld aus, um den Stil in den Content Elementen zu deaktivieren.'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['disableInForm'] = ['In Formularen deaktivieren', 'Bitte wählen Sie dieses Feld aus, um den Stil in den Formularen zu deaktivieren.'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['disableInFormField'] = ['In Formularfeldern deaktivieren', 'Bitte wählen Sie dieses Feld aus, um den Stil in den Formularfeldern zu deaktivieren.'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['disableInLayout'] = ['In Layout deaktivieren', 'Bitte wählen Sie dieses Feld aus, um den Stil in den Layouts zu deaktivieren.'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['disableInModule'] = ['In Modulen deaktivieren', 'Bitte wählen Sie dieses Feld aus, um den Stil in den Modulen zu deaktivieren.'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['disableInNews'] = ['In Neuigkeiten deaktivieren', 'Bitte wählen Sie dieses Feld aus, um den Stil in den Neuigkeiten zu deaktivieren.'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['disableInPage'] = ['In Seiten deaktivieren', 'Bitte wählen Sie dieses Feld aus, um den Stil in den Seiten zu deaktivieren.'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['limitContentElements'] = ['Auf bestimmte Inhaltselemente beschränken', 'Den Stil nur für ausgewählte Inhaltselement-Typen anbieten.'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['contentElements'] = ['Inhaltselemente', 'Bitte wählen Sie die Inhaltselement-Typen aus, für die der Stil angeboten werden soll.'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['limitModules'] = ['Auf bestimmte Module beschränken', 'Den Stil nur für ausgewählte Modul-Typen anbieten.'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['modules'] = ['Module', 'Bitte wählen Sie die Modul-Typen aus, für die der Stil angeboten werden soll.'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['limitFormFields'] = ['Auf bestimmte Formularfelder beschränken', 'Den Stil nur für ausgewählte Formularfeld-Typen anbieten.'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['formFields'] = ['Formularfelder', 'Bitte wählen Sie die Formularfeld-Typen aus, für die der Stil angeboten werden soll.'];

/**
 * Labels
 */
$GLOBALS['TL_LANG']['tl_css_style_selector']['articleEnabled'] = ['Artikel', 'In Artikel aktiviert'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['calendarEventEnabled'] = ['Termin', 'In Kalender Termin aktiviert'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['contentEnabled'] = ['CE', 'In Content Element aktiviert'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['formEnabled'] = ['Formular', 'In Formular aktiviert'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['formFieldEnabled'] = ['Formular Feld', 'In Formular Feld aktiviert'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['layoutEnabled'] = ['Layout', 'In Layout aktiviert'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['moduleEnabled'] = ['Modul', 'In Modul aktiviert'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['newsEnabled'] = ['Neuigkeiten', 'In Neuigkeiten aktiviert'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['pageEnabled'] = ['Seite', 'In Seite aktiviert'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['limited'] = 'eingeschränkt';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_css_style_selector']['new'] = ['Neuer CSS-Stil', 'Neuen CSS-Stil erstellen'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['edit'] = ['CSS-Stil bearbeiten', 'CSS-Stil ID %s bearbeiten'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['copy'] = ['CSS-Stil duplizieren', 'CSS-Stil ID %s duplizieren'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['delete'] = ['CSS-Stil löschen', 'CSS-Stil ID %s löschen'];
$GLOBALS['TL_LANG']['tl_css_style_selector']['show'] = ['CSS-Stil anzeigen', 'CSS-Stil ID %s anzeigen'];
