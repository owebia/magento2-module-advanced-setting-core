<?php
/**
 * Copyright © 2015-2018 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Owebia\AdvancedSettingCore\Controller\Adminhtml\Debug;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Index extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    private $resultRawFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var Filesystem $filesystem */
        $filesystem = $this->_objectManager->get(Filesystem::class);
        $readInterface = $filesystem->getDirectoryRead(DirectoryList::ROOT);

        $path = '/var/log/owebia_advancedsettingcore.log';
        if (!$readInterface->isExist($path)) {
            $content = '';
        } elseif (!$readInterface->isReadable($path)) {
            $content = "<p>File MAGENTO_ROOT{$path} is not readable</p>";
        } else {
            $content = $readInterface->readFile($path);
        }

        $content = preg_replace('@</pre>[\r\n]*<pre@', '</pre><pre', $content);
        if (!$content || !trim($content)) {
            $content = "<p>The debug log is empty</p>";
        }
        $content = <<<EOD
<!doctype html>
<html>
<head>
<meta charset="utf-8"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.0.0/styles/github.min.css" rel="stylesheet">
<style>
body{padding-top:15px}
.clearlog{
    position:fixed;top:2px;right:2px;
    background-color: #eb5202;border-color: #eb5202;color: #fff;text-shadow: 1px 1px 0 rgba(0,0,0,0.25);
    font-size: 1.6rem;
    letter-spacing: .025em;
    padding: .6875em 1em .6875em;
    font-weight: 600;
    line-height: 1.36;
    z-index: 1000;
}
.clearlog:hover{color:#fff;text-decoration:none;background:red}
pre{margin:0;padding:0;border-radius:0;}
pre + pre{border-top:0}
.panel-title pre{border:0;background:transparent}
.panel-title .hljs{background:transparent}
.panel-body{background:#f8f8f8}
.panel-body .row + .row{border-top:solid 1px #ddd}
.panel-body pre{border:0;background:transparent}
.panel-body .hljs{background:transparent;padding:0;}
.target{position:fixed;top:0;right:0;width:50%;padding:2em;bottom:0;overflow:auto}
.panel-body{counter-reset:line}
div[data-target]{clear:both}
div[data-target].current_expression,
div[data-target]:hover{cursor:pointer;background:yellow}
div[data-target]:before {
    counter-increment: line;
    content: counter(line);
    float: left;
    display: block;
    
    padding-right:.5em;
    color: #000;
    opacity: .2;
    font-size:.9em;
    font-family:monospace;
    width: 3em;
    text-align:right;
}
.panel-body div[data-target] pre{border-left: 1px solid #ddd;padding-left:.5em;}

            
</style>
</head>
<body>
<a href="{$this->getUrl('owebia_advancedsettingcore/debug/clear')}" class="clearlog">&times; Clear Log</a>
<div class=container-fluid>
    <div class=row>
        <div class=col-sm-6 id="root">
            {$content}
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.0.0/highlight.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.0.0/languages/php.min.js"></script>
<script>
    <!-- doc scripts start -->
    $(function(){
        $('a[href^="#"]').each(function (e) {
            var anchor = $(this).attr('href').replace('#', '');
            if (anchor == '') return;
            var jelem = $('#' + anchor);
            if (!jelem.length) {
                $(this).css('border', 'solid 1px red').css('padding', 3);
            }
        });
        $('body').on('click', 'div[data-target]', function(e) {
            console.log(123);
            var selector = $(this).attr('data-target');
            var jelem = $(selector);
            $('.target').addClass('hidden');
            $('.current_expression').removeClass('current_expression');
            $(this).addClass('current_expression');
            jelem.removeClass('hidden').show();
        });

        var index = 0;
        hljs.initHighlightingOnLoad();
        var jcontainer = $('#root').children('.panel').slice(-3);
        jcontainer.find('pre.php').each(function(){
            var text = $(this).text();
            while (text.match(/'(title|enabled|price)'(\s*)=>/)) {
                text = text.replace(/'(title|enabled|price)'(\s*)=>/, '<span class="config-attr">\'$1\'</span>$2=>');
            }
            $(this).html("<code class=php>" + text.trim() + "</code>");
        });
        jcontainer.find('code.php').each(function(i, block) {
            hljs.highlightBlock(block);
        });
    });
    <!-- doc scripts end -->
</script>
</body>
</html>
EOD;

        return $this->resultRawFactory->create()
                ->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Content-type', 'text/html; charset=UTF-8', true)
                ->setContents($content);
    }
}
