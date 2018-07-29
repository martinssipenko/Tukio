<?php
declare(strict_types=1);

namespace Crell\Tukio;


use PHPUnit\Framework\TestCase;

class RegisterableListenerProviderServiceTest extends TestCase
{
    /** @var MockContainer */
    protected $mockContainer;

    public function setUp()
    {
        parent::setUp();

        $container = new MockContainer();

        $container->addService('A', new class
        {
            public function listen(CollectingEvent $event)
            {
                $event->add('A');
            }
        });
        $container->addService('B', new class
        {
            public function listen(CollectingEvent $event)
            {
                $event->add('B');
            }
        });
        $container->addService('C', new class
        {
            public function listen(CollectingEvent $event)
            {
                $event->add('C');
            }
        });
        $container->addService('R', new class
        {
            public function listen(CollectingEvent $event)
            {
                $event->add('R');
            }
        });
        $container->addService('E', new class
        {
            public function listen(CollectingEvent $event)
            {
                $event->add('E');
            }
        });
        $container->addService('L', new class
        {
            public function hear(CollectingEvent $event)
            {
                $event->add('L');
            }
        });

        $this->mockContainer = $container;
    }

    public function test_add_listener_service(): void
    {
        $p = new RegisterableListenerProvider($this->mockContainer);

        $p->addListenerService('L', 'hear', CollectingEvent::class, 70);
        $p->addListenerService('E', 'listen', CollectingEvent::class, 80);
        $p->addListenerService('C', 'listen', CollectingEvent::class, 100);
        $p->addListenerService('L', 'hear', CollectingEvent::class); // Defaults to 0
        $p->addListenerService('R', 'listen', CollectingEvent::class, 90);

        $event = new CollectingEvent();

        foreach ($p->getListenersForEvent($event) as $listener) {
            $listener($event);
        }

        $this->assertEquals('CRELL', implode($event->result()));
    }

    public function test_service_registration_fails_without_container(): void
    {
        $this->expectException(ContainerMissingException::class);

        $p = new RegisterableListenerProvider();

        $p->addListenerService('L', 'hear', CollectingEvent::class, 70);
    }


}
