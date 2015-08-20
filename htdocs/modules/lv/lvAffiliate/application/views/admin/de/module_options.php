<?php

/* 
 * Copyright (C) 2015 André Gregor-Herrmann
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
 */

// -------------------------------
// RESOURCE IDENTIFIER = STRING
// -------------------------------

$sLangName = 'Deutsch';

$aLang = array(
    'charset'                                           => 'UTF-8',
    // group assignment
    'SHOP_MODULE_GROUP_lvaffiliateassignment'           => 'Zuordnungen',
    'SHOP_MODULE_sLvAffiliateMainCategory'              => 'Hauptkategorie/Obergeordnete Kategorie',
    'SHOP_MODULE_aLvField2MatchManufacturer'            => 'Werte des Datenarrays Hersteller zuordnen. Wird angelegt, wenn es keinen Treffer gibt.',
    'SHOP_MODULE_aLvField2MatchArticle'                 => 'Zuordnungsreihenfolge über welche versucht wird einen existierenden Artikel zu finden.',
    'SHOP_MODULE_aLvField2DirectTable'                  => 'Direkte Tabellenzuordnungen, sobald die ID des Artikels bekannt ist.',
    'SHOP_MODULE_aLvField2CategoryAssignment'           => 'Direkte Zuordnung zur Kategorie, sobald die ID des Artikels bekannt ist.',
    'SHOP_MODULE_aLvField2Attribute'                    => 'Werte des Datenarrays die als Attributswerte zugeordnet werden. Erfolgt nur, wenn ID des Artikels ermittelt wurde.',
    'SHOP_MODULE_aLvCatId2Attr2CatId'                   => 'Produkte einer Kategorie in Abhängigkeit von einem oder mehreren Attributen anderen Katregorien zuordnen',
    // group maintenance
    'SHOP_MODULE_GROUP_lvaffiliate_maintenance'         => 'Wartungsfunktionen',
    'SHOP_MODULE_blLvAffiliateResetActive'              => 'Automatischen Artikelreset aktivieren',
    'SHOP_MODULE_sLvAffiliateResetFromHour'             => 'Stunde des Tages ab welcher der Artikelreset <b>frühestens</b> erfolgen darf',
    'SHOP_MODULE_sLvAffiliateResetToHour'               => 'Stunde des Tages ab welcher der Artikelreset <b>spätestens</b> erfolgen darf',
    // group debug
    'SHOP_MODULE_GROUP_lvaffiliate_debug'               => 'Logs and Debugging',
    'SHOP_MODULE_blLvAffiliateLogActive'                => 'Aktivitäten in Log protokollieren (lvaffiliate_import.log)',
    'SHOP_MODULE_sLvAffiliateLogLevel'                  => 'Log-Level (1=Fehler,2=Fehler+Warnungen,3=Alle Aktivitäten, 4=Alle Aktivitäten+Debug-Ausgaben)',
    // group top lists
    'SHOP_MODULE_GROUP_lvaffiliate_toplists'            => 'Top-Listen',
    'SHOP_MODULE_blLvOnlyLoadTopManufacturer'           => 'Nur Top-Hersteller auf Startseite laden',
    'SHOP_MODULE_sLvListTopSellerId'                    => 'Aktions-ID der Top-Seller',
    'SHOP_MODULE_sLvListTopSaleId'                      => 'Aktions-ID der Top-Angebote',
    'SHOP_MODULE_sLvListLatestId'                       => 'Aktions-ID der Liste für zuletzt eingetroffenen Artikel',
    'SHOP_MODULE_sLvListLatestAttributeId'              => 'Attributs ID für Veröffentlichungsdatum',
    // group facebook
    'SHOP_MODULE_GROUP_lvaffiliate_facebook'            => 'Facebook',
    'SHOP_MODULE_sLvFbHomePage'                         => 'Link zur Facebook Homepage des Affiliate-Auftritts',
    
    // help
    'HELP_SHOP_MODULE_aLvCatId2Attr2CatId'              => '<p>Links steht die KategorieId der Produktquelle. Rechts steht eine Liste von zuzuordnenden Attributen und Kategorien, welche durch ein Pipe (|) getrennt werden.</p> <p>Wenn die Zuordnung ohne Attributsfilterung erfolgen soll, so wird statt der AttributId das Schlüsselwort <b>LVNOATTR</b> verwendet</p><b>BEISPIELZEILE</b><br>QuellkategorieId => AttributsId_1#AttributsId_2:ZielkategorieId_1|LVNOATTR:ZielkategorieId_2|AttributsId_3:ZielkategorieId_3',
);

