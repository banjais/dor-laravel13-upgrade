<script src="{{ asset('assets/js/highmaps.js') }}"></script>
<script src="{{ asset('assets/js/exporting.js') }}"></script>
<script src="{{ asset('assets/js/np-all.js') }}"></script>
<script>
$(document).ready(function(){
    var data = getdata();   
    function getdata(){
        var data = new Array();
        $.ajax({
            url: sitePath + "get_zones_data_for_map",
            type: 'POST',
            async: false,
            success: function(response){
                returnvalue = JSON.parse(response);         
                var returdata = returnvalue.data;
                $.each(returdata, function(key, value){                 
                    data.push(value);
                });
            },
        });
        return data;        
    }
    /* maps container*/
    $('#container').highcharts('Map', {
        mapNavigation: {
            enabled: true,
            buttonOptions: {
                verticalAlign: 'bottom'
            }
        },

        colorAxis: {
            min: 0
        },

        series : [{
           data : data,
           mapData: Highcharts.maps['countries/np/np-all'],
            joinBy: 'hc-key',
            name: 'No. of DOR Office',
            states: {
                hover: {
                    color: '#BADA55'
                }
            },
            dataLabels: {
                enabled: true,
                format: '{point.name}'
            },          
        }],
        plotOptions: {
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            //alert(this.options.url);
                            location.href = sitePath + subdomain +'/zone/'+ this.options.url;                           
                        }
                    }
                }
            }
        },      
    });

    $('.date-picker').datepicker({
        format: 'yyyy-mm-dd',
    }); 
});
    
</script>
<script src="{{ asset('assets/js/wow.min.js') }}"></script>
<script src="{{ asset('assets/js/slidebars.js') }}"></script>
<script src="{{ asset('assets/js/jquery.bxslider.min.js') }}"></script>
<script src="{{ asset('assets/js/holder.js') }}"></script>
<script src="{{ asset('assets/js/buttons.js') }}"></script>
<script src="{{ asset('assets/js/styleswitcher.js') }}"></script>
<script src="{{ asset('assets/js/jquery.mixitup.min.js') }}"></script>
<script src="{{ asset('assets/js/circles.min.js') }}"></script>

<script src="{{ asset('assets/js/datepicker.js') }}"></script>

<!-- Add fancyBox main JS --> 
<script type="text/javascript" src="{{ asset('assets/js/jquery.fancybox.js') }}?v=2.1.5"></script>

<!-- Syntax highlighter -->
<script src="{{ asset('assets/js/syntaxhighlighter/shCore.js') }}"></script>
<script src="{{ asset('assets/js/syntaxhighlighter/shBrushXml.js') }}"></script>
<script src="{{ asset('assets/js/syntaxhighlighter/shBrushJScript.js') }}"></script>

<script src="{{ asset('assets/js/app.js') }}"></script>

<script type="text/javascript">
	var sitePath = '<?php echo $abs_site_url ?>';
	var subdomain = '<?php echo $subdomain ?>';
	var langId = '<?php echo Session::get('language_id') ?>';
</script>

<script src="{{ asset('assets/js/dropdown-script.js') }}"></script>
<script src="{{ asset('assets/js/index.js') }}"></script>
<script src="{{ asset('assets/js/onload_client.js') }}"></script>


<link href="{{ asset('assets/css/slidebars.css') }}" rel="stylesheet" media="screen">
<link href="{{ asset('assets/css/time.css') }}" rel="stylesheet" media="screen">
<script type="text/javascript">
$(document).ready(function() {
// Making 2 variable month and day
var monthNames = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ]; 
var dayNames= ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]

// make single object
var newDate = new Date();
// make current time
newDate.setDate(newDate.getDate());
// setting date and time
$('#Date').html(dayNames[newDate.getDay()] + " " + newDate.getDate() + ' ' + monthNames[newDate.getMonth()] + ' ' + newDate.getFullYear());

setInterval( function() {
// Create a newDate() object and extract the seconds of the current time on the visitor's
var seconds = new Date().getSeconds();
// Add a leading zero to seconds value
$("#sec").html(( seconds < 10 ? "0" : "" ) + seconds);
},1000);

setInterval( function() {
// Create a newDate() object and extract the minutes of the current time on the visitor's
var minutes = new Date().getMinutes();
// Add a leading zero to the minutes value
$("#min").html(( minutes < 10 ? "0" : "" ) + minutes);
},1000);

setInterval( function() {
// Create a newDate() object and extract the hours of the current time on the visitor's
var hours = new Date().getHours();
// Add a leading zero to the hours value
$("#hours").html(( hours < 10 ? "0" : "" ) + hours);
}, 1000); 
});
</script>
