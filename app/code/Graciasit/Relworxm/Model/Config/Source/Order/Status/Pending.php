<?php

namespace Graciasit\Relworxm\Model\Config\Source\Order\Status;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Config\Source\Order\Status;

class Pending extends Status
{
    protected $_stateStatuses = ['Pending'];
}
