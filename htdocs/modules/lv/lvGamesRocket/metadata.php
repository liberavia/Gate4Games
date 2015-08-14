<?php
/**
 * External media module
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
    'id'           => 'lvGamesRocket',
    'title'        => 'Partner: Gamesrocket',
    'description'  => array(
        'de' => 'Partnermodul für gamesrocket.com',
        'en' => 'Partnermodule for gamesrocket.com',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
    ),
    'files' => array(
        'lvgamesrocket'                 => 'lv/lvGamesRocket/application/models/lvgameliebe.php',
    ),
    'events'       => array(
    ),
    'templates' => array(
    ),
    'blocks' => array(
    ),
    'settings' => array(
        array( 'group' => 'lvgrmain',                  'name' => 'sLvGrPartnerId',                'type' => 'str',        'value' => '' ),
        array( 
            'group' => 'lvgrmain',
            'name' => 'aLvGrVendorId',
            'type' => 'aarr',
            'value' => array(),
        ),
        array( 
            'group' => 'lvgrmain',
            'name' => 'aLvGrCsvFeeds',
            'type' => 'aarr',
            'value' => array(
                'de' => 'http://a.gamesrocket.com/affiliate_get_product_csv.php',
            ),
        ),
        array( 'group' => 'lvgrmain',                  'name' => 'sLvGrDefaultCategoryId',          'type' => 'str',        'value' => '' ),
        // group debug
        array( 'group' => 'lvgr_debug',                'name' => 'blLvGrLogActive',                 'type' => 'bool',       'value' => false ),
        array( 'group' => 'lvgr_debug',                'name' => 'sLvGrLogLevel',                   'type' => 'str',        'value' => '1' ),
    )
);