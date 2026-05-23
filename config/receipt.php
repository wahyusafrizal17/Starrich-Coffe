<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Lebar kertas thermal (karakter per baris, font normal)
    |--------------------------------------------------------------------------
    | 58mm ≈ 32 karakter, 80mm ≈ 48 karakter (Panda & sejenis ESC/POS).
    */
    'paper_width' => env('RECEIPT_PAPER_WIDTH', '58'),

    'store_name' => env('RECEIPT_STORE_NAME', env('APP_NAME', 'Starrich')),

    'footer_line' => env('RECEIPT_FOOTER', 'Terima kasih sudah ngopi di Starrich!'),

    /** Cetak otomatis via RawBT setelah bayar (WebView Android). */
    'auto_print_rawbt' => env('RECEIPT_AUTO_PRINT_RAWBT', true),

];
