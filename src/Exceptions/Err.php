<?php

namespace Cc\Labama\Exceptions;

use Exception;

class Err extends Exception
{
    public function render()
    {
        return err($this->getMessage());
    }
}
