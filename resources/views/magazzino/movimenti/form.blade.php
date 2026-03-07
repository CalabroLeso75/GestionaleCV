<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 font-weight-bold text-dark mb-0">🔁 Nuovo Movimento di Magazzino</h2>
    </x-slot>

    <div class="py-4">
        <div class="container" style="max-width: 750px;">
            <div class="card shadow-sm">
                <div class="card-body">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $e)<p class="mb-0 small">{{ $e }}</p>@endforeach
                        </div>
                    @endif

                    <form action="{{ route('magazzino.movimenti.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Tipo Movimento *</label>
                                <select name="movement_type" id="movement_type" class="form-select" required onchange="updateFields()">
                                    <option value="">-- Seleziona --</option>
                                    <option value="CARICO" {{ old('movement_type') == 'CARICO' ? 'selected' : '' }}>📥 CARICO (entrata stock)</option>
                                    <option value="SCARICO" {{ old('movement_type') == 'SCARICO' ? 'selected' : '' }}>📤 SCARICO (uscita stock)</option>
                                    <option value="TRASFERIMENTO" {{ old('movement_type') == 'TRASFERIMENTO' ? 'selected' : '' }}>🔄 TRASFERIMENTO (tra ubicazioni)</option>
                                    <option value="SMISTAMENTO" {{ old('movement_type') == 'SMISTAMENTO' ? 'selected' : '' }}>📋 SMISTAMENTO (distribuzione ai distretti)</option>
                                    <option value="ASSEGNAZIONE" {{ old('movement_type') == 'ASSEGNAZIONE' ? 'selected' : '' }}>👤 ASSEGNAZIONE (a persona/mezzo)</option>
                                    <option value="RITORNO" {{ old('movement_type') == 'RITORNO' ? 'selected' : '' }}>↩️ RITORNO (rientro materiale)</option>
                                    <option value="INVENTARIO" {{ old('movement_type') == 'INVENTARIO' ? 'selected' : '' }}>📊 INVENTARIO (rettifica inventariale)</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Data e Ora *</label>
                                <input type="datetime-local" name="movement_date" class="form-control" value="{{ old('movement_date', now()->format('Y-m-d\TH:i')) }}" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Prodotto *</label>
                                <select name="product_id" class="form-select" required>
                                    <option value="">-- Seleziona Prodotto --</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}" {{ old('product_id', $prefill['product_id'] ?? null) == $p->id ? 'selected' : '' }}>
                                            {{ $p->name }} {{ $p->code ? "({$p->code})" : '' }} — {{ strtoupper($p->unit_of_measure) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6">
                                <label class="form-label fw-bold">Quantità *</label>
                                <input type="number" name="quantity" class="form-control" min="1" step="1" value="{{ old('quantity', 1) }}" required>
                            </div>

                            <div class="col-6" id="field_source">
                                <label class="form-label">Ubicazione Origine</label>
                                <select name="source_location_id" class="form-select">
                                    <option value="">-- Nessuna --</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}" {{ old('source_location_id', $prefill['location_id'] ?? null) == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6" id="field_dest">
                                <label class="form-label">Ubicazione Destinazione</label>
                                <select name="destination_location_id" class="form-select">
                                    <option value="">-- Nessuna --</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}" {{ old('destination_location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6" id="field_assigned_user" style="display:none;">
                                <label class="form-label">Assegnato A (Dipendente)</label>
                                <select name="assigned_to_user_id" class="form-select">
                                    <option value="">-- Nessuno --</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ old('assigned_to_user_id') == $emp->id ? 'selected' : '' }}>{{ $emp->last_name }} {{ $emp->first_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Note</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Es. Riferimento fornitore, numero DDT, motivazione...">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('magazzino.movimenti.index') }}" class="btn btn-outline-secondary">Annulla</a>
                            <button type="submit" class="btn btn-primary">✅ Registra Movimento</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateFields() {
            const tipo = document.getElementById('movement_type').value;
            const assDiv = document.getElementById('field_assigned_user');
            assDiv.style.display = (tipo === 'ASSEGNAZIONE') ? 'block' : 'none';
        }
        updateFields();
    </script>
    @endpush
</x-app-layout>
