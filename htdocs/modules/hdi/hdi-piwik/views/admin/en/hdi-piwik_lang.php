<?php
/**
 * hdi-piwik: OXID module to include Piwik tracking code.
 * Copyright (C) 2012-2014 HEINER DIRECT GmbH & Co. KG
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      HEINER DIRECT GmbH & Co. KG <oxid@heiner-direct.com>
 * @author      Klaus Weidenbach (HEINER DIRECT GmbH & Co. KG)
 * @author      Rafael Dabrowski (HEINER DIRECT GmbH & Co. KG)
 * @link        http://www.heiner-direct.com
 *
 * @copyright   HEINER DIRECT GmbH & Co. KG 2012-2014
 * @license     GPLv3
 *
 * @package     HDI
 * @subpackage  hdi-piwik
 */

$sLangName  = "English";
// -------------------------------
// RESOURCE IDENTITFIER = STRING
// -------------------------------
$aLang = array(
'charset' => 'UTF-8',
'SHOP_MODULE_hdiPiwik_custindexpayment' => 'Piwik visit-scope index, the number from 1 to 5 where the payment method custom variable is stored (set 0 to deactivate)',
'SHOP_MODULE_GROUP_hdi-piwik_Main' => 'Piwik Configuration',
'SHOP_MODULE_hdi-piwik_sUrl' => '<h3>Piwik Server URL</h3><em>Insert here the url of your Piwik server.</em><br><b>Format:</b><i> without http:// or https:// and without trailing /</i><br><b>e.g.:</b> <i>piwik.domain.tld</i>',
'SHOP_MODULE_hdi-piwik_iPageid' => '<h3>Your Piwik site ID</h3><em>Insert here the Piwik site ID.</em>',
'SHOP_MODULE_hdi-piwik_iNewsgoalid' => '<h3>Newsletter Goal ID</h3><em>If you defined a goal in Piwik for a successfull newsletter activation, insert this goal ID here. (set 0 to disable this feature)</em>',
'SHOP_MODULE_hdi-piwik_iMaxCustVar' => '<h3>Amount of custom variables</h3><em>Piwik allows only 5 custom variables per scope by default. This can be changed in Piwik\'s setup. Only change this value if you are sure what you do and you have followed these instructions <a href="http://piwik.org/faq/how-to/faq_17931/" target="_blank">http://piwik.org/faq/how-to/faq_17931/</a>.</em>',
'SHOP_MODULE_hdi-piwik_iCustindexNewsletter' => '<h3>Custom variable index for newsletter registration tracking</h3><em>Piwik\'s page-scope <a href="http://piwik.org/docs/custom-variables/" target="_blank" title="What is a custom variable">custom variable</a> index between 1 and 5 where the newsletter registration tracking should get stored. (set 0 to disable this feature)</em>',
'SHOP_MODULE_hdi-piwik_iCustindexPayment' => '<h3>Custom variable index for payment method tracking</h3><em>Piwik\'s visit-scope <a href="http://piwik.org/docs/custom-variables/" target="_blank" title="What is a custom variable">custom variable</a> index between 1 and 5 where the payment method tracking should get stored. (set 0 to disable this feature)</em>',
'SHOP_MODULE_hdi-piwik_blFirstReferrerConv' => '<h3>First Referrer Conversion Attribution</h3><em>If set to true attribute a conversion to the first referrer. By default, conversion is attributed to the most recent referrer.</em>',
'SHOP_MODULE_hdi-piwik_blEnableJSErrorTrackin' => '<h3>Track uncatched frontend JavaScript errors</h3><em>Piwik can track uncaught JavaScript errors from the Shop frontend in it\'s <a href="http://piwik.org/docs/event-tracking/" target="_blank" title="Event Tracking Documentation">Event Tracking</a>.<br>Notice, this feature requires Piwik >= 2.2.0</em>',
// Custom variable labels
'SHOP_MODULE_GROUP_hdi-piwik_CustomVars' => 'Label for custom variables',
'SHOP_MODULE_hdi-piwik_CUSTOM_NEWSLETTER' => 'Newsletter',
'SHOP_MODULE_hdi-piwik_sCustomvarNewsletter' => 'This text will be shown in Piwik\'s custom variable for the newsletter registration tracking.',
'SHOP_MODULE_hdi-piwik_CUSTOM_PAYMENT' => 'Payment method',
'SHOP_MODULE_hdi-piwik_sCustomvarPayment' => 'This text will be shown in Piwik\'s custom variable for the payment method tracking.',
'SHOP_MODULE_hdi-piwik_NEWSLETTER_ACCOUNT' => 'View account newsletter settings',
'SHOP_MODULE_hdi-piwik_sNewsletterAccount' => 'This text will be shown in Piwik if a customer has viewed the newsletter settings page in the account settings.',
'SHOP_MODULE_hdi-piwik_NEWSLETTER_ACCOUNT_ON' => 'Account newsletter activated',
'SHOP_MODULE_hdi-piwik_sNewsletterAccountOn' => 'This text will be shown in Piwik if a customer has activated the newsletter in the account settings.',
'SHOP_MODULE_hdi-piwik_NEWSLETTER_ACCOUNT_OFF' => 'Account newsletter deactivated',
'SHOP_MODULE_hdi-piwik_sNewsletterAccountOff' => 'This text will be shown in Piwik if a customer has deactivated the newsletter in the account settings.',
'SHOP_MODULE_hdi-piwik_NEWSLETTER_VIEWED' => 'Viewed',
'SHOP_MODULE_hdi-piwik_sNewsletterViewed' => 'This text will be shown in Piwik if a customer has seen the newsletter registration page.',
'SHOP_MODULE_hdi-piwik_NEWSLETTER_ORDERED' => 'Ordered',
'SHOP_MODULE_hdi-piwik_sNewsletterOrdered' => 'This text will be shown in Piwik if a customer has submitted the newsletter registration form.',
'SHOP_MODULE_hdi-piwik_NEWSLETTER_ACTIVATED' => 'Activated',
'SHOP_MODULE_hdi-piwik_sNewsletterActivated' => 'This text will be shown in Piwik if a customer has confirmed the newsletter double-opt-in.',
'SHOP_MODULE_hdi-piwik_NEWSLETTER_CANCELED' => 'Canceled',
'SHOP_MODULE_hdi-piwik_sNewsletterCanceled' => 'This text will be shown in Piwik if a customer has signed-off from the newsletter.',
// additional mapping
'SHOP_MODULE_GROUP_hdi-piwik_Params' => 'Additional parameter mapping',
'SHOP_MODULE_hdi-piwik_aParamMapVisit' => '<h3>Parameter mapping for visit-scope custom variables</h3><em>You can define addition URL parameters that will get stored in Piwik\'s visit-scope <a href="http://piwik.org/docs/custom-variables/" target="_blank" title="What is a custom variable">custom variable</a>. (let it empty if you do not need this feature)<p><b>Configuration:</b> parameter => index|Label<br><code>foo => 4|Parameter 1<br>foo2 => 5|Parameter2</code><br>On a request like "http://www.shop.tld/index.php?foo=bar" Piwik will store in the custom variable with the the index 4 and the label "Parameter 1" the value "bar".<br><b>Caution</b> one visit-scope index is already used for payment method tracking!</p></em>',
'SHOP_MODULE_hdi-piwik_aParamMapPage' => '<h3>Parameter mapping for page-scope custom variables</h3><em>You can define additional URL parameters that will get stored in Piwik\'s page-scope <a href="http://piwik.org/docs/custom-variables/" target="_blank" title="What is a custom variable">custom variable</a>. (let it empty if you do not need this feature)<br> See visit-scope parameter mapping for instructions.<br><b>Caution</b> one page-scope index is already used for the newsletter registration tracking!</em>'
);
