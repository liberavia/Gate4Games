[{capture append="oxidBlock_content"}]
[{assign var="product" value=$oView->lvGetArticle()}]
<div id="lvAgeCheckContent">
    [{if $oView->lvGetForbiddenByAge()}]
        <div id="lvAgeCheckInformation" class="widgetBox">
            <table width="100%" height="100%">
                <tr>
                    <td style="text-align:center;vertical-align: middle;">
                        <div id="lvEnterAgeForGame">
                            <img src ="[{$oView->lvGetCoverImage()}]" style="height:auto;width:auto;max-height:[{$oView->lvGetDetailsImageMaxHeight()}]px;max-width:[{$oView->lvGetDetailsImageMaxHeight()}]px;">
                        </div>
                    </td>
                    <td style="text-align:center;vertical-align: middle;">
                        [{oxifcontent ident="lvagenotallowed" object="oCont"}]
                            [{$oCont->oxcontents__oxcontent->value}]
                        [{/oxifcontent}]
                    </td>
                </tr>
            </table>
        </div>    
    [{else}]
        <div id="lvEnterAge" class="widgetBox">
            <table width="100%" height="100%">
                <tr>
                    <td style="text-align:center;vertical-align: middle;">
                        <div id="lvEnterAgeForGame">
                            <img src ="[{$oView->lvGetCoverImage()}]" style="height:auto;width:auto;max-height:[{$oView->lvGetDetailsImageMaxHeight()}]px;max-width:[{$oView->lvGetDetailsImageMaxHeight()}]px;">
                        </div>
                    </td>
                    <td style="text-align:center;vertical-align: middle;">
                        <div>
                            [{oxifcontent ident="lventerage" object="oCont"}]
                                [{$oCont->oxcontents__oxcontent->value}]
                            [{/oxifcontent}]
                        </div>
                        <div id="lvEnterAge" class="widgetBox">
                            <form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
                                [{$oViewConf->getHiddenSid()}]
                                <input type="hidden" name="fnc" value="lvValidateAge">
                                <input type="hidden" name="cl" value="lvagecheck">
                                <input type="hidden" name="sReturnUrl" value="[{$oView->lvGetReturnUrl()}]">

                                <table id="lvAgeTable" width="100%">
                                    <tr>
                                        <td>
                                            [{oxmultilang ident="LV_AGECHECK_ENTER_YEAR"}]
                                        </td>
                                        <td>
                                            [{oxmultilang ident="LV_AGECHECK_ENTER_MONTH"}]
                                        </td>
                                        <td>
                                            [{oxmultilang ident="LV_AGECHECK_ENTER_DAY"}]
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <select id="lvYearSelector" name="editval[lvAgeYear]">
                                                [{foreach from=$oView->lvGetYears() item="sYear"}]
                                                    <option value="[{$sYear}]">[{$sYear}]</option>
                                                [{/foreach}]
                                            </select>
                                        </td>
                                        <td>
                                            <select id="lvMonthSelector" name="editval[lvAgeMonth]">
                                                [{foreach from=$oView->lvGetMonths() item="sMonth"}]
                                                    <option value="[{$sMonth}]">[{$sMonth}]</option>
                                                [{/foreach}]
                                            </select>
                                        </td>
                                        <td>
                                            <select id="lvDaySelector" name="editval[lvAgeDay]">
                                                [{foreach from=$oView->lvGetDays() item="sDay"}]
                                                    <option value="[{$sDay}]">[{$sDay}]</option>
                                                [{/foreach}]
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="padding-top:15px;">
                                            <button class="submitButton largeButton" type="submit">[{oxmultilang ident="LV_AGECHECK_SUBMIT"}]</button>
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div>  
                    </td>
                </tr>
            </table>
        </div>
    [{/if}]
</div>
[{/capture}]
[{include file="layout/page.tpl"}]
