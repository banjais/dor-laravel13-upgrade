@extends('layout.admin.masters')
@section('content')


		<div id="main-content">

		  <div class="container-fluid">

			<div class="row-fluid">
					<div class="span12">




						<h3 class="page-title">
							Error!
							<small>Access Denied</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="#">Error</a> <span class="divider">&nbsp;</span>
                            </li>

                        </ul>

					</div>
			  </div>


              <div id="page">
              <div class="row-fluid circle-state-overview">
				<div class="portlet-body">
					<div class="" data-height="600px">
						<p>An error has occurred while processing your request. This may occured because there was an attemt to manipulate this software. Users are prohibited from taking unauthorized actions to intentionally modify the system.</p>
                        <?php /*<h4>Regards,</h4>
                        <h4>Peace Nepal DOT Com P. Ltd.</h4> */?>
					</div>
				</div>
			  </div>
              </div>

			</div>

		</div>

	@stop
