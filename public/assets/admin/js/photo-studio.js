(function () {
    'use strict';

    function initialisePhotoStudio() {
        var studio = document.getElementById('photo-studio');
        if (!studio) return;

        var form = studio.closest('form');
        if (!form) return;

        var type = studio.dataset.type;
        var previewUrl = studio.dataset.previewUrl;
        var rejectUrl = studio.dataset.rejectUrl;
        var downloadUrl = studio.dataset.downloadUrl;

        var fileInput = document.getElementById('photo-studio-file');
        var tokenInput = document.getElementById('photo-studio-token');
        var status = document.getElementById('photo-studio-status');

        var cameraButton = document.getElementById('photo-studio-camera');
        var cameraPanel = document.getElementById('photo-studio-camera-panel');
        var video = document.getElementById('photo-studio-video');
        var captureButton = document.getElementById('photo-studio-capture');
        var closeCameraButton = document.getElementById('photo-studio-camera-close');

        var previewPanel = document.getElementById('photo-studio-preview-panel');
        var previewImage = document.getElementById('photo-studio-preview');
        var acceptButton = document.getElementById('photo-studio-accept');
        var downloadButton = document.getElementById('photo-studio-download');
        var rejectButton = document.getElementById('photo-studio-reject');
        var improveButton = document.getElementById('photo-studio-improve');
        var existingPhotoId = studio.dataset.existingPhotoId || '';

        var pendingToken = '';
        var cameraStream = null;
        var processing = false;

        function setStatus(message, isError) {
            status.textContent = message;
            status.style.color = isError ? '#a12622' : '#555';
        }

        function stopCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(function (track) {
                    track.stop();
                });
                cameraStream = null;
            }

            video.srcObject = null;
            cameraPanel.hidden = true;
        }

        function csrfToken() {
            var field = form.querySelector('input[name="_token"]');
            return field ? field.value : '';
        }

        async function postForm(url, data) {
            var response = await fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept': 'application/json'
                },
                body: data
            });

            var result;

            try {
                result = await response.json();
            } catch (error) {
                throw new Error('The server returned an unexpected response.');
            }

            if (!response.ok || result.status !== 'success') {
                throw new Error(result.message || 'The request could not be completed.');
            }

            return result;
        }

        function updatePreviewActions() {
            acceptButton.disabled = processing || !pendingToken;
            downloadButton.disabled = processing || !(pendingToken || tokenInput.value);
            rejectButton.disabled = processing || !(pendingToken || tokenInput.value);

            if (improveButton) {
                improveButton.disabled = processing;
            }
        }

        function clearVisiblePreview() {
            previewPanel.hidden = true;
            previewImage.removeAttribute('src');
            acceptButton.textContent = 'Accept Photo';
            updatePreviewActions();
        }

        updatePreviewActions();

        async function processPhoto(file) {
            if (!file) return;

            if (!['image/jpeg', 'image/png'].includes(file.type)) {
                setStatus('Please select a JPEG or PNG image.', true);
                fileInput.value = '';
                return;
            }

            if (file.size > 1024 * 1024) {
                setStatus('Photo must not exceed 1 MB.', true);
                fileInput.value = '';
                return;
            }

            if (processing) return;

            stopCamera();

            // Keep the last accepted/pending preview until a replacement
            // has been processed successfully on the server.
            processing = true;
            updatePreviewActions();
            fileInput.disabled = true;
            cameraButton.disabled = true;
            setStatus('Processing photo. Please wait...', false);

            try {
                var data = new FormData();
                data.append('attachment', file, file.name || 'camera-photo.jpg');
                if (type === 'home_officer') {
                    data.append('framing', 'head_neck');
                }

                var result = await postForm(previewUrl, data);

                tokenInput.value = '';
                pendingToken = result.token;
                acceptButton.disabled = false;
                acceptButton.textContent = 'Accept Photo';
                previewImage.src = result.preview_url + '?v=' + Date.now();
                previewPanel.hidden = false;
                updatePreviewActions();

                setStatus(
                    'AI processing completed. Please review the preview and Accept or Reject it.',
                    false
                );
            } catch (error) {
                setStatus(error.message, true);
            } finally {
                processing = false;
                updatePreviewActions();
                fileInput.disabled = false;
                cameraButton.disabled = false;

                // The raw original must not be submitted with the Admin form.
                fileInput.value = '';
            }
        }

        async function processExistingPhoto() {
            if (!existingPhotoId || processing || !improveButton) return;

            stopCamera();

            processing = true;
            updatePreviewActions();
            fileInput.disabled = true;
            cameraButton.disabled = true;
            setStatus('Processing current photo. Please wait...', false);

            try {
                var data = new FormData();
                data.append('existing_photo_id', existingPhotoId);

                var result = await postForm(previewUrl, data);

                tokenInput.value = '';
                pendingToken = result.token;
                acceptButton.textContent = 'Accept Photo';
                previewImage.src = result.preview_url + '?v=' + Date.now();
                previewPanel.hidden = false;
                updatePreviewActions();

                setStatus(
                    'AI processing completed. Please review the preview and Accept or Reject it.',
                    false
                );
            } catch (error) {
                setStatus(error.message, true);
            } finally {
                processing = false;
                updatePreviewActions();
                fileInput.disabled = false;
                cameraButton.disabled = false;
            }
        }

        fileInput.addEventListener('change', function () {
            if (fileInput.files && fileInput.files.length) {
                processPhoto(fileInput.files[0]);
            }
        });

        if (improveButton) {
            improveButton.addEventListener('click', processExistingPhoto);
        }

        cameraButton.addEventListener('click', async function () {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                setStatus(
                    'Camera is unavailable in this browser or page connection. Please use Upload Photo.',
                    true
                );
                return;
            }

            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user' },
                    audio: false
                });

                video.srcObject = cameraStream;
                cameraPanel.hidden = false;
                await video.play();
                setStatus('Camera is ready. Take your photo when ready.', false);
            } catch (error) {
                stopCamera();
                setStatus(
                    'Camera permission was denied or the camera is unavailable. You can still Upload Photo.',
                    true
                );
            }
        });

        closeCameraButton.addEventListener('click', stopCamera);

        captureButton.addEventListener('click', function () {
            if (!video.videoWidth || !video.videoHeight) {
                setStatus('Camera is not ready yet. Please wait.', true);
                return;
            }

            var canvas = document.createElement('canvas');
            var scale = Math.min(1, 2000 / Math.max(video.videoWidth, video.videoHeight));

            canvas.width = Math.round(video.videoWidth * scale);
            canvas.height = Math.round(video.videoHeight * scale);

            var context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            function makeBlob(quality) {
                canvas.toBlob(function (blob) {
                    if (!blob) {
                        setStatus('Could not capture the camera photo. Please try again.', true);
                        return;
                    }

                    if (blob.size > 1024 * 1024 && quality > 0.45) {
                        makeBlob(quality - 0.12);
                        return;
                    }

                    if (blob.size > 1024 * 1024) {
                        setStatus('Captured photo exceeds 1 MB. Please try again.', true);
                        return;
                    }

                    var cameraFile = new File(
                        [blob],
                        'camera-photo.jpg',
                        { type: 'image/jpeg' }
                    );

                    stopCamera();
                    processPhoto(cameraFile);
                }, 'image/jpeg', quality);
            }

            makeBlob(0.88);
        });

        downloadButton.addEventListener('click', function () {
            var token = pendingToken || tokenInput.value;

            if (!token || processing) return;

            var url = downloadUrl.replace('__TOKEN__', encodeURIComponent(token));
            var link = document.createElement('a');

            link.href = url;
            link.download = 'dor-ai-photo.jpg';
            document.body.appendChild(link);
            link.click();
            link.remove();
        });

        acceptButton.addEventListener('click', function () {
            if (!pendingToken || processing) return;

            tokenInput.value = pendingToken;
            pendingToken = '';
            acceptButton.textContent = 'Photo Accepted';
            updatePreviewActions();
            setStatus('Photo accepted. Review it below, then submit the form to save it.', false);
        });

        rejectButton.addEventListener('click', async function () {
            if (processing) return;

            var token = pendingToken || tokenInput.value;

            if (!token) {
                tokenInput.value = '';
                pendingToken = '';
                clearVisiblePreview();
                setStatus('Photo selection cleared. You may upload or take another photo.', false);
                return;
            }

            processing = true;
            updatePreviewActions();
            setStatus('Rejecting preview...', false);

            try {
                var data = new FormData();
                data.append('token', token);

                await postForm(rejectUrl, data);

                tokenInput.value = '';
                pendingToken = '';
                clearVisiblePreview();
                setStatus('Preview rejected. You may upload or take another photo.', false);
            } catch (error) {
                setStatus(error.message, true);
            } finally {
                processing = false;
                updatePreviewActions();
            }
        });

        form.addEventListener('submit', function (event) {
            if (processing || pendingToken) {
                event.preventDefault();
                setStatus(
                    'Please wait for processing to finish, then Accept or Reject the preview.',
                    true
                );
                return;
            }

            if (fileInput.files && fileInput.files.length) {
                event.preventDefault();
                setStatus('Please process and accept the photo before submitting.', true);
            }
        });

        window.addEventListener('pagehide', stopCamera);

        if (type !== 'staff' && type !== 'office_chief' && type !== 'home_officer') {
            studio.hidden = true;
            return;
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialisePhotoStudio);
    } else {
        initialisePhotoStudio();
    }
})();
