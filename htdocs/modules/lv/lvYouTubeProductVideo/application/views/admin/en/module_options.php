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

$sLangName = 'English';

$aLang = array(
    'charset'                                           => 'UTF-8',
    // groups
    'SHOP_MODULE_GROUP_lvyoutubeconnect'                => 'Access Settings',
    'SHOP_MODULE_GROUP_lvyoutubeparams'                 => 'Request parameters',
    'SHOP_MODULE_GROUP_lvyoutubedebug'                  => 'Logs and Loglevel',
    // options connect
    'SHOP_MODULE_sLvApiKey'                             => 'YouTube API Key',           
    'SHOP_MODULE_sLvApiBaseRequestAddress'              => 'Base URL for API-Request',  
    'SHOP_MODULE_sLvApiRequestAction'                   => 'Action for API-Request',
    'SHOP_MODULE_sLvApiBaseTargetAddress'               => 'Baseaddress for YouTube VideoURL',  
    // group params
    'SHOP_MODULE_sLvApiRequestPart'                     => 'Returning request parts of request',
    'SHOP_MODULE_sLvApiRequestMaxResults'               => 'Amount of maximum results',
    'SHOP_MODULE_sLvApiRequestOrder'                    => 'Sorting criteria',
    'SHOP_MODULE_sLvApiRequestPrefix'                   => 'Searchprefix which will be put in front of article title (optional)',
    'SHOP_MODULE_sLvApiRequestSuffix'                   => 'Searchsuffix which will be put behind article title (optional)',
    'SHOP_MODULE_aLvApiChannelIds'                      => 'Limit Search on the following channelids (optional)',
    'SHOP_MODULE_blLvTitleCheck'                        => 'Title of product (without pre- and suffix) must be contained in video title',
    'SHOP_MODULE_aLvTitleRemove'                        => 'The following list of terms need to be removed from title before searching (optional)',
    // group debug
    'SHOP_MODULE_blLvYouTubeLogActive'                  => 'Log activity in file (lvyoutube.log)',
    'SHOP_MODULE_sLvYouTubeLogLevel'                    => 'Log-Level (1=Errors,2=Errors+Warnings,3=All activity, 4=All activity + Debug-Output)',
);

