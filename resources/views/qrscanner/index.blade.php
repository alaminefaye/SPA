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
                    <button class="btn btn-primary" id="start-scanner" type="button">
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

<!-- Ne dépend d'aucune bibliothèque externe, juste du JavaScript pur -->
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
// Attendre que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé, prêt à initialiser');
    
    // Références aux éléments DOM
    const qrReader = document.getElementById('qr-reader');
    const qrResultDisplay = document.getElementById('qr-result-display');
    const startButton = document.getElementById('start-scanner');
    
    // Variables pour le scanner
    let html5QrCode = null;
    let scanning = false;
    
    // Configuration du scanner
    const qrConfig = { fps: 10, qrbox: { width: 250, height: 250 } };
    
    // Initialiser la bibliothèque HTML5-QRCode
    try {
        html5QrCode = new Html5Qrcode('qr-reader');
        console.log('Scanner QR initialisé avec succès');
    } catch(error) {
        console.error('Erreur lors de l\'initialisation du scanner QR:', error);
        qrResultDisplay.innerHTML = `
            <div class="alert alert-danger">
                <h6 class="alert-heading fw-bold mb-1">Erreur d'initialisation</h6>
                <p class="mb-0">Impossible d'initialiser le scanner: ${error.message}</p>
            </div>
        `;
        return;
    }
    
    // Fonction pour gérer la réussite du scan
    function onScanSuccess(decodedText, decodedResult) {
        console.log('QR Code scanné:', decodedText);
        
        // Afficher le résultat
        qrResultDisplay.innerHTML = `
            <div class="alert alert-info">
                <h6 class="alert-heading fw-bold mb-1">QR Code scanné!</h6>
                <p class="mb-0"><strong>Contenu:</strong> ${decodedText}</p>
                <p class="mb-0"><small class="text-muted">Traitement en cours...</small></p>
            </div>
        `;
        
        // Arrêter le scanner
        if (html5QrCode && scanning) {
            html5QrCode.stop().then(() => {
                console.log('Scanner arrêté après scan réussi');
                scanning = false;
                startButton.innerHTML = '<i class="bx bx-qr-scan me-1"></i> Démarrer le scan';
                
                // Envoyer les données au serveur
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                console.log('Token CSRF:', csrfToken);
                
                const formData = new FormData();
                formData.append('qr_data', decodedText);
                formData.append('_token', csrfToken);
                
                fetch('{{ route("qrscanner.process") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Réponse du serveur:', data);
                    
                    if (data.success) {
                        qrResultDisplay.innerHTML = `
                            <div class="alert alert-success">
                                <h6 class="alert-heading fw-bold mb-1">Traitement réussi!</h6>
                                <p class="mb-0">${data.message}</p>
                            </div>
                        `;
                        
                        // Rediriger si nécessaire
                        if (data.redirect) {
                            console.log('Redirection vers:', data.redirect);
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 500);
                        }
                    } else {
                        qrResultDisplay.innerHTML = `
                            <div class="alert alert-danger">
                                <h6 class="alert-heading fw-bold mb-1">Erreur!</h6>
                                <p class="mb-0">${data.message || 'Erreur de traitement du QR code'}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la requête:', error);
                    qrResultDisplay.innerHTML = `
                        <div class="alert alert-danger">
                            <h6 class="alert-heading fw-bold mb-1">Erreur réseau!</h6>
                            <p class="mb-0">Impossible de communiquer avec le serveur.</p>
                        </div>
                    `;
                });
            }).catch(error => {
                console.error('Erreur lors de l\'arrêt du scanner:', error);
            });
        }
    }
    
    // Fonction pour gérer l'échec du scan
    function onScanFailure(error) {
        // Généralement ignoré car normal pendant la recherche de QR code
        // console.warn('Erreur de scan QR:', error);
    }
    
    // Gestionnaire d'événements pour le bouton de démarrage/arrêt
    startButton.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Bouton cliqué, état scanning =', scanning);
        
        if (scanning) {
            // Arrêter le scanner
            html5QrCode.stop().then(() => {
                console.log('Scanner arrêté');
                scanning = false;
                startButton.innerHTML = '<i class="bx bx-qr-scan me-1"></i> Démarrer le scan';
            }).catch(error => {
                console.error('Erreur lors de l\'arrêt du scanner:', error);
            });
        } else {
            // Démarrer le scanner
            console.log('Tentative de démarrage du scanner...');
            html5QrCode.start(
                { facingMode: "environment" }, // Utiliser la caméra arrière
                qrConfig,
                onScanSuccess,
                onScanFailure
            ).then(() => {
                console.log('Scanner démarré avec succès');
                scanning = true;
                startButton.innerHTML = '<i class="bx bx-stop me-1"></i> Arrêter le scan';
            }).catch(error => {
                console.error('Erreur lors du démarrage du scanner:', error);
                qrResultDisplay.innerHTML = `
                    <div class="alert alert-danger">
                        <h6 class="alert-heading fw-bold mb-1">Erreur de caméra</h6>
                        <p class="mb-0">Impossible d'accéder à la caméra: ${error.message}</p>
                        <p class="mb-0"><small class="text-muted">Vérifiez que vous avez autorisé l'accès à la caméra.</small></p>
                    </div>
                `;
            });
        }
    });
});
</script>
// Approche originale avec initialisation complète (désactivée pour l'instant)
/*
$(document).ready(function() {
    // Initialisation des variables
    const qrResultDisplay = document.getElementById("qr-result-display");
    const startButton = document.getElementById("start-scanner");
    let scanning = false;
    let html5QrCode = null;
    
    try {
        // Initialisation du scanner
        html5QrCode = new Html5Qrcode("qr-reader");
        console.log("Scanner initialisé avec succès");
    } catch(err) {
        console.error("Erreur d'initialisation du scanner:", err);
        qrResultDisplay.innerHTML = `
            <div class="alert alert-danger">
                <h6 class="alert-heading fw-bold mb-1">Erreur d'initialisation</h6>
                <p class="mb-0">${err.message}</p>
            </div>
        `;
        return;
    }
    
    // Configuration du gestionnaire d'événement pour le bouton
    $("#start-scanner").on("click", function(event) {
        event.preventDefault();
        console.log("Bouton cliqué, scanning=", scanning);
*/
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
                const csrfToken = '{{ csrf_token() }}';
                console.log('Tentative de traitement pour:', decodedText);
                console.log('Token CSRF:', csrfToken);
                console.log('URL route:', '{{ route("qrscanner.process") }}');
                
                // Utiliser XMLHttpRequest au lieu de fetch pour tester
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route("qrscanner.process") }}');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                
                xhr.onload = function() {
                    console.log('Status:', xhr.status);
                    console.log('Response Text:', xhr.responseText);
                    
                    try {
                        const data = JSON.parse(xhr.responseText);

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
                    } catch (e) {
                        console.error('Erreur de parsing JSON:', e);
                        qrResultDisplay.innerHTML = `
                            <div class="alert alert-danger">
                                <h6 class="alert-heading fw-bold mb-1">Erreur!</h6>
                                <p class="mb-0">Erreur de format de réponse: ${e.message}</p>
                                <p class="mb-0"><small class="text-muted">Vérifiez la console pour plus de détails.</small></p>
                            </div>
                        `;
                    }
                };
                
                xhr.onerror = function() {
                    console.error('Erreur réseau lors de la requête');
                    qrResultDisplay.innerHTML = `
                        <div class="alert alert-danger">
                            <h6 class="alert-heading fw-bold mb-1">Erreur réseau!</h6>
                            <p class="mb-0">Impossible de contacter le serveur.</p>
                        </div>
                    `;
                };
                
                // Envoyer les données
                xhr.send('qr_data=' + encodeURIComponent(decodedText) + '&_token=' + encodeURIComponent(csrfToken));
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
