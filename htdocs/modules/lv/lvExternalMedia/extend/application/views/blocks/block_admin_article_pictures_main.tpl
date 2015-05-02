<colgroup>
    <col width="2%">
    <col width="1%" nowrap>
    <col width="1%">
    <col width="10%" nowrap>
    <col width="95%">
</colgroup>
<tr>
    <th colspan="2" valign="top">
       [{oxmultilang ident="GENERAL_ARTICLE_PICTURES" }]
    </th>
</tr>

[{if $oxparentid}]
    <tr>
      <td class="index" colspan="2">
            <b>[{ oxmultilang ident="GENERAL_VARIANTE" }]</b>
            <a href="Javascript:editThis('[{ $parentarticle->oxarticles__oxid->value}]');" class="edittext"><b>"[{ $parentarticle->oxarticles__oxartnum->value }] [{ $parentarticle->oxarticles__oxtitle->value }]"</b></a>
      </td>
    </tr>
[{/if}]

[{section name=picRow start=1 loop=$iPicCount+1 step=1}]
    [{assign var="iIndex" value=$smarty.section.picRow.index}]
    <tr>
      <td class="index">
          #[{$iIndex}]
      </td>
      <td class="text">
        [{assign var="sPicFile" value=$edit->getPictureFieldValue("oxpic", $iIndex) }]

        [{if $sPicFile == "nopic.jpg" || $sPicFile == ""}]
            [{assign var="blPicUplodaded" value=false}]
            <span class="notActive">-------</span>
        [{else}]
            <input type="text" class="editinput" name="editval[oxarticles__oxpic[{$iIndex}]]" value="[{$edit->oxarticles__oxpic`$iIndex`->value}]">
        [{/if}]
      </td>
    </tr>
[{/section}]
