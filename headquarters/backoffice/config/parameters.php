<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */
# This file is auto-generated during the composer install

// Database configuration
$container->setParameter( 'database_driver', 'pdo_mysql' );
if (true === defined( 'PHPFOX' )) {
  $container->setParameter( 'database_host', \PHPFOX::getParam( array( 'db', 'host' ) ) );
  $container->setParameter( 'database_port', \PHPFOX::getParam( array( 'db', 'port' ) ) );
  $container->setParameter( 'database_name', \PHPFOX::getParam( array( 'db', 'name' ) ) );
  $container->setParameter( 'database_user', \PHPFOX::getParam( array( 'db', 'user' ) ) );
  $container->setParameter( 'database_password', \PHPFOX::getParam( array( 'db', 'pass' ) ) );
} else {

  $container->setParameter( 'database_host', '' );
  $container->setParameter( 'database_port', ''  );
  $container->setParameter( 'database_name', '' );
  $container->setParameter( 'database_user', '' );
  $container->setParameter( 'database_password', '' );
}

// Mail configuration
$container->setParameter( 'mailer_transport', 'mail' );
$container->setParameter( 'mailer_host', '127.0.0.1' );
$container->setParameter( 'mailer_user', null );
$container->setParameter( 'mailer_password', null );

$container->setParameter( 'locale', 'en' );
if (true === defined( 'PHPFOX' )) {
  $container->setParameter( 'secret', \PHPFOX::getParam( 'core.salt' ) );
} else {
  $container->setParameter( 'secret', '' );
}
$container->setParameter( 'debug_toolbar', true );
$container->setParameter( 'debug_redirects', false );
$container->setParameter( 'use_assetic_controller', true );

#
#    # Sonata Admin Options
#    sonata_admin.title:             Sonata Project
#    sonata_admin.logo_title:        /bundles/sonataadmin/logo_title.png
#
#    # Sonata News Options
#    sonata_news.blog_title:         My Awesome Blog
#    sonata_news.blog_link:          http://awesome-blog.ltd
#    sonata_news.blog_description:   My Awesome blog description
#    sonata_news.salt:               ThisTokenIsNotSoSecretChangeIt
#    sonata_news.comment.emails:     [mail@example.org]
#    sonata_news.comment.email_from: no-reply@example.org

# Sonata Media options
$container->setParameter( 'sonata_media.cdn.host', '/file/media' );
#
#    # Sonata User Options
#    sonata_user.google_authenticator.server:  demo.sonata-project.org
#
#    # Sonata Page Options
#    sonata_page.varnish.command: if [ ! -r "/etc/varnish/secret" ]; then echo "VALID ERROR :/"; else varnishadm -S /etc/varnish/secret -T 127.0.0.1:6082 {{ COMMAND }} "{{ EXPRESSION }}"; fi; # you need to adapt this line to work with your configuration
