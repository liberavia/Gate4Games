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
    // groups
    'SHOP_MODULE_GROUP_lvamzpn_connect'                 => 'Zugangseinstellungen',
    'SHOP_MODULE_GROUP_lvamzpn_import'                  => 'Importoptionen',
    'SHOP_MODULE_GROUP_lvamzpn_debug'                   => 'Logs und Loglevel',
    // options connect
    'SHOP_MODULE_sLvAmzPnAssociateTag'                  => 'Amazon Partner-Id',           
    'SHOP_MODULE_sLvAmzPnAWSAccessKeyId'                => 'AWS Access Key',  
    'SHOP_MODULE_sLvAmzPnAWSSecretKey'                  => 'AWS Secret Key',
    'SHOP_MODULE_aLvAmzPnAWSService2Lang'               => 'Zuordnung Sprackkürzel zu Amazon Webservice',
    // group import
    'SHOP_MODULE_aLvAmzPnVendorId'                      => 'Zuordnung Sprachkürzel zu zuzuordnende Lieferanten ID',
    'SHOP_MODULE_sLvAmzPnDefaultCatId'                  => 'Standardkategorie der Produkte zugeordnet werden, wenn das Mapping fehlschlägt',
    'SHOP_MODULE_aLvAmzPnBrowseNodes'                   => 'Zuordnung Sprachkürzel zu Amazon Browse-Nodes aus denen Produkte importiert werden sollen',
    'SHOP_MODULE_aLvAmzPnPriceRanges'                   => 'Zuordnung Sprachkürzel zu Preisbereichen (in Cent), nach denen in den einzelnen Browse-Nodes gefiltert werden soll',
    'SHOP_MODULE_sLvAmzPnSearchIndex'                   => 'Amazon Search-Index von welchem die Produkte importiert werden sollen',
    'SHOP_MODULE_sLvAmzPnCondition'                     => 'Zustand der Produkte',
    'SHOP_MODULE_sLvAmzPnSearchResponseGroups'          => 'Antwortgruppen für Suchanfragen (Entwickleroption)',
    'SHOP_MODULE_sLvAmzPnLookupResponseGroups'          => 'Antwortgruppen für Detailanfragen (Entwickleroption)',
    // group debug
    'SHOP_MODULE_blLvAmzPnLogActive'                    => 'Aktivitäten in Log protokollieren (lvamzpn.log)',
    'SHOP_MODULE_sLvAmzPnLogLevel'                      => 'Log-Level (1=Fehler,2=Fehler+Warnungen,3=Alle Aktivitäten, 4=Alle Aktivitäten+Debug-Ausgaben)',
);

