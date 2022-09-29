<?php

namespace PHPMaker2022\project11;

// Page object
$PronosticadorList = &$Page;
?>
<?php if (!$Page->isExport()) { ?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { pronosticador: currentTable } });
var currentForm, currentPageID;
var fpronosticadorlist;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fpronosticadorlist = new ew.Form("fpronosticadorlist", "list");
    currentPageID = ew.PAGE_ID = "list";
    currentForm = fpronosticadorlist;
    fpronosticadorlist.formKeyCountName = "<?= $Page->FormKeyCountName ?>";

    // Add fields
    var fields = currentTable.fields;
    fpronosticadorlist.addFields([
        ["ID_ENCUESTA", [fields.ID_ENCUESTA.visible && fields.ID_ENCUESTA.required ? ew.Validators.required(fields.ID_ENCUESTA.caption) : null], fields.ID_ENCUESTA.isInvalid],
        ["ID_PARTICIPANTE", [fields.ID_PARTICIPANTE.visible && fields.ID_PARTICIPANTE.required ? ew.Validators.required(fields.ID_PARTICIPANTE.caption) : null], fields.ID_PARTICIPANTE.isInvalid],
        ["GRUPO", [fields.GRUPO.visible && fields.GRUPO.required ? ew.Validators.required(fields.GRUPO.caption) : null], fields.GRUPO.isInvalid],
        ["EQUIPO", [fields.EQUIPO.visible && fields.EQUIPO.required ? ew.Validators.required(fields.EQUIPO.caption) : null], fields.EQUIPO.isInvalid],
        ["POSICION", [fields.POSICION.visible && fields.POSICION.required ? ew.Validators.required(fields.POSICION.caption) : null], fields.POSICION.isInvalid],
        ["NUMERACION", [fields.NUMERACION.visible && fields.NUMERACION.required ? ew.Validators.required(fields.NUMERACION.caption) : null], fields.NUMERACION.isInvalid],
        ["crea_dato", [fields.crea_dato.visible && fields.crea_dato.required ? ew.Validators.required(fields.crea_dato.caption) : null], fields.crea_dato.isInvalid],
        ["modifica_dato", [fields.modifica_dato.visible && fields.modifica_dato.required ? ew.Validators.required(fields.modifica_dato.caption) : null], fields.modifica_dato.isInvalid]
    ]);

    // Form_CustomValidate
    fpronosticadorlist.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fpronosticadorlist.validateRequired = ew.CLIENT_VALIDATE;

    // Dynamic selection lists
    fpronosticadorlist.lists.ID_PARTICIPANTE = <?= $Page->ID_PARTICIPANTE->toClientList($Page) ?>;
    fpronosticadorlist.lists.GRUPO = <?= $Page->GRUPO->toClientList($Page) ?>;
    fpronosticadorlist.lists.EQUIPO = <?= $Page->EQUIPO->toClientList($Page) ?>;
    fpronosticadorlist.lists.POSICION = <?= $Page->POSICION->toClientList($Page) ?>;
    loadjs.done("fpronosticadorlist");
});
var fpronosticadorsrch, currentSearchForm, currentAdvancedSearchForm;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object for search
    fpronosticadorsrch = new ew.Form("fpronosticadorsrch", "list");
    currentSearchForm = fpronosticadorsrch;

    // Dynamic selection lists

    // Filters
    fpronosticadorsrch.filterList = <?= $Page->getFilterList() ?>;
    loadjs.done("fpronosticadorsrch");
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
<form name="fpronosticadorsrch" id="fpronosticadorsrch" class="ew-form ew-ext-search-form" action="<?= CurrentPageUrl(false) ?>">
<div id="fpronosticadorsrch_search_panel" class="mb-2 mb-sm-0 <?= $Page->SearchPanelClass ?>"><!-- .ew-search-panel -->
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="pronosticador">
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
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "" ? " active" : "" ?>" form="fpronosticadorsrch" data-ew-action="search-type"><?= $Language->phrase("QuickSearchAuto") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "=" ? " active" : "" ?>" form="fpronosticadorsrch" data-ew-action="search-type" data-search-type="="><?= $Language->phrase("QuickSearchExact") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "AND" ? " active" : "" ?>" form="fpronosticadorsrch" data-ew-action="search-type" data-search-type="AND"><?= $Language->phrase("QuickSearchAll") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "OR" ? " active" : "" ?>" form="fpronosticadorsrch" data-ew-action="search-type" data-search-type="OR"><?= $Language->phrase("QuickSearchAny") ?></button>
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
<div class="card ew-card ew-grid<?php if ($Page->isAddOrEdit()) { ?> ew-grid-add-edit<?php } ?> pronosticador">
<form name="fpronosticadorlist" id="fpronosticadorlist" class="ew-form ew-list-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="pronosticador">
<div id="gmp_pronosticador" class="<?= ResponsiveTableClass() ?>card-body ew-grid-middle-panel">
<?php if ($Page->TotalRecords > 0 || $Page->isGridEdit()) { ?>
<table id="tbl_pronosticadorlist" class="table table-bordered table-hover table-sm ew-table"><!-- .ew-table -->
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
<?php if ($Page->ID_ENCUESTA->Visible) { // ID_ENCUESTA ?>
        <th data-name="ID_ENCUESTA" class="<?= $Page->ID_ENCUESTA->headerCellClass() ?>"><div id="elh_pronosticador_ID_ENCUESTA" class="pronosticador_ID_ENCUESTA"><?= $Page->renderFieldHeader($Page->ID_ENCUESTA) ?></div></th>
<?php } ?>
<?php if ($Page->ID_PARTICIPANTE->Visible) { // ID_PARTICIPANTE ?>
        <th data-name="ID_PARTICIPANTE" class="<?= $Page->ID_PARTICIPANTE->headerCellClass() ?>"><div id="elh_pronosticador_ID_PARTICIPANTE" class="pronosticador_ID_PARTICIPANTE"><?= $Page->renderFieldHeader($Page->ID_PARTICIPANTE) ?></div></th>
<?php } ?>
<?php if ($Page->GRUPO->Visible) { // GRUPO ?>
        <th data-name="GRUPO" class="<?= $Page->GRUPO->headerCellClass() ?>"><div id="elh_pronosticador_GRUPO" class="pronosticador_GRUPO"><?= $Page->renderFieldHeader($Page->GRUPO) ?></div></th>
<?php } ?>
<?php if ($Page->EQUIPO->Visible) { // EQUIPO ?>
        <th data-name="EQUIPO" class="<?= $Page->EQUIPO->headerCellClass() ?>"><div id="elh_pronosticador_EQUIPO" class="pronosticador_EQUIPO"><?= $Page->renderFieldHeader($Page->EQUIPO) ?></div></th>
<?php } ?>
<?php if ($Page->POSICION->Visible) { // POSICION ?>
        <th data-name="POSICION" class="<?= $Page->POSICION->headerCellClass() ?>"><div id="elh_pronosticador_POSICION" class="pronosticador_POSICION"><?= $Page->renderFieldHeader($Page->POSICION) ?></div></th>
<?php } ?>
<?php if ($Page->NUMERACION->Visible) { // NUMERACION ?>
        <th data-name="NUMERACION" class="<?= $Page->NUMERACION->headerCellClass() ?>"><div id="elh_pronosticador_NUMERACION" class="pronosticador_NUMERACION"><?= $Page->renderFieldHeader($Page->NUMERACION) ?></div></th>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <th data-name="crea_dato" class="<?= $Page->crea_dato->headerCellClass() ?>"><div id="elh_pronosticador_crea_dato" class="pronosticador_crea_dato"><?= $Page->renderFieldHeader($Page->crea_dato) ?></div></th>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <th data-name="modifica_dato" class="<?= $Page->modifica_dato->headerCellClass() ?>"><div id="elh_pronosticador_modifica_dato" class="pronosticador_modifica_dato"><?= $Page->renderFieldHeader($Page->modifica_dato) ?></div></th>
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
            "id" => "r" . $Page->RowCount . "_pronosticador",
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
    <?php if ($Page->ID_ENCUESTA->Visible) { // ID_ENCUESTA ?>
        <td data-name="ID_ENCUESTA"<?= $Page->ID_ENCUESTA->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_pronosticador_ID_ENCUESTA" class="el_pronosticador_ID_ENCUESTA">
<span<?= $Page->ID_ENCUESTA->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->ID_ENCUESTA->getDisplayValue($Page->ID_ENCUESTA->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="pronosticador" data-field="x_ID_ENCUESTA" data-hidden="1" name="x<?= $Page->RowIndex ?>_ID_ENCUESTA" id="x<?= $Page->RowIndex ?>_ID_ENCUESTA" value="<?= HtmlEncode($Page->ID_ENCUESTA->CurrentValue) ?>">
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_pronosticador_ID_ENCUESTA" class="el_pronosticador_ID_ENCUESTA">
<span<?= $Page->ID_ENCUESTA->viewAttributes() ?>>
<?= $Page->ID_ENCUESTA->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } else { ?>
            <input type="hidden" data-table="pronosticador" data-field="x_ID_ENCUESTA" data-hidden="1" name="x<?= $Page->RowIndex ?>_ID_ENCUESTA" id="x<?= $Page->RowIndex ?>_ID_ENCUESTA" value="<?= HtmlEncode($Page->ID_ENCUESTA->CurrentValue) ?>">
    <?php } ?>
    <?php if ($Page->ID_PARTICIPANTE->Visible) { // ID_PARTICIPANTE ?>
        <td data-name="ID_PARTICIPANTE"<?= $Page->ID_PARTICIPANTE->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_pronosticador_ID_PARTICIPANTE" class="el_pronosticador_ID_PARTICIPANTE">
    <select
        id="x<?= $Page->RowIndex ?>_ID_PARTICIPANTE"
        name="x<?= $Page->RowIndex ?>_ID_PARTICIPANTE"
        class="form-select ew-select<?= $Page->ID_PARTICIPANTE->isInvalidClass() ?>"
        data-select2-id="fpronosticadorlist_x<?= $Page->RowIndex ?>_ID_PARTICIPANTE"
        data-table="pronosticador"
        data-field="x_ID_PARTICIPANTE"
        data-value-separator="<?= $Page->ID_PARTICIPANTE->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->ID_PARTICIPANTE->getPlaceHolder()) ?>"
        <?= $Page->ID_PARTICIPANTE->editAttributes() ?>>
        <?= $Page->ID_PARTICIPANTE->selectOptionListHtml("x{$Page->RowIndex}_ID_PARTICIPANTE") ?>
    </select>
    <div class="invalid-feedback"><?= $Page->ID_PARTICIPANTE->getErrorMessage() ?></div>
<?= $Page->ID_PARTICIPANTE->Lookup->getParamTag($Page, "p_x" . $Page->RowIndex . "_ID_PARTICIPANTE") ?>
<script>
loadjs.ready("fpronosticadorlist", function() {
    var options = { name: "x<?= $Page->RowIndex ?>_ID_PARTICIPANTE", selectId: "fpronosticadorlist_x<?= $Page->RowIndex ?>_ID_PARTICIPANTE" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpronosticadorlist.lists.ID_PARTICIPANTE.lookupOptions.length) {
        options.data = { id: "x<?= $Page->RowIndex ?>_ID_PARTICIPANTE", form: "fpronosticadorlist" };
    } else {
        options.ajax = { id: "x<?= $Page->RowIndex ?>_ID_PARTICIPANTE", form: "fpronosticadorlist", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.pronosticador.fields.ID_PARTICIPANTE.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_pronosticador_ID_PARTICIPANTE" class="el_pronosticador_ID_PARTICIPANTE">
<span<?= $Page->ID_PARTICIPANTE->viewAttributes() ?>>
<?= $Page->ID_PARTICIPANTE->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->GRUPO->Visible) { // GRUPO ?>
        <td data-name="GRUPO"<?= $Page->GRUPO->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_pronosticador_GRUPO" class="el_pronosticador_GRUPO">
    <select
        id="x<?= $Page->RowIndex ?>_GRUPO"
        name="x<?= $Page->RowIndex ?>_GRUPO"
        class="form-select ew-select<?= $Page->GRUPO->isInvalidClass() ?>"
        data-select2-id="fpronosticadorlist_x<?= $Page->RowIndex ?>_GRUPO"
        data-table="pronosticador"
        data-field="x_GRUPO"
        data-value-separator="<?= $Page->GRUPO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->GRUPO->getPlaceHolder()) ?>"
        <?= $Page->GRUPO->editAttributes() ?>>
        <?= $Page->GRUPO->selectOptionListHtml("x{$Page->RowIndex}_GRUPO") ?>
    </select>
    <div class="invalid-feedback"><?= $Page->GRUPO->getErrorMessage() ?></div>
<script>
loadjs.ready("fpronosticadorlist", function() {
    var options = { name: "x<?= $Page->RowIndex ?>_GRUPO", selectId: "fpronosticadorlist_x<?= $Page->RowIndex ?>_GRUPO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpronosticadorlist.lists.GRUPO.lookupOptions.length) {
        options.data = { id: "x<?= $Page->RowIndex ?>_GRUPO", form: "fpronosticadorlist" };
    } else {
        options.ajax = { id: "x<?= $Page->RowIndex ?>_GRUPO", form: "fpronosticadorlist", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.pronosticador.fields.GRUPO.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_pronosticador_GRUPO" class="el_pronosticador_GRUPO">
<span<?= $Page->GRUPO->viewAttributes() ?>>
<?= $Page->GRUPO->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->EQUIPO->Visible) { // EQUIPO ?>
        <td data-name="EQUIPO"<?= $Page->EQUIPO->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_pronosticador_EQUIPO" class="el_pronosticador_EQUIPO">
    <select
        id="x<?= $Page->RowIndex ?>_EQUIPO"
        name="x<?= $Page->RowIndex ?>_EQUIPO"
        class="form-select ew-select<?= $Page->EQUIPO->isInvalidClass() ?>"
        data-select2-id="fpronosticadorlist_x<?= $Page->RowIndex ?>_EQUIPO"
        data-table="pronosticador"
        data-field="x_EQUIPO"
        data-value-separator="<?= $Page->EQUIPO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->EQUIPO->getPlaceHolder()) ?>"
        <?= $Page->EQUIPO->editAttributes() ?>>
        <?= $Page->EQUIPO->selectOptionListHtml("x{$Page->RowIndex}_EQUIPO") ?>
    </select>
    <div class="invalid-feedback"><?= $Page->EQUIPO->getErrorMessage() ?></div>
<?= $Page->EQUIPO->Lookup->getParamTag($Page, "p_x" . $Page->RowIndex . "_EQUIPO") ?>
<script>
loadjs.ready("fpronosticadorlist", function() {
    var options = { name: "x<?= $Page->RowIndex ?>_EQUIPO", selectId: "fpronosticadorlist_x<?= $Page->RowIndex ?>_EQUIPO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpronosticadorlist.lists.EQUIPO.lookupOptions.length) {
        options.data = { id: "x<?= $Page->RowIndex ?>_EQUIPO", form: "fpronosticadorlist" };
    } else {
        options.ajax = { id: "x<?= $Page->RowIndex ?>_EQUIPO", form: "fpronosticadorlist", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.pronosticador.fields.EQUIPO.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_pronosticador_EQUIPO" class="el_pronosticador_EQUIPO">
<span<?= $Page->EQUIPO->viewAttributes() ?>>
<?= $Page->EQUIPO->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->POSICION->Visible) { // POSICION ?>
        <td data-name="POSICION"<?= $Page->POSICION->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_pronosticador_POSICION" class="el_pronosticador_POSICION">
    <select
        id="x<?= $Page->RowIndex ?>_POSICION"
        name="x<?= $Page->RowIndex ?>_POSICION"
        class="form-select ew-select<?= $Page->POSICION->isInvalidClass() ?>"
        data-select2-id="fpronosticadorlist_x<?= $Page->RowIndex ?>_POSICION"
        data-table="pronosticador"
        data-field="x_POSICION"
        data-value-separator="<?= $Page->POSICION->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->POSICION->getPlaceHolder()) ?>"
        <?= $Page->POSICION->editAttributes() ?>>
        <?= $Page->POSICION->selectOptionListHtml("x{$Page->RowIndex}_POSICION") ?>
    </select>
    <div class="invalid-feedback"><?= $Page->POSICION->getErrorMessage() ?></div>
<script>
loadjs.ready("fpronosticadorlist", function() {
    var options = { name: "x<?= $Page->RowIndex ?>_POSICION", selectId: "fpronosticadorlist_x<?= $Page->RowIndex ?>_POSICION" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpronosticadorlist.lists.POSICION.lookupOptions.length) {
        options.data = { id: "x<?= $Page->RowIndex ?>_POSICION", form: "fpronosticadorlist" };
    } else {
        options.ajax = { id: "x<?= $Page->RowIndex ?>_POSICION", form: "fpronosticadorlist", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.pronosticador.fields.POSICION.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_pronosticador_POSICION" class="el_pronosticador_POSICION">
<span<?= $Page->POSICION->viewAttributes() ?>>
<?= $Page->POSICION->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->NUMERACION->Visible) { // NUMERACION ?>
        <td data-name="NUMERACION"<?= $Page->NUMERACION->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_pronosticador_NUMERACION" class="el_pronosticador_NUMERACION">
<input type="<?= $Page->NUMERACION->getInputTextType() ?>" name="x<?= $Page->RowIndex ?>_NUMERACION" id="x<?= $Page->RowIndex ?>_NUMERACION" data-table="pronosticador" data-field="x_NUMERACION" value="<?= $Page->NUMERACION->EditValue ?>" size="30" maxlength="4" placeholder="<?= HtmlEncode($Page->NUMERACION->getPlaceHolder()) ?>"<?= $Page->NUMERACION->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->NUMERACION->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_pronosticador_NUMERACION" class="el_pronosticador_NUMERACION">
<span<?= $Page->NUMERACION->viewAttributes() ?>>
<?= $Page->NUMERACION->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <td data-name="crea_dato"<?= $Page->crea_dato->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_pronosticador_crea_dato" class="el_pronosticador_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->crea_dato->getDisplayValue($Page->crea_dato->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="pronosticador" data-field="x_crea_dato" data-hidden="1" name="x<?= $Page->RowIndex ?>_crea_dato" id="x<?= $Page->RowIndex ?>_crea_dato" value="<?= HtmlEncode($Page->crea_dato->CurrentValue) ?>">
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_pronosticador_crea_dato" class="el_pronosticador_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<?= $Page->crea_dato->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <td data-name="modifica_dato"<?= $Page->modifica_dato->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_pronosticador_modifica_dato" class="el_pronosticador_modifica_dato">
<span<?= $Page->modifica_dato->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->modifica_dato->getDisplayValue($Page->modifica_dato->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="pronosticador" data-field="x_modifica_dato" data-hidden="1" name="x<?= $Page->RowIndex ?>_modifica_dato" id="x<?= $Page->RowIndex ?>_modifica_dato" value="<?= HtmlEncode($Page->modifica_dato->CurrentValue) ?>">
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_pronosticador_modifica_dato" class="el_pronosticador_modifica_dato">
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
loadjs.ready(["fpronosticadorlist","load"], () => fpronosticadorlist.updateLists(<?= $Page->RowIndex ?>));
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
    ew.addEventHandlers("pronosticador");
});
</script>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
<?php } ?>
