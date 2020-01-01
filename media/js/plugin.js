(function($) {
    "use strict";

    var clipboard = null;
    var filterTimer = null;
    var $filteredHost = null;

    $(document).ready(function() {
        if (window.ClipboardJS != undefined) {
            clipboard = new ClipboardJS('.lvdbid-copy-handler');
        }

        $('#lvdbid-table-filter').keyup(function() {
            var $filterInput = $(this);
            if (filterTimer !== null) {
                window.clearTimeout(filterTimer);
            }

            filterTimer = window.setTimeout(function() {
                filterTimer = null;

                if ($filteredHost === null) {
                    $filteredHost = $($filterInput.attr('data-filtered-host'));
                }

                var keyword = ($filterInput.val() || '')
                    .toLowerCase();

                $filteredHost.find('tr').each(function(index, element) {
                    var $target = $(element);
                    var compareText = ($target.find('.lvdbid-filtered-column').text() || '')
                        .toLowerCase();

                    $target.toggle(!keyword || compareText.indexOf(keyword) >= 0);
                });
            }, 100);
        });
    });
})(jQuery);