<?php

namespace App\Command;

use App\Message\InvoiceGeneratorMessage;
use App\Service\Invoice\InvoiceDTO;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class InvoiceImporter extends Command
{
    protected static $defaultName = 'invoice:importer';

    private const ORIGIN_FILENAME = './databucket/invoices.csv';
    private const CSV_HEADER = [
        "order_number",
        "invoice_date",
        "receiver_name",
        "receiver_address",
        "receiver_email",
        "receiver_tax_id",
        "service_name",
        "service_cost",
        "tax_rate",
    ];

    private MessageBusInterface $messageBus;
    private LoggerInterface $logger;
    private CsvEncoder $csvEncoder;

    public function __construct(MessageBusInterface $messageBus, LoggerInterface $logger)
    {
        parent::__construct();
        $this->messageBus = $messageBus;
        $this->logger = $logger;
    }

    protected function configure()
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $invoices = $this->getInvoices();
            $this->exportInvoices($invoices);
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function getInvoices(): array
    {
        return $this->getInvoicesFromFile();
    }

    private function getInvoicesFromFile(): array
    {
        $invoices = [];
        if (($handle = fopen(self::ORIGIN_FILENAME, 'r')) !== false) {
            while (($data = fgetcsv($handle, 0, ';')) !== false) {
                if (self::CSV_HEADER === $data) {
                    continue;
                }
                $invoices[] = array_combine(self::CSV_HEADER, $data);
            }
            fclose($handle);
        }

        return $invoices;
    }

    /**
     * @param array $invoices
     * @throws \Exception
     */
    protected function exportInvoices(array $invoices): void
    {
        foreach ($invoices as $invoice) {
            $invoiceDTO = new InvoiceDTO(
                $invoice['order_number'],
                new \DateTimeImmutable($invoice['invoice_date']),
                $invoice['receiver_name'],
                $invoice['receiver_address'],
                $invoice['receiver_email'],
                $invoice['receiver_tax_id'],
                $invoice['service_name'],
                $invoice['service_cost'],
                $invoice['tax_rate'],
            );
            $message = new InvoiceGeneratorMessage($invoiceDTO);
            $this->messageBus->dispatch($message);
        }
    }
}
