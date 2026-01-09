<?php

namespace MessagingClients\DiscordClient\Constants;

enum ApplicationCommandType: int
{
    /**
     * Slash commands; a text-based command that shows up when a user types /
     */
    case CHAT_INPUT = 1;

    /**
     * A UI-based command that shows up when you right-click or tap on a user
     */
    case USER = 2;

    /**
     * A UI-based command that shows up when you right-click or tap on a message
     */
    case MESSAGE = 3;
}
