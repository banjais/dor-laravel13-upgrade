@extends('layout.admin.masters')
@section('content')
<style>
	#sample_1_wrapper .row-fluid div
	{
		position: relative;
		width: inherit;
		}

		#sample_1_wrapper .row-fluid div #sample_1_filter
	{
		position: relative;
		width: inherit;
		}


	#sample_1_wrapper .row-fluid div #sample_1_filter label{

		position: relative;
		width: inherit;
		}

	#sample_1_wrapper .row-fluid div #sample_1_filter label input{
		display: inline-block;
		width: 80%;
		margin-left: 3%;
}

#sample_1_wrapper .row-fluid div .dataTables_paginate.paging_bootstrap.pagination{
	float:right!important;
	width:auto;}

	#sample_1_wrapper .row-fluid div #sample_1_info{
		text-align: right;
		margin:0;
		padding-top: 5px;}
</style>
<script>
	$('li').removeClass('active');
	$('#usersmgmt').addClass('active');
</script>
		<div id="ajax-notifier-background">
        		<img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
        </div>

		<div id="main-content">

		  <div class="container-fluid">

			<div class="row-fluid">
					<div class="span12">




						<h3 class="page-title">
							Users
							<small>Edit Users.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="#"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="{{ $abs_site_url }}admin/users">User Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li><a href="#">Edit User</a><span class="divider-last">&nbsp;</span></li>
                            <a href="{{ $abs_site_url }}admin/user/add">
              <button class="btn  btn-primary top-button pull-right "><i class="icon-plus"></i> Add User</button>
              </a>

                        </ul>

					</div>
			  </div>


              <div id="page">
              <!--<button class="btn btn-large btn-primary "><i class="icon-plus"></i> Create Applicants</button>-->

              <div class="row-fluid">
               <div class="span7">

                  <div class="widget">
                     <div class="widget-title">
                        <h4><i class="icon-reorder"></i> Edit users Form</h4>

                     </div>
                     <div class="widget-body form">
                     	<div id="msgdisp">
                        		@include('layout.admin.alert')
                        	</div>

                        <form action="{{ $abs_site_url }}admin/user/edit" class="form-horizontal" name="editform" id="editform" method="post">
                           <div class="control-group">
                           <label class="control-label">First Name</label>                              <div class="controls">
                                 <input type="text" name="first_name" id="first_name" value="{{ $first_name }}" data-required="1" class="span12 " />
                                 <input type="hidden" id="id" name="id" value="{{ $id }}"/>
                             </div>
                           </div>
                           <div class="control-group">
                           <label class="control-label">Last Name</label>                              <div class="controls">
                                 <input type="text" name="last_name" id="last_name" value="{{ $last_name }}" data-required="1" class="span12 " />
                             </div>
                           </div>
                           <div class="control-group">
                           <label class="control-label">User Name</label>                              <div class="controls">
                                 <input type="text" name="username" id="username" data-required="1" class="span12 " value="{{ $user_name }}" />
                             </div>
                           </div>




                           <div class="control-group">
                           <label class="control-label">Email</label>                              <div class="controls">
                                 <input type="email" name="email" id="email" class="span12 " value="{{ $email }}" />
                             </div>
                           </div>
                          <div class="control-group">
                           <label class="control-label">Contact Number</label>                              <div class="controls">
                                 <input type="text" name="contact_number" id="contact_number" value="{{ $contact_number }}" class="span12 " />
                             </div>
                           </div>
                            <div class="control-group">
                              <label class="control-label">User Type</label>
                              <div class="controls">
                                 <select name="usertype" data-placeholder="Your Favorite Type of Bear" class="chosen-with-diselect span12" tabindex="-1" id="usertype">
                                    <option value="1">Admin</option>
                                    <option value="2">Normal</option>

                                 </select>
                                 <?php

								 ?>
                              </div>

                           </div>
                           <div class="control-group">
                              <label class="control-label">Status</label>
                              <div class="controls">
                                 <select name="status" data-placeholder="Select the status" class="chosen-with-diselect span12" tabindex="-1" id="status">
                                    <option value="0">Unpublished</option>
                                    <option value="1" selected="selected">Published</option>

                                 </select>

                              </div>

                           </div>






                                   <div id="date-validation" style=" margin-left:0px;color:red;"></div>






                           <div class="form-actions">
                              <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i> Submit</button>
                             <a href="{{ $abs_site_url }}admin/users"> <button type="button" class="btn btn-danger "><i class="icon-remove-sign"></i>  Cancel</button></a>
                           </div>
                        </form>

                     </div>
                  </div>

               </div>
            <div class="span5">

                  <div class="widget">
                     <div class="widget-title">
                        <h4><i class="icon-reorder"></i>Search for others</h4>

                     </div>
                     <div class="widget-body form">

                        <table class="table table-striped table-hover table-bordered" id="sample_1">
                                    <thead>
                                    <tr>


                                        <th>Full Name</th>
                                        <th>User Name</th>
                                        <th>Email</th>






                                    </tr>
                                    </thead>
                                    <tbody>
                                    	{{ $userlist }}
                                    </tbody>
                                </table>

                     </div>
                  </div>

               </div>
               </div>


              </div>

			</div>

		</div>

	</div>
	<!-- END CONTAINER -->
    <div id="myModal3" class="modal hide fade " tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">

                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h3 id="myModalLabel1">Add Job Category</h3>

                                        </div>

                                        <div class="modal-body">
                                        	<form name="addJob" id="addJob" action="{{ $abs_site_url }}add-job" method="post">
                                             <div class="control-group">
                           <label class="control-label">Job Category Name</label>                              <div class="controls">
                                 <input type="text" name="job_title" id="job_title" class="span5 " /><input type="text" id="fuckingid" />
                             </div>
                           </div>



                                        </div>
                                        <div class="modal-footer">

                                            <button class="btn btn-primary"><i class="icon-ok-sign"></i> Save</button>
                                            <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true"><i class="icon-remove-sign"></i> Close</button>
                                            <div id="msgdisp4" class="span5" style="float:left; margin-top:5px;"></div>
                                            </form>
                                        </div>
                                        </div>
    <div id="myModal1" class="modal hide fade " tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">

                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h3 id="myModalLabel1">Add New Internal Agent</h3>

                                        </div>

                                        <div class="modal-body">
                                        <form name="addAgent" id="addAgent" action="{{ $abs_site_url }}add-internal-agent" method="">
                                             <div class="control-group">
                           <label class="control-label">Full Name</label>                              <div class="controls">
                                 <input type="text" name="full_name" id="full_name" class="span5 " />
                             </div>
                           </div>
                           <div class="control-group">
                            <label class="control-label">Address</label>
                              <div class="controls">
                                 <textarea class="span5 " rows="3" name="address" id="address"></textarea>
                              </div>
                          </div>
                          <div class="control-group">
                           <label class="control-label">Contact Number</label>                              <div class="controls">
                                 <input type="text" name="contact_number" id="contact_number" class="span5 " />
                             </div>
                           </div>
                           <div class="control-group">
                           <label class="control-label">Email</label>                              <div class="controls">
                                 <input type="text" name="email" id="email" class="span5 " />
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
                                            <h3 id="myModalLabel1">Add New Company</h3>

                                        </div>


                                        <div class="modal-body">
                                        	<form name="addCompany" id="addCompany" action="{{ $abs_site_url }}add-company" method="post">
                                             <div class="control-group">
                           <label class="control-label">Company Name</label>                              <div class="controls">
                                 <input type="text" name="full_name" id="full_name" class="span5 " />
                             </div>
                           </div>
                           <div class="control-group">
                            <label class="control-label">Address</label>
                              <div class="controls">
                                 <textarea class="span5 " rows="3" name="address" id="address"></textarea>
                              </div>
                          </div>
                          <div class="control-group">
                           <label class="control-label">Contact Number</label>                              <div class="controls">
                                 <input type="text" name="contact_number" id="contact_number" class="span5 " />
                             </div>
                           </div>
                           <div class="control-group">
                           <label class="control-label">Email</label>                              <div class="controls">
                                 <input type="text" name="email" id="email" class="span5 " />
                             </div>
                           </div>
                                        </div>
                                        <div class="modal-footer">

                                            <button class="btn btn-primary"><i class="icon-ok-sign"></i> Save</button>
                                            <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true"><i class="icon-remove-sign"></i> Close</button>
                                            <div id="msgdisp2" class="span5" style="float:left; margin-top:5px;"></div>
                                            </form>
                                        </div>
                                       </div>


