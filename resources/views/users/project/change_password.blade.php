@extends('layout.project.masters')
@section('content')

</div>
		<!-- END SIDEBAR -->

		<div id="main-content">

		  <div class="container-fluid">

			<div class="row-fluid">
					<div class="span12">




						<h3 class="page-title">
							Project User
							<small>Change Password</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="{{ route('project.admin.dashboard') }}"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li><a href="#">Change Password : {{ $user->username }}</a><span class="divider-last">&nbsp;</span></li>

                        </ul>

					</div>
			  </div>


              <div id="page">
              <!--<button class="btn btn-large btn-primary "><i class="icon-plus"></i> Create Applicants</button>-->

              <div class="row-fluid">
               <div class="span12">

                  <div class="widget">
                     <div class="widget-title">
                        <h4><i class="icon-reorder"></i>Change password Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp1">
                        <div id="msgdisp">
                        @include('layout.project.alert')
                      </div>
                      </div>

                        <form action="" class="form-horizontal" name="changepassword" id="changepassword" method="post">
                           <div class="control-group">
                           <label class="control-label">New Password</label>
                           	<div class="controls">
                                 <input type="password" name="new_password" id="new_password" data-required="1" class="span8 " />
                                 <input type="hidden" name="id" id="id" value="{{ $user->id }}"/>
                             </div>
                           </div>
                           <div class="control-group">
                           <label class="control-label">Confirm Password</label>                              <div class="controls">
                                 <input type="password" name="confirm_password" id="confirm_password" class="span8 " /><div id="uniquepassport" style="color:red; display:inline; margin-left:10px;"></div>
                             </div>
                           </div>
                           <div id="date-validation" style=" margin-left:0px;color:red;"></div>

                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                              <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button>
                           </div>
                        </form>

                     </div>
                  </div>

               </div>
            </div>

              </div>

			</div>

		</div>

	</div>
	<!-- END CONTAINER -->
   </div>

@section('footer_js')
<script type="text/javascript">
	$('li').removeClass('active');
	$('#usersmgmt').addClass('active');
	$('#users').addClass('active');
/**
  * Basic jQuery Validation Form Demo Code
  * Copyright Sam Deering 2012
  * Licence: http://www.jquery4u.com/license/
  */

(function($,W,D)
{
	var JQUERY4U = {};
    JQUERY4U.UTIL =
    {
        setupFormValidation: function()
        {
            //form validation rules
            $("#changepassword").validate({
                rules: {
					new_password: {
						required:	true,
						minlength:	6
					},
					confirm_password:{
						required:	true,
						minlength:	6,
						equalTo:	'#new_password'
					}
                },
                messages: {
					new_password:	{
						required:	"Please enter new password",
						minlength:	"Password must require at least 6 characters."
					},
					confirm_password:	{
						required:	"Please enter the confirm  password",
						equalTo:	"Confirm password didnot match"
					},
				},
        	});
        },
    }
    //when the dom has loaded setup form validation rules
    $(D).ready(function($) {
        JQUERY4U.UTIL.setupFormValidation();
    });

})(jQuery, window, document);

</script>
@endsection
@stop
