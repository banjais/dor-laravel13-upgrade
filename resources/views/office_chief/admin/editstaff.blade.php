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
                    Office Chief
                    <small>
                        Edit Office Chief.
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
                            Office Chief Management
                        </a>
                        <span class="divider">
                            &nbsp;
                        </span>
                    </li>
                    <li>
                        <a href="#">
                            Edit Office Chief
                        </a>
                        <span class="divider-last">
                            &nbsp;
                        </span>
                    </li>
                    @if($addAccess)
                    <a href="{{ route('admin.office_chiefs.add') }}">
                        <button class="btn  btn-primary top-button pull-right ">
                            <i class="icon-plus">
                            </i>
                            Add Office Chief
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
                                Edit office chief Form
                            </h4>
                        </div>
                        <div class="widget-body form">
                            <div id="msgdisp" class="error">
                                <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                                </ul>
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
                                        Chief Type *
                                    </label>
                                    <div class="controls">
                                        <select name="chief_type" data-placeholder="Select chief type" class="chosen-with-diselect span8" tabindex="-1" id="designation_id">
                                            <option>Select Chief Type</option>
                                            @foreach($designations as $designation)
                                            <option value="{{ $designation }}" @if($designation == $staff->chief_type) selected="selected" @endif>{{ $designation }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">
                                        Service Period (In AD) *
                                    </label>
                                    <div class="controls">
                                        <input type="text" name="date_ad" class="span8" tabindex="-1"
                                            value="{{ old('date_ad', $staff->date_ad) }}"
                                            placeholder="e.g. 1961 - 1969" />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">
                                        Service Period (In BS) *
                                    </label>
                                    <div class="controls">
                                        <input type="text" name="date_bs" class="span8" tabindex="-1"
                                            value="{{ old('date_bs', $staff->date_bs) }}"
                                            placeholder="e.g. 2018 - 2026"/>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Current Photo</label>
                                    <div class="controls">
                                        <div class="thumbnail" style="max-width:200px;">
                                            @php
                                                $currentPhotoExtension = strtolower(pathinfo($staff->photo ?? '', PATHINFO_EXTENSION));
                                                $currentPhotoSafe = $staff->photo
                                                    && basename($staff->photo) === $staff->photo
                                                    && in_array($currentPhotoExtension, ['jpg', 'jpeg', 'png', 'gif', 'bmp'], true);
                                            @endphp

                                            @if($currentPhotoSafe)
                                                <img
                                                    src="{{ asset('uploads/office_chiefs/' . rawurlencode($staff->photo)) }}"
                                                    alt="Current Office Chief photo"
                                                    width="200"
                                                    height="180"
                                                    style="object-fit:contain;"
                                                >
                                            @else
                                                <img
                                                    src="{{ asset('assets/admin/img/no-image.png') }}"
                                                    alt="No current photo"
                                                    width="200"
                                                    height="180"
                                                >
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @include('admin.photo_studio.controls', [
                                    'photoStudioType' => 'office_chief',
                                    'existingPhotoId' => $staff->photo ? $staff->id : null
                                ])

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
                                    <a href="{{ $abs_site_url }}admin/office_chiefs">
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
            chief_type:"required",
            // date_ad:"required",
            // date_bs:"required",
        <?php
            foreach($languages as $language){
                echo 'full_name_'.$language->language_code.': "required",';
            }
        ?>

        },
        messages: {
            chief_type:"Please select the chief type",
            date_ad:"Service date in AD is required",
            date_bs:"Service date in BS is required",
          <?php
          foreach($languages as $language){
          echo 'full_name_'.$language->language_code.': "Please enter the full name in '.$language->language_name.'",';
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
