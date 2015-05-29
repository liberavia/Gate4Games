[{foreach from=$oView->lvGetCompatibilityInformation() item="aCompatibilty"}]
    <div>
        <div><img src="[{$aCompatibilty.iconurl}]"> [{$aCompatibilty.targetsys_trans}]:</div>
        <div>[{$aCompatibilty.description}]</div>
    </div>
[{/foreach}]