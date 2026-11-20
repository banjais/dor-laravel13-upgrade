@extends('layout.admin.masters')
@section('content')
@section('footer_js')
<script>
	$('li').removeClass('active');
	$('#misc').addClass('active');
	$('#equipments').addClass('active');
</script>
@endsection

        <div id="ajax-notifier-background">
        		<img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
        </div>


		<div id="main-content">

		  <div class="container-fluid">

			<div class="row-fluid">
					<div class="span12">




						<h3 class="page-title">
							Equipments
							<small>Add, edit, delete equipments and more</small>
						</h3>
                        <ul class="breadcrumb">
                             <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ route('admin.equipments') }}">Equipment Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>View Equipment : {{ $equipment->equipment_name }}<span class="divider-last">&nbsp;</span></li>
                        </ul>

					</div>
			  </div>


              <div id="page">

              <div class="row-fluid">
                <div class="span12 ">
                    <!-- BEGIN widget widget-->
                    <div class="widget">
                        <div class="widget-title">
                            <h4><i class="icon-reorder"></i>Equipment Log : {{ $equipment->equipment_name }} </h4>
                        </div>
                        <div class="widget-body">
                        	<div id="msgdisp">
                        		@include('layout.admin.alert')
                        	</div>
                            <div class="scroller" data-height="200px">

                                <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
                                    <thead>
                                    <tr>
                                        <th>SN</th>
                                        <th>Office Name</th>
                                        <th>Equipment Log</th>
                                    </tr>
                                    </thead>
                                    <tbody>
									{{-- */ $count = 1 /* --}}
                                    @foreach ($equipmentLogs as $key=>$equipment)
									<tr>
										<td>{{ $key+1 }}</td>
										<td>{{ $equipment->office_name }}</td>
										<td>{{ $equipment->equipment_log }}</td>
									</tr>
									@endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- END widget widget-->
                </div>
            </div>

              </div>

			</div>

		</div>

	@stop
