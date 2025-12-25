<?php

namespace App\View\Composers;

use App\Services\NavigationService;
use Illuminate\View\View;

class NavigationComposer
{
    public function compose(View $view): void
    {
        $user = auth()->user();
        $navigationService = new NavigationService($user);

        $view->with('navigationSections', $navigationService->getNavigationItems());
    }
}
