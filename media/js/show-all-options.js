(function($) {
    "use strict";

    var isShowingDetails = false;

    function disableWindowScroll() {
        $('body').addClass('lvdbid-stop-scrolling');
    }

    function enableWindowScroll() {
        $('body').removeClass('lvdbid-stop-scrolling');
    }

    function showLoading() {
        $.blockUI({
            message: 'Please wait...',
            css: {
                border: 'none', 
                padding: '15px', 
                backgroundColor: '#000', 
                opacity: .5, 
                color: '#fff' 
            },

            onBlock: disableWindowScroll,
            onUnblock: enableWindowScroll
        });
    }

    function hideLoading() {
        $.unblockUI();
    }

    function getOptionDumpUrl(option) {
        return lvdbid_ajaxUrl 
            + '?action=' + lvdbid_dumpOptionAction 
            + '&lvdbid_nonce=' + lvdbid_dumpOptionNonce 
            + '&lvdbid_option=' + option;
    }

    function showOptionDump(option, data) {
        $.blockUI({ 
            onOverlayClick: $.unblockUI,

            message: '<div class="lvdbid-option-dump-container">' + data + '</div>', 
            css: { 
                top:  '5%', 
                left: '5%', 
                width: '90%',
                height: '90%',
                border: '0px none',
                cursor: 'normal',
                boxShadow: '0 5px 15px rgba(0, 0, 0, 0.7)'
            },
            onBlock: function() {
                isShowingDetails = true;
                disableWindowScroll();
            },
            onUnblock: function() {
                isShowingDetails = false;
                enableWindowScroll();
            }
        });
    }

    function listenForEscapeKey() {
        $(document).on('keydown', function(e) {
            if (e.which == 27 && isShowingDetails) {
                $.unblockUI();
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }

    function initOptionDumpDetailsButtons() {
        $('.lvdbid-option-details').on('click', function(e) {
            var option = $(this).attr('data-option-details');
            var url = getOptionDumpUrl(option);

            showLoading();
            $.ajax(url, {
                type: 'GET',
                dataType: 'html'
            }).done(function(data) {
                hideLoading();
                showOptionDump(option, data);    
            }).fail(function() {
                alert('Could no retrieve details for option: "' + option + '"!');
            });

            e.preventDefault();
            e.stopPropagation();
        });
    }

    $(document).ready(function() {
        listenForEscapeKey();
        initOptionDumpDetailsButtons();
    });
})(jQuery);