<?php

namespace PHPMaker2022\project11;

// Page object
$EstadioDelete = &$Page;
?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { estadio: currentTable } });
var currentForm, currentPageID;
var festadiodelete;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    festadiodelete = new ew.Form("festadiodelete", "delete");
    currentPageID = ew.PAGE_ID = "delete";
    currentForm = festadiodelete;
    loadjs.done("festadiodelete");
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
<form name="festadiodelete" id="festadiodelete" class="ew-form ew-delete-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="estadio">
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
<?php if ($Page->id_estadio->Visible) { // id_estadio ?>
        <th class="<?= $Page->id_estadio->headerCellClass() ?>"><span id="elh_estadio_id_estadio" class="estadio_id_estadio"><?= $Page->id_estadio->caption() ?></span></th>
<?php } ?>
<?php if ($Page->id_torneo->Visible) { // id_torneo ?>
        <th class="<?= $Page->id_torneo->headerCellClass() ?>"><span id="elh_estadio_id_torneo" class="estadio_id_torneo"><?= $Page->id_torneo->caption() ?></span></th>
<?php } ?>
<?php if ($Page->nombre_estadio->Visible) { // nombre_estadio ?>
        <th class="<?= $Page->nombre_estadio->headerCellClass() ?>"><span id="elh_estadio_nombre_estadio" class="estadio_nombre_estadio"><?= $Page->nombre_estadio->caption() ?></span></th>
<?php } ?>
<?php if ($Page->foto_estadio->Visible) { // foto_estadio ?>
        <th class="<?= $Page->foto_estadio->headerCellClass() ?>"><span id="elh_estadio_foto_estadio" class="estadio_foto_estadio"><?= $Page->foto_estadio->caption() ?></span></th>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <th class="<?= $Page->crea_dato->headerCellClass() ?>"><span id="elh_estadio_crea_dato" class="estadio_crea_dato"><?= $Page->crea_dato->caption() ?></span></th>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <th class="<?= $Page->modifica_dato->headerCellClass() ?>"><span id="elh_estadio_modifica_dato" class="estadio_modifica_dato"><?= $Page->modifica_dato->caption() ?></span></th>
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
<?php if ($Page->id_estadio->Visible) { // id_estadio ?>
        <td<?= $Page->id_estadio->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_estadio_id_estadio" class="el_estadio_id_estadio">
<span<?= $Page->id_estadio->viewAttributes() ?>>
<?= $Page->id_estadio->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->id_torneo->Visible) { // id_torneo ?>
        <td<?= $Page->id_torneo->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_estadio_id_torneo" class="el_estadio_id_torneo">
<span<?= $Page->id_torneo->viewAttributes() ?>>
<?= $Page->id_torneo->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->nombre_estadio->Visible) { // nombre_estadio ?>
        <td<?= $Page->nombre_estadio->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_estadio_nombre_estadio" class="el_estadio_nombre_estadio">
<span<?= $Page->nombre_estadio->viewAttributes() ?>>
<?= $Page->nombre_estadio->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->foto_estadio->Visible) { // foto_estadio ?>
        <td<?= $Page->foto_estadio->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_estadio_foto_estadio" class="el_estadio_foto_estadio">
<span>
<?= GetFileViewTag($Page->foto_estadio, $Page->foto_estadio->getViewValue(), false) ?>
</span>
</span>
</td>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <td<?= $Page->crea_dato->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_estadio_crea_dato" class="el_estadio_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<?= $Page->crea_dato->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <td<?= $Page->modifica_dato->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_estadio_modifica_dato" class="el_estadio_modifica_dato">
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
