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
                    <td class="lvdbid-filtered-column"><code><?php echo esc_html($option['option_name']); ?></code></td>
                    <td>
                        <?php if (!$option['option_composite']): ?>
                            <pre><code><?php echo esc_html($option['option_value']); ?></code></pre>
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