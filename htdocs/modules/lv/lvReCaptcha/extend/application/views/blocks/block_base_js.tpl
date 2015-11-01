[{$smarty.block.parent}]
[{assign var="lvFeedbackJs" value=$oViewConf->getBaseDir()|cat:"modules/lv/lvReCaptcha/out/src/js/lvfeedback.js"}]
[{oxscript include=$lvFeedbackJs}]
<script src="https://www.google.com/recaptcha/api.js?onload=lvReCaptchaCallBack&render=explicit" async defer></script>
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
