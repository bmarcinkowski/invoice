<?php

namespace App\Service\Invoice;

use App\Service\Invoice\Exception\InvoiceNumberException;
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
        $invoiceNumber = $this->generateInvoiceNumber($invoiceDTO->getIssueDate(), $invoiceDTO->getOrderNumber());
        $invoiceDTO->setInvoiceNumber($invoiceNumber);

        return $this->saveFile($invoiceDTO, $invoiceNumber);
    }

    private function generateInvoiceNumber(\DateTimeImmutable $invoiceDate, int $orderNumber): string
    {
        $invoiceNumber = sprintf(
            'SH%s-%s',
            $invoiceDate->format('ymd'),
            str_pad((string)$orderNumber, 6, '0', STR_PAD_LEFT)
        );

        if (!$this->isUniqeNumber()) {
            throw InvoiceNumberException::createNonUniqueInvoiceNumber($invoiceNumber);
        }

        return $invoiceNumber;
    }

    private function saveFile(InvoiceDTO $invoiceDTO): string
    {
        $content = json_encode($invoiceDTO, JSON_THROW_ON_ERROR);
        $fileName = sprintf('summary/%s.json', $invoiceDTO->getInvoiceNumber());

        $this->publicUploadsFilesystem->write($fileName, $content);

        return $fileName;
    }

    private function isUniqeNumber(): bool
    {
        //check in database
        return true;
    }

}
