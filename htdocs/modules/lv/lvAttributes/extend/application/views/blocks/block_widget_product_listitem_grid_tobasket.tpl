<div class="priceBlock">
    [{$smarty.capture.product_price}]
    <div>
        [{foreach from=$oView->lvGetCompatibilityIcons() item="aCompatibilty"}]
            <span><img src="[{$aCompatibilty.url}]" title="[{$aCompatibilty.title}]"></span>
        [{/foreach}]
    </div>
</div>
