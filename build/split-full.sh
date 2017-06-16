git subsplit init git@github.com:tomahawkphp/framework.git
git subsplit publish --heads="master" src/Tomahawk/Asset:git@github.com:tomahawkphp/asset.git
git subsplit publish --heads="master" src/Tomahawk/Authentication:git@github.com:tomahawkphp/authentication.git
git subsplit publish --heads="master" src/Tomahawk/Cache:git@github.com:tomahawkphp/cache.git
git subsplit publish --heads="master" src/Tomahawk/Config:git@github.com:tomahawkphp/config.git
git subsplit publish --heads="master" src/Tomahawk/CommandBus:git@github.com:tomahawkphp/command-bus.git
git subsplit publish --heads="master" src/Tomahawk/Forms:git@github.com:tomahawkphp/forms.git
git subsplit publish --heads="master" src/Tomahawk/Hashing:git@github.com:tomahawkphp/hashing.git
git subsplit publish --heads="master" src/Tomahawk/Html:git@github.com:tomahawkphp/html.git
git subsplit publish --heads="master" src/Tomahawk/Routing:git@github.com:tomahawkphp/routing.git
git subsplit publish --heads="master" src/Tomahawk/Validation:git@github.com:tomahawkphp/validation.git
rm -rf .subsplit/