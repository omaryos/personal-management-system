<?php


namespace App\Action\Files;

use App\Controller\Core\AjaxResponse;
use App\Controller\Core\Application;
use App\Controller\Core\Controllers;
use App\Controller\Modules\ModulesController;
use App\Controller\Utils\Utils;
use App\Services\Files\DirectoriesHandler;
use App\Services\Files\FilesHandler;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilesUploadSettingsAction extends AbstractController {

    const TWIG_TEMPLATE_FILE_UPLOAD_SETTINGS = 'modules/common/files-upload-settings.html.twig';

    /**
     * @var Application $app
     */
    private Application $app;

    /**
     * @var Controllers $controllers
     */
    private Controllers $controllers;

    /**
     * @var DirectoriesHandler $directoriesHandler
     */
    private DirectoriesHandler $directoriesHandler;

    /**
     * @var FilesHandler $filesHandler
     */
    private FilesHandler $filesHandler;

    public function __construct(
        Controllers         $controllers,
        Application         $app,
        DirectoriesHandler  $directoriesHandler,
        FilesHandler        $filesHandler
    ) {
        $this->app                = $app;
        $this->controllers        = $controllers;
        $this->directoriesHandler = $directoriesHandler;
        $this->filesHandler       = $filesHandler;
    }

    /**
     * @Route("upload/settings", name="upload_settings")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function displaySettings(Request $request) {

        if (!$request->isXmlHttpRequest()) {
            return $this->renderSettingsPage(false, $request);
        }

        $templateContent = $this->renderSettingsPage(true, $request)->getContent();
        return AjaxResponse::buildJsonResponseForAjaxCall(200, "", $templateContent);
    }

    /**
     * @param bool $ajaxRender
     * @return Response
     * @throws Exception
     */
    private function renderSettingsPage(bool $ajaxRender){

        $renameForm       = $this->app->forms->renameSubdirectoryForm();
        $createSubdirForm = $this->app->forms->createSubdirectoryForm();

        $copyDataForm = $this->app->forms->copyUploadSubdirectoryDataForm();

        $menuNodeModulesNamesToReload = [
            ModulesController::MODULE_NAME_IMAGES,
            ModulesController::MODULE_NAME_FILES,
            ModulesController::MODULE_NAME_VIDEO,
        ];

        $data = [
            'ajax_render'                       => $ajaxRender,
            'rename_form'                       => $renameForm->createView(),
            'copy_data_form'                    => $copyDataForm->createView(),
            'create_subdir_form'                => $createSubdirForm->createView(),
            "menu_node_modules_names_to_reload" => Utils::escapedDoubleQuoteJsonEncode($menuNodeModulesNamesToReload),
        ];

        return $this->render(static::TWIG_TEMPLATE_FILE_UPLOAD_SETTINGS, $data);
    }

}