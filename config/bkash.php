<?php

return [
    "bkash_version" => "v1.2.0-beta",
    "bkash_sandbox_url" => env("BKASH_SANDBOX_URL", ""),
    "bkash_base_url" => env("BKASH_TOKENIZE_BASE_URL", ""),
    "bkash_callback_url"  => rtrim(env('APP_URL', 'https://retailnexis.com'), '/').'/bkash_payment_success',
    'bkash_timezone' => 'Asia/Dhaka',
];
