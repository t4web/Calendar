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
use T4webBase\InputFilter\InputFilter as BaseInputFilter;
use T4webEmployees\Controller\User\ListController;
use T4webEmployees\Controller\User\ShowController;
use T4webEmployees\Controller\User\EditController;
use T4webEmployees\Controller\User\AddController;
use T4webEmployees\Controller\User\CreateAjaxController;
use T4webEmployees\Controller\User\SaveAjaxController;
use T4webEmployees\Controller\Console\InitController;
use T4webEmployees\Employee\Service\WorkInfoPopulate;
use T4webEmployees\Employee\Service\PersonalInfoPopulate;
use T4webEmployees\Employee\Service\SocialPopulate;

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
            'employees init' => 'Initialize module',
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'T4webEmployees\Employee\Service\Create' => function (ServiceManager $sm) {
                    $eventManager = $sm->get('EventManager');
                    $eventManager->addIdentifiers('T4webEmployees\Employee\Service\Create');

                    return new Employee\Service\Create(
                        $sm->get('T4webEmployees\Employee\InputFilter\Create'),
                        $sm->get('T4webEmployees\Employee\Repository\DbRepository'),
                        $sm->get('T4webEmployees\PersonalInfo\Repository\DbRepository'),
                        $sm->get('T4webEmployees\WorkInfo\Repository\DbRepository'),
                        $sm->get('T4webEmployees\Social\Repository\DbRepository'),
                        $sm->get('T4webEmployees\Employee\Factory\EntityFactory'),
                        $sm->get('T4webEmployees\PersonalInfo\Factory\EntityFactory'),
                        $sm->get('T4webEmployees\WorkInfo\Factory\EntityFactory'),
                        $sm->get('T4webEmployees\Social\Factory\EntityFactory'),
                        $eventManager
                    );
                },

                'T4webEmployees\Employee\Service\Update' => function (ServiceManager $sm) {
                    $eventManager = $sm->get('EventManager');
                    $eventManager->addIdentifiers('T4webEmployees\Employee\Service\Update');

                    return new Employee\Service\Update(
                        $sm->get('T4webEmployees\Employee\InputFilter\Update'),
                        $sm->get('T4webEmployees\Employee\Repository\DbRepository'),
                        $sm->get('T4webEmployees\PersonalInfo\Repository\DbRepository'),
                        $sm->get('T4webEmployees\WorkInfo\Repository\DbRepository'),
                        $sm->get('T4webEmployees\Social\Repository\DbRepository'),
                        $sm->get('T4webEmployees\Employee\Criteria\CriteriaFactory'),
                        $sm->get('T4webEmployees\PersonalInfo\Criteria\CriteriaFactory'),
                        $sm->get('T4webEmployees\WorkInfo\Criteria\CriteriaFactory'),
                        $sm->get('T4webEmployees\Social\Criteria\CriteriaFactory'),
                        $eventManager
                    );
                },

                'T4webEmployees\Employee\Service\Finder' => function (ServiceManager $sm) {
                    return new ServiceFinder(
                        $sm->get('T4webEmployees\Employee\Repository\DbRepository'),
                        $sm->get('T4webEmployees\Employee\Criteria\CriteriaFactory')
                    );
                },

                'T4webEmployees\Employee\Service\WorkInfoPopulate' =>  function (ServiceManager $sm) {
                    return new WorkInfoPopulate(
                        $sm->get('T4webEmployees\WorkInfo\Service\Finder')
                    );
                },

                'T4webEmployees\Employee\Service\PersonalInfoPopulate' =>  function (ServiceManager $sm) {
                    return new PersonalInfoPopulate(
                        $sm->get('T4webEmployees\PersonalInfo\Service\Finder')
                    );
                },

                'T4webEmployees\Employee\Service\SocialPopulate' =>  function (ServiceManager $sm) {
                    return new SocialPopulate(
                        $sm->get('T4webEmployees\Social\Service\Finder')
                    );
                },

                'T4webEmployees\PersonalInfo\Service\Create' => function (ServiceManager $sm) {
                    return new ServiceCreate(
                        new BaseInputFilter(),
                        $sm->get('T4webEmployees\PersonalInfo\Repository\DbRepository'),
                        $sm->get('T4webEmployees\PersonalInfo\Factory\EntityFactory')
                    );
                },

                'T4webEmployees\PersonalInfo\Service\Finder' => function (ServiceManager $sm) {
                    return new ServiceFinder(
                        $sm->get('T4webEmployees\PersonalInfo\Repository\DbRepository'),
                        $sm->get('T4webEmployees\PersonalInfo\Criteria\CriteriaFactory')
                    );
                },

                'T4webEmployees\WorkInfo\Service\Create' => function (ServiceManager $sm) {
                    return new ServiceCreate(
                        new BaseInputFilter(),
                        $sm->get('T4webEmployees\WorkInfo\Repository\DbRepository'),
                        $sm->get('T4webEmployees\WorkInfo\Factory\EntityFactory')
                    );
                },

                'T4webEmployees\WorkInfo\Service\Finder' => function (ServiceManager $sm) {
                    return new ServiceFinder(
                        $sm->get('T4webEmployees\WorkInfo\Repository\DbRepository'),
                        $sm->get('T4webEmployees\WorkInfo\Criteria\CriteriaFactory')
                    );
                },

                'T4webEmployees\Social\Service\Create' => function (ServiceManager $sm) {
                    return new ServiceCreate(
                        new BaseInputFilter(),
                        $sm->get('T4webEmployees\Social\Repository\DbRepository'),
                        $sm->get('T4webEmployees\Social\Factory\EntityFactory')
                    );
                },

                'T4webEmployees\Social\Service\Finder' => function (ServiceManager $sm) {
                    return new ServiceFinder(
                        $sm->get('T4webEmployees\Social\Repository\DbRepository'),
                        $sm->get('T4webEmployees\Social\Criteria\CriteriaFactory')
                    );
                },
            ),
            'invokables' => array(
                'T4webEmployees\ViewModel\SaveAjaxViewModel' => 'T4webEmployees\ViewModel\SaveAjaxViewModel',
                'T4webEmployees\Controller\User\AddViewModel' => 'T4webEmployees\Controller\User\AddViewModel',
                'T4webEmployees\Controller\User\ListViewModel' => 'T4webEmployees\Controller\User\ListViewModel',
                'T4webEmployees\Controller\User\ShowViewModel' => 'T4webEmployees\Controller\User\ShowViewModel',
                'T4webEmployees\Controller\User\EditViewModel' => 'T4webEmployees\Controller\User\EditViewModel',

                'T4webEmployees\Employee\InputFilter\Create' => 'T4webEmployees\Employee\InputFilter\Create',
                'T4webEmployees\Employee\InputFilter\Update' => 'T4webEmployees\Employee\InputFilter\Update',
                'T4webEmployees\PersonalInfo\InputFilter\Create' => 'T4webEmployees\PersonalInfo\InputFilter\Create',
                'T4webEmployees\WorkInfo\InputFilter\Create' => 'T4webEmployees\WorkInfo\InputFilter\Create',
                'T4webEmployees\Social\InputFilter\Create' => 'T4webEmployees\Social\InputFilter\Create',
            ),
        );
    }

    public function getControllerConfig()
    {
        return array(
            'factories' => array(
                'T4webEmployees\Controller\Console\Init' => function (ControllerManager $cm) {
                    $sl = $cm->getServiceLocator();

                    $fileSystem = new Filesystem(new LocalAdapter(getcwd()));
                    $fileSystem->addPlugin(new LocalSymlinkPlugin\Symlink());

                    return new InitController(
                        $sl->get('Zend\Db\Adapter\Adapter'),
                        $fileSystem
                    );
                },
                'T4webEmployees\Controller\User\List' => function (ControllerManager $cm) {
                    $sl = $cm->getServiceLocator();
                    return new ListController(
                        $sl->get('T4webEmployees\Employee\Service\Finder'),
                        $sl->get('T4webEmployees\Employee\Service\PersonalInfoPopulate'),
                        $sl->get('T4webEmployees\Employee\Service\WorkInfoPopulate'),
                        $sl->get('T4webEmployees\Employee\Service\SocialPopulate'),
                        $sl->get('T4webEmployees\Controller\User\ListViewModel')
                    );
                },
                'T4webEmployees\Controller\User\Show' => function (ControllerManager $cm) {
                    $sl = $cm->getServiceLocator();
                    return new ShowController(
                        $sl->get('T4webEmployees\Employee\Service\Finder'),
                        $sl->get('T4webEmployees\Employee\Service\PersonalInfoPopulate'),
                        $sl->get('T4webEmployees\Employee\Service\WorkInfoPopulate'),
                        $sl->get('T4webEmployees\Employee\Service\SocialPopulate'),
                        $sl->get('T4webEmployees\Controller\User\ShowViewModel')
                    );
                },
                'T4webEmployees\Controller\User\Edit' => function (ControllerManager $cm) {
                    die(var_dump(111));
                    $sl = $cm->getServiceLocator();
                    return new EditController(
                        $sl->get('T4webEmployees\Employee\Service\Finder'),
                        $sl->get('T4webEmployees\Employee\Service\PersonalInfoPopulate'),
                        $sl->get('T4webEmployees\Employee\Service\WorkInfoPopulate'),
                        $sl->get('T4webEmployees\Employee\Service\SocialPopulate'),
                        $sl->get('T4webEmployees\Controller\User\EditViewModel')
                    );
                },
                'T4webEmployees\Controller\User\Add' => function (ControllerManager $cm) {
                    $sl = $cm->getServiceLocator();
                    return new AddController(
                        $sl->get('T4webEmployees\Controller\User\AddViewModel')
                    );
                },
                'T4webEmployees\Controller\User\CreateAjax' => function (ControllerManager $cm) {
                    $sl = $cm->getServiceLocator();
                    return new CreateAjaxController(
                        $sl->get('T4webEmployees\ViewModel\SaveAjaxViewModel'),
                        $sl->get('T4webEmployees\Employee\Service\Create')
                    );
                },
                'T4webEmployees\Controller\User\SaveAjax' => function (ControllerManager $cm) {
                    $sl = $cm->getServiceLocator();
                    return new SaveAjaxController(
                        $sl->get('T4webEmployees\ViewModel\SaveAjaxViewModel'),
                        $sl->get('T4webEmployees\Employee\Service\Update')
                    );
                },
            ),
        );
    }
}
