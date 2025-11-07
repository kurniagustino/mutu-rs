<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\Page;

class ExportPdf extends Page
{
    protected static string $resource = UserResource::class;

    protected string $view = 'filament.resources.users.pages.export-pdf';
}
