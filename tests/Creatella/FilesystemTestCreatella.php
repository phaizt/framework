<?php

namespace Illuminate\Tests\Creatella;

use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Application;
use Illuminate\Filesystem\FilesystemManager;

class FilesystemTestCreatella extends TestCase
{
    public function testApp()
    {
        $filesystem = new FilesystemManager(new Application());

        $driver = $filesystem->driver("Ftp",[
            'host' => 'ftp.example.com',
            'username' => 'admin',
            'permPublic' => 0700,
            'unsupportedParam' => true,
        ]);

        /** @var Ftp $adapter */
        $adapter = $driver->getAdapter();
        $this->assertEquals(0700, $adapter->getPermPublic());
        $this->assertEquals('ftp.example.com', $adapter->getHost());
        $this->assertEquals('admin', $adapter->getUsername());
    }

}
