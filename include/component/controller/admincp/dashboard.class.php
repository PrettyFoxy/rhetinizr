<?php

/*
 * Copyright (C) 2014 Alexandru Furculita <alex@rhetina.com>
 */

class Rhetinizr_Component_Controller_Admincp_Dashboard extends Phpfox_Component
{
    private $frontofficePath;

    public function getName()
    {
        return 'rhetinizr';
    }

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $this->template()->setBreadCrumb( 'Dashboard' );

        $this->frontofficePath = Phpfox::getParam( 'core.url_module' )
            . $this->getName()
            . '/headquarters/frontoffice/';
        $rand = rand( 1, 17 );

        $applicationConfig = array(
            'root'    => "/admincp/{$this->getName()}/dashboard",
            'api'     => $this->frontofficePath . 'index.php/',
            'modules' => Rhetina::service( 'rhetina.module.registrar' )
        );
        $requireJSConfig = array(
            'src'     => $this->frontofficePath . 'vendor/requirejs/require.js',
            'main'    => $this->frontofficePath . 'javascripts/app/main.js',
            'options' => json_encode( $this->getApplicationConfig() )
        );

        $this->template()->assign(
            array(
                'emptyObject'       => '{}',
                'frontofficePath'   => $this->frontofficePath,
                'applicationConfig' => json_encode( $applicationConfig ),
                'rand'              => $rand,
                'requireJS'         => $requireJSConfig
            )
        )
            ->setHeader(
                array(
                    '../../../../headquarters/frontoffice/stylesheets/css/main.css' => 'module_' . $this->getName()
                )
            );
    }

    public function clean()
    {
        ( ( $sPlugin = Phpfox_Plugin::get(
            $this->getName() . '.component_controller_admincp_dashboard_clean'
        ) ) ? eval( $sPlugin ) : false );
    }

    public function getApplicationConfig()
    {
        $config = array();

        $vendor = $this->frontofficePath . 'vendor/';

        $config['baseUrl'] = Phpfox::getParam( 'core.folder' )
            . 'module/'
            . $this->getName()
            . '/headquarters/frontoffice/javascripts/app/';
        $config["locale"] = 'en_US_POSIX';
        $config['paths'] = array(
            'vendor'                            => $vendor,
            'jquery'                            => $vendor . 'jquery/jquery',
            'jqueryui'                          => $vendor . 'jquery-ui/ui/jquery-ui',
            'backbone'                          => $vendor . 'backbone/backbone',
            'underscore'                        => $vendor . 'lodash/dist/lodash.underscore',
            'underscore.string'                 => $vendor . 'underscore.string/lib/underscore.string',
            'backbone.marionette'               => $vendor . 'backbone.marionette/lib/backbone.marionette',
            'backbone.wreqr'                    => $vendor . 'backbone.wreqr/lib/backbone.wreqr',
            'backbone.babysitter'               => $vendor . 'backbone.babysitter/lib/backbone.babysitter',
            'backbone-forms'                    => $vendor . 'backbone-forms/distribution.amd/backbone-forms',
            'backbone-forms.list'               => $vendor . 'backbone-forms/distribution.amd/editors/list',
            'backbone-forms.bootstrap-template' => $vendor . 'backbone-forms/distribution.amd/templates/bootstrap3',
            'backbone.validation'               => $vendor . 'backbone.validation/dist/backbone-validation-amd',
            'backbone.bootstrap-modal'          => $vendor . 'backbone.bootstrap-modal/src/backbone.bootstrap-modal',
            'bootstrap'                         => $vendor . 'bootstrap/dist/js/bootstrap',
            'text'                              => $vendor . 'requirejs-text/text',
            'handlebars'                        => $vendor . 'require-handlebars-plugin/hbs/handlebars.runtime',
            'i18nprecompile'                    => $vendor . 'require-handlebars-plugin/hbs/i18nprecompile',
            'json2'                             => $vendor . 'require-handlebars-plugin/hbs/json2',
            'hbs'                               => $vendor . 'require-handlebars-plugin/hbs',
            'views'                             => 'views',
            'controllers'                       => 'controllers',
            'models'                            => 'models',
            'tmpl'                              => 'templates'
        );

        $config['hbs'] = array(
            'templateExtension' => 'html',
            'helperDirectory'   => 'templates/helpers',
            'i18nDirectory'     => 'templates/i18n',
            'compileOptions'    => array()
        );

        $config['shim'] = array(
            'backbone'                 => array(
                'deps'    => array(
                    'underscore',
                    'jquery'
                ),
                'exports' => 'Backbone'
            ),
            'handlebars'               => array(
                'exports' => 'Handlebars'
            ),
            'backbone.marionette'      => array(
                'deps'    => array(
                    'underscore',
                    'backbone',
                    'jquery'
                ),
                'exports' => 'Marionette'
            ),
            'backbone.bootstrap-modal' => array(
                'deps' => array(
                    'backbone'
                )
            ),
            'jqueryui'                 => array(
                'deps' => array(
                    'jquery'
                )
            ),
            'backbone-forms.list'      => array(
                'deps' => array(
                    'backbone',
                    'backbone-forms',
                    'backbone-forms.bootstrap-template'
                )
            ),
            'bootstrap'                => array(
                'deps'    => array(
                    'jquery'
                ),
                'exports' => 'jquery'
            ),
            'underscore.string'        => array(
                'deps' => array(
                    'underscore'
                )
            )
        );

        $modules = Rhetina::service( "rhetina.module.registrar" )->getModules();

        foreach ($modules as $bundle) {
            if (false === method_exists( $bundle, 'getRequirejsConfiguration' )) {
                continue;
            }

            $bundleConfig = $bundle->getRequirejsConfiguration();

            if (is_array( $bundleConfig )) {
                foreach ($bundleConfig as $key => $value) {
                    $config[$key][] = $value;
                }
            }

        }

        return $config;
    }
}
