<?php

namespace EasyHttp\DiscordClient\Constants;

enum AuthorizationType: int
{
    case BEARER_TOKEN = 1;
    case BOT_TOKEN = 2;
}
