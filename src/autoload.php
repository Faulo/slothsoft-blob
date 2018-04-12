<?php


use Slothsoft\Blob\StreamWrapper\StreamWrapperRegistrar;
use Slothsoft\Blob\BlobStreamWrapperFactory;

StreamWrapperRegistrar::registerStreamWrapper('blob', new BlobStreamWrapperFactory());
