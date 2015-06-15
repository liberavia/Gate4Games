<div>
    [{foreach from=$oView->lvGetLetsPlayVideos() item="oMediaUrl"}]
        <div>[{$oMediaUrl->getHtml()}]</div>
    [{/foreach}]
</div>
