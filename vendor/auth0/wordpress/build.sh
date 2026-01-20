#!/bin/bash

# This script is used to build and package the plugin for distribution.
# It will clean up the environment, execute Composer, prefix dependencies, finalize the build, archive the build, and sign the archive.

function trim() {
    local str="$*"
    str="${str#"${str%%[![:space:]]*}"}"
    str="${str%"${str##*[![:space:]]}"}"
    echo "${str}"
}

function semver {
  local SEMVER_REGEX='^([0-9]+\.){2}(\*|[0-9]+)(-.*)?$'
  local version=$(trim $1)

  if [[ "$version" =~ $SEMVER_REGEX ]]; then
    if [ "$#" -eq 2 ]; then
      local major=${BASH_REMATCH[0]}
      local minor=${BASH_REMATCH[1]}
      local patch=${BASH_REMATCH[2]}
      local suffix=${BASH_REMATCH[3]}
      eval "$2=(\"$major\" \"$minor\" \"$patch\" \"$suffix\")"
    fi
  else
    echo "Error: version '$version' does not match the semver 'X.Y.Z' format."
    exit 1
  fi
}

printf 'What version are you building? '
read version
echo "Version: $version"
semver "$version" version
filename="Auth0_WordPress_${version}.zip"

echo "# Cleaning up environment..."
rm -f build.zip
rm -f build.zip.sig
rm -rf build
rm -rf vendor
rm composer.lock

echo "# Executing Composer..."
composer update --no-plugins

echo "# Prefixing Dependencies..."
vendor/bin/php-scoper add-prefix --force

echo "# Finalizing Build..."
cd build
composer update --no-dev --optimize-autoloader --no-plugins
rm composer.json
rm composer.lock
cd ..

echo "# Archiving Build..."
zip -vr ${filename} build/ -x "*.DS_Store"

echo "# Signing Build..."
openssl dgst -sign private-signing-key.pem -sha256 -out ${filename}.sig -binary ${filename}
