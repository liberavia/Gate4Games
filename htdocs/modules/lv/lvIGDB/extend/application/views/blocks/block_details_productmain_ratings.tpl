[{$smarty.block.parent}]
[{if $oDetailsProduct->oxarticles__lvigdb_rating->value > 0}]
    <div id="lvigdb_rating_container">
        [{oxmultilang ident="LV_IGDB_RATING"}]: <span id="lvigdb_rating_result">[{$oDetailsProduct->oxarticles__lvigdb_rating->value}]</span><span id="lvigdb_rating_max"> / 10</span>
    </div>
[{/if}]