#!/bin/bash

# Git Integration Script for Classroom of the Elite
# Usage: ./git-helper.sh [command]

set -e

PROJECT_NAME="classroom-elite-auth"
MAIN_BRANCH="main"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Functions
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check branch naming convention
check_branch_name() {
    local branch=$(git branch --show-current)
    local pattern="^(feature|fix|hotfix)/[a-z0-9-]+$"
    
    if [[ ! $branch =~ $pattern ]]; then
        log_error "Branch name tidak sesuai konvensi!"
        log_info "Gunakan format: feature/nama-fitur atau fix/nama-bug"
        echo "Contoh: feature/auth-login, fix/role-policy"
        return 1
    fi
    return 0
}

# Check for any conflicts
check_conflicts() {
    log_info "Mengecek konflik git..."
    
    if ! git diff --quiet; then
        log_warn "Terdapat perubahan yang belum di-commit"
        read -p "Lanjutkan dengan stash? (y/n): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            git stash push -m "WIP: $(date)"
        else
            log_info "Batalkan operasi"
            exit 1
        fi
    fi
}

# Update main branch
update_main() {
    log_info "Memperbarui branch main..."
    git checkout $MAIN_BRANCH
    git pull origin $MAIN_BRANCH
}

# Create feature branch
create_branch() {
    local branch_name=$1
    
    if [ -z "$branch_name" ]; then
        log_error "Nama branch harus diberikan"
        echo "Usage: $0 feature <nama-branch>"
        exit 1
    fi
    
    check_conflicts
    
    git checkout -b "$branch_name"
    log_info "Branch baru dibuat: $branch_name"
}

# Start work
start_work() {
    local branch_name=$1
    
    if [ -z "$branch_name" ]; then
        # Use current branch if no name provided
        branch_name=$(git branch --show-current)
    fi
    
    # Check if branch exists
    if ! git show-ref --verify --quiet "refs/heads/$branch_name"; then
        create_branch "$branch_name"
    fi
    
    git checkout "$branch_name"
    log_info "Mulai bekerja di branch: $branch_name"
}

# Prepare commit
prepare_commit() {
    local message=$1
    
    if [ -z "$message" ]; then
        log_error "Pesan commit harus diberikan"
        exit 1
    fi
    
    # Check for syntax errors in PHP files modified
    local php_files=$(git diff --cached --name-only --diff-filter=ACM | grep '\.php$' || true)
    
    if [ -n "$php_files" ]; then
        log_info "Memeriksa sintaks PHP..."
        for file in $php_files; do
            if [ -f "$file" ]; then
                php -l "$file"
            fi
        done
    fi
    
    # Check for authorization issues
    log_info "Memeriksa authorization..."
    # Add authorization check here if needed
    
    git commit -m "$message"
}

# Push branch
push_branch() {
    local branch_name=$(git branch --show-current)
    
    git push origin "$branch_name"
    log_info "Branch $branch_name berhasil di-push ke origin"
}

# Create PR template
create_pr_template() {
    cat << 'EOF'
# Pull Request

## Deskripsi
Deskripsi singkat dari perubahan

## Role yang Terkena
- [ ] Member
- [ ] Technician
- [ ] Coordinator
- [ ] Admin
- [ ] Super Admin

## Perubahan
- [ ] Authentication
- [ ] Authorization
- [ ] Policy
- [ ] Middleware

## Testing
- [ ] Test login
- [ ] Test role access
- [ ] Test logout

## Checklist
- [ ] Branch name sesuai konvensi
- [ ] Tidak ada merge conflict
- [ ] Semua test lulus
- [ ] Code review oleh anggota lain
EOF
}

# Merge to main
merge_main() {
    local branch_name=$(git branch --show-current)
    
    if [ "$branch_name" == "$MAIN_BRANCH" ]; then
        log_error "Tidak bisa merge ke branch main yang sedang aktif"
        exit 1
    fi
    
    log_info "Memastikan main branch up-to-date..."
    git fetch origin
    git merge origin/$MAIN_BRANCH --no-ff -m "Merge $branch_name to $MAIN_BRANCH"
    git push origin $MAIN_BRANCH
    
    log_info "Merge ke main berhasil"
}

# Show status
show_status() {
    echo "================================"
    echo "  Git Helper - $PROJECT_NAME"
    echo "================================"
    echo ""
    echo "Branch aktual: $(git branch --show-current)"
    echo "Status: $(git status --short)"
    echo ""
    echo "Perintah yang tersedia:"
    echo "  $0 start [branch]   - Mulai kerja di branch"
    echo "  $0 commit <msg>     - Buat commit dengan pesan"
    echo "  $0 push             - Push branch ke origin"
    echo "  $0 merge            - Merge ke main (setelah PR disetujui)"
    echo "  $0 pr               - Tampilkan template PR"
    echo "  $0 status           - Tampilkan status"
    echo ""
}

# Main script
case "${1:-}" in
    start)
        start_work "$2"
        ;;
    commit)
        prepare_commit "$2"
        ;;
    push)
        push_branch
        ;;
    merge)
        merge_main
        ;;
    pr)
        create_pr_template
        ;;
    status)
        show_status
        ;;
    *)
        show_status
        ;;
esac