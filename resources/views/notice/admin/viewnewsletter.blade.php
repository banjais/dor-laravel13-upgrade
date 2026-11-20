@extends('layout.admin.masters')
@section('content')

<script>
	$('li').removeClass('active');
	$('#informations').addClass('active');
	$('#newsletters').addClass('active');
</script>


        <div id="ajax-notifier-background">
        		<img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
        </div>
		<div id="main-content">

		  <div class="container-fluid">

			<div class="row-fluid">
					<div class="span12">




						<h3 class="page-title">
							Newsletter
							<small>View newsletter.</small>
						</h3>
                        <ul class="breadcrumb">
                            <li>
                                <a href="../../home"><i class="icon-home"></i></a><span class="divider">&nbsp;</span>
                            </li>
                            <li>
                                <a href="../../newsletters">Newsletter Management</a> <span class="divider">&nbsp;</span>
                            </li>
                            <li>View Newsletter<span class="divider-last">&nbsp;</span></li>
                        </ul>

					</div>
			  </div>


              <div id="page">
              <!--<button class="btn btn-large btn-primary "><i class="icon-plus"></i> Create Applicants</button>-->


              </a>
              <div class="row-fluid">
               <div class="span12">

                  <div class="widget">
                     <div class="widget-title">
                        <h4><i class="icon-reorder"></i>View Newsletter</h4>

                     </div>
                     <div class="widget-body form">
                     	<?php if(Session::get('class')) { ?>
						<div class="<?php echo $class = Session::get('class');?>">
							<button class="close" data-dismiss="alert">x</button>
							<?php
								echo $message = Session::get('message');
								if(Session::get('attachment-error')) {
									echo Session::get('attachment-error');
								}
							?>
						</div>
						<?php } ?>


                           <div class="control-group">
                           <label class="control-label">Groups  </label>
                           <hr>
                          	<div class="controls span12">
                                  {{ $newsletters->emails }}
                             </div>
                           </div>


						   <div class="control-group">
                           <label class="control-label">Subject</label>
                           <hr>
                            <div class="controls">
                                {{ $newsletters->subject }}
                             </div>
                           </div>



                           <div class="control-group">
                           <label class="control-label">Newsletter Details  * </label>                          <hr>
                          	<div class="controls">
                              {{ $newsletters->description }}
                            </div>
                           </div>






						<div class="control-group">
              <label class="control-label">Published Date *</label>
              <hr>
              <div class="controls">
                  {{ $newsletters->published_date }}
              </div>
            </div>

                           <div class="control-group">
                                    <label class="control-label">Attachment</label>
                                    <hr>
                                    <div class="controls">
                                        <div class="fileupload fileupload-new" data-provides="fileupload">  <?php if(file_exists("uploads/notice/".$newsletters->attachment) && $newsletters->attachment!=''){ ?>
                                                        <img src="{{{'../../../uploads/notice/'.$newsletters->attachment}}}" alt="" />
                                                        <?php } else { ?>
                                                       <img src="<?php echo $rel_site_url.'assets/admin/img/no-image.png' ?>" alt="" />
                                                       <?php }  ?></div>


                                    </div>
                          </div>



                     </div>
                  </div>

               </div>
            </div>
              </div>

			</div>

		</div>


	@stop
