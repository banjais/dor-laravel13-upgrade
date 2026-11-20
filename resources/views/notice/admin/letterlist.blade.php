@extends('layout.admin.masters')
@section('content')
@section('footer_js')
<script>
	$('li').removeClass('active');
	$('#informations').addClass('active');
	$('#newsletters').addClass('active');
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
							Nesletters
							<small>Add, edit, delete newsletters and more</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="home"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                Nesletter Management <span class="divider">&nbsp;</span>
                            </li>
							@if($addAccess)
                            <a href="{{ route('admin.newsletter.add') }}"><button class="btn  btn-primary pull-right"><i class="icon-plus"></i> Create Newsletter</button></a>
							@endif
                        </ul>

					</div>
			  </div>


              <div id="page">

              <div class="row-fluid">
                <div class="span12 ">
                    <!-- BEGIN widget widget-->
                    <div class="widget">
                        <div class="widget-title">
                            <h4><i class="icon-reorder"></i>List of Nesletters</h4>

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
                                        <th>Subject</th>
                                        <th>Updated On</th>
										@if($deleteAccess || $editAccess)
                                        <th>Action</th>
										@endif
                                    </tr>
                                    </thead>
                                    <tbody>
									@foreach ($newsletters as $index=>$newsletter)
									<tr>
										<td>{{ $index+1 }}</td>
                                        <td>{{ $newsletter->subject }}</td>
										<td>{{ date_format(date_create($newsletter->updated_at), 'M d,Y') }}</td>
										@if($deleteAccess || $editAccess)
										<td>
											@if($editAccess)
											<a href="{{ route('admin.newsletter.view', $newsletter->id) }}"><i class="icon-refresh"></i> View</a>&nbsp;&nbsp;
											@endif

										</td>
										@endif
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
