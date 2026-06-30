<?php

namespace Source\Controllers\Settings;
use Exception;
use Source\Controllers\settingsController;
use Source\Model\Helpers\Storage;
use Source\Model\Settings\Categories;

class categoriesController extends settingsController {

    public function categories() {
        $this->addTranslator('settings/categories');
        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $model = new Categories();
        $categories = $model->getStoreCategories($storeId);  
        $this->addLayout($this->getTranslator()->translate("Categorias"));

        echo $this->view->render("settings/categories/home", [
            'categories' => $categories,
            'menu' => $this->renderMenu('categories'),
        ]);
    }

    public function categoryDetails($get) {
        $this->addTranslator('settings/categories');
        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $model = new Categories();
        $category = $model->getCategoryById($storeId, $get['categoryId']);
        if (empty($category)) {
            header('Location: ' . url('settings/categories'));
            exit;
        }
        $this->addLayout($this->getTranslator()->translate("Categoria: ") . $category['nome']);
        echo $this->view->render("settings/categories/details", [
            'category' => $category,
            'menu' => $this->renderMenu('categories'),
        ]);
    }

    public function newCategory($post) {
        $this->addTranslator('settings/categories');
        $tradutor = $this->getTranslator();
        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $model = new Categories();
        if (empty($post['name'])) exit($this->renderApiResponse(400, $tradutor->translate("O nome da categoria é obrigatório.")));
        if ($model->searchCategoryByName($storeId, $post['name'])) exit($this->renderApiResponse(400, $tradutor->translate("Já existe uma categoria com esse nome.")));
        if ($model->insertCategory($storeId, $post)) {
            exit($this->renderApiResponse(200, $tradutor->translate("Categoria criada com sucesso.")));
        } else {
            exit($this->renderApiResponse(500, $tradutor->translate("Erro ao criar categoria.")));
        }

    }

    public function updateCategory($post) {
        $this->addTranslator('settings/categories');
        $tradutor = $this->getTranslator();
        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        if (empty($post['id'])) exit($this->renderApiResponse(400, $tradutor->translate("ID da categoria é obrigatório.")));
        if (empty($post['name'])) exit($this->renderApiResponse(400, $tradutor->translate("O nome da categoria é obrigatório.")));
        $model = new Categories();
        $update = [
            'id' => filter_var($post['id'], FILTER_SANITIZE_NUMBER_INT),
            'name' => trim((string) ($post['name'] ?? '')),
            'active' => !empty($post['active']) ? 1 : 0,
            'public' => !empty($post['public']) ? 1 : 0,
        ];
        if (empty($model->getCategoryById($storeId, $update['id']))) exit($this->renderApiResponse(404, $tradutor->translate("Categoria não encontrada.")));
        if ($model->searchCategoryByName($storeId, $post['name'], $post['id'])) exit($this->renderApiResponse(400, $tradutor->translate("Já existe uma categoria com esse nome.")));
        try {
        if (!empty($_FILES['thumbnail']['tmp_name'])) {
                $storagePhoto = new Storage();
                $folder = 'uploads/categories/'.$update['id'].'/';
                $imageName = $folder.'thumbnail.'. pathinfo($_FILES['thumbnail']['name'] ?? '', PATHINFO_EXTENSION);
                $storagePhoto->cleanFolderFromBucket($folder);
                $newLogo = $storagePhoto->sendFileToBucket($_FILES['thumbnail']['tmp_name'], $imageName, true)['message'];
                $update['foto'] = $newLogo;
            }
        } catch (Exception $e) {
            // não interrompe a execução, apenas loga o erro e continua
            // error_log("Erro ao enviar thumbnail da categoria: " . $e->getMessage());
        }
        if ($model->updateCategory($storeId, $update)) {
            exit($this->renderApiResponse(200, $tradutor->translate("Categoria atualizada com sucesso.")));
        } else {
            exit($this->renderApiResponse(500, $tradutor->translate("Erro ao atualizar categoria.")));
        }
    }

    public function reorderCategories($post) {
        $this->addTranslator('settings/categories');
        $tradutor = $this->getTranslator();
        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $model = new Categories();
        if (empty($post['order']) || !is_array($post['order'])) exit($this->renderApiResponse(400, $tradutor->translate("Ordem das categorias é obrigatória.")));
        if ($model->reorderCategories($storeId, $post['order'])) {
            exit($this->renderApiResponse(200, $tradutor->translate("Categorias reordenadas com sucesso.")));
        } else {
            exit($this->renderApiResponse(500, $tradutor->translate("Erro ao reordenar categorias.")));
        }
    }

    public function deleteCategory($post) {
        $this->addTranslator('settings/categories');
        $tradutor = $this->getTranslator();
        $storeId = !empty($this->getUser()['loja_id']) ? (int) $this->getUser()['loja_id'] : 0;
        $categoryId = filter_var($post['categoryId'] ?? '', FILTER_SANITIZE_NUMBER_INT);
        if (empty($categoryId)) exit($this->renderApiResponse(400, $tradutor->translate("ID da categoria é obrigatório.")));
        $model = new Categories();
        $category = $model->getCategoryById($storeId, $categoryId);
        if (empty($category)) exit($this->renderApiResponse(404, $tradutor->translate("Categoria não encontrada.")));
        try {
            if (!empty($category['foto'])) {
                $folder = 'uploads/categories/'.$categoryId.'/';
                $storagePhoto = new Storage();
                $storagePhoto->cleanFolderFromBucket($folder);
            }
        } catch (Exception $e) {
            // não interrompe a execução, apenas loga o erro e continua
            // error_log("Erro ao deletar thumbnail da categoria: " . $e->getMessage());
        }
        try {
            if ($model->deleteCategory($storeId, $categoryId)) {
                exit($this->renderApiResponse(200, $tradutor->translate("Categoria deletada com sucesso.")));
            } else {
                exit($this->renderApiResponse(500, $tradutor->translate("Erro ao deletar categoria.")));
            }
        } catch (Exception $e) {
            exit($this->renderApiResponse(500, $tradutor->translate("Erro ao deletar categoria.")));
        }
    }

}