[{oxifcontent ident="oxsecurityinfo" object="oCont"}]
    [{if $oView->isEnabled()}]
        [{oxscript include="js/libs/cookie/jquery.cookie.js"}]
        [{oxscript include="js/widgets/oxcookienote.js"}]
        <div id="cookieNote">
            <div class="notify">
                [{oxmultilang ident='LV_COOKIE_NOTE'}]
                <span class="dismiss"><a href="#" title="[{oxmultilang ident='CLOSE'}]"><button class="submitButton largeButton">OK</button></a></span>
            </div>
        </div>
        [{oxscript add="$('#cookieNote').oxCookieNote();"}]
    [{/if}]
[{/oxifcontent}]
[{oxscript widget=$oView->getClassName()}]
