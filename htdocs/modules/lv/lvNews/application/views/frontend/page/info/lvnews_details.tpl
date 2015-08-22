[{capture append="oxidBlock_content"}]
    [{assign var="oNews" value=$oView->lvGetNewsArticle()}]
    <h1 class="pageHead">[{$oNews->lvGetTitle()}]</h1>
    <div class="cmsContent">
        [{$oNews->getLongDesc()}]
    </div>
[{/capture}]
[{include file="layout/page.tpl"}]