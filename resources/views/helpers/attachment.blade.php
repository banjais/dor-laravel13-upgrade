@php
$ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
$full_path = '/uploads/' . $folder . '/' . $src;
@endphp
@if (!$src)
    <img id="img_attachment" src="{{ '/assets/admin/img/no-image.png' }}" alt="" width="200" height="180"/>
@elseif ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif' || $ext == 'bmp')
    <span class="attachment-image-preview">
        <img id="img_attachment" src="{{ $full_path }}" width="200" height="180" alt=""
            @if ($showImageError ?? false)
                onerror="this.style.display='none'; this.parentNode.querySelector('.attachment-image-error').style.display='block';"
            @endif
        />
        @if ($showImageError ?? false)
            <span class="attachment-image-error" style="display:none; color:#b42318; font-weight:600;">
                Image cannot be displayed. Please change this image.
            </span>
        @endif
    </span>&nbsp;&nbsp;<span><a
            href="{{ $full_path }}" id="a_link_attachment" target="_blank">{{ $src }}</a></span>
@elseif($ext == 'pdf')
    <span><img id="img_attachment" src="{{ '/assets/admin/img/file-search/pdf.png' }}"
            alt="" /></span>&nbsp;&nbsp;<span><a href="{{ $full_path }}" target="_blank"
            id="a_link_attachment">{{ $src }}</a></span>
@elseif($ext == 'zip')
    <span><img id="img_attachment" src="{{ '/assets/admin/img/file-search/zip.png' }}"
            alt="" /></span>&nbsp;&nbsp;<span><a href="{{ $full_path }}" target="_blank"
            id="a_link_attachment">{{ $src }}</a></span>
@endif
@if ($src)
    <span>
        <input type="hidden" name="attachment_deleted" id="attachment_deleted" />
        <button type="button" id="btn_remove_attachment" onclick="removeAttachment(this)"
            class="btn btn-danger">remove</button>
    </span>
    <script type="text/javascript">
        function removeAttachment(elm) {
            document.getElementById('attachment_deleted').value = 1;
            document.getElementById('a_link_attachment').style.display = 'none';
            document.getElementById('img_attachment').src = '/assets/admin/img/no-image.png';
            elm.style.display = 'none';
        }
    </script>
@endif
