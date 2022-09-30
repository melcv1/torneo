<?php

namespace PHPMaker2022\project11;

// Page object
$JugadorEdit = &$Page;
?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { jugador: currentTable } });
var currentForm, currentPageID;
var fjugadoredit;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fjugadoredit = new ew.Form("fjugadoredit", "edit");
    currentPageID = ew.PAGE_ID = "edit";
    currentForm = fjugadoredit;

    // Add fields
    var fields = currentTable.fields;
    fjugadoredit.addFields([
        ["id_jugador", [fields.id_jugador.visible && fields.id_jugador.required ? ew.Validators.required(fields.id_jugador.caption) : null], fields.id_jugador.isInvalid],
        ["nombre_jugador", [fields.nombre_jugador.visible && fields.nombre_jugador.required ? ew.Validators.required(fields.nombre_jugador.caption) : null], fields.nombre_jugador.isInvalid],
        ["votos_jugador", [fields.votos_jugador.visible && fields.votos_jugador.required ? ew.Validators.required(fields.votos_jugador.caption) : null, ew.Validators.integer], fields.votos_jugador.isInvalid],
        ["imagen_jugador", [fields.imagen_jugador.visible && fields.imagen_jugador.required ? ew.Validators.fileRequired(fields.imagen_jugador.caption) : null], fields.imagen_jugador.isInvalid],
        ["posicion", [fields.posicion.visible && fields.posicion.required ? ew.Validators.required(fields.posicion.caption) : null], fields.posicion.isInvalid],
        ["nombre_equipo", [fields.nombre_equipo.visible && fields.nombre_equipo.required ? ew.Validators.required(fields.nombre_equipo.caption) : null], fields.nombre_equipo.isInvalid]
    ]);

    // Form_CustomValidate
    fjugadoredit.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fjugadoredit.validateRequired = ew.CLIENT_VALIDATE;

    // Dynamic selection lists
    loadjs.done("fjugadoredit");
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
<form name="fjugadoredit" id="fjugadoredit" class="<?= $Page->FormClassName ?>" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="jugador">
<input type="hidden" name="action" id="action" value="update">
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<input type="hidden" name="<?= $Page->OldKeyName ?>" value="<?= $Page->OldKey ?>">
<div class="ew-edit-div"><!-- page* -->
<?php if ($Page->id_jugador->Visible) { // id_jugador ?>
    <div id="r_id_jugador"<?= $Page->id_jugador->rowAttributes() ?>>
        <label id="elh_jugador_id_jugador" class="<?= $Page->LeftColumnClass ?>"><?= $Page->id_jugador->caption() ?><?= $Page->id_jugador->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->id_jugador->cellAttributes() ?>>
<span id="el_jugador_id_jugador">
<span<?= $Page->id_jugador->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->id_jugador->getDisplayValue($Page->id_jugador->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="jugador" data-field="x_id_jugador" data-hidden="1" name="x_id_jugador" id="x_id_jugador" value="<?= HtmlEncode($Page->id_jugador->CurrentValue) ?>">
</div></div>
    </div>
<?php } ?>
<?php if ($Page->nombre_jugador->Visible) { // nombre_jugador ?>
    <div id="r_nombre_jugador"<?= $Page->nombre_jugador->rowAttributes() ?>>
        <label id="elh_jugador_nombre_jugador" for="x_nombre_jugador" class="<?= $Page->LeftColumnClass ?>"><?= $Page->nombre_jugador->caption() ?><?= $Page->nombre_jugador->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->nombre_jugador->cellAttributes() ?>>
<span id="el_jugador_nombre_jugador">
<textarea data-table="jugador" data-field="x_nombre_jugador" name="x_nombre_jugador" id="x_nombre_jugador" cols="35" rows="4" placeholder="<?= HtmlEncode($Page->nombre_jugador->getPlaceHolder()) ?>"<?= $Page->nombre_jugador->editAttributes() ?> aria-describedby="x_nombre_jugador_help"><?= $Page->nombre_jugador->EditValue ?></textarea>
<?= $Page->nombre_jugador->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->nombre_jugador->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->votos_jugador->Visible) { // votos_jugador ?>
    <div id="r_votos_jugador"<?= $Page->votos_jugador->rowAttributes() ?>>
        <label id="elh_jugador_votos_jugador" for="x_votos_jugador" class="<?= $Page->LeftColumnClass ?>"><?= $Page->votos_jugador->caption() ?><?= $Page->votos_jugador->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->votos_jugador->cellAttributes() ?>>
<span id="el_jugador_votos_jugador">
<input type="<?= $Page->votos_jugador->getInputTextType() ?>" name="x_votos_jugador" id="x_votos_jugador" data-table="jugador" data-field="x_votos_jugador" value="<?= $Page->votos_jugador->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->votos_jugador->getPlaceHolder()) ?>"<?= $Page->votos_jugador->editAttributes() ?> aria-describedby="x_votos_jugador_help">
<?= $Page->votos_jugador->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->votos_jugador->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->imagen_jugador->Visible) { // imagen_jugador ?>
    <div id="r_imagen_jugador"<?= $Page->imagen_jugador->rowAttributes() ?>>
        <label id="elh_jugador_imagen_jugador" class="<?= $Page->LeftColumnClass ?>"><?= $Page->imagen_jugador->caption() ?><?= $Page->imagen_jugador->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->imagen_jugador->cellAttributes() ?>>
<span id="el_jugador_imagen_jugador">
<div id="fd_x_imagen_jugador" class="fileinput-button ew-file-drop-zone">
    <input type="file" class="form-control ew-file-input" title="<?= $Page->imagen_jugador->title() ?>" data-table="jugador" data-field="x_imagen_jugador" name="x_imagen_jugador" id="x_imagen_jugador" lang="<?= CurrentLanguageID() ?>"<?= $Page->imagen_jugador->editAttributes() ?> aria-describedby="x_imagen_jugador_help"<?= ($Page->imagen_jugador->ReadOnly || $Page->imagen_jugador->Disabled) ? " disabled" : "" ?>>
    <div class="text-muted ew-file-text"><?= $Language->phrase("ChooseFile") ?></div>
</div>
<?= $Page->imagen_jugador->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->imagen_jugador->getErrorMessage() ?></div>
<input type="hidden" name="fn_x_imagen_jugador" id= "fn_x_imagen_jugador" value="<?= $Page->imagen_jugador->Upload->FileName ?>">
<input type="hidden" name="fa_x_imagen_jugador" id= "fa_x_imagen_jugador" value="<?= (Post("fa_x_imagen_jugador") == "0") ? "0" : "1" ?>">
<input type="hidden" name="fs_x_imagen_jugador" id= "fs_x_imagen_jugador" value="1024">
<input type="hidden" name="fx_x_imagen_jugador" id= "fx_x_imagen_jugador" value="<?= $Page->imagen_jugador->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x_imagen_jugador" id= "fm_x_imagen_jugador" value="<?= $Page->imagen_jugador->UploadMaxFileSize ?>">
<table id="ft_x_imagen_jugador" class="table table-sm float-start ew-upload-table"><tbody class="files"></tbody></table>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->posicion->Visible) { // posicion ?>
    <div id="r_posicion"<?= $Page->posicion->rowAttributes() ?>>
        <label id="elh_jugador_posicion" for="x_posicion" class="<?= $Page->LeftColumnClass ?>"><?= $Page->posicion->caption() ?><?= $Page->posicion->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->posicion->cellAttributes() ?>>
<span id="el_jugador_posicion">
<input type="<?= $Page->posicion->getInputTextType() ?>" name="x_posicion" id="x_posicion" data-table="jugador" data-field="x_posicion" value="<?= $Page->posicion->EditValue ?>" size="30" maxlength="56" placeholder="<?= HtmlEncode($Page->posicion->getPlaceHolder()) ?>"<?= $Page->posicion->editAttributes() ?> aria-describedby="x_posicion_help">
<?= $Page->posicion->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->posicion->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->nombre_equipo->Visible) { // nombre_equipo ?>
    <div id="r_nombre_equipo"<?= $Page->nombre_equipo->rowAttributes() ?>>
        <label id="elh_jugador_nombre_equipo" for="x_nombre_equipo" class="<?= $Page->LeftColumnClass ?>"><?= $Page->nombre_equipo->caption() ?><?= $Page->nombre_equipo->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->nombre_equipo->cellAttributes() ?>>
<span id="el_jugador_nombre_equipo">
<textarea data-table="jugador" data-field="x_nombre_equipo" name="x_nombre_equipo" id="x_nombre_equipo" cols="35" rows="4" placeholder="<?= HtmlEncode($Page->nombre_equipo->getPlaceHolder()) ?>"<?= $Page->nombre_equipo->editAttributes() ?> aria-describedby="x_nombre_equipo_help"><?= $Page->nombre_equipo->EditValue ?></textarea>
<?= $Page->nombre_equipo->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->nombre_equipo->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
</div><!-- /page* -->
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
    ew.addEventHandlers("jugador");
});
</script>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
