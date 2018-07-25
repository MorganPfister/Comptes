<?php

namespace CDC\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CDCCoreBundle extends Bundle {
    public function getParent(){
        return 'FOSUserBundle';
    }
}
