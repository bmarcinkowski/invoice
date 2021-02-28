<?php

namespace App\Message;

class InvoiceSenderMessage
{
    private string $email;
    private string $fileUrl;
    private \DateTimeImmutable $issueDate;
    private string $invoiceNumber;

    public function __construct(string $fileUrl, string $email, \DateTimeImmutable $issueDate, string $invoiceNumber)
    {
        $this->email = $email;
        $this->fileUrl = $fileUrl;
        $this->issueDate = $issueDate;
        $this->invoiceNumber = $invoiceNumber;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFileUrl(): string
    {
        return $this->fileUrl;
    }

    public function getIssueDate($format = "Y-m-d")
    {
        return $this->issueDate->format($format);
    }

    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }
}
