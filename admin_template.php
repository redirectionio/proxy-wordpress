<div class="wrap">
    <h1>redirection.io</h1>
    <p>
        <?= _e('Proxy client for redirection.io | Put an end to 404 errors - Track HTTP errors and setup useful HTTP redirections', 'redirectionio') ?>
    </p>
    <form method="post" action="options.php">
        <?php
            settings_fields('redirectionio-group');
            do_settings_sections('redirectionio');
            submit_button();
        ?>
    </form>         
</div>
