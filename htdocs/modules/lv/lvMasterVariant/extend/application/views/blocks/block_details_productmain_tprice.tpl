[{$smarty.block.parent}]
[{oxhasrights ident="SHOWARTICLEPRICE"}]
    [{if !$oDetailsProduct->getTPrice()}]
        [{if $oView->lvGetMostExpansiveTPrice()}]
            <p class="oldPrice">
                <strong>[{oxmultilang ident="REDUCED_FROM_2"}] <del>[{oxprice price=$oView->lvGetMostExpansiveTPrice() currency=$currency}]</del></strong>
            </p>
        [{/if}]
    [{/if}]
[{/oxhasrights}]
