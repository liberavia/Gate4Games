[{oxhasrights ident="SHOWSHORTDESCRIPTION"}]
    [{if $oDetailsProduct->oxarticles__oxshortdesc->value =='' && $oDetailsProduct->lvGetShortDescription()}]
        <div class="shortDescription description" id="productShortdesc">[{$oDetailsProduct->lvGetShortDescription()}]</div>
    [{/if}]
[{/oxhasrights}]
[{$smarty.block.parent}]