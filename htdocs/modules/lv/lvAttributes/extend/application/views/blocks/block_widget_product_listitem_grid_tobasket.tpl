<div class="priceBlock">
    <div class="lvVerticalMiddle">
        [{assign var="aAffiliateDetails" value=$oView->lvGetBestAffiliateDetails()}]
        [{assign var="oAffiliateProduct" value=$aAffiliateDetails.product}]
        [{assign var="oAffiliateVendor" value=$aAffiliateDetails.vendor}]
        <a href="[{$oAffiliateProduct->oxarticles__oxexturl->rawValue}]" target="_blank" style="text-decoration:none;">
            <span style="font-size:20px;">[{$smarty.capture.product_price}]</span>
            [{oxmultilang ident="LV_GO_DIRECTLY_TO_BEST_OFFER_OF"}]
            <img width="15" height="15" src="[{$oAffiliateVendor->getIconUrl()}]" title="[{$oAffiliateProduct->oxarticles__oxtitle->value}] [{oxmultilang ident="LVAFFILIATE_AT_VENDOR"}] [{$oAffiliateVendor->getTitle()}]" alt="[{$oAffiliateVendor->getTitle()}]">
        </a>
    </div>
    <div style="margin-top: 4px;">
        <a href="[{$oAffiliateProduct->oxarticles__oxexturl->rawValue}]" target="_blank">
            <button class="submitButton largeButton">[{oxmultilang ident="LV_DIRECTLY_TO_SHOP"}]</button>
        </a>
        <a href="[{$_productLink}]">
            <button class="submitButton largeButton">[{oxmultilang ident="LV_DETAILS"}]</button>
        </a>
    </div>
    <div style="margin-top: 4px;">
        [{foreach from=$oView->lvGetCompatibilityInformation() item="aCompatibilty"}]
            <span><img src="[{$aCompatibilty.iconurl}]" title="[{$aCompatibilty.title}]"></span>
        [{/foreach}]
    </div>
</div>
