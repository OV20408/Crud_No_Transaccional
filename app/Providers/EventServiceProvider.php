<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

// IMPORTANTE: importa los eventos y listeners
use App\Events\UsuarioEstadoActualizado;
use App\Events\CursoCreadoOActualizado;
use App\Listeners\EnviarEstadoUsuarioAIncendios;
use App\Listeners\EnviarCursoAIncendios;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UsuarioEstadoActualizado::class => [
            EnviarEstadoUsuarioAIncendios::class,
        ],

        CursoCreadoOActualizado::class => [
            EnviarCursoAIncendios::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
