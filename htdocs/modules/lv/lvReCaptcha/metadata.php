<?php
/**
 * ReCaptcha Module
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
    'id'           => 'lvReCaptcha',
    'title'        => 'Recaptcha Reviews',
    'description'  => array(
        'de' => 'Modul welches öffentliche Bewertungen von Benutzern zulässt und zur Sicherung Google ReCaptcha nutzt. Zusätzlich stellt es optional eine Komponente für Feedback bereit.',
        'en' => 'Module for making public reviews possible by using Google Recaptcha',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'Liberavia',
    'url'          => 'http://www.gate4games.com',
    'email'        => 'info@gate4games.com',
    'extend'       => array(
        // components->widgets
        'oxwrating'                 => 'lv/lvReCaptcha/extend/application/components/widgets/lvrecaptcha_oxwrating',
        'oxwreview'                 => 'lv/lvReCaptcha/extend/application/components/widgets/lvrecaptcha_oxwreview',
        // controllers
        'details'                   => 'lv/lvReCaptcha/extend/application/controllers/lvrecaptcha_details',
        // models
        'oxemail'                   => 'lv/lvReCaptcha/extend/application/models/lvfeedback_oxemail',
        // core
        'oxviewconfig'              => 'lv/lvReCaptcha/extend/core/lvfeedback_oxviewconfig',
    ),
    'files' => array(
    ),
    'events'       => array(
    ),
    'templates' => array(
    ),
    'blocks' => array(
        array( 'template' => 'layout/base.tpl',                         'block'=>'base_style',                                  'file'=>'extend/application/views/blocks/block_base_style.tpl' ),
        array( 'template' => 'page/details/inc/productmain.tpl',        'block'=>'lv_fullproductinfo_details_reviews',          'file'=>'extend/application/views/blocks/block_lv_fullproductinfo_details_reviews.tpl' ),
        array( 'template' => 'page/review/review.tpl',                  'block'=>'lv_page_review_widget',                       'file'=>'extend/application/views/blocks/block_lv_page_review_widget.tpl' ),
        array( 'template' => 'widget/reviews/rating.tpl',               'block'=>'lv_details_rating',                           'file'=>'extend/application/views/blocks/block_lv_details_rating.tpl' ),
        array( 'template' => 'widget/reviews/reviews.tpl',              'block'=>'widget_reviews_form',                         'file'=>'extend/application/views/blocks/block_widget_reviews_form.tpl' ),
        array( 'template' => 'layout/base.tpl',                         'block'=>'base_js',                                     'file'=>'extend/application/views/blocks/block_base_js.tpl' ),
        array( 'template' => 'layout/page.tpl',                         'block'=>'layout_breadcrumb',                           'file'=>'extend/application/views/blocks/block_layout_breadcrumb.tpl' ),
        array( 'template' => 'layout/page.tpl',                         'block'=>'layout_header',                               'file'=>'extend/application/views/blocks/block_layout_header.tpl' ),
    ),
    'settings' => array(
        array( 'group' => 'lvrecaptchamain',        'name' => 'sLvRecaptchaRequestUrl',         'type' => 'str',        'value' => "https://www.google.com/recaptcha/api/siteverify" ),
        array( 'group' => 'lvrecaptchamain',        'name' => 'sLvRecaptchaWebsiteKey',         'type' => 'str',        'value' => '' ),
        array( 'group' => 'lvrecaptchamain',        'name' => 'sLvRecaptchaSecretKey',          'type' => 'str',        'value' => '' ),
        // group email
        array( 'group' => 'lvfeedback',             'name' => 'sLvFeedbackEmail',               'type' => 'str',        'value' => "" ),
        array( 'group' => 'lvfeedback',             'name' => 'blLvFeedbackActive',             'type' => 'bool',       'value' => false ),
    )
);
 
