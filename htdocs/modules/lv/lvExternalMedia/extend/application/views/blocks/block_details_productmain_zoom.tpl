[{if $oView->lvGetYouTubeMediaEmbed()}]
    <div id="lvDetailsVideoStd" class="picture">
        [{*$oView->lvGetYouTubeMediaEmbed()*}]
        [{foreach from=$oView->lvGetAllMedia(false) item="aLvVideoMedia" name="alvMoreVideos"}]
            [{$aLvVideoMedia.embedurl}]
        [{/foreach}]
        <script type="text/javascript">$( 'detailsvideoiframe_1' ).show();</script>
    </div>
    <div id="lvDetailsPictureStd" class="picture">
    </div>
[{else}]
    <div id="lvDetailsPictureStd" class="picture">
        <img src="[{$oView->lvGetFirstPictureUrl()}]" style="height:auto;width:auto;max-height:[{$oView->lvGetDetailsImageMaxHeight()}]px;max-width:[{$oView->lvGetDetailsImageMaxHeight()}]px;" alt="[{$oPictureProduct->oxarticles__oxtitle->value|strip_tags}] [{$oPictureProduct->oxarticles__oxvarselect->value|strip_tags}]">
    </div>
[{/if}]
 
