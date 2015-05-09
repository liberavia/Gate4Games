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
      <td class="index" colspan="3">
            <b>[{ oxmultilang ident="GENERAL_VARIANTE" }]</b>
            <a href="Javascript:editThis('[{$parentarticle->oxarticles__oxid->value}]');" class="edittext"><b>"[{$parentarticle->oxarticles__oxartnum->value}] [{$parentarticle->oxarticles__oxtitle->value}]"</b></a>
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
        <input type="text" class="editinput lvLongEdit" name="editval[oxarticles__oxpic[{$iIndex}]]" value="[{$sPicFile}]">
      </td>
      <td>
          <input type="radio" id="coverpic[{$iIndex}]" name="editval[oxarticles__lvcoverpic]" value="oxpic[{$iIndex}]" [{if $edit->oxarticles__lvcoverpic->value == "oxpic`$iIndex`"}]selected[{/if}]><label for="coverpic[{$iIndex}]">Cover</label>
      </td>
    </tr>
[{/section}]
