[{if $oView->lvHasMoreMedia()}]
    <div class="otherPictures" id="morePicsContainer">
        <div class="shadowLine"></div>
        <ul class="clear">
        [{oxscript add="var aMorePic=new Array();"}]
        [{foreach from=$oView->lvGetAllMedia() key=iPicNr item=sLvExtUrl name=sMorePics}]
            <li>
                <a id="morePics_[{$smarty.foreach.sMorePics.iteration}]" rel="useZoom: 'zoom1', smallImage: '[{$sLvExtUrl}]' " class="cloud-zoom-gallery" href="[{$sLvExtUrl}]">
                    <span class="marker"><img src="[{$oViewConf->getImageUrl('marker.png')}]" alt=""></span>
                    <span class="artIcon"><img src="[{$sLvExtUrl}]" alt=""></span>
                </a>
            </li>
        [{/foreach}]
        </ul>
    </div>
[{/if}]
[{oxscript include="js/widgets/oxmorepictures.js" priority=10}]
[{oxscript add="$('#morePicsContainer').oxMorePictures();"}]