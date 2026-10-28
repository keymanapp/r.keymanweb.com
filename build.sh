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
readonly PORT_R_KEYMANWEB_COM=8062

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
  "--no-unit-test" \
  "--no-lint" \
  "--no-link-check"

builder_parse "$@"


builder_run_action configure  bootstrap_configure
builder_run_action clean      clean_docker_container $R_KEYMANWEB_IMAGE_NAME $R_KEYMANWEB_CONTAINER_NAME
builder_run_action stop       stop_docker_container  $R_KEYMANWEB_IMAGE_NAME $R_KEYMANWEB_CONTAINER_NAME
builder_run_action build      build_docker_container $R_KEYMANWEB_IMAGE_NAME $R_KEYMANWEB_CONTAINER_NAME $BUILDER_CONFIGURATION
builder_run_action start      start_docker_container $R_KEYMANWEB_IMAGE_NAME $R_KEYMANWEB_CONTAINER_NAME $R_KEYMANWEB_CONTAINER_DESC $HOST_R_KEYMANWEB_COM $PORT_R_KEYMANWEB_COM $BUILDER_CONFIGURATION

builder_run_action test       test_docker_container $R_KEYMANWEB_CONTAINER_DESC $PORT_R_KEYMANWEB_COM "/_test"

