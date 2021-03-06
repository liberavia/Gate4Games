<div style="float:right;height:198px;width:250px;position: absolute;right:0px;bottom:95px;">
    <div style="text-align: right;">
        [{foreach from=$oView->lvGetAgeIcons() item="aAge"}]
            <span><img src="[{$aAge.url}]" title="[{$aAge.title}]"></span>
        [{/foreach}]
    </div>
    <br>
    <div style="text-align: right;position:absolute;bottom:0px;width:100%;">
        <table border="0" style="width:100%;height:100%;padding:3px;">
            [{if $oView->lvGetSumCompatibilityInformation()}]
                <tr>
                    <td align="right">
                            [{oxmultilang ident="LV_ATTR_AVAILABLE_FOR_SUM"}]
                    </td>
                    <td>
                        [{foreach from=$oView->lvGetSumCompatibilityInformation() item="aCompatibilty"}]
                            <span><img src="[{$aCompatibilty.iconurl}]" title="[{$aCompatibilty.title}]"></span>
                        [{/foreach}]
                    </td>
                </tr>
            [{/if}]
            [{if $oView->lvGetBestAffiliateDetails()}]
                <tr>
                    <td align="right">
                        [{oxmultilang ident="LV_ATTR_AVAILABLE_FOR_BEST"}]
                    </td>
                    <td>
                        [{assign var="aAffiliateDetails" value=$oView->lvGetBestAffiliateDetails()}]
                        [{assign var="oAffiliateProduct" value=$aAffiliateDetails.product}]
                        [{assign var="oAffiliateVendor" value=$aAffiliateDetails.vendor}]
                        [{foreach from=$oAffiliateProduct->lvGetCompatibilityInformation() item="aCompatibilty"}]
                            <span><img src="[{$aCompatibilty.iconurl}]" title="[{$aCompatibilty.title}]"></span>
                        [{/foreach}]
                    </td>
                </tr>
            [{/if}]
        </table>
    </div>
</div>
[{$smarty.block.parent}]
