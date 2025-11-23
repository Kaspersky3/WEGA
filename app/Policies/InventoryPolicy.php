<?php

namespace App\Policies;

use App\Models\Inventory;
use App\Models\User;

class InventoryPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Inventory $inventory): bool
    {
        return true;
    }

    public function create(?User $user): bool
    {
        return true;
    }

    public function export(?User $user, ?Inventory $inventory = null): bool
    {
        return true;
    }
}

