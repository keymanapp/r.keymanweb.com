#!/usr/bin/env bash
## START STANDARD SITE BUILD SCRIPT INCLUDE
readonly THIS_SCRIPT="$(readlink -f "${BASH_SOURCE[0]}")"
readonly BOOTSTRAP="$(dirname "$THIS_SCRIPT")/resources/bootstrap.inc.sh"
BOOTSTRAP_VERSION="$(cat "$(dirname "THIS_SCRIPT")/resources/.bootstrap-required-version")" || exit 1
readonly BOOTSTRAP_VERSION
if ! [ -f "$BOOTSTRAP" ] || ! source "$BOOTSTRAP"; then
  curl -H "Cache-Control: no-cache" --fail --silent --show-error -w "curl: Finished attempt to download %{url}" "https://raw.githubusercontent.com/keymanapp/shared-sites/$BOOTSTRAP_VERSION/bootstrap.inc.sh" -o "$BOOTSTRAP.tmp" || exit 1
  source "$BOOTSTRAP.tmp"
  rm -f "$BOOTSTRAP.tmp"
fi
## END STANDARD SITE BUILD SCRIPT INCLUDE

readonly R_KEYMANWEB_CONTAINER_NAME=r-keymanweb-com-website
readonly R_KEYMANWEB_CONTAINER_DESC=r-keymanweb-com-app
readonly R_KEYMANWEB_IMAGE_NAME=r-keymanweb-com-website
readonly HOST_R_KEYMANWEB_COM=r.keymanweb.com.localhost

source _common/keyman-local-ports.inc.sh
source _common/docker.inc.sh

################################ Main script ################################

builder_describe \
  "Setup r.keymanweb.com site to run via Docker." \
  configure \
  clean \
  build \
  start \
  stop \
  test \
  verify_bootstrap_required_version \
  "--no-unit-test" \
  "--no-lint" \
  "--no-link-check" \
  "--no-php-error-check"

# --no-unit-test, --no-link-check not relevant for this script but kept for consistency
export DOCKER_NO_UNIT_TEST=1    # test_docker_container uses this
export DOCKER_NO_LINK_CHECK=1   # test_docker_container uses this

builder_parse "$@"

function do_test() {
  # TODO: unit tests for endpoints / redirects (see tavultesoft.com)
  test_docker_container $R_KEYMANWEB_CONTAINER_DESC $PORT_R_KEYMANWEB_COM /

  if ! builder_has_option --no-php-error-check; then
    docker_report_php_errors $R_KEYMANWEB_CONTAINER_DESC
  fi
}

builder_run_action configure  bootstrap_configure
builder_run_action clean      clean_docker_container $R_KEYMANWEB_IMAGE_NAME $R_KEYMANWEB_CONTAINER_NAME
builder_run_action stop       stop_docker_container  $R_KEYMANWEB_IMAGE_NAME $R_KEYMANWEB_CONTAINER_NAME
builder_run_action build      build_docker_container $R_KEYMANWEB_IMAGE_NAME $R_KEYMANWEB_CONTAINER_NAME $BUILDER_CONFIGURATION
builder_run_action start      start_docker_container $R_KEYMANWEB_IMAGE_NAME $R_KEYMANWEB_CONTAINER_NAME $R_KEYMANWEB_CONTAINER_DESC $HOST_R_KEYMANWEB_COM $PORT_R_KEYMANWEB_COM $BUILDER_CONFIGURATION

builder_run_action test       do_test
builder_run_action verify_bootstrap_required_version  docker_verify_bootstrap_required_version
