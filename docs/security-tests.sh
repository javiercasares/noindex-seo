#!/bin/bash
#
# Security Testing Script for noindex SEO Plugin
# Version: 1.0
# Date: 2026-01-20
#
# This script performs automated security checks on the plugin.
# Run from the plugin root directory: bash docs/security-tests.sh
#

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Counters
PASS=0
FAIL=0
WARN=0

echo -e "${BLUE}=================================${NC}"
echo -e "${BLUE}noindex SEO Security Test Suite${NC}"
echo -e "${BLUE}=================================${NC}"
echo ""

# Function to print test results
print_pass() {
    echo -e "${GREEN}[PASS]${NC} $1"
    ((PASS++))
}

print_fail() {
    echo -e "${RED}[FAIL]${NC} $1"
    ((FAIL++))
}

print_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
    ((WARN++))
}

print_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "noindex-seo.php" ]; then
    echo -e "${RED}Error: noindex-seo.php not found. Run this script from the plugin root directory.${NC}"
    exit 1
fi

echo "Starting security tests..."
echo ""

#######################
# TEST 1: Direct File Access Protection
#######################
print_info "Test 1: Checking direct file access protection..."

if grep -q "defined( 'ABSPATH' )" noindex-seo.php; then
    print_pass "noindex-seo.php has direct access protection"
else
    print_fail "noindex-seo.php missing direct access protection"
fi

if [ -f "uninstall.php" ]; then
    if grep -q "defined( 'ABSPATH' )" uninstall.php && grep -q "defined( 'WP_UNINSTALL_PLUGIN' )" uninstall.php; then
        print_pass "uninstall.php has proper uninstall protection"
    else
        print_fail "uninstall.php missing proper uninstall protection"
    fi
fi

echo ""

#######################
# TEST 2: Dangerous Functions
#######################
print_info "Test 2: Checking for dangerous functions..."

DANGEROUS_FUNCTIONS="eval exec system passthru shell_exec popen proc_open"
FOUND_DANGEROUS=0

for func in $DANGEROUS_FUNCTIONS; do
    if grep -q "\b$func\s*(" noindex-seo.php 2>/dev/null; then
        print_fail "Found dangerous function: $func"
        FOUND_DANGEROUS=1
    fi
done

if [ $FOUND_DANGEROUS -eq 0 ]; then
    print_pass "No dangerous functions found"
fi

echo ""

#######################
# TEST 3: SQL Injection (Direct Queries)
#######################
print_info "Test 3: Checking for direct SQL queries..."

if grep -qE "\\\$wpdb->(query|get_|prepare)" noindex-seo.php; then
    print_warn "Found direct database queries (review for SQL injection)"
else
    print_pass "No direct SQL queries found (uses Options API)"
fi

echo ""

#######################
# TEST 4: XSS - Output Escaping
#######################
print_info "Test 4: Checking output escaping..."

# Check for echo with variables that might not be escaped
UNESCAPED=$(grep -n "echo.*\$" noindex-seo.php | grep -v "esc_html\|esc_attr\|esc_url\|esc_js\|wp_kses" | wc -l)

if [ "$UNESCAPED" -gt 0 ]; then
    print_warn "Found $UNESCAPED potentially unescaped echo statements (manual review needed)"
    grep -n "echo.*\$" noindex-seo.php | grep -v "esc_html\|esc_attr\|esc_url\|esc_js\|wp_kses" | head -5
else
    print_pass "All echo statements appear to be properly escaped"
fi

echo ""

#######################
# TEST 5: CSRF Protection (Nonces)
#######################
print_info "Test 5: Checking CSRF protection..."

if grep -q "wp_nonce_field" noindex-seo.php && grep -q "check_admin_referer" noindex-seo.php; then
    print_pass "CSRF protection (nonces) implemented"
else
    print_fail "Missing CSRF protection"
fi

echo ""

#######################
# TEST 6: Capability Checks
#######################
print_info "Test 6: Checking capability verification..."

if grep -q "current_user_can.*manage_options" noindex-seo.php; then
    print_pass "Capability checks found for manage_options"
else
    print_warn "No capability checks found"
fi

# Check if noindex_seo_admin has capability check
if grep -A 5 "function noindex_seo_admin" noindex-seo.php | grep -q "current_user_can"; then
    print_pass "noindex_seo_admin() has capability check"
else
    print_fail "noindex_seo_admin() missing capability check (Vulnerability #1)"
fi

echo ""

#######################
# TEST 7: Input Sanitization
#######################
print_info "Test 7: Checking input sanitization..."

# Check for $_POST usage
POST_USAGE=$(grep -n "\$_POST" noindex-seo.php | wc -l)
if [ "$POST_USAGE" -gt 0 ]; then
    # Check if they're sanitized
    SANITIZED_POST=$(grep -B 1 -A 1 "\$_POST" noindex-seo.php | grep -c "sanitize_\|wp_unslash\|absint\|intval" || true)

    if [ "$SANITIZED_POST" -lt "$POST_USAGE" ]; then
        print_warn "Found $_POST usage that may not be properly sanitized"
        print_info "  $_POST occurrences: $POST_USAGE"
        print_info "  Sanitization calls nearby: $SANITIZED_POST"
    else
        print_pass "All $_POST usage appears to be sanitized"
    fi
fi

