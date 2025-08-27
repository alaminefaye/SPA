@extends('layouts.app')

@section('title', 'Rapport de présence des employés')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">Rapport de présence</h1>
        <div>
            <a href="{{ route('attendance.index') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-clipboard-list"></i> Liste journalière
            </a>
            <a href="{{ route('attendance.calendar') }}" class="btn btn-outline-secondary">
                <i class="fas fa-calendar-alt"></i> Vue calendrier
            </a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <h5 class="mb-0">{{ $stats['total_days'] }}</h5>
                        <div class="small">Jours dans la période</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <h5 class="mb-0">{{ $stats['present_count'] }}</h5>
                        <div class="small">Présences enregistrées</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <h5 class="mb-0">{{ $stats['absent_count'] }}</h5>
                        <div class="small">Absences enregistrées</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-times fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <h5 class="mb-0">{{ number_format($stats['present_count'] > 0 ? ($stats['present_count'] / ($stats['present_count'] + $stats['absent_count']) * 100) : 0, 1) }}%</h5>
                        <div class="small">Taux de présence</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-file-alt me-1"></i>
                Rapport de présence
            </div>
            <div>
                <form method="GET" action="{{ route('attendance.report') }}" class="row g-2">
                    <div class="col-auto">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                            <input type="date" class="form-control" name="start_date" value="{{ $startDate->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                            <input type="date" class="form-control" name="end_date" value="{{ $endDate->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <select class="form-select" name="employee_id">
                                <option value="">Tous les employés</option>
                                @foreach($employees as $id => $name)
                                    <option value="{{ $id }}" {{ $employeeId == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="attendanceReportTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Employé</th>
                            <th>Statut</th>
                            <th>Arrivée</th>
                            <th>Départ</th>
                            <th>Durée</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                        <tr class="{{ $attendance->present ? 'table-success' : 'table-danger' }}">
                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                            <td>{{ $attendance->employee->prenom }} {{ $attendance->employee->nom }}</td>
                            <td>
                                <span class="badge {{ $attendance->present ? 'bg-success' : 'bg-danger' }}">
                                    {{ $attendance->present ? 'Présent' : 'Absent' }}
                                </span>
                            </td>
                            <td>{{ $attendance->arrival_time ? \Carbon\Carbon::parse($attendance->arrival_time)->format('H:i') : '-' }}</td>
                            <td>{{ $attendance->departure_time ? \Carbon\Carbon::parse($attendance->departure_time)->format('H:i') : '-' }}</td>
                            <td>
                                @if($attendance->arrival_time && $attendance->departure_time)
                                    @php
                                        $arrival = \Carbon\Carbon::parse($attendance->arrival_time);
                                        $departure = \Carbon\Carbon::parse($attendance->departure_time);
                                        $duration = $departure->diffInMinutes($arrival);
                                        $hours = floor($duration / 60);
                                        $minutes = $duration % 60;
                                    @endphp
                                    {{ $hours }}h{{ $minutes < 10 ? '0' : '' }}{{ $minutes }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $attendance->notes ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Aucun enregistrement trouvé pour cette période</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    $(document).ready(function() {
        // Initialiser DataTables
        $('#attendanceReportTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json',
            },
            order: [[0, 'desc'], [1, 'asc']],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>
@endsection
