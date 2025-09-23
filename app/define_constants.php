<?php
if (!defined('IT_COMPANY_NAME')) {

<<<<<<< HEAD
    define("IT_COMPANY_NAME", "Web Chilli");
    define("IT_COMPANY_ICON", "");

    if (env('APP_ENV') == 'production')
    {
        define("BACKEND_CSS_VERSION", "05-feb-2025");
        define("BACKEND_JS_VERSION", "05-Feb-2025");
    }
    else
    {
        define("BACKEND_CSS_VERSION", time());
        define("BACKEND_JS_VERSION", time());
    }

    define("ACTION_NOT_PROCEED", 406);
}
=======
    define("IT_COMPANY_NAME", "Celec");
    define("IT_COMPANY_ICON", "");

    if (env('APP_ENV') == 'production') {
        define("CSS_VERSION", "05-feb-2025");
        define("JS_VERSION", "05-Feb-2025");
    } else {
        define("CSS_VERSION", time());
        define("JS_VERSION", time());
    }

    define("ACTION_NOT_PROCEED", 406);

    define("DEFAULT_DISK", "public_external");
    define("NOT_FOUND_IMAGE_PATH", "img/not-found.png");

    define("ICON_SUMMARY_DELETE", "icon-trash");
}
>>>>>>> base_v3
