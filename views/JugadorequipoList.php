<?php

namespace PHPMaker2022\project11;

// Page object
$JugadorequipoList = &$Page;
?>
<?php if (!$Page->isExport()) { ?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { jugadorequipo: currentTable } });
var currentForm, currentPageID;
var fjugadorequipolist;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fjugadorequipolist = new ew.Form("fjugadorequipolist", "list");
    currentPageID = ew.PAGE_ID = "list";
    currentForm = fjugadorequipolist;
    fjugadorequipolist.formKeyCountName = "<?= $Page->FormKeyCountName ?>";

    // Add fields
    var fields = currentTable.fields;
    fjugadorequipolist.addFields([
        ["id_jugadorequipo", [fields.id_jugadorequipo.visible && fields.id_jugadorequipo.required ? ew.Validators.required(fields.id_jugadorequipo.caption) : null], fields.id_jugadorequipo.isInvalid],
        ["id_equipo", [fields.id_equipo.visible && fields.id_equipo.required ? ew.Validators.required(fields.id_equipo.caption) : null], fields.id_equipo.isInvalid],
        ["id_jugador", [fields.id_jugador.visible && fields.id_jugador.required ? ew.Validators.required(fields.id_jugador.caption) : null], fields.id_jugador.isInvalid],
        ["crea_dato", [fields.crea_dato.visible && fields.crea_dato.required ? ew.Validators.required(fields.crea_dato.caption) : null, ew.Validators.datetime(fields.crea_dato.clientFormatPattern)], fields.crea_dato.isInvalid],
        ["modifica_dato", [fields.modifica_dato.visible && fields.modifica_dato.required ? ew.Validators.required(fields.modifica_dato.caption) : null, ew.Validators.datetime(fields.modifica_dato.clientFormatPattern)], fields.modifica_dato.isInvalid]
    ]);

    // Form_CustomValidate
    fjugadorequipolist.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fjugadorequipolist.validateRequired = ew.CLIENT_VALIDATE;

    // Dynamic selection lists
    fjugadorequipolist.lists.id_equipo = <?= $Page->id_equipo->toClientList($Page) ?>;
    fjugadorequipolist.lists.id_jugador = <?= $Page->id_jugador->toClientList($Page) ?>;
    loadjs.done("fjugadorequipolist");
});
var fjugadorequiposrch, currentSearchForm, currentAdvancedSearchForm;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object for search
    fjugadorequiposrch = new ew.Form("fjugadorequiposrch", "list");
    currentSearchForm = fjugadorequiposrch;

    // Dynamic selection lists

    // Filters
    fjugadorequiposrch.filterList = <?= $Page->getFilterList() ?>;
    loadjs.done("fjugadorequiposrch");
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
<form name="fjugadorequiposrch" id="fjugadorequiposrch" class="ew-form ew-ext-search-form" action="<?= CurrentPageUrl(false) ?>">
<div id="fjugadorequiposrch_search_panel" class="mb-2 mb-sm-0 <?= $Page->SearchPanelClass ?>"><!-- .ew-search-panel -->
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="jugadorequipo">
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
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "" ? " active" : "" ?>" form="fjugadorequiposrch" data-ew-action="search-type"><?= $Language->phrase("QuickSearchAuto") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "=" ? " active" : "" ?>" form="fjugadorequiposrch" data-ew-action="search-type" data-search-type="="><?= $Language->phrase("QuickSearchExact") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "AND" ? " active" : "" ?>" form="fjugadorequiposrch" data-ew-action="search-type" data-search-type="AND"><?= $Language->phrase("QuickSearchAll") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "OR" ? " active" : "" ?>" form="fjugadorequiposrch" data-ew-action="search-type" data-search-type="OR"><?= $Language->phrase("QuickSearchAny") ?></button>
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
<div class="card ew-card ew-grid<?php if ($Page->isAddOrEdit()) { ?> ew-grid-add-edit<?php } ?> jugadorequipo">
<form name="fjugadorequipolist" id="fjugadorequipolist" class="ew-form ew-list-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="jugadorequipo">
<div id="gmp_jugadorequipo" class="<?= ResponsiveTableClass() ?>card-body ew-grid-middle-panel">
<?php if ($Page->TotalRecords > 0 || $Page->isGridEdit()) { ?>
<table id="tbl_jugadorequipolist" class="table table-bordered table-hover table-sm ew-table"><!-- .ew-table -->
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
<?php if ($Page->id_jugadorequipo->Visible) { // id_jugadorequipo ?>
        <th data-name="id_jugadorequipo" class="<?= $Page->id_jugadorequipo->headerCellClass() ?>"><div id="elh_jugadorequipo_id_jugadorequipo" class="jugadorequipo_id_jugadorequipo"><?= $Page->renderFieldHeader($Page->id_jugadorequipo) ?></div></th>
<?php } ?>
<?php if ($Page->id_equipo->Visible) { // id_equipo ?>
        <th data-name="id_equipo" class="<?= $Page->id_equipo->headerCellClass() ?>"><div id="elh_jugadorequipo_id_equipo" class="jugadorequipo_id_equipo"><?= $Page->renderFieldHeader($Page->id_equipo) ?></div></th>
<?php } ?>
<?php if ($Page->id_jugador->Visible) { // id_jugador ?>
        <th data-name="id_jugador" class="<?= $Page->id_jugador->headerCellClass() ?>"><div id="elh_jugadorequipo_id_jugador" class="jugadorequipo_id_jugador"><?= $Page->renderFieldHeader($Page->id_jugador) ?></div></th>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <th data-name="crea_dato" class="<?= $Page->crea_dato->headerCellClass() ?>"><div id="elh_jugadorequipo_crea_dato" class="jugadorequipo_crea_dato"><?= $Page->renderFieldHeader($Page->crea_dato) ?></div></th>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <th data-name="modifica_dato" class="<?= $Page->modifica_dato->headerCellClass() ?>"><div id="elh_jugadorequipo_modifica_dato" class="jugadorequipo_modifica_dato"><?= $Page->renderFieldHeader($Page->modifica_dato) ?></div></th>
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
            "id" => "r" . $Page->RowCount . "_jugadorequipo",
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
    <?php if ($Page->id_jugadorequipo->Visible) { // id_jugadorequipo ?>
        <td data-name="id_jugadorequipo"<?= $Page->id_jugadorequipo->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_jugadorequipo_id_jugadorequipo" class="el_jugadorequipo_id_jugadorequipo">
<span<?= $Page->id_jugadorequipo->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->id_jugadorequipo->getDisplayValue($Page->id_jugadorequipo->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="jugadorequipo" data-field="x_id_jugadorequipo" data-hidden="1" name="x<?= $Page->RowIndex ?>_id_jugadorequipo" id="x<?= $Page->RowIndex ?>_id_jugadorequipo" value="<?= HtmlEncode($Page->id_jugadorequipo->CurrentValue) ?>">
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_jugadorequipo_id_jugadorequipo" class="el_jugadorequipo_id_jugadorequipo">
<span<?= $Page->id_jugadorequipo->viewAttributes() ?>>
<?= $Page->id_jugadorequipo->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } else { ?>
            <input type="hidden" data-table="jugadorequipo" data-field="x_id_jugadorequipo" data-hidden="1" name="x<?= $Page->RowIndex ?>_id_jugadorequipo" id="x<?= $Page->RowIndex ?>_id_jugadorequipo" value="<?= HtmlEncode($Page->id_jugadorequipo->CurrentValue) ?>">
    <?php } ?>
    <?php if ($Page->id_equipo->Visible) { // id_equipo ?>
        <td data-name="id_equipo"<?= $Page->id_equipo->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_jugadorequipo_id_equipo" class="el_jugadorequipo_id_equipo">
    <select
        id="x<?= $Page->RowIndex ?>_id_equipo"
        name="x<?= $Page->RowIndex ?>_id_equipo"
        class="form-select ew-select<?= $Page->id_equipo->isInvalidClass() ?>"
        data-select2-id="fjugadorequipolist_x<?= $Page->RowIndex ?>_id_equipo"
        data-table="jugadorequipo"
        data-field="x_id_equipo"
        data-value-separator="<?= $Page->id_equipo->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->id_equipo->getPlaceHolder()) ?>"
        <?= $Page->id_equipo->editAttributes() ?>>
        <?= $Page->id_equipo->selectOptionListHtml("x{$Page->RowIndex}_id_equipo") ?>
    </select>
    <div class="invalid-feedback"><?= $Page->id_equipo->getErrorMessage() ?></div>
<?= $Page->id_equipo->Lookup->getParamTag($Page, "p_x" . $Page->RowIndex . "_id_equipo") ?>
<script>
loadjs.ready("fjugadorequipolist", function() {
    var options = { name: "x<?= $Page->RowIndex ?>_id_equipo", selectId: "fjugadorequipolist_x<?= $Page->RowIndex ?>_id_equipo" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fjugadorequipolist.lists.id_equipo.lookupOptions.length) {
        options.data = { id: "x<?= $Page->RowIndex ?>_id_equipo", form: "fjugadorequipolist" };
    } else {
        options.ajax = { id: "x<?= $Page->RowIndex ?>_id_equipo", form: "fjugadorequipolist", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.jugadorequipo.fields.id_equipo.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_jugadorequipo_id_equipo" class="el_jugadorequipo_id_equipo">
<span<?= $Page->id_equipo->viewAttributes() ?>>
<?= $Page->id_equipo->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->id_jugador->Visible) { // id_jugador ?>
        <td data-name="id_jugador"<?= $Page->id_jugador->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_jugadorequipo_id_jugador" class="el_jugadorequipo_id_jugador">
    <select
        id="x<?= $Page->RowIndex ?>_id_jugador"
        name="x<?= $Page->RowIndex ?>_id_jugador"
        class="form-select ew-select<?= $Page->id_jugador->isInvalidClass() ?>"
        data-select2-id="fjugadorequipolist_x<?= $Page->RowIndex ?>_id_jugador"
        data-table="jugadorequipo"
        data-field="x_id_jugador"
        data-value-separator="<?= $Page->id_jugador->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->id_jugador->getPlaceHolder()) ?>"
        <?= $Page->id_jugador->editAttributes() ?>>
        <?= $Page->id_jugador->selectOptionListHtml("x{$Page->RowIndex}_id_jugador") ?>
    </select>
    <div class="invalid-feedback"><?= $Page->id_jugador->getErrorMessage() ?></div>
<?= $Page->id_jugador->Lookup->getParamTag($Page, "p_x" . $Page->RowIndex . "_id_jugador") ?>
<script>
loadjs.ready("fjugadorequipolist", function() {
    var options = { name: "x<?= $Page->RowIndex ?>_id_jugador", selectId: "fjugadorequipolist_x<?= $Page->RowIndex ?>_id_jugador" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fjugadorequipolist.lists.id_jugador.lookupOptions.length) {
        options.data = { id: "x<?= $Page->RowIndex ?>_id_jugador", form: "fjugadorequipolist" };
    } else {
        options.ajax = { id: "x<?= $Page->RowIndex ?>_id_jugador", form: "fjugadorequipolist", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.jugadorequipo.fields.id_jugador.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_jugadorequipo_id_jugador" class="el_jugadorequipo_id_jugador">
<span<?= $Page->id_jugador->viewAttributes() ?>>
<?= $Page->id_jugador->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <td data-name="crea_dato"<?= $Page->crea_dato->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_jugadorequipo_crea_dato" class="el_jugadorequipo_crea_dato">
<input type="<?= $Page->crea_dato->getInputTextType() ?>" name="x<?= $Page->RowIndex ?>_crea_dato" id="x<?= $Page->RowIndex ?>_crea_dato" data-table="jugadorequipo" data-field="x_crea_dato" value="<?= $Page->crea_dato->EditValue ?>" placeholder="<?= HtmlEncode($Page->crea_dato->getPlaceHolder()) ?>"<?= $Page->crea_dato->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->crea_dato->getErrorMessage() ?></div>
<?php if (!$Page->crea_dato->ReadOnly && !$Page->crea_dato->Disabled && !isset($Page->crea_dato->EditAttrs["readonly"]) && !isset($Page->crea_dato->EditAttrs["disabled"])) { ?>
<script>
loadjs.ready(["fjugadorequipolist", "datetimepicker"], function () {
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
    ew.createDateTimePicker("fjugadorequipolist", "x<?= $Page->RowIndex ?>_crea_dato", ew.deepAssign({"useCurrent":false}, options));
});
</script>
<?php } ?>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_jugadorequipo_crea_dato" class="el_jugadorequipo_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<?= $Page->crea_dato->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <td data-name="modifica_dato"<?= $Page->modifica_dato->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_jugadorequipo_modifica_dato" class="el_jugadorequipo_modifica_dato">
<input type="<?= $Page->modifica_dato->getInputTextType() ?>" name="x<?= $Page->RowIndex ?>_modifica_dato" id="x<?= $Page->RowIndex ?>_modifica_dato" data-table="jugadorequipo" data-field="x_modifica_dato" value="<?= $Page->modifica_dato->EditValue ?>" placeholder="<?= HtmlEncode($Page->modifica_dato->getPlaceHolder()) ?>"<?= $Page->modifica_dato->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->modifica_dato->getErrorMessage() ?></div>
<?php if (!$Page->modifica_dato->ReadOnly && !$Page->modifica_dato->Disabled && !isset($Page->modifica_dato->EditAttrs["readonly"]) && !isset($Page->modifica_dato->EditAttrs["disabled"])) { ?>
<script>
loadjs.ready(["fjugadorequipolist", "datetimepicker"], function () {
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
    ew.createDateTimePicker("fjugadorequipolist", "x<?= $Page->RowIndex ?>_modifica_dato", ew.deepAssign({"useCurrent":false}, options));
});
</script>
<?php } ?>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_jugadorequipo_modifica_dato" class="el_jugadorequipo_modifica_dato">
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
loadjs.ready(["fjugadorequipolist","load"], () => fjugadorequipolist.updateLists(<?= $Page->RowIndex ?>));
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
    ew.addEventHandlers("jugadorequipo");
});
</script>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
<?php } ?>
