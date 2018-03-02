# RedirectionIO Proxy for WordPress

[![Latest Version](https://img.shields.io/github/release/redirectionio/proxy-wordpress.svg)](https://github.com/redirectionio/proxy-wordpress)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Build Status](https://img.shields.io/travis/redirectionio/proxy-wordpress/master.svg)](https://travis-ci.org/redirectionio/proxy-wordpress)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/redirectionio/proxy-wordpress.svg)](https://scrutinizer-ci.com/g/redirectionio/proxy-wordpress)
[![Quality Score](https://img.shields.io/scrutinizer/g/redirectionio/proxy-wordpress.svg)](https://scrutinizer-ci.com/g/redirectionio/proxy-wordpress)

[![Email](https://img.shields.io/badge/email-support@redirection.io-blue.svg)](mailto:support@redirection.io)

redirection.io WordPress Proxy works in combination with [redirection.io](redirection.io).

If you don't know what is redirection.io, take the time to make a quick tour on the website.

Before using it, you need:
- a redirection.io account
- a configured redirection.io agent on your server

You don't have an account ? Please contact us [here](https://redirection.io/contact-us).
You don't have an installed and configured agent ? Follow the [installation guide](https://redirection.io/documentation/developer-documentation/getting-started-installing-the-agent).

Drop us an email to `support@redirection.io` if you need help or have any question.

## Installation

### Automatic installation

*The easy way*

1. Go to your WP admin area in `Plugins > Add new`
2. Type `redirection.io` in `Search Plugins` box
3. Click `Install Now` button
4. Click `Activate` button

### Manual installation

*The old school way*

1. Run `COMPOSER_MIRROR_PATH_REPOS=1 composer install`
2. Copy/paste `wordpress` directory in `wp-content/plugins/` and rename it `redirectionio`
3. Go to your WP admin area in `Plugins > Installed Plugins`
4. Click `Activate` link in redirection.io row

## Configuration

Preliminary step: Find the host/port pair or your agent(s)

1. Go to your WP admin area in `Settings > redirection.io`
2. Configure connection(s)
3. Click `Save changes` button

Yay! You are good to go :raised_hands:
