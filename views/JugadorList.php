<?php

namespace PHPMaker2022\project11;

// Page object
$JugadorList = &$Page;
?>
<?php if (!$Page->isExport()) { ?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { jugador: currentTable } });
var currentForm, currentPageID;
var fjugadorlist;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fjugadorlist = new ew.Form("fjugadorlist", "list");
    currentPageID = ew.PAGE_ID = "list";
    currentForm = fjugadorlist;
    fjugadorlist.formKeyCountName = "<?= $Page->FormKeyCountName ?>";

    // Add fields
    var fields = currentTable.fields;
    fjugadorlist.addFields([
        ["id_jugador", [fields.id_jugador.visible && fields.id_jugador.required ? ew.Validators.required(fields.id_jugador.caption) : null], fields.id_jugador.isInvalid],
        ["nombre_jugador", [fields.nombre_jugador.visible && fields.nombre_jugador.required ? ew.Validators.required(fields.nombre_jugador.caption) : null], fields.nombre_jugador.isInvalid],
        ["votos_jugador", [fields.votos_jugador.visible && fields.votos_jugador.required ? ew.Validators.required(fields.votos_jugador.caption) : null], fields.votos_jugador.isInvalid],
        ["imagen_jugador", [fields.imagen_jugador.visible && fields.imagen_jugador.required ? ew.Validators.fileRequired(fields.imagen_jugador.caption) : null], fields.imagen_jugador.isInvalid],
        ["crea_dato", [fields.crea_dato.visible && fields.crea_dato.required ? ew.Validators.required(fields.crea_dato.caption) : null], fields.crea_dato.isInvalid],
        ["modifica_dato", [fields.modifica_dato.visible && fields.modifica_dato.required ? ew.Validators.required(fields.modifica_dato.caption) : null], fields.modifica_dato.isInvalid],
        ["usuario_dato", [fields.usuario_dato.visible && fields.usuario_dato.required ? ew.Validators.required(fields.usuario_dato.caption) : null], fields.usuario_dato.isInvalid],
        ["posicion", [fields.posicion.visible && fields.posicion.required ? ew.Validators.required(fields.posicion.caption) : null], fields.posicion.isInvalid]
    ]);

    // Form_CustomValidate
    fjugadorlist.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fjugadorlist.validateRequired = ew.CLIENT_VALIDATE;

    // Dynamic selection lists
    loadjs.done("fjugadorlist");
});
var fjugadorsrch, currentSearchForm, currentAdvancedSearchForm;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object for search
    fjugadorsrch = new ew.Form("fjugadorsrch", "list");
    currentSearchForm = fjugadorsrch;

    // Dynamic selection lists

    // Filters
    fjugadorsrch.filterList = <?= $Page->getFilterList() ?>;
    loadjs.done("fjugadorsrch");
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
<?php if ($Page->TotalRecords > 0 && $Page->ExportOptions->visible()) { ?>
<?php $Page->ExportOptions->render("body") ?>
<?php } ?>
<?php if ($Page->ImportOptions->visible()) { ?>
<?php $Page->ImportOptions->render("body") ?>
<?php } ?>
<?php if ($Page->SearchOptions->visible()) { ?>
<?php $Page->SearchOptions->render("body") ?>
<?php } ?>
<?php if ($Page->FilterOptions->visible()) { ?>
<?php $Page->FilterOptions->render("body") ?>
<?php } ?>
</div>
<?php } ?>
<?php
$Page->renderOtherOptions();
?>
<?php if ($Security->canSearch()) { ?>
<?php if (!$Page->isExport() && !$Page->CurrentAction && $Page->hasSearchFields()) { ?>
<form name="fjugadorsrch" id="fjugadorsrch" class="ew-form ew-ext-search-form" action="<?= CurrentPageUrl(false) ?>">
<div id="fjugadorsrch_search_panel" class="mb-2 mb-sm-0 <?= $Page->SearchPanelClass ?>"><!-- .ew-search-panel -->
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="jugador">
<div class="ew-extended-search container-fluid">
<div class="row mb-0">
    <div class="col-sm-auto px-0 pe-sm-2">
        <div class="ew-basic-search input-group">
            <input type="search" name="<?= Config("TABLE_BASIC_SEARCH") ?>" id="<?= Config("TABLE_BASIC_SEARCH") ?>" class="form-control ew-basic-search-keyword" value="<?= HtmlEncode($Page->BasicSearch->getKeyword()) ?>" placeholder="<?= HtmlEncode($Language->phrase("Search")) ?>" aria-label="<?= HtmlEncode($Language->phrase("Search")) ?>">
            <input type="hidden" name="<?= Config("TABLE_BASIC_SEARCH_TYPE") ?>" id="<?= Config("TABLE_BASIC_SEARCH_TYPE") ?>" class="ew-basic-search-type" value="<?= HtmlEncode($Page->BasicSearch->getType()) ?>">
            <button type="button" data-bs-toggle="dropdown" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" aria-haspopup="true" aria-expanded="false">
                <span id="searchtype"><?= $Page->BasicSearch->getTypeNameShort() ?></span>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "" ? " active" : "" ?>" form="fjugadorsrch" data-ew-action="search-type"><?= $Language->phrase("QuickSearchAuto") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "=" ? " active" : "" ?>" form="fjugadorsrch" data-ew-action="search-type" data-search-type="="><?= $Language->phrase("QuickSearchExact") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "AND" ? " active" : "" ?>" form="fjugadorsrch" data-ew-action="search-type" data-search-type="AND"><?= $Language->phrase("QuickSearchAll") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "OR" ? " active" : "" ?>" form="fjugadorsrch" data-ew-action="search-type" data-search-type="OR"><?= $Language->phrase("QuickSearchAny") ?></button>
            </div>
        </div>
    </div>
    <div class="col-sm-auto mb-3">
        <button class="btn btn-primary" name="btn-submit" id="btn-submit" type="submit"><?= $Language->phrase("SearchBtn") ?></button>
    </div>
</div>
</div><!-- /.ew-extended-search -->
</div><!-- /.ew-search-panel -->
</form>
<?php } ?>
<?php } ?>
<?php $Page->showPageHeader(); ?>
<?php
$Page->showMessage();
?>
<?php if ($Page->TotalRecords > 0 || $Page->CurrentAction) { ?>
<div class="card ew-card ew-grid<?php if ($Page->isAddOrEdit()) { ?> ew-grid-add-edit<?php } ?> jugador">
<form name="fjugadorlist" id="fjugadorlist" class="ew-form ew-list-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="jugador">
<div id="gmp_jugador" class="<?= ResponsiveTableClass() ?>card-body ew-grid-middle-panel">
<?php if ($Page->TotalRecords > 0 || $Page->isGridEdit()) { ?>
<table id="tbl_jugadorlist" class="table table-bordered table-hover table-sm ew-table"><!-- .ew-table -->
<thead>
    <tr class="ew-table-header">
<?php
// Header row
$Page->RowType = ROWTYPE_HEADER;

// Render list options
$Page->renderListOptions();

// Render list options (header, left)
$Page->ListOptions->render("header", "left");
?>
<?php if ($Page->id_jugador->Visible) { // id_jugador ?>
        <th data-name="id_jugador" class="<?= $Page->id_jugador->headerCellClass() ?>"><div id="elh_jugador_id_jugador" class="jugador_id_jugador"><?= $Page->renderFieldHeader($Page->id_jugador) ?></div></th>
<?php } ?>
<?php if ($Page->nombre_jugador->Visible) { // nombre_jugador ?>
        <th data-name="nombre_jugador" class="<?= $Page->nombre_jugador->headerCellClass() ?>"><div id="elh_jugador_nombre_jugador" class="jugador_nombre_jugador"><?= $Page->renderFieldHeader($Page->nombre_jugador) ?></div></th>
<?php } ?>
<?php if ($Page->votos_jugador->Visible) { // votos_jugador ?>
        <th data-name="votos_jugador" class="<?= $Page->votos_jugador->headerCellClass() ?>"><div id="elh_jugador_votos_jugador" class="jugador_votos_jugador"><?= $Page->renderFieldHeader($Page->votos_jugador) ?></div></th>
<?php } ?>
<?php if ($Page->imagen_jugador->Visible) { // imagen_jugador ?>
        <th data-name="imagen_jugador" class="<?= $Page->imagen_jugador->headerCellClass() ?>"><div id="elh_jugador_imagen_jugador" class="jugador_imagen_jugador"><?= $Page->renderFieldHeader($Page->imagen_jugador) ?></div></th>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <th data-name="crea_dato" class="<?= $Page->crea_dato->headerCellClass() ?>"><div id="elh_jugador_crea_dato" class="jugador_crea_dato"><?= $Page->renderFieldHeader($Page->crea_dato) ?></div></th>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <th data-name="modifica_dato" class="<?= $Page->modifica_dato->headerCellClass() ?>"><div id="elh_jugador_modifica_dato" class="jugador_modifica_dato"><?= $Page->renderFieldHeader($Page->modifica_dato) ?></div></th>
<?php } ?>
<?php if ($Page->usuario_dato->Visible) { // usuario_dato ?>
        <th data-name="usuario_dato" class="<?= $Page->usuario_dato->headerCellClass() ?>"><div id="elh_jugador_usuario_dato" class="jugador_usuario_dato"><?= $Page->renderFieldHeader($Page->usuario_dato) ?></div></th>
<?php } ?>
<?php if ($Page->posicion->Visible) { // posicion ?>
        <th data-name="posicion" class="<?= $Page->posicion->headerCellClass() ?>"><div id="elh_jugador_posicion" class="jugador_posicion"><?= $Page->renderFieldHeader($Page->posicion) ?></div></th>
<?php } ?>
<?php
// Render list options (header, right)
$Page->ListOptions->render("header", "right");
?>
    </tr>
</thead>
<tbody>
<?php
if ($Page->ExportAll && $Page->isExport()) {
    $Page->StopRecord = $Page->TotalRecords;
} else {
    // Set the last record to display
    if ($Page->TotalRecords > $Page->StartRecord + $Page->DisplayRecords - 1) {
        $Page->StopRecord = $Page->StartRecord + $Page->DisplayRecords - 1;
    } else {
        $Page->StopRecord = $Page->TotalRecords;
    }
}

// Restore number of post back records
if ($CurrentForm && ($Page->isConfirm() || $Page->EventCancelled)) {
    $CurrentForm->Index = -1;
    if ($CurrentForm->hasValue($Page->FormKeyCountName) && ($Page->isGridAdd() || $Page->isGridEdit() || $Page->isConfirm())) {
        $Page->KeyCount = $CurrentForm->getValue($Page->FormKeyCountName);
        $Page->StopRecord = $Page->StartRecord + $Page->KeyCount - 1;
    }
}
$Page->RecordCount = $Page->StartRecord - 1;
if ($Page->Recordset && !$Page->Recordset->EOF) {
    // Nothing to do
} elseif ($Page->isGridAdd() && !$Page->AllowAddDeleteRow && $Page->StopRecord == 0) {
    $Page->StopRecord = $Page->GridAddRowCount;
}

// Initialize aggregate
$Page->RowType = ROWTYPE_AGGREGATEINIT;
$Page->resetAttributes();
$Page->renderRow();
$Page->EditRowCount = 0;
if ($Page->isEdit()) {
    $Page->RowIndex = 1;
}
while ($Page->RecordCount < $Page->StopRecord) {
    $Page->RecordCount++;
    if ($Page->RecordCount >= $Page->StartRecord) {
        $Page->RowCount++;

        // Set up key count
        $Page->KeyCount = $Page->RowIndex;

        // Init row class and style
        $Page->resetAttributes();
        $Page->CssClass = "";
        if ($Page->isGridAdd()) {
            $Page->loadRowValues(); // Load default values
            $Page->OldKey = "";
            $Page->setKey($Page->OldKey);
        } else {
            $Page->loadRowValues($Page->Recordset); // Load row values
            if ($Page->isGridEdit()) {
                $Page->OldKey = $Page->getKey(true); // Get from CurrentValue
                $Page->setKey($Page->OldKey);
            }
        }
        $Page->RowType = ROWTYPE_VIEW; // Render view
        if ($Page->isEdit()) {
            if ($Page->checkInlineEditKey() && $Page->EditRowCount == 0) { // Inline edit
                $Page->RowType = ROWTYPE_EDIT; // Render edit
            }
        }
        if ($Page->isEdit() && $Page->RowType == ROWTYPE_EDIT && $Page->EventCancelled) { // Update failed
            $CurrentForm->Index = 1;
            $Page->restoreFormValues(); // Restore form values
        }
        if ($Page->RowType == ROWTYPE_EDIT) { // Edit row
            $Page->EditRowCount++;
        }

        // Set up row attributes
        $Page->RowAttrs->merge([
            "data-rowindex" => $Page->RowCount,
            "id" => "r" . $Page->RowCount . "_jugador",
            "data-rowtype" => $Page->RowType,
            "class" => ($Page->RowCount % 2 != 1) ? "ew-table-alt-row" : "",
        ]);
        if ($Page->isAdd() && $Page->RowType == ROWTYPE_ADD || $Page->isEdit() && $Page->RowType == ROWTYPE_EDIT) { // Inline-Add/Edit row
            $Page->RowAttrs->appendClass("table-active");
        }

        // Render row
        $Page->renderRow();

        // Render list options
        $Page->renderListOptions();
?>
    <tr <?= $Page->rowAttributes() ?>>
<?php
// Render list options (body, left)
$Page->ListOptions->render("body", "left", $Page->RowCount);
?>
    <?php if ($Page->id_jugador->Visible) { // id_jugador ?>
        <td data-name="id_jugador"<?= $Page->id_jugador->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_jugador_id_jugador" class="el_jugador_id_jugador">
<span<?= $Page->id_jugador->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->id_jugador->getDisplayValue($Page->id_jugador->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="jugador" data-field="x_id_jugador" data-hidden="1" name="x<?= $Page->RowIndex ?>_id_jugador" id="x<?= $Page->RowIndex ?>_id_jugador" value="<?= HtmlEncode($Page->id_jugador->CurrentValue) ?>">
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_jugador_id_jugador" class="el_jugador_id_jugador">
<span<?= $Page->id_jugador->viewAttributes() ?>>
<?= $Page->id_jugador->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } else { ?>
            <input type="hidden" data-table="jugador" data-field="x_id_jugador" data-hidden="1" name="x<?= $Page->RowIndex ?>_id_jugador" id="x<?= $Page->RowIndex ?>_id_jugador" value="<?= HtmlEncode($Page->id_jugador->CurrentValue) ?>">
    <?php } ?>
    <?php if ($Page->nombre_jugador->Visible) { // nombre_jugador ?>
        <td data-name="nombre_jugador"<?= $Page->nombre_jugador->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_jugador_nombre_jugador" class="el_jugador_nombre_jugador">
<textarea data-table="jugador" data-field="x_nombre_jugador" name="x<?= $Page->RowIndex ?>_nombre_jugador" id="x<?= $Page->RowIndex ?>_nombre_jugador" cols="35" rows="4" placeholder="<?= HtmlEncode($Page->nombre_jugador->getPlaceHolder()) ?>"<?= $Page->nombre_jugador->editAttributes() ?>><?= $Page->nombre_jugador->EditValue ?></textarea>
<div class="invalid-feedback"><?= $Page->nombre_jugador->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_jugador_nombre_jugador" class="el_jugador_nombre_jugador">
<span<?= $Page->nombre_jugador->viewAttributes() ?>>
<?= $Page->nombre_jugador->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->votos_jugador->Visible) { // votos_jugador ?>
        <td data-name="votos_jugador"<?= $Page->votos_jugador->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_jugador_votos_jugador" class="el_jugador_votos_jugador">
<textarea data-table="jugador" data-field="x_votos_jugador" name="x<?= $Page->RowIndex ?>_votos_jugador" id="x<?= $Page->RowIndex ?>_votos_jugador" cols="35" rows="4" placeholder="<?= HtmlEncode($Page->votos_jugador->getPlaceHolder()) ?>"<?= $Page->votos_jugador->editAttributes() ?>><?= $Page->votos_jugador->EditValue ?></textarea>
<div class="invalid-feedback"><?= $Page->votos_jugador->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_jugador_votos_jugador" class="el_jugador_votos_jugador">
<span<?= $Page->votos_jugador->viewAttributes() ?>>
<?= $Page->votos_jugador->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->imagen_jugador->Visible) { // imagen_jugador ?>
        <td data-name="imagen_jugador"<?= $Page->imagen_jugador->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_jugador_imagen_jugador" class="el_jugador_imagen_jugador">
<div id="fd_x<?= $Page->RowIndex ?>_imagen_jugador" class="fileinput-button ew-file-drop-zone">
    <input type="file" class="form-control ew-file-input" title="<?= $Page->imagen_jugador->title() ?>" data-table="jugador" data-field="x_imagen_jugador" name="x<?= $Page->RowIndex ?>_imagen_jugador" id="x<?= $Page->RowIndex ?>_imagen_jugador" lang="<?= CurrentLanguageID() ?>"<?= $Page->imagen_jugador->editAttributes() ?><?= ($Page->imagen_jugador->ReadOnly || $Page->imagen_jugador->Disabled) ? " disabled" : "" ?>>
    <div class="text-muted ew-file-text"><?= $Language->phrase("ChooseFile") ?></div>
</div>
<div class="invalid-feedback"><?= $Page->imagen_jugador->getErrorMessage() ?></div>
<input type="hidden" name="fn_x<?= $Page->RowIndex ?>_imagen_jugador" id= "fn_x<?= $Page->RowIndex ?>_imagen_jugador" value="<?= $Page->imagen_jugador->Upload->FileName ?>">
<input type="hidden" name="fa_x<?= $Page->RowIndex ?>_imagen_jugador" id= "fa_x<?= $Page->RowIndex ?>_imagen_jugador" value="<?= (Post("fa_x<?= $Page->RowIndex ?>_imagen_jugador") == "0") ? "0" : "1" ?>">
<input type="hidden" name="fs_x<?= $Page->RowIndex ?>_imagen_jugador" id= "fs_x<?= $Page->RowIndex ?>_imagen_jugador" value="1024">
<input type="hidden" name="fx_x<?= $Page->RowIndex ?>_imagen_jugador" id= "fx_x<?= $Page->RowIndex ?>_imagen_jugador" value="<?= $Page->imagen_jugador->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x<?= $Page->RowIndex ?>_imagen_jugador" id= "fm_x<?= $Page->RowIndex ?>_imagen_jugador" value="<?= $Page->imagen_jugador->UploadMaxFileSize ?>">
<table id="ft_x<?= $Page->RowIndex ?>_imagen_jugador" class="table table-sm float-start ew-upload-table"><tbody class="files"></tbody></table>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_jugador_imagen_jugador" class="el_jugador_imagen_jugador">
<span>
<?= GetFileViewTag($Page->imagen_jugador, $Page->imagen_jugador->getViewValue(), false) ?>
</span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <td data-name="crea_dato"<?= $Page->crea_dato->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_jugador_crea_dato" class="el_jugador_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->crea_dato->getDisplayValue($Page->crea_dato->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="jugador" data-field="x_crea_dato" data-hidden="1" name="x<?= $Page->RowIndex ?>_crea_dato" id="x<?= $Page->RowIndex ?>_crea_dato" value="<?= HtmlEncode($Page->crea_dato->CurrentValue) ?>">
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_jugador_crea_dato" class="el_jugador_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<?= $Page->crea_dato->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <td data-name="modifica_dato"<?= $Page->modifica_dato->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_jugador_modifica_dato" class="el_jugador_modifica_dato">
<span<?= $Page->modifica_dato->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->modifica_dato->getDisplayValue($Page->modifica_dato->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="jugador" data-field="x_modifica_dato" data-hidden="1" name="x<?= $Page->RowIndex ?>_modifica_dato" id="x<?= $Page->RowIndex ?>_modifica_dato" value="<?= HtmlEncode($Page->modifica_dato->CurrentValue) ?>">
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_jugador_modifica_dato" class="el_jugador_modifica_dato">
<span<?= $Page->modifica_dato->viewAttributes() ?>>
<?= $Page->modifica_dato->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->usuario_dato->Visible) { // usuario_dato ?>
        <td data-name="usuario_dato"<?= $Page->usuario_dato->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_jugador_usuario_dato" class="el_jugador_usuario_dato">
<span<?= $Page->usuario_dato->viewAttributes() ?>>
<?= $Page->usuario_dato->EditValue ?></span>
</span>
<input type="hidden" data-table="jugador" data-field="x_usuario_dato" data-hidden="1" name="x<?= $Page->RowIndex ?>_usuario_dato" id="x<?= $Page->RowIndex ?>_usuario_dato" value="<?= HtmlEncode($Page->usuario_dato->CurrentValue) ?>">
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_jugador_usuario_dato" class="el_jugador_usuario_dato">
<span<?= $Page->usuario_dato->viewAttributes() ?>>
<?= $Page->usuario_dato->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->posicion->Visible) { // posicion ?>
        <td data-name="posicion"<?= $Page->posicion->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_jugador_posicion" class="el_jugador_posicion">
<input type="<?= $Page->posicion->getInputTextType() ?>" name="x<?= $Page->RowIndex ?>_posicion" id="x<?= $Page->RowIndex ?>_posicion" data-table="jugador" data-field="x_posicion" value="<?= $Page->posicion->EditValue ?>" size="30" maxlength="56" placeholder="<?= HtmlEncode($Page->posicion->getPlaceHolder()) ?>"<?= $Page->posicion->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->posicion->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_jugador_posicion" class="el_jugador_posicion">
<span<?= $Page->posicion->viewAttributes() ?>>
<?= $Page->posicion->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
<?php
// Render list options (body, right)
$Page->ListOptions->render("body", "right", $Page->RowCount);
?>
    </tr>
<?php if ($Page->RowType == ROWTYPE_ADD || $Page->RowType == ROWTYPE_EDIT) { ?>
<script>
loadjs.ready(["fjugadorlist","load"], () => fjugadorlist.updateLists(<?= $Page->RowIndex ?>));
</script>
<?php } ?>
<?php
    }
    if (!$Page->isGridAdd()) {
        $Page->Recordset->moveNext();
    }
}
?>
</tbody>
</table><!-- /.ew-table -->
<?php } ?>
</div><!-- /.ew-grid-middle-panel -->
<?php if ($Page->isEdit()) { ?>
<input type="hidden" name="<?= $Page->FormKeyCountName ?>" id="<?= $Page->FormKeyCountName ?>" value="<?= $Page->KeyCount ?>">
<input type="hidden" name="<?= $Page->OldKeyName ?>" value="<?= $Page->OldKey ?>">
<?php } ?>
<?php if (!$Page->CurrentAction) { ?>
<input type="hidden" name="action" id="action" value="">
<?php } ?>
</form><!-- /.ew-list-form -->
<?php
// Close recordset
if ($Page->Recordset) {
    $Page->Recordset->close();
}
?>
<?php if (!$Page->isExport()) { ?>
<div class="card-footer ew-grid-lower-panel">
<?php if (!$Page->isGridAdd()) { ?>
<form name="ew-pager-form" class="ew-form ew-pager-form" action="<?= CurrentPageUrl(false) ?>">
<?= $Page->Pager->render() ?>
</form>
<?php } ?>
<div class="ew-list-other-options">
<?php $Page->OtherOptions->render("body", "bottom") ?>
</div>
</div>
<?php } ?>
</div><!-- /.ew-grid -->
<?php } else { ?>
<div class="ew-list-other-options">
<?php $Page->OtherOptions->render("body") ?>
</div>
<?php } ?>
<?php
$Page->showPageFooter();
echo GetDebugMessage();
?>
<?php if (!$Page->isExport()) { ?>
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
<?php } ?>