# Check for $_GET usage
GET_USAGE=$(grep -n "\$_GET" noindex-seo.php | wc -l)
if [ "$GET_USAGE" -gt 0 ]; then
    SANITIZED_GET=$(grep -B 1 -A 1 "\$_GET" noindex-seo.php | grep -c "sanitize_\|wp_unslash\|absint\|intval" || true)

    if [ "$SANITIZED_GET" -lt "$GET_USAGE" ]; then
        print_warn "Found $_GET usage that may not be properly sanitized"
        print_info "  $_GET occurrences: $GET_USAGE"
        print_info "  Sanitization calls nearby: $SANITIZED_GET"
    else
        print_pass "All $_GET usage appears to be sanitized"
    fi
fi

echo ""

#######################
# TEST 8: File Permissions
#######################
print_info "Test 8: Checking file permissions..."

# Check if any PHP files are executable
EXECUTABLE_PHP=$(find . -name "*.php" -type f -perm -111 2>/dev/null | wc -l)
if [ "$EXECUTABLE_PHP" -gt 0 ]; then
    print_warn "Found $EXECUTABLE_PHP executable PHP files (should not be executable)"
    find . -name "*.php" -type f -perm -111 2>/dev/null
else
    print_pass "No executable PHP files found"
fi

echo ""

#######################
# TEST 9: Hardcoded Credentials
#######################
print_info "Test 9: Checking for hardcoded credentials..."

if grep -iE "(password|passwd|pwd|api_key|apikey|secret|token)\s*=\s*['\"][^'\"]+['\"]" noindex-seo.php 2>/dev/null | grep -v "noindex-seo" | grep -q .; then
    print_fail "Found potential hardcoded credentials"
    grep -inE "(password|passwd|pwd|api_key|apikey|secret|token)\s*=\s*['\"][^'\"]+['\"]" noindex-seo.php | grep -v "noindex-seo"
else
    print_pass "No hardcoded credentials found"
fi

echo ""

#######################
# TEST 10: Code Quality with PHP_CodeSniffer
#######################
print_info "Test 10: Running PHP_CodeSniffer (if available)..."

if command -v phpcs &> /dev/null; then
    if phpcs --standard=WordPress --report=summary noindex-seo.php 2>&1 | grep -q "0 errors"; then
        print_pass "PHP_CodeSniffer: No errors found"
    else
        print_warn "PHP_CodeSniffer found issues (see details below)"
        phpcs --standard=WordPress --report=summary noindex-seo.php 2>&1 || true
    fi
elif [ -f "vendor/bin/phpcs" ]; then
    if vendor/bin/phpcs --standard=WordPress --report=summary noindex-seo.php 2>&1 | grep -q "0 errors"; then
        print_pass "PHP_CodeSniffer: No errors found"
    else
        print_warn "PHP_CodeSniffer found issues (see details below)"
        vendor/bin/phpcs --standard=WordPress --report=summary noindex-seo.php 2>&1 || true
    fi
else
    print_info "PHP_CodeSniffer not found - skipping (install with: composer install)"
fi

echo ""

#######################
# TEST 11: WordPress-Specific Security
#######################
print_info "Test 11: Checking WordPress security best practices..."

# Check for wp_safe_redirect vs wp_redirect
if grep -q "wp_redirect" noindex-seo.php && ! grep -q "wp_safe_redirect" noindex-seo.php; then
    print_warn "Using wp_redirect instead of wp_safe_redirect"
elif grep -q "wp_safe_redirect" noindex-seo.php; then
    print_pass "Using wp_safe_redirect for safe redirections"
fi

# Check for prepare() on wpdb queries
if grep -qE "\\\$wpdb->query\s*\(" noindex-seo.php; then
    if grep -qE "\\\$wpdb->prepare\s*\(" noindex-seo.php; then
        print_pass "Using prepared statements for database queries"
    else
        print_fail "Found unprepared database queries"
    fi
fi

echo ""

#######################
# TEST 12: Composer Dependencies Audit
#######################
print_info "Test 12: Checking Composer dependencies (if available)..."

if [ -f "composer.lock" ] && command -v composer &> /dev/null; then
    print_info "Running composer audit..."
    if composer audit --no-interaction 2>&1 | grep -qi "no security vulnerability advisories"; then
        print_pass "Composer audit: No known vulnerabilities"
    else
        print_warn "Composer audit found potential vulnerabilities"
        composer audit --no-interaction 2>&1 || true
    fi
else
    print_info "Composer not available or no composer.lock found - skipping"
fi

echo ""

#######################
# TEST 13: Check for Unused Dependencies
#######################
print_info "Test 13: Checking for potentially unused dependencies..."

if [ -f "composer.json" ]; then
    if grep -q "symfony/http-client\|auth0/wordpress" composer.json; then
        # Check if these dependencies are actually used in the code
        if ! grep -qr "Symfony\|Auth0" noindex-seo.php uninstall.php 2>/dev/null; then
            print_warn "Found dependencies in composer.json that don't appear to be used in main files"
            print_info "  Consider auditing: symfony/http-client, auth0/wordpress"
        else
            print_pass "Dependencies appear to be used"
        fi
    fi
fi

echo ""

#######################
# Summary
#######################
echo -e "${BLUE}=================================${NC}"
echo -e "${BLUE}Test Summary${NC}"
echo -e "${BLUE}=================================${NC}"
echo -e "${GREEN}Passed: $PASS${NC}"
echo -e "${YELLOW}Warnings: $WARN${NC}"
echo -e "${RED}Failed: $FAIL${NC}"
echo ""

if [ $FAIL -eq 0 ]; then
    if [ $WARN -eq 0 ]; then
        echo -e "${GREEN}✓ All security tests passed!${NC}"
        exit 0
    else
        echo -e "${YELLOW}⚠ Tests passed with warnings. Review warnings above.${NC}"
        exit 0
    fi
else
    echo -e "${RED}✗ Some security tests failed. Please review and fix the issues above.${NC}"
    exit 1
fi
