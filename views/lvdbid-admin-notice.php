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

<style type="text/css">
    .lvdbid-admin-notice {
        display: block;
        line-height: 19px;
        padding: 11px 15px;
        font-size: 14px;
        text-align: left;
        margin: 10px 20px 0 2px;
        background-color:#fff;
        border-left: 4px solid #0073aa;
        box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        min-width: 380px;
        max-width: 600px;
    }
</style>

<div class="lvdbid-admin-notice">
    Click <a href="<?php echo esc_url($data->blogInfoUrl); ?>" target="_blank">here</a> to see your current, detailed, blog information. Can also be accessed via main menu: "Debug blog information".
</div>

<div class="lvdbid-admin-notice">
    Click <a href="<?php echo esc_url($data->optionsInfoUrl); ?>" target="_blank">here</a> to see a list of all the options currently stored. Can also be accessed via main menu: "Debug blog information" > "Debug blog options".
</div>