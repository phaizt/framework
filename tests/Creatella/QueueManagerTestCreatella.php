<?php

namespace Illuminate\Tests\Creatella;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Illuminate\Queue\QueueManager;

class QueueManagerTestCreatella extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testDefaultConnectionCanBeResolved()
    {
        $app = [
            'config' => [
                'queue.default' => 'sync',
                'queue.connections.sync' => ['driver' => 'sync'],
            ],
            'encrypter' => $encrypter = m::mock('Illuminate\Contracts\Encryption\Encrypter'),
        ];

        $manager = new QueueManager($app);
        $connector = m::mock('stdClass');
        $queue = m::mock('stdClass');
        $queue->shouldReceive('setConnectionName')->once()->with('sync')->andReturnSelf();
        $connector->shouldReceive('connect')->once()->with(['driver' => 'sync'])->andReturn($queue);
        $manager->extend('sync', function () use ($connector) {
            return $connector;
        });

        $queue->shouldReceive('setContainer')->once()->with($app);
        $this->assertSame($queue, $manager->connection('sync'));
    }

    public function testOtherConnectionCanBeResolved()
    {
        $app = [
            'config' => [
                'queue.default' => 'sync',
                'queue.connections.foo' => ['driver' => 'bar'],
            ],
            'encrypter' => $encrypter = m::mock('Illuminate\Contracts\Encryption\Encrypter'),
        ];

        $manager = new QueueManager($app);
        $connector = m::mock('stdClass');
        $queue = m::mock('stdClass');
        $queue->shouldReceive('setConnectionName')->once()->with('foo')->andReturnSelf();
        $connector->shouldReceive('connect')->once()->with(['driver' => 'bar'])->andReturn($queue);
        $manager->extend('bar', function () use ($connector) {
            return $connector;
        });
        $queue->shouldReceive('setContainer')->once()->with($app);

        $this->assertSame($queue, $manager->connection('foo'));
    }

    public function testNullConnectionCanBeResolved()
    {
        $app = [
            'config' => [
                'queue.default' => 'null',
            ],
            'encrypter' => $encrypter = m::mock('Illuminate\Contracts\Encryption\Encrypter'),
        ];

        $manager = new QueueManager($app);
        $connector = m::mock('stdClass');
        $queue = m::mock('stdClass');
        $queue->shouldReceive('setConnectionName')->once()->with('null')->andReturnSelf();
        $connector->shouldReceive('connect')->once()->with(['driver' => 'null'])->andReturn($queue);
        $manager->extend('null', function () use ($connector) {
            return $connector;
        });
        $queue->shouldReceive('setContainer')->once()->with($app);

        $this->assertSame($queue, $manager->connection('null'));
    }
}
