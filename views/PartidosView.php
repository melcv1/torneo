<?php

namespace PHPMaker2022\project11;

// Page object
$PartidosView = &$Page;
?>
<?php if (!$Page->isExport()) { ?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { partidos: currentTable } });
var currentForm, currentPageID;
var fpartidosview;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fpartidosview = new ew.Form("fpartidosview", "view");
    currentPageID = ew.PAGE_ID = "view";
    currentForm = fpartidosview;
    loadjs.done("fpartidosview");
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
<form name="fpartidosview" id="fpartidosview" class="ew-form ew-view-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="partidos">
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<table class="table table-striped table-bordered table-hover table-sm ew-view-table">
<?php if ($Page->ID_TORNEO->Visible) { // ID_TORNEO ?>
    <tr id="r_ID_TORNEO"<?= $Page->ID_TORNEO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_ID_TORNEO"><?= $Page->ID_TORNEO->caption() ?></span></td>
        <td data-name="ID_TORNEO"<?= $Page->ID_TORNEO->cellAttributes() ?>>
<span id="el_partidos_ID_TORNEO">
<span<?= $Page->ID_TORNEO->viewAttributes() ?>>
<?= $Page->ID_TORNEO->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->equipo_local->Visible) { // equipo_local ?>
    <tr id="r_equipo_local"<?= $Page->equipo_local->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_equipo_local"><?= $Page->equipo_local->caption() ?></span></td>
        <td data-name="equipo_local"<?= $Page->equipo_local->cellAttributes() ?>>
<span id="el_partidos_equipo_local">
<span<?= $Page->equipo_local->viewAttributes() ?>>
<?= $Page->equipo_local->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->equipo_visitante->Visible) { // equipo_visitante ?>
    <tr id="r_equipo_visitante"<?= $Page->equipo_visitante->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_equipo_visitante"><?= $Page->equipo_visitante->caption() ?></span></td>
        <td data-name="equipo_visitante"<?= $Page->equipo_visitante->cellAttributes() ?>>
<span id="el_partidos_equipo_visitante">
<span<?= $Page->equipo_visitante->viewAttributes() ?>>
<?= $Page->equipo_visitante->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->ID_PARTIDO->Visible) { // ID_PARTIDO ?>
    <tr id="r_ID_PARTIDO"<?= $Page->ID_PARTIDO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_ID_PARTIDO"><?= $Page->ID_PARTIDO->caption() ?></span></td>
        <td data-name="ID_PARTIDO"<?= $Page->ID_PARTIDO->cellAttributes() ?>>
<span id="el_partidos_ID_PARTIDO">
<span<?= $Page->ID_PARTIDO->viewAttributes() ?>>
<?= $Page->ID_PARTIDO->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->FECHA_PARTIDO->Visible) { // FECHA_PARTIDO ?>
    <tr id="r_FECHA_PARTIDO"<?= $Page->FECHA_PARTIDO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_FECHA_PARTIDO"><?= $Page->FECHA_PARTIDO->caption() ?></span></td>
        <td data-name="FECHA_PARTIDO"<?= $Page->FECHA_PARTIDO->cellAttributes() ?>>
<span id="el_partidos_FECHA_PARTIDO">
<span<?= $Page->FECHA_PARTIDO->viewAttributes() ?>>
<?= $Page->FECHA_PARTIDO->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->HORA_PARTIDO->Visible) { // HORA_PARTIDO ?>
    <tr id="r_HORA_PARTIDO"<?= $Page->HORA_PARTIDO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_HORA_PARTIDO"><?= $Page->HORA_PARTIDO->caption() ?></span></td>
        <td data-name="HORA_PARTIDO"<?= $Page->HORA_PARTIDO->cellAttributes() ?>>
<span id="el_partidos_HORA_PARTIDO">
<span<?= $Page->HORA_PARTIDO->viewAttributes() ?>>
<?= $Page->HORA_PARTIDO->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->ESTADIO->Visible) { // ESTADIO ?>
    <tr id="r_ESTADIO"<?= $Page->ESTADIO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_ESTADIO"><?= $Page->ESTADIO->caption() ?></span></td>
        <td data-name="ESTADIO"<?= $Page->ESTADIO->cellAttributes() ?>>
<span id="el_partidos_ESTADIO">
<span<?= $Page->ESTADIO->viewAttributes() ?>>
<?= $Page->ESTADIO->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->CIUDAD_PARTIDO->Visible) { // CIUDAD_PARTIDO ?>
    <tr id="r_CIUDAD_PARTIDO"<?= $Page->CIUDAD_PARTIDO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_CIUDAD_PARTIDO"><?= $Page->CIUDAD_PARTIDO->caption() ?></span></td>
        <td data-name="CIUDAD_PARTIDO"<?= $Page->CIUDAD_PARTIDO->cellAttributes() ?>>
<span id="el_partidos_CIUDAD_PARTIDO">
<span<?= $Page->CIUDAD_PARTIDO->viewAttributes() ?>>
<?= $Page->CIUDAD_PARTIDO->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->PAIS_PARTIDO->Visible) { // PAIS_PARTIDO ?>
    <tr id="r_PAIS_PARTIDO"<?= $Page->PAIS_PARTIDO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_PAIS_PARTIDO"><?= $Page->PAIS_PARTIDO->caption() ?></span></td>
        <td data-name="PAIS_PARTIDO"<?= $Page->PAIS_PARTIDO->cellAttributes() ?>>
<span id="el_partidos_PAIS_PARTIDO">
<span<?= $Page->PAIS_PARTIDO->viewAttributes() ?>>
<?= $Page->PAIS_PARTIDO->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->GOLES_LOCAL->Visible) { // GOLES_LOCAL ?>
    <tr id="r_GOLES_LOCAL"<?= $Page->GOLES_LOCAL->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_GOLES_LOCAL"><?= $Page->GOLES_LOCAL->caption() ?></span></td>
        <td data-name="GOLES_LOCAL"<?= $Page->GOLES_LOCAL->cellAttributes() ?>>
<span id="el_partidos_GOLES_LOCAL">
<span<?= $Page->GOLES_LOCAL->viewAttributes() ?>>
<?= $Page->GOLES_LOCAL->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->GOLES_VISITANTE->Visible) { // GOLES_VISITANTE ?>
    <tr id="r_GOLES_VISITANTE"<?= $Page->GOLES_VISITANTE->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_GOLES_VISITANTE"><?= $Page->GOLES_VISITANTE->caption() ?></span></td>
        <td data-name="GOLES_VISITANTE"<?= $Page->GOLES_VISITANTE->cellAttributes() ?>>
<span id="el_partidos_GOLES_VISITANTE">
<span<?= $Page->GOLES_VISITANTE->viewAttributes() ?>>
<?= $Page->GOLES_VISITANTE->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->GOLES_EXTRA_EQUIPO1->Visible) { // GOLES_EXTRA_EQUIPO1 ?>
    <tr id="r_GOLES_EXTRA_EQUIPO1"<?= $Page->GOLES_EXTRA_EQUIPO1->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_GOLES_EXTRA_EQUIPO1"><?= $Page->GOLES_EXTRA_EQUIPO1->caption() ?></span></td>
        <td data-name="GOLES_EXTRA_EQUIPO1"<?= $Page->GOLES_EXTRA_EQUIPO1->cellAttributes() ?>>
<span id="el_partidos_GOLES_EXTRA_EQUIPO1">
<span<?= $Page->GOLES_EXTRA_EQUIPO1->viewAttributes() ?>>
<?= $Page->GOLES_EXTRA_EQUIPO1->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->GOLES_EXTRA_EQUIPO2->Visible) { // GOLES_EXTRA_EQUIPO2 ?>
    <tr id="r_GOLES_EXTRA_EQUIPO2"<?= $Page->GOLES_EXTRA_EQUIPO2->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_GOLES_EXTRA_EQUIPO2"><?= $Page->GOLES_EXTRA_EQUIPO2->caption() ?></span></td>
        <td data-name="GOLES_EXTRA_EQUIPO2"<?= $Page->GOLES_EXTRA_EQUIPO2->cellAttributes() ?>>
<span id="el_partidos_GOLES_EXTRA_EQUIPO2">
<span<?= $Page->GOLES_EXTRA_EQUIPO2->viewAttributes() ?>>
<?= $Page->GOLES_EXTRA_EQUIPO2->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->NOTA_PARTIDO->Visible) { // NOTA_PARTIDO ?>
    <tr id="r_NOTA_PARTIDO"<?= $Page->NOTA_PARTIDO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_NOTA_PARTIDO"><?= $Page->NOTA_PARTIDO->caption() ?></span></td>
        <td data-name="NOTA_PARTIDO"<?= $Page->NOTA_PARTIDO->cellAttributes() ?>>
<span id="el_partidos_NOTA_PARTIDO">
<span<?= $Page->NOTA_PARTIDO->viewAttributes() ?>>
<?= $Page->NOTA_PARTIDO->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->RESUMEN_PARTIDO->Visible) { // RESUMEN_PARTIDO ?>
    <tr id="r_RESUMEN_PARTIDO"<?= $Page->RESUMEN_PARTIDO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_RESUMEN_PARTIDO"><?= $Page->RESUMEN_PARTIDO->caption() ?></span></td>
        <td data-name="RESUMEN_PARTIDO"<?= $Page->RESUMEN_PARTIDO->cellAttributes() ?>>
<span id="el_partidos_RESUMEN_PARTIDO">
<span<?= $Page->RESUMEN_PARTIDO->viewAttributes() ?>>
<?= $Page->RESUMEN_PARTIDO->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->ESTADO_PARTIDO->Visible) { // ESTADO_PARTIDO ?>
    <tr id="r_ESTADO_PARTIDO"<?= $Page->ESTADO_PARTIDO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_ESTADO_PARTIDO"><?= $Page->ESTADO_PARTIDO->caption() ?></span></td>
        <td data-name="ESTADO_PARTIDO"<?= $Page->ESTADO_PARTIDO->cellAttributes() ?>>
<span id="el_partidos_ESTADO_PARTIDO">
<span<?= $Page->ESTADO_PARTIDO->viewAttributes() ?>>
<?= $Page->ESTADO_PARTIDO->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
    <tr id="r_crea_dato"<?= $Page->crea_dato->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_crea_dato"><?= $Page->crea_dato->caption() ?></span></td>
        <td data-name="crea_dato"<?= $Page->crea_dato->cellAttributes() ?>>
<span id="el_partidos_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<?= $Page->crea_dato->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
    <tr id="r_modifica_dato"<?= $Page->modifica_dato->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_modifica_dato"><?= $Page->modifica_dato->caption() ?></span></td>
        <td data-name="modifica_dato"<?= $Page->modifica_dato->cellAttributes() ?>>
<span id="el_partidos_modifica_dato">
<span<?= $Page->modifica_dato->viewAttributes() ?>>
<?= $Page->modifica_dato->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->usuario_dato->Visible) { // usuario_dato ?>
    <tr id="r_usuario_dato"<?= $Page->usuario_dato->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_usuario_dato"><?= $Page->usuario_dato->caption() ?></span></td>
        <td data-name="usuario_dato"<?= $Page->usuario_dato->cellAttributes() ?>>
<span id="el_partidos_usuario_dato">
<span<?= $Page->usuario_dato->viewAttributes() ?>>
<?= $Page->usuario_dato->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->automatico->Visible) { // automatico ?>
    <tr id="r_automatico"<?= $Page->automatico->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_automatico"><?= $Page->automatico->caption() ?></span></td>
        <td data-name="automatico"<?= $Page->automatico->cellAttributes() ?>>
<span id="el_partidos_automatico">
<span<?= $Page->automatico->viewAttributes() ?>>
<div class="form-check d-inline-block">
    <input type="checkbox" id="x_automatico_<?= $Page->RowCount ?>" class="form-check-input" value="<?= $Page->automatico->getViewValue() ?>" disabled<?php if (ConvertToBool($Page->automatico->CurrentValue)) { ?> checked<?php } ?>>
    <label class="form-check-label" for="x_automatico_<?= $Page->RowCount ?>"></label>
</div></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->actualizado->Visible) { // actualizado ?>
    <tr id="r_actualizado"<?= $Page->actualizado->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_partidos_actualizado"><?= $Page->actualizado->caption() ?></span></td>
        <td data-name="actualizado"<?= $Page->actualizado->cellAttributes() ?>>
<span id="el_partidos_actualizado">
<span<?= $Page->actualizado->viewAttributes() ?>>
<?= $Page->actualizado->getViewValue() ?></span>
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
