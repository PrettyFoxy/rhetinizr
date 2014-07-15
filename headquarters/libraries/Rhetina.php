<?php

/**
 * @file
 * Contains Rhetina.
 */
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Debug\Debug;

/**
 * Static Service Container wrapper.
 *
 */
class Rhetina
{

    /**
     * The current system version.
     */
    const VERSION = '1.0-dev';

    /**
     * Core API compatibility.
     */
    const CORE_COMPATIBILITY = '3.x';

    /**
     * Core minimum schema version.
     */
    const CORE_MINIMUM_SCHEMA_VERSION = 8000;
    /**
     *
     */
    const CORE_NAME = 'rhetina';

    /**
     * @var
     */
    private static $booted;
    /**
     * @var
     */
    private static $platform;

    /**
     * The currently active container object.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected static $container;

    /**
     * @return string
     */
    public static function path()
    {
        if (!defined( 'PHPFOX_DIR_MODULE' )) {
            define( 'PHPFOX_DIR_MODULE', PHPFOX_DIR . 'module' . PHPFOX_DS );
        }

        return PHPFOX_DIR_MODULE . self::CORE_NAME . PHPFOX_DS;
    }

    /**
     * @return string
     */
    public static function url()
    {
        return Phpfox::getParam( 'core.path' ) . '/module/' . self::CORE_NAME . PHPFOX_DS;
    }

    /**
     * @param      $env
     * @param bool $debug
     */
    public static function boot( $env, $debug = false )
    {
        if (self::$booted === true) {
            return;
        }
        require_once __DIR__ . '/../backoffice/bootstrap.php';

        if ($debug) {
            Debug::enable( 3 );
        }

        require_once __DIR__ . '/../backoffice/AppKernel.php';
        $kernel = new AppKernel( $env, $debug );
        $kernel->loadClassCache();
        $kernel->boot();

        $request = Request::createFromGlobals();

        $requestStack = new RequestStack();
        $requestStack->push( $request );

        $kernel->getContainer()->enterScope( 'request' );
        $kernel->getContainer()->set( 'request', $request, 'request' );

        $request->setSession( $kernel->getContainer()->get( 'session' ) );
        $kernel->getContainer()->set( 'request_stack', $requestStack );

        self::setContainer( $kernel->getContainer() );
        self::$booted = true;

        //$request = Request::createFromGlobals();
        //$response = $kernel->handle($request);
        //$response->send();
        //$kernel->terminate($request, $response);
    }

    /**
     * @param $platform
     */
    public static function setPlatform( $platform )
    {
        static::$platform = $platform;
    }

    /**
     * Sets a new global container.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * A new container instance to replace the current. NULL may be passed by
     * testing frameworks to ensure that the global state of a previous
     * environment does not leak into a test.
     */
    public static function setContainer( ContainerInterface $container = null )
    {
        static::$container = $container;
    }

    /**
     * Returns the currently active global container.
     *
     * @deprecated This method is only useful for the testing environment. It
     * should not be used otherwise.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public static function getContainer()
    {
        return static::$container;
    }

    /**
     * Retrieves a service from the container.
     *
     * Use this method if the desired service is not one of those with a dedicated
     * accessor method below. If it is listed below, those methods are preferred
     * as they can return useful type hints.
     *
     * @param  string $id
     *                    The ID of the service to retrieve.
     *
     * @return mixed
     *                   The specified service.
     */
    public static function service( $id )
    {
        return static::$container->get( $id );
    }

    /**
     * Indicates if a service is defined in the container.
     *
     * @param string $id
     *                   The ID of the service to check.
     *
     * @return bool
     *              TRUE if the specified service exists, FALSE otherwise.
     */
    public static function hasService( $id )
    {
        return static::$container && static::$container->has( $id );
    }

    /**
     * Indicates if there is a currently active request object.
     *
     * @return bool
     *              TRUE if there is a currently active request object, FALSE otherwise.
     */
    public static function hasRequest()
    {
        return static::$container && static::$container->has( 'request' ) && static::$container->initialized(
            'request'
        ) && static::$container->isScopeActive( 'request' );
    }

    /**
     * Retrieves the currently active request object.
     *
     * Note: The use of this wrapper in particular is especially discouraged. Most
     * code should not need to access the request directly.  Doing so means it
     * will only function when handling an HTTP request, and will require special
     * modification or wrapping when run from a command line tool, from certain
     * queue processors, or from automated tests.
     *
     * If code must access the request, it is considerably better to register
     * an object with the Service Container and give it a setRequest() method
     * that is configured to run when the service is created.  That way, the
     * correct request object can always be provided by the container and the
     * service can still be unit tested.
     *
     * If this method must be used, never save the request object that is
     * returned.  Doing so may lead to inconsistencies as the request object is
     * volatile and may change at various times, such as during a subrequest.
     *
     * @return \Symfony\Component\HttpFoundation\Request
     *                                                   The currently active request object.
     */
    public static function request()
    {
        return static::$container->get( 'request' );
    }


    /**
     * Returns a parameter value.
     *
     * @param string $parameter
     *                          Name of the parameter to return.
     *
     * @return mixed
     */
    public static function parameter( $parameter )
    {
        return static::$container->getParameter( $parameter );
    }

    /**
     * Gets the syncing state.
     *
     * @return bool
     * Returns TRUE is syncing flag set.
     */
    public function isConfigSyncing()
    {
        return static::$container->get( 'config.installer' )->isSyncing();
    }

    /**
     * Gets Phpfox state.
     *
     * @return bool
     */
    public static function isPhpfoxRunning()
    {
        return defined( 'PHPFOX' );
    }

    public static function cacheClear()
    {
        $filesystem = new Symfony\Component\Filesystem\Filesystem();

        if ($filesystem->exists( Phpfox::getParam( 'core.dir_file' ) . 'rhetina/cache/' )) {
            $filesystem->remove( Phpfox::getParam( 'core.dir_file' ) . 'rhetina/cache/' );
        }
    }
}
