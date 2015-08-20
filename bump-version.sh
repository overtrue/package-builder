#!/bin/bash

PHAR_NAME=package-builder
GITHUB_USERNAME=overtrue
#----------------------------

set -e

if [ $# -ne 1 ]; then
  echo "Usage: `basename $0` <tag>"
  exit 65
fi

TAG=$1

#
# Tag & build master branch
#
git checkout master
git tag ${TAG}
box build

#
# Copy executable file into GH pages
#
git checkout gh-pages

cp ${PHAR_NAME}.phar downloads/${PHAR_NAME}-${TAG}.phar
cp ${PHAR_NAME}.phar downloads/${PHAR_NAME}.phar

git add downloads/${PHAR_NAME}-${TAG}.phar

SHA1=$(openssl sha1 ${PHAR_NAME}.phar | cut -d " " -f2)

JSON="name:\"${PHAR_NAME}.phar\""
JSON="${JSON},sha1:\"${SHA1}\""
JSON="${JSON},url:\"http://${GITHUB_USERNAME}.github.io/${PHAR_NAME}/downloads/${PHAR_NAME}-${TAG}.phar\""
JSON="${JSON},version:\"${TAG}\""

if [ -f ${PHAR_NAME}.phar.pubkey ]; then
    cp ${PHAR_NAME}.phar.pubkey pubkeys/${PHAR_NAME}-${TAG}.phar.pubkeys
    git add pubkeys/${PHAR_NAME}-${TAG}.phar.pubkeys
    JSON="${JSON},publicKey:\"http://${GITHUB_USERNAME}.github.io/${PHAR_NAME}/pubkeys/${PHAR_NAME}-${TAG}.phar.pubkey\""
fi

#
# Update manifest
#
if [[ ! -f manifest.json ]]; then
    echo "[]" > manifest.json
fi
cat manifest.json | jsawk -a "this.push({${JSON}})" | python -mjson.tool > manifest.json.tmp
mv manifest.json.tmp manifest.json
git add manifest.json

git commit -m "Bump version ${TAG}"
git push
git push ${TAG}

#
# Go back to master
#
git checkout master

# echo "New version created. Now you should run:"
# echo "git push origin gh-pages"
# echo "git push ${TAG}"
