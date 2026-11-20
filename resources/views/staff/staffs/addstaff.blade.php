@extends('layout.admin.masters')
@section('content')
<script>
    $('li').removeClass('active');
    $('#staffsmgmt').addClass('active');
</script>
<link rel="stylesheet" type="text/css" href="{{ $rel_site_url}}assets/admin/bootstrap-datepicker/css/datepicker.css" />
<!--
    BEGIN PAGE
-->
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
                        Add staff.
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
                        Add Staff
                        <span class="divider-last">
                            &nbsp;
                        </span>
                    </li>
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
                            Add Staff Form
                        </h4>
                    </div>
                    <div class="widget-body form">
                        <div id="msgdisp1">
                            @include('layout.admin.alert')
                        </div>
                        <!-- BEGIN FORM -->
                        <form action="" class="form-horizontal" name="addform" id="addform" method="post" enctype="multipart/form-data">
                            @csrf
                            <?php
                            foreach($languages as $language){
                            echo '
                            <div class="control-group">
                                <label class="control-label">
                                    Full Name('.$language->language_name.') *
                                </label>
                                <div class="controls">
                                    <input type="text" name="full_name_'.$language->language_code.'" id="full_name_'.$language->language_code.'" data-required="1" class="span8 " />
                                </div>
                            </div>
                            ';
                            }
                            ?>

                            <div class="control-group">
                                <label class="control-label">
                                    Designation *
                                </label>
                                <div class="controls">
                                    <select name="designation_id" data-placeholder="Select the designation" class="chosen-with-diselect span8" tabindex="-1" id="designation_id">
                                        <option value="">Select Designation</option>
                                        @foreach($designations as $designation)
                                        <option value="{{ $designation->id }}">{{ $designation->heading }}</option>
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
                                        <option value="{{ $level->id }}">{{ $level->heading }}</option>
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
                                        <option value="{{ $service->id }}">{{ $service->heading }}</option>
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
                                        <option value="{{ $group->id }}">{{ $group->heading }}</option>
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
                                        <option value="{{ $subgroup->id }}">{{ $subgroup->heading }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <?php

                            foreach($languages as $language){
                            echo '
                            <div class="control-group">
                                <label class="control-label">
                                    Remark('.$language->
                                    language_name.')
                                </label>
                                <div class="controls">
                                    <input type="text" name="remark_'.$language->language_code.'" id="remark_'.$language->language_code.'" data-required="1" class="span8 " />
                                </div>
                            </div>
                            ';
                            }
                           /* foreach($languages as $language){
                            echo '
                            <div class="control-group">
                                <label class="control-label">
                                    ID Number('.$language->
                                    language_name.')
                                </label>
                                <div class="controls">
                                    <input type="text" name="staff_id_'.$language->language_code.'" id="staff_id_'.$language->language_code.'" data-required="1" class="span8 " />
                                </div>
                            </div>
                            ';
                            }*/
                            ?>
                            <div id="staff-map-data">
                            </div>
                            <div class="control-group">
                                <label class="control-label">
                                    Gender
                                </label>
                                <div class="controls">
                                    <select name="gender" data-placeholder="Please Select gender" class="chosen-with-diselect span8" tabindex="-1" id="gender">
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
                            foreach($languages as $language){
                            echo '
                            <div class="control-group">
                                <label class="control-label">
                                    Permanent Address('.$language->
                                    language_name.')
                                </label>
                                <div class="controls">
                                    <input type="text" name="permanent_address_'.$language->language_code.'" id="permanent_address_'.$language->language_code.'" data-required="1" class="span8 " />
                                </div>
                            </div>
                            ';
                            }
                            foreach($languages as $language){
                            echo '
                            <div class="control-group">
                                <label class="control-label">
                                    Temporary Address('.$language->
                                    language_name.')
                                </label>
                                <div class="controls">
                                    <input type="text" name="temporary_address_'.$language->language_code.'" id="temporary_address_'.$language->language_code.'" data-required="1" class="span8 " />
                                </div>
                            </div>
                            ';
                            }

                            foreach($languages as $language){
                            echo '
                            <div class="control-group">
                                <label class="control-label">
                                    Contact Number('.$language->
                                    language_name.')
                                </label>
                                <div class="controls">
                                    <input type="text" name="contact_number_'.$language->language_code.'" id="contact_number_'.$language->language_code.'" data-required="1" class="span8 " />
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
                                    <input type="email" name="email" id="email" data-required="1" class="span8 " />
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">
                                    Office Join Date
                                </label>
                                <div class="controls">
                                    <input type="text" name="office_join_date" id="join_date" data-required="1" class="span8 date-picker" />
                                </div>
                            </div>

                            <div class="control-group">
                                @include('admin.photo_studio.controls', ['photoStudioType' => 'staff'])
                            </div>

                            <div class="control-group">
                                <label class="control-label">
                                    Status
                                </label>
                                <div class="controls">
                                    <select name="is_active" data-placeholder="Select the status" class="chosen-with-diselect span8" tabindex="-1" id="status">
                                        <option value="0">
                                            Unpublished
                                        </option>
                                        <option value="1" selected="selected">
                                            Published
                                        </option>
                                    </select>
                                </div>
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
<!--
    END PAGE CONTAINER
-->
</div>
<!--
BEGIN PAGE
-->
</div>
</div>
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
      $("#addform").validate({
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
