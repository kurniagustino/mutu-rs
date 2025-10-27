<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\HospitalSurveyIndicator;
use Illuminate\Auth\Access\HandlesAuthorization;

class HospitalSurveyIndicatorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HospitalSurveyIndicator');
    }

    public function view(AuthUser $authUser, HospitalSurveyIndicator $hospitalSurveyIndicator): bool
    {
        return $authUser->can('View:HospitalSurveyIndicator');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HospitalSurveyIndicator');
    }

    public function update(AuthUser $authUser, HospitalSurveyIndicator $hospitalSurveyIndicator): bool
    {
        return $authUser->can('Update:HospitalSurveyIndicator');
    }

    public function delete(AuthUser $authUser, HospitalSurveyIndicator $hospitalSurveyIndicator): bool
    {
        return $authUser->can('Delete:HospitalSurveyIndicator');
    }

    public function restore(AuthUser $authUser, HospitalSurveyIndicator $hospitalSurveyIndicator): bool
    {
        return $authUser->can('Restore:HospitalSurveyIndicator');
    }

    public function forceDelete(AuthUser $authUser, HospitalSurveyIndicator $hospitalSurveyIndicator): bool
    {
        return $authUser->can('ForceDelete:HospitalSurveyIndicator');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HospitalSurveyIndicator');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HospitalSurveyIndicator');
    }

    public function replicate(AuthUser $authUser, HospitalSurveyIndicator $hospitalSurveyIndicator): bool
    {
        return $authUser->can('Replicate:HospitalSurveyIndicator');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HospitalSurveyIndicator');
    }

}