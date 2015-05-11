[{**
 * hdi-piwik: OXID module to include Piwik tracking code.
 * Copyright (C) 2011-2014 HEINER DIRECT GmbH & Co. KG
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
 * @author		Klaus Weidenbach (HEINER DIRECT GmbH & Co. KG)
 * @author		Rafael Dabrowski (HEINER DIRECT GmbH & Co. KG)
 * @author		HEINER DIRECT GmbH & Co. KG <oxid@heiner-direct.com>
 * @package		HDI
 * @subpackage	hdi-piwik
 *}]
[{$smarty.block.parent}]
[{* BEGIN Settings *}]
[{*The Piwki site ID for the actual OXID eShop*}]
[{assign var="piwik_siteid" value=$oViewConf->getPiwikPageid()}]
[{*The URL to your Piwik installation without http:// and last / *}]
[{assign var="piwik_url" value=$oViewConf->getPiwikUrl()}]
[{*The goal ID for newsletteractivation Tracking; 0 = No goal defined*}]
[{assign var="piwik_NewsletterGoal" value=$oViewConf->getPiwikNlgoalid()}]
[{*The index of the Piwik custom variable for newsletter registration tracking; 0 = disabled*}]
[{assign var="piwik_CustomIndexNewsletter" value=$oViewConf->getPiwikCustomIndexNewsletter()}]
[{*The index of the Piwik custom variable for payment tracking; 0 = disabled*}]
[{assign var="piwik_CustomIndexPayment" value=$oViewConf->getPiwikCustomIndexPayment()}]
[{*Enable first referrer conversion attribution; boolean*}]
[{assign var="piwik_FirstReferrerConversionAttribution" value=$oViewConf->getFirstReferrerConv()}]
[{*Enable JavaScript Error tracking; boolean*}]
[{assign var="piwik_EnableJSErrorTracking" value=$oViewConf->getPiwikEnableJSError()}]
[{*Additional configured custom variables visit-scope*}]
[{ assign var="piwik_paramVisit" value=$oViewConf->addPiwikParamMapVisit() }]
[{*Additional configured custom variables page-scope*}]
[{ assign var="piwik_paramPage" value=$oViewConf->addPiwikParamMapPage() }]
[{* END Settings *}]
<!-- Piwik Code included through hdi-piwik -->
<script type="text/javascript">
var _paq = _paq || [];
[{* Set First Referrer Conversion Attribution, available since Piwik >= 1.2.0 *}][{strip}]
[{if $piwik_FirstReferrerConversionAttribution}]
	_paq.push(['setConversionAttributionFirstReferrer',true]);
[{/if}][{/strip}]
[{ $piwik_paramVisit }][{ $piwik_paramPage }][{strip}]
[{* Ecommerce category page *}]
[{if $oView->getClassName() == "alist"}]
	_paq.push(['setEcommerceView', false, false, '[{$oView->getTitle()}]' ]);
[{/if}]

[{* Ecommerce detail page *}]
[{if $oView->getClassName() == "details"}]
	[{assign var=category value=$oDetailsProduct->getCategory()}]
	_paq.push(['setEcommerceView',
		'[{$oDetailsProduct->oxarticles__oxartnum->value}]',
		'[{$oDetailsProduct->oxarticles__oxtitle->value}]',
		'[{$category->oxcategories__oxtitle->value}]'
	]);
[{/if}]

[{* 404 Page Not Found *}]
[{if $oView->getClassName() == "oxUBase" || $oView->getClassName() == ""}]
	_paq.push(['setDocumentTitle',
		'404/URL = '+String(document.location.pathname+document.location.search).replace(/\//g,"%2f") + '/From = ' + String(document.referrer).replace(/\//g,"%2f")
	]);
[{/if}]

[{* Newsletter *}]
[{* Account newsletter settings *}]
[{if $oView->getClassName() == "account_newsletter" }]
[{* Viewed account newsletter settings page *}]
	[{if $oView->getSubscriptionStatus() == 0}]
		_paq.push(['setCustomVariable', [{$piwik_CustomIndexNewsletter}], "[{ $oViewConf->getPiwikText('Newsletter') }]", "[{ $oViewConf->getPiwikText('NewsletterAcc') }]", "page"]);
[{* enabled newsletter in account settings *}]
	[{elseif $oView->getSubscriptionStatus() == 1}]
		_paq.push(['setCustomVariable', [{$piwik_CustomIndexNewsletter}], "[{ $oViewConf->getPiwikText('Newsletter') }]", "[{ $oViewConf->getPiwikText('NewsletterAccOn') }]", "page"]);
[{* disabled newsletter in account settings *}]
	[{elseif $oView->getSubscriptionStatus() == -1}]
		_paq.push(['setCustomVariable', [{$piwik_CustomIndexNewsletter}], "[{ $oViewConf->getPiwikText('Newsletter') }]", "[{ $oViewConf->getPiwikText('NewsletterAccOff') }]", "page"]);
	[{/if}]
[{/if}]
[{* Newsletter registration form *}][{/strip}]
[{if $oView->getClassName() == "newsletter"}][{strip}]
	[{* Goal tracking if configured *}]
	[{if $oView->getNewsletterStatus() == 2 && $piwik_NewsletterGoal > 0}]
		_paq.push(['trackGoal', [{$piwik_NewletterGoal}]);
	[{/if}][{/strip}]
[{* Custom Variable: Detailed newsletter registration tracking if configured *}][{strip}]
	[{if $piwik_CustomIndexNewsletter > 0}]
		[{* Customer visited newsletter registration form *}]
		[{if $oView->getNewsletterStatus() == 4 || !$oView->getNewsletterStatus()}]
			_paq.push(['setCustomVariable', [{$piwik_CustomIndexNewsletter}], "[{ $oViewConf->getPiwikText('Newsletter') }]", "[{ $oViewConf->getPiwikText('Newsletter4') }]", "page"]);
		[{* Customer submits newsletter registration form *}]
		[{elseif $oView->getNewsletterStatus() == 1}]
			_paq.push(['setCustomVariable', [{$piwik_CustomIndexNewsletter}], "[{ $oViewConf->getPiwikText('Newsletter') }]", "[{ $oViewConf->getPiwikText('Newsletter1') }]", "page"]);
		[{* Customer confirmed double-opt-in *}]
		[{elseif $oView->getNewsletterStatus() == 2}]
			_paq.push(['setCustomVariable', [{$piwik_CustomIndexNewsletter}], "[{ $oViewConf->getPiwikText('Newsletter') }]", "[{ $oViewConf->getPiwikText('Newsletter2') }]", "page"]);
		[{* Customer submits newsletter unsubscribe form *}]
		[{elseif $oView->getNewsletterStatus() == 3}]
			_paq.push(['setCustomVariable', [{$piwik_CustomIndexNewsletter}], "[{ $oViewConf->getPiwikText('Newsletter') }]", "[{ $oViewConf->getPiwikText('Newsletter3') }]", "page"]);
		[{/if}]
	[{/if}][{/strip}]
[{/if}]
[{* Ecommerce add item to basket and Ecommerce view basket *}]
[{if $oxcmp_basket->isNewItemAdded() || $oView->getClassName() == "basket" }]
[{foreach key=basketindex from=$oxcmp_basket->getContents() item=item name=basketContents}][{strip}]
		[{assign var="product" value=$item->getArticle()}]
		[{assign var=itemprice value=$item->getUnitPrice()}]
		[{assign var=category value=$product->getCategory()}]
		_paq.push(['addEcommerceItem',
			'[{$product->oxarticles__oxartnum->value}]',
			'[{$item->getTitle()}]',
			'[{$category->oxcategories__oxtitle->value}]',
			[{$itemprice->getBruttoPrice()}],
			[{$item->getAmount()}]
		]);[{/strip}]
[{/foreach}][{strip}]
	[{assign var=price value=$oxcmp_basket->getPrice()}]
	_paq.push(['trackEcommerceCartUpdate',
		[{$price->getBruttoPrice()}]
	]);[{/strip}]
[{/if}]
[{* Custom Variable: Payment method tracking if configured *}]
[{if $oView->getClassName() == "order" && $piwik_CustomIndexPayment > 0}][{strip}]
	[{assign var="payment" value=$oView->getPayment() }]
	_paq.push(['setCustomVariable',
		[{$piwik_CustomIndexPayment}],
		'[{ $oViewConf->getPiwikText('Payment') }]',
		'[{ $payment->oxpayments__oxdesc->value }]',
		'visit'
	]);[{/strip}]
[{/if}]
[{* Ecommerce conversion thank you page *}]
[{if $oView->getClassName() == "thankyou"}]
[{foreach key=basketindex from=$basket->getContents() item=item name=basketContents}][{strip}]
	[{assign var=product value=$item->getArticle()}]
	[{assign var=itemprice value=$item->getUnitPrice()}]
	[{assign var=category value=$product->getCategory()}]
	_paq.push(['addEcommerceItem',
		'[{$product->oxarticles__oxartnum->value}]',
		'[{$item->getTitle()}]',
		'[{$category->oxcategories__oxtitle->value}]',
		[{$itemprice->getBruttoPrice()}],
		[{$item->getAmount()}]
	]);[{/strip}]
[{/foreach}][{strip}]
	[{assign var=oprice value=$order->getOrderTsProtectionPrice()}]
	[{assign var=delPrice value = $order->getOrderDeliveryPrice()}]
	[{assign var=payPrice value = $order->getOrderPaymentPrice()}]
	[{assign var=wrapPrice value = $order->getOrderWrappingPrice()}]
	_paq.push(['trackEcommerceOrder',
		'[{$order->oxorder__oxordernr->value}]',
		[{$order->getTotalOrderSum()}],
		[{$basket->getDiscountedProductsBruttoPrice()}],
		([{$order->oxorder__oxartvatprice1->value}]+[{$order->oxorder__oxartvatprice2->value}]),
		([{$delPrice->getBruttoPrice()}]+[{$payPrice->getBruttoPrice()}]+[{$wrapPrice->getBruttoPrice()}]),
		[{$order->oxorder__oxdiscount->value}]
	]);[{/strip}]
[{/if}][{strip}]

[{* Shop Search *}]
[{if $oView->getClassName() == "search"}]
	_paq.push(['trackSiteSearch',
		'[{$oView->getSearchParamForHtml()}]',
		false,
		[{$oView->getArticleCount()}]
	]);
[{else}]
	_paq.push(['trackPageView']);
[{/if}][{/strip}]
_paq.push(['enableLinkTracking']);
[{* Enable JavaScript Error Tracking, available since Piwik >= 2.2.0 *}][{strip}]
[{if $piwik_EnableJSErrorTracking}]
	_paq.push(['enableJSErrorTracking']);
[{/if}][{/strip}]
(function(){
	var u=(("https:" == document.location.protocol) ? "https" : "http") + "://[{$piwik_url}]/";
	_paq.push(['setTrackerUrl', u+'piwik.php']);
	_paq.push(['setSiteId', [{$piwik_siteid}]]);
	var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript';
	g.defer=true; g.async=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
})();
</script>
<!-- End Piwik Code -->