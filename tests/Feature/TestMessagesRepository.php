<?php

namespace Tests\Feature;

use App\Services\MessagesRepository;
use App\Transformers\MessageTransformer;
use Tests\TestCase;

class TestMessagesRepository extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $messagesRepository = new MessagesRepository();
        $messages = $messagesRepository->getUserMessages(3, 0, 50);
        $this->assertGreaterThan( 0, count($messages));
        $t = new MessageTransformer();

        $count = 0;
        foreach ($messages as $m ) {
            $count++;

            $messageData = $t->transform($m);
            echo join ( '|', $messageData);

            echo  $m->id . "\n";

            if ( $count == 10 ) {
                break;
            }
        }

        $this->assertTrue(true);
    }
}
