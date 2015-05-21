<div class="widgetBox reviews">
    <h4>[{oxmultilang ident="WRITE_PRODUCT_REVIEW"}]</h4>
    [{assign var="product" value=$oView->getProduct()}]
    [{assign var="force_sid" value=$oView->getSidForWidget()}]
    [{oxid_include_widget cl="oxwReview" nocookie=1 force_sid=$force_sid _parent=$oViewConf->getTopActiveClassName() type=oxarticle anid=$product->oxarticles__oxnid->value aid=$product->oxarticles__oxid->value canrate=$oView->canRate() skipESIforUser=1}]
</div>
