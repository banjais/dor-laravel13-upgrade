<div
    id="photo-studio"
    class="control-group"
    data-type="{{ $photoStudioType }}"
    data-existing-photo-id="{{ $existingPhotoId ?? '' }}"
    data-preview-url="{{ route('admin.photo-studio.create', ['type' => $photoStudioType]) }}"
    data-reject-url="{{ route('admin.photo-studio.reject', ['type' => $photoStudioType]) }}"
    data-download-url="{{ route('admin.photo-studio.download', ['type' => $photoStudioType, 'token' => '__TOKEN__']) }}"
    style="margin:20px 0;padding:15px;border:1px solid #d8dee8;border-radius:6px;background:#fafafa;"
>
    <label class="control-label">AI Photo Studio</label>

    <div class="controls">
        <input type="hidden" name="photo_studio_token" id="photo-studio-token" value="">

        <p style="margin-bottom:12px;color:#555;">
            Upload a JPEG/PNG photo or take a photo using your camera.
            Maximum size: 1 MB.
        </p>

        @if($photoStudioType === 'home_officer')
            <p style="margin:12px 0;color:#555;">
                Head &amp; Neck portrait framing is applied automatically.
                Please review the preview before accepting.
            </p>
        @endif

        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
            <label for="photo-studio-file" class="btn btn-primary" style="cursor:pointer;">
                <i class="icon-upload"></i> {{ $photoStudioType === 'office_chief' ? 'Add Photo' : 'Upload Photo' }}
            </label>

            @if($photoStudioType === 'office_chief' && !empty($existingPhotoId))
                <button
                    type="button"
                    id="photo-studio-improve"
                    class="btn btn-info"
                >
                    <i class="icon-magic"></i> Improve with AI
                </button>
            @endif

            <input
                type="file"
                id="photo-studio-file"
                accept="image/jpeg,image/png"
                style="position:absolute;left:-9999px;width:1px;height:1px;"
            >

            <button type="button" id="photo-studio-camera" class="btn btn-info">
                <i class="icon-camera"></i> Take Photo
            </button>
        </div>

        <div id="photo-studio-camera-panel" hidden style="margin:12px 0;">
            <video
                id="photo-studio-video"
                autoplay
                playsinline
                muted
                style="display:block;width:100%;max-width:420px;background:#222;border-radius:5px;"
            ></video>

            <div style="margin-top:8px;">
                <button type="button" id="photo-studio-capture" class="btn btn-primary">
                    Take Photo
                </button>
                <button type="button" id="photo-studio-camera-close" class="btn btn-default">
                    Close Camera
                </button>
            </div>
        </div>

        <div id="photo-studio-preview-panel" hidden style="margin-top:15px;">
            <p><strong>AI Processed Preview</strong></p>

            <img
                id="photo-studio-preview"
                alt="AI processed photo preview"
                style="display:block;width:100%;max-width:400px;height:auto;background:#fafafa;border:1px solid #ddd;border-radius:4px;"
            >

            <p style="margin-top:8px;color:#555;">
                Background: #FAFAFA · Output: 800 × 800
            </p>

            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <button type="button" id="photo-studio-accept" class="btn btn-success">
                    <i class="icon-ok"></i> Accept Photo
                </button>

                <button type="button" id="photo-studio-download" class="btn btn-primary" disabled>
                    <i class="icon-download"></i> Download
                </button>

                <button type="button" id="photo-studio-reject" class="btn btn-danger">
                    <i class="icon-remove"></i> Reject / Change
                </button>
            </div>
        </div>

        <p
            id="photo-studio-status"
            role="status"
            aria-live="polite"
            style="margin-top:10px;color:#555;"
        >
            No photo processed yet.
        </p>

        <p style="font-size:12px;color:#777;margin-top:8px;">
            The original photo is kept privately during processing.
            Only an accepted preview is used when this form is submitted.
        </p>
    </div>
</div>
