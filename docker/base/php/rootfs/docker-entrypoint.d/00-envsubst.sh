#!/bin/sh
set -eu

# Envsubset all files and only replace exported envs.
defined_envs=$(printf '${%s} ' $(env | cut -d= -f1))

for file in $(find /usr/local/etc -type f -name '*.template')
do
	envsubst "$defined_envs" < $file > ${file%.template}
done
