<?php

namespace App\MessengerMiddleware;

use App\Message\InvoiceSenderMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

class MailAuditLog  implements MiddlewareInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $messengerMailAuditLogger)
    {
        $this->logger = $messengerMailAuditLogger;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();
        if (
            $envelope->last(ReceivedStamp::class)
            && is_a($envelope->getMessage(), InvoiceSenderMessage::class)
        ) {
            /** @var InvoiceSenderMessage$message */
            $this->logger->info(
                sprintf('Send invoice email to: %s', $message->getEmail()),
                [
                    'invoiceNumber' => $message->getInvoiceNumber(),
                    'sentTime' => date('Y-m-d H:i:s')
                ]
            );
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
