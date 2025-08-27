@extends('layouts.app')

@section('title', 'Calendrier de présence des employés')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">Calendrier de présence</h1>
        <div>
            <a href="{{ route('attendance.index') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-clipboard-list"></i> Liste journalière
            </a>
            <a href="{{ route('attendance.report') }}" class="btn btn-outline-secondary">
                <i class="fas fa-chart-bar"></i> Rapports
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-calendar-alt me-1"></i>
                Présences du mois de {{ $month->format('F Y') }}
            </div>
            <div>
                <form method="GET" action="{{ route('attendance.calendar') }}" class="d-flex align-items-center">
                    <div class="input-group me-2" style="width: 220px;">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <input type="month" name="month" class="form-control" value="{{ $month->format('Y-m') }}">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="calendarTable">
                    <thead>
                        <tr>
                            <th class="bg-light">Employé</th>
                            @foreach($calendar as $day)
                            <th class="text-center {{ $day->isWeekend() ? 'bg-light' : '' }} {{ $day->isToday() ? 'bg-info text-white' : '' }}" 
                                style="min-width: 40px; font-size: 0.85rem;">
                                <div>{{ $day->format('d') }}</div>
                                <small>{{ substr($day->format('D'), 0, 1) }}</small>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                        <tr>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    @if($employee->photo)
                                    <img src="{{ Storage::url($employee->photo) }}" alt="Photo de {{ $employee->nom_complet }}" 
                                        class="rounded-circle me-2" width="30" height="30">
                                    @else
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-2" 
                                        style="width: 30px; height: 30px; color: white;">
                                        <i class="fas fa-user fa-sm"></i>
                                    </div>
                                    @endif
                                    <span>{{ $employee->prenom }} {{ $employee->nom }}</span>
                                </div>
                            </td>
                            
                            @foreach($calendar as $day)
                            @php 
                                $dayAttendance = $attendances[$employee->id][$day->format('Y-m-d')] ?? null;
                                $present = $dayAttendance && $dayAttendance->first()->present;
                                $date = $day->format('Y-m-d');
                                $linkDate = $day->format('d/m/Y');
                            @endphp
                            <td class="text-center align-middle {{ $present ? 'bg-success bg-opacity-25' : ($day->isPast() ? 'bg-danger bg-opacity-10' : '') }}">
                                @if($dayAttendance)
                                    @if($present)
                                        <a href="{{ route('attendance.index', ['date' => $date]) }}" 
                                           class="text-success" 
                                           data-bs-toggle="tooltip" 
                                           title="Présent le {{ $linkDate }} - Arrivée: {{ \Carbon\Carbon::parse($dayAttendance->first()->arrival_time)->format('H:i') }}">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('attendance.index', ['date' => $date]) }}" 
                                           class="text-danger" 
                                           data-bs-toggle="tooltip" 
                                           title="Absent le {{ $linkDate }}">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    @endif
                                @else
                                    @if($day->isPast())
                                        <a href="{{ route('attendance.index', ['date' => $date]) }}" 
                                           class="text-muted" 
                                           data-bs-toggle="tooltip" 
                                           title="Non renseigné pour le {{ $linkDate }}">
                                            <i class="fas fa-minus"></i>
                                        </a>
                                    @endif
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <div class="d-flex gap-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-25 border" style="width:20px; height:20px"></div>
                        <span class="ms-1">Présent</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 border" style="width:20px; height:20px"></div>
                        <span class="ms-1">Absent/Non renseigné</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-info text-white text-center" style="width:20px; height:20px">
                            <small>J</small>
                        </div>
                        <span class="ms-1">Aujourd'hui</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    $(document).ready(function() {
        // Activer les tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection
