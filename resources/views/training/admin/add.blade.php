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
                        Training
                        <small>Add training.</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.trainings') }}">Training Management</a> <span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>Add Training<span class="divider-last">&nbsp;</span></li>
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
                                <h4><i class="icon-reorder"></i>Add Training Form</h4>

                            </div>
                            <div class="widget-body form">
                                <div id="msgdisp">
                                    @include('layout.admin.alert')
                                </div>

                                <form action="" class="form-horizontal" name="addform" id="addform" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">Training Heading (
                                                {{ $language->language_name }} ) * </label>
                                            <div class="controls">
                                                <input type="text" name="training_heading_{{ $language->id }}"
                                                    id="training_heading_{{ $language->id }}" data-required="1"
                                                    class="span8"
                                                    value="{{ old('training_heading_' . $language->id) }}" />
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="control-group">
                                        <label class="control-label">Training Url *</label>
                                        <div class="controls">
                                            <input type="text" name="training_url" id="training_url" data-required="1"
                                                class="span8" value="{{ old('training_url') }}" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Training Start Date * </label>
                                        <div class="controls">
                                            <input type="text" name="training_start_date" data-required="1"
                                                class="span8 date-picker" value="{{ old('training_start_date') }}" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Training End Date * </label>
                                        <div class="controls">
                                            <input type="text" name="training_end_date" data-required="1"
                                                class="span8 date-picker" value="{{ old('training_end_date') }}" />
                                        </div>
                                    </div>

                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">Training Description (
                                                {{ $language->language_name }} ) * </label>
                                            <div class="controls">
                                                <textarea class="m-wrap span12 ckeditor" rows="10" cols="20"
                                                    name="training_description_{{ $language->id }}">
            {{ old('training_description_' . $language->id) }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach

                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">Training Contact Person (
                                                {{ $language->language_name }} ) </label>
                                            <div class="controls">
                                                <input type="text" name="training_contact_person_{{ $language->id }}"
                                                    data-required="1" class="span8"
                                                    value="{{ old('training_contact_person_' . $language->id) }}" />
                                            </div>
                                        </div>
                                    @endforeach

                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">Training Contact Number (
                                                {{ $language->language_name }} ) </label>
                                            <div class="controls">
                                                <input type="text" name="training_contact_number_{{ $language->id }}"
                                                    data-required="1" class="span8"
                                                    value="{{ old('training_contact_number_' . $language->id) }}" />
                                            </div>
                                        </div>
                                    @endforeach

                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">Training Remarks (
                                                {{ $language->language_name }} ) * </label>
                                            <div class="controls">
                                                <textarea class="span8"
                                                    name="training_remarks_{{ $language->id }}"
                                                    id="training_remarks">{{ old('training_remarks_' . $language->id) }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach

                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">Training Page Title (
                                                {{ $language->language_name }} ) * </label>
                                            <div class="controls">
                                                <input type="text" name="training_page_title_{{ $language->id }}"
                                                    data-required="1" class="span8"
                                                    value="{{ old('training_page_title_' . $language->id) }}" />
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="control-group">
                                        <label class="control-label">Meta Tags </label>
                                        <div class="controls">
                                            <textarea class="span8" name="training_meta_tags"
                                                id="training_meta_tags">{{ old('training_meta_tags') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Meta Description </label>
                                        <div class="controls">
                                            <textarea class="span8" name="training_meta_description"
                                                id="training_meta_description">{{ old('training_meta_description') }}</textarea>
                                        </div>
                                    </div>
                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">Minimum Qulification (
                                                {{ $language->language_name }} )</label>
                                            <div class="controls">
                                                <textarea class="span8"
                                                    name="minimum_qulification_{{ $language->id }}">{{ old('minimum_qulification_' . $language->id) }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach


                                    <div class="control-group">
                                        <label class="control-label">Number Of Trainees</label>
                                        <div class="controls">
                                            <input type="number" name="number_of_traniees" data-required="1"
                                                class="span8" value="{{ old('number_of_traniees') }}" />
                                        </div>
                                    </div>


                                    <div class="control-group">
                                        <label class="control-label">Status</label>
                                        <div class="controls">
                                            <select name="is_active" data-placeholder="Select the status"
                                                class="chosen-with-diselect span8" tabindex="-1" id="status">
                                                <option value="0">Unpublished</option>
                                                <option value="1">Published</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div id="date-validation" style=" margin-left:0px;color:red;"></div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i>
                                            Submit</button>
                                        <a href="{{ route('admin.trainings') }}"> <button type="button"
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
        /**
         * Basic jQuery Validation Form Demo Code
         * Copyright Sam Deering 2012
         * Licence: http://www.jquery4u.com/license/
         */

        $(document).ready(function() {
            $('li').removeClass('active');
            $('#misc').addClass('active');
            $('#trainings').addClass('active');
            $('#training_heading_2').blur(function() {
                var heading = $(this).val();
                var url = heading.trim().toLowerCase().replace(/[^a-z1-9]+/gi, '-');
                $('#training_url').val(url);
            });
        });

        (function($, W, D) {
            var JQUERY4U = {};

            JQUERY4U.UTIL = {
                setupFormValidation: function() {
                    //form validation rules
                    $("#addform").validate({
                        rules: {
                            training_url: "required",
                            training_start_date: "required",
                            training_end_date: "required",
                            <?php
                            foreach ($languages as $language) {
                                echo 'training_heading_' . $language->id . ': "required",';
                                echo 'training_description_' . $language->id . ': "required",';
                            }
                            ?>
                            attachment: {
                                extension: 'jpe?g|png'
                            }
                        },
                        messages: {
                            training_url: "Please enter url of the training.",
                            training_start_date: "Please enter start date of the training.",
                            training_end_date: "Please enter end date of the training.",
                            <?php
                            foreach ($languages as $language) {
                                echo 'training_heading_' . $language->id . ': "Please enter the training heading in ' . $language->language_name . '",';
                                echo 'training_description_' . $language->id . ': "Please enter the training description in ' . $language->language_name . '",';
                            }
                            ?>

                            attachment: {
                                extension: "Please select appropriate file. Only jpeg/jpg and png format are allowed."
                            }

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
