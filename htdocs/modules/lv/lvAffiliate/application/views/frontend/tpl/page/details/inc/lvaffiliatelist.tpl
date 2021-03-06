<div class="widgetBox">
    [{foreach from=$oView->lvGetAffiliateDetails() item="aLvAffiliateInfo"}]
        [{assign var="oAffiliateProduct"    value=$aLvAffiliateInfo.product}]
        [{assign var="oAffiliateVendor"     value=$aLvAffiliateInfo.vendor}]
        <div class="lvAffiliateDetailsBox widgetBox">
            <table class="lvAffiliateDetailsTable">
                <tr>
                    <td class="lvAffiliateDetailsPrice">
                        <a href="[{$oAffiliateProduct->oxarticles__oxexturl->rawValue}]" target="_blank">
                            <label id="productPrice" class="price">
                                <strong>
                                    <span>[{oxprice price=$oAffiliateProduct->getPrice() currency=$currency}]</span>
                                    [{if $oView->isVatIncluded() }]
                                        <span>*</span>
                                    [{/if}]
                                </strong>
                            </label>
                        </a>
                    </td>
                    <td class="lvAffiliateDetailsIcon">
                        <a href="[{$oAffiliateProduct->oxarticles__oxexturl->rawValue}]" target="_blank"><img src="[{$oAffiliateVendor->getIconUrl()}]"></a>
                    </td>
                    <td class="lvAffiliateDetailsTitle">
                        <a href="[{$oAffiliateProduct->oxarticles__oxexturl->rawValue}]" target="_blank">
                            [{$oAffiliateProduct->oxarticles__oxtitle->value}] [{oxmultilang ident="LVAFFILIATE_AT_VENDOR"}] <strong>[{$oAffiliateVendor->getTitle()}]</strong>
                        </a>
                    </td>
                    <td class="lvAffiliateDetailsToOffer">
                        <a href="[{$oAffiliateProduct->oxarticles__oxexturl->rawValue}]" target="_blank"><button class="submitButton largeButton">[{oxmultilang ident="LVAFFILIATE_TO_OFFER"}]</button></a>
                        [{if method_exists( $oAffiliateProduct, 'lvGetCompatibilityInformation' )}]
                            <div style="padding-top:10px ; text-align: right;">
                                [{foreach from=$oAffiliateProduct->lvGetCompatibilityInformation() item="aCompatibilty"}]
                                    <span><img src="[{$aCompatibilty.iconurl}]" title="[{$aCompatibilty.title}]"></span>
                                [{/foreach}]
                            </div>                        
                        [{/if}]
                    </td>
                </tr>
            </table>
        </div>
    [{/foreach}]
</div>