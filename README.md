# Shippeo's Training Ground

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/)

    *For linux user, we recommend to install compose V2 through `docker-compose-plugin` package.*
2. If not already done, [install taskfile.dev](https://taskfile.dev/#/installation)
4. Setup docker registry path in CI files
5. Open your command line interface in the project directory and run `task`

### Enable bash completion for Task

#### On linux:

Create a file in your home directory: `~/.bash_completion`
```bash
#/usr/bin/env bash

_task_completion()
{
  local scripts;
  local curr_arg;

  # Remove colon from word breaks
  COMP_WORDBREAKS=${COMP_WORDBREAKS//:}

  scripts=$(task --list-all | sed '1d' | sed 's/^\* //' | sed 's/:\s*$//');

  curr_arg="${COMP_WORDS[COMP_CWORD]:-"."}"

  # Do not accept more than 1 argument
  if [ "${#COMP_WORDS[@]}" != "2" ]; then
    return
  fi

  COMPREPLY=($(compgen -c | echo "$scripts" | grep $curr_arg));
}

complete -F _task_completion task
```

Edit your bashrc file: `~/bashrc` and add the following lines:

```bash
if [ -f ~/.bash_completion ]; then
    . ~/.bash_completion
fi
```

Finally source the bashrc file from your terminal:

```bash
. ~/bashrc
```

### List of commands

The task commands are referenced in the main [Taskfile.yaml](./Taskfile.yaml) which includes:
- [service specific](./taskfile.d/docker) task files
- [os specific](./taskfile.d/os) task files
- [app specific](./srv/app/Taskfile.yaml) task file

#### Commands syntax:

##### Start/stop/restart/clean all services:
```bash
task docker:start
task docker:stop
task docker:restart
task docker:clean
```

##### Service status:
```bash
task docker:ps
```

##### Launch a docker-compose command:
```bash
task docker -- {the-arguments}
# .eg task docker -- ps
```

##### Launch all tests:
```bash
task test
```

##### Start/stop/restart/clean a specific service:
```bash
task {short-service-name}:docker:start
# eg. task app:docker:start

task {short-service-name}:docker:stop
# eg. task app:docker:stop

task {short-service-name}:docker:restart
# eg. task app:docker:restart

task {short-service-name}:docker:clean
# eg. task app:docker:clean
```

##### Enter a service container:

*Note that the [app specific](./srv/app/Taskfile.yaml) taskfile is docker agnostic and can also be used **inside** the container with autocompletion ;)*

```bash
task {short-service-name}:docker:enter
# eg. task app:docker:enter
```

##### Open a service in the browser (if available):
```bash
task {short-service-name}:docker:browser
# eg. task nginx:docker:browser
```

##### Launch a service tests suite in parallel (if available):
```bash
task {short-service-name}:test
# eg. task app:test
```
