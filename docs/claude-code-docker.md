# Claude Code Docker Setup

This project includes a Docker container for running [Claude Code](https://github.com/anthropics/claude-code), Anthropic's official CLI for Claude.

## Configuration

### Environment Variables

Add your Anthropic API key to `.env`:

```bash
ANTHROPIC_API_KEY=your-anthropic-api-key-here
```

### Container Details

- **Base image:** Node.js 20 (Debian Bookworm)
- **Working directory:** `/app` (mapped to project root)
- **Config persistence:** `/home/node/.claude` (stored in `claude-config` volume)
- **Flag:** `--dangerously-skip-permissions` enabled for automation

## Usage

### Build the container

```bash
docker-compose build claude-code
```

### Run interactively

```bash
docker-compose run --rm claude-code
```

This starts Claude Code in interactive mode with access to the project files.

### Run in background

```bash
docker-compose up -d claude-code
docker-compose exec claude-code claude --dangerously-skip-permissions
```

### Execute a single command

```bash
docker-compose run --rm claude-code claude "explain the Charge entity"
```

## Files

| File | Description |
|------|-------------|
| `docker/claude-code/Dockerfile` | Container definition with Node.js and Claude CLI |
| `docker/claude-code/init-firewall.sh` | Optional firewall script for network restrictions |

## Security Notes

The `--dangerously-skip-permissions` flag bypasses Claude Code's permission prompts. This is useful for:
- Automated workflows
- CI/CD pipelines
- Development environments where you trust the container

For production or sensitive environments, consider running without this flag to enable permission prompts.

## Troubleshooting

### API Key not working

Ensure your `.env` file contains a valid `ANTHROPIC_API_KEY`:

```bash
# Check if the key is loaded
docker-compose run --rm claude-code printenv ANTHROPIC_API_KEY
```

### Permission denied errors

The container runs as the `node` user. If you encounter permission issues with mounted files:

```bash
# Fix ownership on Linux/WSL
sudo chown -R 1000:1000 .
```

### Container won't start

Rebuild the container after Dockerfile changes:

```bash
docker-compose build --no-cache claude-code
```
