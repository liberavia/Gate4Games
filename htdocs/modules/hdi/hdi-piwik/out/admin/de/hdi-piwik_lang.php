<?php
/**
 * hdi-piwik: OXID module to include Piwik tracking code.
 * Copyright (C) 2012-2014 HEINER DIRECT GmbH & Co. KG
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      HEINER DIRECT GmbH & Co. KG <oxid@heiner-direct.com>
 * @author      Klaus Weidenbach (HEINER DIRECT GmbH & Co. KG)
 * @author      Rafael Dabrowski (HEINER DIRECT GmbH & Co. KG)
 * @link        http://www.heiner-direct.com
 *
 * @copyright   HEINER DIRECT GmbH & Co. KG 2012-2014
 * @license     GPLv3
 *
 * @package     HDI
 * @subpackage  hdi-piwik
 */

$sLangName  = "Deutsch";
// -------------------------------
// RESOURCE IDENTITFIER = STRING
// -------------------------------
$aLang = array(
'charset' => 'UTF-8',
'SHOP_MODULE_GROUP_hdi-piwik_Main' => 'Piwik Konfiguration',
'SHOP_MODULE_hdi-piwik_sUrl' => '<h3>Piwik Server URL</h3><em>Hier die URL zu Ihrem Piwik Server eintragen.</em><br><b>Format:</b><i> ohne http:// bzw. https:// und ohne abschliessendem /</i><br><b>Bsp:</b> <i>piwik.domain.tld</i>',
'SHOP_MODULE_hdi-piwik_iPageid' => '<h3>Piwik Seiten-ID</h3><em>Tragen Sie hier die Piwik Seiten-ID ein.</em>',
'SHOP_MODULE_hdi-piwik_iNewsgoalid' => '<h3>Newsletter Ziel-ID</h3><em>Wenn Sie in Piwik ein Ziel für eine Newsletterbestellung definiert haben, tragen Sie hier die Ziel-ID ein. Sollte dies nicht benötigt werden tragen Sie eine 0 ein.</em>',
'SHOP_MODULE_hdi-piwik_iMaxCustVar' => '<h3>Anzahl benutzerdefinierter Variablen</h3><em>Piwik erlaubt standardmässig nur 5 benutzerdefinierte Variablen pro Scope. Dies kann in der Piwik Konfiguration erweitert werden. Ändern sie diesen Wert nur wenn sie sich sicher sind und diese Anleitung ausgeführt haben <a href="http://piwik.org/faq/how-to/faq_17931/" target="_blank">http://piwik.org/faq/how-to/faq_17931/</a>.</em>',
'SHOP_MODULE_hdi-piwik_iCustindexNewsletter' => '<h3>Index der benutzerdefinierte Variable für das Newsletter Anmeldungstracking</h3><em>Piwik page-scope Index der <a href="http://piwik.org/docs/custom-variables/" target="_blank" title="Was ist eine benutzerdefinierte Variable">benutzerdefinierten Variable</a> zwischen 1 und 5 wo das Newsletter Anmeldungstracking gespeichert werden soll. (0 zum Deaktivieren)</em>',
'SHOP_MODULE_hdi-piwik_iCustindexPayment' => '<h3>Index der benutzerdefinierten Variable für Zahlungsart-Tracking</h3><em>Piwik visit-scope Index der <a href="http://piwik.org/docs/custom-variables/" target="_blank" title="Was ist eine benutzerdefinierte Variable">benutzerdefinierten Variable</a> zwischen 1 und 5 wo die Zahlungsart gespeichert werden soll. (0 zum Deaktivieren)</em>',
'SHOP_MODULE_hdi-piwik_blFirstReferrerConv' => '<h3>Ersten Referrer einer Conversion zuordnen</h3><em>Anhaken um den ersten Referrer einer Conversion zuzuordnen. Standardmässig wird der zuletzte verwendete Referrer einer Conversion zugeordnet.</em>',
'SHOP_MODULE_hdi-piwik_blEnableJSErrorTrackin' => '<h3>Unbehandelte Frontend JavaScript-Fehler tracken</h3><em>Diese Option trackt unbehandelte JavaScript-Fehler im Shop-Frontend in Piwik\'s <a href="http://piwik.org/docs/event-tracking/" target="_blank" title="Event Tracking Dokumentation">Event Tracking</a>.<br>Achtung, dieses Feature erfordert Piwik >= 2.2.0</em>',
// Custom variable labels
'SHOP_MODULE_GROUP_hdi-piwik_CustomVars' => 'Bezeichnungen für benutzerdefinierte Variablen',
'SHOP_MODULE_hdi-piwik_CUSTOM_NEWSLETTER' => 'Newsletter',
'SHOP_MODULE_hdi-piwik_sCustomvarNewsletter' => 'Text der in Piwik bei der benutzerdefinierten Varible für das Newsletter Anmeldungstracking erscheint.',
'SHOP_MODULE_hdi-piwik_CUSTOM_PAYMENT' => 'Zahlungsart',
'SHOP_MODULE_hdi-piwik_sCustomvarPayment' => 'Text der in Piwik bei der benutzerdefinierten Varible für das Zahlungsarttracking erscheint.',
'SHOP_MODULE_hdi-piwik_NEWSLETTER_ACCOUNT' => 'Konto Newslettereinstellungen gesehen',
'SHOP_MODULE_hdi-piwik_sNewsletterAccount' => 'Text der in Piwik angezeigt wird wenn ein Besucher in den Kontoeinstellungen auf der Newslettereinstellungsseite war.',
'SHOP_MODULE_hdi-piwik_NEWSLETTER_ACCOUNT_ON' => 'Konto Newsletter aktiviert',
'SHOP_MODULE_hdi-piwik_sNewsletterAccountOn' => 'Text der in Piwik angezeigt wird wenn ein Besucher in den Kontoeinstellungen den Newsletter aktiviert hat.',
'SHOP_MODULE_hdi-piwik_NEWSLETTER_ACCOUNT_OFF' => 'Konto Newsletter deaktiviert',
'SHOP_MODULE_hdi-piwik_sNewsletterAccountOff' => 'Text der in Piwik angezeigt wird wenn ein Besucher in den Kontoeinstellungen den Newsletter deaktiviert hat.',
'SHOP_MODULE_hdi-piwik_NEWSLETTER_VIEWED' => 'Angeschaut',
'SHOP_MODULE_hdi-piwik_sNewsletterViewed' => 'Text der in Piwik angezeigt wird wenn ein Besucher auf der Newsletter Anmeldeseite war.',
'SHOP_MODULE_hdi-piwik_NEWSLETTER_ORDERED' => 'Angemeldet',
'SHOP_MODULE_hdi-piwik_sNewsletterOrdered' => 'Text der in Piwik angezeigt wird wenn ein Besucher das Newsletter Anmeldeformular abgeschickt hat.',
'SHOP_MODULE_hdi-piwik_NEWSLETTER_ACTIVATED' => 'Best&auml;tigt',
'SHOP_MODULE_hdi-piwik_sNewsletterActivated' => 'Text der in Piwik angezeigt wird wenn ein Besucher den Newsletter Double-Opt-In bestätigt hat.',
'SHOP_MODULE_hdi-piwik_NEWSLETTER_CANCELED' => 'Gel&ouml;scht',
'SHOP_MODULE_hdi-piwik_sNewsletterCanceled' => 'Text der in Piwik angezeigt wird wenn ein Besucher den Newsletter abbestellt hat.',
// additional mapping
'SHOP_MODULE_GROUP_hdi-piwik_Params' => 'Zusätzliches Parametermapping',
'SHOP_MODULE_hdi-piwik_aParamMapVisit' => '<h3>Parameter Mapping für visit-scope benutzerdefinierte Variablen</h3><em>Hierüber können pro Zeile ein URL Parameter in Piwiks visit-scope <a href="http://piwik.org/docs/custom-variables/" target="_blank" title="Was ist eine benutzerdefinierte Variable">benutzerdefinierten Variablen</a> gespeichert werden. (leer lassen wenn nicht benötigt)<p><b>Konfiguration:</b> parameter => index|Bezeichnung<br><code>foo => 4|Parameter 1<br>foo2 => 5|Parameter2</code><br>Bei einem Aufruf von "http://www.shop.tld/index.php?foo=bar" wird in Piwik in die benutzerdefinierte Variable mit dem Index 4 und dem Namen "Parameter 1" der Wert "bar" gespeichert.<br><b>Achtung</b> ein visit-scope Index wird auch schon für das Zahlungsart-Tracking verwendet!</p></em>',
'SHOP_MODULE_hdi-piwik_aParamMapPage' => '<h3>Parameter Mapping für page-scope benutzerdefinierte Variablen</h3><em>Hierüber können pro Zeile ein URL Parameter in Piwiks page-scope <a href="http://piwik.org/docs/custom-variables/" target="_blank" title="Was ist eine benutzerdefinierte Variable">benutzerdefinierten Variablen</a> gespeichert werden. (leer lassen wenn nicht benötigt)<br> Funktionsweise wie beim visit-scope Parameter-Mapping.<br><b>Achtung</b> ein page-scope Index wird auch schon für das Newsletter-Tracking verwendet!</em>'
);
