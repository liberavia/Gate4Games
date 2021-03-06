[{assign var="currency" value=$oView->getActCurrency()}]
<div class="priceBlock">
    <div class="lvVerticalMiddle lvPrice">
        [{if $oView->lvGetBestAffiliateDetails()}]
            [{assign var="aAffiliateDetails" value=$oView->lvGetBestAffiliateDetails()}]
            [{assign var="oAffiliateProduct" value=$aAffiliateDetails.product}]
            [{assign var="oAffiliateVendor" value=$aAffiliateDetails.vendor}]
            <a href="[{$oAffiliateProduct->oxarticles__oxexturl->rawValue}]" target="_blank" style="text-decoration:none;">
                [{if ( method_exists($product,'lvGetMostExpansiveTPrice') && $product->lvGetMostExpansiveTPrice() ) || $oAffiliateProduct->getTPrice()}]
                    <span>
                        [{oxmultilang ident="LV_OLD_PRICE"}] [{if method_exists($product,'lvGetMostExpansiveTPrice')}]<s>[{oxprice price=$product->lvGetMostExpansiveTPrice() currency=$currency}]</s>[{else}]<s>[{oxprice price=$oAffiliateProduct->getTPrice() currency=$currency}]</s>[{/if}]
                    </span>
                [{/if}]
                <br>
                <span style="font-size:20px;">[{$smarty.capture.product_price}]</span>
                [{oxmultilang ident="LV_GO_DIRECTLY_TO_BEST_OFFER_OF"}]
                <img width="15" height="15" src="[{$oAffiliateVendor->getIconUrl()}]" title="[{$oAffiliateProduct->oxarticles__oxtitle->value}] [{oxmultilang ident="LVAFFILIATE_AT_VENDOR"}] [{$oAffiliateVendor->getTitle()}]" alt="[{$oAffiliateVendor->getTitle()}]">
            </a>
        [{/if}]
    </div>
    <div style="margin-top: 8px;">
        [{foreach from=$oView->lvGetSumCompatibilityInformation(true) item="aCompatibilty"}]
            <span><img style="[{$aCompatibilty.greyout}]" src="[{$aCompatibilty.iconurl}]" title="[{$aCompatibilty.title}]"></span>
        [{/foreach}]
    </div>
    <div style="margin-top: 8px;">
        [{if $oView->lvGetBestAffiliateDetails()}]
            <a href="[{$oAffiliateProduct->oxarticles__oxexturl->rawValue}]" target="_blank">
                <button class="submitButton largeButton">[{oxmultilang ident="LV_DIRECTLY_TO_SHOP"}]</button>
            </a>
        [{/if}]
        <a href="[{$_productLink}]">
            <button class="submitButton largeButton">[{oxmultilang ident="LV_DETAILS"}]</button>
        </a>
    </div>
</div>
