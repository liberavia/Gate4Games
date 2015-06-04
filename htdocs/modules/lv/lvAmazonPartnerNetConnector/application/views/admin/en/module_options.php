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
    'SHOP_MODULE_GROUP_lvamzpn_connect'                 => 'Access settings',
    'SHOP_MODULE_GROUP_lvamzpn_import'                  => 'Import settings',
    'SHOP_MODULE_GROUP_lvamzpn_debug'                   => 'Logs and loglevel',
    // options connect
    'SHOP_MODULE_sLvAmzPnAssociateTag'                  => 'Amazon Partner-Id',           
    'SHOP_MODULE_sLvAmzPnAWSAccessKeyId'                => 'AWS Access Key',  
    'SHOP_MODULE_sLvAmzPnAWSSecretKey'                  => 'AWS Secret Key',
    'SHOP_MODULE_aLvAmzPnAWSService2Lang'               => 'Assign language abbreviation to belonging Amazon service',
    // group import
    'SHOP_MODULE_sLvAmzPnVendorId'                      => 'Assigning to Vendor ID',
    'SHOP_MODULE_sLvAmzPnDefaultCatId'                  => 'Default category if mapping fails',
    'SHOP_MODULE_aLvAmzPnBrowseNodes'                   => 'Assign language abbreviation to Amazon Browse-Nodes from which products will be imported',
    'SHOP_MODULE_aLvAmzPnPriceRanges'                   => 'Assign language abbreviation to price ranges (in Cent) where single browse nodes will be filtered to',
    'SHOP_MODULE_sLvAmzPnSearchIndex'                   => 'Amazon Search-Index from which products shall be imported',
    'SHOP_MODULE_sLvAmzPnCondition'                     => 'Condition of result products',
    'SHOP_MODULE_sLvAmzPnSearchResponseGroups'          => 'Response groups of search requests (Developer option)',
    'SHOP_MODULE_sLvAmzPnLookupResponseGroups'          => 'Response groups of detail requests (Developer option)',
    // group debug
    'SHOP_MODULE_blLvAmzPnLogActive'                    => 'Log activitiy (lvamzpn.log)',
    'SHOP_MODULE_sLvAmzPnLogLevel'                      => 'Log-Level (1=Errors,2=Errors+Warnings,3=All activity, 4=All activity+debug output)',
);

