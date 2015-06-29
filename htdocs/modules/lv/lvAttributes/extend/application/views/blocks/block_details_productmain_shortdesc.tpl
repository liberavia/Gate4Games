<div style="float:right;margin-top: 30px;">
    <div style="text-align: right;">
        [{foreach from=$oView->lvGetAgeIcons() item="aAge"}]
            <span><img src="[{$aAge.url}]" title="[{$aAge.title}]"></span>
        [{/foreach}]
    </div>
    <br>
    <div style="text-align: right;">
        [{if $oView->lvGetBestAffiliateDetails()}]
            [{assign var="aAffiliateDetails" value=$oView->lvGetBestAffiliateDetails()}]
            [{assign var="oAffiliateProduct" value=$aAffiliateDetails.product}]
            [{assign var="oAffiliateVendor" value=$aAffiliateDetails.vendor}]
            [{foreach from=$oAffiliateProduct->lvGetCompatibilityInformation() item="aCompatibilty"}]
                <span><img src="[{$aCompatibilty.iconurl}]" title="[{$aCompatibilty.title}]"></span>
            [{/foreach}]
        [{/if}]
    </div>
    <div style="text-align: right;">
        [{foreach from=$oView->lvGetSumCompatibilityInformation() item="aCompatibilty"}]
            <span><img src="[{$aCompatibilty.iconurl}]" title="[{$aCompatibilty.title}]"></span>
        [{/foreach}]
    </div>
</div>
[{$smarty.block.parent}]
