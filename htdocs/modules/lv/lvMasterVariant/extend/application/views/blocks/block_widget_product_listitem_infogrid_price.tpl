[{if $product->oxarticles__oxshortdesc->value =='' && $product->lvGetShortDescription()}]
    <div style="margin-bottom: 3px;line-height: 12px;">[{$product->lvGetShortDescription()|truncate:160:"..."}]</div>
[{/if}]
[{$smarty.block.parent}]
