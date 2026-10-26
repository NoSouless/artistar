<?php

namespace Source\Controllers;
use Exception;
use Source\Core\Core;
use Source\Model\Stock;
use Source\Model\Helpers\Storage;

class stockController extends Core {

    public function __construct($router = ROOT) {
        parent::__construct($router);
        $this->validaAcesso();
        $this->getLayout()->setHeader('header-logado');
        $this->getLayout()->setFooter('footer');
    }

    public function home() {
        $this->addTranslator('stock/home');
        $this->addLayout($this->getTranslator()->translate("Inventário"));
        $stockModel = new Stock();
        $stockModel->setTranslator($this->getTranslator());
        $store = $this->getUser()['loja_id'] ?? 0;
        if (empty($store)) exit($this->renderApiResponse(400, $this->getTranslator()->translate("Loja não encontrada.")));
        $search = $_GET['search'] ?? '';
        $filter = $_GET['filter'] ?? [];
        if (!isset($filter['status'])) $filter['status'] = '';
        if (!isset($filter['category'])) $filter['category'] = [];
        if (!isset($filter['price'])) $filter['price'] = '';
        if (!isset($filter['cost'])) $filter['cost'] = '';
        if (!isset($filter['discount'])) $filter['discount'] = '';
        if (!isset($filter['real_price'])) $filter['real_price'] = '';
        if (!isset($filter['stock'])) $filter['stock'] = '';
        if (!isset($filter['min_stock'])) $filter['min_stock'] = '';
        if (!isset($filter['stock_status'])) $filter['stock_status'] = '';
        $pagination = $_GET['pagination'] ?? [
            'offset' => 0
        ];
        $pagination['limit'] = 12;


        $whereStock = $stockModel->buildWhereStock($search, $filter);

        $stocks = $stockModel->getStocks($store, $whereStock);

        $pages = [
            'current' => ($pagination['offset'] / $pagination['limit']) + 1,
            'total' => ceil($stocks['totalProducts'] / $pagination['limit'])
        ]; 
        
        $sort = $_GET['sort'] ?? 'name_asc';
        $orderList = $stockModel->getOrderList($sort);
        $order = $stockModel->buildOrderBy($orderList, $sort);

        $columnsImport = $this->getImportColumns();

        echo $this->view->render("stock/home", [
            'stocks' => $stocks,
            'categories' => $stockModel->getCategories($store),
            'products' => $stockModel->getProducts($store, $pagination, $whereStock, $order),
            'pagination' => $pagination,
            'pages' => $pages,
            'orderList' => $orderList,
            'search' => $search ?? '',
            'filter' => $filter ?? [],
            'sort' => $sort,
            'get' => $_GET,
            'currency' => $this->getUser()['loja_moeda'] ?? 'R$',
            'columnsImport' => $columnsImport
        ]);
        return;
    }

    public function newProduct($post) {
        $this->addTranslator('stock/home');
        $tradutor = $this->getTranslator();
        $stockModel = new Stock();
        $store = $this->getUser()['loja_id'] ?? 0;
        if (empty($store)) exit($this->renderApiResponse(400, $tradutor->translate("Loja não encontrada.")));
        $insertData = [
            'produto_nome' => $post['name'] ?? '',
            'produto_codigo_interno' => $post['insideId'] ?? '',
            'produto_descricao' => $post['description'] ?? '',
            'produto_valor' => str_replace(',', '.', str_replace('.', '', $post['price'])),
            'produto_valor_desconto' => str_replace(',', '.', str_replace('.', '', $post['discount'] ?? '0')),
            'produto_custo' => str_replace(',', '.', str_replace('.', '', $post['cost'] ?? '0')),
            'produto_estoque' => $post['stock'] ?? 0,
            'produto_estoque_minimo' => $post['min_stock'] ?? 0,
            'produto_sob_demanda' => isset($post['produto_sob_demanda']) ? 1 : 0,
            'produto_loja' => $store,
            'produto_ativo' => isset($post['active']) ? 1 : 0,
            'produto_palavras_chave' => isset($post['keywords']) ? implode('|', $post['keywords']) : '',
        ];
        if (empty($insertData['produto_nome'])) {
            exit($this->renderApiResponse(400, $tradutor->translate("Nome do produto é obrigatório.")));
        }
        $productId = $stockModel->insertProduct($insertData);
        if (!$productId) {
            exit($this->renderApiResponse(500, $tradutor->translate("Erro ao inserir produto.")));
        } else {
            $categories = $post['category'] ?? [];
            $stockModel->insertProductCategories($categories, $store, $productId);
            if (!empty($_FILES['thumbnail']['tmp_name'])) {
                $storage = new Storage();
                $folder = 'uploads/products/'.$productId.'/';
                $imageName = $folder.'thumbnail.'. pathinfo($_FILES['thumbnail']['name'] ?? '', PATHINFO_EXTENSION);
                $thumbnail = $storage->sendFileToBucket($_FILES['thumbnail']['tmp_name'], $imageName, true)['message'];
                $stockModel->updateThumbnail($thumbnail, $productId);
            }
            
            
        }
        exit($this->renderApiResponse(200, $tradutor->translate("Produto inserido com sucesso."), ['productId' => $productId]));
    }

