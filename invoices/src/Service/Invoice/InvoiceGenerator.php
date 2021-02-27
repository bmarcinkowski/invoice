<?php

namespace App\Service\Invoice;

use League\Flysystem\Filesystem;

class InvoiceGenerator
{
    /**
     * @var Filesystem
     */
    private Filesystem $publicUploadsFilesystem;

    public function __construct(Filesystem $publicUploadsFilesystem)
    {
        $this->publicUploadsFilesystem = $publicUploadsFilesystem;
    }

    public function generate(InvoiceDTO $invoiceDTO): string
    {
        $invoiceNumber = $this->generateInvoiceNumber($invoiceDTO->getInvoiceDate(), $invoiceDTO->getOrderNumber());
        $invoiceDTO->setInvoiceNumber($invoiceNumber);

        return $this->saveFile($invoiceDTO, $invoiceNumber);
    }

    private function generateInvoiceNumber(\DateTimeImmutable $invoiceDate, int $orderNumber): string
    {
        return sprintf(
            'SH%s-%s',
            $invoiceDate->format('ymd'),
            str_pad((string)$orderNumber, 6, '0', STR_PAD_LEFT)
        );
    }

    private function saveFile(InvoiceDTO $invoiceDTO): string
    {
        $content = json_encode($invoiceDTO, JSON_THROW_ON_ERROR);
        $fileName = sprintf('summary/%s.json', $invoiceDTO->getInvoiceNumber());

        $this->publicUploadsFilesystem->write($fileName, $content);

        return $fileName;
    }

}
