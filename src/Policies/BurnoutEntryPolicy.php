<?php
/**
 * Copyright (c) 2020. Def Studio (assistenza@defstudio.it)
 */

/** @noinspection PhpUnused */

/** @noinspection PhpUnusedParameterInspection */

namespace DefStudio\Burnout\Policies;

use App\Models\User;
use DefStudio\Burnout\Models\BurnoutEntry;
use Illuminate\Auth\Access\HandlesAuthorization;

class BurnoutEntryPolicy
{
    use HandlesAuthorization;


    public function viewAny(User $user)
    {
        return true;
    }


    public function view(User $user, BurnoutEntry $model)
    {
        return true;
    }


    public function create(User $user)
    {
        return true;
    }


    public function update(User $user, BurnoutEntry $model)
    {
        return true;
    }


    public function delete(User $user, BurnoutEntry $model)
    {
        return true;
    }
}
