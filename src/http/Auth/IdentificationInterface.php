<?php
namespace Gb\Php2\http\Auth;

use Gb\Php2\Blog\User;
use Gb\Php2\http\Request;

interface IdentificationInterface 
{
    public function user(Request $request): User;
}

