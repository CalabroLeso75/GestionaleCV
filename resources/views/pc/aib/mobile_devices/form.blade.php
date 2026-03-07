<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 font-weight-bold text-dark">
            {{ isset($device) ? 'Modifica Dispositivo: ' . $device->marca . ' ' . $device->modello : 'Nuovo Dispositivo Mobile' }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="card shadow-sm">
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ isset($device) ? route('pc.aib.mobile_devices.update', $device) : route('pc.aib.mobile_devices.store') }}" method="POST">
                        @csrf
                        @if(isset($device))
                            @method('PUT')
                        @endif

                        <!-- ---- DATI IDENTIFICATIVI ---- -->
                        <h5 class="fw-bold mb-3 border-bottom pb-2">📋 Dati Identificativi</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tipo *</label>
                                <select name="tipo" class="form-select" required>
                                    <option value="smartphone" {{ old('tipo', $device->tipo ?? 'smartphone') == 'smartphone' ? 'selected' : '' }}>📱 Smartphone</option>
                                    <option value="tablet" {{ old('tipo', $device->tipo ?? '') == 'tablet' ? 'selected' : '' }}>💻 Tablet</option>
                                    <option value="altro" {{ old('tipo', $device->tipo ?? '') == 'altro' ? 'selected' : '' }}>Altro</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Marca *</label>
                                <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror" value="{{ old('marca', $device->marca ?? '') }}" placeholder="Apple, Samsung, Xiaomi..." required>
                                @error('marca')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Modello *</label>
                                <input type="text" name="modello" class="form-control @error('modello') is-invalid @enderror" value="{{ old('modello', $device->modello ?? '') }}" placeholder="iPhone 15, Galaxy S24..." required>
                                @error('modello')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Colore</label>
                                <input type="text" name="colore" class="form-control" value="{{ old('colore', $device->colore ?? '') }}" placeholder="Nero, Bianco...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Anno Acquisto</label>
                                <input type="number" name="anno_acquisto" class="form-control" min="2000" max="2099" value="{{ old('anno_acquisto', $device->anno_acquisto ?? '') }}" placeholder="{{ date('Y') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Cod. Inventariale (Asset)</label>
                                <input type="text" name="asset_code" class="form-control @error('asset_code') is-invalid @enderror" value="{{ old('asset_code', $device->asset_code ?? '') }}" placeholder="CV-MOB-001">
                                @error('asset_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Stato *</label>
                                <select name="stato" class="form-select" required>
                                    @foreach(['Attivo', 'Inattivo', 'Manutenzione', 'Dismesso'] as $s)
                                        <option value="{{ $s }}" {{ old('stato', $device->stato ?? 'Attivo') == $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Codice IMEI</label>
                                <input type="text" name="imei" class="form-control @error('imei') is-invalid @enderror" value="{{ old('imei', $device->imei ?? '') }}" placeholder="15 cifre">
                                @error('imei')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Numero Seriale (SN)</label>
                                <input type="text" name="seriale" class="form-control @error('seriale') is-invalid @enderror" value="{{ old('seriale', $device->seriale ?? '') }}">
                                @error('seriale')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">N° Telefono Associato</label>
                                <input type="text" name="numero_telefono" class="form-control" value="{{ old('numero_telefono', $device->numero_telefono ?? '') }}" placeholder="+39 3xx...">
                            </div>
                        </div>

                        <!-- ---- SPECIFICHE TECNICHE (FACOLTATIVE) ---- -->
                        <h5 class="fw-bold mb-3 border-bottom pb-2">⚙️ Specifiche Tecniche <small class="text-muted fw-normal">(facoltative)</small></h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Sistema Operativo</label>
                                <select name="sistema_operativo" class="form-select">
                                    <option value="">-- Seleziona --</option>
                                    @foreach(['Android', 'iOS', 'iPadOS', 'HarmonyOS', 'Windows', 'Altro'] as $os)
                                        <option value="{{ $os }}" {{ old('sistema_operativo', $device->sistema_operativo ?? '') == $os ? 'selected' : '' }}>{{ $os }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Versione OS</label>
                                <input type="text" name="versione_os" class="form-control" value="{{ old('versione_os', $device->versione_os ?? '') }}" placeholder="17.4, 14...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Schermo (pollici)</label>
                                <input type="number" name="dimensione_schermo" class="form-control" step="0.1" min="4" max="15" value="{{ old('dimensione_schermo', $device->dimensione_schermo ?? '') }}" placeholder="6.1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">RAM</label>
                                <select name="memoria_ram" class="form-select">
                                    <option value="">--</option>
                                    @foreach(['2GB', '3GB', '4GB', '6GB', '8GB', '12GB', '16GB'] as $r)
                                        <option value="{{ $r }}" {{ old('memoria_ram', $device->memoria_ram ?? '') == $r ? 'selected' : '' }}>{{ $r }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Storage</label>
                                <select name="memoria_storage" class="form-select">
                                    <option value="">--</option>
                                    @foreach(['16GB', '32GB', '64GB', '128GB', '256GB', '512GB', '1TB'] as $s)
                                        <option value="{{ $s }}" {{ old('memoria_storage', $device->memoria_storage ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Processore</label>
                                <input type="text" name="processore" class="form-control" value="{{ old('processore', $device->processore ?? '') }}" placeholder="Apple A17 Pro, Snapdragon 8...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fotocamera Principale</label>
                                <input type="text" name="fotocamera_principale" class="form-control" value="{{ old('fotocamera_principale', $device->fotocamera_principale ?? '') }}" placeholder="48MP Triple, 12MP Dual...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Batteria</label>
                                <input type="text" name="batteria_mah" class="form-control" value="{{ old('batteria_mah', $device->batteria_mah ?? '') }}" placeholder="4500 mAh">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Connettività</label>
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="5g" id="field_5g" value="1" {{ old('5g', $device->{'5g'} ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="field_5g">5G</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="nfc" id="field_nfc" value="1" {{ old('nfc', $device->nfc ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="field_nfc">NFC</label>
                                </div>
                            </div>
                        </div>

                        <!-- ---- NOTE ---- -->
                        <div class="mb-4">
                            <label class="form-label">Note aggiuntive</label>
                            <textarea name="note" class="form-control" rows="3">{{ old('note', $device->note ?? '') }}</textarea>
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('pc.aib.mobile_devices.index') }}" class="btn btn-outline-secondary">Annulla</a>
                            <button type="submit" class="btn btn-primary">
                                {{ isset($device) ? '💾 Aggiorna Dispositivo' : '✅ Salva Dispositivo' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
