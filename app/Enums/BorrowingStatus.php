<?php

namespace App\Enums;

enum BorrowingStatus: string
{
    case pending = 'pending';
    case approved = 'approved';
    case rejected = 'rejected';
    case returned = 'returned';

}
