<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\User;

class EmailQuoteService extends EmailService
{

    /**
     * @param Quote $quote
     * @return void
     */
    public function send( Quote $quote )
    {
        $user = User::find($quote->user_id);

        $body_data = array_merge([
            'name' => $quote->stock->name,
            'symbol' => $quote->stock->symbol,
        ], $quote->only([ 'open', 'high', 'low', 'close' ]));

        // -- prepares the email body string
        array_walk($body_data, fn (&$i, $k) => $i = "$k => {$i}\n" );

        $this->AMQPSendEmail([
            'recipient' => [ 'toName' => $user->name, 'toEmail' => $user->email ],
            'body' => "Dear {$user->name},\n\nYou requested this quote on {$quote->date}, it follows below:\n\n".implode("", $body_data)
        ]);

    }

}