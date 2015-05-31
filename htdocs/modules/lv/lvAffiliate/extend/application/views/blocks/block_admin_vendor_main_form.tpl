[{$smarty.block.parent}]
<tr>
    <td class="edittext" width="120">
        [{oxmultilang ident="LV_MAIN_VENDOR"}]
    </td>
    <td class="edittext">
        <input name="editval[oxvendor__lvmainvendor]" type="hidden" value="0">
        <input class="edittext" type="checkbox" name="editval[oxvendor__lvmainvendor]" value='1' [{if $edit->oxvendor__lvmainvendor->value == 1}]checked[{/if}] [{ $readonly }]>
    </td>
</tr>
<tr>
    <td class="edittext">
        [{oxmultilang ident="LV_VENDOR_SHORTCUT"}]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="6" maxlength="[{$edit->oxvendor__lvshortcut->fldmax_length}]" id="oLockTarget" name="editval[oxvendor__lvshortcut]" value="[{$edit->oxvendor__lvshortcut->value}]" [{ $readonly }]>
    </td>
</tr>