<script src="{{ $rel_site_url }}assets/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="{{ $rel_site_url }}assets/js/jquery.validate.min.js"></script>
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
            $("#editform").validate({
                rules: {
                    first_name: "required",
					last_name: "required",
                   /* email: {
                        required: true,
                        email: true
                    }, */
					username: {
                        required: true,

                    },
					contact_number: {
						required: true
					},

					email:		{
									required:	true,
									email:		true
								}

					/*referenced_by : {
						required : true
					}*/

                },
                messages: {
                    first_name: "Please enter your first name of the user",
					last_name: "Please enter your last name of the user",
					username: "Please enter the user name of the user",
					contact_number: "Please enter the contact number of the user",

					//referenced_by : "Please select the name of the agent"

                    email: 	{
							required:	"Please enter a valid email address",
							email	:	'Please enter the valid email'}

                   // message: "Please enter your message"*/
                },
                submitHandler: function(form) {
						//form.submit();
						$data=$('#editform').serialize();
						$.ajax({
							url   :  $('#editform').attr('action'),
							type  :  "POST",
							data  :  $data,
							beforeSend:	   function(){
									$('#ajax-notifier-background').css('display','block');
								},
							success : function(data){
											//alert(data);
											$json= JSON.parse(data);
											type= $json.type;
											message=$json.message;
											if(type=="success")
											{
												$('#msgdisp').empty();
												$('#msgdisp').append('<div class="alert alert-success"><button class="close" data-dismiss="alert">×</button><strong>Success! </strong>'+ message+'</div>');

											}
											else if(type=='error'){
												$('#msgdisp').empty();
												$('#msgdisp').append('<div class="alert alert-error"><button class="close" data-dismiss="alert">×</button>									<strong>Error! </strong>'+ message +'</div>');


											}


										},
							complete:	   function(){
									$('#ajax-notifier-background').css('display','none');
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
</script>

<script>
	 $(document).ready(function(){

	  	$('#addapplicant').submit(function(e){
			e.preventDefault();
			if($('#company_id').val()!='' && $('#date').val()=="")
			{
				$('#date-validation').text('If you select the compay you must select the date of document forwarded.');
				return false;
			}


		});

		$('#passport_number').on('focusout',function(){
			datatosend='pass='+$('#passport_number').val();
			//alert(datatosend);
			$.ajax({
				url:	'{{ $abs_site_url }}unique-passport',
				type: 	'POST',
				data:	datatosend,
				success: function(data){
								$json=JSON.parse(data);
								if($json.type=='error')
								{
									$('#uniquepassport').empty();
									$('#uniquepassport').append($json.message);
								}
								if($json.type=='success')
								{
									$('#uniquepassport').empty();

								}
							},
				error:	function(e){
							alert('error occured');
						}

			});
		});


	 });
</script>
<script>
	 $(document).ready(function(){
		$('#company_id').on('change',function(){
				if($('#company_id').val()!='')
				{
					$('#company-validation').empty();
				}
				$company_id='company_id='+$('#company_id').val();
				$('#demand_id').empty();
				$.ajax({
					url:		'{{ $abs_site_url }}get-demand-numbers',
					type:		'POST',
					data:		$company_id,
					success:    function(data){
									$json=JSON.parse(data);

									$('#demand_id').append($json.batchlist);
									$('#demand_id').trigger("liszt:updated");

										$('#job_cat').empty();
										$demand_id="demand_id="+$json.batchid;
										$.ajax({
											url:    '{{ $abs_site_url }}get-job-cateogory-by-demand-id',
											type:	'POST',
											data:	$demand_id,
											success: function(data){
														$json=JSON.parse(data);
														$('#job_cat').append($json.joblist);
														$('#job_cat').val('1').trigger("liszt:updated");
														}
										});


								},
					error:		function(e){
									alert('Please contact your software vendor.');
								}
				});
		});
//========================================================================================================
//========================================================================================================
	  	$('#addAgent').submit(function(e){
			e.preventDefault();
			$data=$('#addAgent').serialize();
			$.ajax({
				url   :  $('#addAgent').attr('action'),
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
									$('#addAgent')[0].reset();
								}
								else if(type=='error'){
									$('#msgdisp').append('<div class="alert alert-error"><button class="close" data-dismiss="alert">×</button>									<strong>Error! </strong>'+ message +'</div>');
								}


							},
				error:     function(e){
						  	alert("error occured");

						  }
			});


		});

	$(document).on('click','.edituser',function(){
		//alert('hello');
		datatosend="id="+this.id;
		$.ajax({
			url		:		'{{ $abs_site_url }}admin/user/get-info-dynamically',
			type	:		'POST',
			data	:		datatosend,
			beforeSend:	   function(){
									$('#ajax-notifier-background').css('display','block');
								},
			success	:		function(data){
								//alert(data);
								$json=JSON.parse(data);
								$('#id').val($json.id);
								$('#first_name').val($json.first_name);
								$('#last_name').val($json.last_name);
								$('#username').val($json.user_name);
								$('#email').val($json.email);
								$('#contact_number').val($json.contact_number);
								$('#usertype').val($json.user_type).trigger("liszt:updated");

								$('#status').val($json.status).trigger("liszt:updated");


							},
			complete:	   function(){
									$('#ajax-notifier-background').css('display','none');
								},
			error	:		function(){
								alert('error occured');
							}

		});
	});

	 });
</script>

	@stop
