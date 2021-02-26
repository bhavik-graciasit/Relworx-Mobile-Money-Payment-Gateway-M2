<?php

namespace Graciasit\Relworxm\Controller\Checkout;

use Magento\Framework\App\Action;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;

class Process extends Action\Action implements CsrfAwareActionInterface
{
    protected $_checkoutSession;
    protected $resultRedirect;
    private $orderRepository;
    protected $_invoiceService;
    protected $_invoiceSender;
    protected $_orderSender;
    protected $request;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\ResultFactory $result,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->_checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->_invoiceService = $invoiceService;
        $this->_invoiceSender = $invoiceSender;
        $this->_orderSender = $orderSender;
        $this->_transaction = $transaction;
        $this->resultRedirect = $result;
        $this->request = $request;
        parent::__construct($context);
    }

    public function execute()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json,true);

        if(isset($data) && !empty($data))
        {
            $status = $data["status"];
            $OrderStringArr = explode('_',$data["customer_reference"]);
            $orderId = $OrderStringArr[2];

            if($status == 'success')
            {
                $_order = $this->orderRepository->get($orderId);
                $this->_orderSender->send($_order);

                if($_order->canInvoice())
                {
                    $invoice = $this->_invoiceService->prepareInvoice($_order);
                    $invoice->register();
                    $invoice->save();

                    $transactionSave = $this->_transaction->addObject(
                        $invoice
                    )->addObject(
                        $invoice->getOrder()
                    );

                    $transactionSave->save();
                    $this->_invoiceSender->send($invoice);
                    $_order->addStatusHistoryComment(
                        __('Notified customer about invoice #%1.', $invoice->getId())
                    )
                        ->setIsCustomerNotified(true)
                        ->save();
                }

                $_order->setState('processing');
                $_order->setStatus('processing');
                $_order->save();

            }
            else
            {
                $_order = $this->orderRepository->get($orderId);
                $_order->setState('canceled');
                $_order->setStatus('canceled');
                $_order->save();
            }

        }

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

