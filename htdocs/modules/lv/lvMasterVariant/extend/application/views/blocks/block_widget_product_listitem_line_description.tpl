[{if $product->oxarticles__oxshortdesc->value =='' && $product->lvGetShortDescription()}]
    <div class="description">[{$product->lvGetShortDescription()|truncate:160:"..."}]</div>
[{/if}]
