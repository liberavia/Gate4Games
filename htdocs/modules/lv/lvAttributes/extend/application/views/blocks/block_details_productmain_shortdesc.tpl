<div style="float:right;">
    [{foreach from=$oView->lvGetCompatibilityIcons() item="aCompatibilty"}]
        <span><img src="[{$aCompatibilty.url}]" title="[{$aCompatibilty.title}]"></span>
    [{/foreach}]
    <br>
    [{foreach from=$oView->lvGetAgeIcons() item="aAge"}]
        <span><img src="[{$aAge.url}]" title="[{$aAge.title}]"></span>
    [{/foreach}]
</div>
[{$smarty.block.parent}]
