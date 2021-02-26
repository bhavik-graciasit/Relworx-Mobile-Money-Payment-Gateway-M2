<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Graciasit\Relworxm\Controller\Checkout;

use Magento\Framework\App\Action;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;

class Prosuccess extends Action\Action implements CsrfAwareActionInterface
{
    protected $_checkoutSession;
    protected $resultRedirect;
    private $orderRepository;
    protected $_invoiceService;
    protected $_invoiceSender;
    protected $_orderSender;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\ResultFactory $result,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        $this->_checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->_invoiceService = $invoiceService;
        $this->_invoiceSender = $invoiceSender;
        $this->_orderSender = $orderSender;
        $this->_transaction = $transaction;
        $this->resultRedirect = $result;
        parent::__construct($context);
    }

    public function execute()
    {
        $orderData = $this->_checkoutSession->getLastRealOrder();
        $orderId = $orderData->getEntityId();

        $_order = $this->orderRepository->get($orderId);

        $_order->setState('pending_payment');
        $_order->setStatus('pending_payment');
        $_order->save();

        $this->_redirect('checkout/onepage/success');

    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

}

