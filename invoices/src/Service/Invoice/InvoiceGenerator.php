<?php

namespace App\Service\Invoice;

use App\Service\Invoice\Exception\InvoiceNumberException;
use League\Flysystem\Filesystem;
use League\Flysystem\Visibility;

class InvoiceGenerator
{
    private Filesystem $publicUploadsFilesystem;
    private string $filePath;
    private string $fileStorageBaseUrl;

    public function __construct(Filesystem $publicUploadsFilesystem, string $fileStorageBaseUrl)
    {
        $this->publicUploadsFilesystem = $publicUploadsFilesystem;
        $this->fileStorageBaseUrl = $fileStorageBaseUrl;
    }

    public function generate(InvoiceDTO $invoiceDTO): string
    {
        $invoiceNumber = $this->generateInvoiceNumber($invoiceDTO->getIssueDate(), $invoiceDTO->getOrderNumber());
        $invoiceDTO->setInvoiceNumber($invoiceNumber);
        $this->filePath = $this->saveFile($invoiceDTO);

        return $this->filePath;
    }

    public function getFileUrl(): string
    {
        return sprintf('%s/%s', $this->fileStorageBaseUrl, $this->filePath);
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
        $fileName = sprintf('%s.json', $invoiceDTO->getInvoiceNumber());

        $this->publicUploadsFilesystem->write($fileName, $content,  ['visibility' => Visibility::PUBLIC]);
        return $fileName;
    }

    private function isUniqeNumber(): bool
    {
        //check in database
        return true;
    }

}
