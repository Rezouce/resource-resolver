<?php
namespace ResourceResolver\Exception;

use InvalidArgumentException as BaseInvalidArgumentException;

class InvalidArgumentException extends BaseInvalidArgumentException implements ResourceResolverExceptionInterface
{
}
