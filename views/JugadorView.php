<?php

namespace PHPMaker2022\project11;

// Page object
$JugadorView = &$Page;
?>
<?php if (!$Page->isExport()) { ?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { jugador: currentTable } });
var currentForm, currentPageID;
var fjugadorview;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fjugadorview = new ew.Form("fjugadorview", "view");
    currentPageID = ew.PAGE_ID = "view";
    currentForm = fjugadorview;
    loadjs.done("fjugadorview");
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
<form name="fjugadorview" id="fjugadorview" class="ew-form ew-view-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="jugador">
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<table class="table table-striped table-bordered table-hover table-sm ew-view-table">
<?php if ($Page->id_jugador->Visible) { // id_jugador ?>
    <tr id="r_id_jugador"<?= $Page->id_jugador->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_jugador_id_jugador"><?= $Page->id_jugador->caption() ?></span></td>
        <td data-name="id_jugador"<?= $Page->id_jugador->cellAttributes() ?>>
<span id="el_jugador_id_jugador">
<span<?= $Page->id_jugador->viewAttributes() ?>>
<?= $Page->id_jugador->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->nombre_jugador->Visible) { // nombre_jugador ?>
    <tr id="r_nombre_jugador"<?= $Page->nombre_jugador->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_jugador_nombre_jugador"><?= $Page->nombre_jugador->caption() ?></span></td>
        <td data-name="nombre_jugador"<?= $Page->nombre_jugador->cellAttributes() ?>>
<span id="el_jugador_nombre_jugador">
<span<?= $Page->nombre_jugador->viewAttributes() ?>>
<?= $Page->nombre_jugador->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->votos_jugador->Visible) { // votos_jugador ?>
    <tr id="r_votos_jugador"<?= $Page->votos_jugador->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_jugador_votos_jugador"><?= $Page->votos_jugador->caption() ?></span></td>
        <td data-name="votos_jugador"<?= $Page->votos_jugador->cellAttributes() ?>>
<span id="el_jugador_votos_jugador">
<span<?= $Page->votos_jugador->viewAttributes() ?>>
<?= $Page->votos_jugador->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->imagen_jugador->Visible) { // imagen_jugador ?>
    <tr id="r_imagen_jugador"<?= $Page->imagen_jugador->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_jugador_imagen_jugador"><?= $Page->imagen_jugador->caption() ?></span></td>
        <td data-name="imagen_jugador"<?= $Page->imagen_jugador->cellAttributes() ?>>
<span id="el_jugador_imagen_jugador">
<span>
<?= GetFileViewTag($Page->imagen_jugador, $Page->imagen_jugador->getViewValue(), false) ?>
</span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
    <tr id="r_crea_dato"<?= $Page->crea_dato->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_jugador_crea_dato"><?= $Page->crea_dato->caption() ?></span></td>
        <td data-name="crea_dato"<?= $Page->crea_dato->cellAttributes() ?>>
<span id="el_jugador_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<?= $Page->crea_dato->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
    <tr id="r_modifica_dato"<?= $Page->modifica_dato->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_jugador_modifica_dato"><?= $Page->modifica_dato->caption() ?></span></td>
        <td data-name="modifica_dato"<?= $Page->modifica_dato->cellAttributes() ?>>
<span id="el_jugador_modifica_dato">
<span<?= $Page->modifica_dato->viewAttributes() ?>>
<?= $Page->modifica_dato->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->usuario_dato->Visible) { // usuario_dato ?>
    <tr id="r_usuario_dato"<?= $Page->usuario_dato->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_jugador_usuario_dato"><?= $Page->usuario_dato->caption() ?></span></td>
        <td data-name="usuario_dato"<?= $Page->usuario_dato->cellAttributes() ?>>
<span id="el_jugador_usuario_dato">
<span<?= $Page->usuario_dato->viewAttributes() ?>>
<?= $Page->usuario_dato->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->posicion->Visible) { // posicion ?>
    <tr id="r_posicion"<?= $Page->posicion->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_jugador_posicion"><?= $Page->posicion->caption() ?></span></td>
        <td data-name="posicion"<?= $Page->posicion->cellAttributes() ?>>
<span id="el_jugador_posicion">
<span<?= $Page->posicion->viewAttributes() ?>>
<?= $Page->posicion->getViewValue() ?></span>
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
