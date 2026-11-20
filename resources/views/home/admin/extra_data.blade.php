@php
    $isLiveFlashBox = preg_match('/^general-information(?:-[0-9]+)?$/', (string) $code);
@endphp
@if ($isLiveFlashBox)
    @include('home.admin.extra_data_details_live')
@elseif ($code === 'facebook-feed' || $code === 'instagram-feed')
@php
    // dd($the_home->data_1);
@endphp
    @include('home.admin.extra_data_details_facebook_instagram_feed')
@else
    @if ($the_home->data_1_title)
        <div class="control-group">
            <label class="control-label">{{ $the_home->data_1_title }} </label>
            <div class="controls">
                <input type="text" name="data_1" class="span8" value="{{ $the_home->data_1 }}" />
            </div>
        </div>
    @endif

    @if ($the_home->data_2_title)
        <div class="control-group">
            <label class="control-label">{{ $the_home->data_2_title }} </label>
            <div class="controls">
                <input type="text" name="data_2" class="span8" value="{{ $the_home->data_2 }}" />
            </div>
        </div>
    @endif
@endif

@if ($the_home->data_3_title)
    <div class="control-group">
        <label class="control-label">{{ $the_home->data_3_title }} </label>
        <div class="controls">
            <input type="text" name="data_3" class="span8" value="{{ $the_home->data_3 }}" />
        </div>
    </div>
@endif

@if ($the_home->data_4_title)
    <div class="control-group">
        <label class="control-label">{{ $the_home->data_4_title }} </label>
        <div class="controls">
            <input type="text" name="data_4" class="span8" value="{{ $the_home->data_4 }}" />
        </div>
    </div>
@endif

@if ($the_home->data_5_title && !$isLiveFlashBox)
    <div class="control-group">
        <label class="control-label">{{ $the_home->data_5_title }} </label>
        <div class="controls">
            <input type="text" name="data_5" class="span8" value="{{ $the_home->data_5 }}" />
        </div>
    </div>
@endif

@if ($the_home->data_6_title && !$isLiveFlashBox)
    <div class="control-group">
        <label class="control-label">{{ $the_home->data_6_title }} </label>
        <div class="controls">
            <input type="text" name="data_6" class="span8" value="{{ $the_home->data_6 }}" />
        </div>
    </div>
@endif
