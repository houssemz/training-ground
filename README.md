# Shippeo's Training Ground

- [Getting Started](#getting-started)
  - [List of commands](#list-of-commands)
    - [Commands syntax:](#commands-syntax)
      - [Start/stop/restart/clean all services:](#startstoprestartclean-all-services)
      - [Service status:](#service-status)
      - [Launch a docker-compose command:](#launch-a-docker-compose-command)
      - [Launch all tests:](#launch-all-tests)
      - [Start/stop/restart/clean a specific service:](#startstoprestartclean-a-specific-service)
      - [Enter a service container:](#enter-a-service-container)
      - [Open a service in the browser (if available):](#open-a-service-in-the-browser-if-available)
      - [Launch a service tests suite in parallel (if available):](#launch-a-service-tests-suite-in-parallel-if-available)
- [Usage](#usage)
  - [Start project](#start-project)
  - [Add Data](#add-data)


## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/)

    *For linux user, we recommend to install compose V2 through `docker-compose-plugin` package.*
2. If not already done, [install taskfile.dev](https://taskfile.dev/#/installation)
4. Setup docker registry path in CI files
5. Open your command line interface in the project directory and run `task`

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

## Usage
### Start project
To start project (or reset it) you can execute the followings command:
```bash
task docker:clean && task docker:start && task app:postgres-db-migrate
```

### Add Data
You can generate an order using the following cli:
```bash
php bin/console app:mock:order-create
```
