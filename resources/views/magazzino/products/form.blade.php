<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 font-weight-bold text-dark mb-0">
            {{ isset($product) ? 'Modifica Prodotto: ' . $product->name : 'Nuovo Prodotto a Catalogo' }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="card shadow-sm max-w-3xl mx-auto">
                <div class="card-body">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ isset($product) ? route('magazzino.prodotti.update', $product) : route('magazzino.prodotti.store') }}" method="POST">
                        @csrf
                        @if(isset($product))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label font-weight-bold">Nome Prodotto *</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Codice Interno (Opzionale)</label>
                                <input type="text" name="code" class="form-control" value="{{ old('code', $product->code ?? '') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Barcode (EAN/UPC)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                    <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode ?? $prefilled_barcode ?? '') }}" placeholder="Scansiona qui...">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Marca</label>
                                <input type="text" name="brand" class="form-control" value="{{ old('brand', $product->brand ?? '') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Categoria</label>
                                <input type="text" name="category" class="form-control" value="{{ old('category', $product->category ?? '') }}" placeholder="Es. DPI, Antincendio, Cancelleria...">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Unità di Misura *</label>
                                <select name="unit_of_measure" class="form-select" required>
                                    <option value="pz" {{ old('unit_of_measure', $product->unit_of_measure ?? '') == 'pz' ? 'selected' : '' }}>Pezzi (pz)</option>
                                    <option value="kg" {{ old('unit_of_measure', $product->unit_of_measure ?? '') == 'kg' ? 'selected' : '' }}>Chilogrammi (kg)</option>
                                    <option value="l" {{ old('unit_of_measure', $product->unit_of_measure ?? '') == 'l' ? 'selected' : '' }}>Litri (l)</option>
                                    <option value="m" {{ old('unit_of_measure', $product->unit_of_measure ?? '') == 'm' ? 'selected' : '' }}>Metri (m)</option>
                                    <option value="kit" {{ old('unit_of_measure', $product->unit_of_measure ?? '') == 'kit' ? 'selected' : '' }}>Kit</option>
                                    <option value="scatola" {{ old('unit_of_measure', $product->unit_of_measure ?? '') == 'scatola' ? 'selected' : '' }}>Scatola</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Tracciabilità Inventario</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="hidden" name="is_inventariable" value="0">
                                    <input class="form-check-input" type="checkbox" id="is_inventariable" name="is_inventariable" value="1" {{ old('is_inventariable', $product->is_inventariable ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_inventariable">
                                        Prodotto Inventariabile (Richiede numero Seriale/Matricola per ogni pezzo es. Motosega, PC)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <a href="{{ route('magazzino.prodotti.index') }}" class="btn btn-outline-secondary me-2">Annulla</a>
                            <button type="submit" class="btn btn-primary">Salva Prodotto</button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
