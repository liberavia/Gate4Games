[{$smarty.block.parent}]

[{if method_exists($oViewConf,'lvGetCKEditor') }][{ $oViewConf->loadTinyMce() }][{/if}]