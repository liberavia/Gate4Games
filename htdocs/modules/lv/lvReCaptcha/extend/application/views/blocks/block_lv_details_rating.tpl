<ul id="itemRating" class="rating">
    [{math equation="x*y" x=20 y=$oView->getRatingValue() assign="iRatingAverage"}]

    [{assign var="_star_title" value="MESSAGE_RATE_THIS_ARTICLE"|oxmultilangassign}]

    <li class="currentRate" style="width: [{$iRatingAverage}]%;">
        <a title="[{$_star_title}]"></a>
        <span title="[{$iRatingAverage}]"></span>
    </li>
    [{section name=star start=1 loop=6}]
        <li class="s[{$smarty.section.star.index}]">
            <a  class="[{if $oView->canRate()}]ox-write-review[{/if}] ox-rateindex-[{$smarty.section.star.index}]" rel="nofollow" href="#review" title="[{$_star_title}]">
            </a>
         </li>
    [{/section}]
    <li class="ratingValue">
        [{assign var="sRateUrl" value=$oView->getRateUrl()}]
        <a id="itemRatingText" class="rates" rel="nofollow" href="#review">
            [{if $oView->getRatingCount()}]
                ([{$oView->getRatingCount()}])
            [{else}]
                [{oxmultilang ident="NO_RATINGS"}]
            [{/if}]
        </a>
    </li>
</ul>
