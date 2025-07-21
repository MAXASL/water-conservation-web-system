<?php
namespace App\Helpers;

class CurrencyHelper {
    public static function formatZMW($amount) {
        return 'ZMW ' . number_format($amount, 2);
    }
}
