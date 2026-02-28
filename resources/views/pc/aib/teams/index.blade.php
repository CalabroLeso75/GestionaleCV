<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <span>Squadre AIB Operative</span>
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
                                    <th>Turno</th>
                                    <th>Membri</th>
                                    <th>Stato</th>
                                    <th class="text-end pe-4">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teams as $team)
                                <tr>
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
                                    <td>{{ $team->turno }}</td>
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
                                        <span class="badge rounded-pill {{ $team->stato_operativo === 'Pronto' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $team->stato_operativo }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-outline-danger ms-1"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
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
