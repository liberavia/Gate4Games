[{if $oxparentid}]
    <tr>
        <td class="edittext" width="120">
            [{oxmultilang ident="LVMV_MASTERVARIANT"}]
        </td>
        <td class="edittext">
              <input type="hidden" name="editval[oxarticles__lvmastervariant]" value="0">
              <input class="edittext" type="checkbox" name="editval[oxarticles__lvmastervariant]" value='1' [{if $edit->oxarticles__lvmastervariant->value == 1}]checked[{/if}] [{$readonly}]>
        </td>
    </tr>
[{/if}]
[{$smarty.block.parent}]