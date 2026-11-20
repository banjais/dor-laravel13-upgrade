@extends('layout.admin.masters')
@section('content')
<script>
	$('li').removeClass('active');
	$('#accessmgmt').addClass('active');
</script>
<style>
	td a.check{font-size:1.4em; cursor:pointer; cursor:hand; text-decoration:none;}
</style>
</div>
		<!-- END SIDEBAR -->

		<div id="main-content">

		  <div class="container-fluid">

			<div class="row-fluid">
					<div class="span12">




						<h3 class="page-title">
							User  Acess Control
						  <small>Configuration Page</small>
					  </h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="#"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="#">Access List Management</a> <span class="divider">&nbsp;</span>
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
                            <h4><i class="icon-reorder"></i>List of Actions to Each Module and User Type</h4>

                        </div>
                        <div class="widget-body">
                            <div class="scroller" data-height="200px">


                                <table class="table table-striped table-hover table-bordered">
                                    <thead>
                                    <tr>

                                      <th>User Details</th>
                                      <th>Current Privileges</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    	{{ $accesslist }}
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

	</div>
	<!-- END CONTAINER -->
    <div id="myModal1" class="modal hide fade " tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">

                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h3 id="myModalLabel1">Add New User Type</h3>

                                        </div>

                                        <div class="modal-body">
                                        <form name="addusertype" id="addusertype" action="{{ $abs_site_url }}admin/add-user-type" method="post">
                                          <div class="control-group">
                           <label class="control-label">User Type</label>
                           <div class="controls">
                             <input type="text" name="user_type" id="user_type" class="span5 " />
                             </div>
                           </div>
                                          </div>

                                        <div class="modal-footer">


                                            <button class="btn btn-primary"><i class="icon-ok-sign"></i> Save</button>
                                            <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true"><i class="icon-remove-sign"></i> Close</button>
                                         <div id="msgdisp" class="span5" style="float:left; margin-top:5px;"></div>
                                        </div>
                                        </form>

                                    </div>


                          <div id="myModal2" class="modal hide fade " tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">

                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h3 id="myModalLabel1">Add New Module</h3>

                                        </div>

                                        <div class="modal-body">
                                        <form name="addmodule" id="addmodule" action="{{ $abs_site_url }}admin/add-module" method="">
                                          <div class="control-group">
                           <label class="control-label">Module Name</label>
                           <div class="controls">
                             <input type="text" name="module_name" id="module_name" class="span5 " />
                             </div>
                           </div>
                                          </div>

                                        <div class="modal-footer">


                                            <button class="btn btn-primary"><i class="icon-ok-sign"></i> Save</button>
                                            <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true"><i class="icon-remove-sign"></i> Close</button>
                                         <div id="msgdisp1" class="span5" style="float:left; margin-top:5px;"></div>
                                        </div>
                                        </form>

                                    </div><script type="text/javascript" src="{{ $rel_site_url }}assets/js/jquery.validate.min.js"></script>
<script type="text/javascript">
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
            $("#addusertype").validate({
                rules: {
                    user_type: "required",

                },
                messages: {
                    user_type: "Please enter the title of the user type.",
				},
                submitHandler: function(form) {
					//form.submit();

						$data=$('#addusertype').serialize();
						$.ajax({
							url   :  $('#addusertype').attr('action'),
							type  :  "POST",
							data  :  $data,
							success : function(data){
											//alert(data);
											$json= JSON.parse(data);
											type= $json.type;
											message=$json.message;
											if(type=="success")
											{
												$('#msgdisp').append('<div class="alert alert-success"><button class="close" data-dismiss="alert">×</button><strong>Success! </strong>'+ message+'</div>');
												$('#addusertype')[0].reset();
											}
											else if(type=='error'){
												$('#msgdisp').append('<div class="alert alert-error"><button class="close" data-dismiss="alert">×</button>									<strong>Error! </strong>'+ message +'</div>');
											}


							},
							error:     function(e){
										alert("Error occured. Please contact the company developer.");

						  }
			});


					}
				});

			$("#addmodule").validate({
                rules: {
                    module_name: "required",

                },
                messages: {
                    module_name: "Please enter the module name  of module.",
				},
                submitHandler: function(form) {
					//alert('Hello');
					form.submit();

						$data=$('#addmodule').serialize();
						$.ajax({
							url   :  $('#addmodule').attr('action'),
							type  :  "POST",
							data  :  $data,
							success : function(data){
											//alert(data);
											$json= JSON.parse(data);
											type= $json.type;
											message=$json.message;
											if(type=="success")
											{
												$('#msgdisp1').append('<div class="alert alert-success"><button class="close" data-dismiss="alert">×</button><strong>Success! </strong>'+ message+'</div>');

											}
											else if(type=='error'){
												$('#msgdisp1').append('<div class="alert alert-error"><button class="close" data-dismiss="alert">×</button>									<strong>Error! </strong>'+ message +'</div>');
											}


							},
							error:     function(e){
										alert("Error occured. Please contact the company developer.");

						  }
			});


					}
				});


        }

    }

    //when the dom has loaded setup form validation rules
    $(D).ready(function($) {
        JQUERY4U.UTIL.setupFormValidation();
    });

})(jQuery, window, document);
$(document).on('click','.check',function(){
		$datatosend="data="+this.id;
		$id=this;

		$.ajax({
			url		:	'{{ $abs_site_url }}admin/acess-control-user/change-access-status-dynamically'	,
			type	:	'POST',
			data	:	$datatosend,
			success	:	function(data){
							$json=JSON.parse(data);
							$($id).empty();
							$($id).append($json.message);
						},

			error	:	function(data){
							alert(data);
						}
		});
});
</script>

	@stop
