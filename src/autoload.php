<?php

use Slothsoft\Blob\StreamWrapper;

stream_wrapper_register('blob', StreamWrapper::class);