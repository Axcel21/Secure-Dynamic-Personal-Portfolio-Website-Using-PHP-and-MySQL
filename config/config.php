<?php
// Change these for your machine / host. Never push real passwords to a public repo.
return [
    'db_host' => '127.0.0.1',
    'db_name' => 'portfolio_db',
    'db_user' => 'root',
    'db_pass' => '',
    'base_url' => '/portfolio-php', // folder name under htdocs; use '' if the site is at the web root
    'max_login_attempts' => 5,      // per 15 minutes per IP
    'max_messages_per_hour' => 5,   // per IP
];
