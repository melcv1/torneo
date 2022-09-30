<?php

namespace PHPMaker2022\project11;

// Page object
$PartidosEdit = &$Page;
?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { partidos: currentTable } });
var currentForm, currentPageID;
var fpartidosedit;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fpartidosedit = new ew.Form("fpartidosedit", "edit");
    currentPageID = ew.PAGE_ID = "edit";
    currentForm = fpartidosedit;

    // Add fields
    var fields = currentTable.fields;
    fpartidosedit.addFields([
        ["ID_TORNEO", [fields.ID_TORNEO.visible && fields.ID_TORNEO.required ? ew.Validators.required(fields.ID_TORNEO.caption) : null], fields.ID_TORNEO.isInvalid],
        ["equipo_local", [fields.equipo_local.visible && fields.equipo_local.required ? ew.Validators.required(fields.equipo_local.caption) : null], fields.equipo_local.isInvalid],
        ["equipo_visitante", [fields.equipo_visitante.visible && fields.equipo_visitante.required ? ew.Validators.required(fields.equipo_visitante.caption) : null], fields.equipo_visitante.isInvalid],
        ["ID_PARTIDO", [fields.ID_PARTIDO.visible && fields.ID_PARTIDO.required ? ew.Validators.required(fields.ID_PARTIDO.caption) : null], fields.ID_PARTIDO.isInvalid],
        ["FECHA_PARTIDO", [fields.FECHA_PARTIDO.visible && fields.FECHA_PARTIDO.required ? ew.Validators.required(fields.FECHA_PARTIDO.caption) : null, ew.Validators.datetime(fields.FECHA_PARTIDO.clientFormatPattern)], fields.FECHA_PARTIDO.isInvalid],
        ["HORA_PARTIDO", [fields.HORA_PARTIDO.visible && fields.HORA_PARTIDO.required ? ew.Validators.required(fields.HORA_PARTIDO.caption) : null, ew.Validators.time(fields.HORA_PARTIDO.clientFormatPattern)], fields.HORA_PARTIDO.isInvalid],
        ["ESTADIO", [fields.ESTADIO.visible && fields.ESTADIO.required ? ew.Validators.required(fields.ESTADIO.caption) : null], fields.ESTADIO.isInvalid],
        ["CIUDAD_PARTIDO", [fields.CIUDAD_PARTIDO.visible && fields.CIUDAD_PARTIDO.required ? ew.Validators.required(fields.CIUDAD_PARTIDO.caption) : null], fields.CIUDAD_PARTIDO.isInvalid],
        ["PAIS_PARTIDO", [fields.PAIS_PARTIDO.visible && fields.PAIS_PARTIDO.required ? ew.Validators.required(fields.PAIS_PARTIDO.caption) : null], fields.PAIS_PARTIDO.isInvalid],
        ["GOLES_LOCAL", [fields.GOLES_LOCAL.visible && fields.GOLES_LOCAL.required ? ew.Validators.required(fields.GOLES_LOCAL.caption) : null, ew.Validators.integer], fields.GOLES_LOCAL.isInvalid],
        ["GOLES_VISITANTE", [fields.GOLES_VISITANTE.visible && fields.GOLES_VISITANTE.required ? ew.Validators.required(fields.GOLES_VISITANTE.caption) : null, ew.Validators.integer], fields.GOLES_VISITANTE.isInvalid],
        ["GOLES_EXTRA_EQUIPO1", [fields.GOLES_EXTRA_EQUIPO1.visible && fields.GOLES_EXTRA_EQUIPO1.required ? ew.Validators.required(fields.GOLES_EXTRA_EQUIPO1.caption) : null, ew.Validators.integer], fields.GOLES_EXTRA_EQUIPO1.isInvalid],
        ["GOLES_EXTRA_EQUIPO2", [fields.GOLES_EXTRA_EQUIPO2.visible && fields.GOLES_EXTRA_EQUIPO2.required ? ew.Validators.required(fields.GOLES_EXTRA_EQUIPO2.caption) : null, ew.Validators.integer], fields.GOLES_EXTRA_EQUIPO2.isInvalid],
        ["NOTA_PARTIDO", [fields.NOTA_PARTIDO.visible && fields.NOTA_PARTIDO.required ? ew.Validators.required(fields.NOTA_PARTIDO.caption) : null], fields.NOTA_PARTIDO.isInvalid],
        ["RESUMEN_PARTIDO", [fields.RESUMEN_PARTIDO.visible && fields.RESUMEN_PARTIDO.required ? ew.Validators.required(fields.RESUMEN_PARTIDO.caption) : null], fields.RESUMEN_PARTIDO.isInvalid],
        ["ESTADO_PARTIDO", [fields.ESTADO_PARTIDO.visible && fields.ESTADO_PARTIDO.required ? ew.Validators.required(fields.ESTADO_PARTIDO.caption) : null], fields.ESTADO_PARTIDO.isInvalid],
        ["crea_dato", [fields.crea_dato.visible && fields.crea_dato.required ? ew.Validators.required(fields.crea_dato.caption) : null], fields.crea_dato.isInvalid],
        ["modifica_dato", [fields.modifica_dato.visible && fields.modifica_dato.required ? ew.Validators.required(fields.modifica_dato.caption) : null], fields.modifica_dato.isInvalid],
        ["usuario_dato", [fields.usuario_dato.visible && fields.usuario_dato.required ? ew.Validators.required(fields.usuario_dato.caption) : null], fields.usuario_dato.isInvalid],
        ["automatico", [fields.automatico.visible && fields.automatico.required ? ew.Validators.required(fields.automatico.caption) : null], fields.automatico.isInvalid],
        ["actualizado", [fields.actualizado.visible && fields.actualizado.required ? ew.Validators.required(fields.actualizado.caption) : null], fields.actualizado.isInvalid]
    ]);

    // Form_CustomValidate
    fpartidosedit.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fpartidosedit.validateRequired = ew.CLIENT_VALIDATE;

    // Dynamic selection lists
    fpartidosedit.lists.ID_TORNEO = <?= $Page->ID_TORNEO->toClientList($Page) ?>;
    fpartidosedit.lists.equipo_local = <?= $Page->equipo_local->toClientList($Page) ?>;
    fpartidosedit.lists.equipo_visitante = <?= $Page->equipo_visitante->toClientList($Page) ?>;
    fpartidosedit.lists.ESTADIO = <?= $Page->ESTADIO->toClientList($Page) ?>;
    fpartidosedit.lists.PAIS_PARTIDO = <?= $Page->PAIS_PARTIDO->toClientList($Page) ?>;
    fpartidosedit.lists.ESTADO_PARTIDO = <?= $Page->ESTADO_PARTIDO->toClientList($Page) ?>;
    fpartidosedit.lists.automatico = <?= $Page->automatico->toClientList($Page) ?>;
    loadjs.done("fpartidosedit");
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
<form name="fpartidosedit" id="fpartidosedit" class="<?= $Page->FormClassName ?>" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="partidos">
<input type="hidden" name="action" id="action" value="update">
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<input type="hidden" name="<?= $Page->OldKeyName ?>" value="<?= $Page->OldKey ?>">
<div class="ew-edit-div"><!-- page* -->
<?php if ($Page->ID_TORNEO->Visible) { // ID_TORNEO ?>
    <div id="r_ID_TORNEO"<?= $Page->ID_TORNEO->rowAttributes() ?>>
        <label id="elh_partidos_ID_TORNEO" for="x_ID_TORNEO" class="<?= $Page->LeftColumnClass ?>"><?= $Page->ID_TORNEO->caption() ?><?= $Page->ID_TORNEO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->ID_TORNEO->cellAttributes() ?>>
<span id="el_partidos_ID_TORNEO">
<?php $Page->ID_TORNEO->EditAttrs->prepend("onchange", "ew.updateOptions.call(this);"); ?>
    <select
        id="x_ID_TORNEO"
        name="x_ID_TORNEO"
        class="form-select ew-select<?= $Page->ID_TORNEO->isInvalidClass() ?>"
        data-select2-id="fpartidosedit_x_ID_TORNEO"
        data-table="partidos"
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
loadjs.ready("fpartidosedit", function() {
    var options = { name: "x_ID_TORNEO", selectId: "fpartidosedit_x_ID_TORNEO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpartidosedit.lists.ID_TORNEO.lookupOptions.length) {
        options.data = { id: "x_ID_TORNEO", form: "fpartidosedit" };
    } else {
        options.ajax = { id: "x_ID_TORNEO", form: "fpartidosedit", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.partidos.fields.ID_TORNEO.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->equipo_local->Visible) { // equipo_local ?>
    <div id="r_equipo_local"<?= $Page->equipo_local->rowAttributes() ?>>
        <label id="elh_partidos_equipo_local" for="x_equipo_local" class="<?= $Page->LeftColumnClass ?>"><?= $Page->equipo_local->caption() ?><?= $Page->equipo_local->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->equipo_local->cellAttributes() ?>>
<span id="el_partidos_equipo_local">
<?php $Page->equipo_local->EditAttrs->prepend("onchange", "ew.updateOptions.call(this);"); ?>
<div class="input-group flex-nowrap">
    <select
        id="x_equipo_local"
        name="x_equipo_local"
        class="form-select ew-select<?= $Page->equipo_local->isInvalidClass() ?>"
        data-select2-id="fpartidosedit_x_equipo_local"
        data-table="partidos"
        data-field="x_equipo_local"
        data-value-separator="<?= $Page->equipo_local->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->equipo_local->getPlaceHolder()) ?>"
        <?= $Page->equipo_local->editAttributes() ?>>
        <?= $Page->equipo_local->selectOptionListHtml("x_equipo_local") ?>
    </select>
    <button type="button" class="btn btn-default ew-add-opt-btn" id="aol_x_equipo_local" title="<?= HtmlTitle($Language->phrase("AddLink")) . "&nbsp;" . $Page->equipo_local->caption() ?>" data-title="<?= $Page->equipo_local->caption() ?>" data-ew-action="add-option" data-el="x_equipo_local" data-url="<?= GetUrl("equipotorneoaddopt") ?>"><i class="fas fa-plus ew-icon"></i></button>
</div>
<?= $Page->equipo_local->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->equipo_local->getErrorMessage() ?></div>
<?= $Page->equipo_local->Lookup->getParamTag($Page, "p_x_equipo_local") ?>
<script>
loadjs.ready("fpartidosedit", function() {
    var options = { name: "x_equipo_local", selectId: "fpartidosedit_x_equipo_local" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpartidosedit.lists.equipo_local.lookupOptions.length) {
        options.data = { id: "x_equipo_local", form: "fpartidosedit" };
    } else {
        options.ajax = { id: "x_equipo_local", form: "fpartidosedit", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.partidos.fields.equipo_local.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->equipo_visitante->Visible) { // equipo_visitante ?>
    <div id="r_equipo_visitante"<?= $Page->equipo_visitante->rowAttributes() ?>>
        <label id="elh_partidos_equipo_visitante" for="x_equipo_visitante" class="<?= $Page->LeftColumnClass ?>"><?= $Page->equipo_visitante->caption() ?><?= $Page->equipo_visitante->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->equipo_visitante->cellAttributes() ?>>
<span id="el_partidos_equipo_visitante">
<div class="input-group flex-nowrap">
    <select
        id="x_equipo_visitante"
        name="x_equipo_visitante"
        class="form-select ew-select<?= $Page->equipo_visitante->isInvalidClass() ?>"
        data-select2-id="fpartidosedit_x_equipo_visitante"
        data-table="partidos"
        data-field="x_equipo_visitante"
        data-value-separator="<?= $Page->equipo_visitante->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->equipo_visitante->getPlaceHolder()) ?>"
        <?= $Page->equipo_visitante->editAttributes() ?>>
        <?= $Page->equipo_visitante->selectOptionListHtml("x_equipo_visitante") ?>
    </select>
    <button type="button" class="btn btn-default ew-add-opt-btn" id="aol_x_equipo_visitante" title="<?= HtmlTitle($Language->phrase("AddLink")) . "&nbsp;" . $Page->equipo_visitante->caption() ?>" data-title="<?= $Page->equipo_visitante->caption() ?>" data-ew-action="add-option" data-el="x_equipo_visitante" data-url="<?= GetUrl("equipotorneoaddopt") ?>"><i class="fas fa-plus ew-icon"></i></button>
</div>
<?= $Page->equipo_visitante->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->equipo_visitante->getErrorMessage() ?></div>
<?= $Page->equipo_visitante->Lookup->getParamTag($Page, "p_x_equipo_visitante") ?>
<script>
loadjs.ready("fpartidosedit", function() {
    var options = { name: "x_equipo_visitante", selectId: "fpartidosedit_x_equipo_visitante" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpartidosedit.lists.equipo_visitante.lookupOptions.length) {
        options.data = { id: "x_equipo_visitante", form: "fpartidosedit" };
    } else {
        options.ajax = { id: "x_equipo_visitante", form: "fpartidosedit", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.partidos.fields.equipo_visitante.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->ID_PARTIDO->Visible) { // ID_PARTIDO ?>
    <div id="r_ID_PARTIDO"<?= $Page->ID_PARTIDO->rowAttributes() ?>>
        <label id="elh_partidos_ID_PARTIDO" class="<?= $Page->LeftColumnClass ?>"><?= $Page->ID_PARTIDO->caption() ?><?= $Page->ID_PARTIDO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->ID_PARTIDO->cellAttributes() ?>>
<span id="el_partidos_ID_PARTIDO">
<span<?= $Page->ID_PARTIDO->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->ID_PARTIDO->getDisplayValue($Page->ID_PARTIDO->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="partidos" data-field="x_ID_PARTIDO" data-hidden="1" name="x_ID_PARTIDO" id="x_ID_PARTIDO" value="<?= HtmlEncode($Page->ID_PARTIDO->CurrentValue) ?>">
</div></div>
    </div>
<?php } ?>
<?php if ($Page->FECHA_PARTIDO->Visible) { // FECHA_PARTIDO ?>
    <div id="r_FECHA_PARTIDO"<?= $Page->FECHA_PARTIDO->rowAttributes() ?>>
        <label id="elh_partidos_FECHA_PARTIDO" for="x_FECHA_PARTIDO" class="<?= $Page->LeftColumnClass ?>"><?= $Page->FECHA_PARTIDO->caption() ?><?= $Page->FECHA_PARTIDO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->FECHA_PARTIDO->cellAttributes() ?>>
<span id="el_partidos_FECHA_PARTIDO">
<input type="<?= $Page->FECHA_PARTIDO->getInputTextType() ?>" name="x_FECHA_PARTIDO" id="x_FECHA_PARTIDO" data-table="partidos" data-field="x_FECHA_PARTIDO" value="<?= $Page->FECHA_PARTIDO->EditValue ?>" placeholder="<?= HtmlEncode($Page->FECHA_PARTIDO->getPlaceHolder()) ?>"<?= $Page->FECHA_PARTIDO->editAttributes() ?> aria-describedby="x_FECHA_PARTIDO_help">
<?= $Page->FECHA_PARTIDO->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->FECHA_PARTIDO->getErrorMessage() ?></div>
<?php if (!$Page->FECHA_PARTIDO->ReadOnly && !$Page->FECHA_PARTIDO->Disabled && !isset($Page->FECHA_PARTIDO->EditAttrs["readonly"]) && !isset($Page->FECHA_PARTIDO->EditAttrs["disabled"])) { ?>
<script>
loadjs.ready(["fpartidosedit", "datetimepicker"], function () {
    let format = "<?= DateFormat(14) ?>",
        options = {
            localization: {
                locale: ew.LANGUAGE_ID + "-u-nu-" + ew.getNumberingSystem()
            },
            display: {
                icons: {
                    previous: ew.IS_RTL ? "fas fa-chevron-right" : "fas fa-chevron-left",
                    next: ew.IS_RTL ? "fas fa-chevron-left" : "fas fa-chevron-right"
                },
                components: {
                    hours: !!format.match(/h/i),
                    minutes: !!format.match(/m/),
                    seconds: !!format.match(/s/i),
                    useTwentyfourHour: !!format.match(/H/)
                }
            },
            meta: {
                format
            }
        };
    ew.createDateTimePicker("fpartidosedit", "x_FECHA_PARTIDO", ew.deepAssign({"useCurrent":false}, options));
});
</script>
<?php } ?>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->HORA_PARTIDO->Visible) { // HORA_PARTIDO ?>
    <div id="r_HORA_PARTIDO"<?= $Page->HORA_PARTIDO->rowAttributes() ?>>
        <label id="elh_partidos_HORA_PARTIDO" for="x_HORA_PARTIDO" class="<?= $Page->LeftColumnClass ?>"><?= $Page->HORA_PARTIDO->caption() ?><?= $Page->HORA_PARTIDO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->HORA_PARTIDO->cellAttributes() ?>>
<span id="el_partidos_HORA_PARTIDO">
<input type="<?= $Page->HORA_PARTIDO->getInputTextType() ?>" name="x_HORA_PARTIDO" id="x_HORA_PARTIDO" data-table="partidos" data-field="x_HORA_PARTIDO" value="<?= $Page->HORA_PARTIDO->EditValue ?>" placeholder="<?= HtmlEncode($Page->HORA_PARTIDO->getPlaceHolder()) ?>"<?= $Page->HORA_PARTIDO->editAttributes() ?> aria-describedby="x_HORA_PARTIDO_help">
<?= $Page->HORA_PARTIDO->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->HORA_PARTIDO->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->ESTADIO->Visible) { // ESTADIO ?>
    <div id="r_ESTADIO"<?= $Page->ESTADIO->rowAttributes() ?>>
        <label id="elh_partidos_ESTADIO" for="x_ESTADIO" class="<?= $Page->LeftColumnClass ?>"><?= $Page->ESTADIO->caption() ?><?= $Page->ESTADIO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->ESTADIO->cellAttributes() ?>>
<span id="el_partidos_ESTADIO">
<div class="input-group flex-nowrap">
    <select
        id="x_ESTADIO"
        name="x_ESTADIO"
        class="form-select ew-select<?= $Page->ESTADIO->isInvalidClass() ?>"
        data-select2-id="fpartidosedit_x_ESTADIO"
        data-table="partidos"
        data-field="x_ESTADIO"
        data-value-separator="<?= $Page->ESTADIO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->ESTADIO->getPlaceHolder()) ?>"
        <?= $Page->ESTADIO->editAttributes() ?>>
        <?= $Page->ESTADIO->selectOptionListHtml("x_ESTADIO") ?>
    </select>
    <button type="button" class="btn btn-default ew-add-opt-btn" id="aol_x_ESTADIO" title="<?= HtmlTitle($Language->phrase("AddLink")) . "&nbsp;" . $Page->ESTADIO->caption() ?>" data-title="<?= $Page->ESTADIO->caption() ?>" data-ew-action="add-option" data-el="x_ESTADIO" data-url="<?= GetUrl("estadioaddopt") ?>"><i class="fas fa-plus ew-icon"></i></button>
</div>
<?= $Page->ESTADIO->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->ESTADIO->getErrorMessage() ?></div>
<?= $Page->ESTADIO->Lookup->getParamTag($Page, "p_x_ESTADIO") ?>
<script>
loadjs.ready("fpartidosedit", function() {
    var options = { name: "x_ESTADIO", selectId: "fpartidosedit_x_ESTADIO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpartidosedit.lists.ESTADIO.lookupOptions.length) {
        options.data = { id: "x_ESTADIO", form: "fpartidosedit" };
    } else {
        options.ajax = { id: "x_ESTADIO", form: "fpartidosedit", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.partidos.fields.ESTADIO.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->CIUDAD_PARTIDO->Visible) { // CIUDAD_PARTIDO ?>
    <div id="r_CIUDAD_PARTIDO"<?= $Page->CIUDAD_PARTIDO->rowAttributes() ?>>
        <label id="elh_partidos_CIUDAD_PARTIDO" for="x_CIUDAD_PARTIDO" class="<?= $Page->LeftColumnClass ?>"><?= $Page->CIUDAD_PARTIDO->caption() ?><?= $Page->CIUDAD_PARTIDO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->CIUDAD_PARTIDO->cellAttributes() ?>>
<span id="el_partidos_CIUDAD_PARTIDO">
<textarea data-table="partidos" data-field="x_CIUDAD_PARTIDO" name="x_CIUDAD_PARTIDO" id="x_CIUDAD_PARTIDO" cols="35" rows="1" placeholder="<?= HtmlEncode($Page->CIUDAD_PARTIDO->getPlaceHolder()) ?>"<?= $Page->CIUDAD_PARTIDO->editAttributes() ?> aria-describedby="x_CIUDAD_PARTIDO_help"><?= $Page->CIUDAD_PARTIDO->EditValue ?></textarea>
<?= $Page->CIUDAD_PARTIDO->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->CIUDAD_PARTIDO->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->PAIS_PARTIDO->Visible) { // PAIS_PARTIDO ?>
    <div id="r_PAIS_PARTIDO"<?= $Page->PAIS_PARTIDO->rowAttributes() ?>>
        <label id="elh_partidos_PAIS_PARTIDO" for="x_PAIS_PARTIDO" class="<?= $Page->LeftColumnClass ?>"><?= $Page->PAIS_PARTIDO->caption() ?><?= $Page->PAIS_PARTIDO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->PAIS_PARTIDO->cellAttributes() ?>>
<span id="el_partidos_PAIS_PARTIDO">
    <select
        id="x_PAIS_PARTIDO"
        name="x_PAIS_PARTIDO"
        class="form-select ew-select<?= $Page->PAIS_PARTIDO->isInvalidClass() ?>"
        data-select2-id="fpartidosedit_x_PAIS_PARTIDO"
        data-table="partidos"
        data-field="x_PAIS_PARTIDO"
        data-value-separator="<?= $Page->PAIS_PARTIDO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->PAIS_PARTIDO->getPlaceHolder()) ?>"
        <?= $Page->PAIS_PARTIDO->editAttributes() ?>>
        <?= $Page->PAIS_PARTIDO->selectOptionListHtml("x_PAIS_PARTIDO") ?>
    </select>
    <?= $Page->PAIS_PARTIDO->getCustomMessage() ?>
    <div class="invalid-feedback"><?= $Page->PAIS_PARTIDO->getErrorMessage() ?></div>
<?= $Page->PAIS_PARTIDO->Lookup->getParamTag($Page, "p_x_PAIS_PARTIDO") ?>
<script>
loadjs.ready("fpartidosedit", function() {
    var options = { name: "x_PAIS_PARTIDO", selectId: "fpartidosedit_x_PAIS_PARTIDO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpartidosedit.lists.PAIS_PARTIDO.lookupOptions.length) {
        options.data = { id: "x_PAIS_PARTIDO", form: "fpartidosedit" };
    } else {
        options.ajax = { id: "x_PAIS_PARTIDO", form: "fpartidosedit", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.partidos.fields.PAIS_PARTIDO.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->GOLES_LOCAL->Visible) { // GOLES_LOCAL ?>
    <div id="r_GOLES_LOCAL"<?= $Page->GOLES_LOCAL->rowAttributes() ?>>
        <label id="elh_partidos_GOLES_LOCAL" for="x_GOLES_LOCAL" class="<?= $Page->LeftColumnClass ?>"><?= $Page->GOLES_LOCAL->caption() ?><?= $Page->GOLES_LOCAL->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->GOLES_LOCAL->cellAttributes() ?>>
<span id="el_partidos_GOLES_LOCAL">
<input type="<?= $Page->GOLES_LOCAL->getInputTextType() ?>" name="x_GOLES_LOCAL" id="x_GOLES_LOCAL" data-table="partidos" data-field="x_GOLES_LOCAL" value="<?= $Page->GOLES_LOCAL->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->GOLES_LOCAL->getPlaceHolder()) ?>"<?= $Page->GOLES_LOCAL->editAttributes() ?> aria-describedby="x_GOLES_LOCAL_help">
<?= $Page->GOLES_LOCAL->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->GOLES_LOCAL->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->GOLES_VISITANTE->Visible) { // GOLES_VISITANTE ?>
    <div id="r_GOLES_VISITANTE"<?= $Page->GOLES_VISITANTE->rowAttributes() ?>>
        <label id="elh_partidos_GOLES_VISITANTE" for="x_GOLES_VISITANTE" class="<?= $Page->LeftColumnClass ?>"><?= $Page->GOLES_VISITANTE->caption() ?><?= $Page->GOLES_VISITANTE->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->GOLES_VISITANTE->cellAttributes() ?>>
<span id="el_partidos_GOLES_VISITANTE">
<input type="<?= $Page->GOLES_VISITANTE->getInputTextType() ?>" name="x_GOLES_VISITANTE" id="x_GOLES_VISITANTE" data-table="partidos" data-field="x_GOLES_VISITANTE" value="<?= $Page->GOLES_VISITANTE->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->GOLES_VISITANTE->getPlaceHolder()) ?>"<?= $Page->GOLES_VISITANTE->editAttributes() ?> aria-describedby="x_GOLES_VISITANTE_help">
<?= $Page->GOLES_VISITANTE->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->GOLES_VISITANTE->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->GOLES_EXTRA_EQUIPO1->Visible) { // GOLES_EXTRA_EQUIPO1 ?>
    <div id="r_GOLES_EXTRA_EQUIPO1"<?= $Page->GOLES_EXTRA_EQUIPO1->rowAttributes() ?>>
        <label id="elh_partidos_GOLES_EXTRA_EQUIPO1" for="x_GOLES_EXTRA_EQUIPO1" class="<?= $Page->LeftColumnClass ?>"><?= $Page->GOLES_EXTRA_EQUIPO1->caption() ?><?= $Page->GOLES_EXTRA_EQUIPO1->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->GOLES_EXTRA_EQUIPO1->cellAttributes() ?>>
<span id="el_partidos_GOLES_EXTRA_EQUIPO1">
<input type="<?= $Page->GOLES_EXTRA_EQUIPO1->getInputTextType() ?>" name="x_GOLES_EXTRA_EQUIPO1" id="x_GOLES_EXTRA_EQUIPO1" data-table="partidos" data-field="x_GOLES_EXTRA_EQUIPO1" value="<?= $Page->GOLES_EXTRA_EQUIPO1->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->GOLES_EXTRA_EQUIPO1->getPlaceHolder()) ?>"<?= $Page->GOLES_EXTRA_EQUIPO1->editAttributes() ?> aria-describedby="x_GOLES_EXTRA_EQUIPO1_help">
<?= $Page->GOLES_EXTRA_EQUIPO1->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->GOLES_EXTRA_EQUIPO1->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->GOLES_EXTRA_EQUIPO2->Visible) { // GOLES_EXTRA_EQUIPO2 ?>
    <div id="r_GOLES_EXTRA_EQUIPO2"<?= $Page->GOLES_EXTRA_EQUIPO2->rowAttributes() ?>>
        <label id="elh_partidos_GOLES_EXTRA_EQUIPO2" for="x_GOLES_EXTRA_EQUIPO2" class="<?= $Page->LeftColumnClass ?>"><?= $Page->GOLES_EXTRA_EQUIPO2->caption() ?><?= $Page->GOLES_EXTRA_EQUIPO2->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->GOLES_EXTRA_EQUIPO2->cellAttributes() ?>>
<span id="el_partidos_GOLES_EXTRA_EQUIPO2">
<input type="<?= $Page->GOLES_EXTRA_EQUIPO2->getInputTextType() ?>" name="x_GOLES_EXTRA_EQUIPO2" id="x_GOLES_EXTRA_EQUIPO2" data-table="partidos" data-field="x_GOLES_EXTRA_EQUIPO2" value="<?= $Page->GOLES_EXTRA_EQUIPO2->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->GOLES_EXTRA_EQUIPO2->getPlaceHolder()) ?>"<?= $Page->GOLES_EXTRA_EQUIPO2->editAttributes() ?> aria-describedby="x_GOLES_EXTRA_EQUIPO2_help">
<?= $Page->GOLES_EXTRA_EQUIPO2->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->GOLES_EXTRA_EQUIPO2->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->NOTA_PARTIDO->Visible) { // NOTA_PARTIDO ?>
    <div id="r_NOTA_PARTIDO"<?= $Page->NOTA_PARTIDO->rowAttributes() ?>>
        <label id="elh_partidos_NOTA_PARTIDO" for="x_NOTA_PARTIDO" class="<?= $Page->LeftColumnClass ?>"><?= $Page->NOTA_PARTIDO->caption() ?><?= $Page->NOTA_PARTIDO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->NOTA_PARTIDO->cellAttributes() ?>>
<span id="el_partidos_NOTA_PARTIDO">
<textarea data-table="partidos" data-field="x_NOTA_PARTIDO" name="x_NOTA_PARTIDO" id="x_NOTA_PARTIDO" cols="35" rows="1" placeholder="<?= HtmlEncode($Page->NOTA_PARTIDO->getPlaceHolder()) ?>"<?= $Page->NOTA_PARTIDO->editAttributes() ?> aria-describedby="x_NOTA_PARTIDO_help"><?= $Page->NOTA_PARTIDO->EditValue ?></textarea>
<?= $Page->NOTA_PARTIDO->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->NOTA_PARTIDO->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->RESUMEN_PARTIDO->Visible) { // RESUMEN_PARTIDO ?>
    <div id="r_RESUMEN_PARTIDO"<?= $Page->RESUMEN_PARTIDO->rowAttributes() ?>>
        <label id="elh_partidos_RESUMEN_PARTIDO" for="x_RESUMEN_PARTIDO" class="<?= $Page->LeftColumnClass ?>"><?= $Page->RESUMEN_PARTIDO->caption() ?><?= $Page->RESUMEN_PARTIDO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->RESUMEN_PARTIDO->cellAttributes() ?>>
<span id="el_partidos_RESUMEN_PARTIDO">
<textarea data-table="partidos" data-field="x_RESUMEN_PARTIDO" name="x_RESUMEN_PARTIDO" id="x_RESUMEN_PARTIDO" cols="35" rows="1" placeholder="<?= HtmlEncode($Page->RESUMEN_PARTIDO->getPlaceHolder()) ?>"<?= $Page->RESUMEN_PARTIDO->editAttributes() ?> aria-describedby="x_RESUMEN_PARTIDO_help"><?= $Page->RESUMEN_PARTIDO->EditValue ?></textarea>
<?= $Page->RESUMEN_PARTIDO->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->RESUMEN_PARTIDO->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->ESTADO_PARTIDO->Visible) { // ESTADO_PARTIDO ?>
    <div id="r_ESTADO_PARTIDO"<?= $Page->ESTADO_PARTIDO->rowAttributes() ?>>
        <label id="elh_partidos_ESTADO_PARTIDO" for="x_ESTADO_PARTIDO" class="<?= $Page->LeftColumnClass ?>"><?= $Page->ESTADO_PARTIDO->caption() ?><?= $Page->ESTADO_PARTIDO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->ESTADO_PARTIDO->cellAttributes() ?>>
<span id="el_partidos_ESTADO_PARTIDO">
    <select
        id="x_ESTADO_PARTIDO"
        name="x_ESTADO_PARTIDO"
        class="form-select ew-select<?= $Page->ESTADO_PARTIDO->isInvalidClass() ?>"
        data-select2-id="fpartidosedit_x_ESTADO_PARTIDO"
        data-table="partidos"
        data-field="x_ESTADO_PARTIDO"
        data-value-separator="<?= $Page->ESTADO_PARTIDO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->ESTADO_PARTIDO->getPlaceHolder()) ?>"
        <?= $Page->ESTADO_PARTIDO->editAttributes() ?>>
        <?= $Page->ESTADO_PARTIDO->selectOptionListHtml("x_ESTADO_PARTIDO") ?>
    </select>
    <?= $Page->ESTADO_PARTIDO->getCustomMessage() ?>
    <div class="invalid-feedback"><?= $Page->ESTADO_PARTIDO->getErrorMessage() ?></div>
<script>
loadjs.ready("fpartidosedit", function() {
    var options = { name: "x_ESTADO_PARTIDO", selectId: "fpartidosedit_x_ESTADO_PARTIDO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpartidosedit.lists.ESTADO_PARTIDO.lookupOptions.length) {
        options.data = { id: "x_ESTADO_PARTIDO", form: "fpartidosedit" };
    } else {
        options.ajax = { id: "x_ESTADO_PARTIDO", form: "fpartidosedit", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.partidos.fields.ESTADO_PARTIDO.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
    <div id="r_crea_dato"<?= $Page->crea_dato->rowAttributes() ?>>
        <label id="elh_partidos_crea_dato" for="x_crea_dato" class="<?= $Page->LeftColumnClass ?>"><?= $Page->crea_dato->caption() ?><?= $Page->crea_dato->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->crea_dato->cellAttributes() ?>>
<span id="el_partidos_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->crea_dato->getDisplayValue($Page->crea_dato->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="partidos" data-field="x_crea_dato" data-hidden="1" name="x_crea_dato" id="x_crea_dato" value="<?= HtmlEncode($Page->crea_dato->CurrentValue) ?>">
</div></div>
    </div>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
    <div id="r_modifica_dato"<?= $Page->modifica_dato->rowAttributes() ?>>
        <label id="elh_partidos_modifica_dato" for="x_modifica_dato" class="<?= $Page->LeftColumnClass ?>"><?= $Page->modifica_dato->caption() ?><?= $Page->modifica_dato->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->modifica_dato->cellAttributes() ?>>
<span id="el_partidos_modifica_dato">
<span<?= $Page->modifica_dato->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->modifica_dato->getDisplayValue($Page->modifica_dato->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="partidos" data-field="x_modifica_dato" data-hidden="1" name="x_modifica_dato" id="x_modifica_dato" value="<?= HtmlEncode($Page->modifica_dato->CurrentValue) ?>">
</div></div>
    </div>
<?php } ?>
<?php if ($Page->automatico->Visible) { // automatico ?>
    <div id="r_automatico"<?= $Page->automatico->rowAttributes() ?>>
        <label id="elh_partidos_automatico" class="<?= $Page->LeftColumnClass ?>"><?= $Page->automatico->caption() ?><?= $Page->automatico->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->automatico->cellAttributes() ?>>
<span id="el_partidos_automatico">
<div class="form-check d-inline-block">
    <input type="checkbox" class="form-check-input<?= $Page->automatico->isInvalidClass() ?>" data-table="partidos" data-field="x_automatico" name="x_automatico[]" id="x_automatico_592094" value="1"<?= ConvertToBool($Page->automatico->CurrentValue) ? " checked" : "" ?><?= $Page->automatico->editAttributes() ?> aria-describedby="x_automatico_help">
    <div class="invalid-feedback"><?= $Page->automatico->getErrorMessage() ?></div>
</div>
<?= $Page->automatico->getCustomMessage() ?>
</span>
</div></div>
    </div>
<?php } ?>
</div><!-- /page* -->
<span id="el_partidos_actualizado">
<input type="hidden" data-table="partidos" data-field="x_actualizado" data-hidden="1" name="x_actualizado" id="x_actualizado" value="<?= HtmlEncode($Page->actualizado->CurrentValue) ?>">
</span>
<?php if (!$Page->IsModal) { ?>
<div class="row"><!-- buttons .row -->
    <div class="<?= $Page->OffsetColumnClass ?>"><!-- buttons offset -->
<button class="btn btn-primary ew-btn" name="btn-action" id="btn-action" type="submit"><?= $Language->phrase("SaveBtn") ?></button>
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
    ew.addEventHandlers("partidos");
});
</script>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
