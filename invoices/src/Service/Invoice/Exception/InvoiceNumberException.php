<?php

namespace App\Service\Invoice\Exception;

use RuntimeException;

class InvoiceNumberException extends RuntimeException
{
    public static function createNonUniqueInvoiceNumber(string $invoiceNumber): self
    {
        return new self(sprintf('Invoice number %s is not unique!', $invoiceNumber));
    }
}
