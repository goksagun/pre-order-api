<?php

namespace App\Notification\Adapter;

use App\Utils\StringHelper;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioAdapter implements NotificationInterface
{
    public const COUNTRY_CODE = '+90';

    private $sender;
    private $environment;
    private $twilioNumber;
    private $twilioNumberTo;

    public function __construct(Client $sender, string $environment, string $twilioNumber, string $twilioNumberTo)
    {
        $this->sender = $sender;
        $this->environment = $environment;
        $this->twilioNumber = $twilioNumber;
        $this->twilioNumberTo = $twilioNumberTo;
    }

    public function send($to, $message): bool
    {
        $to = $this->addCountryCodeToPhoneNumber($to);

        try {
            $this->sender->messages->create(
                $this->environment === 'prod' ? $to : $this->twilioNumberTo,
                [
                    'from' => $this->twilioNumber,
                    'body' => $message,
                ]
            );
        } catch (TwilioException $e) {
            // TODO: handle exception, log, mail etc...
            return false;
        }

        return true;
    }

    private function addCountryCodeToPhoneNumber($phone): string
    {
        if (StringHelper::startsWith($phone, ['05', '5'])) {
            $phone = self::COUNTRY_CODE.ltrim($phone, '0');
        }

        return $phone;
    }
}