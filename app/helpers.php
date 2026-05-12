<?php

if (! function_exists('format_rupiah')) {
    function format_rupiah(?int $amount): string
    {
        return 'Rp '.number_format((int) ($amount ?? 0), 0, ',', '.');
    }
}
