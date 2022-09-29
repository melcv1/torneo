<?php

namespace PHPMaker2022\project11;

// Page object
$JugadorequipoView = &$Page;
?>
<?php if (!$Page->isExport()) { ?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { jugadorequipo: currentTable } });
var currentForm, currentPageID;
var fjugadorequipoview;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fjugadorequipoview = new ew.Form("fjugadorequipoview", "view");
    currentPageID = ew.PAGE_ID = "view";
    currentForm = fjugadorequipoview;
    loadjs.done("fjugadorequipoview");
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
<form name="fjugadorequipoview" id="fjugadorequipoview" class="ew-form ew-view-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="jugadorequipo">
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<table class="table table-striped table-bordered table-hover table-sm ew-view-table">
<?php if ($Page->id_jugadorequipo->Visible) { // id_jugadorequipo ?>
    <tr id="r_id_jugadorequipo"<?= $Page->id_jugadorequipo->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_jugadorequipo_id_jugadorequipo"><?= $Page->id_jugadorequipo->caption() ?></span></td>
        <td data-name="id_jugadorequipo"<?= $Page->id_jugadorequipo->cellAttributes() ?>>
<span id="el_jugadorequipo_id_jugadorequipo">
<span<?= $Page->id_jugadorequipo->viewAttributes() ?>>
<?= $Page->id_jugadorequipo->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->id_equipo->Visible) { // id_equipo ?>
    <tr id="r_id_equipo"<?= $Page->id_equipo->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_jugadorequipo_id_equipo"><?= $Page->id_equipo->caption() ?></span></td>
        <td data-name="id_equipo"<?= $Page->id_equipo->cellAttributes() ?>>
<span id="el_jugadorequipo_id_equipo">
<span<?= $Page->id_equipo->viewAttributes() ?>>
<?= $Page->id_equipo->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->id_jugador->Visible) { // id_jugador ?>
    <tr id="r_id_jugador"<?= $Page->id_jugador->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_jugadorequipo_id_jugador"><?= $Page->id_jugador->caption() ?></span></td>
        <td data-name="id_jugador"<?= $Page->id_jugador->cellAttributes() ?>>
<span id="el_jugadorequipo_id_jugador">
<span<?= $Page->id_jugador->viewAttributes() ?>>
<?= $Page->id_jugador->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
    <tr id="r_crea_dato"<?= $Page->crea_dato->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_jugadorequipo_crea_dato"><?= $Page->crea_dato->caption() ?></span></td>
        <td data-name="crea_dato"<?= $Page->crea_dato->cellAttributes() ?>>
<span id="el_jugadorequipo_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<?= $Page->crea_dato->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
    <tr id="r_modifica_dato"<?= $Page->modifica_dato->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_jugadorequipo_modifica_dato"><?= $Page->modifica_dato->caption() ?></span></td>
        <td data-name="modifica_dato"<?= $Page->modifica_dato->cellAttributes() ?>>
<span id="el_jugadorequipo_modifica_dato">
<span<?= $Page->modifica_dato->viewAttributes() ?>>
<?= $Page->modifica_dato->getViewValue() ?></span>
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
