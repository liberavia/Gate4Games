[{$smarty.block.parent}]
[{*handle feedback on submitted form 1= success, 2= captcha, 3 = empty message 4= mail error*}]
[{if $smarty.get.lvFeedbackReturnMessage == '1'}]
    <div class="status success corners"><p>[{oxmultilang ident="LVFEEDBACK_MAIL_SENDED"}]</p></div>
[{elseif $smarty.get.lvFeedbackReturnMessage == '2'}]
    <div class="status error corners"><p>[{oxmultilang ident="LVFEEDBACK_MAIL_ERROR_CAPTCHA"}]</p></div>
[{elseif $smarty.get.lvFeedbackReturnMessage == '3'}]
    <div class="status error corners"><p>[{oxmultilang ident="LVFEEDBACK_MAIL_ERROR_NO_MESSAGE"}]</p></div>
[{elseif $smarty.get.lvFeedbackReturnMessage == '4'}]
    <div class="status error corners"><p>[{oxmultilang ident="LVFEEDBACK_MAIL_ERROR_SEND"}]</p></div>
[{/if}]
