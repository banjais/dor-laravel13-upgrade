@extends('layout.admin.masters')
@section('content')
@php
    use App\Models\Language;
@endphp
<script>
    $('li').removeClass('active');
    $('#staffsmgmt').addClass('active');
</script>
<div id="ajax-notifier-background">
    <img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants"/>
</div>
<div id="main-content">
    <!-- BEGIN PAGE CONTAINER -->
    <div class="container-fluid">
        <!-- BEGIN PAGE HEADER -->
        <div class="row-fluid">
            <div class="span12">
                <!-- BEGIN THEME CUSTOMIZER -->
                <!-- END THEME CUSTOMIZER -->
                <!-- BEGIN PAGE TITLE & BREADCRUMB -->
                <h3 class="page-title">
                    Staffs
                    <small>
                        Edit Staffs.
                    </small>
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <a href="{{ route('dashboard') }}">
                            <i class="icon-home">
                            </i>
                        </a>
                        <span class="divider">
                            &nbsp;
                        </span>
                    </li>
                    <li>
                        <a href="{{ route('admin.staffs') }}">
                            Staff Management
                        </a>
                        <span class="divider">
                            &nbsp;
                        </span>
                    </li>
                    <li>
                        <a href="#">
                            Edit Staff
                        </a>
                        <span class="divider-last">
                            &nbsp;
                        </span>
                    </li>
                    @if($addAccess)
                    <a href="{{ route('admin.staff.add') }}">
                        <button class="btn  btn-primary top-button pull-right ">
                            <i class="icon-plus">
                            </i>
                            Add Staff
                        </button>
                    </a>
                    @endif
                </ul>
                <!-- END PAGE TITLE & BREADCRUMB -->
            </div>
        </div>
        <!-- END PAGE HEADER -->
        <!-- BEGIN PAGE CONTENT -->
        <div id="page">
            <!--
                <button class="btn btn-large btn-primary ">
                    <i class="icon-plus">
                    </i>
                    Create Applicants
                </button>
            -->
            <div class="row-fluid">
                <div class="span12">
                    <!-- BEGIN SAMPLE FORM widget -->
                    <div class="widget">
                        <div class="widget-title">
                            <h4>
                                <i class="icon-reorder">
                                </i>
                                Edit staff Form
                            </h4>
                        </div>
                        <div class="widget-body form">
                            <div id="msgdisp">
                            </div>
                            <!-- BEGIN FORM -->
                            <form action="" class="form-horizontal" name="editform" id="editform" method="post" enctype="multipart/form-data">
                                <input type="hidden"  id="id" name="id" value="{{ $staff->id }}" />
                                @csrf
                                <?php
                                foreach($staff->details as $detail){
                                //echo '
                               // <input type="hidden" name="id_'.$detail->language->language_code.'" id="id_'.$detail->language->language_code.'" data-required="1" class="span12 " value="'.$detail->detail_id.'" />
                                //';

								 echo '
                                <input type="hidden" name="id_'.$detail->language->language_code.'" id="id_'.$detail->language->language_code.'" data-required="1" class="span12 " value="'.$detail->id.'" />
                                ';
                                }
                                foreach($staff->details as $detail){
                                echo '
                                <div class="control-group">
                                    <label class="control-label">
                                        Full Name('.$detail->language->language_name.')
                                    </label>
                                    <div class="controls">
                                        <input type="text" name="full_name_'.$detail->language->language_code.'" id="full_name_'.$detail->language->language_code.'" data-required="1" class="span12 " value="'.$detail->full_name.'" />
                                    </div>
                                </div>
                                ';
                                } ?>

                                <div class="control-group">
                                <label class="control-label">
                                    Designation *
                                </label>
                                <div class="controls">
                                    <select name="designation_id" data-placeholder="Select the designation" class="chosen-with-diselect span8" tabindex="-1" id="designation_id">
                                        <option value="">Select Designation</option>
                                        @foreach($designations as $designation)
                                        <option value="{{ $designation->id }}" @if($designation->id == $staff->designation_id) selected="selected" @endif>{{ $designation->heading }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">
                                    Level
                                </label>
                                <div class="controls">
                                    <select name="level_id" data-placeholder="Select the level" class="chosen-with-diselect span8" tabindex="-1" id="level_ids">
                                        <option value="">Select Level</option>
                                        @foreach($levels as $level)
                                        <option value="{{ $level->id }}"  @if($level->id == $staff->level_id) selected="selected" @endif>{{ $level->heading }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">
                                    Service
                                </label>
                                <div class="controls">
                                    <select name="service_id" data-placeholder="Select the service" class="chosen-with-diselect span8" tabindex="-1" id="service_id">
                                        <option value="">Select Service</option>
                                        @foreach($services as $service)
                                        <option value="{{ $service->id }}"  @if($service->id == $staff->service_id) selected="selected" @endif>{{ $service->heading }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">
                                    Group
                                </label>
                                <div class="controls">
                                    <select name="group_id" data-placeholder="Select the group" class="chosen-with-diselect span8" tabindex="-1" id="group_ids">
                                        <option value="">Select Group</option>
                                        @foreach($groups as $group)
                                        <option value="{{ $group->id }}"  @if($group->id == $staff->group_id) selected="selected" @endif>{{ $group->heading }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label">
                                    Sub Group
                                </label>
                                <div class="controls">
                                    <select name="subgroup_id" data-placeholder="Select the subgroup" class="chosen-with-diselect span8" tabindex="-1" id="subgroup_id">
                                        <option value="">Select Sub Group</option>
                                        @foreach($subgroups as $subgroup)
                                        <option value="{{ $subgroup->id }}"  @if($subgroup->id == $staff->subgroup_id) selected="selected" @endif>{{ $subgroup->heading }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                                <?php
                                foreach($staff->details as $detail){
                                echo '
                                <div class="control-group">
                                    <label class="control-label">
                                        Remark('.$detail->language->language_name.')
                                    </label>
                                    <div class="controls">
                                        <input type="text" name="remark_'.$detail->language->language_code.'" id="remark_'.$detail->language->language_code.'" data-required="1" class="span8 "  value="'.$detail->remarks .'" />
                                    </div>
                                </div>
                                ';
                                }
								/*
                                foreach($staff->details as $detail){
                                echo '
                                <div class="control-group">
                                    <label class="control-label">
                                        ID Number('.$detail->language->language_name.')
                                    </label>
                                    <div class="controls">
                                        <input type="text" name="id_'.$detail->language->language_code.'" id="id_'.$detail->language->language_code.'" data-required="1" class="span12 " value="'.$detail->id.'" />
                                    </div>
                                </div>
                                ';
                                }*/
                                ?>

                                <div class="control-group">
                                    <label class="control-label">
                                        Gender
                                    </label>
                                    <div class="controls">
                                        <select name="gender" data-placeholder="Please Select gender" class="chosen-with-diselect span12" tabindex="-1" id="gender">
                                            <option value="M">
                                                Male
                                            </option>
                                            <option value="F">
                                                Female
                                            </option>
                                            <option value="O">
                                                Omni
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <?php
                                foreach($staff->details as $detail){
                                echo '
                                <div class="control-group">
                                    <label class="control-label">
                                        Permanent Address('.$detail->language->language_name.')
                                    </label>
                                    <div class="controls">
                                        <input type="text" name="permanent_address_'.$detail->language->language_code.'" id="permanent_address_'.$detail->language->language_code.'" data-required="1" class="span12 " value="'.$detail->permanent_address.'" />
                                    </div>
                                </div>
                                ';
                                }
                                foreach($staff->details as $detail){
                                echo '
                                <div class="control-group">
                                    <label class="control-label">
                                        Temporary Address('.$detail->language->language_name.')
                                    </label>
                                    <div class="controls">
                                        <input type="text" name="temporary_address_'.$detail->language->language_code.'" id="temporary_address_'.$detail->language->language_code.'" data-required="1" class="span12 " value="'.$detail->temporary_address.'" />
                                    </div>
                                </div>
                                ';
                                }

                                foreach($staff->details as $detail){
                                echo '
                                <div class="control-group">
                                    <label class="control-label">
                                        Contact Number('.$detail->language->language_name.')
                                    </label>
                                    <div class="controls">
                                        <input type="text" name="contact_number_'.$detail->language->language_code.'" id="contact_number_'.$detail->language->language_code.'" data-required="1" class="span12 " value="'.$detail->contact_number.'" />
                                    </div>
                                </div>
                                ';
                                }
                                ?>
                                <div class="control-group">
                                    <label class="control-label">
                                        Email Address
                                    </label>
                                    <div class="controls">
                                        <input type="email" name="email" id="email" data-required="1" class="span12 " value="{{ $staff->email_address }}" />
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">
                                        Office Join Date
                                    </label>
                                    <div class="controls">
                                      <input type="text" name="office_join_date" id="office_join_date" data-required="1" class="span12 date-picker" value="{{ $staff->office_join_date }}" />
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Current Photo</label>
                                    <div class="controls">
                                        <div class="thumbnail" style="max-width:200px;">
                                            @include('helpers.attachment', ['src'=>$staff->photo,'folder'=>'staffs'])
                                        </div>
                                    </div>
                                </div>

                                @include('admin.photo_studio.controls', ['photoStudioType' => 'staff'])

                                <label class="control-label">
                                    Status
                                    </label>
                                    <div class="controls">
                                        <select name="is_active" data-placeholder="Select the status" class="chosen-with-diselect span12" tabindex="-1" id="status">
                                            <option value="0" @if($staff->is_active == 0) selected="selected" @endif>
                                                Unpublished
                                            </option>
                                            <option value="1" @if($staff->is_active == 1) selected="selected" @endif>
                                                Published
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div id="date-validation" style=" margin-left:0px;color:red;">
                                </div>
                                <div id="date-validation" style=" margin-left:0px;color:red;">
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="icon-ok-sign">
                                        </i>
                                        Submit
                                    </button>
                                    <a href="{{ $abs_site_url }}admin/staffs">
                                        <button type="button" class="btn btn-danger ">
                                            <i class="icon-remove-sign">
                                            </i>
                                            Cancel
                                        </button>
                                    </a>
                                </div>
                            </form>
                            <!-- END FORM -->
                        </div>
                    </div>
                    <!-- END SAMPLE FORM widget -->
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT -->
    </div>
    <!-- END PAGE CONTAINER -->
</div>
<!--
    BEGIN PAGE
-->
</div>
<!--
END CONTAINER
-->

<script type="text/javascript">
/**
* Basic jQuery Validation Form Demo Code
* Copyright Sam Deering 2012
* Licence: http://www.jquery4u.com/license/
*/
(function($,W,D){
  var JQUERY4U = {};
  JQUERY4U.UTIL =
  {
    setupFormValidation: function()
    {
      //form validation rules
      $("#editform").validate({
        rules: {
            designation_id:"required",
        <?php
            foreach($languages as $language){
                echo 'full_name_'.$language->language_code.': "required",';
            }
        ?>

        },
        messages: {
            designation_id:"Please enter the designaiton of the staff.",
          <?php
          foreach($languages as $language){
          echo 'full_name_'.$language->language_code.': "Please enter the full name of the staff in '.$language->language_name.'",';
          }
          ?>

        },
      });
    }
  }
  //when the dom has loaded setup form validation rules
  $(D).ready(function($) {
    JQUERY4U.UTIL.setupFormValidation();
  });
})(jQuery, window, document);
</script>

@stop

@section('footer_js')
<script src="{{ asset('assets/admin/js/photo-studio.js') }}?v={{ filemtime(public_path('assets/admin/js/photo-studio.js')) }}"></script>
@stop
