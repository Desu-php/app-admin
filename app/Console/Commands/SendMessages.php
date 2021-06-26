<?php

namespace App\Console\Commands;

use App\Models\LogMessage;
use App\Models\Message;
use App\Models\User;
use App\Models\Whatsapp;
use App\Services\Sbis;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class SendMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $logMessage = LogMessage::orderBy('message_id', 'DESC')->first();

        $message = Message::whereHas('user.whatsapp', function (Builder $builder) {
            $builder->where('status', Whatsapp::ENABLED);
        });

        $users = User::whereHas('whatsapp', function (Builder $builder) {
            $builder->where('status', Whatsapp::ENABLED);
        })->has('sbis')->with('sbis')->get();

        if (!is_null($logMessage)) {
            $message->where('id', '>', $logMessage->message_id);
        }

        $messages = $message->get();

        foreach ($users as $user) {
            $sbis = new Sbis($user->sbis->app_client_id, $user->sbis->app_secret, $user->sbis->secret_key);
            $sbis->getThemes();

            $messages = $messages->where('user_id', $user->id)->groupBy('chatId');

            $counter = 0;
            foreach ($messages as $key => $messageData) {
                foreach ($messageData as $message) {

                    $counter++;
                }
            }
        }

        return 0;
    }
}
