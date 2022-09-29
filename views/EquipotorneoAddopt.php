<?php

namespace PHPMaker2022\project11;

// Page object
$EquipotorneoAddopt = &$Page;
?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { equipotorneo: currentTable } });
var currentForm, currentPageID;
var fequipotorneoaddopt;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fequipotorneoaddopt = new ew.Form("fequipotorneoaddopt", "addopt");
    currentPageID = ew.PAGE_ID = "addopt";
    currentForm = fequipotorneoaddopt;

    // Add fields
    var fields = currentTable.fields;
    fequipotorneoaddopt.addFields([
        ["ID_TORNEO", [fields.ID_TORNEO.visible && fields.ID_TORNEO.required ? ew.Validators.required(fields.ID_TORNEO.caption) : null], fields.ID_TORNEO.isInvalid],
        ["ID_EQUIPO", [fields.ID_EQUIPO.visible && fields.ID_EQUIPO.required ? ew.Validators.required(fields.ID_EQUIPO.caption) : null], fields.ID_EQUIPO.isInvalid],
        ["PARTIDOS_JUGADOS", [fields.PARTIDOS_JUGADOS.visible && fields.PARTIDOS_JUGADOS.required ? ew.Validators.required(fields.PARTIDOS_JUGADOS.caption) : null, ew.Validators.integer], fields.PARTIDOS_JUGADOS.isInvalid],
        ["PARTIDOS_GANADOS", [fields.PARTIDOS_GANADOS.visible && fields.PARTIDOS_GANADOS.required ? ew.Validators.required(fields.PARTIDOS_GANADOS.caption) : null, ew.Validators.integer], fields.PARTIDOS_GANADOS.isInvalid],
        ["PARTIDOS_EMPATADOS", [fields.PARTIDOS_EMPATADOS.visible && fields.PARTIDOS_EMPATADOS.required ? ew.Validators.required(fields.PARTIDOS_EMPATADOS.caption) : null, ew.Validators.integer], fields.PARTIDOS_EMPATADOS.isInvalid],
        ["PARTIDOS_PERDIDOS", [fields.PARTIDOS_PERDIDOS.visible && fields.PARTIDOS_PERDIDOS.required ? ew.Validators.required(fields.PARTIDOS_PERDIDOS.caption) : null, ew.Validators.integer], fields.PARTIDOS_PERDIDOS.isInvalid],
        ["GF", [fields.GF.visible && fields.GF.required ? ew.Validators.required(fields.GF.caption) : null, ew.Validators.integer], fields.GF.isInvalid],
        ["GC", [fields.GC.visible && fields.GC.required ? ew.Validators.required(fields.GC.caption) : null, ew.Validators.integer], fields.GC.isInvalid],
        ["GD", [fields.GD.visible && fields.GD.required ? ew.Validators.required(fields.GD.caption) : null, ew.Validators.integer], fields.GD.isInvalid],
        ["GRUPO", [fields.GRUPO.visible && fields.GRUPO.required ? ew.Validators.required(fields.GRUPO.caption) : null], fields.GRUPO.isInvalid],
        ["POSICION_EQUIPO_TORENO", [fields.POSICION_EQUIPO_TORENO.visible && fields.POSICION_EQUIPO_TORENO.required ? ew.Validators.required(fields.POSICION_EQUIPO_TORENO.caption) : null], fields.POSICION_EQUIPO_TORENO.isInvalid],
        ["crea_dato", [fields.crea_dato.visible && fields.crea_dato.required ? ew.Validators.required(fields.crea_dato.caption) : null], fields.crea_dato.isInvalid],
        ["modifica_dato", [fields.modifica_dato.visible && fields.modifica_dato.required ? ew.Validators.required(fields.modifica_dato.caption) : null], fields.modifica_dato.isInvalid],
        ["usuario_dato", [fields.usuario_dato.visible && fields.usuario_dato.required ? ew.Validators.required(fields.usuario_dato.caption) : null], fields.usuario_dato.isInvalid]
    ]);

    // Form_CustomValidate
    fequipotorneoaddopt.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fequipotorneoaddopt.validateRequired = ew.CLIENT_VALIDATE;

    // Dynamic selection lists
    fequipotorneoaddopt.lists.ID_TORNEO = <?= $Page->ID_TORNEO->toClientList($Page) ?>;
    fequipotorneoaddopt.lists.ID_EQUIPO = <?= $Page->ID_EQUIPO->toClientList($Page) ?>;
    fequipotorneoaddopt.lists.GRUPO = <?= $Page->GRUPO->toClientList($Page) ?>;
    fequipotorneoaddopt.lists.POSICION_EQUIPO_TORENO = <?= $Page->POSICION_EQUIPO_TORENO->toClientList($Page) ?>;
    loadjs.done("fequipotorneoaddopt");
});
</script>
<script>
loadjs.ready("head", function () {
    // Write your table-specific client script here, no need to add script tags.
});
</script>
<?php $Page->showPageHeader(); ?>
<form name="fequipotorneoaddopt" id="fequipotorneoaddopt" class="ew-form" action="<?= HtmlEncode(GetUrl(Config("API_URL"))) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="<?= Config("API_ACTION_NAME") ?>" id="<?= Config("API_ACTION_NAME") ?>" value="<?= Config("API_ADD_ACTION") ?>">
<input type="hidden" name="<?= Config("API_OBJECT_NAME") ?>" id="<?= Config("API_OBJECT_NAME") ?>" value="equipotorneo">
<input type="hidden" name="addopt" id="addopt" value="1">
<?php if ($Page->ID_TORNEO->Visible) { // ID_TORNEO ?>
    <div<?= $Page->ID_TORNEO->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_ID_TORNEO"><?= $Page->ID_TORNEO->caption() ?><?= $Page->ID_TORNEO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->ID_TORNEO->cellAttributes() ?>>
    <select
        id="x_ID_TORNEO"
        name="x_ID_TORNEO"
        class="form-select ew-select<?= $Page->ID_TORNEO->isInvalidClass() ?>"
        data-select2-id="fequipotorneoaddopt_x_ID_TORNEO"
        data-table="equipotorneo"
        data-field="x_ID_TORNEO"
        data-value-separator="<?= $Page->ID_TORNEO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->ID_TORNEO->getPlaceHolder()) ?>"
        <?= $Page->ID_TORNEO->editAttributes() ?>>
        <?= $Page->ID_TORNEO->selectOptionListHtml("x_ID_TORNEO") ?>
    </select>
    <div class="invalid-feedback"><?= $Page->ID_TORNEO->getErrorMessage() ?></div>
<?= $Page->ID_TORNEO->Lookup->getParamTag($Page, "p_x_ID_TORNEO") ?>
<script>
loadjs.ready("fequipotorneoaddopt", function() {
    var options = { name: "x_ID_TORNEO", selectId: "fequipotorneoaddopt_x_ID_TORNEO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fequipotorneoaddopt.lists.ID_TORNEO.lookupOptions.length) {
        options.data = { id: "x_ID_TORNEO", form: "fequipotorneoaddopt" };
    } else {
        options.ajax = { id: "x_ID_TORNEO", form: "fequipotorneoaddopt", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.equipotorneo.fields.ID_TORNEO.selectOptions);
    ew.createSelect(options);
});
</script>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->ID_EQUIPO->Visible) { // ID_EQUIPO ?>
    <div<?= $Page->ID_EQUIPO->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_ID_EQUIPO"><?= $Page->ID_EQUIPO->caption() ?><?= $Page->ID_EQUIPO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->ID_EQUIPO->cellAttributes() ?>>
<div class="input-group flex-nowrap">
    <select
        id="x_ID_EQUIPO"
        name="x_ID_EQUIPO"
        class="form-select ew-select<?= $Page->ID_EQUIPO->isInvalidClass() ?>"
        data-select2-id="fequipotorneoaddopt_x_ID_EQUIPO"
        data-table="equipotorneo"
        data-field="x_ID_EQUIPO"
        data-value-separator="<?= $Page->ID_EQUIPO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->ID_EQUIPO->getPlaceHolder()) ?>"
        <?= $Page->ID_EQUIPO->editAttributes() ?>>
        <?= $Page->ID_EQUIPO->selectOptionListHtml("x_ID_EQUIPO") ?>
    </select>
    <button type="button" class="btn btn-default ew-add-opt-btn" id="aol_x_ID_EQUIPO" title="<?= HtmlTitle($Language->phrase("AddLink")) . "&nbsp;" . $Page->ID_EQUIPO->caption() ?>" data-title="<?= $Page->ID_EQUIPO->caption() ?>" data-ew-action="add-option" data-el="x_ID_EQUIPO" data-url="<?= GetUrl("equipoaddopt") ?>"><i class="fas fa-plus ew-icon"></i></button>
</div>
<div class="invalid-feedback"><?= $Page->ID_EQUIPO->getErrorMessage() ?></div>
<?= $Page->ID_EQUIPO->Lookup->getParamTag($Page, "p_x_ID_EQUIPO") ?>
<script>
loadjs.ready("fequipotorneoaddopt", function() {
    var options = { name: "x_ID_EQUIPO", selectId: "fequipotorneoaddopt_x_ID_EQUIPO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fequipotorneoaddopt.lists.ID_EQUIPO.lookupOptions.length) {
        options.data = { id: "x_ID_EQUIPO", form: "fequipotorneoaddopt" };
    } else {
        options.ajax = { id: "x_ID_EQUIPO", form: "fequipotorneoaddopt", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.equipotorneo.fields.ID_EQUIPO.selectOptions);
    ew.createSelect(options);
});
</script>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->PARTIDOS_JUGADOS->Visible) { // PARTIDOS_JUGADOS ?>
    <div<?= $Page->PARTIDOS_JUGADOS->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_PARTIDOS_JUGADOS"><?= $Page->PARTIDOS_JUGADOS->caption() ?><?= $Page->PARTIDOS_JUGADOS->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->PARTIDOS_JUGADOS->cellAttributes() ?>>
<input type="<?= $Page->PARTIDOS_JUGADOS->getInputTextType() ?>" name="x_PARTIDOS_JUGADOS" id="x_PARTIDOS_JUGADOS" data-table="equipotorneo" data-field="x_PARTIDOS_JUGADOS" value="<?= $Page->PARTIDOS_JUGADOS->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->PARTIDOS_JUGADOS->getPlaceHolder()) ?>"<?= $Page->PARTIDOS_JUGADOS->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->PARTIDOS_JUGADOS->getErrorMessage() ?></div>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->PARTIDOS_GANADOS->Visible) { // PARTIDOS_GANADOS ?>
    <div<?= $Page->PARTIDOS_GANADOS->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_PARTIDOS_GANADOS"><?= $Page->PARTIDOS_GANADOS->caption() ?><?= $Page->PARTIDOS_GANADOS->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->PARTIDOS_GANADOS->cellAttributes() ?>>
<input type="<?= $Page->PARTIDOS_GANADOS->getInputTextType() ?>" name="x_PARTIDOS_GANADOS" id="x_PARTIDOS_GANADOS" data-table="equipotorneo" data-field="x_PARTIDOS_GANADOS" value="<?= $Page->PARTIDOS_GANADOS->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->PARTIDOS_GANADOS->getPlaceHolder()) ?>"<?= $Page->PARTIDOS_GANADOS->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->PARTIDOS_GANADOS->getErrorMessage() ?></div>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->PARTIDOS_EMPATADOS->Visible) { // PARTIDOS_EMPATADOS ?>
    <div<?= $Page->PARTIDOS_EMPATADOS->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_PARTIDOS_EMPATADOS"><?= $Page->PARTIDOS_EMPATADOS->caption() ?><?= $Page->PARTIDOS_EMPATADOS->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->PARTIDOS_EMPATADOS->cellAttributes() ?>>
<input type="<?= $Page->PARTIDOS_EMPATADOS->getInputTextType() ?>" name="x_PARTIDOS_EMPATADOS" id="x_PARTIDOS_EMPATADOS" data-table="equipotorneo" data-field="x_PARTIDOS_EMPATADOS" value="<?= $Page->PARTIDOS_EMPATADOS->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->PARTIDOS_EMPATADOS->getPlaceHolder()) ?>"<?= $Page->PARTIDOS_EMPATADOS->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->PARTIDOS_EMPATADOS->getErrorMessage() ?></div>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->PARTIDOS_PERDIDOS->Visible) { // PARTIDOS_PERDIDOS ?>
    <div<?= $Page->PARTIDOS_PERDIDOS->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_PARTIDOS_PERDIDOS"><?= $Page->PARTIDOS_PERDIDOS->caption() ?><?= $Page->PARTIDOS_PERDIDOS->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->PARTIDOS_PERDIDOS->cellAttributes() ?>>
<input type="<?= $Page->PARTIDOS_PERDIDOS->getInputTextType() ?>" name="x_PARTIDOS_PERDIDOS" id="x_PARTIDOS_PERDIDOS" data-table="equipotorneo" data-field="x_PARTIDOS_PERDIDOS" value="<?= $Page->PARTIDOS_PERDIDOS->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->PARTIDOS_PERDIDOS->getPlaceHolder()) ?>"<?= $Page->PARTIDOS_PERDIDOS->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->PARTIDOS_PERDIDOS->getErrorMessage() ?></div>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->GF->Visible) { // GF ?>
    <div<?= $Page->GF->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_GF"><?= $Page->GF->caption() ?><?= $Page->GF->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->GF->cellAttributes() ?>>
<input type="<?= $Page->GF->getInputTextType() ?>" name="x_GF" id="x_GF" data-table="equipotorneo" data-field="x_GF" value="<?= $Page->GF->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->GF->getPlaceHolder()) ?>"<?= $Page->GF->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->GF->getErrorMessage() ?></div>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->GC->Visible) { // GC ?>
    <div<?= $Page->GC->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_GC"><?= $Page->GC->caption() ?><?= $Page->GC->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->GC->cellAttributes() ?>>
<input type="<?= $Page->GC->getInputTextType() ?>" name="x_GC" id="x_GC" data-table="equipotorneo" data-field="x_GC" value="<?= $Page->GC->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->GC->getPlaceHolder()) ?>"<?= $Page->GC->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->GC->getErrorMessage() ?></div>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->GD->Visible) { // GD ?>
    <div<?= $Page->GD->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_GD"><?= $Page->GD->caption() ?><?= $Page->GD->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->GD->cellAttributes() ?>>
<input type="<?= $Page->GD->getInputTextType() ?>" name="x_GD" id="x_GD" data-table="equipotorneo" data-field="x_GD" value="<?= $Page->GD->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->GD->getPlaceHolder()) ?>"<?= $Page->GD->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->GD->getErrorMessage() ?></div>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->GRUPO->Visible) { // GRUPO ?>
    <div<?= $Page->GRUPO->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_GRUPO"><?= $Page->GRUPO->caption() ?><?= $Page->GRUPO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->GRUPO->cellAttributes() ?>>
    <select
        id="x_GRUPO"
        name="x_GRUPO"
        class="form-select ew-select<?= $Page->GRUPO->isInvalidClass() ?>"
        data-select2-id="fequipotorneoaddopt_x_GRUPO"
        data-table="equipotorneo"
        data-field="x_GRUPO"
        data-value-separator="<?= $Page->GRUPO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->GRUPO->getPlaceHolder()) ?>"
        <?= $Page->GRUPO->editAttributes() ?>>
        <?= $Page->GRUPO->selectOptionListHtml("x_GRUPO") ?>
    </select>
    <div class="invalid-feedback"><?= $Page->GRUPO->getErrorMessage() ?></div>
<script>
loadjs.ready("fequipotorneoaddopt", function() {
    var options = { name: "x_GRUPO", selectId: "fequipotorneoaddopt_x_GRUPO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fequipotorneoaddopt.lists.GRUPO.lookupOptions.length) {
        options.data = { id: "x_GRUPO", form: "fequipotorneoaddopt" };
    } else {
        options.ajax = { id: "x_GRUPO", form: "fequipotorneoaddopt", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.equipotorneo.fields.GRUPO.selectOptions);
    ew.createSelect(options);
});
</script>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->POSICION_EQUIPO_TORENO->Visible) { // POSICION_EQUIPO_TORENO ?>
    <div<?= $Page->POSICION_EQUIPO_TORENO->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_POSICION_EQUIPO_TORENO"><?= $Page->POSICION_EQUIPO_TORENO->caption() ?><?= $Page->POSICION_EQUIPO_TORENO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->POSICION_EQUIPO_TORENO->cellAttributes() ?>>
    <select
        id="x_POSICION_EQUIPO_TORENO"
        name="x_POSICION_EQUIPO_TORENO"
        class="form-select ew-select<?= $Page->POSICION_EQUIPO_TORENO->isInvalidClass() ?>"
        data-select2-id="fequipotorneoaddopt_x_POSICION_EQUIPO_TORENO"
        data-table="equipotorneo"
        data-field="x_POSICION_EQUIPO_TORENO"
        data-value-separator="<?= $Page->POSICION_EQUIPO_TORENO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->POSICION_EQUIPO_TORENO->getPlaceHolder()) ?>"
        <?= $Page->POSICION_EQUIPO_TORENO->editAttributes() ?>>
        <?= $Page->POSICION_EQUIPO_TORENO->selectOptionListHtml("x_POSICION_EQUIPO_TORENO") ?>
    </select>
    <div class="invalid-feedback"><?= $Page->POSICION_EQUIPO_TORENO->getErrorMessage() ?></div>
<script>
loadjs.ready("fequipotorneoaddopt", function() {
    var options = { name: "x_POSICION_EQUIPO_TORENO", selectId: "fequipotorneoaddopt_x_POSICION_EQUIPO_TORENO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fequipotorneoaddopt.lists.POSICION_EQUIPO_TORENO.lookupOptions.length) {
        options.data = { id: "x_POSICION_EQUIPO_TORENO", form: "fequipotorneoaddopt" };
    } else {
        options.ajax = { id: "x_POSICION_EQUIPO_TORENO", form: "fequipotorneoaddopt", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.equipotorneo.fields.POSICION_EQUIPO_TORENO.selectOptions);
    ew.createSelect(options);
});
</script>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
    <div<?= $Page->crea_dato->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label"><?= $Page->crea_dato->caption() ?><?= $Page->crea_dato->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->crea_dato->cellAttributes() ?>>
<input type="hidden" data-table="equipotorneo" data-field="x_crea_dato" data-hidden="1" name="x_crea_dato" id="x_crea_dato" value="<?= HtmlEncode($Page->crea_dato->CurrentValue) ?>">
</div></div>
    </div>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
    <div<?= $Page->modifica_dato->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label"><?= $Page->modifica_dato->caption() ?><?= $Page->modifica_dato->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->modifica_dato->cellAttributes() ?>>
<input type="hidden" data-table="equipotorneo" data-field="x_modifica_dato" data-hidden="1" name="x_modifica_dato" id="x_modifica_dato" value="<?= HtmlEncode($Page->modifica_dato->CurrentValue) ?>">
</div></div>
    </div>
<?php } ?>
<?php if ($Page->usuario_dato->Visible) { // usuario_dato ?>
    <input type="hidden" data-table="equipotorneo" data-field="x_usuario_dato" data-hidden="1" name="x_usuario_dato" id="x_usuario_dato" value="<?= HtmlEncode($Page->usuario_dato->CurrentValue) ?>">
<?php } ?>
</form>
<?php
$Page->showPageFooter();
echo GetDebugMessage();
?>
<script>
// Field event handlers
loadjs.ready("head", function() {
    ew.addEventHandlers("equipotorneo");
});
</script>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
