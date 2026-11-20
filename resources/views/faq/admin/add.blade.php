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
                        Faq
                        <small>Add faq.</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.faqs') }}">Faq Management</a> <span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>Add Faq<span class="divider-last">&nbsp;</span></li>
                    </ul>

                </div>
            </div>


            <div id="page">
                <!--<button class="btn btn-large btn-primary "><i class="icon-plus"></i> Create Applicants</button>-->

                <div class="row-fluid">
                    <div class="span12">

                        <div class="widget">
                            <div class="widget-title">
                                <h4><i class="icon-reorder"></i>Add Faq Form</h4>

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
                                            <label class="control-label">Faq Question ( {{ $language->language_name }} )
                                                * </label>
                                            <div class="controls">
                                                <input type="text" name="faq_question_{{ $language->id }}"
                                                    id="faq_question_{{ $language->id }}" data-required="1"
                                                    class="span8"
                                                    value="{{ old('faq_question_' . $language->id) }}" />
                                            </div>
                                        </div>
                                    @endforeach

                                    @foreach ($languages as $language)
                                        <div class="control-group">
                                            <label class="control-label">Faq Answer ( {{ $language->language_name }} ) *
                                            </label>
                                            <div class="controls">
                                                <textarea class="m-wrap span12 ckeditor" rows="20" cols="20"
                                                    name="faq_answer_{{ $language->id }}">
            {{ old('faq_answer_' . $language->id) }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach

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
                                        <a href="{{ route('admin.faqs') }}"> <button type="button"
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
        $('#faqs').addClass('active');

        (function($, W, D) {
            var JQUERY4U = {};

            JQUERY4U.UTIL = {
                setupFormValidation: function() {
                    //form validation rules
                    $("#addform").validate({
                        rules: {
                            <?php
                            foreach ($languages as $language) {
                                echo 'faq_question_' . $language->id . ': "required",';
                                echo 'faq_answer_' . $language->id . ': "required",';
                            }
                            ?>
                        },
                        messages: {
                            <?php
                            foreach ($languages as $language) {
                                echo 'faq_question_' . $language->id . ': "Please enter faq question in ' . $language->language_name . '.",';
                                echo 'faq_answer_' . $language->id . ': "Please enter faq answer in ' . $language->language_name . '.",';
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
@endsection

@stop
