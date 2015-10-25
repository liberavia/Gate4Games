[{$smarty.block.parent}]
[{if $oDetailsProduct->oxarticles__lvigdb_rating->value > 0}]
    <div id="lvigdb_rating_container">
        [{if $oDetailsProduct->lvGetIGDBLink()}]<a href="[{$oDetailsProduct->lvGetIGDBLink()}]" target="_blank">[{/if}][{oxmultilang ident="LV_IGDB_RATING"}]:[{if $oDetailsProduct->lvGetIGDBLink()}]</a>[{/if}] <span id="lvigdb_rating_result">[{$oDetailsProduct->oxarticles__lvigdb_rating->value}]</span><span id="lvigdb_rating_max"> / 10</span>
    </div>
[{/if}]