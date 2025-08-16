@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Modifier l'Achat #{{ $purchase->id }}</span>
                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Retour aux détails
                    </a>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('purchases.update', $purchase) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="client_id">Client</label>
                            <select class="form-control" id="client_id" name="client_id" required>
                                <option value="">-- Sélectionner un client --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ $purchase->client_id == $client->id ? 'selected' : '' }}>
                                        {{ $client->nom_complet }} ({{ $client->numero_telephone }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="payment_method">Mode de paiement</label>
                            <select class="form-control" id="payment_method" name="payment_method" required>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method }}" {{ $purchase->payment_method == $method ? 'selected' : '' }}>
                                        @if($method == 'cash') Espèces
                                        @elseif($method == 'wave') Wave
                                        @elseif($method == 'orange_money') Orange Money
                                        @else {{ $method }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes / Commentaires</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $purchase->notes) }}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Status</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="status_completed" value="completed" {{ $purchase->status == 'completed' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_completed">
                                    Complété
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="status_cancelled" value="cancelled" {{ $purchase->status == 'cancelled' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_cancelled">
                                    Annulé
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Enregistrer les modifications
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
