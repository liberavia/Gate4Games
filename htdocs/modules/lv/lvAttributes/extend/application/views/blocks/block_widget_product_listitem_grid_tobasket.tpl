<div class="priceBlock">
    [{$smarty.capture.product_price}]
    <div>
        [{assign var='aBestAffiliateDetails' value=$oView->lvGetBestAffiliateDetails()}}
        [{assign var='oAffiliateProduct'    value=$aBestAffiliateDetails.product}]
        [{assign var='oAffiliateVendor'    value=$aBestAffiliateDetails.vendor}]
        <a href="href="[{$oAffiliateProduct->oxarticles__oxexturl->rawValue}]" target="_blank"">
            [{oxmultilang ident="LV_GO_DIRECTLY_TO_BEST_OFFER_OF"}]
            <img src="[{$oAffiliateVendor->getIconUrl()}]" title="[{$oAffiliateProduct->oxarticles__oxtitle->value}] [{oxmultilang ident="LVAFFILIATE_AT_VENDOR"}] [{$oAffiliateVendor->getTitle()}]" alt="[{$oAffiliateVendor->getTitle()}]">
        </a>
    </div>
    <div>
        [{foreach from=$oView->lvGetCompatibilityInformation() item="aCompatibilty"}]
            <span><img src="[{$aCompatibilty.iconurl}]" title="[{$aCompatibilty.title}]"></span>
        [{/foreach}]
    </div>
</div>
