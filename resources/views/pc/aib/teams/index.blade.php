<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <h2 class="h4 font-weight-bold text-dark mb-0">Squadre AIB Operative</h2>
            <a href="{{ route('pc.aib.teams.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nuova Squadra
            </a>
        </div>
    </x-slot>

    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Sigla</th>
                                    <th>Postazioni</th>
                                    <th>Periodo / Campagna</th>
                                    <th>Mezzi</th>
                                    <th>Telefoni</th>
                                    <th>Dispositivi</th>
                                    <th>Membri</th>
                                    <th>Stato</th>
                                    <th class="text-end pe-4">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teams as $team)
                                <tr class="{{ $team->stato_operativo === 'Inattiva' ? 'opacity-50 bg-light' : '' }}">
                                    <td class="ps-4 fw-bold text-dark">{{ $team->sigla }}</td>
                                    <td>
                                        @foreach($team->stations as $station)
                                            <span class="d-block">{{ $station->nome }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div>{{ \Carbon\Carbon::parse($team->data_inizio)->format('d/m/Y') }} @if($team->data_fine) - {{ \Carbon\Carbon::parse($team->data_fine)->format('d/m/Y') }} @endif</div>
                                        @if($team->campagna)<small class="text-muted">{{ $team->campagna }}</small>@endif
                                    </td>
                                    <td>
                                        @forelse($team->vehicles as $vehicle)
                                            <span class="badge bg-secondary mb-1">{{ $vehicle->targa }}</span>
                                        @empty
                                            <span class="text-muted small">-</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @forelse($team->phones as $phone)
                                            <span class="badge bg-info text-dark mb-1">{{ $phone->numero }}</span>
                                        @empty
                                            <span class="text-muted small">-</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @forelse($team->mobileDevices as $device)
                                            <span class="badge bg-secondary mb-1" title="{{ $device->seriale ?? $device->imei }}">{{ $device->marca }} {{ $device->modello }}</span>
                                        @empty
                                            <span class="text-muted small">-</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            @foreach($team->members as $member)
                                                @php
                                                    $roles = [];
                                                    if($member->is_caposquadra) $roles[] = 'CSQ';
                                                    if($member->is_autista) $roles[] = 'AUT';
                                                    if($member->ruolo_specifico) $roles[] = $member->ruolo_specifico;
                                                    $rolesStr = empty($roles) ? 'OP' : implode(',', $roles);
                                                @endphp
                                                <span class="badge bg-light text-dark border" title="{{ $rolesStr }}: {{ $member->member->first_name }} {{ $member->member->last_name }}">
                                                    {{ $rolesStr }} - {{ $member->member->last_name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill {{ $team->stato_operativo === 'Pronto' ? 'bg-success' : ($team->stato_operativo === 'Inattiva' ? 'bg-secondary' : 'bg-warning') }}">
                                            {{ $team->stato_operativo }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($team->stato_operativo !== 'Inattiva')
                                            <a href="{{ route('pc.aib.teams.edit', $team) }}" class="btn btn-sm btn-outline-primary" title="Modifica Assegnatari e Risorse"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('pc.aib.teams.destroy', $team) }}" method="POST" class="d-inline" onsubmit="return confirm('Sei sicuro di voler chiudere e annullare questa squadra? Tutte le risorse verranno ritirate e rimesse in disponibilità.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger ms-1" title="Chiudi Squadra (Ritira Risorse)"><i class="fas fa-trash"></i></button>
                                            </form>
                                        @else
                                            <span class="badge bg-secondary mb-1">Archiviata</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        Nessuna squadra composta per oggi.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
