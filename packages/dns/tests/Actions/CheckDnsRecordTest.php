<?php

namespace Vigilant\Dns\Tests\Actions;

use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Dns\Actions\CheckDnsRecord;
use Vigilant\Dns\Actions\ResolveRecord;
use Vigilant\Dns\Enums\Type;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Dns\Notifications\RecordChangedNotification;
use Vigilant\Dns\Tests\TestCase;

class CheckDnsRecordTest extends TestCase
{
    #[Test]
    public function it_does_not_update_when_value_is_unchanged(): void
    {
        RecordChangedNotification::fake();

        $this->mock(ResolveRecord::class, function (MockInterface $mock): void {
            $mock->shouldReceive('resolve')->with(Type::A, 'govigilant.io')->andReturn('127.0.0.1');
        });

        $monitor = DnsMonitor::query()->create([
            'type' => Type::A,
            'record' => 'govigilant.io',
            'value' => '127.0.0.1',
        ]);

        /** @var CheckDnsRecord $action */
        $action = app(CheckDnsRecord::class);

        $action->check($monitor);

        $this->assertFalse(RecordChangedNotification::wasDispatched());
    }

    #[Test]
    public function it_handles_change(): void
    {
        RecordChangedNotification::fake();

        $this->mock(ResolveRecord::class, function (MockInterface $mock): void {
            $mock->shouldReceive('resolve')->with(Type::A, 'govigilant.io')->andReturn('127.0.0.2');
        });

        $monitor = DnsMonitor::query()->create([
            'type' => Type::A,
            'record' => 'govigilant.io',
            'value' => '127.0.0.1',
        ]);

        /** @var CheckDnsRecord $action */
        $action = app(CheckDnsRecord::class);

        $action->check($monitor);

        $this->assertTrue(RecordChangedNotification::wasDispatched());
        $this->assertEquals(1, $monitor->history->count());
        $this->assertEquals('127.0.0.1', $monitor->history->first()?->value);
    }
}
