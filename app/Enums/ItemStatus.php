<?php

namespace App\Enums;

enum ItemStatus: string
{
    case available = 'available';
    case borrowed = 'borrowed';

}
