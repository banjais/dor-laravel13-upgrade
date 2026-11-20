@extends('layout.admin.masters')
@section('content')

    <div id="ajax-notifier-background">
        <img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
    </div>
    <div id="main-content">

        <div class="container-fluid">

            <div class="row-fluid">
                <div class="span12">




                    <h3 class="page-title">
                        Equipment
                        <small>Add equipment.</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.equipments') }}">Equipment Management</a> <span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>Add Equipment<span class="divider-last">&nbsp;</span></li>
                    </ul>

                </div>
            </div>


            <div id="page">
                <!--<button class="btn btn-large btn-primary "><i class="icon-plus"></i> Create Applicants</button>-->

                <div class="row-fluid">
                    <div class="span12">

                        <div class="widget">
                            <div class="widget-title">
                                <h4><i class="icon-reorder"></i>Add Equipment Form</h4>

                            </div>
                            <div class="widget-body form">
                                <div id="msgdisp">
                                    @include('layout.admin.alert')
                                </div>

                                <form action="" class="form-horizontal" name="addform" id="addform" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="control-group">
                                        <label class="control-label">Equipment Name </label>
                                        <div class="controls">
                                            <input type="text" name="equipment_name" data-required="1"
                                                class="span4" value="{{ old('equipment_name') }}" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Equipment Identification </label>
                                        <div class="controls">
                                            <input type="text" name="equipment_identification" data-required="1"
                                                class="span4"
                                                value="{{ old('equipment_identification') }}" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Category/Capacity </label>
                                        <div class="controls">
                                            <input type="text" name="equipment_capacity_category" data-required="1"
                                                class="span4"
                                                value="{{ old('equipment_capacity_category') }}" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Dor code </label>
                                        <div class="controls">
                                            <input type="text" name="equipment_dor_code" data-required="1"
                                                class="span4" value="{{ old('equipment_dor_code') }}" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Per Hour Rs. </label>
                                        <div class="controls">
                                            <input type="text" name="equipment_per_hour" data-required="1"
                                                class="span4" value="{{ old('equipment_per_hour') }}" />
                                        </div>
                                    </div>


                                    <div class="control-group">
                                        <label class="control-label">Status</label>
                                        <div class="controls">
                                            <select name="is_active" data-placeholder="Select the status"
                                                class="chosen-with-diselect span4" tabindex="-1" id="status">
                                                <option value="0">Unpublished</option>
                                                <option value="1">Published</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i>
                                            Submit</button>
                                        <a href="{{ route('admin.equipments') }}"> <button type="button"
                                                class="btn btn-danger "><i class="icon-remove-sign"></i> Cancel</button></a>
                                    </div>
                                </form>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>


@section('footer_js')
    <script type="text/javascript">
        $('li').removeClass('active');
        $('#misc').addClass('active');
        $('#equipments').addClass('active');

        (function($, W, D) {
            var JQUERY4U = {};

            JQUERY4U.UTIL = {
                setupFormValidation: function() {
                    //form validation rules
                    $("#addform").validate({
                        rules: {
                            equipment_name: "required",

                        },
                        messages: {
                            equipment_name: "Please enter equipment name.",
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
@endsection

@stop
