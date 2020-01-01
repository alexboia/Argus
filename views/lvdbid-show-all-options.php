<script type="text/javascript">
    var lvdbid_ajaxUrl = '<?php echo esc_js($data->ajaxUrl); ?>';
    var lvdbid_dumpOptionNonce = '<?php echo esc_js($data->dumpOptionNonce); ?>';
    var lvdbid_dumpOptionAction = '<?php echo esc_js($data->dumpOptionAction); ?>';
</script>

<div class="lvdbid-page-container">
    <h1><?php echo esc_html($data->pageTitle); ?></h1>
    <div class="lvdbid-table-filter-container">
        <input type="text" 
            class="regular-text lvdbid-table-filter" 
            id="lvdbid-table-filter" 
            placeholder="Filter options..." 
            data-filtered-host="#lvdbid-options-list" />
    </div>
    <table id="lvdbid-options-list" class="widefat fixed" cellspacing="0">
        <thead>
            <tr>
                <th>Name</th>
                <th>Value</th>
                <th style="width: 100px;">Autoload</th>
            </tr>
        </thead>
        <tbody> 
            <?php foreach ($data->allOptions as $option): ?>
                <tr>
                    <td class="lvdbid-filtered-column"><code><?php echo $option['option_name']; ?></code></td>
                    <td>
                        <?php if (!$option['option_composite']): ?>
                            <pre><code><?php echo $option['option_value']; ?></code></pre>
                        <?php else: ?>
                            <a class="lvdbid-option-details" 
                                href="javascript:void(0)" 
                                data-option-details="<?php echo esc_attr($option['option_name']); ?>">Complex type. Click here to view details</a>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $option['autoload']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>