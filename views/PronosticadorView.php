<?php

namespace PHPMaker2022\project11;

// Page object
$PronosticadorView = &$Page;
?>
<?php if (!$Page->isExport()) { ?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { pronosticador: currentTable } });
var currentForm, currentPageID;
var fpronosticadorview;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fpronosticadorview = new ew.Form("fpronosticadorview", "view");
    currentPageID = ew.PAGE_ID = "view";
    currentForm = fpronosticadorview;
    loadjs.done("fpronosticadorview");
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
<form name="fpronosticadorview" id="fpronosticadorview" class="ew-form ew-view-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="pronosticador">
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<table class="table table-striped table-bordered table-hover table-sm ew-view-table">
<?php if ($Page->ID_ENCUESTA->Visible) { // ID_ENCUESTA ?>
    <tr id="r_ID_ENCUESTA"<?= $Page->ID_ENCUESTA->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_pronosticador_ID_ENCUESTA"><?= $Page->ID_ENCUESTA->caption() ?></span></td>
        <td data-name="ID_ENCUESTA"<?= $Page->ID_ENCUESTA->cellAttributes() ?>>
<span id="el_pronosticador_ID_ENCUESTA">
<span<?= $Page->ID_ENCUESTA->viewAttributes() ?>>
<?= $Page->ID_ENCUESTA->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->ID_PARTICIPANTE->Visible) { // ID_PARTICIPANTE ?>
    <tr id="r_ID_PARTICIPANTE"<?= $Page->ID_PARTICIPANTE->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_pronosticador_ID_PARTICIPANTE"><?= $Page->ID_PARTICIPANTE->caption() ?></span></td>
        <td data-name="ID_PARTICIPANTE"<?= $Page->ID_PARTICIPANTE->cellAttributes() ?>>
<span id="el_pronosticador_ID_PARTICIPANTE">
<span<?= $Page->ID_PARTICIPANTE->viewAttributes() ?>>
<?= $Page->ID_PARTICIPANTE->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->GRUPO->Visible) { // GRUPO ?>
    <tr id="r_GRUPO"<?= $Page->GRUPO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_pronosticador_GRUPO"><?= $Page->GRUPO->caption() ?></span></td>
        <td data-name="GRUPO"<?= $Page->GRUPO->cellAttributes() ?>>
<span id="el_pronosticador_GRUPO">
<span<?= $Page->GRUPO->viewAttributes() ?>>
<?= $Page->GRUPO->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->EQUIPO->Visible) { // EQUIPO ?>
    <tr id="r_EQUIPO"<?= $Page->EQUIPO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_pronosticador_EQUIPO"><?= $Page->EQUIPO->caption() ?></span></td>
        <td data-name="EQUIPO"<?= $Page->EQUIPO->cellAttributes() ?>>
<span id="el_pronosticador_EQUIPO">
<span<?= $Page->EQUIPO->viewAttributes() ?>>
<?= $Page->EQUIPO->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->POSICION->Visible) { // POSICION ?>
    <tr id="r_POSICION"<?= $Page->POSICION->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_pronosticador_POSICION"><?= $Page->POSICION->caption() ?></span></td>
        <td data-name="POSICION"<?= $Page->POSICION->cellAttributes() ?>>
<span id="el_pronosticador_POSICION">
<span<?= $Page->POSICION->viewAttributes() ?>>
<?= $Page->POSICION->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->NUMERACION->Visible) { // NUMERACION ?>
    <tr id="r_NUMERACION"<?= $Page->NUMERACION->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_pronosticador_NUMERACION"><?= $Page->NUMERACION->caption() ?></span></td>
        <td data-name="NUMERACION"<?= $Page->NUMERACION->cellAttributes() ?>>
<span id="el_pronosticador_NUMERACION">
<span<?= $Page->NUMERACION->viewAttributes() ?>>
<?= $Page->NUMERACION->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
    <tr id="r_crea_dato"<?= $Page->crea_dato->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_pronosticador_crea_dato"><?= $Page->crea_dato->caption() ?></span></td>
        <td data-name="crea_dato"<?= $Page->crea_dato->cellAttributes() ?>>
<span id="el_pronosticador_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<?= $Page->crea_dato->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
    <tr id="r_modifica_dato"<?= $Page->modifica_dato->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_pronosticador_modifica_dato"><?= $Page->modifica_dato->caption() ?></span></td>
        <td data-name="modifica_dato"<?= $Page->modifica_dato->cellAttributes() ?>>
<span id="el_pronosticador_modifica_dato">
<span<?= $Page->modifica_dato->viewAttributes() ?>>
<?= $Page->modifica_dato->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->usuario_dato->Visible) { // usuario_dato ?>
    <tr id="r_usuario_dato"<?= $Page->usuario_dato->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_pronosticador_usuario_dato"><?= $Page->usuario_dato->caption() ?></span></td>
        <td data-name="usuario_dato"<?= $Page->usuario_dato->cellAttributes() ?>>
<span id="el_pronosticador_usuario_dato">
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
