<?php

namespace PHPMaker2022\project11;

// Page object
$EstadioAddopt = &$Page;
?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { estadio: currentTable } });
var currentForm, currentPageID;
var festadioaddopt;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    festadioaddopt = new ew.Form("festadioaddopt", "addopt");
    currentPageID = ew.PAGE_ID = "addopt";
    currentForm = festadioaddopt;

    // Add fields
    var fields = currentTable.fields;
    festadioaddopt.addFields([
        ["id_torneo", [fields.id_torneo.visible && fields.id_torneo.required ? ew.Validators.required(fields.id_torneo.caption) : null], fields.id_torneo.isInvalid],
        ["nombre_estadio", [fields.nombre_estadio.visible && fields.nombre_estadio.required ? ew.Validators.required(fields.nombre_estadio.caption) : null], fields.nombre_estadio.isInvalid],
        ["foto_estadio", [fields.foto_estadio.visible && fields.foto_estadio.required ? ew.Validators.fileRequired(fields.foto_estadio.caption) : null], fields.foto_estadio.isInvalid],
        ["crea_dato", [fields.crea_dato.visible && fields.crea_dato.required ? ew.Validators.required(fields.crea_dato.caption) : null], fields.crea_dato.isInvalid],
        ["modifica_dato", [fields.modifica_dato.visible && fields.modifica_dato.required ? ew.Validators.required(fields.modifica_dato.caption) : null], fields.modifica_dato.isInvalid],
        ["usuario_dato", [fields.usuario_dato.visible && fields.usuario_dato.required ? ew.Validators.required(fields.usuario_dato.caption) : null], fields.usuario_dato.isInvalid]
    ]);

    // Form_CustomValidate
    festadioaddopt.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    festadioaddopt.validateRequired = ew.CLIENT_VALIDATE;

    // Dynamic selection lists
    festadioaddopt.lists.id_torneo = <?= $Page->id_torneo->toClientList($Page) ?>;
    loadjs.done("festadioaddopt");
});
</script>
<script>
loadjs.ready("head", function () {
    // Write your table-specific client script here, no need to add script tags.
});
</script>
<?php $Page->showPageHeader(); ?>
<form name="festadioaddopt" id="festadioaddopt" class="ew-form" action="<?= HtmlEncode(GetUrl(Config("API_URL"))) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="<?= Config("API_ACTION_NAME") ?>" id="<?= Config("API_ACTION_NAME") ?>" value="<?= Config("API_ADD_ACTION") ?>">
<input type="hidden" name="<?= Config("API_OBJECT_NAME") ?>" id="<?= Config("API_OBJECT_NAME") ?>" value="estadio">
<input type="hidden" name="addopt" id="addopt" value="1">
<?php if ($Page->id_torneo->Visible) { // id_torneo ?>
    <div<?= $Page->id_torneo->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_id_torneo"><?= $Page->id_torneo->caption() ?><?= $Page->id_torneo->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->id_torneo->cellAttributes() ?>>
    <select
        id="x_id_torneo"
        name="x_id_torneo"
        class="form-select ew-select<?= $Page->id_torneo->isInvalidClass() ?>"
        data-select2-id="festadioaddopt_x_id_torneo"
        data-table="estadio"
        data-field="x_id_torneo"
        data-value-separator="<?= $Page->id_torneo->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->id_torneo->getPlaceHolder()) ?>"
        <?= $Page->id_torneo->editAttributes() ?>>
        <?= $Page->id_torneo->selectOptionListHtml("x_id_torneo") ?>
    </select>
    <div class="invalid-feedback"><?= $Page->id_torneo->getErrorMessage() ?></div>
<?= $Page->id_torneo->Lookup->getParamTag($Page, "p_x_id_torneo") ?>
<script>
loadjs.ready("festadioaddopt", function() {
    var options = { name: "x_id_torneo", selectId: "festadioaddopt_x_id_torneo" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (festadioaddopt.lists.id_torneo.lookupOptions.length) {
        options.data = { id: "x_id_torneo", form: "festadioaddopt" };
    } else {
        options.ajax = { id: "x_id_torneo", form: "festadioaddopt", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.estadio.fields.id_torneo.selectOptions);
    ew.createSelect(options);
});
</script>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->nombre_estadio->Visible) { // nombre_estadio ?>
    <div<?= $Page->nombre_estadio->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_nombre_estadio"><?= $Page->nombre_estadio->caption() ?><?= $Page->nombre_estadio->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->nombre_estadio->cellAttributes() ?>>
<textarea data-table="estadio" data-field="x_nombre_estadio" name="x_nombre_estadio" id="x_nombre_estadio" cols="35" rows="4" placeholder="<?= HtmlEncode($Page->nombre_estadio->getPlaceHolder()) ?>"<?= $Page->nombre_estadio->editAttributes() ?>><?= $Page->nombre_estadio->EditValue ?></textarea>
<div class="invalid-feedback"><?= $Page->nombre_estadio->getErrorMessage() ?></div>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->foto_estadio->Visible) { // foto_estadio ?>
    <div<?= $Page->foto_estadio->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label"><?= $Page->foto_estadio->caption() ?><?= $Page->foto_estadio->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->foto_estadio->cellAttributes() ?>>
<div id="fd_x_foto_estadio" class="fileinput-button ew-file-drop-zone">
    <input type="file" class="form-control ew-file-input" title="<?= $Page->foto_estadio->title() ?>" data-table="estadio" data-field="x_foto_estadio" name="x_foto_estadio" id="x_foto_estadio" lang="<?= CurrentLanguageID() ?>"<?= $Page->foto_estadio->editAttributes() ?><?= ($Page->foto_estadio->ReadOnly || $Page->foto_estadio->Disabled) ? " disabled" : "" ?>>
    <div class="text-muted ew-file-text"><?= $Language->phrase("ChooseFile") ?></div>
</div>
<div class="invalid-feedback"><?= $Page->foto_estadio->getErrorMessage() ?></div>
<input type="hidden" name="fn_x_foto_estadio" id= "fn_x_foto_estadio" value="<?= $Page->foto_estadio->Upload->FileName ?>">
<input type="hidden" name="fa_x_foto_estadio" id= "fa_x_foto_estadio" value="0">
<input type="hidden" name="fs_x_foto_estadio" id= "fs_x_foto_estadio" value="1024">
<input type="hidden" name="fx_x_foto_estadio" id= "fx_x_foto_estadio" value="<?= $Page->foto_estadio->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x_foto_estadio" id= "fm_x_foto_estadio" value="<?= $Page->foto_estadio->UploadMaxFileSize ?>">
<table id="ft_x_foto_estadio" class="table table-sm float-start ew-upload-table"><tbody class="files"></tbody></table>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
    <div<?= $Page->crea_dato->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label"><?= $Page->crea_dato->caption() ?><?= $Page->crea_dato->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->crea_dato->cellAttributes() ?>>
<input type="hidden" data-table="estadio" data-field="x_crea_dato" data-hidden="1" name="x_crea_dato" id="x_crea_dato" value="<?= HtmlEncode($Page->crea_dato->CurrentValue) ?>">
</div></div>
    </div>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
    <div<?= $Page->modifica_dato->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label"><?= $Page->modifica_dato->caption() ?><?= $Page->modifica_dato->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->modifica_dato->cellAttributes() ?>>
<input type="hidden" data-table="estadio" data-field="x_modifica_dato" data-hidden="1" name="x_modifica_dato" id="x_modifica_dato" value="<?= HtmlEncode($Page->modifica_dato->CurrentValue) ?>">
</div></div>
    </div>
<?php } ?>
<?php if ($Page->usuario_dato->Visible) { // usuario_dato ?>
    <input type="hidden" data-table="estadio" data-field="x_usuario_dato" data-hidden="1" name="x_usuario_dato" id="x_usuario_dato" value="<?= HtmlEncode($Page->usuario_dato->CurrentValue) ?>">
<?php } ?>
</form>
<?php
$Page->showPageFooter();
echo GetDebugMessage();
?>
<script>
// Field event handlers
loadjs.ready("head", function() {
    ew.addEventHandlers("estadio");
});
</script>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
