<?php

namespace MessagingClients\DiscordClient\Constants;

enum InteractionContextType: int
{
    /**
     * Interaction can be used within servers (guilds)
     */
    case GUILD = 0;

    /**
     * Interaction can be used within DMs with the app's bot user
     */
    case BOT_DM = 1;

    /**
     * Interaction can be used within Group DMs and DMs other than the app's bot user
     */
    case PRIVATE_CHANNEL = 2;
}
