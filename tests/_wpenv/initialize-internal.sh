#!/bin/bash

# Print the script name.
echo $(basename "$0")

echo "wp plugin activate --all"
wp plugin activate --all


echo "Set up pretty permalinks for REST API."
wp rewrite structure /%year%/%monthnum%/%postname%/ --hard;