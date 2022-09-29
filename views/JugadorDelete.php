<?php

namespace PHPMaker2022\project11;

// Page object
$JugadorDelete = &$Page;
?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { jugador: currentTable } });
var currentForm, currentPageID;
var fjugadordelete;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fjugadordelete = new ew.Form("fjugadordelete", "delete");
    currentPageID = ew.PAGE_ID = "delete";
    currentForm = fjugadordelete;
    loadjs.done("fjugadordelete");
});
</script>
<script>
loadjs.ready("head", function () {
    // Write your table-specific client script here, no need to add script tags.
});
</script>
<?php $Page->showPageHeader(); ?>
<?php
$Page->showMessage();
?>
<form name="fjugadordelete" id="fjugadordelete" class="ew-form ew-delete-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="jugador">
<input type="hidden" name="action" id="action" value="delete">
<?php foreach ($Page->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode(Config("COMPOSITE_KEY_SEPARATOR"), $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?= HtmlEncode($keyvalue) ?>">
<?php } ?>
<div class="card ew-card ew-grid">
<div class="<?= ResponsiveTableClass() ?>card-body ew-grid-middle-panel">
<table class="table table-bordered table-hover table-sm ew-table">
    <thead>
    <tr class="ew-table-header">
<?php if ($Page->id_jugador->Visible) { // id_jugador ?>
        <th class="<?= $Page->id_jugador->headerCellClass() ?>"><span id="elh_jugador_id_jugador" class="jugador_id_jugador"><?= $Page->id_jugador->caption() ?></span></th>
<?php } ?>
<?php if ($Page->nombre_jugador->Visible) { // nombre_jugador ?>
        <th class="<?= $Page->nombre_jugador->headerCellClass() ?>"><span id="elh_jugador_nombre_jugador" class="jugador_nombre_jugador"><?= $Page->nombre_jugador->caption() ?></span></th>
<?php } ?>
<?php if ($Page->votos_jugador->Visible) { // votos_jugador ?>
        <th class="<?= $Page->votos_jugador->headerCellClass() ?>"><span id="elh_jugador_votos_jugador" class="jugador_votos_jugador"><?= $Page->votos_jugador->caption() ?></span></th>
<?php } ?>
<?php if ($Page->imagen_jugador->Visible) { // imagen_jugador ?>
        <th class="<?= $Page->imagen_jugador->headerCellClass() ?>"><span id="elh_jugador_imagen_jugador" class="jugador_imagen_jugador"><?= $Page->imagen_jugador->caption() ?></span></th>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <th class="<?= $Page->crea_dato->headerCellClass() ?>"><span id="elh_jugador_crea_dato" class="jugador_crea_dato"><?= $Page->crea_dato->caption() ?></span></th>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <th class="<?= $Page->modifica_dato->headerCellClass() ?>"><span id="elh_jugador_modifica_dato" class="jugador_modifica_dato"><?= $Page->modifica_dato->caption() ?></span></th>
<?php } ?>
<?php if ($Page->usuario_dato->Visible) { // usuario_dato ?>
        <th class="<?= $Page->usuario_dato->headerCellClass() ?>"><span id="elh_jugador_usuario_dato" class="jugador_usuario_dato"><?= $Page->usuario_dato->caption() ?></span></th>
<?php } ?>
<?php if ($Page->posicion->Visible) { // posicion ?>
        <th class="<?= $Page->posicion->headerCellClass() ?>"><span id="elh_jugador_posicion" class="jugador_posicion"><?= $Page->posicion->caption() ?></span></th>
<?php } ?>
    </tr>
    </thead>
    <tbody>
<?php
$Page->RecordCount = 0;
$i = 0;
while (!$Page->Recordset->EOF) {
    $Page->RecordCount++;
    $Page->RowCount++;

    // Set row properties
    $Page->resetAttributes();
    $Page->RowType = ROWTYPE_VIEW; // View

    // Get the field contents
    $Page->loadRowValues($Page->Recordset);

    // Render row
    $Page->renderRow();
?>
    <tr <?= $Page->rowAttributes() ?>>
<?php if ($Page->id_jugador->Visible) { // id_jugador ?>
        <td<?= $Page->id_jugador->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_jugador_id_jugador" class="el_jugador_id_jugador">
<span<?= $Page->id_jugador->viewAttributes() ?>>
<?= $Page->id_jugador->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->nombre_jugador->Visible) { // nombre_jugador ?>
        <td<?= $Page->nombre_jugador->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_jugador_nombre_jugador" class="el_jugador_nombre_jugador">
<span<?= $Page->nombre_jugador->viewAttributes() ?>>
<?= $Page->nombre_jugador->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->votos_jugador->Visible) { // votos_jugador ?>
        <td<?= $Page->votos_jugador->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_jugador_votos_jugador" class="el_jugador_votos_jugador">
<span<?= $Page->votos_jugador->viewAttributes() ?>>
<?= $Page->votos_jugador->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->imagen_jugador->Visible) { // imagen_jugador ?>
        <td<?= $Page->imagen_jugador->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_jugador_imagen_jugador" class="el_jugador_imagen_jugador">
<span>
<?= GetFileViewTag($Page->imagen_jugador, $Page->imagen_jugador->getViewValue(), false) ?>
</span>
</span>
</td>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <td<?= $Page->crea_dato->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_jugador_crea_dato" class="el_jugador_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<?= $Page->crea_dato->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <td<?= $Page->modifica_dato->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_jugador_modifica_dato" class="el_jugador_modifica_dato">
<span<?= $Page->modifica_dato->viewAttributes() ?>>
<?= $Page->modifica_dato->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->usuario_dato->Visible) { // usuario_dato ?>
        <td<?= $Page->usuario_dato->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_jugador_usuario_dato" class="el_jugador_usuario_dato">
<span<?= $Page->usuario_dato->viewAttributes() ?>>
<?= $Page->usuario_dato->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->posicion->Visible) { // posicion ?>
        <td<?= $Page->posicion->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_jugador_posicion" class="el_jugador_posicion">
<span<?= $Page->posicion->viewAttributes() ?>>
<?= $Page->posicion->getViewValue() ?></span>
</span>
</td>
<?php } ?>
    </tr>
<?php
    $Page->Recordset->moveNext();
}
$Page->Recordset->close();
?>
</tbody>
</table>
</div>
</div>
<div>
<button class="btn btn-primary ew-btn" name="btn-action" id="btn-action" type="submit"><?= $Language->phrase("DeleteBtn") ?></button>
<button class="btn btn-default ew-btn" name="btn-cancel" id="btn-cancel" type="button" data-href="<?= HtmlEncode(GetUrl($Page->getReturnUrl())) ?>"><?= $Language->phrase("CancelBtn") ?></button>
</div>
</form>
<?php
$Page->showPageFooter();
echo GetDebugMessage();
?>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
