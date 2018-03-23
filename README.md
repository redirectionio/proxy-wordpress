# redirection.io Proxy for WordPress

The redirection.io Proxy for WordPress works in combination with
[redirection.io](redirection.io).

If you don't know what is redirection.io, take the time to make a quick tour on
the website.

Before using it, you need:
- a redirection.io account. If you don't have an account, please [contact us here](https://redirection.io/contact-us).
- a configured redirection.io agent on your server. Please follow the [installation guide](https://redirection.io/documentation/developer-documentation/installation-of-the-agent).

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
