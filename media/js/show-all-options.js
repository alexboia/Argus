/**
 * Copyright (c) 2019-2020 Alexandru Boia
 *
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 *	1. Redistributions of source code must retain the above copyright notice, 
 *		this list of conditions and the following disclaimer.
 *
 * 	2. Redistributions in binary form must reproduce the above copyright notice, 
 *		this list of conditions and the following disclaimer in the documentation 
 *		and/or other materials provided with the distribution.
 *
 *	3. Neither the name of the copyright holder nor the names of its contributors 
 *		may be used to endorse or promote products derived from this software without 
 *		specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, 
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY 
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES 
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) 
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, 
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 */

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