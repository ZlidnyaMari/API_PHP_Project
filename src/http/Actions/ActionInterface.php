<?php
namespace Gb\Php2\http\Actions;

use Gb\Php2\http\Request;
use Gb\Php2\http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}
