@extends('layout.admin.masters')
@section('content')
    @php
    use App\Models\Office;
    use App\Models\Zone;
    @endphp
    <div id="ajax-notifier-background">
        <img src="{{ asset('assets/admin/pre-loader/Rounded%20blocks.gif') }}" alt="Fancy pants">
    </div>
    <div id="main-content">
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span12">
                    <h3 class="page-title">
                        Offices
                        <small>Add office.</small>
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a><span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.offices') }}">Office Management</a> <span
                                class="divider">&nbsp;</span>
                        </li>
                        <li>Add Offices<span class="divider-last">&nbsp;</span></li>

                    </ul>
                </div>
            </div>
            <div id="page">

                <div class="row-fluid">
                    <div class="span12">
                        <div class="widget">
                            <div class="widget-title">
                                <h4><i class="icon-reorder"></i>Add Office Form</h4>

                            </div>
                            <div class="widget-body form">
                                <div id="msgdisp1">
                                    @include('layout.admin.alert')
                                </div>
                                <form action="" class="form-horizontal" name="addform" id="addform" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div id="form-body">
                                        <div class="control-group">
                                            <label class="control-label">Regional</label>
                                            <div class="controls">
                                                {!! Office::content_list_for_Regional(2, 0, 0, '') !!}
                                            </div>
                                        </div>
                                        <?php
                                        foreach ($languages as $language) {
                                            echo '<div class="control-group">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                										   <label class="control-label">Office  Name (' .
                                                $language->language_name .
                                                ') *</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                											<div class="controls">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                												 <input type="text" name="name_' .
                                                $language->language_code .
                                                '" id="name_' .
                                                $language->language_code .
                                                '" data-required="1" class="span8" value="' .
                                                old('name_' . $language->language_code) .
                                                '" />
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                											 </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                										   </div>';
                                        }

                                        foreach ($languages as $language) {
                                            echo '<div class="control-group">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                										   <label class="control-label">Address(' .
                                                $language->language_name .
                                                ') *</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                										   <div class="controls">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                												 <input type="text" name="office_address_' .
                                                $language->language_code .
                                                '" id="office_address_' .
                                                $language->language_code .
                                                '" data-required="1" class="span8" value="' .
                                                old('office_address_' . $language->language_code) .
                                                '"/>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                											 </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                										   </div>';
                                        }
                                        foreach ($languages as $language) {
                                            echo '<div class="control-group">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                										   <label class="control-label">Primary Contact Number(' .
                                                $language->language_name .
                                                ') *</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                											<div class="controls">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                												 <input type="text" name="primary_contact_' .
                                                $language->language_code .
                                                '" id="primary_contact_' .
                                                $language->language_code .
                                                '" data-required="1" class="span8" value="' .
                                                old('primary_contact_' . $language->language_code) .
                                                '"/>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                											 </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                										   </div>';
                                        }

                                        foreach ($languages as $language) {
                                            echo '<div class="control-group">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                										   <label class="control-label">Secondary Contact Number(' .
                                                $language->language_name .
                                                ')</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                											<div class="controls">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                												 <input type="text" name="secondary_contact_' .
                                                $language->language_code .
                                                '" id="secondary_contact_' .
                                                $language->language_code .
                                                '" data-required="1" class="span8" value="' .
                                                old('secondary_contact_' . $language->language_code) .
                                                '"/>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                											 </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                										   </div>';
                                        }

                                        ?>

                                        <div class="control-group">
                                            <label class="control-label">Email Address *</label>
                                            <div class="controls">
                                                <input type="email" name="email" id="email" data-required="1"
                                                    class="span8" value="{{ old('email') }}" />
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label">Alternative Email</label>
                                            <div class="controls">
                                                <input type="email" name="alternative_email" id="alternative_email"
                                                    class="span8" value="{{ old('alternative_email') }}" />
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label">Sub domain *</label>
                                            <div class="controls">
                                                <input type="text" name="office_subdomain" id="office_subdomain"
                                                    data-required="1" class="span8"
                                                    value="{{ old('office_subdomain') }}" />
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
                                                        echo '<option value="' . $zone->id . '">' . $zone->zone_name . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- <fieldset class="gllpLatlonPicker"> --}}
                                        {{-- <div class="control-group">
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
                                                        data-required="1" class="gllpLatitude" value="28" />
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">Longitude: </label>
                                                <div class="controls">
                                                    <input type="text" name="office_longitude" id="office_longitude"
                                                        data-required="1" class="gllpLongitude" value="84" />
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">Zoom: </label>
                                                <div class="controls">
                                                    <input type="text" name="office_zoom" id="office_zoom" data-required="1"
                                                        class="gllpZoom" value="7" />
                                                    <input type="button" class="gllpUpdateButton" value="update map">
                                                </div>
                                            </div> --}}
                                        {{-- </fieldset> --}}

                                        <div class="control-group">
                                            <label class="control-label">Google map embed code (with iframe): </label>
                                            <div class="controls">
                                                <textarea type="text" style="width:100%" rows="5" name="google_map_embed" id="google_map_embed" data-required="1" class="gllpZoom"
                                                    value="7"></textarea>
                                            </div>
                                        </div>

                                        <br />

                                        <div class="control-group">
                                            <label class="control-label">Office Type</label>
                                            <div class="controls">
                                                <input type="checkbox" name="office_type[]" value="2" />&nbsp;Regional&nbsp;
                                                <input type="checkbox" name="office_type[]" value="3" />&nbsp;Division&nbsp;
                                                <input type="checkbox" name="office_type[]" value="4" />&nbsp;Heavy
                                                Equipment Division&nbsp;
                                                <input type="checkbox" name="office_type[]" value="5" />&nbsp;Mechanical
                                                office&nbsp;
                                                <input type="checkbox" name="office_type[]" id="division"
                                                    value="6" />&nbsp;Main
                                                Project office 1&nbsp;
                                                <input type="checkbox" name="office_type[]" id="division"
                                                    value="7" />&nbsp;Main
                                                Project office 2&nbsp;
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label">Order</label>
                                            <div class="controls">
                                                <input type="number" name="order_menu" value="{{ old('order_menu') }}" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Status</label>
                                            <div class="controls">
                                                <select name="status" data-placeholder="Select the status"
                                                    class="chosen-with-diselect span8" tabindex="-1" id="status">
                                                    <option value="0">Unpublished</option>
                                                    <option value="1" selected="selected">Published</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary"><i class="icon-ok-sign"></i>
                                            Submit</button>
                                        <a href="{{ route('admin.offices') }}"> <button type="button"
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

        $('li').removeClass('active');
        $('#usersmgmt').addClass('active');
        $('#officemgmt').addClass('active');

        (function($, W, D) {
            var JQUERY4U = {};
            JQUERY4U.UTIL = {
                setupFormValidation: function() {
                    $("#addform").validate({
                        rules: {
                            <?php
                            foreach ($languages as $language) {
                                echo 'name_' . $language->language_code . ': "required",';
                                echo 'office_address_' . $language->language_code . ': "required",';
                                echo 'primary_contact_' . $language->language_code . ':"required",	';
                            }
                            ?>
                            email: {
                                email: true
                            },
                            office_subdomain: {
                                required: true
                            },
                        },
                        messages: {
                            <?php
                            foreach ($languages as $language) {
                                echo 'name_' . $language->language_code . ': "Please enter the name of office in ' . $language->language_name . '",';
                                echo 'office_address_' . $language->language_code . ': "Please enter address of the office in ' . $language->language_name . '",';
                                echo 'primary_contact_' . $language->language_code . ':"Please enter the primary contact number in  ' . $language->language_name . '",	';
                            }
                            ?>
                            email: {
                                email: 'Please enter the valid email address.',
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
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCibdBtd_WXNGP7yOF4oYnTtw_mEj7_OVg&sensor=false"></script>
    <!-- CSS and JS for our code -->
    <link rel="stylesheet" type="text/css"
        href="{{ $abs_site_url }}assets/admin/googlemap/jquery-gmaps-latlon-picker.css" />
    <script src="{{ $abs_site_url }}assets/admin/googlemap/jquery-gmaps-latlon-picker.js"></script>

@endsection
@stop
