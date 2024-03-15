<?php

namespace App\Enums\Transaction;

enum TransactionAction
{
    case CREATE;
    case UPDATE;
    case DELETE;
}
