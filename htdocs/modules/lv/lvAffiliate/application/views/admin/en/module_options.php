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
    // group assignment
    'SHOP_MODULE_GROUP_lvaffiliateassignment'           => 'Assignments',
    'SHOP_MODULE_sLvAffiliateMainCategory'              => 'Main Category/Root Category',
    'SHOP_MODULE_aLvField2MatchManufacturer'            => 'Assign values of data array to manufacturers. Will be created if there is no match.',
    'SHOP_MODULE_aLvField2MatchArticle'                 => 'Assignment ordering which is for trying to match an existing article.',
    'SHOP_MODULE_aLvField2DirectTable'                  => 'Direct value-table assignments if ID of article is available.',
    'SHOP_MODULE_aLvField2CategoryAssignment'           => 'Direct assignment to category if ID of article is available.',
    'SHOP_MODULE_aLvField2Attribute'                    => 'Values of data array that will be assigned to shop attributes. Wil only be done if ID of article is known.',
    'SHOP_MODULE_aLvCatId2Attr2CatId'                   => 'Assign categories in condition of one ore more attributes to other category-ids',
    'SHOP_MODULE_aLvRemoveFromName'                     => 'Remove list of following terms from name before trying to match it',
    'SHOP_MODULE_aLvDeclineImportOnTerm'                => 'List of terms that lead to not importing products (Steam-Presents etc.)',
    // group maintenance
    'SHOP_MODULE_GROUP_lvaffiliate_maintenance'         => 'Maintenance functions',
    'SHOP_MODULE_blLvAffiliateResetActive'              => 'Activate automatic articlereset',
    'SHOP_MODULE_sLvAffiliateResetFromHour'             => 'Hour of day from which articlereset may be done',
    'SHOP_MODULE_sLvAffiliateResetToHour'               => 'Hour of day to which articlereset may be done',
    'SHOP_MODULE_sLvCompleteDeleteDelayDays'            => 'Days after inactive articles should be removed completely',
    // group debug
    'SHOP_MODULE_GROUP_lvaffiliate_debug'               => 'Logs and Debugging',
    'SHOP_MODULE_blLvAffiliateLogActive'                => 'Log activity in logfile (lvaffiliate_import.log)',
    'SHOP_MODULE_sLvAffiliateLogLevel'                  => 'Log-Level (1=Errors,2=Errors+warnings,3=All activity, 4=All activity+Debug-messages)',
    // group top lists
    'SHOP_MODULE_GROUP_lvaffiliate_toplists'            => 'Top-Lists',
    'SHOP_MODULE_blLvOnlyLoadTopManufacturer'           => 'Load only top manufacturer on startpage',
    'SHOP_MODULE_sLvListTopSellerId'                    => 'Action-ID of Top-Seller',
    'SHOP_MODULE_sLvListTopSaleId'                      => 'Action-ID of Top-Sale',
    'SHOP_MODULE_sLvListLatestId'                       => 'Action-ID of list for latest arrived articles',
    'SHOP_MODULE_sLvListLatestAttributeId'              => 'Attribute ID for realesedate',
    // group facebook
    'SHOP_MODULE_GROUP_lvaffiliate_facebook'            => 'Facebook',
    'SHOP_MODULE_sLvFbHomePage'                         => 'Link to facebook homepage of affiliate shop',
    
    // help
    'HELP_SHOP_MODULE_aLvCatId2Attr2CatId'              => 'yet to come',
    
);

