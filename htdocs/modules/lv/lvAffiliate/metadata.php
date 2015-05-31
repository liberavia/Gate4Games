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
    'id'           => 'lvAfffiliate',
    'title'        => 'Affiliate Module',
    'description'  => array(
        'de' => 'Modul deaktiviert Shopfunktionen und bietet eine Importschnittstelle an mit welcher darauf aufsetzende Module Daten in das Shopsystem importieren können.',
        'en' => 'Module deactivates shop functions and offers an interface for importing products which other modules can build on',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
        // components->widgets
        'oxwarticledetails'                         => 'lv/lvAffiliate/extend/application/components/widgets/lvaffiliate_oxwarticledetails',
        // controllers admin
        'vendor_main'                               => 'lv/lvAffiliate/extend/application/controllers/admin/lvaffiliate_vendor_main',
    ),
    'files' => array(
        'lvaffiliate_import'                        => 'lv/lvAffiliate/application/models/lvaffiliate_import.php',
    ),
    'events'       => array(
    ),
    'templates' => array(
        'page/details/inc/lvaffiliatelist.tpl'      => 'lv/lvAffiliate/application/views/frontend/tpl/page/details/inc/lvaffiliatelist.tpl',
    ),
    'blocks' => array(
        array( 'template' => 'page/details/inc/fullproductinfo.tpl',        'block'=>'lv_fullproductinfo_details_tabs',             'file'=>'extend/application/views/blocks/block_lv_fullproductinfo_details_tabs.tpl' ),
        array( 'template' => 'layout/base.tpl',                             'block'=>'base_style',                                  'file'=>'extend/application/views/blocks/block_base_style.tpl' ),
        array( 'template' => 'vendor_main.tpl',                             'block'=>'admin_vendor_main_form',                      'file'=>'extend/application/views/blocks/block_admin_vendor_main_form.tpl' ),
    ),
    'settings' => array(
    )
);
 
