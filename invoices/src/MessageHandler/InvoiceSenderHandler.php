<?php

namespace App\MessageHandler;

use App\Message\InvoiceSenderMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class InvoiceSenderHandler implements MessageHandlerInterface
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(InvoiceSenderMessage $invoiceSender)
    {
        $email = (new Email())
            ->from(new Address('invoices@slowhop.com', 'Slowhop'))
            ->to($invoiceSender->getEmail())
            ->replyTo('accountancy@slowhop.com')
            ->subject('Invoice for service from ' . $invoiceSender->getIssueDate())
            ->text(
                sprintf("hello\n, this is invoice for you!\n\n %s\n regards", $invoiceSender->getFileUrl())
            );

        $this->mailer->send($email);
    }
}
