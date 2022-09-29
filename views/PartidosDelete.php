<?php

namespace PHPMaker2022\project11;

// Page object
$PartidosDelete = &$Page;
?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { partidos: currentTable } });
var currentForm, currentPageID;
var fpartidosdelete;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fpartidosdelete = new ew.Form("fpartidosdelete", "delete");
    currentPageID = ew.PAGE_ID = "delete";
    currentForm = fpartidosdelete;
    loadjs.done("fpartidosdelete");
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
<form name="fpartidosdelete" id="fpartidosdelete" class="ew-form ew-delete-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="partidos">
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
<?php if ($Page->ID_TORNEO->Visible) { // ID_TORNEO ?>
        <th class="<?= $Page->ID_TORNEO->headerCellClass() ?>"><span id="elh_partidos_ID_TORNEO" class="partidos_ID_TORNEO"><?= $Page->ID_TORNEO->caption() ?></span></th>
<?php } ?>
<?php if ($Page->equipo_local->Visible) { // equipo_local ?>
        <th class="<?= $Page->equipo_local->headerCellClass() ?>"><span id="elh_partidos_equipo_local" class="partidos_equipo_local"><?= $Page->equipo_local->caption() ?></span></th>
<?php } ?>
<?php if ($Page->equipo_visitante->Visible) { // equipo_visitante ?>
        <th class="<?= $Page->equipo_visitante->headerCellClass() ?>"><span id="elh_partidos_equipo_visitante" class="partidos_equipo_visitante"><?= $Page->equipo_visitante->caption() ?></span></th>
<?php } ?>
<?php if ($Page->ID_PARTIDO->Visible) { // ID_PARTIDO ?>
        <th class="<?= $Page->ID_PARTIDO->headerCellClass() ?>"><span id="elh_partidos_ID_PARTIDO" class="partidos_ID_PARTIDO"><?= $Page->ID_PARTIDO->caption() ?></span></th>
<?php } ?>
<?php if ($Page->FECHA_PARTIDO->Visible) { // FECHA_PARTIDO ?>
        <th class="<?= $Page->FECHA_PARTIDO->headerCellClass() ?>"><span id="elh_partidos_FECHA_PARTIDO" class="partidos_FECHA_PARTIDO"><?= $Page->FECHA_PARTIDO->caption() ?></span></th>
<?php } ?>
<?php if ($Page->HORA_PARTIDO->Visible) { // HORA_PARTIDO ?>
        <th class="<?= $Page->HORA_PARTIDO->headerCellClass() ?>"><span id="elh_partidos_HORA_PARTIDO" class="partidos_HORA_PARTIDO"><?= $Page->HORA_PARTIDO->caption() ?></span></th>
<?php } ?>
<?php if ($Page->ESTADIO->Visible) { // ESTADIO ?>
        <th class="<?= $Page->ESTADIO->headerCellClass() ?>"><span id="elh_partidos_ESTADIO" class="partidos_ESTADIO"><?= $Page->ESTADIO->caption() ?></span></th>
<?php } ?>
<?php if ($Page->CIUDAD_PARTIDO->Visible) { // CIUDAD_PARTIDO ?>
        <th class="<?= $Page->CIUDAD_PARTIDO->headerCellClass() ?>"><span id="elh_partidos_CIUDAD_PARTIDO" class="partidos_CIUDAD_PARTIDO"><?= $Page->CIUDAD_PARTIDO->caption() ?></span></th>
<?php } ?>
<?php if ($Page->PAIS_PARTIDO->Visible) { // PAIS_PARTIDO ?>
        <th class="<?= $Page->PAIS_PARTIDO->headerCellClass() ?>"><span id="elh_partidos_PAIS_PARTIDO" class="partidos_PAIS_PARTIDO"><?= $Page->PAIS_PARTIDO->caption() ?></span></th>
<?php } ?>
<?php if ($Page->GOLES_LOCAL->Visible) { // GOLES_LOCAL ?>
        <th class="<?= $Page->GOLES_LOCAL->headerCellClass() ?>"><span id="elh_partidos_GOLES_LOCAL" class="partidos_GOLES_LOCAL"><?= $Page->GOLES_LOCAL->caption() ?></span></th>
<?php } ?>
<?php if ($Page->GOLES_VISITANTE->Visible) { // GOLES_VISITANTE ?>
        <th class="<?= $Page->GOLES_VISITANTE->headerCellClass() ?>"><span id="elh_partidos_GOLES_VISITANTE" class="partidos_GOLES_VISITANTE"><?= $Page->GOLES_VISITANTE->caption() ?></span></th>
<?php } ?>
<?php if ($Page->GOLES_EXTRA_EQUIPO1->Visible) { // GOLES_EXTRA_EQUIPO1 ?>
        <th class="<?= $Page->GOLES_EXTRA_EQUIPO1->headerCellClass() ?>"><span id="elh_partidos_GOLES_EXTRA_EQUIPO1" class="partidos_GOLES_EXTRA_EQUIPO1"><?= $Page->GOLES_EXTRA_EQUIPO1->caption() ?></span></th>
<?php } ?>
<?php if ($Page->GOLES_EXTRA_EQUIPO2->Visible) { // GOLES_EXTRA_EQUIPO2 ?>
        <th class="<?= $Page->GOLES_EXTRA_EQUIPO2->headerCellClass() ?>"><span id="elh_partidos_GOLES_EXTRA_EQUIPO2" class="partidos_GOLES_EXTRA_EQUIPO2"><?= $Page->GOLES_EXTRA_EQUIPO2->caption() ?></span></th>
<?php } ?>
<?php if ($Page->NOTA_PARTIDO->Visible) { // NOTA_PARTIDO ?>
        <th class="<?= $Page->NOTA_PARTIDO->headerCellClass() ?>"><span id="elh_partidos_NOTA_PARTIDO" class="partidos_NOTA_PARTIDO"><?= $Page->NOTA_PARTIDO->caption() ?></span></th>
<?php } ?>
<?php if ($Page->RESUMEN_PARTIDO->Visible) { // RESUMEN_PARTIDO ?>
        <th class="<?= $Page->RESUMEN_PARTIDO->headerCellClass() ?>"><span id="elh_partidos_RESUMEN_PARTIDO" class="partidos_RESUMEN_PARTIDO"><?= $Page->RESUMEN_PARTIDO->caption() ?></span></th>
<?php } ?>
<?php if ($Page->ESTADO_PARTIDO->Visible) { // ESTADO_PARTIDO ?>
        <th class="<?= $Page->ESTADO_PARTIDO->headerCellClass() ?>"><span id="elh_partidos_ESTADO_PARTIDO" class="partidos_ESTADO_PARTIDO"><?= $Page->ESTADO_PARTIDO->caption() ?></span></th>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <th class="<?= $Page->crea_dato->headerCellClass() ?>"><span id="elh_partidos_crea_dato" class="partidos_crea_dato"><?= $Page->crea_dato->caption() ?></span></th>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <th class="<?= $Page->modifica_dato->headerCellClass() ?>"><span id="elh_partidos_modifica_dato" class="partidos_modifica_dato"><?= $Page->modifica_dato->caption() ?></span></th>
<?php } ?>
<?php if ($Page->usuario_dato->Visible) { // usuario_dato ?>
        <th class="<?= $Page->usuario_dato->headerCellClass() ?>"><span id="elh_partidos_usuario_dato" class="partidos_usuario_dato"><?= $Page->usuario_dato->caption() ?></span></th>
<?php } ?>
<?php if ($Page->automatico->Visible) { // automatico ?>
        <th class="<?= $Page->automatico->headerCellClass() ?>"><span id="elh_partidos_automatico" class="partidos_automatico"><?= $Page->automatico->caption() ?></span></th>
<?php } ?>
<?php if ($Page->actualizado->Visible) { // actualizado ?>
        <th class="<?= $Page->actualizado->headerCellClass() ?>"><span id="elh_partidos_actualizado" class="partidos_actualizado"><?= $Page->actualizado->caption() ?></span></th>
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
<?php if ($Page->ID_TORNEO->Visible) { // ID_TORNEO ?>
        <td<?= $Page->ID_TORNEO->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_ID_TORNEO" class="el_partidos_ID_TORNEO">
<span<?= $Page->ID_TORNEO->viewAttributes() ?>>
<?= $Page->ID_TORNEO->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->equipo_local->Visible) { // equipo_local ?>
        <td<?= $Page->equipo_local->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_equipo_local" class="el_partidos_equipo_local">
<span<?= $Page->equipo_local->viewAttributes() ?>>
<?= $Page->equipo_local->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->equipo_visitante->Visible) { // equipo_visitante ?>
        <td<?= $Page->equipo_visitante->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_equipo_visitante" class="el_partidos_equipo_visitante">
<span<?= $Page->equipo_visitante->viewAttributes() ?>>
<?= $Page->equipo_visitante->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->ID_PARTIDO->Visible) { // ID_PARTIDO ?>
        <td<?= $Page->ID_PARTIDO->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_ID_PARTIDO" class="el_partidos_ID_PARTIDO">
<span<?= $Page->ID_PARTIDO->viewAttributes() ?>>
<?= $Page->ID_PARTIDO->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->FECHA_PARTIDO->Visible) { // FECHA_PARTIDO ?>
        <td<?= $Page->FECHA_PARTIDO->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_FECHA_PARTIDO" class="el_partidos_FECHA_PARTIDO">
<span<?= $Page->FECHA_PARTIDO->viewAttributes() ?>>
<?= $Page->FECHA_PARTIDO->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->HORA_PARTIDO->Visible) { // HORA_PARTIDO ?>
        <td<?= $Page->HORA_PARTIDO->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_HORA_PARTIDO" class="el_partidos_HORA_PARTIDO">
<span<?= $Page->HORA_PARTIDO->viewAttributes() ?>>
<?= $Page->HORA_PARTIDO->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->ESTADIO->Visible) { // ESTADIO ?>
        <td<?= $Page->ESTADIO->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_ESTADIO" class="el_partidos_ESTADIO">
<span<?= $Page->ESTADIO->viewAttributes() ?>>
<?= $Page->ESTADIO->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->CIUDAD_PARTIDO->Visible) { // CIUDAD_PARTIDO ?>
        <td<?= $Page->CIUDAD_PARTIDO->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_CIUDAD_PARTIDO" class="el_partidos_CIUDAD_PARTIDO">
<span<?= $Page->CIUDAD_PARTIDO->viewAttributes() ?>>
<?= $Page->CIUDAD_PARTIDO->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->PAIS_PARTIDO->Visible) { // PAIS_PARTIDO ?>
        <td<?= $Page->PAIS_PARTIDO->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_PAIS_PARTIDO" class="el_partidos_PAIS_PARTIDO">
<span<?= $Page->PAIS_PARTIDO->viewAttributes() ?>>
<?= $Page->PAIS_PARTIDO->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->GOLES_LOCAL->Visible) { // GOLES_LOCAL ?>
        <td<?= $Page->GOLES_LOCAL->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_GOLES_LOCAL" class="el_partidos_GOLES_LOCAL">
<span<?= $Page->GOLES_LOCAL->viewAttributes() ?>>
<?= $Page->GOLES_LOCAL->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->GOLES_VISITANTE->Visible) { // GOLES_VISITANTE ?>
        <td<?= $Page->GOLES_VISITANTE->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_GOLES_VISITANTE" class="el_partidos_GOLES_VISITANTE">
<span<?= $Page->GOLES_VISITANTE->viewAttributes() ?>>
<?= $Page->GOLES_VISITANTE->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->GOLES_EXTRA_EQUIPO1->Visible) { // GOLES_EXTRA_EQUIPO1 ?>
        <td<?= $Page->GOLES_EXTRA_EQUIPO1->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_GOLES_EXTRA_EQUIPO1" class="el_partidos_GOLES_EXTRA_EQUIPO1">
<span<?= $Page->GOLES_EXTRA_EQUIPO1->viewAttributes() ?>>
<?= $Page->GOLES_EXTRA_EQUIPO1->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->GOLES_EXTRA_EQUIPO2->Visible) { // GOLES_EXTRA_EQUIPO2 ?>
        <td<?= $Page->GOLES_EXTRA_EQUIPO2->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_GOLES_EXTRA_EQUIPO2" class="el_partidos_GOLES_EXTRA_EQUIPO2">
<span<?= $Page->GOLES_EXTRA_EQUIPO2->viewAttributes() ?>>
<?= $Page->GOLES_EXTRA_EQUIPO2->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->NOTA_PARTIDO->Visible) { // NOTA_PARTIDO ?>
        <td<?= $Page->NOTA_PARTIDO->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_NOTA_PARTIDO" class="el_partidos_NOTA_PARTIDO">
<span<?= $Page->NOTA_PARTIDO->viewAttributes() ?>>
<?= $Page->NOTA_PARTIDO->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->RESUMEN_PARTIDO->Visible) { // RESUMEN_PARTIDO ?>
        <td<?= $Page->RESUMEN_PARTIDO->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_RESUMEN_PARTIDO" class="el_partidos_RESUMEN_PARTIDO">
<span<?= $Page->RESUMEN_PARTIDO->viewAttributes() ?>>
<?= $Page->RESUMEN_PARTIDO->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->ESTADO_PARTIDO->Visible) { // ESTADO_PARTIDO ?>
        <td<?= $Page->ESTADO_PARTIDO->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_ESTADO_PARTIDO" class="el_partidos_ESTADO_PARTIDO">
<span<?= $Page->ESTADO_PARTIDO->viewAttributes() ?>>
<?= $Page->ESTADO_PARTIDO->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <td<?= $Page->crea_dato->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_crea_dato" class="el_partidos_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<?= $Page->crea_dato->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <td<?= $Page->modifica_dato->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_modifica_dato" class="el_partidos_modifica_dato">
<span<?= $Page->modifica_dato->viewAttributes() ?>>
<?= $Page->modifica_dato->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->usuario_dato->Visible) { // usuario_dato ?>
        <td<?= $Page->usuario_dato->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_usuario_dato" class="el_partidos_usuario_dato">
<span<?= $Page->usuario_dato->viewAttributes() ?>>
<?= $Page->usuario_dato->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->automatico->Visible) { // automatico ?>
        <td<?= $Page->automatico->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_automatico" class="el_partidos_automatico">
<span<?= $Page->automatico->viewAttributes() ?>>
<div class="form-check d-inline-block">
    <input type="checkbox" id="x_automatico_<?= $Page->RowCount ?>" class="form-check-input" value="<?= $Page->automatico->getViewValue() ?>" disabled<?php if (ConvertToBool($Page->automatico->CurrentValue)) { ?> checked<?php } ?>>
    <label class="form-check-label" for="x_automatico_<?= $Page->RowCount ?>"></label>
</div></span>
</span>
</td>
<?php } ?>
<?php if ($Page->actualizado->Visible) { // actualizado ?>
        <td<?= $Page->actualizado->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_partidos_actualizado" class="el_partidos_actualizado">
<span<?= $Page->actualizado->viewAttributes() ?>>
<?= $Page->actualizado->getViewValue() ?></span>
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
