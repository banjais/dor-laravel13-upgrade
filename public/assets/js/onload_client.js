$nepali_numbers = ['०', '१', '२', '३', '४', '५', '६', '७', '८', '९', '.'];

function englishToNepaliNumber($number) {
    $number = Number($number).toFixed(2);
    $numberstring = $number.toString();
    var $fractionpart = $numberstring.substring($numberstring.lastIndexOf('.') + 1);
    if ($fractionpart.length != $numberstring.length) {
        var $unformattedintpart = $numberstring.substring(0, $numberstring.lastIndexOf('.'));
    } else {
        $unformattedintpart = $numberstring;
    }
    $fractionpartnepali = '';
    if ($fractionpart.length == 1) {
        $fractionpartnepali = '.' + $nepali_numbers[$fractionpart.substr(0, 1)];
    } else if ($fractionpart.length == 2) {
        $fractionpartnepali = '.' + $nepali_numbers[$fractionpart.substr(0, 1)] + $nepali_numbers[$fractionpart.substr(1, 1)]
    } else {
        $fractionpartnepali = '';
    }

    $len = $unformattedintpart.length;
    var $count = 1;
    var $nepalinumberstring = '';
    for ($index = $len - 1; $index >= 0; $index--) {
        $nepalinumberstring = $nepali_numbers[$unformattedintpart.substr($index, 1)] + $nepalinumberstring;
        if ($count % 3 == 0 && $index != 0) {
            $nepalinumberstring = ',' + $nepalinumberstring;
        }
        $count++;
    }
    return ($nepalinumberstring);
}

$(document).ready(function() {

    if (langId == 1) {
        $('ul.ullist').each(function() {
            var current = $(this).find('li:first');
            var num = parseInt(current.text());
            nepaliNum = englishToNepaliNumber(num);
            current.text(nepaliNum);
        });
        $('#project_table tr').each(function() {
            var count = $(this).find("td:first");
            var num = parseInt(count.text());
            nepaliNum = englishToNepaliNumber(num);
            count.text(nepaliNum);
        });
        $('#normal_table tr').each(function() {
            var count = $(this).find("td:first");
            var num = parseInt(count.text());
            nepaliNum = englishToNepaliNumber(num);
            count.text(nepaliNum);
        });

        $('#bill_table tr').each(function() {
            var count = $(this).find("td:first");
            nepaliNum = englishToNepaliNumber((count.text()));
            count.text(nepaliNum);

            var pan = $(this).find(".pan_number");
            //nepaliNum = englishToNepaliNumber(parseInt(pan.text()));
            //pan.text(nepaliNum);

            var amount = $(this).find(".bill_amount");
            //nepaliNum = englishToNepaliNumber(amount.text());
            //amount.text(nepaliNum);
        });

        $('#training_table tr').each(function() {
            var count = $(this).find("td:first");
            nepaliNum = englishToNepaliNumber((count.text()));
            count.text(nepaliNum);
            var number_of_traniees = $(this).find(".number_of_traniees");
            nepaliNum = englishToNepaliNumber(number_of_traniees.text());
            number_of_traniees.text(nepaliNum);
        });

        // $('#staff_table tr').each(function(){
        // 	var count = $(this).find("td:first");
        // 	var num = parseInt(count.text());
        // 	nepaliNum = englishToNepaliNumber(num);
        // 	count.text(nepaliNum);
        // });

        var nepaliNum = englishToNepaliNumber($("#number_of_traniees").text());
        $("#number_of_traniees").text(nepaliNum);

        $('.photo_count').each(function() {
            var album = $(this).find('p');
            var num = parseInt(album.text());
            album.text(englishToNepaliNumber(num) + 'फोटो');
        });

    }

    $(".language_id").change(function() {
        var langId = $(this).val();
        $.ajax({
            data: 'language_id=' + langId,
            url: sitePath + 'change_language',
            type: 'POST',
            success: function(response) {
                location.reload();
            },
            error: function(e) {
                alert('Serevr Error!');
            },

        });
    });

    $(".change_language").click(function() {
        str = this.id;
        var langId = str.replace('language_', '');
        $.ajax({
            data: 'language_id=' + langId,
            url: sitePath + 'change_language',
            type: 'POST',
            success: function(response) {
                debugger;
                location.reload();
            },
            error: function(e) {
                alert('Serevr Error!');
            },

        });
    });

    $('#reload-captcha').click(function() {
        $.ajax({
            type: 'POST',
            url: '/reload_captcha',
            success: function(response) {
                document.getElementById('captcha-image').innerHTML = response;
            },
            error: function(e) {
                alert('Server Error !');
            },
        });
    });
});