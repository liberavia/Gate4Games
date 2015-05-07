[{if $oView->lvHasMoreMedia()}]
    <script type="text/javascript">
        function lvChangeDetailsToPicture( sTargetPictureUrl ) {
            $( '#lvDetailsVideoStd' ).hide();
            $( '#lvDetailsPictureStd' ).show();
            $( '#lvDetailsPictureStd' ).empty();
            var sPictureCode = '<img src="' + sTargetPictureUrl + '" style="height:auto;width:auto;max-height:[{$oView->lvGetDetailsImageMaxHeight()}]px;max-width:[{$oView->lvGetDetailsImageMaxHeight()}]px;" alt="[{$oPictureProduct->oxarticles__oxtitle->value|strip_tags}] [{$oPictureProduct->oxarticles__oxvarselect->value|strip_tags}]">';
            $( '#lvDetailsPictureStd' ).append( sPictureCode );
        }
        
        function lvChangeDetailsToVideo( iVideoNr ) {
            $( '#lvDetailsVideoStd' ).hide();
            $( '#lvDetailsVideoStd' ).show();
            iVideoNr = parseInt(iVideoNr);
            $( '#lvDetailsPictureStd' ).empty();
            for ( var iCurrentVideoNr=1;iCurrentVideoNr<=6;iCurrrentVideoNr++ ) {
                var sCurrentVideoNr =iCurrentVideoNr.toString();
                if ( $( '#detailsvideoiframe_' + sCurrentVideoNr ).length > 0 ) ) {
                    if ( iCurrentVideoNr == iVideoNr ) {
                        $( '#detailsvideoiframe_' + sCurrentVideoNr ).show();
                    }
                    else {
                        $( '#detailsvideoiframe_' + sCurrentVideoNr ).hide();
                    }
                }
            }
        }
    </script>

    <div class="otherPictures" id="morePicsContainer">
        <div class="shadowLine"></div>
        <ul class="clear">
        [{oxscript add="var aMorePic=new Array();"}]
        [{foreach from=$oView->lvGetAllMedia() key=iPicNr item=aLvExtUrl name=sMorePics}]
            [{if $aLvExtUrl.mediatype == 'youtube'}]
                <li>
                    <a id="morePics_[{$smarty.foreach.sMorePics.iteration}]" onclick="lvChangeDetailsToVideo( '[{$smarty.foreach.sMorePics.iteration}]' )">
                        <span class="marker"><img src="[{$oViewConf->getImageUrl('marker.png')}]" alt=""></span>
                        <span class="artIcon"><img src="[{$aLvExtUrl.iconurl}]" style="height:auto;width:auto;max-height:[{$aLvExtUrl.iconheight}]px;max-width:[{$aLvExtUrl.iconwidth}]px;" alt=""></span>
                    </a>
                </li>
            [{elseif $aLvExtUrl.mediatype == 'extpic'}]
                <li>
                    <a id="morePics_[{$smarty.foreach.sMorePics.iteration}]" onclick="lvChangeDetailsToPicture( '[{$aLvExtUrl.iconurl}]' )">
                        <span class="marker"><img src="[{$oViewConf->getImageUrl('marker.png')}]" alt=""></span>
                        <span class="artIcon"><img src="[{$aLvExtUrl.iconurl}]" style="height:auto;width:auto;max-height:[{$aLvExtUrl.iconheight}]px;max-width:[{$aLvExtUrl.iconwidth}]px;" alt=""></span>
                    </a>
                </li>
            [{/if}]
        [{/foreach}]
        </ul>
    </div>
[{/if}]
