#!/bin/bash

WORKDIR=$PWD
echo "Work directory: $WORKDIR"
cd ..
git clone https://github.com/owncloud/core
cd core
git submodule update --init
mkdir apps2
ln -s $WORKDIR apps2
git checkout urlParams_fix
git checkout -b oc6-integration
git fetch origin
git merge origin/improved_request
cd $WORKDIR