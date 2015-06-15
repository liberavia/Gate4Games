<div>
    [{foreach from=$oView->lvGetReviewVideos() item="oMediaUrl"}]
        <div>[{$oMediaUrl->getHtml()}]</div>
    [{/foreach}]
</div>
