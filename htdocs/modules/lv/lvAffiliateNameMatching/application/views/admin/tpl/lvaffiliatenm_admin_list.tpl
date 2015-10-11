[{include file="headitem.tpl" title="ADMINLINKS_LIST_TITLE"|oxmultilangassign box="list"}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<script type="text/javascript">
<!--
window.onload = function ()
{
    top.reloadEditFrame();
    [{ if $updatelist == 1}]
        top.oxid.admin.updateList('[{ $oxid }]');
    [{ /if}]
}
//-->
</script>

<div id="liste">


<form name="search" id="search" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{include file="_formparams.tpl" cl="lvaffiliatenm_admin_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]

<table cellspacing="0" cellpadding="0" border="0" width="100%">
<colgroup>
        <col width="3%">
        <col width="40%">
        <col width="40%">
        <col width="7%">
</colgroup>
<tr class="listitem">
    <td valign="top" class="listfilter first" height="20">
        <div class="r1"><div class="b1">
         </div></div>
    </td>
     <td valign="top" class="listfilter" height="20">
        <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="60" maxlength="128" name="where[[{$listTable}][{$nameconcat}]lvfromname]" value="[{ $where.lvaffiliatenm.lvfromname }]">
         </div></div>
    </td>
    <td valign="top" class="listfilter " height="20">
        <div class="r1"><div class="b1">
            <input class="listedit" type="text" size="60" maxlength="128" name="where[[{$listTable}][{$nameconcat}]lvtoname]" value="[{ $where.lvaffiliatenm.lvtoname}]">
         </div></div>
    </td>
    <td valign="top" class="listfilter">
        <div class="r1">
            <div class="b1">
                    <div class="find">
                            <input class="listedit" type="submit" name="submitit" value="[{ oxmultilang ident="GENERAL_SEARCH" }]">
                    </div>
            </div>
        </div>
    </td>
</tr>

<tr>
    <td class="listheader first" height="15" width="30" align="center"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'lvaffiliatenm', 'lvactive', 'asc');document.search.submit();" class="listheader">[{oxmultilang ident="GENERAL_ACTIVTITLE"}]</a></td>
    <td class="listheader" height="15">&nbsp;<a href="Javascript:top.oxid.admin.setSorting( document.search, 'lvaffiliatenm', 'lvfromname', 'asc');document.search.submit();" class="listheader">[{oxmultilang ident="LV_FROM_NAME"}]</a></td>
    <td colspan="2" class="listheader" height="15">&nbsp;<a href="Javascript:top.oxid.admin.setSorting( document.search, 'lvaffiliatenm', 'lvtoname', 'asc');document.search.submit();" class="listheader">[{oxmultilang ident="LV_TO_NAME"}]</a></td>
</tr>
[{assign var="blWhite" value=""}]
[{assign var="_cnt" value=0}]
[{foreach from=$mylist item=listitem}]
    [{assign var="_cnt" value=$_cnt+1}]
    <tr id="row.[{$_cnt}]">

    [{ if $listitem->blacklist == 1}]
        [{assign var="listclass" value=listitem3 }]
    [{ else}]
        [{assign var="listclass" value=listitem$blWhite }]
    [{ /if}]
    [{ if $listitem->getId() == $oxid }]
        [{assign var="listclass" value=listitem4 }]
    [{ /if}]
        
    <td valign="top" class="[{ $listclass}][{ if $listitem->lvaffiliatenm__lvactive->value == 1}] active[{/if}]" height="15"><div class="listitemfloating">&nbsp</a></div></td>
    <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating">&nbsp;<a href="Javascript:top.oxid.admin.editThis('[{ $listitem->lvaffiliatenm__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->lvaffiliatenm__lvfromname->value }]</a></div></td>
    <td valign="top" class="[{ $listclass}]"><div class="listitemfloating">&nbsp;<a href="Javascript:top.oxid.admin.editThis('[{ $listitem->lvaffiliatenm__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->lvaffiliatenm__lvtoname->value }]</a></div></td>

    <td class="[{ $listclass}]">
      [{if !$readonly }]
          [{if $listitem->blIsDerived && !$oViewConf->isMultiShop()}]
            <a href="Javascript:top.oxid.admin.unassignThis('[{ $listitem->lvaffiliatenm__oxid->value }]');" class="unasign" id="una.[{$_cnt}]" [{include file="help.tpl" helpid=item_unassign}]></a>
          [{/if}]
          [{if !$readonly && !$listitem->blIsDerived}]
            <a href="Javascript:top.oxid.admin.deleteThis('[{ $listitem->lvaffiliatenm__oxid->value }]');" class="delete" id="del.[{$_cnt}]" [{include file="help.tpl" helpid=item_delete}]></a>
          [{/if}]
      [{/if}]
    </td>
</tr>
[{if $blWhite == "2"}]
[{assign var="blWhite" value=""}]
[{else}]
[{assign var="blWhite" value="2"}]
[{/if}]
[{/foreach}]
[{include file="pagenavisnippet.tpl" colspan="6"}]
</table>
</form>
</div>

[{include file="pagetabsnippet.tpl"}]

<script type="text/javascript">
if (parent.parent)
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{ oxmultilang ident="LV_NAMEMATCHING_MENUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="LV_NAMEMATCHING_MENUSUBITEM" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>
