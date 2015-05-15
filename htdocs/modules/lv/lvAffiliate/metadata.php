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
    'id'           => 'lvAfffiliage',
    'title'        => 'Affiliate Module',
    'description'  => array(
        'de' => 'Modul welches den Shop in einen Affiliate Shop verwandelt. Es deaktiviert auch verschiedene Funktionen des Shops.',
        'en' => 'Module for transforming shop into an affiliate shop. It also deactivates some components of the shop system',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
        // components->widgets
        'oxwarticledetails'                         => 'lv/lvAffiliate/extend/application/components/widgets/lvaffiliate_oxwarticledetails',
    ),
    'files' => array(
    ),
    'events'       => array(
    ),
    'templates' => array(
        'page/details/inc/lvaffiliatelist.tpl'      => 'lv/lvAffiliate/application/views/frontend/tpl/page/details/inc/lvaffiliatelist.tpl',
    ),
    'blocks' => array(
        array( 'template' => 'page/details/inc/fullproductinfo.tpl',        'block'=>'lv_fullproductinfo_details_related',          'file'=>'extend/application/views/blocks/block_lv_fullproductinfo_details_related.tpl' ),
    ),
    'settings' => array(
    )
);
 
