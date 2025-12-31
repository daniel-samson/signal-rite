#!/bin/bash
# Firewall initialization script for Claude Code container
# Based on https://github.com/anthropics/claude-code/.devcontainer

set -e

echo "Initializing firewall rules..."

# Allow loopback
iptables -A INPUT -i lo -j ACCEPT
iptables -A OUTPUT -o lo -j ACCEPT

# Allow established connections
iptables -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
iptables -A OUTPUT -m state --state ESTABLISHED,RELATED -j ACCEPT

# Allow DNS (port 53)
iptables -A OUTPUT -p udp --dport 53 -j ACCEPT
iptables -A OUTPUT -p tcp --dport 53 -j ACCEPT

# Allow HTTPS (port 443) for API access
iptables -A OUTPUT -p tcp --dport 443 -j ACCEPT

# Allow HTTP (port 80) for package downloads
iptables -A OUTPUT -p tcp --dport 80 -j ACCEPT

# Allow SSH (port 22) for git operations
iptables -A OUTPUT -p tcp --dport 22 -j ACCEPT

# Create ipset for allowed domains
ipset create allowed-domains hash:net 2>/dev/null || ipset flush allowed-domains

# Add Anthropic API ranges
for ip in $(dig +short api.anthropic.com); do
    ipset add allowed-domains "$ip/32" 2>/dev/null || true
done

# Add npm registry
for ip in $(dig +short registry.npmjs.org); do
    ipset add allowed-domains "$ip/32" 2>/dev/null || true
done

# Add GitHub API ranges
GITHUB_META=$(curl -s https://api.github.com/meta 2>/dev/null || echo '{}')
if [ -n "$GITHUB_META" ] && [ "$GITHUB_META" != "{}" ]; then
    echo "$GITHUB_META" | jq -r '.git[]?, .api[]?, .web[]?' 2>/dev/null | while read -r cidr; do
        if [ -n "$cidr" ]; then
            ipset add allowed-domains "$cidr" 2>/dev/null || true
        fi
    done
fi

echo "Firewall rules initialized successfully."
