<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 font-weight-bold text-dark mb-0">Requisiti e Qualifiche AIB Personale</h2>
    </x-slot>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body bg-light rounded pt-4">
            <form action="{{ route('pc.aib.personnel.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase">Cerca Personale</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Nome, Cognome o Codice Fiscale..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold text-uppercase">Filtro Qualifica</label>
                    <select name="status" class="form-select">
                        <option value="">Tutti i dipendenti</option>
                        <option value="qualified" {{ request('status') == 'qualified' ? 'selected' : '' }}>Solo Qualificati AIB (Sì)</option>
                        <option value="unqualified" {{ request('status') == 'unqualified' ? 'selected' : '' }}>Non Qualificati (No)</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-2"></i>Filtra</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-white text-muted small text-uppercase border-bottom">
                    <tr>
                        <th class="ps-4">Dipendente</th>
                        <th>Codice Fiscale</th>
                        <th>Mansione / Profilo</th>
                        <th>Sede Assegnata</th>
                        <th class="text-center">Qualifica AIB Attiva</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-3 text-secondary">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $employee->last_name }} {{ $employee->first_name }}</div>
                                    <div class="small text-muted">{{ $employee->email ?? 'Nessuna email' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="font-monospace text-muted small">{{ $employee->tax_code }}</td>
                        <td>{{ $employee->position ?? '-' }}</td>
                        <td>{{ $employee->location_id ? 'Assegnata (ID: '.$employee->location_id.')' : '-' }}</td>
                        <td class="text-center">
                            <div class="form-check form-switch d-flex justify-content-center m-0 p-0">
                                <input class="form-check-input aib-toggle" type="checkbox" role="switch" 
                                    data-id="{{ $employee->id }}" 
                                    id="toggle_{{ $employee->id }}" 
                                    style="width: 3em; height: 1.5em; cursor:pointer;"
                                    {{ $employee->is_aib_qualified ? 'checked' : '' }}>
                            </div>
                            <small class="text-muted d-block mt-1 status-label" id="label_{{ $employee->id }}">
                                {{ $employee->is_aib_qualified ? 'Attiva' : 'Non Attiva' }}
                            </small>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-search fa-3x mb-3 text-light"></i>
                            <p class="mb-0">Nessun dipendente trovato con questi filtri.</p>
                            @if(request()->has('search') || request()->has('status'))
                                <a href="{{ route('pc.aib.personnel.index') }}" class="btn btn-sm btn-outline-secondary mt-3">Resetta Filtri</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top py-3">
            {{ $employees->links() }}
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggles = document.querySelectorAll('.aib-toggle');
            
            toggles.forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const empId = this.dataset.id;
                    const isChecked = this.checked;
                    const label = document.getElementById('label_' + empId);
                    
                    // Visual update immediately
                    if(isChecked) {
                        label.textContent = 'Attiva';
                        label.classList.add('text-success');
                        label.classList.remove('text-muted');
                    } else {
                        label.textContent = 'Non Attiva';
                        label.classList.remove('text-success');
                        label.classList.add('text-muted');
                    }
                    
                    // Disable while saving
                    this.disabled = true;

                    fetch(`/GestionaleCV/admin/pc/aib/personale/${empId}/toggle-aib`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.disabled = false;
                        if(data.success) {
                            // Show small toast or just let it be
                            console.log('Update success:', data.message);
                        } else {
                            // Revert on failure
                            this.checked = !isChecked;
                            alert('Errore durante l\'aggiornamento.');
                        }
                    })
                    .catch(error => {
                        this.disabled = false;
                        this.checked = !isChecked;
                        console.error('Error:', error);
                        alert('Errore di connessione al server.');
                    });
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
