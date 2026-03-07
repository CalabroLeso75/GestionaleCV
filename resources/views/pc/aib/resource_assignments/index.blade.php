<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 font-weight-bold text-dark">
            Registro Globale Assegnazioni Personali a Dipendenti
        </h2>
    </x-slot>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Data Ora</th>
                                <th>Risorsa</th>
                                <th>Azione</th>
                                <th>Assegnatario (Dipendente)</th>
                                <th>Operatore a Sistema</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td class="ps-4 text-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    @if(class_basename($log->assignable_type) === 'Vehicle')
                                        🚗 Mezzo: {{ $log->assignable->targa ?? 'N/A' }} 
                                    @elseif(class_basename($log->assignable_type) === 'CompanyPhone')
                                        📱 SIM/Tel: {{ $log->assignable->numero ?? 'N/A' }}
                                    @else
                                        📱 Dispositivo: {{ $log->assignable->modello ?? 'N/A' }}
                                    @endif
                                </td>
                                <td>
                                    @if(str_contains($log->azione, 'Assegnazione'))
                                        <span class="badge bg-success">{{ $log->azione }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ $log->azione }}</span>
                                    @endif
                                </td>
                                <td class="fw-bold">
                                    @if($log->assignee)
                                        {{ $log->assignee->surname ?? '' }} {{ $log->assignee->name ?? '' }}
                                        <small class="text-muted d-block">{{ class_basename($log->assignee_type) == 'InternalEmployee' ? 'Dipendente Interno' : 'Esterno' }}</small>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="small">{{ $log->user->name ?? 'Sistema' }} {{ $log->user->surname ?? '' }}</td>
                                <td class="small text-muted">{{ $log->note }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-clipboard-list fa-3x mb-3 text-light"></i>
                                    <p class="mb-0">Nessun log di assegnazione personale trovato.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
