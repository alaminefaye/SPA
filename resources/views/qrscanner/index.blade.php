@extends('layouts.app')

@section('title', 'Scanner QR Code')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Outils /</span> Scanner QR Code
    </h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Scanner un code QR</h5>
                    <button class="btn btn-primary" id="start-scanner">
                        <i class="bx bx-qr-scan me-1"></i> Démarrer le scan
                    </button>
                </div>
                <div class="card-body">
                    <div id="qr-reader" style="width:100%"></div>
                    <div id="qr-reader-results" class="mt-3"></div>
                    
                    <div class="alert alert-info mt-3">
                        <h6 class="alert-heading fw-bold mb-1"><i class="bx bx-info-circle me-1"></i> Information:</h6>
                        <p class="mb-0">Cliquez sur "Démarrer le scan" et autorisez l'accès à votre caméra pour scanner un code QR.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Dernier résultat</h5>
                </div>
                <div class="card-body">
                    <div id="qr-result-display">
                        <p>Aucun code QR n'a encore été scanné.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('page-js')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const html5QrCode = new Html5Qrcode("qr-reader");
        const qrResultDisplay = document.getElementById("qr-result-display");
        let scanning = false;
        
        document.getElementById("start-scanner").addEventListener("click", function() {
            if (scanning) {
                html5QrCode.stop().then(() => {
                    scanning = false;
                    document.getElementById("start-scanner").innerHTML = '<i class="bx bx-qr-scan me-1"></i> Démarrer le scan';
                }).catch(err => {
                    console.log("Erreur lors de l'arrêt du scanner: ", err);
                });
            } else {
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                
                html5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    onScanSuccess,
                    onScanFailure
                ).then(() => {
                    scanning = true;
                    document.getElementById("start-scanner").innerHTML = '<i class="bx bx-stop me-1"></i> Arrêter le scan';
                }).catch(err => {
                    console.log("Erreur lors du démarrage du scanner: ", err);
                    alert("Impossible d'accéder à la caméra. Veuillez vérifier les permissions.");
                });
            }
        });
        
        function onScanSuccess(decodedText, decodedResult) {
            // Play a beep sound
            let beep = new Audio("data:audio/wav;base64,//uQRAAAAWMSLwUIYAAsYkXgoQwAEaYLWfkWgAI0wWs/ItAAAGDgYtAgAyN+QWaAAihwMWm4G8QQRDiMcCBcH3Cc+CDv/7xA4Tvh9Rz/y8QADBwMWgQAZG/ILNAARQ4GLTcDeIIIhxGOBAuD7hOfBB3/94gcJ3w+o5/5eIAIAAAVwWgQAVQ2ORaIQwEMAJiDg95G4nQL7mQVWI6GwRcfsZAcsKkJvxgxEjzFUgfHoSQ9Qq7KNwqHwuB13MA4a1q/DmBrHgPcmjiGoh//EwC5nGPEmS4RcfkVKOhJf+WOgoxJclFz3kgn//dBA+ya1GhurNn8zb//9NNutNuhz31f////9vt///z+IdAEAAAK4LQIAKobHItEIYCGAExBwe8jcToF9zIKrEdDYIuP2MgOWFSE34wYiR5iqQPj0JIeoVdlG4VD4XA67mAcNa1fhzA1jwHuTRxDUQ//iYBczjHiTJcIuPyKlHQkv/LHQUYkuSi57yQT//uggfZNajQ3Vmz+Zt//+mm3Wm3Q576v////+32///5/EOgAAADVghQAAAAA//uQZAUAB1WI0PZugAAAAAoQwAAAEk3nRd2qAAAAACiDgAAAAAAABCqEEQRLCgwpBGMlJkIz8jKhGvj4k6jzRnqasNKIeoh5gI7BJaC1A1AoNBjJgbyApVS4IDlZgDU5WUAxEKDNmmALHzZp0Fkz1FMTmGFl1FMEyodIavcCAUHDWrKAIA4aa2oCgILEBupZgHvAhEBcZ6joQBxS76AgccrFlczBvKLC0QI2cBoCFvfTDAo7eoOQInqDPBtvrDEZBNYN5xwNwxQRfw8ZQ5wQVLvO8OYU+mHvFLlDh05Mdg7BT6YrRPpCBznMB2r//xKJjyyOh+cImr2/4doscwD6neZjuZR4AgAABYAAAABy1xcdQtxYBYYZdifkUDgzzXaXn98Z0oi9ILU5mBjFANmRwlVJ3/6jYDAmxaiDG3/6xjQQCCKkRb/6kg/wW+kSJ5//rLobkLSiKmqP/0ikJuDaSaSf/6JiLYLEYnW/+kXg1WRVJL/9EmQ1YZIsv/6Qzwy5qk7/+tEU0nkls3/zIUMPKNX/6yZLf+kFgAfgGyLFAUwY//uQZAUABcd5UiNPVXAAAApAAAAAE0VZQKw9ISAAACgAAAAAVQIygIElVrFkBS+Jhi+EAuu+lKAkYUEIsmEAEoMeDmCETMvfSHTGkF5RWH7kz/ESHWPAq/kcCRhqBtMdokPdM7vil7RG98A2sc7zO6ZvTdM7pmOUAZTnJW+NXxqmd41dqJ6mLTXxrPpnV8oDvKaVR3B5KvCWpTOafO3UgRKsKT/1+vnks1+vBF26dEPJhdlme9YSps6Xukv/k4P25Xp0Mk8ck+5T1VVBuS63r84sj8LnAF50LO48/tIrQ8YHu/BcNtmScHLn1zpPiw+X6p2qXGiySrr/Rqlho/k6FpRU2JJP24/JrpATRo31WwvON+QQVwVAu/kCFykvQSCsWcO8Cu/oRVw/xO/RQ1tgu7/BXP/bHP/Z5fLh+nqZTZ262VnTZ1rZzo3QO7ztdUxsCwDM7k1nYx0eZD5cNMdLdRh2S6OFE76SKD3lYyExZZrMOgHazR3S+McgKYRFiVO9OBzsvAgMBJimZMf8q1ChSIRkJahBwEX4PgFIWQIp6+5c5cjSiO0YW3kB5NQiKTDCkitPr7GiMeGj/eQVmHQoag1oEEUk5hhkg5CoYRiyBUKSJekHSIS2WYCH2LEICm44lWu0DKKFUJSG1Bg13/UJOEgTh24ZB6xDXIeeM9/nZ4B+4U5UAPxpQIDAQABHIujVLxaCXY8WUCh0ypo71DU0Mzax86Xd4ameYJ+2l5n/KWuI0c2Em5FcHuIZUXDzzWMT+8y1YzJrr17lk8rh9JRUFO+ZiPfQCH65/wB+rIR+2ywA==");
            beep.play();
            
            // Handle the scanned QR code
            console.log(`QR Code decoded: ${decodedText}`);
            qrResultDisplay.innerHTML = `
                <div class="alert alert-info">
                    <h6 class="alert-heading fw-bold mb-1">QR Code scanné!</h6>
                    <p class="mb-0"><strong>Contenu:</strong> ${decodedText}</p>
                    <p class="mb-0"><small class="text-muted">Traitement en cours...</small></p>
                </div>
            `;
            
            // Automatically stop scanning after successful scan
            html5QrCode.stop().then(() => {
                scanning = false;
                document.getElementById("start-scanner").innerHTML = '<i class="bx bx-qr-scan me-1"></i> Démarrer le scan';
                
                // Envoyer les données au serveur pour traitement
                fetch('{{ route("qrscanner.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        qr_data: decodedText
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Réponse serveur:', data);
                    
                    if (data.success) {
                        qrResultDisplay.innerHTML = `
                            <div class="alert alert-success">
                                <h6 class="alert-heading fw-bold mb-1">Traitement réussi!</h6>
                                <p class="mb-0">${data.message}</p>
                            </div>
                        `;
                        
                        // Si une redirection est fournie, rediriger l'utilisateur
                        if (data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 500); // Redirection après 500ms
                        }
                    } else {
                        qrResultDisplay.innerHTML = `
                            <div class="alert alert-danger">
                                <h6 class="alert-heading fw-bold mb-1">Erreur!</h6>
                                <p class="mb-0">${data.message}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du traitement du QR code:', error);
                    qrResultDisplay.innerHTML = `
                        <div class="alert alert-danger">
                            <h6 class="alert-heading fw-bold mb-1">Erreur!</h6>
                            <p class="mb-0">Une erreur est survenue lors du traitement du QR code.</p>
                        </div>
                    `;
                });
            }).catch(err => {
                console.log("Erreur lors de l'arrêt du scanner: ", err);
            });
        }
        
        function onScanFailure(error) {
            // Handle scan failure, usually better to just ignore this
            // console.warn(`QR Code scan error = ${error}`);
        }
    });
</script>
@endsection
@endsection
