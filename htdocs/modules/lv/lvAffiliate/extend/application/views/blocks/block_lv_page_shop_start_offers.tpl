[{if $oView->lvGetTopSale()}]
    [{include file="widget/product/list.tpl" type=$oViewConf->getViewThemeParam('sStartPageListDisplayType') head="LV_TOP_SALE"|oxmultilangassign listId="lvTopSale" products=$oView->lvGetTopSale()  showMainLink=true}]
[{/if}]
[{if $oView->getNewestArticles()}]
    [{assign var='rsslinks' value=$oView->getRssLinks() }]
    [{include file="widget/product/list.tpl" type=$oViewConf->getViewThemeParam('sStartPageListDisplayType') head="LV_JUST_ARRIVED"|oxmultilangassign listId="newItems" products=$oView->getNewestArticles() rsslink=$rsslinks.newestArticles rssId="rssNewestProducts" showMainLink=true}]
[{/if}]
[{if $oView->lvGetTopSeller()}]
    [{include file="widget/product/list.tpl" type=$oViewConf->getViewThemeParam('sStartPageListDisplayType') head="LV_TOP_SELLER"|oxmultilangassign listId="lvTopSeller" products=$oView->lvGetTopSeller()  showMainLink=true}]
[{/if}]

