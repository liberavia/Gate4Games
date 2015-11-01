[{$smarty.block.parent}]
<script>
    [{if $oViewConf->getActiveClassName() == 'details'}]
        var lvrecaptcha_review;
    [{/if}]
    var lvrecaptcha_feedback;

    var lvReCaptchaCallBack = function() {
        [{if $oViewConf->getActiveClassName() == 'details'}]
            lvrecaptcha_review = grecaptcha.render( 'lvrecaptcha_review', {
                'sitekey' : '[{$oViewConf->lvGetReCaptchaWebsiteKey()}]',
                'theme' : 'light'
            });
        [{/if}]
        lvrecaptcha_feedback = grecaptcha.render( 'lvrecaptcha_feedback', {
            'sitekey' : '[{$oViewConf->lvGetReCaptchaWebsiteKey()}]',
            'theme' : 'light'
        });
</script>                        
<script src="https://www.google.com/recaptcha/api.js?onload=lvReCaptchaCallBack&render=explicit" async defer></script>