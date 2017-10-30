<?php 

namespace RedirectionIO\Client\Wordpress;

require_once __DIR__ . '/../php/WPCoreFunctionsOverrider.php';

$overrider = new WPCoreFunctionsOverrider();

?>

<div class="wrap">
    <h1><?= _e('redirection.io settings', 'redirectionio') ?></h1>
    <p>
        <?= _e('Proxy client for redirection.io | Put an end to 404 errors - Track HTTP errors and setup useful HTTP redirections', 'redirectionio') ?>
        <br/>
        <?= _e('Please set here the connection options of your redirection.io agent [required].', 'redirectionio') ?>
    </p>
    <form id="connections" method="post" action="options.php">
        <?php
            settings_fields('redirectionio-group');
            $overrider->do_settings_sections('redirectionio');
        ?>
        <button id="connections_add" class="button" onclick="addConnection(event)"><?= _e('Add') ?></button>
        <?php
            submit_button();
        ?>
    </form>
</div>

<script>
    var confirmStr = '<?= _e('Are you sure ?', 'redirectionio') ?>';
</script>
