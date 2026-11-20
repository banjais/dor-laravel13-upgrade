@php
    $bandwidth_code = Session::get('bandwidth_code', 'high');
@endphp

<div
    class="slider-wrap shadow-none"
    id="single-slider-live"
    data-animation-enabled="{{ $flashBoxSettings->animation_enabled ? '1' : '0' }}"
    data-autoplay-enabled="{{ $flashBoxSettings->autoplay_enabled ? '1' : '0' }}"
    data-autoplay-speed="{{ $flashBoxSettings->autoplay_speed }}"
    data-effect="{{ $flashBoxSettings->animation_effect }}"
    data-pause-on-hover="{{ $flashBoxSettings->pause_on_hover ? '1' : '0' }}"
    data-arrows-enabled="{{ $flashBoxSettings->arrows_enabled ? '1' : '0' }}"
    data-dots-enabled="{{ $flashBoxSettings->dots_enabled ? '1' : '0' }}"
    data-mobile-animation-enabled="{{ $flashBoxSettings->mobile_animation_enabled ? '1' : '0' }}"
>
    @forelse ($flashBoxItems as $item)
        @php
            if ($item->data_6) {
                $destinationUrl = $item->data_6;
            } elseif ($item->data_5) {
                $destinationUrl = $item->data_5;
            } elseif ($item->data_7) {
                $destinationUrl = $item->data_7;
            } else {
                $destinationUrl = $item->data_1;
            }
        @endphp

        <div>
            <div class="block-title">
                <h2>{{ $item->home_heading }}</h2>
                <div class="divider"></div>
            </div>

            <div class="video-wrap">
                <div class="fixed-vdo">
                    @if ($item->data_1 && $bandwidth_code == 'high')
                        <a href="{{ $destinationUrl }}" target="_blank">
                            <img src="{{ $item->data_1 }}" width="100%" />
                        </a>
                    @endif
                </div>

                <div class="row mt-2 align-items-center">
                    <div class="col-md-6">
                        <p>
                            <span class="live-alert" style="background:{{ $item->data_2 }}"></span>
                            {{ $languageId == 1 ? $item->data_3 : $item->data_4 }}
                        </p>
                    </div>

                    <div class="col-md-6">
                        <div class="text-md-end mt-md-0 mt-1">
                            <a href="/{{ $subdomain }}/home/{{ $item->home_url }}" class="more-btn">
                                {{ $languageId == 1 ? 'पूरा पढ्नुहोस्' : 'Read More' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center">
            {{ $languageId == 1 ? 'हाल कुनै जानकारी उपलब्ध छैन।' : 'No information available.' }}
        </div>
    @endforelse
</div>
