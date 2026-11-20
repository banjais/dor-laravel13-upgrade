@if ($the_home->home_url === 'instagram-feed')
    <div class="control-group">
        <label class="control-label">Instagram Embed Code (optional)</label>
        <div class="controls">
            <textarea name="data_1" class="span8" rows="4">{{ $the_home->data_1 }}</textarea>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">Instagram Profile URL</label>
        <div class="controls">
            <input type="text" name="data_2" class="span8" value="{{ $the_home->data_2 }}" />
        </div>
    </div>
@else
    @if ($the_home->data_1_title)
        <div class="control-group">
            <label class="control-label">{{ $the_home->data_1_title }}</label>
            <div class="controls">
                <textarea name="data_1" class="span8" rows="4">{{ $the_home->data_1 }}</textarea>
            </div>
        </div>
    @endif
    @if ($the_home->data_2_title)
        <div class="control-group">
            <label class="control-label">{{ $the_home->data_2_title }}</label>
            <div class="controls">
                <input type="text" name="data_2" class="span8" value="{{ $the_home->data_2 }}" />
            </div>
        </div>
    @endif
@endif
