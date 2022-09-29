<?php

namespace PHPMaker2022\project11;

// Page object
$JugadorequipoDelete = &$Page;
?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { jugadorequipo: currentTable } });
var currentForm, currentPageID;
var fjugadorequipodelete;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fjugadorequipodelete = new ew.Form("fjugadorequipodelete", "delete");
    currentPageID = ew.PAGE_ID = "delete";
    currentForm = fjugadorequipodelete;
    loadjs.done("fjugadorequipodelete");
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
<form name="fjugadorequipodelete" id="fjugadorequipodelete" class="ew-form ew-delete-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="jugadorequipo">
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
<?php if ($Page->id_jugadorequipo->Visible) { // id_jugadorequipo ?>
        <th class="<?= $Page->id_jugadorequipo->headerCellClass() ?>"><span id="elh_jugadorequipo_id_jugadorequipo" class="jugadorequipo_id_jugadorequipo"><?= $Page->id_jugadorequipo->caption() ?></span></th>
<?php } ?>
<?php if ($Page->id_equipo->Visible) { // id_equipo ?>
        <th class="<?= $Page->id_equipo->headerCellClass() ?>"><span id="elh_jugadorequipo_id_equipo" class="jugadorequipo_id_equipo"><?= $Page->id_equipo->caption() ?></span></th>
<?php } ?>
<?php if ($Page->id_jugador->Visible) { // id_jugador ?>
        <th class="<?= $Page->id_jugador->headerCellClass() ?>"><span id="elh_jugadorequipo_id_jugador" class="jugadorequipo_id_jugador"><?= $Page->id_jugador->caption() ?></span></th>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <th class="<?= $Page->crea_dato->headerCellClass() ?>"><span id="elh_jugadorequipo_crea_dato" class="jugadorequipo_crea_dato"><?= $Page->crea_dato->caption() ?></span></th>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <th class="<?= $Page->modifica_dato->headerCellClass() ?>"><span id="elh_jugadorequipo_modifica_dato" class="jugadorequipo_modifica_dato"><?= $Page->modifica_dato->caption() ?></span></th>
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
<?php if ($Page->id_jugadorequipo->Visible) { // id_jugadorequipo ?>
        <td<?= $Page->id_jugadorequipo->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_jugadorequipo_id_jugadorequipo" class="el_jugadorequipo_id_jugadorequipo">
<span<?= $Page->id_jugadorequipo->viewAttributes() ?>>
<?= $Page->id_jugadorequipo->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->id_equipo->Visible) { // id_equipo ?>
        <td<?= $Page->id_equipo->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_jugadorequipo_id_equipo" class="el_jugadorequipo_id_equipo">
<span<?= $Page->id_equipo->viewAttributes() ?>>
<?= $Page->id_equipo->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->id_jugador->Visible) { // id_jugador ?>
        <td<?= $Page->id_jugador->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_jugadorequipo_id_jugador" class="el_jugadorequipo_id_jugador">
<span<?= $Page->id_jugador->viewAttributes() ?>>
<?= $Page->id_jugador->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <td<?= $Page->crea_dato->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_jugadorequipo_crea_dato" class="el_jugadorequipo_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<?= $Page->crea_dato->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <td<?= $Page->modifica_dato->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_jugadorequipo_modifica_dato" class="el_jugadorequipo_modifica_dato">
<span<?= $Page->modifica_dato->viewAttributes() ?>>
<?= $Page->modifica_dato->getViewValue() ?></span>
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
