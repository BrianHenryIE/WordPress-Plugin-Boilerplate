#!/bin/bash

PLUGIN_SLUG="plugin-slug";

# Print the script name.
echo $(basename "$0")

echo "Installing latest build of $PLUGIN_SLUG"
wp plugin install ./setup/$PLUGIN_SLUG.latest.zip --activate --force