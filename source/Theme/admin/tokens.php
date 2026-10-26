<?= $this->layout("base", $layout); ?>

<?= $this->start("css") ?>
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<div class="container pt-3 minimum-height">
    <div class="row avoid-navbar">
        <div class="col-sm-6 col-12 mb-3 mb-sm-0 px-sm-0">
            <div>
                <h1 class="text-center text-sm-start color-nocturne-purple">Tokens</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 table-responsive-lg">
            <table class="table table-striped table-hover table-responsive">
                <thead>
                    <tr>
                        <th scope="col" class="color-nocturne-purple">#</th>
                        <th scope="col" class="color-nocturne-purple">Usuário</th>
                        <th scope="col" class="text-center color-nocturne-purple">Criado em</th>
                        <th scope="col" class="text-center color-nocturne-purple">Último uso</th>
                        <th scope="col" class="text-center color-nocturne-purple">Expira em</th>
                        <th scope="col" class="color-nocturne-purple">IP</th>
                        <th scope="col" class="color-nocturne-purple">Dispositivo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tokens as $token): ?>
                    <tr>
                        <th scope="row" class="color-stellar-blue"><?= (int) $token['token_id'] ?></th>
                        <td><?= htmlspecialchars(($token['usuario_nome'] ?? '') . ' (' . ($token['usuario_email'] ?? '') . ')', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-center"><?= date('d/m/Y H:i:s', strtotime($token['token_criado_em'])) ?></td>
                        <td class="text-center"><?= !empty($token['token_ultimo_uso']) ? date('d/m/Y H:i:s', strtotime($token['token_ultimo_uso'])) : '-' ?></td>
                        <td class="text-center"><?= date('d/m/Y H:i:s', strtotime($token['token_expira_em'])) ?></td>
                        <td><?= htmlspecialchars($token['token_ip'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($token['token_user_agent'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row justify-content-between align-items-center" id="pagination-controls">
        <div class="col-md-6 col-12 d-flex justify-content-md-start justify-content-center mb-2 mb-md-0 px-0">
            <p class="text-muted">Mostrando <span id="result-count"><?= count($tokens) ?></span> resultados de <span id="total-count"><?= $total ?></span></p>
        </div>
        <div class="col-md-6 col-12 d-flex justify-content-md-end justify-content-center px-0">
            <nav aria-label="Navegação de páginas">
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link stellar-blue" href="?page=<?= $page - 1 ?>">Anterior</a></li>
                    <?php else: ?>
                    <li class="page-item disabled"><span class="page-link stellar-blue">Anterior</span></li>
                    <?php endif; ?>
                    <?php
                    $start = max(1, $page - 4);
                    $end = min(ceil($total / $limit), $page + 4);
                    for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : 'd-none d-md-flex' ?>">
                            <a class="page-link stellar-blue" href="<?= $i == $page ? '#' : '?page=' . $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page < ceil($total / $limit)): ?>
                    <li class="page-item"><a class="page-link stellar-blue" href="?page=<?= $page + 1 ?>">Próxima</a></li>
                    <?php else: ?>
                    <li class="page-item disabled"><span class="page-link stellar-blue">Próxima</span></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<?= $this->stop() ?>
