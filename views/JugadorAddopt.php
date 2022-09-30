<?php

namespace PHPMaker2022\project11;

// Page object
$JugadorAddopt = &$Page;
?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { jugador: currentTable } });
var currentForm, currentPageID;
var fjugadoraddopt;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fjugadoraddopt = new ew.Form("fjugadoraddopt", "addopt");
    currentPageID = ew.PAGE_ID = "addopt";
    currentForm = fjugadoraddopt;

    // Add fields
    var fields = currentTable.fields;
    fjugadoraddopt.addFields([
        ["nombre_jugador", [fields.nombre_jugador.visible && fields.nombre_jugador.required ? ew.Validators.required(fields.nombre_jugador.caption) : null], fields.nombre_jugador.isInvalid],
        ["votos_jugador", [fields.votos_jugador.visible && fields.votos_jugador.required ? ew.Validators.required(fields.votos_jugador.caption) : null, ew.Validators.integer], fields.votos_jugador.isInvalid],
        ["imagen_jugador", [fields.imagen_jugador.visible && fields.imagen_jugador.required ? ew.Validators.fileRequired(fields.imagen_jugador.caption) : null], fields.imagen_jugador.isInvalid],
        ["crea_dato", [fields.crea_dato.visible && fields.crea_dato.required ? ew.Validators.required(fields.crea_dato.caption) : null, ew.Validators.datetime(fields.crea_dato.clientFormatPattern)], fields.crea_dato.isInvalid],
        ["modifica_dato", [fields.modifica_dato.visible && fields.modifica_dato.required ? ew.Validators.required(fields.modifica_dato.caption) : null, ew.Validators.datetime(fields.modifica_dato.clientFormatPattern)], fields.modifica_dato.isInvalid],
        ["usuario_dato", [fields.usuario_dato.visible && fields.usuario_dato.required ? ew.Validators.required(fields.usuario_dato.caption) : null], fields.usuario_dato.isInvalid],
        ["posicion", [fields.posicion.visible && fields.posicion.required ? ew.Validators.required(fields.posicion.caption) : null], fields.posicion.isInvalid],
        ["nombre_equipo", [fields.nombre_equipo.visible && fields.nombre_equipo.required ? ew.Validators.required(fields.nombre_equipo.caption) : null], fields.nombre_equipo.isInvalid]
    ]);

    // Form_CustomValidate
    fjugadoraddopt.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fjugadoraddopt.validateRequired = ew.CLIENT_VALIDATE;

    // Dynamic selection lists
    loadjs.done("fjugadoraddopt");
});
</script>
<script>
loadjs.ready("head", function () {
    // Write your table-specific client script here, no need to add script tags.
});
</script>
<?php $Page->showPageHeader(); ?>
<form name="fjugadoraddopt" id="fjugadoraddopt" class="ew-form" action="<?= HtmlEncode(GetUrl(Config("API_URL"))) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="<?= Config("API_ACTION_NAME") ?>" id="<?= Config("API_ACTION_NAME") ?>" value="<?= Config("API_ADD_ACTION") ?>">
<input type="hidden" name="<?= Config("API_OBJECT_NAME") ?>" id="<?= Config("API_OBJECT_NAME") ?>" value="jugador">
<input type="hidden" name="addopt" id="addopt" value="1">
<?php if ($Page->nombre_jugador->Visible) { // nombre_jugador ?>
    <div<?= $Page->nombre_jugador->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_nombre_jugador"><?= $Page->nombre_jugador->caption() ?><?= $Page->nombre_jugador->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->nombre_jugador->cellAttributes() ?>>
<textarea data-table="jugador" data-field="x_nombre_jugador" name="x_nombre_jugador" id="x_nombre_jugador" cols="35" rows="4" placeholder="<?= HtmlEncode($Page->nombre_jugador->getPlaceHolder()) ?>"<?= $Page->nombre_jugador->editAttributes() ?>><?= $Page->nombre_jugador->EditValue ?></textarea>
<div class="invalid-feedback"><?= $Page->nombre_jugador->getErrorMessage() ?></div>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->votos_jugador->Visible) { // votos_jugador ?>
    <div<?= $Page->votos_jugador->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_votos_jugador"><?= $Page->votos_jugador->caption() ?><?= $Page->votos_jugador->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->votos_jugador->cellAttributes() ?>>
<input type="<?= $Page->votos_jugador->getInputTextType() ?>" name="x_votos_jugador" id="x_votos_jugador" data-table="jugador" data-field="x_votos_jugador" value="<?= $Page->votos_jugador->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->votos_jugador->getPlaceHolder()) ?>"<?= $Page->votos_jugador->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->votos_jugador->getErrorMessage() ?></div>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->imagen_jugador->Visible) { // imagen_jugador ?>
    <div<?= $Page->imagen_jugador->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label"><?= $Page->imagen_jugador->caption() ?><?= $Page->imagen_jugador->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->imagen_jugador->cellAttributes() ?>>
<div id="fd_x_imagen_jugador" class="fileinput-button ew-file-drop-zone">
    <input type="file" class="form-control ew-file-input" title="<?= $Page->imagen_jugador->title() ?>" data-table="jugador" data-field="x_imagen_jugador" name="x_imagen_jugador" id="x_imagen_jugador" lang="<?= CurrentLanguageID() ?>"<?= $Page->imagen_jugador->editAttributes() ?><?= ($Page->imagen_jugador->ReadOnly || $Page->imagen_jugador->Disabled) ? " disabled" : "" ?>>
    <div class="text-muted ew-file-text"><?= $Language->phrase("ChooseFile") ?></div>
</div>
<div class="invalid-feedback"><?= $Page->imagen_jugador->getErrorMessage() ?></div>
<input type="hidden" name="fn_x_imagen_jugador" id= "fn_x_imagen_jugador" value="<?= $Page->imagen_jugador->Upload->FileName ?>">
<input type="hidden" name="fa_x_imagen_jugador" id= "fa_x_imagen_jugador" value="0">
<input type="hidden" name="fs_x_imagen_jugador" id= "fs_x_imagen_jugador" value="1024">
<input type="hidden" name="fx_x_imagen_jugador" id= "fx_x_imagen_jugador" value="<?= $Page->imagen_jugador->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x_imagen_jugador" id= "fm_x_imagen_jugador" value="<?= $Page->imagen_jugador->UploadMaxFileSize ?>">
<table id="ft_x_imagen_jugador" class="table table-sm float-start ew-upload-table"><tbody class="files"></tbody></table>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
    <div<?= $Page->crea_dato->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_crea_dato"><?= $Page->crea_dato->caption() ?><?= $Page->crea_dato->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->crea_dato->cellAttributes() ?>>
<input type="<?= $Page->crea_dato->getInputTextType() ?>" name="x_crea_dato" id="x_crea_dato" data-table="jugador" data-field="x_crea_dato" value="<?= $Page->crea_dato->EditValue ?>" placeholder="<?= HtmlEncode($Page->crea_dato->getPlaceHolder()) ?>"<?= $Page->crea_dato->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->crea_dato->getErrorMessage() ?></div>
<?php if (!$Page->crea_dato->ReadOnly && !$Page->crea_dato->Disabled && !isset($Page->crea_dato->EditAttrs["readonly"]) && !isset($Page->crea_dato->EditAttrs["disabled"])) { ?>
<script>
loadjs.ready(["fjugadoraddopt", "datetimepicker"], function () {
    let format = "<?= DateFormat(0) ?>",
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
    ew.createDateTimePicker("fjugadoraddopt", "x_crea_dato", ew.deepAssign({"useCurrent":false}, options));
});
</script>
<?php } ?>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
    <div<?= $Page->modifica_dato->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_modifica_dato"><?= $Page->modifica_dato->caption() ?><?= $Page->modifica_dato->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->modifica_dato->cellAttributes() ?>>
<input type="<?= $Page->modifica_dato->getInputTextType() ?>" name="x_modifica_dato" id="x_modifica_dato" data-table="jugador" data-field="x_modifica_dato" value="<?= $Page->modifica_dato->EditValue ?>" placeholder="<?= HtmlEncode($Page->modifica_dato->getPlaceHolder()) ?>"<?= $Page->modifica_dato->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->modifica_dato->getErrorMessage() ?></div>
<?php if (!$Page->modifica_dato->ReadOnly && !$Page->modifica_dato->Disabled && !isset($Page->modifica_dato->EditAttrs["readonly"]) && !isset($Page->modifica_dato->EditAttrs["disabled"])) { ?>
<script>
loadjs.ready(["fjugadoraddopt", "datetimepicker"], function () {
    let format = "<?= DateFormat(0) ?>",
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
    ew.createDateTimePicker("fjugadoraddopt", "x_modifica_dato", ew.deepAssign({"useCurrent":false}, options));
});
</script>
<?php } ?>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->usuario_dato->Visible) { // usuario_dato ?>
    <input type="hidden" data-table="jugador" data-field="x_usuario_dato" data-hidden="1" name="x_usuario_dato" id="x_usuario_dato" value="<?= HtmlEncode($Page->usuario_dato->CurrentValue) ?>">
<?php } ?>
<?php if ($Page->posicion->Visible) { // posicion ?>
    <div<?= $Page->posicion->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_posicion"><?= $Page->posicion->caption() ?><?= $Page->posicion->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->posicion->cellAttributes() ?>>
<input type="<?= $Page->posicion->getInputTextType() ?>" name="x_posicion" id="x_posicion" data-table="jugador" data-field="x_posicion" value="<?= $Page->posicion->EditValue ?>" size="30" maxlength="56" placeholder="<?= HtmlEncode($Page->posicion->getPlaceHolder()) ?>"<?= $Page->posicion->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->posicion->getErrorMessage() ?></div>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->nombre_equipo->Visible) { // nombre_equipo ?>
    <div<?= $Page->nombre_equipo->rowAttributes() ?>>
        <label class="col-sm-2 col-form-label ew-label" for="x_nombre_equipo"><?= $Page->nombre_equipo->caption() ?><?= $Page->nombre_equipo->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="col-sm-10"><div<?= $Page->nombre_equipo->cellAttributes() ?>>
<textarea data-table="jugador" data-field="x_nombre_equipo" name="x_nombre_equipo" id="x_nombre_equipo" cols="35" rows="4" placeholder="<?= HtmlEncode($Page->nombre_equipo->getPlaceHolder()) ?>"<?= $Page->nombre_equipo->editAttributes() ?>><?= $Page->nombre_equipo->EditValue ?></textarea>
<div class="invalid-feedback"><?= $Page->nombre_equipo->getErrorMessage() ?></div>
</div></div>
    </div>
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
