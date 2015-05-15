<div class="widgetBox">
    [{foreach from=$oView->lvGetAffiliateDetails() item="aLvAffiliateInfo"}]
        [{assign var="oAffiliateProduct"    value=$aLvAffiliateInfo.product}]
        [{assign var="oAffiliateVendor"     value=$aLvAffiliateInfo.vendor}]
        <div class="lvAffiliateDetailsBox widgetBox">
            Title: [{$oAffiliateProduct->oxarticles__oxtitle->value}]<br>
            Vendor: [{$oAffiliateVendor->getTitle()}]<br>
            Price: [{$oAffiliateProduct->getFPrice()}] <br>
            VendorLogo: <img src="[{$oAffiliateVendor->getIconUrl()}]"><br>
            VendorLink <a href="http://[{$oAffiliateProduct->oxarticles__oxexturl->value}]">[{$oAffiliateProduct->oxarticles__oxexturl->value}]</a><br>
        </div>
    [{/foreach}]
</div>