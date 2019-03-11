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

### zip archive install

We provide a zip archive of the plugin [in our "releases" page](https://github.com/redirectionio/proxy-wordpress/releases).

1. Download the latest release
2. In your WordPress admin area, head to `Plugins > Add New`
3. Click the "Upload Plugin" button
4. Choose the zip archive and click the "Install Now" button

### Manual installation

This install procedure allows to install the plugin from this repository's code.

1. Donwload the zip archive of this repository's code: [https://github.com/redirectionio/proxy-wordpress/archive/master.zip](https://github.com/redirectionio/proxy-wordpress/archive/master.zip)
2. Extract this archive, and navigate in the extracted folder with a shell
3. Install composer dependencies:
```sh
COMPOSER_MIRROR_PATH_REPOS=1 composer install
```
4. Move this `wordpress` directory in `wp-content/plugins/` and rename it `redirectionio`
5. Go to your WordPress admin area in `Plugins > Installed Plugins`
6. Click `Activate` link in redirection.io row

## Configuration

This step is required to get the plugin work. First, you need to [setup a redirection.io agent](https://redirection.io/documentation/developer-documentation/installation-of-the-agent). Once this is done, you can go on with the plugin's configuration:

1. Go to your WP admin area in `Settings > redirection.io`
2. Configure connection(s)
3. Click `Save changes` button

Yay! You are good to go :raised_hands:
