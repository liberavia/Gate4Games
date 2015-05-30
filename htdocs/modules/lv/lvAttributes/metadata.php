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
    'id'           => 'lvAttributes',
    'title'        => 'Attributserweiterungen',
    'description'  => array(
        'de' => 'Modul für Attributserweiterungen',
        'en' => 'Module for attribute extensions',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
        // components->widgets
        'oxwarticledetails'                 => 'lv/lvAttributes/extend/application/components/widgets/lvattr_oxwarticledetails',
        'oxwarticlebox'                     => 'lv/lvAttributes/extend/application/components/widgets/lvattr_oxwarticlebox',
        // models
        'oxarticle'                         => 'lv/lvAttributes/extend/application/models/lvattr_oxarticle',
        'oxattributelist'                   => 'lv/lvAttributes/extend/application/models/lvattr_oxattributelist',
    ),
    'files' => array(
    ),
    'events'       => array(
    ),
    'templates' => array(
        'lvattr_sysrequirements.tpl'        => 'lv/lvAttributes/application/views/frontend/page/details/inc/lvattr_sysrequirements.tpl'
    ),
    'blocks' => array(
        array( 'template' => 'page/details/inc/productmain.tpl',        'block'=>'details_productmain_shortdesc',                   'file'=>'extend/application/views/blocks/block_details_productmain_shortdesc.tpl' ),
        array( 'template' => 'widget/product/listitem_grid.tpl',        'block'=>'widget_product_listitem_grid_tobasket',           'file'=>'extend/application/views/blocks/block_widget_product_listitem_grid_tobasket.tpl' ),
        array( 'template' => 'widget/product/listitem_infogrid.tpl',    'block'=>'widget_product_listitem_infogrid_tobasket',       'file'=>'extend/application/views/blocks/block_widget_product_listitem_infogrid_tobasket.tpl' ),
        array( 'template' => 'widget/product/listitem_line.tpl',        'block'=>'widget_product_listitem_line_tobasket',           'file'=>'extend/application/views/blocks/block_widget_product_listitem_line_tobasket.tpl' ),
        array( 'template' => 'page/details/inc/tabs.tpl',               'block'=>'details_tabs_attributes',                         'file'=>'extend/application/views/blocks/block_widget_details_tabs_attributes.tpl' ),
    ),
    'settings' => array(
        array( 
            'group' => 'lvattrmain',      
            'name' => 'aLvCompatibilityValue2Icon',         
            'type' => 'aarr',  
            'value' => array( 
                'CompatibilityTypeWine'     => 'Silber:wine_24.png:LV_ATTR_COMPATIBLE_WINE_SILVER|Gold:wine_24.png:LV_ATTR_COMPATIBLE_WINE_GOLD|Platin:wine_24.png:LV_ATTR_COMPATIBLE_WINE_PLATINUM', 
                'CompatibilityTypeMac'      => 'Ja:mac_24.png:LV_ATTR_COMPATIBLE_MAC', 
                'CompatibilityTypeLin'      => 'Ja:linux_24.png:LV_ATTR_COMPATIBLE_LIN',
                'CompatibilityTypeWin'      => 'Ja:win_24.png:LV_ATTR_COMPATIBLE_WIN', 
                'CompatibilityTypePOL'      => 'Ja:pol_24.png:LV_ATTR_COMPATIBLE_POL',
            ) 
        ),
        array( 
            'group' => 'lvattrmain',      
            'name' => 'aLvAgeValue2Icon',         
            'type' => 'aarr',  
            'value' => array( 
                'RecommendedAgePegi'        => '3:pegi_3.png:LV_ATTR_AGE_PEGI|7:pegi_7.png:LV_ATTR_AGE_PEGI|12:pegi_12.png:LV_ATTR_AGE_PEGI|16:pegi_16.png:LV_ATTR_AGE_PEGI|18:pegi_18.png:LV_ATTR_AGE_PEGI', 
                'RecommendedAgeUsk'         => '0:usk_0.png:LV_ATTR_AGE_USK|6:usk_6.png:LV_ATTR_AGE_USK|12:usk_12.png:LV_ATTR_AGE_USK|16:usk_16.png:LV_ATTR_AGE_USK|18:pegi_18.png:LV_ATTR_AGE_USK', 
            ) 
        ),
    )
);
 
