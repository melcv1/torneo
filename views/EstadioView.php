<?php

namespace PHPMaker2022\project11;

// Page object
$EstadioView = &$Page;
?>
<?php if (!$Page->isExport()) { ?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { estadio: currentTable } });
var currentForm, currentPageID;
var festadioview;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    festadioview = new ew.Form("festadioview", "view");
    currentPageID = ew.PAGE_ID = "view";
    currentForm = festadioview;
    loadjs.done("festadioview");
});
</script>
<script>
loadjs.ready("head", function () {
    // Write your table-specific client script here, no need to add script tags.
});
</script>
<?php } ?>
<?php if (!$Page->isExport()) { ?>
<div class="btn-toolbar ew-toolbar">
<?php $Page->ExportOptions->render("body") ?>
<?php $Page->OtherOptions->render("body") ?>
</div>
<?php } ?>
<?php $Page->showPageHeader(); ?>
<?php
$Page->showMessage();
?>
<form name="festadioview" id="festadioview" class="ew-form ew-view-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="estadio">
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<table class="table table-striped table-bordered table-hover table-sm ew-view-table">
<?php if ($Page->id_estadio->Visible) { // id_estadio ?>
    <tr id="r_id_estadio"<?= $Page->id_estadio->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_estadio_id_estadio"><?= $Page->id_estadio->caption() ?></span></td>
        <td data-name="id_estadio"<?= $Page->id_estadio->cellAttributes() ?>>
<span id="el_estadio_id_estadio">
<span<?= $Page->id_estadio->viewAttributes() ?>>
<?= $Page->id_estadio->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->id_torneo->Visible) { // id_torneo ?>
    <tr id="r_id_torneo"<?= $Page->id_torneo->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_estadio_id_torneo"><?= $Page->id_torneo->caption() ?></span></td>
        <td data-name="id_torneo"<?= $Page->id_torneo->cellAttributes() ?>>
<span id="el_estadio_id_torneo">
<span<?= $Page->id_torneo->viewAttributes() ?>>
<?= $Page->id_torneo->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->nombre_estadio->Visible) { // nombre_estadio ?>
    <tr id="r_nombre_estadio"<?= $Page->nombre_estadio->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_estadio_nombre_estadio"><?= $Page->nombre_estadio->caption() ?></span></td>
        <td data-name="nombre_estadio"<?= $Page->nombre_estadio->cellAttributes() ?>>
<span id="el_estadio_nombre_estadio">
<span<?= $Page->nombre_estadio->viewAttributes() ?>>
<?= $Page->nombre_estadio->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->foto_estadio->Visible) { // foto_estadio ?>
    <tr id="r_foto_estadio"<?= $Page->foto_estadio->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_estadio_foto_estadio"><?= $Page->foto_estadio->caption() ?></span></td>
        <td data-name="foto_estadio"<?= $Page->foto_estadio->cellAttributes() ?>>
<span id="el_estadio_foto_estadio">
<span>
<?= GetFileViewTag($Page->foto_estadio, $Page->foto_estadio->getViewValue(), false) ?>
</span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
    <tr id="r_crea_dato"<?= $Page->crea_dato->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_estadio_crea_dato"><?= $Page->crea_dato->caption() ?></span></td>
        <td data-name="crea_dato"<?= $Page->crea_dato->cellAttributes() ?>>
<span id="el_estadio_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<?= $Page->crea_dato->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
    <tr id="r_modifica_dato"<?= $Page->modifica_dato->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_estadio_modifica_dato"><?= $Page->modifica_dato->caption() ?></span></td>
        <td data-name="modifica_dato"<?= $Page->modifica_dato->cellAttributes() ?>>
<span id="el_estadio_modifica_dato">
<span<?= $Page->modifica_dato->viewAttributes() ?>>
<?= $Page->modifica_dato->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->usuario_dato->Visible) { // usuario_dato ?>
    <tr id="r_usuario_dato"<?= $Page->usuario_dato->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_estadio_usuario_dato"><?= $Page->usuario_dato->caption() ?></span></td>
        <td data-name="usuario_dato"<?= $Page->usuario_dato->cellAttributes() ?>>
<span id="el_estadio_usuario_dato">
<span<?= $Page->usuario_dato->viewAttributes() ?>>
<?= $Page->usuario_dato->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
</table>
</form>
<?php
$Page->showPageFooter();
echo GetDebugMessage();
?>
<?php if (!$Page->isExport()) { ?>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
<?php } ?>
