@extends('layout.admin.masters')
@section('content')
    @php
    $lang = Request::get('lang');
    // dd($lang);

    if ($lang == 'en') {
        $lang = 'English';
    } else {
        $lang = 'Nepali';
    }

    $title = $lang == 'Nepali' ? 'शिर्षक' : 'Title';
    $details = $lang == 'Nepali' ? 'बिस्तृत विवरण ' : 'Details';
    $attachment = $lang == 'Nepali' ? 'संलग्न डाउनलोड ' : 'Attachment';
    $published_date = $lang == 'Nepali' ? 'प्रकाशित मिति  ' : 'Published Date';
    // dd($circularData);
    @endphp

    <div id="ajax-notifier-background">
        <img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
    </div>
    <div id="main-content">

        <div class="container-fluid">

            <div class="row-fluid">
                <div class="span12">
                    <h3 class="page-title">
                        Circular
                        <small>details</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.circulars') }}">List of Circulars</a> <span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>Circular Details</li>
                    </ul>

                </div>
            </div>

            <div id="page">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="widget" style="padding:10px;">
                            <div class="widget-title">
                                <h4><i class="icon-reorder"></i>Circular Details</h4>
                            </div>
                            <div style="text-align: right;
                                    padding-right: 20px;
                                    margin-top: 10px;
                                    font-weight: bold;">
                                @if ($lang == 'Nepali')
                                    <a href="{{ route('admin.circular.view', $circularData->id) . '?lang=en' }}">View in
                                        English</a>
                                @else
                                    <a href="{{ route('admin.circular.view', $circularData->id) . '?lang=np' }}">View in
                                        Nepali</a>
                                @endif
                            </div>

                            <div style="padding:10px;margin-top:10px;">
                                @foreach ($circulars as $circular)
                                    @if ($circular->language_name == $lang)
                                        <div class="control-group">
                                            <p class="control-label bold">{{ $title }}
                                            </p>
                                            <div class="controls">
                                                <h4>{{ $circular->circular_heading }}</h4>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                <hr />
                                @foreach ($circulars as $circular)
                                    @if ($circular->language_name == $lang)
                                        <div class="control-group">
                                            <p class="control-label bold">{{ $details }} </p>
                                            <div class="controls">
                                                {!! $circular->circular_description !!}
                                            </div>
                                        </div>
                                    @endif
                                @endforeach

                                <hr />

                                <div class="control-group">
                                    <p class="control-label bold">{{ $published_date }} </p>
                                    <div class="controls">
                                        @php
                                            // dd($circularData);
                                        @endphp
                                        <span>{{ $circularData->published_date }}</span>
                                    </div>
                                </div>

                                {{-- @php
                                    dd($downloadAccess);
                                @endphp --}}
                                @if ($downloadAccess)
                                    <div class="control-group">
                                        <p class="control-label bold">{{ $attachment }}</label>
                                        <div class="controls">
                                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                                <div class="fileupload-new thumbnail">
                                                    @php
                                                        $attachmentName = $circularData->circular_attachment;
                                                        $attachmentExtension = strtolower(pathinfo($attachmentName ?? '', PATHINFO_EXTENSION));
                                                        $attachmentIcon = match ($attachmentExtension) {
                                                            'pdf' => 'pdf.png',
                                                            'doc', 'docx' => 'doc.png',
                                                            'ppt', 'pptx' => 'ppt.png',
                                                            'xls', 'xlsx' => 'xls.png',
                                                            'zip', 'rar' => 'zip.png',
                                                            default => null,
                                                        };
                                                    @endphp

                                                    @if (!empty($attachmentName) && basename($attachmentName) === $attachmentName)
                                                        <div>
                                                            @if (in_array($attachmentExtension, ['jpg', 'jpeg', 'png', 'gif', 'bmp'], true))
                                                                <img src="{{ asset('uploads/circular/' . $attachmentName) }}" alt="Circular attachment" width="200" height="180">
                                                            @elseif ($attachmentIcon)
                                                                <img src="{{ asset('assets/admin/img/file-search/' . $attachmentIcon) }}" alt="{{ strtoupper($attachmentExtension) }} file">
                                                            @endif

                                                            <a href="{{ asset('uploads/circular/' . $attachmentName) }}" target="_blank" rel="noopener noreferrer">
                                                                {{ $attachmentName }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- <div class="control-group">
                                    <p class="control-label bold">Status</label>
                                    <div class="controls">
                                        @if ($circularData->is_active == '0')
                                            Not Published
                                        @else
                                            Published
                                        @endif
                                    </div>
                                </div> --}}
                                <hr />
                                <div class="control-group">
                                    <a href="{{ route('admin.circulars') }}">
                                        << Back to List of Circulars</a>
                                </div>

                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    </div>

@section('footer_js')
    <script type="text/javascript">
        $('li').removeClass('active');
        $('#misc').addClass('active');
        $('#circulars').addClass('active');
    </script>
@endsection
@stop
