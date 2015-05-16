<?php
/**
 * Master variant module
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
    'id'           => 'lvMasterVariant',
    'title'        => 'Variantenvererbung',
    'description'  => array(
        'de' => 'Modul für die Rückwärtsvererung von Varianten zu Elternartikeln. Achtung: Modul hängt ab von Externe Medien und Bilder',
        'en' => 'Module for reverse inheritance from variants to parent articles. Caution: Modul depends on activated module Externe Medien und Bilder',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
        // controllers admin
        'article_main'                          => 'lv/lvMasterVariant/extend/application/controllers/admin/lvmv_article_main',
        // models
        'oxarticle'                             => 'lv/lvMasterVariant/extend/application/models/lvmv_oxarticle',
    ),
    'files' => array(
    ),
    'events'       => array(
    ),
    'templates' => array(
    ),
    'blocks' => array(
        array( 'template' => 'article_main.tpl',                            'block'=>'admin_article_main_form',                 'file'=>'extend/application/views/blocks/block_admin_article_main_form.tpl' ),
        array( 'template' => 'page/details/inc/productmain.tpl',            'block'=>'details_productmain_shortdesc',           'file'=>'extend/application/views/blocks/block_details_productmain_shortdesc.tpl' ),
        array( 'template' => 'widget/product/listitem/infogrid_price.tpl',  'block'=>'widget_product_listitem_infogrid_price',  'file'=>'extend/application/views/blocks/block_widget_product_listitem_infogrid_price.tpl' ),
        
    ),
    'settings' => array(
    )
);
 
