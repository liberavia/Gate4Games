[{if $product->oxarticles__oxshortdesc->value =='' && $product->lvGetShortDescription()}]
    <div class="shortDescription description" id="productShortdesc">[{$product->lvGetShortDescription()}]</div>
[{/if}]
[{$smarty.block.parent}]
