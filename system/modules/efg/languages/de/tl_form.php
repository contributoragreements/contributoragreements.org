<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   Efg
 * @author    Thomas Kuhn <mail@th-kuhn.de>
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 * @copyright Thomas Kuhn 2007-2014
 */


$GLOBALS['TL_LANG']['tl_form']['storeFormdata'] = array('Daten im Modul "Formular-Daten" speichern', 'Wenn Sie diese Option wählen, werden die Daten im Backend-Modul "Formular-Daten" gespeichert.<br>Hinweis: Nach Ergänzung oder Änderung von Formular-Feldern bitte das Formular erneut speichern.');
$GLOBALS['TL_LANG']['tl_form']['efgAliasField'] = array('Formularfeld für Alias', 'Wählen Sie hier das Formularfeld, dessen Inhalt zur Erzeugung des Formulardaten-Alias verwendet wird.');
$GLOBALS['TL_LANG']['tl_form']['efgStoreValues'] = array('Options-Werte speichern', 'Wenn Sie diese Option wählen, wird bei Feldern des Typs Select-Menü, Radio-Button-Menü und Checkbox-Menü der ausgewählte "Wert" anstelle der "Bezeichnung" gespeichert. Die Option hat keine Auswirkung bei Feldern des Typs Select-Menü (DB), Radio-Button-Menü (DB) und Checkbox-Menu (DB)');
$GLOBALS['TL_LANG']['tl_form']['useFormValues'] = array('Feldwerte exportieren', 'Wenn Sie diese Option wählen, werden beim Export der Formulardaten die ausgewählten Werte von Formularfeldern anstelle der ausgewählten Bezeichnungen exportiert. Dies trifft für alle Radio-Buttons, Checkboxen und Selects zu.');
$GLOBALS['TL_LANG']['tl_form']['useFieldNames'] = array('Feldnamen exportieren', 'Wenn Sie diese Option wählen, werden beim Export der Formulardaten die Feldnamen anstelle der Feldbezeichnungen exportiert.');
$GLOBALS['TL_LANG']['tl_form']['sendConfirmationMail'] = array('Bestätigung per E-Mail versenden', 'Wenn Sie diese Option wählen, wird eine Bestätigung per E-Mail an den Absender des Formulars versendet.');
$GLOBALS['TL_LANG']['tl_form']['confirmationMailRecipientField'] = array('Formularfeld mit E-Mail-Adresse des Empfängers', 'Wählen Sie hier das Formularfeld, in dem der Absender seine E-Mail-Adresse angibt oder ein Formularfeld, das die Empfänger-Adresse als Wert enthält.');
$GLOBALS['TL_LANG']['tl_form']['confirmationMailRecipient'] = array('Empfänger', 'Kommagetrennte Liste von E-Mail-Adressen, falls die E-Mail-Adresse nicht per Formularfeld definiert wird, oder die E-Mail an weitere Empfänger gesendet werden soll.');
$GLOBALS['TL_LANG']['tl_form']['confirmationMailSender'] = array('Absender', 'Bitte geben Sie hier die Absender-E-Mail-Adresse ein.');
$GLOBALS['TL_LANG']['tl_form']['confirmationMailReplyto'] = array('Antwort an (Reply-To)', 'Kommagetrennte Liste von E-Mail-Adressen, falls Antworten auf die E-Mail nicht an den Absender gesendet werden sollen.');
$GLOBALS['TL_LANG']['tl_form']['confirmationMailSubject'] = array('Betreff', 'Bitte geben Sie eine Betreffzeile für die Bestätigungs-E-Mail ein. Wenn Sie keine Betreffzeile erfassen, steigt die Wahrscheinlichkeit, dass die E-Mail als SPAM identifiziert wird.');
$GLOBALS['TL_LANG']['tl_form']['confirmationMailText'] = array('Text der Bestätigungs-E-Mail', 'Bitte geben Sie hier den Text der Bestätigungs-E-Mail ein. Neben den allgemeinen Insert-Tags werden Tags der Form form::FORMULARFELDNAME unterstützt.');
$GLOBALS['TL_LANG']['tl_form']['confirmationMailTemplate'] = array('HTML-Vorlage für die Bestätigungs-E-Mail', 'Wenn die Bestätigungs-E-Mail als HTML-E-Mail versendet werden soll, wählen Sie hier die HTML-Vorlage aus dem Dateisystem.');
$GLOBALS['TL_LANG']['tl_form']['addConfirmationMailAttachments'] = array('Dateien an Bestätigungs-E-Mail anhängen', 'Der Bestätigungs-E-Mail können hier Dateien zum Versand angehängt werden.');
$GLOBALS['TL_LANG']['tl_form']['confirmationMailAttachments'] = array('Dateianhänge', 'Bitte wählen Sie hier die anzuhängenden Dateien aus.');
$GLOBALS['TL_LANG']['tl_form']['addFormattedMailAttachments'] = array('Dateien an E-Mail anhängen', 'Der E-Mail können hier Dateien zum Versand angehängt werden.');
$GLOBALS['TL_LANG']['tl_form']['formattedMailAttachments'] = array('Dateianhänge', 'Bitte wählen Sie hier die anzuhängenden Dateien aus.');
$GLOBALS['TL_LANG']['tl_form']['sendFormattedMail'] = array('Per E-Mail versenden (formatierter Text / HTML)', 'Der Inhalt der Nachricht kann frei angegeben werden, unter Verwendung von Insert-Tags. Die Nachricht kann auch als HTML-E-Mail versendet werden.');
$GLOBALS['TL_LANG']['tl_form']['formattedMailText'] = array('Text der E-Mail', 'Bitte geben Sie hier den Text der E-Mail ein. Neben den allgemeinen Insert-Tags werden Tags der Form form::FORMULARFELDNAME unterstützt.');
$GLOBALS['TL_LANG']['tl_form']['formattedMailTemplate'] = array('HTML-Vorlage für die E-Mail', 'Wenn die E-Mail als HTML-E-Mail versendet werden soll, wählen Sie hier die HTML-Vorlage aus dem Dateisystem.');
$GLOBALS['TL_LANG']['tl_form']['efgStoreFormdata_legend'] = '(EFG) Formular-Daten speichern';
$GLOBALS['TL_LANG']['tl_form']['efgSendFormattedMail_legend'] = '(EFG) Per E-Mail versenden';
$GLOBALS['TL_LANG']['tl_form']['efgSendConfirmationMail_legend'] = '(EFG) Bestätigung per E-Mail versenden';
