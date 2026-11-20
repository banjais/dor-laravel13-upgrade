@php
use App\Models\OfficeChief;
$officeId = 4;
$staffs = OfficeChief::with([
    'details' => function ($query) use ($languageId, $officeId) {
        $query->where('language_id', $languageId);
    },
])
    ->where('office_id', $officeId)
    ->where('is_deleted', '0')
    ->where('is_active', '1')
    ->orderBy('order_position', 'asc')
    ->get();

// $engineers = $staffs->where('chief_type', 'Chief Engineer');
// $directors = $staffs->where('chief_type', 'Director General');

// $engineer_title = \Helper::getNameOftext('office_chiefs_engineer', $languageId);
// $chief_title = \Helper::getNameOftext('office_chiefs_dg', $languageId);
@endphp

<div class="block-title">
    <h2>{{ \Helper::getNameOftext('office_chiefs', $languageId) }}</h2>
    <div class="divider"></div>
</div>
<p>{{ \Helper::getNameOftext('office_chiefs_list', $languageId) }}</p>
<div id="chiefs" class="row mt-2">
    @foreach ($staffs as $index => $staff)
        <div class="col-3">
            <div class="border border-1 hip-item text-center mb-3 dept-wrap">
                @if ($staff->photo)
                    <div class="fig-holder"> <img src="{{ asset('uploads/office_chiefs/' . $staff->photo) }}"
                            width="120" /></div>
                @endif
                <p><strong>{{ $staff->chief_type }}</strong></p>
                @foreach ($staff->details as $detail)
                    <h6>{{ $detail->full_name }}</h6>
                    <div>{{ $staff->date_ad }} AD ({{ $staff->date_bs }} BS)</div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

@section('scripts')
    <script type="text/javascript" charset="utf8" src="{{ asset('assets/js_2021/jquery.paginate.min.js') }}">
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#chiefs').paginate({
                // pagination_class: "pagination",
                items_per_page: 12,
                prev_next: false,
                prev_text: '«',
                next_text: '»'
            });
        });
    </script>
@endsection
