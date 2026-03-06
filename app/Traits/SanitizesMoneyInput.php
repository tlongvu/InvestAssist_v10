<?php

namespace App\Traits;

trait SanitizesMoneyInput
{
    /**
     * Remove commas from numeric strings before validation.
     */
    protected function sanitizeMoney(string|null $value): float|null
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Remove all commas
        $sanitized = str_replace(',', '', $value);
        
        return (float) $sanitized;
    }
}
