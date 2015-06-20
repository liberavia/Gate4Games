[{capture name="slides"}]
    [{foreach from=$oView->getManufacturerForSlider() item=oManufacturer}]
        [{if $oManufacturer->oxmanufacturers__oxicon->value }]
        [{counter assign="slideCount"}]
            <li>
                <a class="sliderHover" href="[{$oManufacturer->getLink()}]">
                    <img src="[{$oManufacturer->getIconUrl()}]" alt="[{$oManufacturer->oxmanufacturers__oxtitle->value}]">
                </a>
            </li>
        [{/if}]
    [{/foreach}]
[{/capture}]
[{if $slideCount > 6 }]
    [{oxscript include="js/libs/jcarousellite.js"}]
    [{oxscript include="js/widgets/oxmanufacturerslider.js" priority=10 }]
    [{oxscript add="$( '#manufacturerSlider' ).oxManufacturerSlider();"}]
    <div class="itemSlider">
        <a class="prevItem slideNav" href="#" rel="nofollow"><span class="slidePointer">&laquo;</span><span class="slideBg"></span></a>
        <a class="nextItem slideNav" href="#" rel="nofollow"><span class="slidePointer">&raquo;</span><span class="slideBg"></span></a>
        <div id="manufacturerSlider">
            <ul>
                [{$smarty.capture.slides}]
            </ul>
        </div>
    </div>  
[{/if}]