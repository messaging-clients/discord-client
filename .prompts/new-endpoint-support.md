# Implement New Discord API Endpoint

## Option 1: Specific Endpoint

Implement the following endpoint using the implementation pattern in `.cursor/rules/endpoint-implementation-pattern.md`:

```
{HTTP_METHOD} {endpoint_path}
```

Example:
```
DELETE /applications/{application.id}/commands/{command.id}
```

## Option 2: Automatic Discovery

Check available endpoints at https://discord.com/developers/docs/interactions/application-commands (or relevant section) and compare with existing implementations in `src/DiscordClient.php`. Select one endpoint with one HTTP method to implement.

## Workflow

1. **Implement** following `.cursor/rules/endpoint-implementation-pattern.md`:
   - Add method to `src/DiscordClient.php`
   - Create test in `tests/Integration/DiscordClient/{Feature}Test.php`
   - Create example in `examples/{category}/{feature_name}.php`

2. **Run tests**:
```bash
docker exec \
    -w /var/www/vhosts/easy-http/discord-client web_app \
    vendor/bin/phpunit \
    --filter "{TestClassName}"
```

3. **Fix code style**:
```bash
docker exec \
    -w /var/www/vhosts/easy-http/discord-client web_app \
    composer phpcs:fix
```

4. **Create feature branch**:
```bash
git checkout -b feature/{endpoint-description}
```

5. **Commit**:
```bash
git add .
git commit -m "feat: support {HTTP_METHOD} {endpoint_path} endpoint"
```

6. **Push**:
```bash
git push -u origin feature/{branch-name}
```

7. **Create Pull Request** using GitHub MCP:
   - Use `mcp_github_get_me` to get username if needed
   - Use `mcp_github_create_pull_request` with:
     - `owner`: Repository owner
     - `repo`: `discord-client` (or check actual repo name)
     - `title`: "feat: Add {HTTP_METHOD} {endpoint_path} endpoint"
     - `head`: Feature branch name
     - `base`: Target branch (usually `main` or `master`)
     - `body`: Description with reference to Discord API docs

