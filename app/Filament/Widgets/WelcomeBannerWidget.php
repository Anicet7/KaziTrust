<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WelcomeBannerWidget extends Widget
{
    protected static string $view = 'filament.widgets.welcome-banner';
    protected static ?int $sort   = 1;
    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $user   = Auth::user();
        $tenant = $user->tenant;

        return [
            'userName'    => $user->name,
            'companyName' => $tenant?->name ?? '',
            'plan'        => $tenant?->subscription_plan ?? 'trial',
            'trialEndsAt' => $tenant?->trial_ends_at,
            'onTrial'     => $tenant?->onTrial() ?? false,
            'daysLeft'    => $tenant?->trial_ends_at
                                ? (int) now()->diffInDays($tenant->trial_ends_at, false)
                                : 0,
        ];
    }
}