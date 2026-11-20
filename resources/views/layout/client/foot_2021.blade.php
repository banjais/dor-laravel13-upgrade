<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="{{ asset('assets/js_2021/jquery.min.js') }}?v={{ filemtime(public_path('assets/js_2021/jquery.min.js')) }}"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="{{ asset('assets/js_2021/bootstrap.bundle.min.js') }}?v={{ filemtime(public_path('assets/js_2021/bootstrap.bundle.min.js')) }}"></script>
<script src="{{ asset('assets/js_2021/acmeticker.min.js') }}?v={{ filemtime(public_path('assets/js_2021/acmeticker.min.js')) }}"></script>
<script src="{{ asset('assets/js_2021/slick.min.js') }}?v={{ filemtime(public_path('assets/js_2021/slick.min.js')) }}"></script>
<script src="{{ asset('assets/js_2021/script.js') }}?v={{ filemtime(public_path('assets/js_2021/script.js')) }}"></script>
<script type="text/javascript">
    var sitePath = '<?php echo $abs_site_url; ?>';
    var subdomain = '<?php echo $subdomain; ?>';
    var langId = "{{ Session::get('language_id') }}";
</script>
<script src="{{ asset('assets/js/onload_client.js') }}?v={{ filemtime(public_path('assets/js/onload_client.js')) }}"></script>
<script type="text/javascript">
    var is_changing_font = false;
    var is_changing_bandwidth = false;
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });
        // Making 2 variable month and day
        var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September",
            "October", "November", "December"
        ];
        var dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]

        // make single object
        var newDate = new Date();
        // make current time
        newDate.setDate(newDate.getDate());
        // setting date and time
        $('#Date').html(dayNames[newDate.getDay()] + " " + newDate.getDate() + ' ' + monthNames[newDate
            .getMonth()] + ' ' + newDate.getFullYear());

        setInterval(function() {
            // Create a newDate() object and extract the seconds of the current time on the visitor's
            var seconds = new Date().getSeconds();
            // Add a leading zero to seconds value
            $("#sec").html((seconds < 10 ? "0" : "") + seconds);
        }, 1000);

        setInterval(function() {
            // Create a newDate() object and extract the minutes of the current time on the visitor's
            var minutes = new Date().getMinutes();
            // Add a leading zero to the minutes value
            $("#min").html((minutes < 10 ? "0" : "") + minutes);
        }, 1000);

        setInterval(function() {
            // Create a newDate() object and extract the hours of the current time on the visitor's
            var hours = new Date().getHours();
            // Add a leading zero to the hours value
            $("#hours").html((hours < 10 ? "0" : "") + hours);
        }, 1000);

        // Language dropdown: preserve the existing server-side language endpoint.
        $('#headerLanguageSelect').on('change', function() {
            const language_id = $(this).val();
            $.ajax({
                data: 'language_id=' + language_id,
                url: '/change_language',
                type: 'POST',
                success: function() {
                    location.reload();
                },
                error: function() {
                    alert('Error occurred.');
                }
            });
        });

        // One-click font-size cycle: 100 -> 104 -> 108 -> 100 -> 96 -> 92 -> 100.
        $('#fontSizeToggle').on('click', function() {
            if (is_changing_font) return;

            is_changing_font = true;

            const currentClass = ['body_92', 'body_96', 'body_100', 'body_104', 'body_108']
                .find(function(className) {
                    return $('body').hasClass(className);
                }) || 'body_100';

            const fontCycle = ['body_100', 'body_104', 'body_108', 'body_96', 'body_92'];
            const currentIndex = fontCycle.indexOf(currentClass);
            const nextClass = fontCycle[(currentIndex + 1) % fontCycle.length];

            const fontCodes = {
                body_100: 'a',
                body_104: 'a-plus',
                body_108: 'a-plus-2',
                body_96: 'a-minus',
                body_92: 'a-minus-2'
            };

            const fontSizeCode = fontCodes[nextClass];

            $.ajax({
                data: 'font_size_code=' + fontSizeCode,
                url: '/change_font_size',
                type: 'POST',
                success: function() {
                    setFontSize(fontSizeCode);
                    is_changing_font = false;
                },
                error: function() {
                    is_changing_font = false;
                    alert('Error occurred.');
                }
            });
        });

        //font switch functionality
        $('#linkBandWidthSwitch').on('click', function() {
            // alert(is_changing_bandwidth);
            if (is_changing_bandwidth == true) return;

            is_changing_bandwidth = true;

            $.ajax({
                data: {},
                url: '/change_bandwidth',
                type: 'POST',
                success: function(response) {
                    window.location.reload();
                },
                error: function(e) {
                    is_changing_bandwidth = false;
                    alert('Error occurred.');
                },

            });

        });

        var bandwidth = "{{ Session::get('bandwidth_code') }}";
        if (bandwidth == 'low') {
            $("img:not('.no-bandwidth')").each(function() {
                $(this).attr('src', '');
            })
        }

        $('#a-home-download-scroll').click(function() {
            var someTabTriggerEl = $('#tab12-tab');
            var tab = new bootstrap.Tab(someTabTriggerEl);
            tab.show();

            $('html, body').animate({
                scrollTop: $("#download-tabs-section").offset().top
            }, 100);
        });

    });

    function goTool(url) {
        window.open(url);
    }

    function setFontSize(font_size_code) {
        var body_class = 'body_100';

        if (font_size_code == 'a-minus-2') body_class = 'body_92';
        else if (font_size_code == 'a-minus') body_class = 'body_96';
        else if (font_size_code == 'a-plus') body_class = 'body_104';
        else if (font_size_code == 'a-plus-2') body_class = 'body_108';

        $('body').removeClass().addClass(body_class);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const appItem = document.querySelector('.footer-app-item');
        const appTrigger = appItem?.querySelector('.footer-install-app-trigger');

        if (appItem && appTrigger) {
            const touchDevice = window.matchMedia('(hover: none), (pointer: coarse)').matches;

            appTrigger.addEventListener('click', function () {
                if (!touchDevice) return;

                const isOpen = appItem.classList.toggle('is-open');
                appTrigger.setAttribute('aria-expanded', String(isOpen));
                if (!isOpen) appTrigger.blur();
            });

            document.addEventListener('click', function (event) {
                if (!appItem.contains(event.target)) {
                    appItem.classList.remove('is-open');
                    appTrigger.setAttribute('aria-expanded', 'false');
                    appTrigger.blur();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    appItem.classList.remove('is-open');
                    appTrigger.setAttribute('aria-expanded', 'false');
                    appTrigger.blur();
                }
            });
        }

        document.querySelectorAll('.sitemap-app-link').forEach(function (link) {
            link.addEventListener('click', function (event) {
                const appItem = document.querySelector('.footer-app-item');
                const appTrigger = appItem?.querySelector('.footer-install-app-trigger');
                if (!appItem || !appTrigger) return;

                event.preventDefault();
                appItem.classList.add('is-open');
                appTrigger.setAttribute('aria-expanded', 'true');
                appItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });

        const shareButton = document.querySelector('.footer-native-share');
        shareButton?.addEventListener('click', async function () {
            const shareData = {
                title: 'Department of Roads',
                text: 'Department of Roads, Government of Nepal',
                url: window.location.href
            };

            try {
                if (navigator.share) {
                    await navigator.share(shareData);
                } else if (navigator.clipboard?.writeText) {
                    await navigator.clipboard.writeText(shareData.url);
                    const status = document.getElementById('footerShareStatus');
                    if (status) status.textContent = 'Link copied';
                } else {
                    const field = document.createElement('textarea');
                    field.value = shareData.url;
                    field.style.position = 'fixed';
                    field.style.opacity = '0';
                    document.body.appendChild(field);
                    field.select();
                    document.execCommand('copy');
                    field.remove();
                    const status = document.getElementById('footerShareStatus');
                    if (status) status.textContent = 'Link copied';
                }
            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.warn('Website sharing was not available.', error);
                }
            }
        });
    });

</script>
