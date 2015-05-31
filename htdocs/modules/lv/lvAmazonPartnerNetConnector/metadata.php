<?php
/**
 * Attribute extension module
 *
 * This module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales PayPal module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.gate4games.com
 * @copyright (C) André Gregor-Herrmann
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.2';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'lvAmazonPartnerNetConnector',
    'title'        => 'Amazon Partner net Connector',
    'description'  => array(
        'de' => 'Dieses Modul verbindet sich mit Ihrem Amazon PartnerNet API und Importiert Produkte eines definierten Browse-Nodes über Affiliate-Modul in ihren Shop',
        'en' => 'This module connects to your Amazon PartnerNet API and imports products of a defined BrowseNode via the Affiliate Module into your Shop',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
    ),
    'files' => array(
    ),
    'events'       => array(
    ),
    'templates' => array(
    ),
    'blocks' => array(
    ),
    'settings' => array(
        // group connect
        array( 'group' => 'lvamzpn_connect',        'name' => 'sLvAmzPnAssociateTag',           'type' => 'str',        'value' => "" ),
        array( 'group' => 'lvamzpn_connect',        'name' => 'sLvAmzPnAWSAccessKeyId',         'type' => 'str',        'value' => "" ),
        array( 'group' => 'lvamzpn_connect',        'name' => 'sLvAmzPnAWSSecretKey',           'type' => 'str',        'value' => "" ),
        // group import
        array( 'group' => 'lvamzpn_import',         'name' => 'sLvAmzPnBrowseNode',             'type' => 'str',        'value' => '' ),
        array( 'group' => 'lvamzpn_import',         'name' => 'sLvAmzPnSearchIndex',            'type' => 'str',        'value' => '' ),
        array( 'group' => 'lvamzpn_import',         'name' => 'sLvAmzPnCondition',              'type' => 'str',        'value' => 'New' ),
        array( 'group' => 'lvamzpn_import',         'name' => 'sLvAmzPnSearchResponseGroups',   'type' => 'str',        'value' => 'Images,ItemAttributes,Offers,VariationSummary,Variations' ),
        array( 'group' => 'lvamzpn_import',         'name' => 'sLvAmzPnLookupResponseGroups',   'type' => 'str',        'value' => 'Images,ItemAttributes,Offers,BrowseNodes,EditorialReview,VariationMatrix,VariationSummary,Variations,SalesRank' ),
        // group debug
        array( 'group' => 'lvamzpn_debug',          'name' => 'blLvAmzPnLogActive',             'type' => 'bool',       'value' => false ),
        array( 'group' => 'lvamzpn_debug',          'name' => 'sLvAmzPnLogLevel',               'type' => 'str',        'value' => '1' ),
    )
);
 
