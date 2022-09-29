<?php

namespace PHPMaker2022\project11;

// Page object
$EstadioList = &$Page;
?>
<?php if (!$Page->isExport()) { ?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { estadio: currentTable } });
var currentForm, currentPageID;
var festadiolist;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    festadiolist = new ew.Form("festadiolist", "list");
    currentPageID = ew.PAGE_ID = "list";
    currentForm = festadiolist;
    festadiolist.formKeyCountName = "<?= $Page->FormKeyCountName ?>";

    // Add fields
    var fields = currentTable.fields;
    festadiolist.addFields([
        ["id_estadio", [fields.id_estadio.visible && fields.id_estadio.required ? ew.Validators.required(fields.id_estadio.caption) : null], fields.id_estadio.isInvalid],
        ["id_torneo", [fields.id_torneo.visible && fields.id_torneo.required ? ew.Validators.required(fields.id_torneo.caption) : null], fields.id_torneo.isInvalid],
        ["nombre_estadio", [fields.nombre_estadio.visible && fields.nombre_estadio.required ? ew.Validators.required(fields.nombre_estadio.caption) : null], fields.nombre_estadio.isInvalid],
        ["foto_estadio", [fields.foto_estadio.visible && fields.foto_estadio.required ? ew.Validators.fileRequired(fields.foto_estadio.caption) : null], fields.foto_estadio.isInvalid],
        ["crea_dato", [fields.crea_dato.visible && fields.crea_dato.required ? ew.Validators.required(fields.crea_dato.caption) : null], fields.crea_dato.isInvalid],
        ["modifica_dato", [fields.modifica_dato.visible && fields.modifica_dato.required ? ew.Validators.required(fields.modifica_dato.caption) : null], fields.modifica_dato.isInvalid]
    ]);

    // Form_CustomValidate
    festadiolist.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    festadiolist.validateRequired = ew.CLIENT_VALIDATE;

    // Dynamic selection lists
    festadiolist.lists.id_torneo = <?= $Page->id_torneo->toClientList($Page) ?>;
    loadjs.done("festadiolist");
});
var festadiosrch, currentSearchForm, currentAdvancedSearchForm;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object for search
    festadiosrch = new ew.Form("festadiosrch", "list");
    currentSearchForm = festadiosrch;

    // Dynamic selection lists

    // Filters
    festadiosrch.filterList = <?= $Page->getFilterList() ?>;
    loadjs.done("festadiosrch");
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
<form name="festadiosrch" id="festadiosrch" class="ew-form ew-ext-search-form" action="<?= CurrentPageUrl(false) ?>">
<div id="festadiosrch_search_panel" class="mb-2 mb-sm-0 <?= $Page->SearchPanelClass ?>"><!-- .ew-search-panel -->
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="estadio">
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
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "" ? " active" : "" ?>" form="festadiosrch" data-ew-action="search-type"><?= $Language->phrase("QuickSearchAuto") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "=" ? " active" : "" ?>" form="festadiosrch" data-ew-action="search-type" data-search-type="="><?= $Language->phrase("QuickSearchExact") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "AND" ? " active" : "" ?>" form="festadiosrch" data-ew-action="search-type" data-search-type="AND"><?= $Language->phrase("QuickSearchAll") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "OR" ? " active" : "" ?>" form="festadiosrch" data-ew-action="search-type" data-search-type="OR"><?= $Language->phrase("QuickSearchAny") ?></button>
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
<div class="card ew-card ew-grid<?php if ($Page->isAddOrEdit()) { ?> ew-grid-add-edit<?php } ?> estadio">
<form name="festadiolist" id="festadiolist" class="ew-form ew-list-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="estadio">
<div id="gmp_estadio" class="<?= ResponsiveTableClass() ?>card-body ew-grid-middle-panel">
<?php if ($Page->TotalRecords > 0 || $Page->isGridEdit()) { ?>
<table id="tbl_estadiolist" class="table table-bordered table-hover table-sm ew-table"><!-- .ew-table -->
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
<?php if ($Page->id_estadio->Visible) { // id_estadio ?>
        <th data-name="id_estadio" class="<?= $Page->id_estadio->headerCellClass() ?>"><div id="elh_estadio_id_estadio" class="estadio_id_estadio"><?= $Page->renderFieldHeader($Page->id_estadio) ?></div></th>
<?php } ?>
<?php if ($Page->id_torneo->Visible) { // id_torneo ?>
        <th data-name="id_torneo" class="<?= $Page->id_torneo->headerCellClass() ?>"><div id="elh_estadio_id_torneo" class="estadio_id_torneo"><?= $Page->renderFieldHeader($Page->id_torneo) ?></div></th>
<?php } ?>
<?php if ($Page->nombre_estadio->Visible) { // nombre_estadio ?>
        <th data-name="nombre_estadio" class="<?= $Page->nombre_estadio->headerCellClass() ?>"><div id="elh_estadio_nombre_estadio" class="estadio_nombre_estadio"><?= $Page->renderFieldHeader($Page->nombre_estadio) ?></div></th>
<?php } ?>
<?php if ($Page->foto_estadio->Visible) { // foto_estadio ?>
        <th data-name="foto_estadio" class="<?= $Page->foto_estadio->headerCellClass() ?>"><div id="elh_estadio_foto_estadio" class="estadio_foto_estadio"><?= $Page->renderFieldHeader($Page->foto_estadio) ?></div></th>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <th data-name="crea_dato" class="<?= $Page->crea_dato->headerCellClass() ?>" style="white-space: nowrap;"><div id="elh_estadio_crea_dato" class="estadio_crea_dato"><?= $Page->renderFieldHeader($Page->crea_dato) ?></div></th>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <th data-name="modifica_dato" class="<?= $Page->modifica_dato->headerCellClass() ?>" style="white-space: nowrap;"><div id="elh_estadio_modifica_dato" class="estadio_modifica_dato"><?= $Page->renderFieldHeader($Page->modifica_dato) ?></div></th>
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
            "id" => "r" . $Page->RowCount . "_estadio",
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
    <?php if ($Page->id_estadio->Visible) { // id_estadio ?>
        <td data-name="id_estadio"<?= $Page->id_estadio->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_estadio_id_estadio" class="el_estadio_id_estadio">
<span<?= $Page->id_estadio->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->id_estadio->getDisplayValue($Page->id_estadio->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="estadio" data-field="x_id_estadio" data-hidden="1" name="x<?= $Page->RowIndex ?>_id_estadio" id="x<?= $Page->RowIndex ?>_id_estadio" value="<?= HtmlEncode($Page->id_estadio->CurrentValue) ?>">
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_estadio_id_estadio" class="el_estadio_id_estadio">
<span<?= $Page->id_estadio->viewAttributes() ?>>
<?= $Page->id_estadio->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } else { ?>
            <input type="hidden" data-table="estadio" data-field="x_id_estadio" data-hidden="1" name="x<?= $Page->RowIndex ?>_id_estadio" id="x<?= $Page->RowIndex ?>_id_estadio" value="<?= HtmlEncode($Page->id_estadio->CurrentValue) ?>">
    <?php } ?>
    <?php if ($Page->id_torneo->Visible) { // id_torneo ?>
        <td data-name="id_torneo"<?= $Page->id_torneo->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_estadio_id_torneo" class="el_estadio_id_torneo">
    <select
        id="x<?= $Page->RowIndex ?>_id_torneo"
        name="x<?= $Page->RowIndex ?>_id_torneo"
        class="form-select ew-select<?= $Page->id_torneo->isInvalidClass() ?>"
        data-select2-id="festadiolist_x<?= $Page->RowIndex ?>_id_torneo"
        data-table="estadio"
        data-field="x_id_torneo"
        data-value-separator="<?= $Page->id_torneo->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->id_torneo->getPlaceHolder()) ?>"
        <?= $Page->id_torneo->editAttributes() ?>>
        <?= $Page->id_torneo->selectOptionListHtml("x{$Page->RowIndex}_id_torneo") ?>
    </select>
    <div class="invalid-feedback"><?= $Page->id_torneo->getErrorMessage() ?></div>
<?= $Page->id_torneo->Lookup->getParamTag($Page, "p_x" . $Page->RowIndex . "_id_torneo") ?>
<script>
loadjs.ready("festadiolist", function() {
    var options = { name: "x<?= $Page->RowIndex ?>_id_torneo", selectId: "festadiolist_x<?= $Page->RowIndex ?>_id_torneo" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (festadiolist.lists.id_torneo.lookupOptions.length) {
        options.data = { id: "x<?= $Page->RowIndex ?>_id_torneo", form: "festadiolist" };
    } else {
        options.ajax = { id: "x<?= $Page->RowIndex ?>_id_torneo", form: "festadiolist", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.estadio.fields.id_torneo.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_estadio_id_torneo" class="el_estadio_id_torneo">
<span<?= $Page->id_torneo->viewAttributes() ?>>
<?= $Page->id_torneo->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->nombre_estadio->Visible) { // nombre_estadio ?>
        <td data-name="nombre_estadio"<?= $Page->nombre_estadio->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_estadio_nombre_estadio" class="el_estadio_nombre_estadio">
<textarea data-table="estadio" data-field="x_nombre_estadio" name="x<?= $Page->RowIndex ?>_nombre_estadio" id="x<?= $Page->RowIndex ?>_nombre_estadio" cols="35" rows="4" placeholder="<?= HtmlEncode($Page->nombre_estadio->getPlaceHolder()) ?>"<?= $Page->nombre_estadio->editAttributes() ?>><?= $Page->nombre_estadio->EditValue ?></textarea>
<div class="invalid-feedback"><?= $Page->nombre_estadio->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_estadio_nombre_estadio" class="el_estadio_nombre_estadio">
<span<?= $Page->nombre_estadio->viewAttributes() ?>>
<?= $Page->nombre_estadio->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->foto_estadio->Visible) { // foto_estadio ?>
        <td data-name="foto_estadio"<?= $Page->foto_estadio->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_estadio_foto_estadio" class="el_estadio_foto_estadio">
<div id="fd_x<?= $Page->RowIndex ?>_foto_estadio" class="fileinput-button ew-file-drop-zone">
    <input type="file" class="form-control ew-file-input" title="<?= $Page->foto_estadio->title() ?>" data-table="estadio" data-field="x_foto_estadio" name="x<?= $Page->RowIndex ?>_foto_estadio" id="x<?= $Page->RowIndex ?>_foto_estadio" lang="<?= CurrentLanguageID() ?>"<?= $Page->foto_estadio->editAttributes() ?><?= ($Page->foto_estadio->ReadOnly || $Page->foto_estadio->Disabled) ? " disabled" : "" ?>>
    <div class="text-muted ew-file-text"><?= $Language->phrase("ChooseFile") ?></div>
</div>
<div class="invalid-feedback"><?= $Page->foto_estadio->getErrorMessage() ?></div>
<input type="hidden" name="fn_x<?= $Page->RowIndex ?>_foto_estadio" id= "fn_x<?= $Page->RowIndex ?>_foto_estadio" value="<?= $Page->foto_estadio->Upload->FileName ?>">
<input type="hidden" name="fa_x<?= $Page->RowIndex ?>_foto_estadio" id= "fa_x<?= $Page->RowIndex ?>_foto_estadio" value="<?= (Post("fa_x<?= $Page->RowIndex ?>_foto_estadio") == "0") ? "0" : "1" ?>">
<input type="hidden" name="fs_x<?= $Page->RowIndex ?>_foto_estadio" id= "fs_x<?= $Page->RowIndex ?>_foto_estadio" value="1024">
<input type="hidden" name="fx_x<?= $Page->RowIndex ?>_foto_estadio" id= "fx_x<?= $Page->RowIndex ?>_foto_estadio" value="<?= $Page->foto_estadio->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x<?= $Page->RowIndex ?>_foto_estadio" id= "fm_x<?= $Page->RowIndex ?>_foto_estadio" value="<?= $Page->foto_estadio->UploadMaxFileSize ?>">
<table id="ft_x<?= $Page->RowIndex ?>_foto_estadio" class="table table-sm float-start ew-upload-table"><tbody class="files"></tbody></table>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_estadio_foto_estadio" class="el_estadio_foto_estadio">
<span>
<?= GetFileViewTag($Page->foto_estadio, $Page->foto_estadio->getViewValue(), false) ?>
</span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <td data-name="crea_dato"<?= $Page->crea_dato->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_estadio_crea_dato" class="el_estadio_crea_dato">
<input type="hidden" data-table="estadio" data-field="x_crea_dato" data-hidden="1" name="x<?= $Page->RowIndex ?>_crea_dato" id="x<?= $Page->RowIndex ?>_crea_dato" value="<?= HtmlEncode($Page->crea_dato->CurrentValue) ?>">
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_estadio_crea_dato" class="el_estadio_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<?= $Page->crea_dato->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <td data-name="modifica_dato"<?= $Page->modifica_dato->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_estadio_modifica_dato" class="el_estadio_modifica_dato">
<input type="hidden" data-table="estadio" data-field="x_modifica_dato" data-hidden="1" name="x<?= $Page->RowIndex ?>_modifica_dato" id="x<?= $Page->RowIndex ?>_modifica_dato" value="<?= HtmlEncode($Page->modifica_dato->CurrentValue) ?>">
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_estadio_modifica_dato" class="el_estadio_modifica_dato">
<span<?= $Page->modifica_dato->viewAttributes() ?>>
<?= $Page->modifica_dato->getViewValue() ?></span>
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
loadjs.ready(["festadiolist","load"], () => festadiolist.updateLists(<?= $Page->RowIndex ?>));
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
    ew.addEventHandlers("estadio");
});
</script>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
<?php } ?>
