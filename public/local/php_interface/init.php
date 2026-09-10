<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    return;
}

if (!defined('ADMIN_SECTION') || ADMIN_SECTION !== true) {
    global $APPLICATION;
    $APPLICATION->SetAdditionalCSS('/local/css/portal-theme.css');
}
