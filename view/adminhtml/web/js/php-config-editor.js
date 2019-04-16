/**
 * Copyright © 2019 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
require([
    'jquery'
], function($) {
    $.fn.phpConfigEditor = function () {
        return $(this).each(function (index, item) {
            var jinput = $(item);
            if (jinput.data('phpConfigEditor')) {
                return;
            }
            var id = jinput.attr('id');
            var jcontainer = $('#pce_' + id);
            jinput.data('phpConfigEditor', true);
            var toggleFullscreen = function (toggle) {
                var editor = jcontainer.data('editor');
                if (toggle) {
                    var elems = id.split('_');
                    jcontainer.find('.page-title').text($('#carriers_' + elems[1] + '_title').val());
                    jcontainer.addClass('pceFullscreen');
                    $('.pceFieldContainer', jcontainer).css({
                        top: $('.pceHead', jcontainer).outerHeight(true)
                            + parseInt(jcontainer.css('padding-top'), 10),
                        bottom: 0
                    });
                    if (editor) {
                        editor.resize(true);
                        editor._muteChangeEvents = true;
                        editor.setValue("<?php // Keep for syntax highlighting\n\n" + editor.getValue().trim() + "\n");
                        editor._muteChangeEvents = false;
                        editor.clearSelection();
                        editor.focus();
                    }
                } else {
                    jcontainer.removeClass('pceFullscreen');
                    if (editor) {
                        editor._muteChangeEvents = true;
                        editor.setValue(editor.getValue().replace(/^<\?php(?:\s+\/\/ Keep for syntax highlighting)?\s+/, '').trim());
                        editor._muteChangeEvents = false;
                    }
                }
            };
            var markChangedState = function (jcontainer) {
                jcontainer.addClass('pceChanged');
                $('.pceStatus', jcontainer).text('Save Config to apply changes');
            };
            jcontainer
                .on('click', '.pceFullscreenOn', function (e) {
                    e.preventDefault();
                    toggleFullscreen(true);
                })
                .on('click', '.pceFullscreenOff', function (e) {
                    e.preventDefault();
                    toggleFullscreen(false);
                });

            if (typeof ace !== 'undefined') {
                var editor = ace.edit(id);
                editor.setTheme('ace/theme/tomorrow_night');
                editor.session.setMode('ace/mode/php');
                jcontainer.data('editor', editor);

                editor.session.on('change', function (delta) {
                    if (editor._muteChangeEvents) {
                        return;
                    }
                    markChangedState(jcontainer);
                    editor.resize(true);
                });
            } else {
                jinput
                    .on('change', function () {
                        markChangedState(jcontainer);
                    })
                    .on('keydown', function (e) {
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
            }
        });
    };
});
