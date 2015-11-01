<form action="[{$oViewConf->getSelfActionLink()}]" method="post" id="rating">
    <div id="writeReview">
        <input id="productRating" type="hidden" name="artrating" value="0">
        <input id="recommListRating" type="hidden" name="recommlistrating" value="0">
        <ul id="reviewRating" class="rating">
            <li id="reviewCurrentRating" class="currentRate">
                <a title="[{$_star_title}]"></a>
            </li>
            [{section name=star start=1 loop=6}]
                <li class="s[{$smarty.section.star.index}]">
                  <a class="ox-write-review ox-rateindex-[{$smarty.section.star.index}]" rel="nofollow" title="[{$smarty.section.star.index}] [{if $smarty.section.star.index==1}][{oxmultilang ident="STAR"}][{else}][{oxmultilang ident="STARS"}][{/if}]"></a>
                </li>
            [{/section}]
        </ul>
        [{$oViewConf->getHiddenSid()}]
        [{$oViewConf->getNavFormParams()}]
        [{oxid_include_dynamic file="form/formparams.tpl"}]
        <input type="hidden" name="fnc" value="savereview">
        <input type="hidden" name="cl" value="[{$oViewConf->getTopActiveClassName()}]">

        [{if $oView->getReviewType() == 'oxarticle'}]
            <input type="hidden" name="anid" value="[{$oView->getArticleId()}]">
        [{elseif $oView->getReviewType() == 'oxrecommlist'}]
            <input type="hidden" name="recommid" value="[{$oView->getRecommListId()}]">
        [{/if}]

        [{assign var="sReviewUserHash" value=$oView->getReviewUserHash()}]
        [{if $sReviewUserHash}]
            <input type="hidden" name="reviewuserhash" value="[{$sReviewUserHash}]">
        [{/if}]
        <br>[{oxmultilang ident="LV_RATING_USERNAME"}] <input type="text" value="" maxlength="32" name="lvusername"><br>
        <textarea  rows="15" name="rvw_txt" class="areabox"></textarea><br>
        <script>
            var lvrecaptcha_review;
            var lvrecaptcha_feedback;
            
            var lvReCaptchaCallBack = function() {
                lvrecaptcha_review = grecaptcha.render( 'lvrecaptcha_review', {
                    'sitekey' : '[{$oView->lvGetReCaptchaWebsiteKey()}]',
                    'theme' : 'light'
                });
                lvrecaptcha_feedback = grecaptcha.render( 'lvrecaptcha_feedback', {
                    'sitekey' : '[{$oViewConf->lvGetReCaptchaWebsiteKey()}]',
                    'theme' : 'light'
                });
            }
        </script>                        
        <div id="lvrecaptcha_review"></div>
        <br>
        <button id="reviewSave" type="submit" class="submitButton">[{oxmultilang ident="SAVE_RATING_AND_REVIEW"}]</button>
    </div>
</form>
<a id="writeNewReview" rel="nofollow"><b>[{oxmultilang ident="WRITE_REVIEW"}]</b></a>
