[{block name="widget_header_search_form"}]
[{if $oView->showSearch() }]
    [{oxscript include="js/widgets/oxinnerlabel.js" priority=10 }]
    [{oxscript add="$( '#searchParam' ).oxInnerLabel();"}]
    <form class="search" action="[{ $oViewConf->getSelfActionLink() }]" method="get" name="search">
        <div class="searchBox">
            [{ $oViewConf->getHiddenSid() }]
            <input type="hidden" name="cl" value="search">
            [{assign var="currency" value=$oView->getActCurrency()}]
            [{if $currency->id}]
                <input type="hidden" name="cur" value="[{$currency->id}]">
            [{/if}]
            [{block name="header_search_field"}]
            <label for="searchParam" class="innerLabel">[{oxmultilang ident="LV_SEARCH_IN_CURRENTLY"}] <span class="G4GCiSearchColor">[{$oViewConf->lvGetAmountArticles()}]</span> [{oxmultilang ident="LV_GAME_TITLES"}]...</label>
                <input class="textbox" type="text" id="searchParam" name="searchparam" value="[{$oView->getSearchParamForHtml()}]">
            [{/block}]
            <button class="searchButton" type="submit">[{oxmultilang ident="SEARCH" }]</button>
        </div>
    </form>
[{/if}]
[{/block}]