[{$smarty.block.parent}]
<tr>
    <td class="edittext">
        [{oxmultilang ident="LV_MANUFACTURER_SHORTCUT"}]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="6" maxlength="[{$edit->oxmanufacturers__lvshortcut->fldmax_length}]" id="oLockTarget" name="editval[oxmanufacturers__lvshortcut]" value="[{$edit->oxmanufacturers__lvshortcut->value}]" [{ $readonly }]>
    </td>
</tr>
