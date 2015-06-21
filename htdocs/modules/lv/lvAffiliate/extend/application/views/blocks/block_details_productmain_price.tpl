[{$smarty.block.parent}]
[{if $oView->lvGetBestAffiliateDetails()}]
    [{assign var="aAffiliateDetails" value=$oView->lvGetBestAffiliateDetails()}]
    [{assign var="oAffiliateProduct" value=$aAffiliateDetails.product}]
    [{assign var="oAffiliateVendor" value=$aAffiliateDetails.vendor}]
    <br>&nbsp;
    <div class="widgetBox">    
        [{oxmultilang ident="LVAFFILIATE_GO_DIRECTLY_TO_BEST_OFFER"}] [{oxmultilang ident="LVAFFILIATE_AT_VENDOR"}] [{$oAffiliateVendor->getTitle()}]
        <img width="25" height="25" src="[{$oAffiliateVendor->getIconUrl()}]" title="[{$oAffiliateProduct->oxarticles__oxtitle->value}] [{oxmultilang ident="LVAFFILIATE_AT_VENDOR"}] [{$oAffiliateVendor->getTitle()}]" alt="[{$oAffiliateVendor->getTitle()}]">
        <a href="[{$oAffiliateProduct->oxarticles__oxexturl->rawValue}]" target="_blank" style="text-decoration:none;">
            <button class="submitButton largeButton">[{oxmultilang ident="LVAFFILIATE_TO_OFFER"}]</button>
        </a>
    </div>
[{/if}]