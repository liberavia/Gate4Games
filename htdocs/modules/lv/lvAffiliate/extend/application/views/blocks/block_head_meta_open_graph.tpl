[{if $oViewConf->getFbAppId()}]
    <meta property="og:site_name" content="[{$oViewConf->getBaseDir()}]">
    <meta property="fb:app_id" content="[{$oViewConf->getFbAppId()}]">
    <meta property="og:title" content="[{$oView->getMetaDescription()}]">
    [{if $oViewConf->getActiveClassName() == 'details'}]
        <meta property="og:type" content="product">
        [{*Depends on activated External Media module*}]
        [{if method_exists($oView, 'lvGetCoverPictureUrl')}]
            <meta property="og:image" content="[{$oView->lvGetCoverPictureUrl()}]">
        [{else}]
            <meta property="og:image" content="[{$oView->getActPicture()}]">
        [{/if}]
        <meta property="og:url" content="[{$oView->getCanonicalUrl()}]">
    [{else}]
        <meta property="og:type" content="website">
        [{if method_exists($oView, 'lvGetCoverPictureUrl')}]
            <meta property="og:image" content="[{$oView->lvGetCoverPictureUrl()}]">
        [{else}]
            <meta property="og:image" content="[{$oViewConf->getImageUrl('nopic.png')}]">
        [{/if}]
        <meta property="og:url" content="[{$oViewConf->getCurrentHomeDir()}]">
    [{/if}]
[{/if}]
