<?php

namespace PHPMaker2022\project11;

// Page object
$EstadioEdit = &$Page;
?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { estadio: currentTable } });
var currentForm, currentPageID;
var festadioedit;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    festadioedit = new ew.Form("festadioedit", "edit");
    currentPageID = ew.PAGE_ID = "edit";
    currentForm = festadioedit;

    // Add fields
    var fields = currentTable.fields;
    festadioedit.addFields([
        ["id_estadio", [fields.id_estadio.visible && fields.id_estadio.required ? ew.Validators.required(fields.id_estadio.caption) : null], fields.id_estadio.isInvalid],
        ["id_torneo", [fields.id_torneo.visible && fields.id_torneo.required ? ew.Validators.required(fields.id_torneo.caption) : null], fields.id_torneo.isInvalid],
        ["nombre_estadio", [fields.nombre_estadio.visible && fields.nombre_estadio.required ? ew.Validators.required(fields.nombre_estadio.caption) : null], fields.nombre_estadio.isInvalid],
        ["foto_estadio", [fields.foto_estadio.visible && fields.foto_estadio.required ? ew.Validators.fileRequired(fields.foto_estadio.caption) : null], fields.foto_estadio.isInvalid],
        ["crea_dato", [fields.crea_dato.visible && fields.crea_dato.required ? ew.Validators.required(fields.crea_dato.caption) : null], fields.crea_dato.isInvalid],
        ["modifica_dato", [fields.modifica_dato.visible && fields.modifica_dato.required ? ew.Validators.required(fields.modifica_dato.caption) : null], fields.modifica_dato.isInvalid],
        ["usuario_dato", [fields.usuario_dato.visible && fields.usuario_dato.required ? ew.Validators.required(fields.usuario_dato.caption) : null], fields.usuario_dato.isInvalid]
    ]);

    // Form_CustomValidate
    festadioedit.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    festadioedit.validateRequired = ew.CLIENT_VALIDATE;

    // Dynamic selection lists
    festadioedit.lists.id_torneo = <?= $Page->id_torneo->toClientList($Page) ?>;
    loadjs.done("festadioedit");
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
<form name="festadioedit" id="festadioedit" class="<?= $Page->FormClassName ?>" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="estadio">
<input type="hidden" name="action" id="action" value="update">
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<input type="hidden" name="<?= $Page->OldKeyName ?>" value="<?= $Page->OldKey ?>">
<div class="ew-edit-div"><!-- page* -->
<?php if ($Page->id_estadio->Visible) { // id_estadio ?>
    <div id="r_id_estadio"<?= $Page->id_estadio->rowAttributes() ?>>
        <label id="elh_estadio_id_estadio" class="<?= $Page->LeftColumnClass ?>"><?= $Page->id_estadio->caption() ?><?= $Page->id_estadio->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->id_estadio->cellAttributes() ?>>
<span id="el_estadio_id_estadio">
<span<?= $Page->id_estadio->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->id_estadio->getDisplayValue($Page->id_estadio->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="estadio" data-field="x_id_estadio" data-hidden="1" name="x_id_estadio" id="x_id_estadio" value="<?= HtmlEncode($Page->id_estadio->CurrentValue) ?>">
</div></div>
    </div>
<?php } ?>
<?php if ($Page->id_torneo->Visible) { // id_torneo ?>
    <div id="r_id_torneo"<?= $Page->id_torneo->rowAttributes() ?>>
        <label id="elh_estadio_id_torneo" for="x_id_torneo" class="<?= $Page->LeftColumnClass ?>"><?= $Page->id_torneo->caption() ?><?= $Page->id_torneo->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->id_torneo->cellAttributes() ?>>
<span id="el_estadio_id_torneo">
    <select
        id="x_id_torneo"
        name="x_id_torneo"
        class="form-select ew-select<?= $Page->id_torneo->isInvalidClass() ?>"
        data-select2-id="festadioedit_x_id_torneo"
        data-table="estadio"
        data-field="x_id_torneo"
        data-value-separator="<?= $Page->id_torneo->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->id_torneo->getPlaceHolder()) ?>"
        <?= $Page->id_torneo->editAttributes() ?>>
        <?= $Page->id_torneo->selectOptionListHtml("x_id_torneo") ?>
    </select>
    <?= $Page->id_torneo->getCustomMessage() ?>
    <div class="invalid-feedback"><?= $Page->id_torneo->getErrorMessage() ?></div>
<?= $Page->id_torneo->Lookup->getParamTag($Page, "p_x_id_torneo") ?>
<script>
loadjs.ready("festadioedit", function() {
    var options = { name: "x_id_torneo", selectId: "festadioedit_x_id_torneo" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (festadioedit.lists.id_torneo.lookupOptions.length) {
        options.data = { id: "x_id_torneo", form: "festadioedit" };
    } else {
        options.ajax = { id: "x_id_torneo", form: "festadioedit", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.estadio.fields.id_torneo.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->nombre_estadio->Visible) { // nombre_estadio ?>
    <div id="r_nombre_estadio"<?= $Page->nombre_estadio->rowAttributes() ?>>
        <label id="elh_estadio_nombre_estadio" for="x_nombre_estadio" class="<?= $Page->LeftColumnClass ?>"><?= $Page->nombre_estadio->caption() ?><?= $Page->nombre_estadio->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->nombre_estadio->cellAttributes() ?>>
<span id="el_estadio_nombre_estadio">
<textarea data-table="estadio" data-field="x_nombre_estadio" name="x_nombre_estadio" id="x_nombre_estadio" cols="35" rows="4" placeholder="<?= HtmlEncode($Page->nombre_estadio->getPlaceHolder()) ?>"<?= $Page->nombre_estadio->editAttributes() ?> aria-describedby="x_nombre_estadio_help"><?= $Page->nombre_estadio->EditValue ?></textarea>
<?= $Page->nombre_estadio->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->nombre_estadio->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->foto_estadio->Visible) { // foto_estadio ?>
    <div id="r_foto_estadio"<?= $Page->foto_estadio->rowAttributes() ?>>
        <label id="elh_estadio_foto_estadio" class="<?= $Page->LeftColumnClass ?>"><?= $Page->foto_estadio->caption() ?><?= $Page->foto_estadio->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->foto_estadio->cellAttributes() ?>>
<span id="el_estadio_foto_estadio">
<div id="fd_x_foto_estadio" class="fileinput-button ew-file-drop-zone">
    <input type="file" class="form-control ew-file-input" title="<?= $Page->foto_estadio->title() ?>" data-table="estadio" data-field="x_foto_estadio" name="x_foto_estadio" id="x_foto_estadio" lang="<?= CurrentLanguageID() ?>"<?= $Page->foto_estadio->editAttributes() ?> aria-describedby="x_foto_estadio_help"<?= ($Page->foto_estadio->ReadOnly || $Page->foto_estadio->Disabled) ? " disabled" : "" ?>>
    <div class="text-muted ew-file-text"><?= $Language->phrase("ChooseFile") ?></div>
</div>
<?= $Page->foto_estadio->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->foto_estadio->getErrorMessage() ?></div>
<input type="hidden" name="fn_x_foto_estadio" id= "fn_x_foto_estadio" value="<?= $Page->foto_estadio->Upload->FileName ?>">
<input type="hidden" name="fa_x_foto_estadio" id= "fa_x_foto_estadio" value="<?= (Post("fa_x_foto_estadio") == "0") ? "0" : "1" ?>">
<input type="hidden" name="fs_x_foto_estadio" id= "fs_x_foto_estadio" value="1024">
<input type="hidden" name="fx_x_foto_estadio" id= "fx_x_foto_estadio" value="<?= $Page->foto_estadio->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x_foto_estadio" id= "fm_x_foto_estadio" value="<?= $Page->foto_estadio->UploadMaxFileSize ?>">
<table id="ft_x_foto_estadio" class="table table-sm float-start ew-upload-table"><tbody class="files"></tbody></table>
</span>
</div></div>
    </div>
<?php } ?>
</div><!-- /page* -->
<span id="el_estadio_crea_dato">
<input type="hidden" data-table="estadio" data-field="x_crea_dato" data-hidden="1" name="x_crea_dato" id="x_crea_dato" value="<?= HtmlEncode($Page->crea_dato->CurrentValue) ?>">
</span>
<span id="el_estadio_modifica_dato">
<input type="hidden" data-table="estadio" data-field="x_modifica_dato" data-hidden="1" name="x_modifica_dato" id="x_modifica_dato" value="<?= HtmlEncode($Page->modifica_dato->CurrentValue) ?>">
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
    ew.addEventHandlers("estadio");
});
</script>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
