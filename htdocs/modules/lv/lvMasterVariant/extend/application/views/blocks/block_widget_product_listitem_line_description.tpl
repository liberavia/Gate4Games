[{if $product->oxarticles__oxshortdesc->value =='' && $product->lvGetShortDescription()}]
    <div class="description">[{$product->lvGetShortDescription()}]</div>
[{/if}]
