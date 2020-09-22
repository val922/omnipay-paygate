<?php

namespace Omnipay\PaygGate;

 use Illuminate\Http\Request;
 use Omnipay\Common\AbstractGateway;
 use Omnipay\PayGate\Message\CompleteResponse;
 
 /**
  *  PayGate Gateway
  *  @link TODO
  */
  class Gateway extends AbstractGateway
  {
    public $testMerchantId = '9F416C11-127B-4DE2-AC7F-D5710E4C5E0A';
    public $testSecretKey = 'secret';
    
    public function getName()
    {
      return 'PayGate';
    }
    
    public function getDefaultParameters()
    {
      return array(
        'merchantId'  => '',
        'keyVersion'  => '',
        'currency'    => '',
        'country'     => '',
        'locale'      => '',
        'payMethod'   => '',
        'payMethodDetail' => '',
        'notifyUrl'   => '',
        'returnUrl'   => '',
        'testMode'    => true,
      );
    }
    public function getCurrency()
    {
        return $this->getParameter('currency');
    }
    public function setCurrency($value)
    {
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
    public function getMerchantId()
    {
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
        return $this->getParameter('secretKey');
    }

    public function setSecretKey($value)
    {
        if($this->getTestMode()) {
            $value = $this->testSecretKey;
        }
        return $this->setParameter('secretKey', $value);
    }
    // BASE

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayGate\Message\PurchaseRequest', $parameters);
    }

    public function complete(array $parameters = array())
    {
        return new CompleteResponse($parameters, $this->getSecretKey());
    }

    // CUSTOM

    public function query(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayGate\Message\QueryRequest', $parameters);
    }
    
  }
