[{capture append="oxidBlock_content"}]
<div id="lvAgeCheckContent">
    [{if $blLvForbiddenByAge}]
        <div id="lvAgeCheckInformation">
            [{oxifcontent ident="lvagenotallowed" object="oCont"}]
                [{$oCont->oxcontents__oxcontent->value}]
            [{/oxifcontent}]
        </div>    
    [{else}]
        <div id="lvEnterAge">
            [{oxifcontent ident="lventerage" object="oCont"}]
                [{$oCont->oxcontents__oxcontent->value}]
            [{/oxifcontent}]
        </div>
        <div id="lvEnterAge">
            <form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
                [{ $oViewConf->getHiddenSid() }]
                <input type="hidden" name="fnc" value="lvValidateAge">
                <input type="hidden" name="cl" value="lvagecheck">
                <table id="lvAgeTable">
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
                        <td colspan="3">
                            <button class="submitButton largeButton" type="submit">[{oxmultilang ident="LV_AGECHECK_SUBMIT"}]</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>  
    [{/if}]
</div>
[{/capture}]
[{include file="layout/page.tpl"}]
