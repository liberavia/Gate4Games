[{foreach from=$oView->lvGetSumCompatibilityInformation() item="aCompatibilty"}]
    <div>
        <div><img src="[{$aCompatibilty.iconurl}]"> [{$aCompatibilty.targetsys_trans}]:</div>
        <div>
            [{if $aCompatibilty.description != ''}]
                [{$aCompatibilty.description}]
            [{else}]
                [{oxmultilang ident="LVATTR_SYSREQ_NOTYETKNOWN"}]
            [{/if}]
        </div>
    </div>
[{/foreach}]