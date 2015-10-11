[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="lvaffiliatenm_admin_main">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>

      <table cellspacing="0" cellpadding="0" border="0" width="98%">
        <colgroup><col width="20%"><col width="5%"><col width="75%"></colgroup>
        <form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="cl" value="lvaffiliatenm_admin_main">
        <input type="hidden" name="fnc" value="">
        <input type="hidden" name="oxid" value="[{$oxid}]">
        <input type="hidden" name="voxid" value="[{$oxid}]">
        <input type="hidden" name="editval[lvaffiliatenm__oxid]" value="[{$oxid}]">
        <tr>
          <td valign="top" class="edittext">
            <table cellspacing="0" cellpadding="0" border="0">
              <tr>
                <td class="edittext" width="90">
                [{ oxmultilang ident="GENERAL_ACTIVE" }]&nbsp;
                </td>
                <td class="edittext">
                    <input class="edittext" type="checkbox" name="editval[lvaffiliatenm__fcactive]" value='1' [{if $edit->lvaffiliatenm__fcactive->value == 1}]checked[{/if}] [{$readonly}]>
                </td>
              </tr>
              <tr>
                <td class="edittext">
                    [{oxmultilang ident="LV_FROM_NAME"}]&nbsp;
                </td>
                <td class="edittext">
                    <input type="text" class="editinput" size="60" maxlength="[{$edit->lvaffiliatenm__lvfromname->fldmax_length}]" name="editval[lvaffiliatenm__lvfromname]" value="[{if $smarty.session.blRedirectError}][{$smarty.session.sEnteredCallUri}][{else}][{$edit->lvaffiliatenm__lvfromname }][{/if}]" [{$readonly}]>
                </td>
              </tr>
              <tr>
                <td class="edittext">
                    [{oxmultilang ident="LV_TO_NAME"}]&nbsp;
                </td>
                <td class="edittext">
                    <input type="text" class="editinput" size="60" maxlength="[{$edit->lvaffiliatenm__lvtoname->fldmax_length}]" name="editval[lvaffiliatenm__lvtoname]" value="[{if $smarty.session.blRedirectError}][{$smarty.session.sEnteredTargetUri}][{else}][{$edit->lvaffiliatenm__lvtoname }][{/if}]" [{$readonly}]>
                </td>
              </tr>             
              <tr>
                <td class="edittext">
                </td>
                <td class="edittext"><br>
                <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'"" [{$readonly}]>
                </td>
              </tr>
            </table>
          </td>
        <td></td>
        </form>
        <!-- Anfang rechte Seite -->
          <td valign="top" class="edittext" align="left">
          </td>
        </tr>
      </table>
[{include file="lvaffiliatenmbottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
