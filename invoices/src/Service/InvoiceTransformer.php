<?php

namespace App\Command;

use App\Service\Invoice\InvoiceDTO;

class InvoiceTransformer
{
    public static function generateDTO(array $invoice): InvoiceDTO
    {
        $taxValue = $invoice['service_cost'] * $invoice['tax_rate'] / 100;
        $grossValue = $invoice['service_cost'] * (100 + $invoice['tax_rate']) / 100;

        return new InvoiceDTO(
            $invoice['order_number'],
            new \DateTimeImmutable($invoice['invoice_date']),
            $invoice['receiver_name'],
            $invoice['receiver_address'],
            $invoice['receiver_email'],
            $invoice['receiver_tax_id'],
            $invoice['service_name'],
            $invoice['service_cost'],
            $invoice['tax_rate'],
            $taxValue,
            $grossValue,
        );
    }
}
