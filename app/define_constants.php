<?php
if (!defined('IT_COMPANY_NAME')) {

    define("IT_COMPANY_NAME", "Corridor");
    define("IT_COMPANY_ICON", "");

    if (env('APP_ENV') == 'production') {
        define("CSS_VERSION", "16-Nov-2026");
        define("JS_VERSION", "16-Nov-2026");
    } else {
        define("CSS_VERSION", time());
        define("JS_VERSION", time());
    }

    define("ACTION_NOT_PROCEED", 406);

    define("DEFAULT_DISK", "public");
    define("NOT_FOUND_IMAGE_PATH", "img/not-found.png");

    define("ICON_SUMMARY_DELETE", "icon-trash");
}
