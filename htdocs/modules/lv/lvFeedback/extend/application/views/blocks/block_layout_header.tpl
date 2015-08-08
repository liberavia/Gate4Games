<div id="lvFeedbackButtonIdent" class="lvFeedbackButtonContainer">
    <img src="[{$oViewConf->lvGetFeedbackButtonImg()}]">
</div>
<div id="lvFeedbackFormIdent" class="lvFeedbackFormContainer">
    <form action="[{$oViewConf->getSelfActionLink()}]" method="post" id="lvFeedbackForm">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="fnc" value="savereview">
        <input type="hidden" name="cl" value="lvsendfeedback">
        <input type="hidden" name="currentpage" value="[{$smarty.server.HTTP_HOST}][{$smarty.server.REQUEST_URI}]">
        <table id="lvFeedbackTable">
            <tr>
                <td colspan="2">
                    <h3>[{oxmultilang ident="LVFEEDBACK_FORM_GREETER"}]</h3>
                </td>
            </tr>
            <tr>
                <td>
                    [{oxmultilang ident="LVFEEDBACK_FORM_EMAIL"}]:
                </td>
                <td>
                    <input type="text" name="editval[email]">
                </td>
            </tr>
            <tr>
                <td>
                    [{oxmultilang ident="LVFEEDBACK_FORM_NAME"}]:
                </td>
                <td>
                    <input type="text" name="editval[name]" value="anonymous">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    [{oxmultilang ident="LVFEEDBACK_FORM_MESSAGE"}]:
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <textarea name="editval[message]" cols="60" rows="10"></textarea>
                </td>
            </tr>
            [{if $oViewConf->lvFeedbackRecaptchaActive()}]
                <tr>
                    <td colspan="2">
                        <div class="g-recaptcha" data-sitekey="[{$oViewConf->lvGetReCaptchaWebsiteKey()}]"></div>
                    </td>
                </tr>
            [{/if}]
            <tr>
                <td colspan="2">
                    <button id="submitfeedback" type="submit" class="submitButton">[{oxmultilang ident="LVFEEDBACK_FORM_SUBMIT"}]</button>
                </td>
            </tr>
            

        </table>
    </form>
</div>
[{$smarty.block.parent}]