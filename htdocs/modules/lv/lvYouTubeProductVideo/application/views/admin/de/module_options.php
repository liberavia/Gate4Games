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
    'SHOP_MODULE_GROUP_lvyoutubeconnect'                => 'Zugangseinstellungen',
    'SHOP_MODULE_GROUP_lvyoutubeparams'                 => 'Abfrageparameter',
    'SHOP_MODULE_GROUP_lvyoutubedebug'                  => 'Logs und Loglevel',
    // options connect
    'SHOP_MODULE_sLvApiKey'                             => 'YouTube API Key',           
    'SHOP_MODULE_sLvApiBaseRequestAddress'              => 'Basisadresse zur API-Afrage',  
    'SHOP_MODULE_sLvApiRequestAction'                   => 'Aktion der API-Abfrage',
    'SHOP_MODULE_sLvApiBaseTargetAddress'               => 'Basisadresse für die YouTube VideoURL',  
    // group params
    'SHOP_MODULE_sLvApiRequestPart'                     => 'Zurückzuliefernde Teile (part) der Abfrage',
    'SHOP_MODULE_sLvApiRequestMaxResults'               => 'Anzahl der maximal zurückzuliefernden Ergebnisse',
    'SHOP_MODULE_sLvApiRequestOrder'                    => 'Sortierkriterium',
    'SHOP_MODULE_sLvApiRequestPrefix'                   => 'Suchpräfix der dem Titel des Produkts vorangestellt wird (optional)',
    'SHOP_MODULE_sLvApiRequestSuffix'                   => 'Suchsuffix der dem Titel des Produkts hinten angestellt wird (optional)',
    'SHOP_MODULE_aLvApiChannelIds'                      => 'Suche auf folgende Kanal-IDs eingrenzen (optional)',
    'SHOP_MODULE_blLvTitleCheck'                        => 'Titel des Produkts (ohne prä- und suffix) muss im Videotitel enthalten sein',
    'SHOP_MODULE_aLvTitleRemove'                        => 'Die aufgelisteten Begriffe sollen vom Produkttitel VOR der Suche entfernt werden (optional)',
    // group debug
    'SHOP_MODULE_blLvYouTubeLogActive'                  => 'Aktivitäten in Log protokollieren (lvyoutube.log)',
    'SHOP_MODULE_sLvYouTubeLogLevel'                    => 'Log-Level (1=Fehler,2=Fehler+Warnungen,3=Alle Aktivitäten, 4=Alle Aktivitäten+Debug-Ausgaben)',
);

