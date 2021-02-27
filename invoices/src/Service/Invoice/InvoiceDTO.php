<?php

namespace App\Service\Invoice;

use DateTimeImmutable;

class InvoiceDTO implements \JsonSerializable
{
    private int $orderNumber;
    private DateTimeImmutable $invoiceDate;
    private string $receiverName;
    private string $receiverAddress;
    private string $receiverEmail;
    private string $receiverTaxId;
    private string $serviceName;
    private float $serviceCost;
    private int $taxRate;
    private int $taxValue;
    private string $invoiceNumber;
    private float $grossValue;

    public function __construct(
        int $orderNumber,
        DateTimeImmutable $invoiceDate,
        string $receiverName,
        string $receiverAddress,
        string $receiverEmail,
        string $receiverTaxId,
        string $serviceName,
        float $serviceCost,
        int $taxRate,
        float $taxValue,
        float $grossValue
    ) {
        $this->orderNumber = $orderNumber;
        $this->invoiceDate = $invoiceDate;
        $this->receiverName = $receiverName;
        $this->receiverAddress = $receiverAddress;
        $this->receiverEmail = $receiverEmail;
        $this->receiverTaxId = $receiverTaxId;
        $this->serviceName = $serviceName;
        $this->serviceCost = $serviceCost;
        $this->taxRate = $taxRate;
        $this->taxValue = $taxValue;
        $this->grossValue = $grossValue;
    }


    public function getServiceCost(): float
    {
        return $this->serviceCost;
    }

    public function getReceiverEmail(): string
    {
        return $this->receiverEmail;
    }

    public function setInvoiceNumber(string $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    public function getInvoiceDate(): DateTimeImmutable
    {
        return $this->invoiceDate;
    }

    public function getOrderNumber(): int
    {
        return $this->orderNumber;
    }

    public function getTaxRate(): int
    {
        return $this->taxRate;
    }

    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }

    public function jsonSerialize(): array
    {
        return [
            'invoice_number' => $this->invoiceNumber,
            'issue_date' => $this->invoiceDate->format('Y-m-d'),
            'receiver' => (object)[
                'name' => $this->receiverName,
                'address' => $this->receiverAddress,
                'tax_id' => $this->receiverTaxId
            ],
            'positions' => [
                (object)[
                    'order' => $this->orderNumber,
                    'name' => $this->serviceName,
                    'value' => $this->serviceCost,
                    'tax_rate' => $this->taxRate,
                    'tax_value' => $this->taxValue,
                ]
            ]
        ];
    }
}
