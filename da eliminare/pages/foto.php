<?php include("header.php"); ?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Scatta una Foto</title>
    <link rel="stylesheet" href="../css/style.css">
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/exif-js/2.3.0/exif.min.js"></script>
</head>
<body>
<?php include("menubar.php"); ?>
    <header>
        <h1 style="text-align: center;">Scatta una Foto</h1>
    </header>
    <main class="conversion-container" style="max-width: 600px; margin: 0 auto; text-align: center;">
        <label for="siglaDOS">Sigla DOS:</label>
        <input type="text" id="siglaDOS" maxlength="10" pattern="[A-Z0-9-/]+" title="Solo lettere maiuscole, numeri, '-' e '/'" required>

        <label for="nome">Nome:</label>
        <input type="text" id="nome" required>

        <label for="cognome">Cognome:</label>
        <input type="text" id="cognome" required>

        <label for="commento">Commento (max 100 caratteri):</label>
        <textarea id="commento" maxlength="100" oninput="document.getElementById('charCount').textContent=this.value.length+'/100'"></textarea>
        <span id="charCount">0/100</span>

        <p>Coordinate GPS: <span id="gpsCoords">Attendere...</span></p>
        <p>Quota: <span id="altitude">Attendere...</span></p>

        <label for="cameraSelect">Seleziona fotocamera:</label>
        <select id="cameraSelect" class="form-control">
            <option value="environment">Posteriore</option>
            <option value="user">Frontale</option>
        </select>

        <div style="position: relative; display: inline-block; max-width: 100%;">
            <video id="camera" autoplay muted playsinline style="display: block; max-width: 100%; border: 2px solid #ccc;"></video>
            <button id="capture" class="btn" style="position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); z-index: 10;">Scatta Foto</button>
        </div>

        <canvas id="canvas" class="canvas-preview" style="display: block; margin: 20px auto; max-width: 100%; border: 2px solid #ccc;"></canvas>
        <button id="save" class="btn" style="display: none; margin: 10px auto;">Salva Foto</button>
        <button id="whatsapp" class="btn" style="display: none; margin: 10px auto; background-color: #25D366; color: white;">Invia via WhatsApp</button>
    </main>
<footer>
    <p>&copy; 2024 Strumenti DOS - Azienda Calabria Verde</p>
