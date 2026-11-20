@extends('layout.admin.masters')
@section('content')
<script>
	$('li').removeClass('active');
	$('#usersmgmt').addClass('active');
	$('#officemgmt').addClass('active');
</script>

<div id="ajax-notifier-background">
        		<img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
        </div>


		<div id="main-content">

		  <div class="container-fluid">

			<div class="row-fluid">
					<div class="span12">




						<h3 class="page-title">
							Offices
							<small>Add, edit, delete offices and more</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ $abs_site_url }}admin/home"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ $abs_site_url }}admin/offices">Office Management</a> <span class="divider">&nbsp;</span>
                            </li>
                         </ul>

					</div>
			  </div>


              <div id="page">


              <div class="row-fluid">
                <div class="span12 ">
                    <!-- BEGIN widget widget-->
                    <div class="widget">
                        <div class="widget-title">
                            <h4><i class="icon-reorder"></i>List of Offices</h4>

                        </div>
                        <div class="widget-body">
                        	<div id="msgdisp">
                        		@include('layout.admin.alert')
                        	</div>
                            <div class="scroller" data-height="200px">
                               <div class="row-fluid" id="offices">
								<ul>
								@foreach($offices as $office)
									<div class="span3" id="office_{{ $office->id }}">
										<div class="item">
										 	<img src="{{ asset('assets/admin/img/logonew.png') }}" style="width: 200px; height: 150px;" alt="" />
                                            <span>{{ $office->office_name }}</span>
										</div>
									</div>
								@endforeach
								</ul>
							</div>


                            </div>
                        </div>
                    </div>
                    <!-- END widget widget-->
                </div>
            </div>

              </div>

			</div>

		</div>

<script type="text/javascript">
	$(document).ready(function(){
		$("#offices ul").sortable({
			update: function (event, ui) {
				var orderOffices = $(this).sortable('serialize');
				debugger;
				$.ajax({
					type: 'POST',
					url: sitePath+"admin/office/sort_offices",
					data: orderOffices,
					success: function(response){
						var $jsondata = JSON.parse(response);
						debugger;
						$("#success-message").html('<div class="portlet-body"><div class="alert alert-success"><button class="close" data-dismiss="alert"></button>'+$jsondata.message+'</div></div>');

					},
					error: function(e){
						alert('Server Error');
					},
				});
			}
		});
	});


</script>

	@stop
