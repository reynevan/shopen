<?php

namespace Shopen\Blocks\Admin\Order\Show;

use Illuminate\Support\Number;
use Shopen\Blocks\Admin\Order\Show;
use Shopen\Core\Context;

class Status extends Show
{
    public function __construct(
        Context $context
    )
    {
        parent::__construct($context);
    }
}