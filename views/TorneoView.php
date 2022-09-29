<?php

namespace PHPMaker2022\project11;

// Page object
$TorneoView = &$Page;
?>
<?php if (!$Page->isExport()) { ?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { torneo: currentTable } });
var currentForm, currentPageID;
var ftorneoview;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    ftorneoview = new ew.Form("ftorneoview", "view");
    currentPageID = ew.PAGE_ID = "view";
    currentForm = ftorneoview;
    loadjs.done("ftorneoview");
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
<form name="ftorneoview" id="ftorneoview" class="ew-form ew-view-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="torneo">
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<table class="table table-striped table-bordered table-hover table-sm ew-view-table">
<?php if ($Page->ID_TORNEO->Visible) { // ID_TORNEO ?>
    <tr id="r_ID_TORNEO"<?= $Page->ID_TORNEO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_torneo_ID_TORNEO"><?= $Page->ID_TORNEO->caption() ?></span></td>
        <td data-name="ID_TORNEO"<?= $Page->ID_TORNEO->cellAttributes() ?>>
<span id="el_torneo_ID_TORNEO">
<span<?= $Page->ID_TORNEO->viewAttributes() ?>>
<?= $Page->ID_TORNEO->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->NOM_TORNEO_CORTO->Visible) { // NOM_TORNEO_CORTO ?>
    <tr id="r_NOM_TORNEO_CORTO"<?= $Page->NOM_TORNEO_CORTO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_torneo_NOM_TORNEO_CORTO"><?= $Page->NOM_TORNEO_CORTO->caption() ?></span></td>
        <td data-name="NOM_TORNEO_CORTO"<?= $Page->NOM_TORNEO_CORTO->cellAttributes() ?>>
<span id="el_torneo_NOM_TORNEO_CORTO">
<span<?= $Page->NOM_TORNEO_CORTO->viewAttributes() ?>>
<?= $Page->NOM_TORNEO_CORTO->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->NOM_TORNEO_LARGO->Visible) { // NOM_TORNEO_LARGO ?>
    <tr id="r_NOM_TORNEO_LARGO"<?= $Page->NOM_TORNEO_LARGO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_torneo_NOM_TORNEO_LARGO"><?= $Page->NOM_TORNEO_LARGO->caption() ?></span></td>
        <td data-name="NOM_TORNEO_LARGO"<?= $Page->NOM_TORNEO_LARGO->cellAttributes() ?>>
<span id="el_torneo_NOM_TORNEO_LARGO">
<span<?= $Page->NOM_TORNEO_LARGO->viewAttributes() ?>>
<?= $Page->NOM_TORNEO_LARGO->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->PAIS_TORNEO->Visible) { // PAIS_TORNEO ?>
    <tr id="r_PAIS_TORNEO"<?= $Page->PAIS_TORNEO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_torneo_PAIS_TORNEO"><?= $Page->PAIS_TORNEO->caption() ?></span></td>
        <td data-name="PAIS_TORNEO"<?= $Page->PAIS_TORNEO->cellAttributes() ?>>
<span id="el_torneo_PAIS_TORNEO">
<span<?= $Page->PAIS_TORNEO->viewAttributes() ?>>
<?= $Page->PAIS_TORNEO->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->REGION_TORNEO->Visible) { // REGION_TORNEO ?>
    <tr id="r_REGION_TORNEO"<?= $Page->REGION_TORNEO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_torneo_REGION_TORNEO"><?= $Page->REGION_TORNEO->caption() ?></span></td>
        <td data-name="REGION_TORNEO"<?= $Page->REGION_TORNEO->cellAttributes() ?>>
<span id="el_torneo_REGION_TORNEO">
<span<?= $Page->REGION_TORNEO->viewAttributes() ?>>
<?= $Page->REGION_TORNEO->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->DETALLE_TORNEO->Visible) { // DETALLE_TORNEO ?>
    <tr id="r_DETALLE_TORNEO"<?= $Page->DETALLE_TORNEO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_torneo_DETALLE_TORNEO"><?= $Page->DETALLE_TORNEO->caption() ?></span></td>
        <td data-name="DETALLE_TORNEO"<?= $Page->DETALLE_TORNEO->cellAttributes() ?>>
<span id="el_torneo_DETALLE_TORNEO">
<span<?= $Page->DETALLE_TORNEO->viewAttributes() ?>>
<?= $Page->DETALLE_TORNEO->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->LOGO_TORNEO->Visible) { // LOGO_TORNEO ?>
    <tr id="r_LOGO_TORNEO"<?= $Page->LOGO_TORNEO->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_torneo_LOGO_TORNEO"><?= $Page->LOGO_TORNEO->caption() ?></span></td>
        <td data-name="LOGO_TORNEO"<?= $Page->LOGO_TORNEO->cellAttributes() ?>>
<span id="el_torneo_LOGO_TORNEO">
<span>
<?= GetFileViewTag($Page->LOGO_TORNEO, $Page->LOGO_TORNEO->getViewValue(), false) ?>
</span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
    <tr id="r_crea_dato"<?= $Page->crea_dato->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_torneo_crea_dato"><?= $Page->crea_dato->caption() ?></span></td>
        <td data-name="crea_dato"<?= $Page->crea_dato->cellAttributes() ?>>
<span id="el_torneo_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<?= $Page->crea_dato->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
    <tr id="r_modifica_dato"<?= $Page->modifica_dato->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_torneo_modifica_dato"><?= $Page->modifica_dato->caption() ?></span></td>
        <td data-name="modifica_dato"<?= $Page->modifica_dato->cellAttributes() ?>>
<span id="el_torneo_modifica_dato">
<span<?= $Page->modifica_dato->viewAttributes() ?>>
<?= $Page->modifica_dato->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->usuario_dato->Visible) { // usuario_dato ?>
    <tr id="r_usuario_dato"<?= $Page->usuario_dato->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_torneo_usuario_dato"><?= $Page->usuario_dato->caption() ?></span></td>
        <td data-name="usuario_dato"<?= $Page->usuario_dato->cellAttributes() ?>>
<span id="el_torneo_usuario_dato">
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
