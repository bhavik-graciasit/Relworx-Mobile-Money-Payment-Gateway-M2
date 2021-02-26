<?php

namespace Graciasit\Relworxm\Model;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\ScopeInterface;

class MobilePayment extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_code = 'mobilepayment';
    protected $_isInitializeNeeded = true;
    protected $_isOnline = true;
	protected $_isGateway = true;

    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {

    }

    public function initialize($paymentAction, $stateObject)
    {
        $payment = $this->getInfoInstance();
        $order = $payment->getOrder();
        $order->setCanSendNewEmailFlag(false);

        $stateObject->setState('pending');
        $stateObject->setStatus('pending');
        $stateObject->setIsNotified(false);
    }

}
