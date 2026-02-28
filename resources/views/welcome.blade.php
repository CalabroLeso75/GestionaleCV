<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Benvenuto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="callout note mb-4">
                        <div class="callout-title">
                            <svg class="icon"><use xlink:href="{{ asset('svg/sprites.svg') }}#it-info-circle"></use></svg>
                            Gestione Personale Calabria Verde
                        </div>
                        <p>Il sistema è attualmente in fase di sviluppo. Questa pagina utilizza <strong>Bootstrap Italia</strong>.</p>
                    </div>

                    <div class="flex justify-center mt-4">
                        @if (Route::has('login'))
                            <div class="top-right links">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="btn btn-primary">Vai alla Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary">Accedi</a>

                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="btn btn-outline-primary ml-2">Registrati</a>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
