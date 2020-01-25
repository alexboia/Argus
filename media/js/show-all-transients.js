(function($) {
	"use strict";

	var isShowingDetails = false;

    function disableWindowScroll() {
        window.lvdbid.disableWindowScroll();
    }

    function enableWindowScroll() {
        window.lvdbid.enableWindowScroll();
    }

    function showLoading() {
        window.lvdbid.showLoading();
    }

    function hideLoading() {
        window.lvdbid.hideLoading();
    }

    function getTransientDumpUrl(transientOption) {
        return lvdbid_ajaxUrl 
            + '?action=' + lvdbid_dumpTransientAction 
            + '&lvdbid_nonce=' + lvdbid_dumpTransientNonce 
            + '&lvdbid_transient=' + transientOption;
	}
	
	function showTransientDump(option, data) {
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

	function listenForEscapeKeyAndUnblockUi() {
        window.lvdbid.listenForEscapeKeyAndUnblockUi(function() {
            return isShowingDetails === true;
        });
	}
	
	function initOptionDumpDetailsButtons() {
        $('.lvdbid-option-details').on('click', function(e) {
            var optionTransient = $(this).attr('data-option-details');
            var url = getTransientDumpUrl(optionTransient);

            showLoading();
            $.ajax(url, {
                type: 'GET',
                dataType: 'html'
            }).done(function(data) {
                hideLoading();
                showTransientDump(optionTransient, data);    
            }).fail(function() {
				hideLoading();
				alert('Could no retrieve details for transient: "' + optionTransient + '"!');
            });

            e.preventDefault();
            e.stopPropagation();
        });
    }

	$(document).ready(function() {
		listenForEscapeKeyAndUnblockUi();
        initOptionDumpDetailsButtons();
	});
})(jQuery);