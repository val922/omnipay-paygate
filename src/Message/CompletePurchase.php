<?php

namespace Omnipay\PayGate\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * PayGate Complete Response
 */
class CompleteResponse
{
    protected $status;
    protected $code;
    protected $paymentId;
    protected $paygateId;
    protected $requestId;
    protected $checksum;
    protected $post;
    protected $secretKey;

    public function __construct($data, $secretKey)
    {
        $this->secretKey = $secretKey;
        $this->status = $this->getByKeyFromArray('TRANSACTION_STATUS', $data);
        $this->code = $this->getByKeyFromArray('RESULT_CODE', $data);
        $this->paymentId = $this->getByKeyFromArray('REFERENCE', $data);
        $this->paygateId = $this->getByKeyFromArray('PAYGATE_ID', $data);
        $this->requestId = $this->getByKeyFromArray('PAY_REQUEST_ID', $data);
        $this->checksum = $this->getByKeyFromArray('CHECKSUM', $data);
        if(isset($data['CHECKSUM'])) {
            unset($data['CHECKSUM']);
        }
        $this->post = $data;
    }

    public function validate()
    {
        $validated = false;
        if($this->status == 1) {
            $data = $this->post;
            $checksum = "";
            foreach ($data as $dKey => $dValue) {
                $checksum .= $dValue;
            }
            $hash = md5($checksum . $this->getSecretKey());
            if($hash == $this->checksum) {
                $validated = true;
            }
        }
        return $validated;
    }

    public function isSuccessful()
    {
        $successful = false;
        if($this->validate() == true) {
            $successful = true;
        }
        return $successful;
    }

    public function getSecretKey()
    {
        return isset($this->secretKey) ? $this->secretKey : null;
    }

    public function getCode()
    {
        return isset($this->code) ? $this->code : null;
    }
    
        public function getMessage()
    {
        $code = $this->getCode();
        
        $messages = [
            '900001' => 'Call for Approval',
            '900002' => 'Card Expired',
            '900003' => 'Insufficient Funds',
            '900004' => 'Invalid Card Number',
            '900005' => 'Bank Interface Timeout',
            '900006' => 'Invalid Card',
            '900007' => 'Declined',
            '900009' => 'Lost Card',
            '900010' => 'Invalid Card Length',
            '900011' => 'Suspected Fraud',
            '900012' => 'Card Reported As Stolen',
            '900013' => 'Restricted Card',
            '900014' => 'Excessive Card Usage',
            '900015' => 'Card Blacklisted',
            '900207' => 'Declined; authentication failed',
            '990020' => 'Auth Declined',
            '991001' => 'Invalid expiry date',
            '991002' => 'Invalid Amount',

            '990017' => 'Auth Done',
            '900205' => 'Unexpected authentication result (phase 1)',
            '900206' => 'Unexpected authentication result (phase 1)',
            '990001' => 'Could not insert into Database',
            '990022' => 'Bank not available',
            '990053' => 'Error processing transaction',

            '900209' => 'Transaction verification failed (phase 2)',
            '900210' => 'Authentication complete; transaction must be restarted',
            '990024' => 'Duplicate Transaction Detected. Please check before submitting',
            '990028' => 'Transaction cancelled'
        ];

        return isset($messages[$code]) ? $messages[$code] : null;
    }

    public function getStatus()
    {
        $code = $this->status;
        $status = [
            '0' => 'Not Done',
            '1' => 'Approved',
            '2' => 'Declined',
            '3' => 'Cancelled',
            '4' => 'User Cancelled',
        ];

        return !empty($status[$code]) ? $status[$code] : 'Failed';
    }

    protected function getByKeyFromArray($key, $data)
    {
        return !empty($data[$key]) ? $data[$key] : '';
    }
}
