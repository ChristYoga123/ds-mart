<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\NavigationBuilder;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\Gudang\Resources\ProdukResource;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Gudang\Resources\ProdukMutasiResource;
use Filament\Http\Middleware\DisableBladeIconComponents;
use App\Filament\Gudang\Resources\ProdukGolonganResource;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

class GudangPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('gudang')
            ->path('gudang')
            // ->login()
            ->brandName('Gudang DS Mart')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Gudang/Resources'), for: 'App\\Filament\\Gudang\\Resources')
            ->discoverPages(in: app_path('Filament/Gudang/Pages'), for: 'App\\Filament\\Gudang\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Gudang/Widgets'), for: 'App\\Filament\\Gudang\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
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
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentEditProfilePlugin::make()
                    ->shouldShowAvatarForm(
                        value: true,
                        directory: 'avatars', // image will be stored in 'storage/app/public/avatars
                        rules: 'mimes:jpeg,png,webp|max:1024' //only accept jpeg and png files with a maximum size of 1MB
                    ),
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
                            ...Dashboard::getNavigationItems(),
                            ...ProdukResource::getNavigationItems(),
                            ...ProdukMutasiResource::getNavigationItems(),
                            ...ProdukGolonganResource::getNavigationItems(),
                        ]),
                ]);
            })
            ->spa();
    }
}
