<div class="tobasketFunction clear">
    [{foreach from=$oView->lvGetCompatibilityIcons() item="aCompatibilty"}]
        <span><img src="[{$aCompatibilty.url}]" title="[{$aCompatibilty.title}]"></span>
    [{/foreach}]
</div>
