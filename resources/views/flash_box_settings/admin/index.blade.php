@extends('layout.admin.masters')

@section('content')
<div id="main-content">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <h3 class="page-title">
                    Flash Box
                    <small>Control Flash Box animation and display behaviour on the public website.</small>
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a>
                        <span class="divider">&nbsp;</span>
                    </li>
                    <li>Flash Box</li>
                </ul>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span12">
                <div class="widget">
                    <div class="widget-title">
                        <h4><i class="icon-list"></i> Flash Box Items</h4>
                        @if ($addAccess)
                            <a href="{{ route('admin.home.add.live') }}" class="btn btn-primary pull-right">
                                <i class="icon-plus"></i> Add Flash Box
                            </a>
                        @endif
                    </div>
                    <div class="widget-body">
                        <div id="flash-box-msgdisp">
                            @include('layout.admin.alert')
                        </div>
                        <input type="hidden" id="flash-box-csrf-token" value="{{ csrf_token() }}">
                        <div class="scroller">
                            <table class="table table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>SN</th>
                                        <th>Flash Box Item</th>
                                        <th>URL</th>
                                        <th>Updated On</th>
                                        @if ($publishAccess)
                                            <th>Publish</th>
                                        @endif
                                        @if ($editAccess)
                                            <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($flashBoxes as $index => $home)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @foreach ($home->details as $detail)
                                                    {{ $detail->home_heading }}
                                                @endforeach
                                            </td>
                                            <td>{{ $home->home_url }}</td>
                                            <td>{{ date_format(date_create($home->updated_at), 'M d,Y') }}</td>
                                            @if ($publishAccess)
                                                <td>
                                                    <a href="javascript:void(0)"
                                                       class="flash-box-change-status"
                                                       data-id="{{ $home->id }}">
                                                        @if ($home->is_active == 1)
                                                            <i class="icon-check"></i>
                                                        @else
                                                            <i class="icon-check-empty"></i>
                                                        @endif
                                                    </a>
                                                </td>
                                            @endif
                                            @if ($editAccess)
                                                <td>
                                                    <a href="{{ route('admin.home.edit', $home->id) }}">
                                                        <i class="icon-pencil"></i> Edit
                                                    </a>
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ 4 + ($publishAccess ? 1 : 0) + ($editAccess ? 1 : 0) }}">
                                                No Flash Box items found for this office.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($isSuperAdmin)
        <div class="row-fluid">
            <div class="span12">
                <div class="widget">
                    <div class="widget-title">
                        <h4><i class="icon-sliders"></i> Flash Box Animation Controls
                            <span class="label label-info"><i class="icon-lock"></i> Super Admin</span>
                        </h4>
                    </div>

                    <div class="widget-body form">
                        <div class="alert alert-info">
                            These settings control the Flash Box display on the public website.
                            They do not change Flash Box content or the Admin Preview.
                            You can add more Flash Box items without a fixed three-item limit.
                        </div>

                        <form action="{{ route('admin.flash-box-settings.update') }}"
                              method="POST"
                              class="form-horizontal">
                            @csrf

                            @php
                                $switches = [
                                    'animation_enabled' => 'Enable Flash Box Animation',
                                    'autoplay_enabled' => 'Enable Automatic Slide',
                                    'pause_on_hover' => 'Pause When Mouse Hovers',
                                    'arrows_enabled' => 'Show Previous / Next Arrows',
                                    'dots_enabled' => 'Show Navigation Dots',
                                    'mobile_animation_enabled' => 'Enable Animation on Mobile',
                                ];
                            @endphp

                            @foreach ($switches as $field => $label)
                                <div class="control-group">
                                    <label class="control-label" for="{{ $field }}">
                                        {{ $label }}
                                    </label>
                                    <div class="controls">
                                        <input type="hidden" name="{{ $field }}" value="0">
                                        <label class="checkbox">
                                            <input type="checkbox"
                                                   id="{{ $field }}"
                                                   name="{{ $field }}"
                                                   value="1"
                                                   {{ old($field, $settings->{$field}) ? 'checked' : '' }}>
                                            Enable
                                        </label>
                                    </div>
                                </div>
                            @endforeach

                            <div class="control-group">
                                <label class="control-label" for="autoplay_speed">
                                    Autoplay Duration
                                </label>
                                <div class="controls">
                                    <input type="number"
                                           id="autoplay_speed"
                                           name="autoplay_speed"
                                           class="span2"
                                           min="2000"
                                           max="15000"
                                           step="500"
                                           value="{{ old('autoplay_speed', $settings->autoplay_speed) }}"
                                           required>
                                    <span class="help-inline">Milliseconds (2000–15000 = 2–15 seconds)</span>
                                    @error('autoplay_speed')
                                        <span class="help-block" style="color:#b94a48;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="animation_effect">
                                    Animation Effect
                                </label>
                                <div class="controls">
                                    <select id="animation_effect" name="animation_effect" class="span3">
                                        <option value="slide" {{ old('animation_effect', $settings->animation_effect) === 'slide' ? 'selected' : '' }}>
                                            Slide
                                        </option>
                                        <option value="fade" {{ old('animation_effect', $settings->animation_effect) === 'fade' ? 'selected' : '' }}>
                                            Fade
                                        </option>
                                    </select>
                                    @error('animation_effect')
                                        <span class="help-block" style="color:#b94a48;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-ok-sign"></i> Save Flash Box
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn">
                                    <i class="icon-remove-sign"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('footer_js')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('#flash-box-csrf-token').val()
        }
    });

    $(document).on('click', '.flash-box-change-status', function (e) {
        e.preventDefault();

        var button = $(this);
        var id = button.data('id');

        if (button.data('busy')) {
            return;
        }

        button.data('busy', true);

        $.ajax({
            url: "{{ route('admin.flash-box.change-status') }}",
            type: "POST",
            dataType: "json",
            data: { id: id },
            beforeSend: function () {
                $('#ajax-notifier-background').css('display', 'block');
            },
            success: function (response) {
                if (response.type === 'success') {
                    $('#flash-box-msgdisp').html(
                        '<div class="alert alert-success"><button class="close" data-dismiss="alert">×</button><strong>Success! </strong>' +
                        $('<div>').text(response.message).html() + '</div>'
                    );
                    button.html(response.icon);
                } else {
                    $('#flash-box-msgdisp').html(
                        '<div class="alert alert-error">' +
                        $('<div>').text(response.message || 'Status could not be changed.').html() +
                        '</div>'
                    );
                }
            },
            error: function (xhr) {
                var message = 'Status could not be changed. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                $('#flash-box-msgdisp').html(
                    '<div class="alert alert-error">' +
                    $('<div>').text(message).html() + '</div>'
                );
            },
            complete: function () {
                button.data('busy', false);
                $('#ajax-notifier-background').css('display', 'none');
            }
        });
    });
</script>
@endsection
