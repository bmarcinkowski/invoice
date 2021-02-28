<?php

namespace App\Command;

use App\Message\InvoiceGeneratorMessage;
use App\Service\Invoice\InvoiceTransformer;
use League\Flysystem\Filesystem;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class InvoiceImporter extends Command
{
    protected static $defaultName = 'invoice:importer';

    private const ORIGIN_FILENAME_PATTERN = '%s_%s.csv';
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
    /**
     * @var Filesystem
     */
    private Filesystem $publicUploadsFilesystem;

    public function __construct(MessageBusInterface $messageBus, Filesystem $publicUploadsFilesystem, LoggerInterface $logger)
    {
        ini_set('memory_limit', '128M');
        parent::__construct();
        $this->messageBus = $messageBus;
        $this->logger = $logger;
        $this->publicUploadsFilesystem = $publicUploadsFilesystem;
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
        $filename = $this->generateFilename();
        $file = $this->publicUploadsFilesystem->readStream($filename);
        while (($data = fgetcsv($file, 0, ';')) !== false) {
            if (self::CSV_HEADER === $data) {
                continue;
            }
            $invoices[] = array_combine(self::CSV_HEADER, $data);
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
            $invoiceDTO = InvoiceTransformer::generateDTO($invoice);

            $message = new InvoiceGeneratorMessage($invoiceDTO);
            $this->messageBus->dispatch($message);
        }
    }

    private function generateFilename(): string
    {
        $date = (new \DateTime())->modify('- 1 month');

        return sprintf(self::ORIGIN_FILENAME_PATTERN, $date->format('Y'), $date->format('m'));
    }
}
