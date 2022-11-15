<?php

namespace PHPMaker2022\project11;

// Page object
$PronosticadorAdd = &$Page;
?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { pronosticador: currentTable } });
var currentForm, currentPageID;
var fpronosticadoradd;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fpronosticadoradd = new ew.Form("fpronosticadoradd", "add");
    currentPageID = ew.PAGE_ID = "add";
    currentForm = fpronosticadoradd;

    // Add fields
    var fields = currentTable.fields;
    fpronosticadoradd.addFields([
        ["ID_EQUIPOTORNEO", [fields.ID_EQUIPOTORNEO.visible && fields.ID_EQUIPOTORNEO.required ? ew.Validators.required(fields.ID_EQUIPOTORNEO.caption) : null], fields.ID_EQUIPOTORNEO.isInvalid],
        ["EQUIPO", [fields.EQUIPO.visible && fields.EQUIPO.required ? ew.Validators.required(fields.EQUIPO.caption) : null], fields.EQUIPO.isInvalid],
        ["GRUPO", [fields.GRUPO.visible && fields.GRUPO.required ? ew.Validators.required(fields.GRUPO.caption) : null], fields.GRUPO.isInvalid],
        ["POSICION", [fields.POSICION.visible && fields.POSICION.required ? ew.Validators.required(fields.POSICION.caption) : null], fields.POSICION.isInvalid],
        ["NUMERACION", [fields.NUMERACION.visible && fields.NUMERACION.required ? ew.Validators.required(fields.NUMERACION.caption) : null], fields.NUMERACION.isInvalid],
        ["ID_PARTICIPANTE", [fields.ID_PARTICIPANTE.visible && fields.ID_PARTICIPANTE.required ? ew.Validators.required(fields.ID_PARTICIPANTE.caption) : null], fields.ID_PARTICIPANTE.isInvalid]
    ]);

    // Form_CustomValidate
    fpronosticadoradd.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fpronosticadoradd.validateRequired = ew.CLIENT_VALIDATE;

    // Dynamic selection lists
    fpronosticadoradd.lists.ID_EQUIPOTORNEO = <?= $Page->ID_EQUIPOTORNEO->toClientList($Page) ?>;
    fpronosticadoradd.lists.EQUIPO = <?= $Page->EQUIPO->toClientList($Page) ?>;
    fpronosticadoradd.lists.GRUPO = <?= $Page->GRUPO->toClientList($Page) ?>;
    fpronosticadoradd.lists.POSICION = <?= $Page->POSICION->toClientList($Page) ?>;
    fpronosticadoradd.lists.NUMERACION = <?= $Page->NUMERACION->toClientList($Page) ?>;
    fpronosticadoradd.lists.ID_PARTICIPANTE = <?= $Page->ID_PARTICIPANTE->toClientList($Page) ?>;
    loadjs.done("fpronosticadoradd");
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
<form name="fpronosticadoradd" id="fpronosticadoradd" class="<?= $Page->FormClassName ?>" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="pronosticador">
<input type="hidden" name="action" id="action" value="insert">
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<input type="hidden" name="<?= $Page->OldKeyName ?>" value="<?= $Page->OldKey ?>">
<div class="ew-add-div"><!-- page* -->
<?php if ($Page->ID_EQUIPOTORNEO->Visible) { // ID_EQUIPOTORNEO ?>
    <div id="r_ID_EQUIPOTORNEO"<?= $Page->ID_EQUIPOTORNEO->rowAttributes() ?>>
        <label id="elh_pronosticador_ID_EQUIPOTORNEO" for="x_ID_EQUIPOTORNEO" class="<?= $Page->LeftColumnClass ?>"><?= $Page->ID_EQUIPOTORNEO->caption() ?><?= $Page->ID_EQUIPOTORNEO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->ID_EQUIPOTORNEO->cellAttributes() ?>>
<span id="el_pronosticador_ID_EQUIPOTORNEO">
<?php $Page->ID_EQUIPOTORNEO->EditAttrs->prepend("onchange", "ew.updateOptions.call(this);"); ?>
    <select
        id="x_ID_EQUIPOTORNEO"
        name="x_ID_EQUIPOTORNEO"
        class="form-select ew-select<?= $Page->ID_EQUIPOTORNEO->isInvalidClass() ?>"
        data-select2-id="fpronosticadoradd_x_ID_EQUIPOTORNEO"
        data-table="pronosticador"
        data-field="x_ID_EQUIPOTORNEO"
        data-value-separator="<?= $Page->ID_EQUIPOTORNEO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->ID_EQUIPOTORNEO->getPlaceHolder()) ?>"
        <?= $Page->ID_EQUIPOTORNEO->editAttributes() ?>>
        <?= $Page->ID_EQUIPOTORNEO->selectOptionListHtml("x_ID_EQUIPOTORNEO") ?>
    </select>
    <?= $Page->ID_EQUIPOTORNEO->getCustomMessage() ?>
    <div class="invalid-feedback"><?= $Page->ID_EQUIPOTORNEO->getErrorMessage() ?></div>
<?= $Page->ID_EQUIPOTORNEO->Lookup->getParamTag($Page, "p_x_ID_EQUIPOTORNEO") ?>
<script>
loadjs.ready("fpronosticadoradd", function() {
    var options = { name: "x_ID_EQUIPOTORNEO", selectId: "fpronosticadoradd_x_ID_EQUIPOTORNEO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpronosticadoradd.lists.ID_EQUIPOTORNEO.lookupOptions.length) {
        options.data = { id: "x_ID_EQUIPOTORNEO", form: "fpronosticadoradd" };
    } else {
        options.ajax = { id: "x_ID_EQUIPOTORNEO", form: "fpronosticadoradd", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.pronosticador.fields.ID_EQUIPOTORNEO.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->EQUIPO->Visible) { // EQUIPO ?>
    <div id="r_EQUIPO"<?= $Page->EQUIPO->rowAttributes() ?>>
        <label id="elh_pronosticador_EQUIPO" for="x_EQUIPO" class="<?= $Page->LeftColumnClass ?>"><?= $Page->EQUIPO->caption() ?><?= $Page->EQUIPO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->EQUIPO->cellAttributes() ?>>
<span id="el_pronosticador_EQUIPO">
    <select
        id="x_EQUIPO"
        name="x_EQUIPO"
        class="form-select ew-select<?= $Page->EQUIPO->isInvalidClass() ?>"
        data-select2-id="fpronosticadoradd_x_EQUIPO"
        data-table="pronosticador"
        data-field="x_EQUIPO"
        data-value-separator="<?= $Page->EQUIPO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->EQUIPO->getPlaceHolder()) ?>"
        <?= $Page->EQUIPO->editAttributes() ?>>
        <?= $Page->EQUIPO->selectOptionListHtml("x_EQUIPO") ?>
    </select>
    <?= $Page->EQUIPO->getCustomMessage() ?>
    <div class="invalid-feedback"><?= $Page->EQUIPO->getErrorMessage() ?></div>
<?= $Page->EQUIPO->Lookup->getParamTag($Page, "p_x_EQUIPO") ?>
<script>
loadjs.ready("fpronosticadoradd", function() {
    var options = { name: "x_EQUIPO", selectId: "fpronosticadoradd_x_EQUIPO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpronosticadoradd.lists.EQUIPO.lookupOptions.length) {
        options.data = { id: "x_EQUIPO", form: "fpronosticadoradd" };
    } else {
        options.ajax = { id: "x_EQUIPO", form: "fpronosticadoradd", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumInputLength = ew.selectMinimumInputLength;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.pronosticador.fields.EQUIPO.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->GRUPO->Visible) { // GRUPO ?>
    <div id="r_GRUPO"<?= $Page->GRUPO->rowAttributes() ?>>
        <label id="elh_pronosticador_GRUPO" for="x_GRUPO" class="<?= $Page->LeftColumnClass ?>"><?= $Page->GRUPO->caption() ?><?= $Page->GRUPO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->GRUPO->cellAttributes() ?>>
<span id="el_pronosticador_GRUPO">
    <select
        id="x_GRUPO"
        name="x_GRUPO"
        class="form-select ew-select<?= $Page->GRUPO->isInvalidClass() ?>"
        data-select2-id="fpronosticadoradd_x_GRUPO"
        data-table="pronosticador"
        data-field="x_GRUPO"
        data-value-separator="<?= $Page->GRUPO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->GRUPO->getPlaceHolder()) ?>"
        <?= $Page->GRUPO->editAttributes() ?>>
        <?= $Page->GRUPO->selectOptionListHtml("x_GRUPO") ?>
    </select>
    <?= $Page->GRUPO->getCustomMessage() ?>
    <div class="invalid-feedback"><?= $Page->GRUPO->getErrorMessage() ?></div>
<?= $Page->GRUPO->Lookup->getParamTag($Page, "p_x_GRUPO") ?>
<script>
loadjs.ready("fpronosticadoradd", function() {
    var options = { name: "x_GRUPO", selectId: "fpronosticadoradd_x_GRUPO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpronosticadoradd.lists.GRUPO.lookupOptions.length) {
        options.data = { id: "x_GRUPO", form: "fpronosticadoradd" };
    } else {
        options.ajax = { id: "x_GRUPO", form: "fpronosticadoradd", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.pronosticador.fields.GRUPO.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->POSICION->Visible) { // POSICION ?>
    <div id="r_POSICION"<?= $Page->POSICION->rowAttributes() ?>>
        <label id="elh_pronosticador_POSICION" for="x_POSICION" class="<?= $Page->LeftColumnClass ?>"><?= $Page->POSICION->caption() ?><?= $Page->POSICION->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->POSICION->cellAttributes() ?>>
<span id="el_pronosticador_POSICION">
    <select
        id="x_POSICION"
        name="x_POSICION"
        class="form-select ew-select<?= $Page->POSICION->isInvalidClass() ?>"
        data-select2-id="fpronosticadoradd_x_POSICION"
        data-table="pronosticador"
        data-field="x_POSICION"
        data-value-separator="<?= $Page->POSICION->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->POSICION->getPlaceHolder()) ?>"
        <?= $Page->POSICION->editAttributes() ?>>
        <?= $Page->POSICION->selectOptionListHtml("x_POSICION") ?>
    </select>
    <?= $Page->POSICION->getCustomMessage() ?>
    <div class="invalid-feedback"><?= $Page->POSICION->getErrorMessage() ?></div>
<script>
loadjs.ready("fpronosticadoradd", function() {
    var options = { name: "x_POSICION", selectId: "fpronosticadoradd_x_POSICION" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpronosticadoradd.lists.POSICION.lookupOptions.length) {
        options.data = { id: "x_POSICION", form: "fpronosticadoradd" };
    } else {
        options.ajax = { id: "x_POSICION", form: "fpronosticadoradd", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.pronosticador.fields.POSICION.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->NUMERACION->Visible) { // NUMERACION ?>
    <div id="r_NUMERACION"<?= $Page->NUMERACION->rowAttributes() ?>>
        <label id="elh_pronosticador_NUMERACION" for="x_NUMERACION" class="<?= $Page->LeftColumnClass ?>"><?= $Page->NUMERACION->caption() ?><?= $Page->NUMERACION->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->NUMERACION->cellAttributes() ?>>
<span id="el_pronosticador_NUMERACION">
    <select
        id="x_NUMERACION"
        name="x_NUMERACION"
        class="form-select ew-select<?= $Page->NUMERACION->isInvalidClass() ?>"
        data-select2-id="fpronosticadoradd_x_NUMERACION"
        data-table="pronosticador"
        data-field="x_NUMERACION"
        data-value-separator="<?= $Page->NUMERACION->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->NUMERACION->getPlaceHolder()) ?>"
        <?= $Page->NUMERACION->editAttributes() ?>>
        <?= $Page->NUMERACION->selectOptionListHtml("x_NUMERACION") ?>
    </select>
    <?= $Page->NUMERACION->getCustomMessage() ?>
    <div class="invalid-feedback"><?= $Page->NUMERACION->getErrorMessage() ?></div>
<script>
loadjs.ready("fpronosticadoradd", function() {
    var options = { name: "x_NUMERACION", selectId: "fpronosticadoradd_x_NUMERACION" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpronosticadoradd.lists.NUMERACION.lookupOptions.length) {
        options.data = { id: "x_NUMERACION", form: "fpronosticadoradd" };
    } else {
        options.ajax = { id: "x_NUMERACION", form: "fpronosticadoradd", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumInputLength = ew.selectMinimumInputLength;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.pronosticador.fields.NUMERACION.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->ID_PARTICIPANTE->Visible) { // ID_PARTICIPANTE ?>
    <div id="r_ID_PARTICIPANTE"<?= $Page->ID_PARTICIPANTE->rowAttributes() ?>>
        <label id="elh_pronosticador_ID_PARTICIPANTE" for="x_ID_PARTICIPANTE" class="<?= $Page->LeftColumnClass ?>"><?= $Page->ID_PARTICIPANTE->caption() ?><?= $Page->ID_PARTICIPANTE->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->ID_PARTICIPANTE->cellAttributes() ?>>
<span id="el_pronosticador_ID_PARTICIPANTE">
    <select
        id="x_ID_PARTICIPANTE"
        name="x_ID_PARTICIPANTE"
        class="form-select ew-select<?= $Page->ID_PARTICIPANTE->isInvalidClass() ?>"
        data-select2-id="fpronosticadoradd_x_ID_PARTICIPANTE"
        data-table="pronosticador"
        data-field="x_ID_PARTICIPANTE"
        data-value-separator="<?= $Page->ID_PARTICIPANTE->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->ID_PARTICIPANTE->getPlaceHolder()) ?>"
        <?= $Page->ID_PARTICIPANTE->editAttributes() ?>>
        <?= $Page->ID_PARTICIPANTE->selectOptionListHtml("x_ID_PARTICIPANTE") ?>
    </select>
    <?= $Page->ID_PARTICIPANTE->getCustomMessage() ?>
    <div class="invalid-feedback"><?= $Page->ID_PARTICIPANTE->getErrorMessage() ?></div>
<?= $Page->ID_PARTICIPANTE->Lookup->getParamTag($Page, "p_x_ID_PARTICIPANTE") ?>
<script>
loadjs.ready("fpronosticadoradd", function() {
    var options = { name: "x_ID_PARTICIPANTE", selectId: "fpronosticadoradd_x_ID_PARTICIPANTE" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpronosticadoradd.lists.ID_PARTICIPANTE.lookupOptions.length) {
        options.data = { id: "x_ID_PARTICIPANTE", form: "fpronosticadoradd" };
    } else {
        options.ajax = { id: "x_ID_PARTICIPANTE", form: "fpronosticadoradd", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.pronosticador.fields.ID_PARTICIPANTE.selectOptions);
    ew.createSelect(options);
});
</script>
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
    ew.addEventHandlers("pronosticador");
});
</script>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
