<?php

namespace App\Mail\Transport;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\HttpTransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractApiTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BrevoTransport extends AbstractApiTransport
{
    private const HOST = 'api.brevo.com';
    private const ENDPOINT = '/v3/smtp/email';

    public function __construct(
        #[\SensitiveParameter] private readonly string $apiKey,
        ?HttpClientInterface $client = null,
        ?EventDispatcherInterface $dispatcher = null,
        ?LoggerInterface $logger = null,
    ) {
        parent::__construct($client, $dispatcher, $logger);
    }

    public function __toString(): string
    {
        return sprintf('brevo+api://%s', $this->getEndpoint());
    }

    protected function doSendApi(SentMessage $sentMessage, Email $email, Envelope $envelope): ResponseInterface
    {
        $response = $this->client->request('POST', 'https://'.$this->getEndpoint(), [
            'json' => $this->getPayload($email, $envelope),
            'headers' => [
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'api-key' => $this->apiKey,
            ],
        ]);

        try {
            $statusCode = $response->getStatusCode();
        } catch (\Throwable $e) {
            throw new HttpTransportException('Could not reach Brevo API: '.$e->getMessage(), $response, 0, $e);
        }

        if ($statusCode !== 201) {
            try {
                $result = $response->toArray(false);
                $message = $result['message'] ?? ('HTTP '.$statusCode);
                $code = $result['code'] ?? '';
            } catch (\Throwable) {
                $message = 'HTTP '.$statusCode;
                $code = '';
            }

            throw new HttpTransportException(
                sprintf('Unable to send an email via Brevo API: %s%s.', $message, $code ? ' ('.$code.')' : ''),
                $response
            );
        }

        $result = $response->toArray(false);
        if (isset($result['messageId'])) {
            $sentMessage->setMessageId($result['messageId']);
        }

        return $response;
    }

    private function getEndpoint(): string
    {
        return self::HOST.self::ENDPOINT;
    }

    private function getPayload(Email $email, Envelope $envelope): array
    {
        $email = MessageConverter::toEmail($email);

        $payload = [
            'sender' => $this->formatAddress($envelope->getSender()),
            'to' => $this->formatAddresses($this->getRecipients($email, $envelope)),
            'subject' => $email->getSubject(),
        ];

        if ($cc = $email->getCc()) {
            $payload['cc'] = $this->formatAddresses($cc);
        }
        if ($bcc = $email->getBcc()) {
            $payload['bcc'] = $this->formatAddresses($bcc);
        }
        if ($replyTo = $email->getReplyTo()) {
            $payload['replyTo'] = $this->formatAddress($replyTo[0]);
        }

        if ($html = $email->getHtmlBody()) {
            $payload['htmlContent'] = $html;
        }
        if ($text = $email->getTextBody()) {
            $payload['textContent'] = $text;
        }

        if ($attachments = $this->formatAttachments($email)) {
            $payload['attachment'] = $attachments;
        }

        $headers = [];
        foreach ($email->getHeaders()->all() as $header) {
            $name = $header->getName();
            if (in_array(strtolower($name), ['from', 'to', 'cc', 'bcc', 'subject', 'reply-to', 'sender', 'mime-version', 'content-type', 'content-transfer-encoding', 'date', 'message-id'], true)) {
                continue;
            }
            $headers[$name] = $header->getBodyAsString();
        }
        if ($headers) {
            $payload['headers'] = $headers;
        }

        return $payload;
    }

    private function formatAddress(Address $address): array
    {
        $out = ['email' => $address->getAddress()];
        if ($name = $address->getName()) {
            $out['name'] = $name;
        }

        return $out;
    }

    /**
     * @param  Address[]  $addresses
     */
    private function formatAddresses(array $addresses): array
    {
        return array_map(fn (Address $a) => $this->formatAddress($a), $addresses);
    }

    private function formatAttachments(Email $email): array
    {
        $out = [];
        foreach ($email->getAttachments() as $attachment) {
            $headers = $attachment->getPreparedHeaders();
            $filename = $headers->getHeaderParameter('content-disposition', 'filename') ?? 'file';
            $out[] = [
                'name' => $filename,
                'content' => base64_encode($attachment->getBody()),
            ];
        }

        return $out;
    }
}