    private function getImportColumns() {
        return [
            'name' => 'Nome',
            'internal_id' => 'Identificação Interna',
            'categories' => 'Categorias',
            'keywords' => 'Palavras-Chave',
            'description' => 'Descrição',
            'price' => 'Preço',
            'cost' => 'Custo',
            'discount' => 'Desconto',
            'stock' => 'Estoque',
            'minimum_stock' => 'Estoque Mínimo'
        ];
    }

    public function exportProducts() {
        $this->addTranslator('stock/home');
        $store = $this->getUser()['loja_id'] ?? 0;
        if (empty($store)) exit($this->renderApiResponse(400, "Loja não encontrada."));
        $stockModel = new Stock();
        $search = $_GET['search'] ?? '';
        $filter = $_GET['filter'] ?? [];
        $where = $stockModel->buildWhereStock($search, $filter);
        $sort = $_GET['sort'] ?? 'name_asc';
        $stockModel->setTranslator($this->getTranslator());
        $order = $stockModel->buildOrderBy($stockModel->getOrderList($sort), $sort);
        $products = $stockModel->getProductsForExport($store, $where, $order);

        $columns = $this->getImportColumns();
        $headers = array_keys($columns);
        $sheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $ws = $sheet->getActiveSheet();
        $translator = $this->getTranslator();
        $ws->fromArray(array_map(fn($label) => $translator->translate($label), array_values($columns)), null, 'A1');
        $ws->getStyle('A1:J1')->getFont()->setBold(true);
        $rowNumber = 2;
        foreach ($products as $product) {
            foreach ($headers as $i => $key) {
                $value = $product[$key] ?? '';
                $ws->setCellValueExplicit([$i + 1, $rowNumber], $value, in_array($key, ['price', 'cost', 'discount', 'stock', 'minimum_stock'])
                    ? \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC
                    : \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
            $rowNumber++;
        }
        foreach (range('A', 'J') as $col) $ws->getColumnDimension($col)->setAutoSize(true);

        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="produtos_' . date('Y-m-d_His') . '.xlsx"');
        header('Cache-Control: max-age=0');
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($sheet))->save('php://output');
        exit;
    }

    public function importProducts($post) {
        $this->addTranslator('stock/home');
        $tradutor = $this->getTranslator();
        $stockModel = new Stock();
        $store = $this->getUser()['loja_id'] ?? 0;
        if (empty($store)) exit($this->renderApiResponse(400, $tradutor->translate("Loja não encontrada.")));
        if (empty($_FILES['importFile']['tmp_name'])) {
            exit($this->renderApiResponse(400, $tradutor->translate("Nenhum arquivo enviado.")));
        }
        try {
            $result = $stockModel->importProductsFromFile($_FILES['importFile'], $store, $_POST['mapping'] ?? []);
            exit($this->renderApiResponse(200, $tradutor->translate("Produtos importados com sucesso!"), [
                'created' => $result['created'], 
                'updated' => $result['updated']
            ]));
        } catch (Exception $e) {
            exit($this->renderApiResponse(500, $tradutor->translate("Erro ao importar produtos: ") . $e->getMessage()));
        }
    }

    public function productDetails($get) {
        $this->addTranslator('stock/productDetails');
        $productId = $get['productId'] ?? 0;
        $stockModel = new Stock();
        $store = $this->getUser()['loja_id'] ?? 0;
        if (empty($store)) exit($this->renderApiResponse(400, $this->getTranslator()->translate("Loja não encontrada.")));
        $product = $stockModel->getProductById($productId, $store);
        if (empty($product['id'])) header("Location: ".url('stock'));
        $this->addLayout($product['nome']);
        echo $this->view->render("stock/productDetails", [
            'product' => $product,
            'categories' => $stockModel->getCategories($store),
            'currency' => $this->getUser()['loja_moeda'] ?? 'R$'
        ]);
        return;
    }

    public function alterProduct($post) {
        $this->addTranslator('stock/productDetails');
        $tradutor = $this->getTranslator();
        $productId = $post['productId'] ?? 0;
        $store = $this->getUser()['loja_id'] ?? 0; 
        $stockModel = new Stock();
        $product = $stockModel->getProductById($productId, $store);
        if (empty($store)) exit($this->renderApiResponse(400, $tradutor->translate("Loja não encontrada.")));
        if (empty($product)) exit($this->renderApiResponse(400, $tradutor->translate("Produto não encontrado.")));
        $update = [
            'produto_nome' => $post['name'] ?? '',
            'produto_descricao' => $post['description'] ?? '',
            'produto_valor' => desconverteMoedaReal($post['price']),
            'produto_valor_desconto' => desconverteMoedaReal($post['discount'] ?? '0'),
            'produto_custo' => desconverteMoedaReal($post['cost'] ?? '0'),
            'produto_estoque' => $post['stock'] ?? 0,
            'produto_estoque_minimo' => $post['min_stock'] ?? 0,
            'produto_sob_demanda' => isset($post['produto_sob_demanda']) ? 1 : 0,
            'produto_ativo' => isset($post['active']) ? 1 : 0,
            'produto_palavras_chave' => isset($post['keywords']) ? implode('|', $post['keywords']) : '',
            'produto_codigo_interno' => $post['insideId'] ?? ''
        ];
        try {
            $stockModel->updateProduct($productId, $store, $update);
            if (!isset($post['category'])) $post['category'] = [];
            $stockModel->addNewProductCategories($post['category'], $store, $productId);
            if (!empty($_FILES['thumbnail']['tmp_name'])) {
                $storage = new Storage();
                $folder = 'uploads/products/'.$productId.'/';
                $imageName = $folder.'thumbnail.'. pathinfo($_FILES['thumbnail']['name'] ?? '', PATHINFO_EXTENSION);
                $storage->cleanFolderFromBucket($folder);
                $newThumbnail = $storage->sendFileToBucket($_FILES['thumbnail']['tmp_name'], $imageName, true)['message'];
                $stockModel->updateThumbnail($newThumbnail, $productId);
            }
        } catch (Exception $e) {
            exit($this->renderApiResponse(500, $tradutor->translate("Erro ao atualizar produto: ") . $e->getMessage()));
        }

        exit($this->renderApiResponse(200, $tradutor->translate("Produto atualizado com sucesso.")));

        return;
    }

    public function duplicateProduct($post) {
        $this->addTranslator('stock/productDetails');
        $tradutor = $this->getTranslator();
        $productId = $post['productId'] ?? 0;
        $store = $this->getUser()['loja_id'] ?? 0; 
        $stockModel = new Stock();
        $product = $stockModel->getProductById($productId, $store);
        if (empty($store)) exit($this->renderApiResponse(400, $tradutor->translate("Loja não encontrada.")));
        if (empty($product)) exit($this->renderApiResponse(400, $tradutor->translate("Produto não encontrado.")));
        
        try {
            $newProductId = $stockModel->duplicateProduct($productId, $store, $tradutor->translate("Cópia"));
            if (!$newProductId) exit($this->renderApiResponse(500, $tradutor->translate("Erro ao duplicar produto.")));
            $stockModel->duplicateProductCategories($productId, $newProductId, $store);
            if (!empty($product['thumbnail'])) {
                $storage = new Storage();
                $folder = 'uploads/products/'.$newProductId.'/';
                $newThumbnail = $storage->copyFileFromBucket($product['thumbnail'], $folder.'thumbnail.'. pathinfo($product['thumbnail'], PATHINFO_EXTENSION))['message'] ?? '';
                if($newThumbnail) $stockModel->updateThumbnail($newThumbnail, $newProductId);
            }
        } catch (Exception $e) {
            exit($this->renderApiResponse(500, $tradutor->translate("Erro ao duplicar produto: ") . $e->getMessage()));
        }

        exit($this->renderApiResponse(200, $tradutor->translate("Produto duplicado com sucesso."), ['newProductId' => $newProductId]));
    }

    public function deleteProduct($post) {
        $this->addTranslator('stock/productDetails');
        $tradutor = $this->getTranslator();
        $productId = $post['productId'] ?? 0;
        $store = $this->getUser()['loja_id'] ?? 0; 
        $stockModel = new Stock();
        $product = $stockModel->getProductById($productId, $store);
        if (empty($store)) exit($this->renderApiResponse(400, $tradutor->translate("Loja não encontrada.")));
        if (empty($product)) exit($this->renderApiResponse(400, $tradutor->translate("Produto não encontrado.")));
        
        try {
            $stockModel->deleteProduct($productId, $store);
            $stockModel->deleteProductCategories($productId, $store);
            if (!empty($product['thumbnail'])) {
                $storage = new Storage();
                $storage->deleteFileFromBucket('uploads/products/'.$productId.'/thumbnail.'. pathinfo($product['thumbnail'], PATHINFO_EXTENSION), true);
            }
        } catch (Exception $e) {
            exit($this->renderApiResponse(500, $tradutor->translate("Erro ao excluir produto: ") . $e->getMessage()));
        }

        exit($this->renderApiResponse(200, $tradutor->translate("Produto excluído com sucesso.")));
    }
}