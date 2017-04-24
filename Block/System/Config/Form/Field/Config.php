<?php
/**
 * Copyright © 2016-2017 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Owebia\AdvancedSettingCore\Block\System\Config\Form\Field;

abstract class Config extends AbstractField
{
    abstract protected function getFullscreenTitle(\Magento\Framework\Data\Form\Element\AbstractElement $element);
    abstract protected function getHelpUrl(\Magento\Framework\Data\Form\Element\AbstractElement $element);

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function getHeader(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $elementId = $element->getHtmlId();
        $hash = md5($elementId);
        return <<<EOD
    <script>
        function fs_$hash(toggle) {
            require([
                'jquery'
            ], function($) {
                if (toggle) {
                    var id = '$elementId';
                    var elems = id.split('_');
                    $('#owb_$elementId').find('.page-title').text($('#carriers_' + elems[1] + '_title').val());
                    $('#owb_$elementId').addClass('fs');
                    $('#owb_$elementId .owb_field_container').css({
                        top:    $('#owb_$elementId .owb_head').outerHeight(true)
                            + parseInt($('#owb_$elementId').css('padding-top'), 10),
                        bottom: $('#owb_$elementId .owb_foot').outerHeight(true)
                            + parseInt($('#owb_$elementId').css('padding-bottom'), 10)
                    });
                } else {
                    $('#owb_$elementId').removeClass('fs');
                }
            });
        }
        require([
            'jquery'
        ], function($) {
            $('#$elementId').on('change', function(){
                $('#owb_$elementId').addClass('changed');
                $('#owb_$elementId .owb_foot').text('Save Config to apply changes');
            });
            $('#$elementId').on('keydown', function(e) {
                var keyCode = e.keyCode || e.which;
                if (keyCode == 9) {
                    e.preventDefault();
                    var jinput = $(this);
                    var toInsert = "\t";
                    var startPos = this.selectionStart;
                    var endPos = this.selectionEnd;
                    var scrollTop = this.scrollTop;
                    var value = jinput.val();
                    jinput.val(value.substring(0, startPos) + toInsert + value.substring(endPos, value.length));
                    jinput.focus();
                    this.selectionStart = startPos + toInsert.length;
                    this.selectionEnd = startPos + toInsert.length;
                    this.scrollTop = scrollTop;
                }
            });
        });
    </script>
    <style>
    .owb.fs{position:fixed;top:0;left:0;right:0;bottom:0;z-index:800;background:#fff;padding:2rem}
    .owb.fs .owb_fs_only .owb_toolbar{text-align:right}
    .owb textarea{background:#eee;color:Teal;font-family:monospace;overflow:auto;white-space:pre;height:400px;
        tab-size:4;display:none}
    .owb.fs textarea{display:block;position:absolute;width:100%;height:100%;resize:none}
    .owb.fs .owb_field_container{display:block;position:absolute;top:2rem;left:2rem;right:2rem;bottom:2rem;}
    .owb.fs .owb_fs_hidden{display:none}
    .owb.fs .owb_foot{position:fixed;bottom:2rem;left:2rem}
    .owb .owb_fs_only{display:none}
    .owb .icon span{display:none}
    .owb a.icon{text-decoration:none}
    .owb .icon:before{-webkit-font-smoothing:antialiased;font-size:2.2rem;line-height:1;font-family:'Admin Icons';
        vertical-align:middle;display:inline-block;font-weight: normal;overflow:hidden;speak:none;text-align:center;}
    .owb .icon:before{color:#514943;}
    .owb.changed .owb_foot{color:red}
    .owb .icon svg{fill:#514943}
    .owb a.icon:hover:before{color:#000}
    .owb a.icon:hover svg{fill:#000}
    .owb .icon-edit:before{content:'\\e631';}
    .owb .icon-help:before{content:'\\e633';}
    .owb .icon-check:before{content:'\\e62d';}
    .owb .icon-svg svg{vertical-align:middle}
    .owb.fs .owb_fs_only{display:block}
    .owb_head{padding-bottom:5px;}
    .owb_toolbar * + .icon{margin-left:10px}
    .owb_foot{padding-top:5px;}
    </style>
    <div class="owb_head">
        <div id="owb_fh_$elementId" class="owb_fs_only">
            <div class="row">
                <div class="col-l-8 col-m-6">
                    <span class="page-title">{$this->escapeHtml($this->getFullscreenTitle($element))}</span>
                </div>
                <div class="owb_toolbar col-l-4 col-m-6">
                    <a target="_blank" class="icon icon-help" href="{$this->escapeHtml($this->getHelpUrl($element))}"
                        title="{$this->escapeHtml(__('Help'))}"><span>{$this->escapeHtml(__('Help'))}</span></a>
                    <a href="#" class="icon icon-check" onclick="fs_$hash(false);"
                        title="{$this->escapeHtml(__('Reduce'))}"><span>{$this->escapeHtml(__('Reduce'))}</span></a>
                </div>
            </div>
        </div>
        <div class="owb_fs_hidden owb_toolbar">
            {$this->getToolbarContent($element)}
        </div>
    </div>
EOD;
    }

    protected function getToolbarContent(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $elementId = $element->getHtmlId();
        $hash = md5($elementId);
        return <<<EOD
            <a href="#" class="icon icon-edit" onclick="fs_$hash(true);"
                title="{$this->escapeHtml(__('Edit'))}"><span>{$this->escapeHtml(__('Edit'))}</span></a>
            <a target="_blank" class="icon icon-help" href="{$this->escapeHtml($this->getHelpUrl($element))}"
                title="{$this->escapeHtml(__('Help'))}"><span>{$this->escapeHtml(__('Help'))}</span></a>
            <a target="_blank" class="icon icon-svg" href="{$this->getUrl('owebia_advancedsettingcore/debug/index')}"
                title="{$this->escapeHtml(__('View Debug'))}">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                    width="22" height="23" viewBox="0 0 416 448">
                    <g id="icomoon-ignore"></g>
                    <path d="M408 240q0 6.5-4.75 11.25t-11.25 4.75h-56q0 42.75-16.75 72.5l52 52.25q4.75 4.75 4.75
11.25t-4.75 11.25q-4.5 4.75-11.25 4.75t-11.25-4.75l-49.5-49.25q-1.25 1.25-3.75 3.25t-10.5 7.125-16.25 9.125-20.5
7.25-24.25 3.25v-224h-32v224q-12.75 0-25.375-3.375t-21.75-8.25-16.5-9.75-10.875-8.125l-3.75-3.5-45.75 51.75q-5
5.25-12 5.25-6 0-10.75-4-4.75-4.5-5.125-11.125t3.875-11.625l50.5-56.75q-14.5-28.5-14.5-68.5h-56q-6.5
0-11.25-4.75t-4.75-11.25 4.75-11.25 11.25-4.75h56v-73.5l-43.25-43.25q-4.75-4.75-4.75-11.25t4.75-11.25 11.25-4.75 11.25
4.75l43.25 43.25h211l43.25-43.25q4.75-4.75 11.25-4.75t11.25 4.75 4.75 11.25-4.75 11.25l-43.25 43.25v73.5h56q6.5 0 11.25
4.75t4.75 11.25zM288 96h-160q0-33.25 23.375-56.625t56.625-23.375 56.625 23.375 23.375 56.625z"></path>
                </svg>
            </a>
EOD;
    }

    protected function getFooterContent(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '';
    }

    /**
     * Retrieve element HTML markup
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings("CamelCaseMethodName")
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return <<<EOD
    <div id="owb_{$element->getHtmlId()}" class="owb">
        {$this->getHeader($element)}
        <div class="owb_field_container">{$element->getElementHtml()}</div>
        <div class="owb_foot">
            {$this->getFooterContent($element)}
        </div>
    </div>
EOD;
    }
}
