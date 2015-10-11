[{* Enter your custom HTML here *}]
</div>

<div class="actions">
    [{strip}]

    <ul>
        <li>
            <a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( '-1' );return false" target="edit">[{oxmultilang ident="LV_TOOLTIPPS_NEW_MAPPING"}]</a>
        </li>
    </ul>
    [{/strip}]
</div>