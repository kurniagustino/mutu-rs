<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\StatusKategori;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatusKategoriPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StatusKategori');
    }

    public function view(AuthUser $authUser, StatusKategori $statusKategori): bool
    {
        return $authUser->can('View:StatusKategori');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StatusKategori');
    }

    public function update(AuthUser $authUser, StatusKategori $statusKategori): bool
    {
        return $authUser->can('Update:StatusKategori');
    }

    public function delete(AuthUser $authUser, StatusKategori $statusKategori): bool
    {
        return $authUser->can('Delete:StatusKategori');
    }

    public function restore(AuthUser $authUser, StatusKategori $statusKategori): bool
    {
        return $authUser->can('Restore:StatusKategori');
    }

    public function forceDelete(AuthUser $authUser, StatusKategori $statusKategori): bool
    {
        return $authUser->can('ForceDelete:StatusKategori');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StatusKategori');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StatusKategori');
    }

    public function replicate(AuthUser $authUser, StatusKategori $statusKategori): bool
    {
        return $authUser->can('Replicate:StatusKategori');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StatusKategori');
    }

}