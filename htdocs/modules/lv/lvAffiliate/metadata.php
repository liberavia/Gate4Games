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
    'id'           => 'lvAffiliate',
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
        // components
        'oxcmp_oxcategories'                        => 'lv/lvAffiliate/extend/application/components/lvaffiliate_oxcmp_oxcategories',
        // components->widgets
        'oxwarticledetails'                         => 'lv/lvAffiliate/extend/application/components/widgets/lvaffiliate_oxwarticledetails',
        // controllers admin
        'vendor_main'                               => 'lv/lvAffiliate/extend/application/controllers/admin/lvaffiliate_vendor_main',
        'content_main'                              => 'lv/lvAffiliate/extend/application/controllers/admin/lvaffiliate_content_main',
        // controllers
        'start'                                     => 'lv/lvAffiliate/extend/application/controllers/lvaffiliate_start',
        'account'                                   => 'lv/lvAffiliate/extend/application/controllers/lvaffiliate_account',
        'basket'                                    =>  'lv/lvAffiliate/extend/application/controllers/lvaffiliate_basket',
        // models
        'oxarticle'                                 => 'lv/lvAffiliate/extend/application/models/lvaffiliate_oxarticle',
        'oxcategory'                                => 'lv/lvAffiliate/extend/application/models/lvaffiliate_oxcategory',
        'oxarticlelist'                             => 'lv/lvAffiliate/extend/application/models/lvaffiliate_oxarticlelist',
        'oxcontentlist'                             => 'lv/lvAffiliate/extend/application/models/lvaffiliate_oxcontentlist',
        'oxpricealarm'                              => 'lv/lvAffiliate/extend/application/models/lvaffiliate_oxpricealarm',
        'oxemail'                                 => 'lv/lvAffiliate/extend/application/models/lvaffiliate_oxemail',
        // core
        'oxviewconfig'                              => 'lv/lvAffiliate/extend/core/lvaffiliate_oxviewconfig',
    ),
    'files' => array(
        'lvaffiliate_import'                        => 'lv/lvAffiliate/application/models/lvaffiliate_import.php',
        'lvaffiliate_tools'                         => 'lv/lvAffiliate/application/models/lvaffiliate_tools.php',
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
        array( 'template' => 'manufacturer_main.tpl',                       'block'=>'admin_manufacturer_main_form',                'file'=>'extend/application/views/blocks/block_admin_manufacturer_main_form.tpl' ),
        array( 'template' => 'page/shop/start.tpl',                         'block'=>'lv_page_shop_start_offers',                   'file'=>'extend/application/views/blocks/block_lv_page_shop_start_offers.tpl' ),
        array( 'template' => 'page/details/inc/productmain.tpl',            'block'=>'details_productmain_price',                   'file'=>'extend/application/views/blocks/block_details_productmain_price.tpl' ),
        array( 'template' => 'widget/header/search.tpl',                    'block'=>'header_search_field',                         'file'=>'extend/application/views/blocks/block_header_search_field.tpl' ),
        array( 'template' => 'content_main.tpl',                            'block'=>'admin_content_main_form',                     'file'=>'extend/application/views/blocks/block_admin_content_main_form.tpl' ),
    ),
    'settings' => array(
        // group assignments
        array( 'group' => 'lvaffiliateassignment',              'name' => 'sLvAffiliateMainCategory',               'type' => 'str',        'value' => '' ),
        array( 
            'group' => 'lvaffiliateassignment',      
            'name' => 'aLvField2MatchManufacturer',         
            'type' => 'aarr',  
            'value' => array( 
                'MANUFACTURER'                  => 'oxmanufacturers|OXTITLE', 
            ) 
        ),
        array( 
            'group' => 'lvaffiliateassignment',      
            'name' => 'aLvField2MatchArticle',         
            'type' => 'aarr',  
            'value' => array( 
                'ARTNUM'                        => 'OXARTNUM|child',
                'TITLE'                         => 'OXTITLE|parent', 
            ) 
        ),
        array( 
            'group' => 'lvaffiliateassignment',      
            'name' => 'aLvField2DirectTable',         
            'type' => 'aarr',  
            'value' => array( 
                'ARTNUM'                            => 'oxarticles|OXARTNUM', 
                'EXTURL'                            => 'oxarticles|OXEXTURL', 
                'COVERIMAGE'                        => 'oxarticles|OXPIC1',
                'PIC1'                              => 'oxarticles|OXPIC2',
                'PIC2'                              => 'oxarticles|OXPIC3',
                'PIC3'                              => 'oxarticles|OXPIC4',
                'PIC4'                              => 'oxarticles|OXPIC5',
                'PIC5'                              => 'oxarticles|OXPIC6',
                'PIC6'                              => 'oxarticles|OXPIC7',
                'PIC7'                              => 'oxarticles|OXPIC8',
                'PIC8'                              => 'oxarticles|OXPIC9',
                'PIC9'                              => 'oxarticles|OXPIC10',
                'PIC10'                             => 'oxarticles|OXPIC11',
                'PIC11'                             => 'oxarticles|OXPIC12',
                'PRICE'                             => 'oxarticles|OXPRICE',
                'TPRICE'                            => 'oxarticles|OXTPRICE',
            ) 
        ),
        array( 
            'group' => 'lvaffiliateassignment',      
            'name' => 'aLvField2CategoryAssignment',         
            'type' => 'aarr',  
            'value' => array( 
                'CATEGORYID'                        => 'oxobject2category|OXCATNID',
                'CATEGORYID_SALE'                   => 'oxobject2category|OXCATNID',
            ) 
        ),
        array( 
            'group' => 'lvaffiliateassignment',      
            'name' => 'aLvField2Attribute',         
            'type' => 'aarr',  
            'value' => array( 
                'GENRE'                             => 'GameGenre', 
                'GAMETYPE'                          => 'GameType', 
                'DRM'                               => 'DRM',
                'COMPATWIN'                         => 'CompatibilityTypeWin', 
                'COMPATLIN'                         => 'CompatibilityTypeLin',
                'COMPATMAC'                         => 'CompatibilityTypeMac',
                'COMPATWINE'                        => 'CompatibilityTypeWine',
                'COMPATPOL'                         => 'CompatibilityTypePOL',
                'LANGUAGEINFO|DUBBED'               => 'GameLanguageAudio',
                'LANGUAGEINFO|INTERFACE'            => 'GameLanguageInterface',
                'LANGUAGEINFO|SUBTITLE'             => 'GameLanguageSubtitles',
                'USK'                               => 'RecommendedAgeUsk',
                'PEGI'                              => 'RecommendedAgePegi',
                'RELEASE'                           => 'ReleaseDate',
            ) 
        ),
        array( 
            'group' => 'lvaffiliateassignment',      
            'name' => 'aLvCatId2Attr2CatId',         
            'type' => 'aarr',  
            'value' => array( 
                '41625027aee7153cabcb4d7b9120c0d9'  => 'LVISSALE:fadcb6dd70b9f6248efa425bd159684e|CompatibilityTypeWine#CompatibilityTypePOL:8bc5e347ed09f9c8004fe39784368388|CompatibilityTypeLin:112d57e64d38a45af1dbc560ac797af0|CompatibilityTypeMac:aac464ccbb8aae00f4fafcc9bbc8cafa', 
            ) 
        ),
        // group debug
        array( 'group' => 'lvaffiliate_debug',          'name' => 'blLvAffiliateLogActive',             'type' => 'bool',       'value' => false ),
        array( 'group' => 'lvaffiliate_debug',          'name' => 'sLvAffiliateLogLevel',               'type' => 'str',        'value' => '1' ),
        // group toplists
        array( 'group' => 'lvaffiliate_toplists',       'name' => 'blLvOnlyLoadTopManufacturer',        'type' => 'bool',       'value' => true ),
        array( 'group' => 'lvaffiliate_toplists',       'name' => 'sLvListTopSellerId',                 'type' => 'str',        'value' => '' ),
        array( 'group' => 'lvaffiliate_toplists',       'name' => 'sLvListTopSaleId',                   'type' => 'str',        'value' => '' ),
        array( 'group' => 'lvaffiliate_toplists',       'name' => 'sLvListLatestId',                    'type' => 'str',        'value' => '' ),
        array( 'group' => 'lvaffiliate_toplists',       'name' => 'sLvListLatestAttributeId',           'type' => 'str',        'value' => '' ),
        // group facebook
        array( 'group' => 'lvaffiliate_facebook',       'name' => 'sLvFbHomePage',                      'type' => 'str',        'value' => '' ),
        
    )
);
 
