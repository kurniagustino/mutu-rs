<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ImutCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImutCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ImutCategory');
    }

    public function view(AuthUser $authUser, ImutCategory $imutCategory): bool
    {
        return $authUser->can('View:ImutCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ImutCategory');
    }

    public function update(AuthUser $authUser, ImutCategory $imutCategory): bool
    {
        return $authUser->can('Update:ImutCategory');
    }

    public function delete(AuthUser $authUser, ImutCategory $imutCategory): bool
    {
        return $authUser->can('Delete:ImutCategory');
    }

    public function restore(AuthUser $authUser, ImutCategory $imutCategory): bool
    {
        return $authUser->can('Restore:ImutCategory');
    }

    public function forceDelete(AuthUser $authUser, ImutCategory $imutCategory): bool
    {
        return $authUser->can('ForceDelete:ImutCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ImutCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ImutCategory');
    }

    public function replicate(AuthUser $authUser, ImutCategory $imutCategory): bool
    {
        return $authUser->can('Replicate:ImutCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ImutCategory');
    }

}