"use strict";
jQuery(function($){

    // tabs system for wpr settings
    $('.wp-color').wpColorPicker();
        $( "#gdpr-tabs" ).tabs();

    // ajax callback function for saved all wpr settings
    $('.wpgdpr_sub_st_control').find('.wpgdpr-spinner').hide(); //@today_work
    $('#wpgdpr_settings_form').on('submit',function(e){
        e.preventDefault();

        $('.wpgdpr_sub_st_control').find('.wpgdpr-spinner').show(); //@today_work
        $(this).find('.wpgdpr_sub_st_control input').prop('disabled', true); //work

        var data = $(this).serialize();
        
        $.post(ajaxurl, data, function(response){
            $('.wpgdpr_sub_st_control').find('.wpgdpr-spinner').hide(); //@today_work
            $('.wpgdpr_save_alert').removeClass('alert_display');
            window.location.reload();
            

        });
    });

    $('[data-hide-url ="set_advance"]').hide();
    $(document).on('click', '[data-show-url ="wpgdpr-url-toggle"]', function(e) {
        e.preventDefault();
        $('[data-hide-url="set_advance"]').slideToggle(500);
    // $('[data-advance="set_advance"]').show();
    });


    // select2 control for wpr settings
    $('.wpgdpr-select2').select2({
        placeholder: 'Select',
        width:"65%",
    });

    $(".gn_roles").select2({
        placeholder: "Select",
        // allowClear: true,
        width:"65%",
        // multiple:true
    });

    $("Select.wpgdpr_op_select").select2({
        placeholder: "Select",
        allowClear: true,
        width:"65%",
    });
});