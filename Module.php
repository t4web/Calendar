<?php
namespace T4webCalendar;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Console\Adapter\AdapterInterface as ConsoleAdapterInterface;
use Zend\ServiceManager\ServiceManager;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalAdapter;
use Falc\Flysystem\Plugin\Symlink\Local as LocalSymlinkPlugin;

use T4webBase\Domain\Service\Create as ServiceCreate;
use T4webBase\Domain\Service\Update as ServiceUpdate;
use T4webBase\Domain\Service\BaseFinder as ServiceFinder;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface,
    ControllerProviderInterface, ConsoleUsageProviderInterface,
    ServiceProviderInterface
{
    public function getConfig($env = null)
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConsoleUsage(ConsoleAdapterInterface $console)
    {
        return array(
            'calendar init' => 'Initialize module',
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'T4webCalendar\Calendar\Service\Create' => function (ServiceManager $sm) {
                    $eventManager = $sm->get('EventManager');
                    $eventManager->addIdentifiers('T4webCalendar\Calendar\Service\Create');

                    return new ServiceCreate(
                        $sm->get('T4webCalendar\Calendar\InputFilter\Create'),
                        $sm->get('T4webCalendar\Calendar\Repository\DbRepository'),
                        $sm->get('T4webCalendar\Calendar\Factory\EntityFactory'),
                        $eventManager
                    );
                },

                'T4webCalendar\Calendar\Service\Update' => function (ServiceManager $sm) {
                    $eventManager = $sm->get('EventManager');
                    $eventManager->addIdentifiers('T4webCalendar\Calendar\Service\Update');

                    return new ServiceUpdate(
                        $sm->get('T4webCalendar\Calendar\InputFilter\Update'),
                        $sm->get('T4webCalendar\Calendar\Repository\DbRepository'),
                        $sm->get('T4webCalendar\Calendar\Criteria\CriteriaFactory'),
                        $eventManager
                    );
                },

                'T4webCalendar\Calendar\Service\Finder' => function (ServiceManager $sm) {
                    return new ServiceFinder(
                        $sm->get('T4webCalendar\Calendar\Repository\DbRepository'),
                        $sm->get('T4webCalendar\Calendar\Criteria\CriteriaFactory')
                    );
                },

            ),

            'invokables' => array(
                'T4webCalendar\Controller\ViewModel\AjaxViewModel' => 'T4webCalendar\Controller\ViewModel\AjaxViewModel',
                'T4webCalendar\Controller\ViewModel\ShowViewModel' => 'T4webCalendar\Controller\ViewModel\ShowViewModel',

                'T4webCalendar\Calendar\InputFilter\Create' => 'T4webCalendar\Calendar\InputFilter\Create',
                'T4webCalendar\Calendar\InputFilter\Update' => 'T4webCalendar\Calendar\InputFilter\Update',
            ),
        );
    }

    public function getControllerConfig()
    {
        return array(
            'factories' => array(
                'T4webCalendar\Controller\Console\Init' => function (ControllerManager $cm) {
                    $sl = $cm->getServiceLocator();

                    $fileSystem = new Filesystem(new LocalAdapter(getcwd()));
                    $fileSystem->addPlugin(new LocalSymlinkPlugin\Symlink());

                    return new Controller\Console\InitController(
                        $sl->get('Zend\Db\Adapter\Adapter'),
                        $fileSystem
                    );
                },
                'T4webCalendar\Controller\User\Show' => function (ControllerManager $cm) {
                    $sl = $cm->getServiceLocator();
                    return new Controller\User\ShowController(
                        $sl->get('T4webCalendar\Calendar\Service\Finder'),
                        $sl->get('T4webCalendar\Controller\ViewModel\ShowViewModel')
                    );
                },
                'T4webCalendar\Controller\User\Ajax' => function (ControllerManager $cm) {
                    $sl = $cm->getServiceLocator();
                    return new Controller\User\AjaxController(
                        $sl->get('T4webCalendar\Controller\ViewModel\AjaxViewModel')
                    );
                },
            ),
        );
    }
}
