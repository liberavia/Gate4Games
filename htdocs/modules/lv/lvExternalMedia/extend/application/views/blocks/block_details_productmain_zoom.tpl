[{if $oView->lvGetYouTubeMediaEmbed()}]
    <div id="lvDetailsPictureStd" class="picture">
        [{$oView->lvGetYouTubeMediaEmbed()}]
    </div>
[{else}]
    <div id="lvDetailsPictureStd" class="picture">
        <img src="[{$oView->lvGetFirstPictureUrl()}]" style="height:auto;width:auto;max-height:[{$oView->lvGetDetailsImageMaxHeight()}]px;max-width:[{$oView->lvGetDetailsImageMaxHeight()}]px;" alt="[{$oPictureProduct->oxarticles__oxtitle->value|strip_tags}] [{$oPictureProduct->oxarticles__oxvarselect->value|strip_tags}]">
    </div>
[{/if}]
 
