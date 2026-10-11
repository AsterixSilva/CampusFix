#!/usr/bin/env bash
# Create GitHub PR for member-1/auth-user
set -euo pipefail

REPO="AsterixSilva/CampusFix"
HEAD_BRANCH="member-1/auth-user"
BASE_BRANCH="main"
PR_TITLE="feat(auth): tambahkan integrasi lokalisi & pivot issue_user dari member 2"
PR_BODY="## Deskripsi

Integrasi schema lokasi & pivot issue_user dari member 2 ke branch auth milik member 1.

## Perubahan

- \`app/Models/Location.php\` — lokasi hierarkis (campus -> faculty -> building -> floor -> room/area)
- \`app/Models/Issue.php\` — relasi reporters() / affectedUsers() / followers() lewat pivot issue_user
- \`bootstrap/app.php\` — register role-based middleware (Laravel 11)
- \`bootstrap/providers.php\` — daftar service providers
- \`database/migrations/2026_10_10_102827_create_issue_user_table.php\` — migration pivot issue_user (reporter / affected / follower)

## Verifikasi

- Syntax PHP: `php -l` semua file
- 5 file baru siap commit & push
- Branch `member-1/auth-user` telah di-push ke origin"

# Check existing PRs with this head branch
echo "=== Existing PRs with head=member-1/auth-user ==="
EXISTING=$(curl -s "https://api.github.com/repos/$REPO/pulls" \
  -H "Accept: application/vnd.github+json" \
  -H "X-GitHub-Api-Version: 2022-11-28" \
  | python3 -c "import sys,json,sys; data=json.load(sys.stdin); [print(p['number'], p['state'], p['head'].get('ref') if isinstance(p.get('head'),dict) else 'NA', p['html_url']) for p in data]" 2>/dev/null || echo "no python")

if echo "$EXISTING" | grep -q "member-1/auth-user"; then
  echo "PR already exists. Showing:"
  echo "$EXISTING"
  exit 0
fi

echo "=== Creating PR ==="
RESPONSE=$(curl -s -X POST \
  "https://api.github.com/repos/$REPO/pulls" \
  -H "Accept: application/vnd.github+json" \
  -H "X-GitHub-Api-Version: 2022-11-28" \
  -H "Authorization: Bearer ${GITHUB_TOKEN:-}" \
  -H "Content-Type: application/json" \
  -d "{
    \"title\": \"$PR_TITLE\",
    \"head\": \"$HEAD_BRANCH\",
    \"base\": \"$BASE_BRANCH\",
    \"body\": \"$PR_BODY\"
  }")

echo "$RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$RESPONSE"
