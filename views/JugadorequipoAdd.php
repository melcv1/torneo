<?php

namespace PHPMaker2022\project11;

// Page object
$JugadorequipoAdd = &$Page;
?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { jugadorequipo: currentTable } });
var currentForm, currentPageID;
var fjugadorequipoadd;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fjugadorequipoadd = new ew.Form("fjugadorequipoadd", "add");
    currentPageID = ew.PAGE_ID = "add";
    currentForm = fjugadorequipoadd;

    // Add fields
    var fields = currentTable.fields;
    fjugadorequipoadd.addFields([
        ["ID_TORNEO", [fields.ID_TORNEO.visible && fields.ID_TORNEO.required ? ew.Validators.required(fields.ID_TORNEO.caption) : null], fields.ID_TORNEO.isInvalid],
        ["id_equipo", [fields.id_equipo.visible && fields.id_equipo.required ? ew.Validators.required(fields.id_equipo.caption) : null], fields.id_equipo.isInvalid],
        ["id_jugador", [fields.id_jugador.visible && fields.id_jugador.required ? ew.Validators.required(fields.id_jugador.caption) : null], fields.id_jugador.isInvalid],
        ["GOLES", [fields.GOLES.visible && fields.GOLES.required ? ew.Validators.required(fields.GOLES.caption) : null], fields.GOLES.isInvalid]
    ]);

    // Form_CustomValidate
    fjugadorequipoadd.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fjugadorequipoadd.validateRequired = ew.CLIENT_VALIDATE;

    // Dynamic selection lists
    fjugadorequipoadd.lists.ID_TORNEO = <?= $Page->ID_TORNEO->toClientList($Page) ?>;
    fjugadorequipoadd.lists.id_equipo = <?= $Page->id_equipo->toClientList($Page) ?>;
    fjugadorequipoadd.lists.id_jugador = <?= $Page->id_jugador->toClientList($Page) ?>;
    loadjs.done("fjugadorequipoadd");
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
<form name="fjugadorequipoadd" id="fjugadorequipoadd" class="<?= $Page->FormClassName ?>" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="jugadorequipo">
<input type="hidden" name="action" id="action" value="insert">
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<input type="hidden" name="<?= $Page->OldKeyName ?>" value="<?= $Page->OldKey ?>">
<div class="ew-add-div"><!-- page* -->
<?php if ($Page->ID_TORNEO->Visible) { // ID_TORNEO ?>
    <div id="r_ID_TORNEO"<?= $Page->ID_TORNEO->rowAttributes() ?>>
        <label id="elh_jugadorequipo_ID_TORNEO" for="x_ID_TORNEO" class="<?= $Page->LeftColumnClass ?>"><?= $Page->ID_TORNEO->caption() ?><?= $Page->ID_TORNEO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->ID_TORNEO->cellAttributes() ?>>
<span id="el_jugadorequipo_ID_TORNEO">
<?php $Page->ID_TORNEO->EditAttrs->prepend("onchange", "ew.updateOptions.call(this);"); ?>
    <select
        id="x_ID_TORNEO"
        name="x_ID_TORNEO"
        class="form-select ew-select<?= $Page->ID_TORNEO->isInvalidClass() ?>"
        data-select2-id="fjugadorequipoadd_x_ID_TORNEO"
        data-table="jugadorequipo"
        data-field="x_ID_TORNEO"
        data-value-separator="<?= $Page->ID_TORNEO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->ID_TORNEO->getPlaceHolder()) ?>"
        <?= $Page->ID_TORNEO->editAttributes() ?>>
        <?= $Page->ID_TORNEO->selectOptionListHtml("x_ID_TORNEO") ?>
    </select>
    <?= $Page->ID_TORNEO->getCustomMessage() ?>
    <div class="invalid-feedback"><?= $Page->ID_TORNEO->getErrorMessage() ?></div>
<?= $Page->ID_TORNEO->Lookup->getParamTag($Page, "p_x_ID_TORNEO") ?>
<script>
loadjs.ready("fjugadorequipoadd", function() {
    var options = { name: "x_ID_TORNEO", selectId: "fjugadorequipoadd_x_ID_TORNEO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fjugadorequipoadd.lists.ID_TORNEO.lookupOptions.length) {
        options.data = { id: "x_ID_TORNEO", form: "fjugadorequipoadd" };
    } else {
        options.ajax = { id: "x_ID_TORNEO", form: "fjugadorequipoadd", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.jugadorequipo.fields.ID_TORNEO.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->id_equipo->Visible) { // id_equipo ?>
    <div id="r_id_equipo"<?= $Page->id_equipo->rowAttributes() ?>>
        <label id="elh_jugadorequipo_id_equipo" for="x_id_equipo" class="<?= $Page->LeftColumnClass ?>"><?= $Page->id_equipo->caption() ?><?= $Page->id_equipo->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->id_equipo->cellAttributes() ?>>
<span id="el_jugadorequipo_id_equipo">
<div class="input-group flex-nowrap">
    <select
        id="x_id_equipo"
        name="x_id_equipo"
        class="form-select ew-select<?= $Page->id_equipo->isInvalidClass() ?>"
        data-select2-id="fjugadorequipoadd_x_id_equipo"
        data-table="jugadorequipo"
        data-field="x_id_equipo"
        data-value-separator="<?= $Page->id_equipo->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->id_equipo->getPlaceHolder()) ?>"
        <?= $Page->id_equipo->editAttributes() ?>>
        <?= $Page->id_equipo->selectOptionListHtml("x_id_equipo") ?>
    </select>
    <button type="button" class="btn btn-default ew-add-opt-btn" id="aol_x_id_equipo" title="<?= HtmlTitle($Language->phrase("AddLink")) . "&nbsp;" . $Page->id_equipo->caption() ?>" data-title="<?= $Page->id_equipo->caption() ?>" data-ew-action="add-option" data-el="x_id_equipo" data-url="<?= GetUrl("equipotorneoaddopt") ?>"><i class="fas fa-plus ew-icon"></i></button>
</div>
<?= $Page->id_equipo->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->id_equipo->getErrorMessage() ?></div>
<?= $Page->id_equipo->Lookup->getParamTag($Page, "p_x_id_equipo") ?>
<script>
loadjs.ready("fjugadorequipoadd", function() {
    var options = { name: "x_id_equipo", selectId: "fjugadorequipoadd_x_id_equipo" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fjugadorequipoadd.lists.id_equipo.lookupOptions.length) {
        options.data = { id: "x_id_equipo", form: "fjugadorequipoadd" };
    } else {
        options.ajax = { id: "x_id_equipo", form: "fjugadorequipoadd", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.jugadorequipo.fields.id_equipo.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->id_jugador->Visible) { // id_jugador ?>
    <div id="r_id_jugador"<?= $Page->id_jugador->rowAttributes() ?>>
        <label id="elh_jugadorequipo_id_jugador" for="x_id_jugador" class="<?= $Page->LeftColumnClass ?>"><?= $Page->id_jugador->caption() ?><?= $Page->id_jugador->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->id_jugador->cellAttributes() ?>>
<span id="el_jugadorequipo_id_jugador">
<div class="input-group flex-nowrap">
    <select
        id="x_id_jugador"
        name="x_id_jugador"
        class="form-select ew-select<?= $Page->id_jugador->isInvalidClass() ?>"
        data-select2-id="fjugadorequipoadd_x_id_jugador"
        data-table="jugadorequipo"
        data-field="x_id_jugador"
        data-value-separator="<?= $Page->id_jugador->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->id_jugador->getPlaceHolder()) ?>"
        <?= $Page->id_jugador->editAttributes() ?>>
        <?= $Page->id_jugador->selectOptionListHtml("x_id_jugador") ?>
    </select>
    <button type="button" class="btn btn-default ew-add-opt-btn" id="aol_x_id_jugador" title="<?= HtmlTitle($Language->phrase("AddLink")) . "&nbsp;" . $Page->id_jugador->caption() ?>" data-title="<?= $Page->id_jugador->caption() ?>" data-ew-action="add-option" data-el="x_id_jugador" data-url="<?= GetUrl("jugadoraddopt") ?>"><i class="fas fa-plus ew-icon"></i></button>
</div>
<?= $Page->id_jugador->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->id_jugador->getErrorMessage() ?></div>
<?= $Page->id_jugador->Lookup->getParamTag($Page, "p_x_id_jugador") ?>
<script>
loadjs.ready("fjugadorequipoadd", function() {
    var options = { name: "x_id_jugador", selectId: "fjugadorequipoadd_x_id_jugador" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fjugadorequipoadd.lists.id_jugador.lookupOptions.length) {
        options.data = { id: "x_id_jugador", form: "fjugadorequipoadd" };
    } else {
        options.ajax = { id: "x_id_jugador", form: "fjugadorequipoadd", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.jugadorequipo.fields.id_jugador.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->GOLES->Visible) { // GOLES ?>
    <div id="r_GOLES"<?= $Page->GOLES->rowAttributes() ?>>
        <label id="elh_jugadorequipo_GOLES" for="x_GOLES" class="<?= $Page->LeftColumnClass ?>"><?= $Page->GOLES->caption() ?><?= $Page->GOLES->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->GOLES->cellAttributes() ?>>
<span id="el_jugadorequipo_GOLES">
<input type="<?= $Page->GOLES->getInputTextType() ?>" name="x_GOLES" id="x_GOLES" data-table="jugadorequipo" data-field="x_GOLES" value="<?= $Page->GOLES->EditValue ?>" size="30" maxlength="64" placeholder="<?= HtmlEncode($Page->GOLES->getPlaceHolder()) ?>"<?= $Page->GOLES->editAttributes() ?> aria-describedby="x_GOLES_help">
<?= $Page->GOLES->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->GOLES->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
</div><!-- /page* -->
<?php if (!$Page->IsModal) { ?>
<div class="row"><!-- buttons .row -->
    <div class="<?= $Page->OffsetColumnClass ?>"><!-- buttons offset -->
<button class="btn btn-primary ew-btn" name="btn-action" id="btn-action" type="submit"><?= $Language->phrase("AddBtn") ?></button>
<button class="btn btn-default ew-btn" name="btn-cancel" id="btn-cancel" type="button" data-href="<?= HtmlEncode(GetUrl($Page->getReturnUrl())) ?>"><?= $Language->phrase("CancelBtn") ?></button>
    </div><!-- /buttons offset -->
</div><!-- /buttons .row -->
<?php } ?>
</form>
<?php
$Page->showPageFooter();
echo GetDebugMessage();
?>
<script>
// Field event handlers
loadjs.ready("head", function() {
    ew.addEventHandlers("jugadorequipo");
});
</script>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
