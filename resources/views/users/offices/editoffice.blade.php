@extends('layout.admin.masters')
@section('content')
    @php
    use App\Models\Office;
    use App\Models\Zone;
    // dd($details);
    @endphp
    <style>
        #sample_1_wrapper .row-fluid div {
            position: relative;
            width: inherit;
        }

        #sample_1_wrapper .row-fluid div #sample_1_filter {
            position: relative;
            width: inherit;
        }


        #sample_1_wrapper .row-fluid div #sample_1_filter label {

            position: relative;
            width: inherit;
        }

        #sample_1_wrapper .row-fluid div #sample_1_filter label input {
            display: inline-block;
            width: 80%;
            margin-left: 3%;
        }

        #sample_1_wrapper .row-fluid div .dataTables_paginate.paging_bootstrap.pagination {
            float: right !important;
            width: auto;
        }

        #sample_1_wrapper .row-fluid div #sample_1_info {
            text-align: right;
            margin: 0;
            padding-top: 5px;
        }

    </style>

    <div id="ajax-notifier-background">
        <img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
    </div>

    <div id="main-content">

        <div class="container-fluid">

            <div class="row-fluid">
                <div class="span12">




                    <h3 class="page-title">
                        {{ $isOfficeAdmin ? 'Office Profile' : 'Office' }}
                        <small>{{ $isOfficeAdmin ? 'Edit your office profile.' : 'Edit Office.' }}</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        @unless($isOfficeAdmin)
                        <li>
                            <a href="{{ $abs_site_url }}admin/offices">Office Management</a> <span
                                class="divider">&nbsp;</span>
                        </li>
                        @endunless
                        <li><a href="#">Edit Office</a><span class="divider-last">&nbsp;</span></li>
                        @unless($isOfficeAdmin)
                        <a href="{{ $abs_site_url }}admin/office/add">
                            <button class="btn btn-primary top-button pull-right"><i class="icon-plus"></i> Add
                                Office</button>
                        </a>
                        @endunless

                    </ul>

                </div>
            </div>


            <div id="page">
                <!--<button class="btn btn-large btn-primary "><i class="icon-plus"></i> Create Applicants</button>-->

                <div class="row-fluid">
                    <div class="span12">

                        <div class="widget">
                            <div class="widget-title">
                                <h4><i class="icon-reorder"></i> Edit users Form</h4>

                            </div>
                            <div class="widget-body form">
                                <div id="msgdisp">
                                    @include('layout.admin.alert')
                                </div>

                                <form action="" class="form-horizontal" name="editform" id="editform" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div id="form-body">
                                        <input type="hidden" name="id" id="id" value="{{ $id }}" />
                                        @unless($isOfficeAdmin)
                                        <div class="control-group">
                                            <label class="control-label">Parent</label>
                                            <div class="controls">
                                                {!! Office::content_list_for_Regional(2, 0, 0, $parent_id) !!}
                                            </div>
                                        </div>
                                        @endunless

                                        <?php
                                        foreach ($details as $detail) {
                                            echo '
                                                                                                                        												 <input type="hidden" name="id_' .
                                                $detail->language_code .
                                                '" id="id_' .
                                                $detail->language_code .
                                                '" data-required="1" class="span12" value="' .
                                                $detail->detailid .
                                                '" />
                                                                                                                        											';
                                        }

                                        foreach ($details as $detail) {
                                            echo '<div class="control-group">
                                                                                                                        										   <label class="control-label">Office  Name (' .
                                                $detail->language_name .
                                                ') </label>
                                                                                                                        											<div class="controls">
                                                                                                                        												 <input type="text" name="name_' .
                                                $detail->language_code .
                                                '" id="name_' .
                                                $detail->language_code .
                                                '" data-required="1" class="span12" value="' .
                                                $detail->office_name .
                                                '" />
                                                                                                                        											 </div>
                                                                                                                        										   </div>';
                                        }

                                        foreach ($details as $detail) {
                                            echo '<div class="control-group">
                                                                                                                        										   <label class="control-label">Address(' .
                                                $detail->language_name .
                                                ')</label>
                                                                                                                        										   <div class="controls">
                                                                                                                        												 <input type="text" name="office_address_' .
                                                $detail->language_code .
                                                '" id="office_address_' .
                                                $detail->language_code .
                                                '" data-required="1" class="span12 " value="' .
                                                $detail->office_address .
                                                '" />
                                                                                                                        											 </div>
                                                                                                                        										   </div>';
                                        }
                                        foreach ($details as $detail) {
                                            echo '<div class="control-group">
                                                                                                                        										   <label class="control-label">Primary Contact Number(' .
                                                $detail->language_name .
                                                ')</label>
                                                                                                                        											<div class="controls">
                                                                                                                        												 <input type="text" name="primary_contact_' .
                                                $detail->language_code .
                                                '" id="primary_contact_' .
                                                $detail->language_code .
                                                '" data-required="1" class="span12 " value="' .
                                                $detail->office_phone_number1 .
                                                '" />
                                                                                                                        											 </div>
                                                                                                                        										   </div>';
                                        }

                                        foreach ($details as $detail) {
                                            echo '<div class="control-group">
                                                                                                                        										   <label class="control-label">Secondary Contact Number(' .
                                                $detail->language_name .
                                                ')</label>
                                                                                                                        											<div class="controls">
                                                                                                                        												 <input type="text" name="secondary_contact_' .
                                                $detail->language_code .
                                                '" id="secondary_contact_' .
                                                $detail->language_code .
                                                '" data-required="1" class="span12 " value="' .
                                                $detail->office_phone_number2 .
                                                '" />
                                                                                                                        											 </div>
                                                                                                                        										   </div>';
                                        }
                                        ?>
                                        <div class="control-group">
                                            <label class="control-label">Email Address</label>
                                            <div class="controls">
                                                <input type="email" name="email" id="email"
                                                    class="span12" value="{{ old('email', $email) }}" />
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label">Alternative Email</label>
                                            <div class="controls">
                                                <input type="email" name="alternative_email" id="alternative_email"
                                                    class="span12" value="{{ old('alternative_email', $alternative_email) }}" />
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label">Facebook Page</label>
                                            <div class="controls">
                                                <input type="url" name="facebook_page" id="facebook_page"
                                                    class="span12" value="{{ old('facebook_page', $facebook_page) }}"
                                                    placeholder="https://www.facebook.com/your-page" />
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label">Instagram Profile</label>
                                            <div class="controls">
                                                <input type="url" name="instagram_page" id="instagram_page"
                                                    class="span12" value="{{ old('instagram_page', $instagram_page) }}"
                                                    placeholder="https://www.instagram.com/your-profile" />
                                            </div>
                                        </div>

                                        @unless($isOfficeAdmin)
                                        <div class="control-group">
                                            <label class="control-label">Sub domain</label>
                                            <div class="controls">
                                                <input type="text" name="office_subdomain" id="office_subdomain"
                                                    data-required="1" class="span12 "
                                                    value="{{ $office_subdomain }}" />
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label">Zone</label>
                                            <div class="controls">
                                                <select name="zone_id" data-placeholder="Select the zone"
                                                    class="chosen-with-diselect span8" tabindex="-1" id="zone_id">
                                                    <option value="">Select Zone</option>
                                                    <?php
                                                    $zones = Zone::getActiveZones();
                                                    foreach ($zones as $zone) {
                                                        $selected = $zone->id == $zone_id ? 'selected="selected"' : '';
                                                        echo '<option value="' . $zone->id . '" ' . $selected . '>' . $zone->zone_name . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        @endunless

                                        {{-- <fieldset class="gllpLatlonPicker">
                                            <div class="control-group">
                                                <label class="control-label">Search Map : </label>
                                                <div class="controls">
                                                    <input type="text" class="gllpSearchField">
                                                    <input type="button" class="gllpSearchButton" value="search">
                                                    <br /><br />
                                                    <div class="gllpMap">Google Maps</div>
                                                    <br />
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">Latitude : </label>
                                                <div class="controls">
                                                    <input type="text" name="office_latitude" id="office_latitude"
                                                        data-required="1" class="gllpLatitude"
                                                        value="{{ $office_latitude }}" />
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">Longitude: </label>
                                                <div class="controls">
                                                    <input type="text" name="office_longitude" id="office_longitude"
                                                        data-required="1" class="gllpLongitude"
                                                        value="{{ $office_longitude }}" />
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">Zoom: </label>
                                                <div class="controls">
                                                    <input type="text" name="office_zoom" data-required="1" id=""
                                                        class="gllpZoom" value="{{ $office_zoom }}" />
                                                    <input type="button" class="gllpUpdateButton" value="update map">
                                                </div>
                                            </div>
                                        </fieldset> --}}

                                        @if($isOfficeAdmin)
                                        <div class="control-group">
                                            <label class="control-label">Google Maps Share Link</label>
                                            <div class="controls">
                                                <input type="url" name="google_maps_link" id="google_maps_link"
                                                    class="span12"
                                                    value="{{ old('google_maps_link', $google_maps_link) }}"
                                                    placeholder="Paste Google Maps Share link here" />
                                                <span class="help-block">
                                                    Google Maps मा कार्यालयको स्थान खोलेर Share → Copy link गर्नुहोस् र यहाँ Paste गर्नुहोस्। खाली छोडेमा हालको Map परिवर्तन हुँदैन।
                                                </span>
                                            </div>
                                        </div>
                                        @else
                                        <div class="control-group">
                                            <label class="control-label">Google map embed code (with iframe): </label>
                                            <div class="controls">
                                                <textarea style="width:100%" rows="5" type="text" name="google_map_embed" id="google_map_embed" data-required="1"
                                                    class="gllpZoom" value="7">{{ $google_map_embed }}</textarea>
                                            </div>
                                        </div>

                                        @endif

                                        <br />

                                        @unless($isOfficeAdmin)
                                        <?php
                                        $array = explode(',', $office_type);

                                        ?>
                                        <div class="control-group">
                                            <label class="control-label">Office Type</label>
                                            <div class="controls">
                                                <input type="checkbox" name="office_type[]" id="regional" value="2"
                                                    {{ in_array(2, $array) ? 'checked="checked"' : '' }} />&nbsp;Regional&nbsp;
                                                <input type="checkbox" name="office_type[]" id="division" value="3"
                                                    {{ in_array(3, $array) ? 'checked="checked"' : '' }} />&nbsp;Division&nbsp;
                                                <input type="checkbox" name="office_type[]" id="division" value="4"
                                                    {{ in_array(4, $array) ? 'checked="checked"' : '' }} />&nbsp;Heavy
                                                Equipment Division &nbsp;
                                                <input type="checkbox" name="office_type[]" id="division" value="5"
                                                    {{ in_array(5, $array) ? 'checked="checked"' : '' }} />&nbsp;Mechanical
                                                office&nbsp;
                                                <input type="checkbox" name="office_type[]" id="division" value="6"
                                                    {{ in_array(6, $array) ? 'checked="checked"' : '' }} />&nbsp;Main
                                                Project office 1&nbsp;
                                                <input type="checkbox" name="office_type[]" id="division" value="7"
                                                    {{ in_array(7, $array) ? 'checked="checked"' : '' }} />&nbsp;Main
                                                Project office 2&nbsp;
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Order</label>
                                            <div class="controls">
                                                <input type="number" name="order_menu" value="{{ $order_menu }}" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Status</label>
                                            <div class="controls">
                                                <select name="status" data-placeholder="Select the status"
                                                    class="chosen-with-diselect span12" tabindex="-1" id="status">
                                                    <option value="0" {{ $unpublished }}>Unpublished</option>
                                                    <option value="1" {{ $published }}>Published</option>
                                                </select>
                                            </div>
                                        </div>
                                        @endunless
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i>
                                            Submit</button>
                                        @unless($isOfficeAdmin)
                                        <a href="{{ $abs_site_url }}admin/users"> <button type="button"
                                                class="btn btn-danger"><i class="icon-remove-sign"></i> Cancel</button></a>
                                        @endunless
                                    </div>
                                </form>

                            </div>
                        </div>

                    </div>

                </div>


            </div>

        </div>

    </div>

    </div>
    <!-- END CONTAINER -->
    </div>
    </div>

@section('footer_js')
    <script type="text/javascript">
        $('li').removeClass('active');
        $('#usersmgmt').addClass('active');
        $('#offices').addClass('active');
        /**
         * Basic jQuery Validation Form Demo Code
         * Copyright Sam Deering 2012
         * Licence: http://www.jquery4u.com/license/
         */

        (function($, W, D) {
            var JQUERY4U = {};

            JQUERY4U.UTIL = {
                setupFormValidation: function() {
                    //form validation rules
                    $("#editform").validate({
                        rules: {
                            <?php

                            foreach ($languages as $language) {
                                echo 'name_' . $language->language_code . ': "required",';
                                echo 'office_address_' . $language->language_code . ': "required",';
                                echo 'primary_contact_' . $language->language_code . ':"required",	';
                            }
                            ?>

                            email: {
                                @unless($isOfficeAdmin)
                                required: true,
                                @endunless
                                email: true,
                            },
                            @unless($isOfficeAdmin)
                            office_subdomain: {
                                required: true
                            },
                            @endunless
                        },
                        messages: {
                            <?php
                            foreach ($languages as $language) {
                                echo 'name_' . $language->language_code . ': "Please eneter the name of office in ' . $language->language_name . '",';
                                echo 'office_address_' . $language->language_code . ': "Please enter address of the office in ' . $language->language_name . '",';
                                echo 'primary_contact_' . $language->language_code . ':"Please enter the primary contact number in  ' . $language->language_name . '",	';
                            }
                            ?>
                            email: {
                                required: "Please enter a valid email address.",
                                email: "Please enter the valid email.",
                            },
                            office_subdomain: "Please enter office sub domain.",
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
    {{-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCibdBtd_WXNGP7yOF4oYnTtw_mEj7_OVg&sensor=false"></script> --}}






    <!-- CSS and JS for our code -->
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/googlemap/jquery-gmaps-latlon-picker.css') }}" />
    <script src="{{ asset('assets/admin/googlemap/jquery-gmaps-latlon-picker.js') }}"></script> --}}

@endsection
@stop
