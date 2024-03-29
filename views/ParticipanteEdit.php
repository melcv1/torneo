<?php

namespace PHPMaker2022\project11;

// Page object
$ParticipanteEdit = &$Page;
?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { participante: currentTable } });
var currentForm, currentPageID;
var fparticipanteedit;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fparticipanteedit = new ew.Form("fparticipanteedit", "edit");
    currentPageID = ew.PAGE_ID = "edit";
    currentForm = fparticipanteedit;

    // Add fields
    var fields = currentTable.fields;
    fparticipanteedit.addFields([
        ["ID_PARTICIPANTE", [fields.ID_PARTICIPANTE.visible && fields.ID_PARTICIPANTE.required ? ew.Validators.required(fields.ID_PARTICIPANTE.caption) : null], fields.ID_PARTICIPANTE.isInvalid],
        ["NOMBRE", [fields.NOMBRE.visible && fields.NOMBRE.required ? ew.Validators.required(fields.NOMBRE.caption) : null], fields.NOMBRE.isInvalid],
        ["APELLIDO", [fields.APELLIDO.visible && fields.APELLIDO.required ? ew.Validators.required(fields.APELLIDO.caption) : null], fields.APELLIDO.isInvalid],
        ["FECHA_NACIMIENTO", [fields.FECHA_NACIMIENTO.visible && fields.FECHA_NACIMIENTO.required ? ew.Validators.required(fields.FECHA_NACIMIENTO.caption) : null], fields.FECHA_NACIMIENTO.isInvalid],
        ["CEDULA", [fields.CEDULA.visible && fields.CEDULA.required ? ew.Validators.required(fields.CEDULA.caption) : null], fields.CEDULA.isInvalid],
        ["_EMAIL", [fields._EMAIL.visible && fields._EMAIL.required ? ew.Validators.required(fields._EMAIL.caption) : null], fields._EMAIL.isInvalid],
        ["TELEFONO", [fields.TELEFONO.visible && fields.TELEFONO.required ? ew.Validators.required(fields.TELEFONO.caption) : null], fields.TELEFONO.isInvalid],
        ["crea_dato", [fields.crea_dato.visible && fields.crea_dato.required ? ew.Validators.required(fields.crea_dato.caption) : null], fields.crea_dato.isInvalid],
        ["modifica_dato", [fields.modifica_dato.visible && fields.modifica_dato.required ? ew.Validators.required(fields.modifica_dato.caption) : null], fields.modifica_dato.isInvalid],
        ["usuario_dato", [fields.usuario_dato.visible && fields.usuario_dato.required ? ew.Validators.required(fields.usuario_dato.caption) : null], fields.usuario_dato.isInvalid]
    ]);

    // Form_CustomValidate
    fparticipanteedit.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fparticipanteedit.validateRequired = ew.CLIENT_VALIDATE;

    // Dynamic selection lists
    loadjs.done("fparticipanteedit");
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
<form name="fparticipanteedit" id="fparticipanteedit" class="<?= $Page->FormClassName ?>" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="participante">
<input type="hidden" name="action" id="action" value="update">
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<input type="hidden" name="<?= $Page->OldKeyName ?>" value="<?= $Page->OldKey ?>">
<div class="ew-edit-div"><!-- page* -->
<?php if ($Page->ID_PARTICIPANTE->Visible) { // ID_PARTICIPANTE ?>
    <div id="r_ID_PARTICIPANTE"<?= $Page->ID_PARTICIPANTE->rowAttributes() ?>>
        <label id="elh_participante_ID_PARTICIPANTE" class="<?= $Page->LeftColumnClass ?>"><?= $Page->ID_PARTICIPANTE->caption() ?><?= $Page->ID_PARTICIPANTE->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->ID_PARTICIPANTE->cellAttributes() ?>>
<span id="el_participante_ID_PARTICIPANTE">
<span<?= $Page->ID_PARTICIPANTE->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->ID_PARTICIPANTE->getDisplayValue($Page->ID_PARTICIPANTE->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="participante" data-field="x_ID_PARTICIPANTE" data-hidden="1" name="x_ID_PARTICIPANTE" id="x_ID_PARTICIPANTE" value="<?= HtmlEncode($Page->ID_PARTICIPANTE->CurrentValue) ?>">
</div></div>
    </div>
<?php } ?>
<?php if ($Page->NOMBRE->Visible) { // NOMBRE ?>
    <div id="r_NOMBRE"<?= $Page->NOMBRE->rowAttributes() ?>>
        <label id="elh_participante_NOMBRE" for="x_NOMBRE" class="<?= $Page->LeftColumnClass ?>"><?= $Page->NOMBRE->caption() ?><?= $Page->NOMBRE->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->NOMBRE->cellAttributes() ?>>
<span id="el_participante_NOMBRE">
<textarea data-table="participante" data-field="x_NOMBRE" name="x_NOMBRE" id="x_NOMBRE" cols="35" rows="1" placeholder="<?= HtmlEncode($Page->NOMBRE->getPlaceHolder()) ?>"<?= $Page->NOMBRE->editAttributes() ?> aria-describedby="x_NOMBRE_help"><?= $Page->NOMBRE->EditValue ?></textarea>
<?= $Page->NOMBRE->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->NOMBRE->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->APELLIDO->Visible) { // APELLIDO ?>
    <div id="r_APELLIDO"<?= $Page->APELLIDO->rowAttributes() ?>>
        <label id="elh_participante_APELLIDO" for="x_APELLIDO" class="<?= $Page->LeftColumnClass ?>"><?= $Page->APELLIDO->caption() ?><?= $Page->APELLIDO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->APELLIDO->cellAttributes() ?>>
<span id="el_participante_APELLIDO">
<textarea data-table="participante" data-field="x_APELLIDO" name="x_APELLIDO" id="x_APELLIDO" cols="35" rows="1" placeholder="<?= HtmlEncode($Page->APELLIDO->getPlaceHolder()) ?>"<?= $Page->APELLIDO->editAttributes() ?> aria-describedby="x_APELLIDO_help"><?= $Page->APELLIDO->EditValue ?></textarea>
<?= $Page->APELLIDO->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->APELLIDO->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->FECHA_NACIMIENTO->Visible) { // FECHA_NACIMIENTO ?>
    <div id="r_FECHA_NACIMIENTO"<?= $Page->FECHA_NACIMIENTO->rowAttributes() ?>>
        <label id="elh_participante_FECHA_NACIMIENTO" for="x_FECHA_NACIMIENTO" class="<?= $Page->LeftColumnClass ?>"><?= $Page->FECHA_NACIMIENTO->caption() ?><?= $Page->FECHA_NACIMIENTO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->FECHA_NACIMIENTO->cellAttributes() ?>>
<span id="el_participante_FECHA_NACIMIENTO">
<textarea data-table="participante" data-field="x_FECHA_NACIMIENTO" name="x_FECHA_NACIMIENTO" id="x_FECHA_NACIMIENTO" cols="35" rows="1" placeholder="<?= HtmlEncode($Page->FECHA_NACIMIENTO->getPlaceHolder()) ?>"<?= $Page->FECHA_NACIMIENTO->editAttributes() ?> aria-describedby="x_FECHA_NACIMIENTO_help"><?= $Page->FECHA_NACIMIENTO->EditValue ?></textarea>
<?= $Page->FECHA_NACIMIENTO->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->FECHA_NACIMIENTO->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->CEDULA->Visible) { // CEDULA ?>
    <div id="r_CEDULA"<?= $Page->CEDULA->rowAttributes() ?>>
        <label id="elh_participante_CEDULA" for="x_CEDULA" class="<?= $Page->LeftColumnClass ?>"><?= $Page->CEDULA->caption() ?><?= $Page->CEDULA->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->CEDULA->cellAttributes() ?>>
<span id="el_participante_CEDULA">
<input type="<?= $Page->CEDULA->getInputTextType() ?>" name="x_CEDULA" id="x_CEDULA" data-table="participante" data-field="x_CEDULA" value="<?= $Page->CEDULA->EditValue ?>" size="30" maxlength="10" placeholder="<?= HtmlEncode($Page->CEDULA->getPlaceHolder()) ?>"<?= $Page->CEDULA->editAttributes() ?> aria-describedby="x_CEDULA_help">
<?= $Page->CEDULA->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->CEDULA->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->_EMAIL->Visible) { // EMAIL ?>
    <div id="r__EMAIL"<?= $Page->_EMAIL->rowAttributes() ?>>
        <label id="elh_participante__EMAIL" for="x__EMAIL" class="<?= $Page->LeftColumnClass ?>"><?= $Page->_EMAIL->caption() ?><?= $Page->_EMAIL->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->_EMAIL->cellAttributes() ?>>
<span id="el_participante__EMAIL">
<textarea data-table="participante" data-field="x__EMAIL" name="x__EMAIL" id="x__EMAIL" cols="35" rows="1" placeholder="<?= HtmlEncode($Page->_EMAIL->getPlaceHolder()) ?>"<?= $Page->_EMAIL->editAttributes() ?> aria-describedby="x__EMAIL_help"><?= $Page->_EMAIL->EditValue ?></textarea>
<?= $Page->_EMAIL->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->_EMAIL->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->TELEFONO->Visible) { // TELEFONO ?>
    <div id="r_TELEFONO"<?= $Page->TELEFONO->rowAttributes() ?>>
        <label id="elh_participante_TELEFONO" for="x_TELEFONO" class="<?= $Page->LeftColumnClass ?>"><?= $Page->TELEFONO->caption() ?><?= $Page->TELEFONO->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->TELEFONO->cellAttributes() ?>>
<span id="el_participante_TELEFONO">
<input type="<?= $Page->TELEFONO->getInputTextType() ?>" name="x_TELEFONO" id="x_TELEFONO" data-table="participante" data-field="x_TELEFONO" value="<?= $Page->TELEFONO->EditValue ?>" size="30" maxlength="10" placeholder="<?= HtmlEncode($Page->TELEFONO->getPlaceHolder()) ?>"<?= $Page->TELEFONO->editAttributes() ?> aria-describedby="x_TELEFONO_help">
<?= $Page->TELEFONO->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->TELEFONO->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
    <div id="r_crea_dato"<?= $Page->crea_dato->rowAttributes() ?>>
        <label id="elh_participante_crea_dato" for="x_crea_dato" class="<?= $Page->LeftColumnClass ?>"><?= $Page->crea_dato->caption() ?><?= $Page->crea_dato->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->crea_dato->cellAttributes() ?>>
<span id="el_participante_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->crea_dato->getDisplayValue($Page->crea_dato->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="participante" data-field="x_crea_dato" data-hidden="1" name="x_crea_dato" id="x_crea_dato" value="<?= HtmlEncode($Page->crea_dato->CurrentValue) ?>">
</div></div>
    </div>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
    <div id="r_modifica_dato"<?= $Page->modifica_dato->rowAttributes() ?>>
        <label id="elh_participante_modifica_dato" for="x_modifica_dato" class="<?= $Page->LeftColumnClass ?>"><?= $Page->modifica_dato->caption() ?><?= $Page->modifica_dato->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->modifica_dato->cellAttributes() ?>>
<span id="el_participante_modifica_dato">
<span<?= $Page->modifica_dato->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->modifica_dato->getDisplayValue($Page->modifica_dato->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="participante" data-field="x_modifica_dato" data-hidden="1" name="x_modifica_dato" id="x_modifica_dato" value="<?= HtmlEncode($Page->modifica_dato->CurrentValue) ?>">
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
    ew.addEventHandlers("participante");
});
</script>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
