#!/bin/bash
# Setup script for CampusFix auth push to GitHub
# Run this after authenticating with GitHub

set -e

echo "CampusFix Auth - GitHub Push Setup"
echo "===================================="

# Check if we're in the project directory
if [ ! -f "composer.json" ]; then
    echo "Error: composer.json not found. Run this script from the project root."
    exit 1
fi

# Check git status
echo ""
echo "Checking git status..."
git status --porcelain | head -10

# Set up git identity
git config --global user.name "Team Member 1"
git config --global user.email "member1@campusfix.local"

# Add all files
echo ""
echo "Adding files..."
git add .

# Show what will be committed
echo ""
echo "Files to be committed:"
git status --staged --short | head -20

if [ $(git status --staged --short | wc -l) -gt 20 ]; then
    echo "... and $(git status --staged --short | wc -l) more files"
fi

echo ""
echo "Ready to commit!"
echo "Run the following commands:"
echo "  git commit -m 'feat: complete auth & role authorization system for CampusFix'"
echo "  git push -u origin member-1/auth-user"
echo ""
echo "If authentication required:"
echo "  1. Go to: https://github.com/login/device"
echo "  2. Enter code: DF22-F584"
echo "  3. Wait for approval"
echo "  4. Run: git push -u origin member-1/auth-user"