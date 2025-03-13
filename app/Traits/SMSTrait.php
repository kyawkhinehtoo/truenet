<?php

namespace App\Traits;

use App\Models\SmsGateway;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;

trait SMSTrait
{
    private const PHONE_PATTERNS = [
        'PLUS959' => "/^\+959[0-9]{7,9}$/",
        '959' => "/^959[0-9]{7,9}$/",
        '0959' => "/^0959[0-9]{7,9}$/",
        '9' => "/^9[0-9]{7,9}$/",
        '09' => "/^09[0-9]{7,9}$/"
    ];

    private const PHONE_DELIMITERS = [",", ";", ":", "/"];
    private ?string $single_sms_url = null;
    private ?string $bulk_sms_url = null;
    private ?string $sms_status_url = null;
    private ?string $sid = null;
    private ?string $token = null;
    private ?string $sender_id = null;
    private ?array $header = null;

    public function __construct()
    {
        $this->initializeSMSGateway();
    }

    private function initializeSMSGateway(): void
    {
        $smsgateway = SmsGateway::first();

        if ($smsgateway && $smsgateway->status === '1') {
            $this->single_sms_url = $smsgateway->single_sms;
            $this->bulk_sms_url = $smsgateway->bulk_sms;
            $this->sms_status_url = $smsgateway->info;
            $this->sid = $smsgateway->sid;
            $this->token = $smsgateway->token;
            $this->sender_id = $smsgateway->sender_id;
            $this->header = ['Authorization' => 'Bearer ' . base64_encode("{$this->sid}:{$this->token}")];
        }
    }

    private function sanitisePhone(string $phone_no): string
    {
        $phone_no = preg_replace('/[^0-9]/', '', $phone_no);

        if (preg_match("/^\+959[0-9]{7,9}$/", $phone_no)) {
            return '09' . substr($phone_no, 4);
        } elseif (preg_match("/^959[0-9]{7,9}$/", $phone_no)) {
            return '09' . substr($phone_no, 3);
        } elseif (preg_match("/^0959[0-9]{7,9}$/", $phone_no)) {
            return '09' . substr($phone_no, 4);
        } elseif (preg_match("/^9[0-9]{7,9}$/", $phone_no)) {
            return '0' . $phone_no;
        } elseif (preg_match("/^09[0-9]{7,9}$/", $phone_no)) {
            return $phone_no;
        }

        return $phone_no;
    }

    public function checkPhoneString(string $cus_phone): string
    {
        $delimiters = [",", ";", ":", "/","|"];
        $cus_phone = str_replace($delimiters, ",", $cus_phone);

        $phones = strpos($cus_phone, ',') !== false ? explode(',', $cus_phone) : [$cus_phone];
        $phones = array_unique(array_map(fn($phone) => $this->sanitisePhone(trim($phone)), $phones));

        return implode(",", $phones);
    }

    private function processBulkSMS(array $phones, string $message): bool
    {
        $sms_response = $this->sendSMS($phones, $message, true);
        Storage::append('phone.log', json_encode($sms_response));
        return isset($sms_response['status']) ;
    }

    private function processSingleSMS(string $phone, string $message): bool
    {
        $sms_response = $this->sendSMS($phone, $message);
        return isset($sms_response['status']) && $this->checkSMSStatus($sms_response['messageId']);
    }

    public function deliverSMS($phones, string $message): bool
    {
        if (is_array($phones)) {
            \Log::debug('Before sanitise Phone No. : ' . json_encode($phones));
            $phones = array_unique(array_map(fn($phone) => $this->sanitisePhone(trim($phone)), $phones));
            \Log::debug('After sanitise Phone No. : ' . json_encode($phones));
            $phones = array_values(array_filter($phones)); // Remove empty values and reindex
            \Log::debug('After array filter Phone No. : ' . json_encode($phones));
            return $this->processBulkSMS($phones, $message);
        }

        $phones = $this->checkPhoneString($phones);
        Storage::append('phone.log', json_encode($phones));

        $phone_array = strpos($phones, ',') !== false ? explode(',', $phones) : [$phones];
        
        return $this->processSingleSMS($phone_array[0], $message);
    }

    /**
     * Send SMS message to one or multiple recipients
     *
     * @param string|array $phone Single phone number or array of phone numbers
     * @param string $message Message content
     * @param bool $bulk Whether to use bulk SMS API
     * @return array|null Response from SMS gateway or null on failure
     */
    public function sendSMS($phone, string $message, bool $bulk = false): ?array
        {
            if (!$this->header || !$this->single_sms_url || !$this->bulk_sms_url) {
                \Log::error('SMS Gateway not properly initialized');
                return null;
            }
    
            $url = $bulk ? $this->bulk_sms_url : $this->single_sms_url;
    
            if (empty($url)) {
                \Log::error('SMS URL not configured');
                return null;
            }
    
           $url = $bulk
                ?  $this->bulk_sms_url
                :  $this->single_sms_url;
    
            $postInput = $bulk
                ? ["messages" => [["to" => (array) $phone, "message" => $message, "from" => $this->sender_id]]]
                : ['from' => $this->sender_id, 'to' => trim($phone), 'message' => trim($message)];
            
            try {
                \Log::debug('SMS URL: ' . json_encode($url));
              
                \Log::debug('Message : ' . json_encode($postInput));
                $client = new Client();
                $response = $client->request('POST', $url, ['json' => $postInput, 'headers' => $this->header]);
                $responseBody = json_decode($response->getBody(), true);
                return $responseBody;
                
                \Log::error('SMS failed. Response: ' . json_encode($responseBody));
            } catch (\Exception $e) {
                \Log::error('SMS sending failed: ' . $e->getMessage());
            }
        
        return null;
    }

    private function checkSMSStatus(string $messageId): bool
    {
        if (!$this->header) {
            \Log::error('SMS status check failed: Header not initialized.');
            return false;
        }

        try {
            $client = new Client();
            $response = $client->request('GET', $this->sms_status_url . $messageId, ['headers' => $this->header]);
            $responseBody = json_decode($response->getBody(), true);

            return isset($responseBody['status']) && $responseBody['status'] === 'Sent';
        } catch (\Exception $e) {
            \Log::error('SMS status check failed: ' . $e->getMessage(), ['messageId' => $messageId]);
        }

        return false;
    }
}