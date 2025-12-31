# Claude Code Development Container
# Based on https://github.com/anthropics/claude-code/.devcontainer

FROM node:20-bookworm

ARG CLAUDE_CODE_VERSION=latest

# Set timezone
ENV TZ=UTC
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Install essential tools
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    wget \
    vim \
    nano \
    jq \
    sudo \
    ca-certificates \
    iptables \
    ipset \
    iproute2 \
    dnsutils \
    && rm -rf /var/lib/apt/lists/*

# Setup node user with sudo access
RUN echo "node ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers.d/node

# Create workspace and config directories
RUN mkdir -p /workspace /home/node/.claude \
    && chown -R node:node /workspace /home/node/.claude

# Install Claude Code CLI
RUN if [ "$CLAUDE_CODE_VERSION" = "latest" ]; then \
        npm install -g @anthropic-ai/claude-code; \
    else \
        npm install -g @anthropic-ai/claude-code@$CLAUDE_CODE_VERSION; \
    fi

# Copy firewall init script
COPY init-firewall.sh /usr/local/bin/init-firewall.sh
RUN chmod +x /usr/local/bin/init-firewall.sh

# Set environment
ENV DEVCONTAINER=true
ENV EDITOR=nano

# Switch to non-root user
USER node
WORKDIR /workspace

# Default command - run claude with dangerous skip permissions for automation
CMD ["claude", "--dangerously-skip-permissions"]
