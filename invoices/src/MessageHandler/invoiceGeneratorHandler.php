<?php

namespace App\MessageHandler;

use App\Message\InvoiceGeneratorMessage;
use App\Service\Invoice\InvoiceGenerator;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class invoiceGeneratorHandler implements MessageHandlerInterface
{
    private InvoiceGenerator $invoiceGenerator;

    public function __construct(InvoiceGenerator $invoiceGenerator)
    {
        $this->invoiceGenerator = $invoiceGenerator;
    }

    public function __invoke(InvoiceGeneratorMessage $invoiceMessage)
    {
        $path = $this->invoiceGenerator->generate($invoiceMessage->getInvoiceDTO());
    }
}
