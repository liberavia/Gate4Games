<div class="widgetBox">
    [{foreach from=$oView->lvGetAffiliateDetails() item="aLvAffiliateInfo"}]
        [{assign var="oAffiliateProduct"    value=$aLvAffiliateInfo.product}]
        [{assign var="oAffiliateVendor"     value=$aLvAffiliateInfo.vendor}]
        <div class="lvAffiliateDetailsBox widgetBox">
            <table class="lvAffiliateDetailsTable">
                <tr>
                    <td valign="middle" align="center" class="lvAffiliateDetailsPrice">
                        <label id="productPrice" class="price">
                            <strong>
                                <span>[{oxprice price=$oAffiliateProduct->getPrice() currency=$currency}]</span>
                                [{if $oView->isVatIncluded() }]
                                    <span>*</span>
                                [{/if}]
                            </strong>
                        </label>
                    </td>
                    <td valign="middle" align="center" class="lvAffiliateDetailsIcon">
                        <img src="[{$oAffiliateVendor->getIconUrl()}]">
                    </td>
                    <td valign="middle" align="center" class="lvAffiliateDetailsTitle">
                        [{$oAffiliateProduct->oxarticles__oxtitle->value}] [{oxmultilang ident="LVAFFILIATE_AT_VENDOR"}] [{$oAffiliateVendor->getTitle()}]
                    </td>
                    <td valign="middle" align="center" class="lvAffiliateDetailsToOffer">
                        <a href="http://[{$oAffiliateProduct->oxarticles__oxexturl->value}]"><button class="submitButton largeButton">[{oxmultilang ident="LVAFFILIATE_TO_OFFER"}]</button></a>
                    </td>
                </tr>
            </table>
        </div>
    [{/foreach}]
</div>