<?php

namespace App\Message;

use App\Service\Invoice\InvoiceDTO;

class InvoiceGeneratorMessage
{
    private InvoiceDTO $invoiceDTO;

    public function __construct(InvoiceDTO $invoiceDTO)
    {
        $this->invoiceDTO = $invoiceDTO;
    }

    public function getInvoiceDTO(): InvoiceDTO
    {
        return $this->invoiceDTO;
    }
}
