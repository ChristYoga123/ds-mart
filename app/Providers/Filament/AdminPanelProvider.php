<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Custom\Dashboard as CustomDashboard;
use App\Filament\Admin\Pages\Dashboard as PagesDashboard;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\NavigationBuilder;
use App\Filament\Admin\Resources\AkunResource;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\Admin\Resources\UserLogResource;
use Filament\Http\Middleware\AuthenticateSession;
use App\Filament\Admin\Resources\TransaksiResource;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use FilipFonal\FilamentLogManager\FilamentLogManager;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Filament\Admin\Resources\ProdukMutasiResource;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Admin DS Mart')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->resources([
                config('filament-logger.activity_resource')
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->pages([])
            ->widgets([])
            ->databaseNotifications()
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                FilamentEditProfilePlugin::make()
                    ->shouldShowAvatarForm(
                        value: true,
                        directory: 'avatars', // image will be stored in 'storage/app/public/avatars
                        rules: 'mimes:jpeg,png,webp|max:1024' //only accept jpeg and png files with a maximum size of 1MB
                    ),
                FilamentLogManager::make()
            ])
            // topbar
            ->userMenuItems([
                MenuItem::make()
                    ->label(fn() => 'Edit Profile')
                    ->url(fn (): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-cog-6-tooth'),
            ])
            // sidebar
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->groups([
                    NavigationGroup::make('')
                        ->items([
                            ...PagesDashboard::getNavigationItems(),
                            ...AkunResource::getNavigationItems(),
                            ...ProdukMutasiResource::getNavigationItems(),
                            ...TransaksiResource::getNavigationItems(),
                            ...UserLogResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Settings')
                        ->items([
                            NavigationItem::make('Roles & Permissions')
                                ->icon('heroicon-s-shield-check')
                                ->visible(fn() => auth()->user()->can('view_role') && auth()->user()->can('view_any_role'))
                                ->url(fn() => route('filament.admin.resources.shield.roles.index'))
                                ->isActiveWhen(fn() => request()->routeIs('filament.admin.resources.shield.roles.*')),
                            NavigationItem::make('Activity Logs')
                                ->icon('heroicon-s-exclamation-triangle')
                                ->visible(fn() => auth()->user()->can('view_activity'))
                                ->url(fn() => route('filament.admin.resources.activity-logs.index'))
                                ->isActiveWhen(fn() => request()->routeIs('filament.admin.resources.activity-logs.*')),
                            NavigationItem::make('Monitoring')
                                ->icon('heroicon-s-computer-desktop')
                                ->visible(fn() => auth()->user()->can('page_PulsePage'))
                                ->url(fn() => route('pulse'))
                                ->isActiveWhen(fn() => request()->routeIs('pulse')),
                            NavigationItem::make('Logs')
                                ->icon('heroicon-s-newspaper')
                                ->url(fn() => route('filament.admin.pages.logs'))
                                ->visible(fn() => auth()->user()->can('page_Logs'))
                                ->isActiveWhen(fn() => request()->routeIs('filament.admin.pages.logs')),
                        ]),
                ]);
            });
    }
}
