<?php
declare(strict_types = 1);


use Slothsoft\Core\StreamWrapper\StreamWrapperRegistrar;
use Slothsoft\Blob\BlobStreamWrapperFactory;

StreamWrapperRegistrar::registerStreamWrapper('blob', new BlobStreamWrapperFactory());
