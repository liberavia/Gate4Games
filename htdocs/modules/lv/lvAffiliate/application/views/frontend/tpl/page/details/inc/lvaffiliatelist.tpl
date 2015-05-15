<div class="widgetBox">
    [{foreach from=$oView->lvGetAffiliateDetails() item="aLvAffiliateInfo"}]
        [{assign var="oAffiliateProduct"    value=$aLvAffiliateInfo.product}]
        [{assign var="oAffiliateVendor"     value=$aLvAffiliateInfo.vendor}]
        <div class="lvAffiliateDetailsBox widgetBox">
            <div class="lvAffiliateDetailsPrice">
                <label id="productPrice" class="price">
                    <strong>
                        <span>[{oxprice price=$oAffiliateProduct->getPrice() currency=$currency}]</span>
                        [{if $oView->isVatIncluded() }]
                            <span>*</span>
                        [{/if}]
                    </strong>
                </label>
            </div>
            <div class="lvAffiliateDetailsTitle">
                [{$oAffiliateProduct->oxarticles__oxtitle->value}] [{oxmultilang ident="LVAFFILIATE_AT_VENDOR"}] [{$oAffiliateVendor->getTitle()}]
            </div>
            <div class="lvAffiliateDetailsVendorLogo">
                <img src="[{$oAffiliateVendor->getIconUrl()}]">
            </div>
            <div class="lvAffiliateDetailsVendorToOffer">
                <a href="http://[{$oAffiliateProduct->oxarticles__oxexturl->value}]"></a>
            </div>
        </div>
    [{/foreach}]
</div>