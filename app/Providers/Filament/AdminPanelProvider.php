<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('💼 Facturation Pro')
            ->colors([
                'primary' => Color::Indigo,
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->font('Inter')
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('250px')
            ->maxContentWidth('full')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
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
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn(): string => Blade::render('<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"><style>
                    :root {
                        --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        --gradient-success: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                        --gradient-warning: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                        --gradient-info: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                    }
                    
                    /* Dashboard Header Enhancement */
                    .fi-page-header {
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        border-radius: 1rem;
                        padding: 1.5rem 2rem !important;
                        margin-bottom: 1.5rem;
                        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
                    }
                    
                    .fi-page-header h1 {
                        color: white !important;
                        font-weight: 700 !important;
                        font-size: 1.75rem !important;
                    }
                    
                    /* Stats Cards Enhancement */
                    .fi-wi-stats-overview-stat {
                        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
                        border-radius: 1rem !important;
                        border: none !important;
                        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                        overflow: hidden;
                    }
                    
                    .fi-wi-stats-overview-stat:hover {
                        transform: translateY(-4px);
                        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
                    }
                    
                    .dark .fi-wi-stats-overview-stat {
                        background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
                    }
                    
                    /* Chart Widget Enhancement */
                    .fi-wi-chart {
                        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
                        border-radius: 1.25rem !important;
                        border: none !important;
                        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.06);
                        overflow: hidden;
                        transition: all 0.3s ease;
                    }
                    
                    .fi-wi-chart:hover {
                        box-shadow: 0 8px 35px rgba(0, 0, 0, 0.12);
                    }
                    
                    .dark .fi-wi-chart {
                        background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
                    }
                    
                    /* Table Widget Enhancement */
                    .fi-wi-table {
                        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
                        border-radius: 1.25rem !important;
                        border: none !important;
                        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.06);
                        overflow: hidden;
                    }
                    
                    .dark .fi-wi-table {
                        background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
                    }
                    
                    /* Filter Section Enhancement */
                    .fi-page-dashboard-filters {
                        background: linear-gradient(145deg, #ffffff 0%, #f1f5f9 100%);
                        border-radius: 1rem;
                        padding: 1.25rem;
                        margin-bottom: 1.5rem;
                        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
                        border: 1px solid rgba(226, 232, 240, 0.8);
                    }
                    
                    .dark .fi-page-dashboard-filters {
                        background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
                        border-color: rgba(51, 65, 85, 0.5);
                    }
                    
                    /* Widget Header Style */
                    .fi-wi-chart-header, .fi-wi-table-header {
                        padding: 1.25rem 1.5rem !important;
                        border-bottom: 1px solid rgba(226, 232, 240, 0.6);
                    }
                    
                    .dark .fi-wi-chart-header, .dark .fi-wi-table-header {
                        border-bottom-color: rgba(51, 65, 85, 0.5);
                    }
                    
                    /* Sidebar Enhancement */
                    .fi-sidebar {
                        background: #ffffff !important;
                        border-right: 1px solid #e2e8f0 !important;
                    }
                    
                    .dark .fi-sidebar {
                        background: #1e293b !important;
                        border-right-color: #334155 !important;
                    }
                    
                    .fi-sidebar-header {
                        border-bottom: 1px solid #e2e8f0;
                    }
                    
                    .dark .fi-sidebar-header {
                        border-bottom-color: #334155;
                    }
                    
                    .fi-sidebar-nav-item-button {
                        border-radius: 0.75rem !important;
                        transition: all 0.2s ease !important;
                        color: #475569 !important;
                    }
                    
                    .dark .fi-sidebar-nav-item-button {
                        color: #cbd5e1 !important;
                    }
                    
                    .fi-sidebar-nav-item-button:hover {
                        background: #f1f5f9 !important;
                        color: #6366f1 !important;
                        transform: translateX(4px);
                    }
                    
                    .dark .fi-sidebar-nav-item-button:hover {
                        background: #334155 !important;
                        color: #818cf8 !important;
                    }
                    
                    .fi-sidebar-nav-item-active .fi-sidebar-nav-item-button {
                        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
                        color: #ffffff !important;
                    }
                    
                    /* Select Input Enhancement */
                    .fi-input-wrp {
                        border-radius: 0.75rem !important;
                        transition: all 0.2s ease;
                    }
                    
                    .fi-input-wrp:focus-within {
                        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
                    }
                    
                    /* Badge Enhancement */
                    .fi-badge {
                        font-weight: 600;
                        letter-spacing: 0.025em;
                    }
                    
                    /* Animations */
                    @keyframes fadeInUp {
                        from {
                            opacity: 0;
                            transform: translateY(20px);
                        }
                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }
                    
                    .fi-widgets {
                        animation: fadeInUp 0.5s ease-out;
                    }
                    
                    /* Scrollbar Styling */
                    ::-webkit-scrollbar {
                        width: 8px;
                        height: 8px;
                    }
                    
                    ::-webkit-scrollbar-track {
                        background: #f1f5f9;
                        border-radius: 4px;
                    }
                    
                    ::-webkit-scrollbar-thumb {
                        background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
                        border-radius: 4px;
                    }
                    
                    .dark ::-webkit-scrollbar-track {
                        background: #1e293b;
                    }
                </style>')
            );
    }
}
