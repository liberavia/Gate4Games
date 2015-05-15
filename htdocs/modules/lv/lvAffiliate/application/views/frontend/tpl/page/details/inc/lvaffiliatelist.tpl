<div class="widgetBox">
    [{foreach from=$oView->lvGetAffiliateDetails() item="aLvAffiliate"}]
        <div class="lvAffiliateDetailsBox widgetBox">[{$aLvAffiliate|@print_r}]</div>
    [{/foreach}]
</div>