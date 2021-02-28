<?php

namespace App\MessageHandler;

use App\Message\InvoiceGeneratorMessage;
use App\Message\InvoiceSenderMessage;
use App\Service\Invoice\InvoiceGenerator;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class InvoiceGeneratorHandler implements MessageHandlerInterface
{
    private InvoiceGenerator $invoiceGenerator;
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus, InvoiceGenerator $invoiceGenerator)
    {
        $this->invoiceGenerator = $invoiceGenerator;
        $this->messageBus = $messageBus;
    }

    public function __invoke(InvoiceGeneratorMessage $invoiceMessage)
    {
        $invoice = $invoiceMessage->getInvoiceDTO();
        $fileUrl = $this->invoiceGenerator->generate($invoice);

        $this->messageBus->dispatch(
            new InvoiceSenderMessage($fileUrl, $invoice->getReceiverEmail(), $invoice->getIssueDate(), $invoice->getInvoiceNumber())
        );
    }
}
