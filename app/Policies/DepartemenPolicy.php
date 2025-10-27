<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Departemen;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartemenPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Departemen');
    }

    public function view(AuthUser $authUser, Departemen $departemen): bool
    {
        return $authUser->can('View:Departemen');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Departemen');
    }

    public function update(AuthUser $authUser, Departemen $departemen): bool
    {
        return $authUser->can('Update:Departemen');
    }

    public function delete(AuthUser $authUser, Departemen $departemen): bool
    {
        return $authUser->can('Delete:Departemen');
    }

    public function restore(AuthUser $authUser, Departemen $departemen): bool
    {
        return $authUser->can('Restore:Departemen');
    }

    public function forceDelete(AuthUser $authUser, Departemen $departemen): bool
    {
        return $authUser->can('ForceDelete:Departemen');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Departemen');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Departemen');
    }

    public function replicate(AuthUser $authUser, Departemen $departemen): bool
    {
        return $authUser->can('Replicate:Departemen');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Departemen');
    }

}