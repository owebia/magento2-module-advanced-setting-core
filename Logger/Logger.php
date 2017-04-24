<?php
/**
 * Copyright © 2016-2017 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Logger;

class Logger extends \Monolog\Logger
{

    /**
     * @param string $title
     * @param string $msg
     * @param string $type
     */
    public function collapse($title, $msg, $type = 'panel-default')
    {
        $this->collapseOpen($title, $type);
        $this->debug($msg);
        $this->collapseClose();
    }

    /**
     * @param string $title
     * @param string $type
     */
    public function collapseOpen($title, $type = 'panel-default')
    {
        $uid = 'id' . uniqid();
        $content = <<<EOD
<div class="panel $type">
    <div class="panel-heading" role="tab" id="heading-$uid">
        <h4 class="panel-title">
            <a role="button" data-toggle="collapse" href="#$uid" aria-expanded="true" aria-controls="$uid">
                $title
            </a>
        </h4>
    </div>
    <div id="$uid" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-$uid">
        <div class="panel-body">
EOD;
        $this->debug($content);
    }

    /**
     *
     */
    public function collapseClose()
    {
        $content = <<<EOD
        </div>
    </div>
</div>
EOD;
        $this->debug($content);
    }
}
