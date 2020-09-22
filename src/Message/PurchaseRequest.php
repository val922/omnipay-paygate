<?php
namespace Omnipay\PayGate\Message;

use GuzzleHttp\Client;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * PayGate Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    public $testMerchantId = '10011072130';
    public $testSecretKey = 'secret';

    // set in config
    public function getCurrency()
    {
        if($this->getTestMode()) {
            return 'USD';
        }
        return $this->getParameter('currency');
    }
    public function setCurrency($value)
    {
        if($this->getTestMode()) {
            return 'USD';
        }
        return $this->setParameter('currency', $value);
    }
    public function getCountry()
    {
        return $this->getParameter('country');
    }
    public function setCountry($value)
    {
        return $this->setParameter('country', $value);
    }
    public function getLocale()
    {
        return $this->getParameter('locale');
    }
    public function setLocale($value)
    {
        return $this->setParameter('locale', $value);
    }
    public function getPayMethod()
    {
        return $this->getParameter('payMethod');
    }
    public function setPayMethod($value)
    {
        return $this->setParameter('payMethod', $value);
    }
    public function getPayMethodDetail()
    {
        return $this->getParameter('payMethodDetail');
    }
    public function setPayMethodDetail($value)
    {
        return $this->setParameter('payMethodDetail', $value);
    }
    
    
    // set on payment request
    public function getNotifyUrl()
    {
        return $this->getParameter('notifyUrl');
    }
    public function setNotifyUrl($value)
    {
        return $this->setParameter('notifyUrl', $value);
    }
    public function getReturnUrl()
    {
        return $this->getParameter('returnUrl');
    }
    public function setReturnUrl($value)
    {
        return $this->setParameter('returnUrl', $value);
    }
    public function getReference()
    {
        return $this->getParameter('reference');
    }
    public function setReference($value)
    {
        return $this->setParameter('reference', $value);
    }
    public function getAmount()
    {
        return $this->getParameter('amount');
    }
    public function setAmount($value)
    {
        return $this->setParameter('amount', $value);
    }
    
    
    
    public function getUserEmail()
    {
        return $this->getParameter('userEmail');
    }
    public function setUserEmail($value)
    {
        return $this->setParameter('userEmail', $value);
    }
    public function getUserName()
    {
        return $this->getParameter('userName');
    }
    public function setUserName($value)
    {
        return $this->setParameter('userName', $value);
    }
    public function getUserId()
    {
        return $this->getParameter('userId');
    }
    public function setUserId($value)
    {
        return $this->setParameter('userId', $value);
    }
    public function getUserPhone()
    {
        return $this->getParameter('userPhone');
    }
    public function setUserPhone($value)
    {
        return $this->setParameter('userPhone', $value);
    }




    public function getMerchantId()
    {
        if($this->getTestMode()) {
            return $this->testMerchantId;
        }
        return $this->getParameter('merchantId');
    }
    public function setMerchantId($value)
    {
        if($this->getTestMode()) {
            $value = $this->testMerchantId;
        }
        return $this->setParameter('merchantId', $value);
    }

    public function getSecretKey()
    {
        if($this->getTestMode()) {
            return $this->testSecretKey;
        }
        return $this->getParameter('secretKey');
    }

    public function setSecretKey($value)
    {
        if($this->getTestMode()) {
            $value = $this->testSecretKey;
        }
        return $this->setParameter('secretKey', $value);
    }

    public function getData()
    {
        $this->validate(
            'reference',
            'amount',
            'userEmail',
            'userPhone',
            'userId',
            'userName'
        );

        $data = [];
        $data['PAYGATE_ID'] = $this->getMerchantId();
        $data['REFERENCE'] = $this->getReference();
        $data['AMOUNT'] = $this->getAmount();
        $data['CURRENCY'] = $this->getCurrency();
        $data['RETURN_URL'] = $this->getReturnUrl();
        $data['TRANSACTION_DATE'] = date('Y-m-d H:i:s', time());
        $data['LOCALE'] = $this->getLocale();
        $data['COUNTRY'] = $this->getCountry();
        $data['EMAIL'] = $this->getUserEmail();

        if(!$this->getTestMode() &&
            !empty($this->getPayMethod()) && !empty($this->getPayMethodDetail())) {
            $data['PAY_METHOD'] = $this->getPayMethod();
            $data['PAY_METHOD_DETAIL'] = $this->getPayMethodDetail();
        }

        $data['NOTIFY_URL'] = $this->getNotifyUrl();
        $data['USER1'] = $this->getUserId();
        $data['USER2'] = $this->getUserName();
        $data['USER3'] = $this->getUserPhone();

        $data['CHECKSUM'] = $this->generateSignature($data);

        return $data;
    }

    public function generateSignature($data)
    {
        if (empty($data)) {
            throw new InvalidRequestException('Missing data parameters');
        }
        $checksum = "";
        foreach ($data as $dKey => $dValue) {
            $checksum .= $dValue;
        }
        return md5($checksum . $this->getSecretKey());
    }


    public function sendData($data)
    {
        $client = new Client();
        $httpResponse = $client->post($this->getEndpoint(), ['form_params' => $data]);
        $this->response = new PurchaseResponse($this, $httpResponse->getBody()->getContents(), $this->getTestMode());
        return $this->response;
    }

    public function getEndpoint()
    {
        /**
        * Consider the transaction API callback
        */
        return 'https://secure.paygate.co.za/payweb3/initiate.trans';
    }
}
