<!-- BEGIN JAVASCRIPTS -->
	<!-- Load javascripts at bottom, this will reduce page load time -->
   
   <script type="text/javascript" src="{{ asset('assets/admin/bootstrap/js/bootstrap-fileupload.js') }}"></script>
   
   <!-- ie8 fixes -->
   <!--[if lt IE 9]>
   <script src="js/excanvas.js') }}"></script>
   <script src="js/respond.js') }}"></script>
   <![endif]-->
   <script type="text/javascript" src="{{ asset('assets/admin/chosen_bootstrap/chosen.jquery.js') }}"></script>
   <script type="text/javascript" src="{{ asset('assets/admin/uniform/jquery.uniform.min.js') }}"></script>
   <script type="text/javascript" src="{{ asset('assets/admin/bootstrap-wysihtml5/wysihtml5-0.3.0.js') }}"></script> 
   <script type="text/javascript" src="{{ asset('assets/admin/bootstrap-wysihtml5/bootstrap-wysihtml5.js') }}"></script>
   <script type="text/javascript" src="{{ asset('assets/admin/data-tables/jquery.dataTables.js') }}"></script>
   <script type="text/javascript" src="{{ asset('assets/admin/data-tables/DT_bootstrap.js') }}"></script>   
   
   <script type="text/javascript" src="{{ asset('assets/admin/clockface/js/clockface.js') }}"></script>
   <script type="text/javascript" src="{{ asset('assets/admin/jquery-tags-input/jquery.tagsinput.min.js') }}"></script>
   <script type="text/javascript" src="{{ asset('assets/admin/bootstrap-toggle-buttons/static/js/jquery.toggle.buttons.js') }}"></script>
   <script type="text/javascript" src="{{ asset('assets/admin/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>   
   <script type="text/javascript" src="{{ asset('assets/admin/bootstrap-daterangepicker/date.js') }}"></script>
   <script type="text/javascript" src="{{ asset('assets/admin/bootstrap-daterangepicker/daterangepicker.js') }}"></script> 
   <script type="text/javascript" src="{{ asset('assets/admin/bootstrap-colorpicker/js/bootstrap-colorpicker.js') }}"></script>  
   <script type="text/javascript" src="{{ asset('assets/admin/bootstrap-timepicker/js/bootstrap-timepicker.js') }}"></script>
   <script type="text/javascript" src="{{ asset('assets/admin/bootstrap-inputmask/bootstrap-inputmask.min.js') }}"></script>
   <script src="{{ asset('assets/admin/fancybox/source/jquery.fancybox.pack.js') }}"></script>
   
   <script src="{{ asset('assets/admin/js/jquery.form.js') }}"></script>
   <script src="{{ asset('assets/admin/js/scripts.js') }}"></script>

   <script type="text/javascript" src="{{ asset('assets/admin/js/jquery-ui.min.js') }}"></script>
	<script>
		$('#sample_editable_1').dataTable({
				
			});
    
		jQuery(document).ready(function() {
			// initiate layout and plugins			
			App.init();
			//UIJQueryUI.init();
			//TableEditable.init();	
			
		});
	</script>
    
	<!-- END JAVASCRIPTS -->