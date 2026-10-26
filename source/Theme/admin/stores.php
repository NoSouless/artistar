<?= $this->layout("base", $layout); ?>

<?= $this->start("css") ?>
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<div class="container pt-3 minimum-height">
    <div class="row avoid-navbar">
        <div class="col-sm-6 col-12 mb-3 mb-sm-0 px-sm-0">
            <div>
                <h1 class="text-center text-sm-start color-nocturne-purple">Lojas</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 table-responsive-lg">
            <table class="table table-striped table-hover table-responsive-lg">
                <thead>
                    <tr>
                        <th scope="col" class="color-nocturne-purple">#</th>
                        <th scope="col" class="text-center color-nocturne-purple">Ícone</th>
                        <th scope="col" class="text-center color-nocturne-purple">Cadastro</th>
                        <th scope="col" class="color-nocturne-purple">Nome</th>
                        <th scope="col" class="color-nocturne-purple">Nome Completo</th>
                        <th scope="col" class="color-nocturne-purple">Proprietário</th>
                        <th scope="col" class="text-center color-nocturne-purple">Ativa</th>
                        <th scope="col" class="text-center color-nocturne-purple">Última Venda</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stores as $store): ?>
                    <tr>
                        <th scope="row" class="color-stellar-blue"><?= $store['loja_id'] ?></th>
                        <td class="text-center">
                            <?php if (!empty($store['loja_foto'])): ?>
                                <img src="<?= htmlspecialchars(storageURL($store['loja_foto']), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($store['loja_nome'], ENT_QUOTES, 'UTF-8') ?>" style="max-width: 50px; max-height: 50px;">
                            <?php else: ?>
                                <i class="fas fa-store fa-2x color-stellar-blue" aria-label="Sem ícone de loja"></i>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?= date('d/m/Y H:i:s', strtotime($store['loja_dt_criacao'])) ?></td>
                        <td><a href="<?= htmlspecialchars(url($store['loja_nome_unico']), ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($store['loja_nome_unico'], ENT_QUOTES, 'UTF-8') ?></a></td>
                        <td><?= $store['loja_nome'] ?></td>
                        <td><?= $store['usuario_nome_completo'] ?></td>
                        <td class="text-center"><?= $store['loja_ativa'] == 1 ? 'Sim' : 'Não' ?></td>
                        <td class="text-center"><?= !empty($store['ultima_venda']) ? date('d/m/Y H:i:s', strtotime($store['ultima_venda'])) : '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row justify-content-between align-items-center" id="pagination-controls">
        <div class="col-md-6 col-12 d-flex justify-content-md-start justify-content-center mb-2 mb-md-0 px-0">
            <p class="text-muted">Mostrando <span id="result-count"><?= count($stores) ?></span> resultados de <span id="total-count"><?= $total ?></span></p>
        </div>
        <div class="col-md-6 col-12 d-flex justify-content-md-end justify-content-center px-0">
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link stellar-blue" href="?page=<?= $page - 1 ?>">Anterior</a></li>
                    <?php else: ?>
                    <li class="page-item disabled"><span class="page-link stellar-blue">Anterior</span></li>
                    <?php endif; ?>
                    <!-- Max of 4 pages before and 4 after -->
                    <?php
                    $start = max(1, $page - 4);
                    $end = min(ceil($total / $limit), $page + 4);
                    for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : 'd-none d-md-flex' ?>" >
                            <a class="page-link stellar-blue" href="<?= $i == $page ? '#' : '?page='.$i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page < ceil($total / $limit)): ?>
                    <li class="page-item">
                        <a class="page-link stellar-blue" href="?page=<?= $page + 1 ?>">Próxima</a>
                    </li>
                    <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link stellar-blue">Próxima</span>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
        
</div>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<?= $this->stop() ?>