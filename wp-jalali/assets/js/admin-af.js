jQuery(document).ready(function($) {
    /**
     * create vars
     */
    var jalaliMonthNames = ["", "حمل", "ثور", "جوزا", "سرطان", "اسد", "سنبله", "میزان", "عقرب", "قوس", "جدی", "دلو", "حوت"];
    var timestampdiv = $('#timestampdiv');
    /* =================================================================== */
    /**
     *define functions 
     */
    function timestampDivModifier(year, month, day, hour, min) {
        content = '<div class="timestamp-wrap jalaliDivBox">';
        content += '<select id="Jmm" name="Jmm">';
        for (var i = 1; i <= 12; i++) {
            if (i == month)
                sel = 'selected="selected"';
            else
                sel = '';
            content += '<option ' + sel + ' value="' + i + '">' + jalaliMonthNames[i] + '</option>';
        }
        content += '</select>';

        content += '<input type="text" name="Jjj" value="' + day + '" id="Jjj" size="2" maxlength="2" autocomplete="off" />,';
        content += '<input type="text" name="Jaa" value="' + year + '" id="Jaa" size="4" maxlength="4" autocomplete="off" /> @';
        content += '<input type="text" name="Jmn" value="' + min + '" id="Jmn" size="2" maxlength="2" autocomplete="off" /> : ';
        content += '<input type="text" name="Jhh" value="' + hour + '" id="Jhh" size="2" maxlength="2" autocomplete="off" />';
        content += '</div>';
        return content;
    }

    function changeTimestampViewer(){
        y = $('input[name=aa]').val();
        m = $('select[name=mm]').val();
        d = $('input[name=jj]').val();
        h = $('input[name=hh]').val();
        i = $('input[name=mn]').val();
        //alert(y+','+m+','+d);
        jd = JalaliDate.gregorianToJalali(y, m, d);
        //alert(jd);
        ret='';
        $text = jalaliMonthNames[jd[1]]+' '+jd[2]+', '+jd[0]+' @'+h+':'+i;
        for (var i = 0; i < $text.length ; i++){
            if(!isNaN($text[i]) && $text[i]!=' '){
                ret += String.fromCharCode($text.charCodeAt(i) + 1728);
            }else{
                ret += $text[i];
            }
        }
        $('#timestamp b').text(ret);
        
    }
    /* =================================================================== */
    $('#the-list').on('click', '.editinline', function() {
        var tr = $(this).closest('td');
        var year = tr.find('.aa').html();
        if (year > 1700){
            var month = tr.find('.mm').html();
            var day = tr.find('.jj').html();
            var hour = tr.find('.hh').html();
            var min = tr.find('.mn').html();
            var date = JalaliDate.gregorianToJalali(year, month, day);
//            $('.jalaliDivBox').remove();
            $('.inline-edit-date div').hide();
            $('.inline-edit-date').prepend(timestampDivModifier(date[0], date[1], date[2], hour, min));
        }
    });

    $('.inline-edit-date').on('keyup', '#Jhh', function(e) {
        $('input[name=hh]').val($(this).val());
    });

    $('.inline-edit-date').on('keyup', '#Jmn', function(e) {
        $('input[name=mn]').val($(this).val());
    });

    $('.inline-edit-date').on('keyup', '#Jaa , #Jjj', function(e) {
        year = $('#Jaa').val();
        month = $('#Jmm').val();
        day = $('#Jjj').val();
        date = JalaliDate.jalaliToGregorian(year, month, day);
        $('input[name=aa]').val(date[0]);
        $('select[name=mm]').val(date[1]);
        $('input[name=jj]').val(date[2]);
    });

    $('.inline-edit-date').on('change', '#Jmm', function() {
        year = $('#Jaa').val();
        month = $('#Jmm').val();
        day = $('#Jjj').val();
        date = JalaliDate.jalaliToGregorian(year, month, day);
        $('input[name=aa]').val(date[0]);
        if(date[1]<10)date[1] = '0'+date[1];
        $('select[name=mm]').val(date[1]);
        $('input[name=jj]').val(date[2]);
    });

    /* =================================================================== */
    /**
     * in edit.php
     */
    
    $('a.edit-timestamp').on('click', function() {
        var date = JalaliDate.gregorianToJalali($('#aa').val(), $('#mm').val(), $('#jj').val());
        var divCnt = timestampDivModifier(date[0], date[1], date[2], $('#hh').val(), $('#mn').val());
        $('#timestampdiv .timestamp-wrap').hide();
        $('#timestampdiv').prepend(divCnt);
    });


    $('#timestampdiv .cancel-timestamp').on('click', function() {
       $('.jalaliDivBox').remove();
    });
    
    $('.save-timestamp,#publish').on('click', function() {
        if($('#Jhh').val()!== undefined){
            $('input[name=hh]').val($('#Jhh').val());
            $('input[name=mn]').val($('#Jmn').val());
            year = $('#Jaa').val();
            month = $('#Jmm').val();
            day = $('#Jjj').val();
            jDate = [year,month,day];
            date = JalaliDate.jalaliToGregorian(year, month, day);
            if(date[1]<10)date[1] = '0'+date[1];
            $('input[name=aa]').val(date[0]);
            $('select[name=mm]').val(date[1]);
            $('input[name=jj]').val(date[2]);
        }

        setTimeout(function(){        
            if($('#timestampdiv .timestamp-wrap:eq(1)').hasClass('form-invalid')){
                $('.jalaliDivBox').addClass('.form-invalid');
            }else{
                $('.jalaliDivBox').remove();
                $('#timestampdiv').slideUp('fast');
                $('a.edit-timestamp').slideDown('fast');
                setTimeout(function(){changeTimestampViewer()},100);
            }
        },100);
    });


    /* =================================================================== */

});