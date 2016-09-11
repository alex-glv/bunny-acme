[![CircleCI](https://circleci.com/gh/alex-glv/bunny-acme.svg?style=svg)](https://circleci.com/gh/alex-glv/bunny-acme)

# bunnyacme
A simple hassle-free queue system

# Pre-requisities
  - Docker
  - Docker compose (recommended)

# Usage

Go to the php-bcmath-docker folder, then type:
```
docker build -t bunnyacme .
```

Install composer dependencies:
```
docker run -v $PWD:/bunnyacme bunnyacme  php /tmp/composer.phar install
```

Start the application:
```
docker-compose up
```

