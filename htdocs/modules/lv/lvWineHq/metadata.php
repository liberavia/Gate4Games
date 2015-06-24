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
    'id'           => 'lvWineHq',
    'title'        => 'WineHq',
    'description'  => array(
        'de' => 'Modul zum automatischen Bezug von Wine-Kompatibilitäten',
        'en' => 'Module for automatic fetching of wine compatibility information',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
    ),
    'files' => array(
        'lvwinehq'                      => 'lv/lvWineHq/application/models/lvwinehq.php',
    ),
    'events'       => array(
    ),
    'templates' => array(
    ),
    'blocks' => array(
    ),
    'settings' => array(
        array( 'group' => 'lvwinehqmain',       'name' => 'sLvWineHqListRequestBase',               'type' => 'str',       'value' => 'https://appdb.winehq.org/objectManager.php?sappCategoryData0=2&iappCategoryOp0=11&iItemsPerPage=200&sClass=application&iPage=1&iappVersion-ratingOp0=5&sappVersion-ratingData0=' ),
        array( 
            'group'     => 'lvwinehqmain',       
            'name'      => 'aLvWineHqRatings',                   
            'type'      => 'arr',       
            'value'     => array(
                'Silver',
                'Gold',
                'Platinum',
            ), 
        ),
        array( 'group' => 'lvwinehqmain',       'name' => 'sLvWineHqDetailsLinkBase',               'type' => 'str',        'value' => 'https://appdb.winehq.org/objectManager.php?sClass=application&iId=' ),
    )
);
 
// https://appdb.winehq.org/objectManager.php?sappCategoryData0=2&iappCategoryOp0=11&iItemsPerPage=200&sClass=application&iPage=1&iappVersion-ratingOp0=5&sappVersion-ratingData0=Platinum