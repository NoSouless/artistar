<?= $this->layout("base"); ?>

<?= $this->start("css") ?>
<link href="<?= url("assets/css/settings/profile.css") ?>" rel="stylesheet" />
<?= $this->stop() ?>

<?= $this->start("conteudo") ?>
<div class="container pt-3 minimum-height">
    <div class="row avoid-navbar">
        <?= $menu ?>
        <div class="col-md-9 col-12 ps-5 p-3">
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="px-5">
                        <h2 class="text-center text-sm-start color-nocturne-purple"><?= $translator->translate("Senha e Segurança") ?></h2>
                    </div>
                </div>
                <div class="col-12">
                    <div class="login-form p-5">
                        <form id="settings-security-form" method="post" action="<?= url('settings/security') ?>" data-success="<?= $translator->translate("Alteração Salva!") ?>" data-error="<?= $translator->translate("Erro ao Salvar!") ?>" data-mismatch="<?= $translator->translate("A confirmação não corresponde à nova senha.") ?>">
                            <div class="mb-3">
                                <label for="senha-atual" class="form-label"><?= $translator->translate("Senha atual") ?></label>
                                <input type="password" class="form-control input-stellar-blue" id="senha-atual" name="senha-atual" autocomplete="current-password" required>
                            </div>
                            <div class="mb-3">
                                <label for="nova-senha" class="form-label"><?= $translator->translate("Nova senha") ?></label>
                                <input type="password" class="form-control input-stellar-blue" id="nova-senha" name="nova-senha" minlength="8" autocomplete="new-password" required>
                                <small class="form-text text-muted"><?= $translator->translate("Mínimo de 8 caracteres.") ?></small>
                            </div>
                            <div class="mb-3">
                                <label for="confirmacao-senha" class="form-label"><?= $translator->translate("Confirmação da nova senha") ?></label>
                                <input type="password" class="form-control input-stellar-blue" id="confirmacao-senha" name="confirmacao-senha" minlength="8" autocomplete="new-password" required>
                            </div>
                            <div class="d-flex justify-content-end align-items-center">
                                <button type="submit" class="btn btn-stellar-blue w-25" id="settings-security-submit"><?= $translator->translate("Salvar") ?></button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <div class="login-form p-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="color-nocturne-purple mb-0"><?= $translator->translate("Dispositivos conectados") ?></h4>
                            <?php if (count($devices) > 1 || (count($devices) === 1 && !$devices[0]['current'])): ?>
                                <button type="button" class="btn btn-gray btn-sm" id="revoke-all-devices"><?= $translator->translate("Desconectar os outros") ?></button>
                            <?php endif; ?>
                        </div>
                        <small class="form-text text-muted d-block mb-3"><?= $translator->translate("Aqui aparecem os dispositivos em que você marcou \"Manter conectado\".") ?></small>
                        <?php if (empty($devices)): ?>
                            <p class="mb-0"><?= $translator->translate("Nenhum dispositivo com login persistente.") ?></p>
                        <?php endif; ?>
                        <ul class="list-group" id="devices-list" data-confirm="<?= $translator->translate("Desconectar este dispositivo?") ?>" data-success="<?= $translator->translate("Alteração Salva!") ?>" data-error="<?= $translator->translate("Erro ao Salvar!") ?>" data-url="<?= url('settings/security/revoke') ?>">
                            <?php foreach ($devices as $device): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= htmlspecialchars($translator->translate($device['description']), ENT_QUOTES, 'UTF-8') ?></strong>
                                        <?php if ($device['current']): ?><span class="badge bg-success ms-2"><?= $translator->translate("Este dispositivo") ?></span><?php endif; ?>
                                        <br>
                                        <small class="text-muted">
                                            IP <?= htmlspecialchars((string) $device['token_ip'], ENT_QUOTES, 'UTF-8') ?> ·
                                            <?= $translator->translate("Último acesso") ?>: <?= date('d/m/Y H:i', strtotime($device['token_ultimo_uso'] ?: $device['token_criado_em'])) ?>
                                        </small>
                                    </div>
                                    <button type="button" class="btn btn-gray btn-sm revoke-device" data-id="<?= (int) $device['token_id'] ?>"><?= $translator->translate("Desconectar") ?></button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->stop() ?>

<?= $this->start("js") ?>
<script src="<?= url("assets/js/settings/security.js") ?>"></script>
<?= $this->stop() ?>
