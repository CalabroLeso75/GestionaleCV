<x-app-layout>
    <x-slot name="header">
        Stile e Template
    </x-slot>

    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div style="font-size: 4em;">🎨</div>
                    <h4 class="mt-3">Personalizzazione Grafica</h4>
                    <p class="text-muted">
                        Questa sezione permetterà di personalizzare l'aspetto del sito:<br>
                        colori, logo, font, layout dell'header e del footer.
                    </p>
                    <span class="badge bg-info">In sviluppo</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">← Torna all'Amministrazione</a>
    </div>
</x-app-layout>
