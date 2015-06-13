[{$smarty.block.parent}]
<tr>
    <td class="edittext">
        [{oxmultilang ident="LV_MANUFACTURER_SHORTCUT"}]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="6" maxlength="[{$edit->oxmanufacturers__lvshortcut->fldmax_length}]" id="oLockTarget" name="editval[oxmanufacturers__lvshortcut]" value="[{$edit->oxmanufacturers__lvshortcut->value}]" [{ $readonly }]>
    </td>
</tr>
<tr>
    <td class="edittext" width="120">
        [{oxmultilang ident="LV_TOP_MANUFACTURER"}]
    </td>
    <td class="edittext">
        <input name="editval[oxmanufacturers__lvtopmanufacturer]" type="hidden" value="0">
        <input class="edittext" type="checkbox" name="editval[oxmanufacturers__lvtopmanufacturer]" value='1' [{if $edit->oxmanufacturers__lvtopmanufacturer->value == 1}]checked[{/if}] [{ $readonly }]>
    </td>
</tr>
