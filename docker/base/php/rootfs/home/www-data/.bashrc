#!/usr/bin/env bash

# enable bash completion in interactive shells
if ! shopt -oq posix; then
  completionDir=/usr/local/etc/bash-completion/bash_completion.d
  if [[ -d $completionDir && -r $completionDir && -x $completionDir ]]; then
    for i in "$completionDir"/*; do
        [[ -f $i && -r $i ]] && . "$i"
    done
  elif [ -f /usr/share/bash-completion/bash_completion ]; then
    . /usr/share/bash-completion/bash_completion
  elif [ -f /etc/bash_completion ]; then
    . /etc/bash_completion
  fi
fi
