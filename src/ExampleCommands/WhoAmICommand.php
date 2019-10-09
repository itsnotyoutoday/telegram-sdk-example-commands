<?php

/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Written by Marco Boretto <marco.bore@gmail.com>
 */


namespace Telegram\Bot\ExampleCommands;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\PhotoSize;

/**
 * User "/whoami" command
 *
 * Simple command that returns info about the current user.
 */
class WhoAmICommand extends Command

{

    /**
     * @var string
     */

    protected $name = 'whoami';


    /**
     * @var string
     */

    protected $description = 'Show your id, name and username';



    /**
     * @var bool
     */

    protected $private_only = true;


    public function handle($arguments)

    {
        $update = $this->getUpdate();

        $message = $update->getMessage();
        $from = $message->getFrom();
        $user_id = $from->getId();
        $chat_id = $message->getChat()->getId();
        $message_id = $message->getMessageId();

        $data = [
            'chat_id'             => $chat_id,
            'reply_to_message_id' => $message_id,
        ];

        $this->telegram->sendChatAction(['chat_id' => $chat_id, 'action' => 'typing']);

        $caption = sprintf(
            'Your Id: %d' . PHP_EOL .
            'Name: %s %s' . PHP_EOL .
            'Username: %s',
            $user_id,
            $from->getFirstName(),
            $from->getLastName(),
            $from->getUsername()
        );

        $response = $this->telegram->getUserProfilePhotos(['user_id' => $user_id, 'limit' => 10, 'offset' => null]);
        if($response->getTotalCount()>0) {
            /** @var [PhotoSize::class] $photos */
            if($response->getTotalCount()) {
                $photos = $response->getPhotos()->get(0);
                /** @var  [$photos] PhotoSize:class */
                // This really should work like getFileId()..
                // Need to look into why this doesn't work right
                    $data['photo'] = $photos[0]['file_id'];
                    $data['caption'] = $caption;
                    $result = $this->telegram->sendPhoto($data);
                    $this->telegram->getFile(['file_id' => $file_id]);
                    return $result;

            } else {
                $data['text'] = 'Total Photos => '.count($photos);
                $this->telegram->sendMessage($data);
            }
        }
        $data['text'] = $caption;
        $this->telegram->sendMessage($data);
    }
}
