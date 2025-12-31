# Authorization

This guide will walk you through creating a Discord application, installing it, and configuring authorization for the Discord Client.

## Creating a Discord Application

1. Go to [Discord Developer Portal](https://discord.com/developers/applications) and create a new application.

2. From the **General Information** page, grab your:
   - **Application ID**
   - **Public Key**

3. Configure installation settings:
   - In the left sidebar, go to **Installation**
   - Ensure both **"User Install"** and **"Guild Install"** are selected
   - Under **"Install Link"**, choose **"Discord Provided Link"**
   - A new **Default Install Settings** section will appear
   - For guild install, add the **"bot"** scope and permission to send messages or any other action you need.

## Installing Your Application

### Install to Server (Guild Install)

1. Browse to the installation link of your application
2. Click **"Add to Server"**
3. Select the server where you want to install the bot
4. Authorize the requested permissions

You should see the app in the server member's list. Once you have added commands to your app, you should see them after typing `/` in the server channel.

### Install to User Account (User Install)

1. Browse to the installation link of your application
2. Click **"Add to My Apps"**
3. Authorize the requested permissions

Once you have added commands to your app, you can go to a DM and type `/` to see the commands.

## Using the Discord Client

The `DiscordClient` requires an `Authorization` object to authenticate requests. There are two ways to authorize against Discord:

1. **Bot Token** - Direct authentication using a bot token
2. **Bearer Token (OAuth2)** - Authentication using OAuth2 client credentials flow

Once you have configured your authorization, pass it to the `DiscordClient` constructor:

```php
use EasyHttp\DiscordClient\Authorization;
use EasyHttp\DiscordClient\Constants\AuthorizationType;
use EasyHttp\DiscordClient\DiscordClient;

$authorization = new Authorization();
// Configure authorization (see methods below)

$client = new DiscordClient($authorization);
```

### Authorization Methods

#### Bot Token Authorization

Bot tokens are used when you want to authenticate as a bot user. This is the most common method for bot applications.

**Prerequisites**
- Have an application created in Discord.

**Setup:**
1. Go to [Discord Developer Portal](https://discord.com/developers/applications) and click in one of your
   existing applications.
2. In the left sidebar, navigate to **Bot**:
3. Click "Reset Token" to generate a new bot token
4. Configure the authorization with the bot token (see example below)

**Example:**

```php
use EasyHttp\DiscordClient\Authorization;
use EasyHttp\DiscordClient\Constants\AuthorizationType;
use EasyHttp\DiscordClient\DiscordClient;

$authorization = new Authorization();
$authorization->setAuthorization(AuthorizationType::BOT_TOKEN, 'your-bot-token-here');

$client = new DiscordClient($authorization);
```

**When to use:**
- Building Discord bots
- Need to perform actions as a bot user
- Most common use case for server-side applications

#### Bearer Token Authorization (OAuth2 Client Credentials)

Bearer tokens are obtained through OAuth2 client credentials flow. This method is useful when you need to authenticate as an application rather than a bot user.

**Prerequisites**
- Have an application created in Discord.

**Setup:**
1. Go to [Discord Developer Portal](https://discord.com/developers/applications) and click in one of your
   existing applications.
2. In the left sidebar, navigate to **OAuth2**:
3. Click "Reset Secret" to generate a new **Client Secret**. Grab this value and **Client ID**.
4. Request an access token using the `requestAccessToken()` method
5. Use the bearer token from the response (see example below)

**Example:**

```php
use EasyHttp\DiscordClient\Authorization;
use EasyHttp\DiscordClient\Constants\AuthorizationType;
use EasyHttp\DiscordClient\DiscordClient;

// Configure authorization using client credentials
$authorization = new Authorization();
$authorization->setClientCredentials('your-client-id', 'your-client-secret');

// Request access token with required scopes
$client = new DiscordClient($authorization);
$response = $client->requestAccessToken($['identify', 'email']);
$tokenData = $response->parseJson();

// Configure authorization with the bearer token
$authorization->setAuthorization(AuthorizationType::BEARER_TOKEN, $tokenData['access_token']);

// Now you can use the client with bearer token authentication
$client = new DiscordClient($authorization);
```

**Alternative: Direct Bearer Token**

If you already have a bearer token (e.g., from a previous OAuth2 flow), you can set it directly:

```php
$authorization = new Authorization();
$authorization->setAuthorization(AuthorizationType::Bearer_Token, 'your-bearer-token-here');

$client = new DiscordClient($authorization);
```

**When to use:**
- Authenticating as an application (not a bot user)
- Using OAuth2 client credentials grant flow
- Need application-level permissions
