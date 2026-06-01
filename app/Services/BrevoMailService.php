<?php

namespace App\Services;

use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;

class BrevoMailService
{
    protected $apiInstance;

    public function __construct()
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', config('brevo.api_key'));
        $this->apiInstance = new TransactionalEmailsApi(new Client(), $config);
    }

    public function sendEmail($to, $subject, $htmlContent)
    {
        $sendSmtpEmail = new SendSmtpEmail();
        $sendSmtpEmail['to'] = [['email' => $to]];
        $sendSmtpEmail['subject'] = $subject;
        $sendSmtpEmail['htmlContent'] = $htmlContent;
        $sendSmtpEmail['sender'] = [
            'email' => env('MAIL_FROM_ADDRESS', 'tomas_munoz82212@elpoli.edu.co'),
            'name' => env('MAIL_FROM_NAME', 'Sistema CRU')
        ];

        try {
            $result = $this->apiInstance->sendTransacEmail($sendSmtpEmail);
            \Log::info('Correo enviado con ID: ' . $result->getMessageId());
            return true;
        } catch (\Exception $e) {
            \Log::error('Brevo API Error: ' . $e->getMessage());
            return false;
        }
    }
}