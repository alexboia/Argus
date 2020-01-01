<div class="lvdbid-page-container">
    <h1><?php echo esc_html($data->pageTitle); ?></h1>
    <div class="lvdbid-table-filter-container">
        <input type="text" 
            class="regular-text lvdbid-table-filter" 
            id="lvdbid-table-filter" 
            placeholder="Filter blog information..." 
            data-filtered-host="#lvdbid-bloginfo-list" />
    </div>
    <table id="lvdbid-bloginfo-list" class="widefat fixed" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 200px;">Key</th>
                <th>Description</th>
                <th style="width: 350px;">Current value</th>
                <th>Usage</th>
                <th>Alternate</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($data->blogInfoKeys as $key => $info): ?>
                <tr>
                    <td class="lvdbid-filtered-column"><code><?php echo $key; ?></code></td>
                    <td><?php echo $info['desc']; ?></td>
                    <td>
                        <input id="bloginfo-value-<?php echo $i; ?>" 
                            class="lvdbid-code-host"
                            type="text" 
                            value="<?php echo esc_attr(get_bloginfo($key)); ?>" 
                            readonly="readonly" />
                        <button class="lvdbid-copy-handler" data-clipboard-target="#bloginfo-value-<?php echo $i; ?>">
                            <span class="dashicons dashicons-clipboard"></span>
                        </button>
                    </td>
                    <td>
                        <input id="bloginfo-invoke-<?php echo $i; ?>" 
                            class="lvdbid-code-host"
                            type="text" 
                            value="get_bloginfo('<?php echo esc_attr($key); ?>');" 
                            readonly="readonly" />
                        <button class="lvdbid-copy-handler" data-clipboard-target="#bloginfo-invoke-<?php echo $i; ?>">
                            <span class="dashicons dashicons-clipboard"></span>
                        </button>
                    </td>
                    <td><code><?php echo $info['provider']; ?></code></td>
                </tr>
                <?php $i++; ?>
            <?php endforeach ?>
        </tbody>
    </table>
</div>