<?php
declare(strict_types = 1);
namespace Slothsoft\Blob;

use Slothsoft\Core\StreamWrapper\StreamWrapperRegistrar;
StreamWrapperRegistrar::registerStreamWrapper('blob', new BlobStreamWrapperFactory());
