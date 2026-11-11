#!/usr/bin/env bash
# Haspr API Packaging Script (Debian & RPM)
# Generates haspr-api-ce Debian package and RPM package

set -euo pipefail

VERSION="${1:-5.17.0}"
# Strip leading 'v' if present
VERSION="${VERSION#v}"

echo "=== Building Haspr API CE packages for version ${VERSION} ==="

WORKDIR="$(pwd)"
BUILD_DIR="${WORKDIR}/build/package"
STAGE_DIR="${BUILD_DIR}/usr/share/php/haspr-ce"
DEBIAN_DIR="${BUILD_DIR}/DEBIAN"

rm -rf "${BUILD_DIR}"
mkdir -p "${STAGE_DIR}" "${DEBIAN_DIR}" "${BUILD_DIR}/usr/bin"

# 1. Copy application files into stage directory
echo "Copying application source files..."
tar --exclude='.git' \
    --exclude='.github' \
    --exclude='.ddev' \
    --exclude='build' \
    --exclude='tests' \
    --exclude='tmp/*' \
    --exclude='logs/*' \
    -cf - . | (cd "${STAGE_DIR}" && tar -xf -)

# 2. Ensure tmp and logs directories exist
mkdir -p "${STAGE_DIR}/tmp/cache/models" \
         "${STAGE_DIR}/tmp/cache/persistent" \
         "${STAGE_DIR}/tmp/cache/views" \
         "${STAGE_DIR}/logs"

# 3. Create symlink for CLI tool
ln -sf /usr/share/php/haspr-ce/bin/cake "${BUILD_DIR}/usr/bin/haspr-cake"

# 4. Generate Debian control file
cat << EOF > "${DEBIAN_DIR}/control"
Package: haspr-api-ce
Version: ${VERSION}
Section: web
Priority: optional
Architecture: all
Maintainer: Haspr Contributors <contact@haspr.dev>
Depends: php-cli (>= 8.1), php-fpm (>= 8.1), php-gnupg, php-intl, php-mbstring, php-xml, php-pdo
Provides: passbolt-ce-server
Replaces: passbolt-ce-server
Conflicts: passbolt-ce-server
Homepage: https://github.com/haspr-dev/haspr-api
Description: Haspr Community Edition backend API server
 Official backend API server package for Haspr, compatible with Passbolt.
EOF

# 5. Build Debian package
echo "Building Debian package..."
dpkg-deb --build --root-owner-group "${BUILD_DIR}" "${WORKDIR}/haspr-api-ce_${VERSION}_all.deb"
echo "Debian package created: haspr-api-ce_${VERSION}_all.deb"

# 6. Build RPM package if nfpm is available
if command -v nfpm >/dev/null 2>&1; then
  echo "Building RPM package with nfpm..."
  cat << EOF > "${WORKDIR}/build/nfpm-api.yaml"
name: haspr-api-ce
arch: noarch
platform: linux
version: "${VERSION}"
release: "1"
section: web
priority: optional
maintainer: Haspr Contributors <contact@haspr.dev>
description: Haspr Community Edition backend API server
homepage: https://github.com/haspr-dev/haspr-api
license: AGPL-3.0
provides:
  - passbolt-ce-server
replaces:
  - passbolt-ce-server
conflicts:
  - passbolt-ce-server
contents:
  - src: ${STAGE_DIR}
    dst: /usr/share/php/haspr-ce
  - src: /usr/share/php/haspr-ce/bin/cake
    dst: /usr/bin/haspr-cake
    type: symlink
EOF
  nfpm pkg --config "${WORKDIR}/build/nfpm-api.yaml" --target "${WORKDIR}/haspr-api-ce-${VERSION}-1.noarch.rpm"
  echo "RPM package created: haspr-api-ce-${VERSION}-1.noarch.rpm"
else
  echo "Notice: nfpm not found, skipping RPM package generation."
fi

echo "=== Packaging complete ==="
