<?php
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

    defined('LVD_BID_LOADED') or die;
?>

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
                    <td><?php echo esc_html($info['desc']); ?></td>
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
                    <td><code><?php echo esc_html($info['provider']); ?></code></td>
                </tr>
                <?php $i++; ?>
            <?php endforeach ?>
        </tbody>
    </table>
</div>