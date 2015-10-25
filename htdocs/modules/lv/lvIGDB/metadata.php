<?php
/**
 * News extension module
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
    'id'           => 'lvIGDB',
    'title'        => 'Internet Games Database',
    'description'  => array(
        'de' => 'Modul zum Bezug von Game-Informationen von IGDB.com',
        'en' => 'Module for fetching Game-Informations of IGDB.com',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
    ),
    'files' => array(
        // models
        'lvigdb'                            => 'lv/lvIGDB/application/models/lvigdb.php',
        // core
        'lvigdbevents'                      => 'lv/lvIGDB/core/lvigdbevents.php',
    ),
    'events'       => array(
        'onActivate'                        => 'lvigdbevents::onActivate',
        'onDeactivate'                      => 'lvigdbevents::onDeactivate',        
    ),
    'templates' => array(
    ),
    'blocks' => array(
        array( 'template' => 'productmain.tpl',           'block'=>'details_productmain_ratings',            'file'=>'extend/application/views/blocks/block_details_productmain_ratings.tpl' ),
    ),
    'settings' => array(
        array( 
            'group' => 'lvigdbmain',      
            'name' => 'sLvIGDBAuthToken',       
            'type' => 'str',  
            'value' => ''
        ),
        array( 
            'group' => 'lvigdbmain',      
            'name' => 'sLvIGDBRefreshDayRatio',       
            'type' => 'str',  
            'value' => '14'
        ),
        array( 
            'group' => 'lvigdbmain',      
            'name' => 'aLvIGDBPlatforms',       
            'type' => 'arr',  
            'value' => array( 
            ) 
        ),
        array( 
            'group' => 'lvigdbmain',      
            'name' => 'aLvIGDBCleanupTerms',       
            'type' => 'arr',  
            'value' => array( 
            ) 
        ),
    )
);
 
