<div style="float:right;margin-top: 30px;">
    <div style="text-align: right;">
        [{foreach from=$oView->lvGetAgeIcons() item="aAge"}]
            <span><img src="[{$aAge.url}]" title="[{$aAge.title}]"></span>
        [{/foreach}]
    </div>
    <br>
    <div style="text-align: right;">
        [{foreach from=$oView->lvGetCompatibilityIcons() item="aCompatibilty"}]
            <span><img src="[{$aCompatibilty.url}]" title="[{$aCompatibilty.title}]"></span>
        [{/foreach}]
    </div>
</div>
[{$smarty.block.parent}]
