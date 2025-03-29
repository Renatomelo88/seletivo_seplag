<?php

namespace App\Http\Controllers;
abstract class ApiController
{
    const PER_PAGE_DEFAULT = 20;
    const PER_PAGE_MAX = 50;
}