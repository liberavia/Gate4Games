[{oxscript include="js/libs/cloudzoom.js" priority=10}]
[{if $oView->lvGetYouTubeMediaEmbed()}]
    <div class="picture">
        [{$oView->lvGetYouTubeMediaEmbed()}]
    </div>
[{elseif $oView->showZoomPics()}]
    [{oxscript include="js/widgets/oxmodalpopup.js" priority=10 }]
    [{oxscript add="$('#zoomTrigger').oxModalPopup({target:'#zoomModal'});"}]
    [{oxscript add="$('#zoom1').oxModalPopup({target:'#zoomModal'});"}]
    <a id="zoomTrigger" rel="nofollow" href="#">Zoom</a>
    [{oxscript add="$('#zoom1').attr( 'rel', $('#zoom1').attr('data-zoomparams'));"}]
    [{oxscript add="$('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();"}]
    <div class="picture">
        <a href="[{$oPictureProduct->getMasterZoomPictureUrl(1)}]" class="cloud-zoom" id="zoom1" rel='' data-zoomparams="adjustY:-2, zoomWidth:'354', fixZoomWindow:'390', trImg:'[{$oViewConf->getImageUrl('dot.png')}]', loadingText:'[{oxmultilang ident="LOADING"}]'">
            <img src="[{$oView->getActPicture()}]" alt="[{$oPictureProduct->oxarticles__oxtitle->value|strip_tags}] [{$oPictureProduct->oxarticles__oxvarselect->value|strip_tags}]">
        </a>
    </div>
[{else}]
    <div class="picture">
        <img src="[{$oView->getActPicture()}]" alt="[{$oPictureProduct->oxarticles__oxtitle->value|strip_tags}] [{$oPictureProduct->oxarticles__oxvarselect->value|strip_tags}]">
    </div>
[{/if}]
 
