<div class="buttonBox">
    [{foreach from=$oView->lvGetCompatibilityInformation() item="aCompatibilty"}]
        <span><img src="[{$aCompatibilty.iconurl}]" title="[{$aCompatibilty.title}]"></span>
    [{/foreach}]
</div>
