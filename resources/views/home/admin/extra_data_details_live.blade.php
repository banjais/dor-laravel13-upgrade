@php
// dd($the_home->data_2);
@endphp
@if ($the_home->data_1_title)
    <div class="control-group">
        <label class="control-label">{{ $the_home->data_1_title }} </label>
        <div class="controls">
            <input id="data_1" type="text" name="data_1" class="span8"
                value="{{ old('data_1', $the_home->data_1) }}" />&nbsp;
            <a href="javascript:void(0);" onclick="openCoverImageBrowser()">Browse Cover Image</a>&nbsp;&nbsp;
            <a id="aLinkRemoveCoverImage" href="javascript:void(0);" onclick="removeCoverImage()">Remove</a>
            <br />
            <img id='imgCoverPhoto' src="{{ $the_home->data_1 }}" onerror="this.src='/assets/admin/img/no-image.png'"
                width="200" height="80" />

        </div>
    </div>
@endif

<div class="control-group">
    <label class="control-label">{{ $the_home->data_5_title }} </label>
    <div class="controls">
        <input id="data_5_txt" type="text" name="data_5_txt" class="span8"
            value="{{ old('data_5_txt', $the_home->data_5) }}" />&nbsp;
        <a href="javascript:void(0);" onclick="openPdfBrowser()">Browse Pdf</a>&nbsp;&nbsp;
        <a id="aLinkRemovePdf" href="javascript:void(0);" onclick="removePdf()">Remove</a>
        @if ($the_home->data_5)
            <br />
            <span><img src="{{ '/assets/admin/img/file-search/pdf.png' }}" alt="" /></span>&nbsp;&nbsp;<span><a
                    href="{{ $the_home->data_5 }}" target="_blank">{{ $the_home->data_5 }}</a></span>
        @endif
    </div>
</div>

<div class="control-group">
    <label class="control-label">{{ $the_home->data_6_title }} </label>
    <div class="controls">
        <input id="data_6_txt" type="text" name="data_6_txt" class="span8"
            value="{{ old('data_6_txt', $the_home->data_6) }}" />&nbsp;
        <a href="javascript:void(0);" onclick="openVideoBrowser()">Browse Video</a>&nbsp;&nbsp;
        <a id="aLinkRemoveVideo" href="javascript:void(0);" onclick="removeVideo()">Remove</a>
        @if ($the_home->data_6)
            <br />
            <span><img src="{{ '/assets/admin/img/file-search/video.png' }}" alt="" /></span>&nbsp;&nbsp;<span><a
                    href="{{ $the_home->data_6 }}" target="_blank">{{ $the_home->data_6 }}</a></span>
        @endif
    </div>
</div>

<div class="control-group">
    <label class="control-label">{{ $the_home->data_7_title }} </label>
    <div class="controls">
        <input id="data_7" type="text" name="data_7" class="span8"
            value="{{ old('data_7', $the_home->data_7) }}" />
    </div>
</div>

@if ($the_home->data_2_title)
    <div class="control-group">
        <label class="control-label">{{ $the_home->data_2_title }} </label>
        <div class="controls">
            <input id="data_2" type="text" name="data_2" class="span2" value="{{ old('data_2', $the_home->data_2) }}" />
        </div>
    </div>
@endif

<script type="text/javascript">
    //for cover image
    function setCoverImage(data) {
        document.getElementById('imgCoverPhoto').src = data;
        document.getElementById('data_1').value = data;
    }

    function openCoverImageBrowser() {
        window.open('/assets/admin/ckfinder/ckfinder.html?func=setCoverImage&action=js', 'Browse Cover Image',
            "menubar=1,resizable=1,width=850,height=600");
    }

    //for Pdf
    function setPdf(data) {
        console.log('data', data);
        document.getElementById('data_5_txt').value = data;
    }

    function openPdfBrowser() {
        window.open('/assets/admin/ckfinder/ckfinder.html?func=setPdf&action=js', 'Browse Pdf',
            "menubar=1,resizable=1,width=850,height=600");
    }

    //for video
    function setVideo(data) {
        console.log('data', data);
        document.getElementById('data_6_txt').value = data;
    }

    function openVideoBrowser() {
        window.open('/assets/admin/ckfinder/ckfinder.html?func=setVideo&action=js', 'Browse Video',
            "menubar=1,resizable=1,width=850,height=600");
    }

    $(function() {
        //maintain cover image link
        $cur_val = $('#data_1').val();
        $('#aLinkRemoveCoverImage').on('click', function() {
            $('#imgCoverPhoto').attr('src', '');
            $('#data_1').val('');
        });

        //maintain pdf link
        $cur_val_pdf = $('#data_5').val();
        $('#aLinkRemovePdf').on('click', function() {
            $('#data_5_txt').val('');
        });

        //maintain video link
        $cur_val_video = $('#data_6').val();
        $('#aLinkRemoveVideo').on('click', function() {
            $('#data_6_txt').val('');
        });

        $("#data_2").spectrum({
            color: "{{ old('data_2', $the_home->data_2) }}",
            preferredFormat: "hex",
            showInput: true,
            showPalette: true,
            // showPaletteOnly: true,
            togglePaletteOnly: true,
            togglePaletteMoreText: 'more',
            togglePaletteLessText: 'less',
            palette: [
                ["#000", "#444", "#666", "#999", "#ccc", "#eee", "#f3f3f3", "#fff"],
                ["#f00", "#f90", "#ff0", "#0f0", "#0ff", "#00f", "#90f", "#f0f"],
                ["#f4cccc", "#fce5cd", "#fff2cc", "#d9ead3", "#d0e0e3", "#cfe2f3", "#d9d2e9",
                    "#ead1dc"
                ],
                ["#ea9999", "#f9cb9c", "#ffe599", "#b6d7a8", "#a2c4c9", "#9fc5e8", "#b4a7d6",
                    "#d5a6bd"
                ],
                ["#e06666", "#f6b26b", "#ffd966", "#93c47d", "#76a5af", "#6fa8dc", "#8e7cc3",
                    "#c27ba0"
                ],
                ["#c00", "#e69138", "#f1c232", "#6aa84f", "#45818e", "#3d85c6", "#674ea7",
                    "#a64d79"
                ],
                ["#900", "#b45f06", "#bf9000", "#38761d", "#134f5c", "#0b5394", "#351c75",
                    "#741b47"
                ],
                ["#600", "#783f04", "#7f6000", "#274e13", "#0c343d", "#073763", "#20124d",
                    "#4c1130"
                ]
            ]
        });
    });
</script>
