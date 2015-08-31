<a id="[{$iIndex}]" href="[{$_productLink}]" class="titleBlock title fn" title="[{ $product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]">
    <span>[{ $product->oxarticles__oxtitle->value }] [{$product->oxarticles__oxvarselect->value}]</span>
    <div class="gridPicture">
        <img src="[{$product->lvGetCoverPictureUrl()}]" alt="[{ $product->oxarticles__oxtitle->value }]" style="height:auto;width:auto;height:[{$oView->lvGetListImageMaxHeight()}]px;max-width:[{$oView->lvGetListImageMaxHeight()}]px;">
    </div>
</a>
