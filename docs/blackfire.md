# How to profile the application with blackfire

## Getting Started
1. If not already done, [create a blackfire account](https://blackfire.io)
2. If not already done, export the [blackfire credentials](https://blackfire.io/my/settings/credentials) in your ~/.bashrc / ~/.zshrc
```bash
export BLACKFIRE_SERVER_ID=XXXXXXX
export BLACKFIRE_SERVER_TOKEN=XXXXXXX
export BLACKFIRE_CLIENT_ID=XXXXXXX
export BLACKFIRE_CLIENT_TOKEN=XXXXXXX
```
3. If not already done, disable xdebug and pcov extension by adding `APP_DISABLED_EXTENSION=xdebug pcov` in your [.env](../.env) file.

## Profile an api cli command
```
task app:blackfire-cli -- {your-command}
# eg. task app:blackfire-cli -- cache:clear
```

## Profile an api endpoint
```
task app:blackfire-curl -- {your-curl-content}
# eg. task app:blackfire-curl -- http://nginx:8080/api/hello-world
```
