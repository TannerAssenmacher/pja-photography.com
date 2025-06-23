<?php return array(
    'root' => array(
        'pretty_version' => 'dev-master',
        'version' => 'dev-master',
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'reference' => 'e7f98d164d4da798f7126fec7d294fce16299e5d',
        'name' => 'easy-digital-downloads/edd-stripe',
        'dev' => false,
    ),
    'versions' => array(
        'composer/installers' => array(
            'pretty_version' => 'v1.11.0',
            'version' => '1.11.0.0',
            'type' => 'composer-plugin',
            'install_path' => __DIR__ . '/./installers',
            'aliases' => array(),
            'reference' => 'ae03311f45dfe194412081526be2e003960df74b',
            'dev_requirement' => false,
        ),
        'easy-digital-downloads/edd-stripe' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'reference' => 'e7f98d164d4da798f7126fec7d294fce16299e5d',
            'dev_requirement' => false,
        ),
        'roundcube/plugin-installer' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'shama/baton' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'stripe/stripe-php' => array(
            'pretty_version' => 'v7.47.0',
            'version' => '7.47.0.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../stripe/stripe-php',
            'aliases' => array(),
            'reference' => 'b51656cb398d081fcee53a76f6edb8fd5c1a5306',
            'dev_requirement' => false,
        ),
    ),
);
