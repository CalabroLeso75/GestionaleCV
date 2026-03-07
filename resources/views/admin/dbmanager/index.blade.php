<x-app-layout>
    <x-slot name="header">
        Database Manager
    </x-slot>

    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-database text-warning"></i> SQL Explorer (phpMyAdmin Base)</h1>
            <a href="{{ route('admin.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Torna all'Amministrazione</a>
        </div>
    </div>

    @if(isset($queryError) && $queryError)
        <div class="alert alert-danger shadow-sm border-left-danger">
            <h5 class="alert-heading font-weight-bold">Errore SQL</h5>
            <p class="mb-0 font-monospace">{{ $queryError }}</p>
        </div>
    @endif

    <div class="row">
        <!-- Sidebar Tabelle -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white font-weight-bold text-primary py-3">
                    <i class="fas fa-table mr-2"></i> Tabelle ({{ count($tables) }})
                </div>
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                    <div class="list-group list-group-flush rounded-0 font-monospace small">
                        @foreach($tables as $table)
                            <a href="{{ route('admin.dbmanager.index', ['table' => $table]) }}" 
                               class="list-group-item list-group-item-action py-2 {{ $selectedTable === $table ? 'active font-weight-bold' : '' }}">
                                <i class="fas fa-list-alt text-muted mr-2 {{ $selectedTable === $table ? 'text-white' : '' }}"></i> {{ $table }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonna Principale: Tabella e Query -->
        <div class="col-md-9 mb-4">
            <!-- Box SQL Query -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white font-weight-bold text-dark py-3 d-flex justify-content-between">
                    <span><i class="fas fa-terminal"></i> Console SQL</span>
                </div>
                <div class="card-body bg-light">
                    <form action="{{ route('admin.dbmanager.query') }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="query">
                        <div class="form-group">
                            <textarea name="query" class="form-control font-monospace" rows="4" 
                                      placeholder="Es: SELECT * FROM users WHERE id = 1 LIMIT 10;">{{ $sqlQuery ?? "SELECT * FROM " . ($selectedTable ?? 'users') . " LIMIT 50;" }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fas fa-play"></i> Esegui Query (Ctrl+Enter)</button>
                        <button type="button" class="btn btn-outline-secondary ml-2" onclick="document.querySelector('textarea[name=query]').value=''">Pulisci</button>
                    </form>
                </div>
            </div>

            <!-- Box Risultati Tabella/Query -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white font-weight-bold text-secondary py-3">
                    <i class="fas fa-search"></i> Visualizzazione Dati: <span class="text-primary">{{ $selectedTable ?? 'Risultato Custom Query' }}</span>
                </div>
                <div class="card-body p-0" style="overflow-x: auto;">
                    
                    {{-- Caso: Risultato di una Select Custom Mista --}}
                    @if(isset($queryResult) && is_array($queryResult))
                        @if(count($queryResult) > 0)
                            <table class="table table-hover table-bordered table-sm mb-0 font-monospace" style="font-size: 0.85rem;">
                                <thead class="thead-light">
                                    <tr>
                                        @foreach(array_keys((array)$queryResult[0]) as $col)
                                            <th>{{ $col }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($queryResult as $row)
                                        <tr>
                                            @foreach((array)$row as $val)
                                                <td class="text-truncate" style="max-width: 250px;" title="{{ $val }}">{{ Str::limit((string)$val, 50, '...') }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="p-3 text-muted small border-top">
                                Righe restituite: {{ count($queryResult) }}
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                Nessun risultato o la tabella è vuota.
                            </div>
                        @endif
                    
                    {{-- Caso: Stringa (es: query INSERT/UPDATE) --}}
                    @elseif(isset($queryResult) && is_string($queryResult))
                        <div class="p-4 text-center text-success font-weight-bold">
                            {{ $queryResult }}
                        </div>

                    {{-- Caso: Visualizzazione Standard di Tabella (Paginata) --}}
                    @elseif(isset($tableData) && $tableData->count() > 0)
                        <table class="table table-hover table-bordered table-sm mb-0 font-monospace" style="font-size: 0.85rem;">
                            <thead class="thead-light">
                                <tr>
                                    @foreach($columns as $col)
                                        <th class="text-nowrap">{{ $col }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tableData as $row)
                                    <tr>
                                        @foreach($columns as $col)
                                            <td class="text-nowrap text-truncate" style="max-width: 200px;" title="{{ $row->{$col} }}">
                                                {{ Str::limit((string)$row->{$col}, 40, '...') }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="p-3 border-top">
                            {{ $tableData->links('pagination::bootstrap-4') }}
                        </div>
                    @else
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-hand-pointer fa-2x mb-3 text-gray-300"></i><br>
                            Seleziona una tabella dalla barra laterale o esegui una query per visualizzare i dati.
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
