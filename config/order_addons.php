<?php

/**
 * Add-on pesanan (biji kopi, susu, dll.). Tambah entri di sini untuk opsi baru.
 * Key = kode unik disimpan di DB & dikirim dari kasir.
 */
return [
    'items' => [
        'arabica' => [
            'label' => 'Biji Arabika',
            'harga' => 2000,
        ],
        'oatside_milk' => [
            'label' => 'Susu Oatside',
            'harga' => 5000,
        ],
    ],
];