</footer>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const video = document.getElementById('camera');
            const canvas = document.getElementById('canvas');
            const context = canvas.getContext('2d');
            const captureButton = document.getElementById('capture');
            const saveButton = document.getElementById('save');
            const whatsappButton = document.getElementById('whatsapp');
            const gpsCoords = document.getElementById('gpsCoords');
            const altitudeElem = document.getElementById('altitude');
            const cameraSelect = document.getElementById('cameraSelect');
            const siglaDOS = document.getElementById('siglaDOS');
            const nome = document.getElementById('nome');
            const cognome = document.getElementById('cognome');

            let stream = null;
            let lastFileName = "";

            [siglaDOS, nome, cognome].forEach(input => {
                const saved = localStorage.getItem("foto_" + input.id);
                if (saved) input.value = saved;
                input.addEventListener("input", () => {
                    localStorage.setItem("foto_" + input.id, input.value);
                });
            });

            siglaDOS.addEventListener('input', () => {
                siglaDOS.value = siglaDOS.value.toUpperCase().replace(/[^A-Z0-9-/]/g, '');
            });

            function convertToDMS(lat, lon) {
                const toDMS = (deg) => {
                    const absolute = Math.abs(deg);
                    const degrees = Math.floor(absolute);
                    const minutesNotTruncated = (absolute - degrees) * 60;
                    const minutes = Math.floor(minutesNotTruncated);
                    const seconds = ((minutesNotTruncated - minutes) * 60).toFixed(3);
                    return `${degrees}° ${minutes}' ${seconds}"`;
                };
                const latDirection = lat >= 0 ? 'N' : 'S';
                const lonDirection = lon >= 0 ? 'E' : 'W';
                return `${toDMS(lat)} ${latDirection}, ${toDMS(lon)} ${lonDirection}`;
            }

            function getFileName() {
                const now = new Date();
                const yyyy = now.getFullYear();
                const mm = String(now.getMonth() + 1).padStart(2, '0');
                const dd = String(now.getDate()).padStart(2, '0');
                const hh = String(now.getHours()).padStart(2, '0');
                const min = String(now.getMinutes()).padStart(2, '0');
                const ss = String(now.getSeconds()).padStart(2, '0');
                const sigla = siglaDOS.value.trim();
                return `${sigla ? sigla + '_' : ''}${yyyy}${mm}${dd}_${hh}${min}${ss}.png`;
            }

            async function startCamera(facingMode = 'environment') {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: facingMode },
                        audio: false
                    });
                    video.srcObject = stream;
                } catch (err) {
                    console.error('Errore accesso fotocamera:', err);
                }
            }

            navigator.geolocation.watchPosition(
                position => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    const altitude = position.coords.altitude !== null ? `${position.coords.altitude.toFixed(2)} m` : 'Non disponibile';
                    gpsCoords.textContent = convertToDMS(lat, lon);
                    altitudeElem.textContent = altitude;
                },
                err => console.error('Errore accesso GPS:', err),
                { enableHighAccuracy: true, maximumAge: 0 }
            );

            captureButton.addEventListener('click', () => {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                const logo = new Image();
                logo.src = '../images/LOGO-CV.png';
                logo.onload = () => {
                    const logoWidth = canvas.width / 3;
                    const logoHeight = (logo.height / logo.width) * logoWidth;
                    context.drawImage(logo, 10, 10, logoWidth, logoHeight);

                    const dataOra = new Date().toLocaleString();
                    const sigla = siglaDOS.value;
                    const nomeDOS = nome.value + ' ' + cognome.value;
                    const coordinate = gpsCoords.textContent;
                    const altitude = altitudeElem.textContent;
                    const descrizione = document.getElementById('commento').value;

                    const textBoxWidth = canvas.width - 20;
                    const textX = 10;
                    const textYStart = canvas.height - 140;
                    const lineHeight = 20;

                    context.fillStyle = "rgba(0, 0, 0, 0.5)";
                    context.fillRect(textX, textYStart - 30, textBoxWidth, 120);

                    context.font = `${canvas.width * 0.03}px Arial`;
                    context.fillStyle = "white";
                    context.textAlign = "left";

                    context.fillText(dataOra, textX + 10, textYStart);
                    context.fillText(`${sigla} - ${nomeDOS}`, textX + 10, textYStart + lineHeight);
                    context.fillText(coordinate, textX + 10, textYStart + lineHeight * 2);
                    context.fillText(`Quota: ${altitude}`, textX + 10, textYStart + lineHeight * 3);
                    context.fillText(descrizione, textX + 10, textYStart + lineHeight * 4);

                    canvas.style.display = 'block';
                    saveButton.style.display = 'block';
                };
            });

            saveButton.addEventListener('click', async () => {
                const fileName = getFileName();
                lastFileName = fileName;
                const link = document.createElement('a');
                link.href = canvas.toDataURL('image/png');
                link.download = fileName;
                link.click();
                whatsappButton.style.display = 'inline-block';
            });

            whatsappButton.addEventListener('click', async () => {
                const fileName = lastFileName || 'foto.png';
                const base64Image = canvas.toDataURL('image/png');
                const blob = dataURItoBlob(base64Image);
                const file = new File([blob], fileName, { type: 'image/png' });

                if (navigator.canShare && navigator.canShare({ files: [file] })) {
                    try {
                        await navigator.share({
                            files: [file],
                            title: "Invio Foto Fire Master",
                            text: "Foto scattata da Fire Master"
                        });
                        whatsappButton.style.display = 'none';
                    } catch (err) {
                        console.error('Condivisione annullata o fallita:', err);
                    }
                } else {
                    alert("Condivisione file non supportata su questo dispositivo.");
                }
            });

            function dataURItoBlob(dataURI) {
                const byteString = atob(dataURI.split(',')[1]);
                const mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
                const ab = new ArrayBuffer(byteString.length);
                const ia = new Uint8Array(ab);
                for (let i = 0; i < byteString.length; i++) {
                    ia[i] = byteString.charCodeAt(i);
                }
                return new Blob([ab], { type: mimeString });
            }

            cameraSelect.addEventListener('change', (e) => {
                startCamera(e.target.value);
            });

            video.addEventListener('click', () => {
                if (video.paused) video.play();
            });

            startCamera();
        });
    </script>
</body>
</html>