<?php

namespace Evenvi\Tianyi;

use PHPUnit\Framework\TestCase;

class AccessTokenTest extends TestCase
{

    public function testShowInfo()
    {
        $at = new AccessToken();
        $at->showInfo();
        $this->assertEquals('123', $at->get());
    }
}
