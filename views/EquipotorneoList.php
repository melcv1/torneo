<?php

namespace PHPMaker2022\project11;

// Page object
$EquipotorneoList = &$Page;
?>
<?php if (!$Page->isExport()) { ?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { equipotorneo: currentTable } });
var currentForm, currentPageID;
var fequipotorneolist;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fequipotorneolist = new ew.Form("fequipotorneolist", "list");
    currentPageID = ew.PAGE_ID = "list";
    currentForm = fequipotorneolist;
    fequipotorneolist.formKeyCountName = "<?= $Page->FormKeyCountName ?>";

    // Add fields
    var fields = currentTable.fields;
    fequipotorneolist.addFields([
        ["ID_EQUIPO_TORNEO", [fields.ID_EQUIPO_TORNEO.visible && fields.ID_EQUIPO_TORNEO.required ? ew.Validators.required(fields.ID_EQUIPO_TORNEO.caption) : null], fields.ID_EQUIPO_TORNEO.isInvalid],
        ["ID_TORNEO", [fields.ID_TORNEO.visible && fields.ID_TORNEO.required ? ew.Validators.required(fields.ID_TORNEO.caption) : null], fields.ID_TORNEO.isInvalid],
        ["ID_EQUIPO", [fields.ID_EQUIPO.visible && fields.ID_EQUIPO.required ? ew.Validators.required(fields.ID_EQUIPO.caption) : null], fields.ID_EQUIPO.isInvalid],
        ["PARTIDOS_JUGADOS", [fields.PARTIDOS_JUGADOS.visible && fields.PARTIDOS_JUGADOS.required ? ew.Validators.required(fields.PARTIDOS_JUGADOS.caption) : null, ew.Validators.integer], fields.PARTIDOS_JUGADOS.isInvalid],
        ["PARTIDOS_GANADOS", [fields.PARTIDOS_GANADOS.visible && fields.PARTIDOS_GANADOS.required ? ew.Validators.required(fields.PARTIDOS_GANADOS.caption) : null, ew.Validators.integer], fields.PARTIDOS_GANADOS.isInvalid],
        ["PARTIDOS_EMPATADOS", [fields.PARTIDOS_EMPATADOS.visible && fields.PARTIDOS_EMPATADOS.required ? ew.Validators.required(fields.PARTIDOS_EMPATADOS.caption) : null, ew.Validators.integer], fields.PARTIDOS_EMPATADOS.isInvalid],
        ["PARTIDOS_PERDIDOS", [fields.PARTIDOS_PERDIDOS.visible && fields.PARTIDOS_PERDIDOS.required ? ew.Validators.required(fields.PARTIDOS_PERDIDOS.caption) : null, ew.Validators.integer], fields.PARTIDOS_PERDIDOS.isInvalid],
        ["GF", [fields.GF.visible && fields.GF.required ? ew.Validators.required(fields.GF.caption) : null, ew.Validators.integer], fields.GF.isInvalid],
        ["GC", [fields.GC.visible && fields.GC.required ? ew.Validators.required(fields.GC.caption) : null, ew.Validators.integer], fields.GC.isInvalid],
        ["GD", [fields.GD.visible && fields.GD.required ? ew.Validators.required(fields.GD.caption) : null, ew.Validators.integer], fields.GD.isInvalid],
        ["GRUPO", [fields.GRUPO.visible && fields.GRUPO.required ? ew.Validators.required(fields.GRUPO.caption) : null], fields.GRUPO.isInvalid],
        ["POSICION_EQUIPO_TORENO", [fields.POSICION_EQUIPO_TORENO.visible && fields.POSICION_EQUIPO_TORENO.required ? ew.Validators.required(fields.POSICION_EQUIPO_TORENO.caption) : null], fields.POSICION_EQUIPO_TORENO.isInvalid],
        ["crea_dato", [fields.crea_dato.visible && fields.crea_dato.required ? ew.Validators.required(fields.crea_dato.caption) : null], fields.crea_dato.isInvalid],
        ["modifica_dato", [fields.modifica_dato.visible && fields.modifica_dato.required ? ew.Validators.required(fields.modifica_dato.caption) : null], fields.modifica_dato.isInvalid],
        ["usuario_dato", [fields.usuario_dato.visible && fields.usuario_dato.required ? ew.Validators.required(fields.usuario_dato.caption) : null], fields.usuario_dato.isInvalid]
    ]);

    // Form_CustomValidate
    fequipotorneolist.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fequipotorneolist.validateRequired = ew.CLIENT_VALIDATE;

    // Dynamic selection lists
    fequipotorneolist.lists.ID_TORNEO = <?= $Page->ID_TORNEO->toClientList($Page) ?>;
    fequipotorneolist.lists.ID_EQUIPO = <?= $Page->ID_EQUIPO->toClientList($Page) ?>;
    fequipotorneolist.lists.GRUPO = <?= $Page->GRUPO->toClientList($Page) ?>;
    fequipotorneolist.lists.POSICION_EQUIPO_TORENO = <?= $Page->POSICION_EQUIPO_TORENO->toClientList($Page) ?>;
    loadjs.done("fequipotorneolist");
});
var fequipotorneosrch, currentSearchForm, currentAdvancedSearchForm;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object for search
    fequipotorneosrch = new ew.Form("fequipotorneosrch", "list");
    currentSearchForm = fequipotorneosrch;

    // Add fields
    var fields = currentTable.fields;
    fequipotorneosrch.addFields([
        ["ID_EQUIPO_TORNEO", [], fields.ID_EQUIPO_TORNEO.isInvalid],
        ["ID_TORNEO", [], fields.ID_TORNEO.isInvalid],
        ["ID_EQUIPO", [], fields.ID_EQUIPO.isInvalid],
        ["PARTIDOS_JUGADOS", [], fields.PARTIDOS_JUGADOS.isInvalid],
        ["PARTIDOS_GANADOS", [], fields.PARTIDOS_GANADOS.isInvalid],
        ["PARTIDOS_EMPATADOS", [], fields.PARTIDOS_EMPATADOS.isInvalid],
        ["PARTIDOS_PERDIDOS", [], fields.PARTIDOS_PERDIDOS.isInvalid],
        ["GF", [], fields.GF.isInvalid],
        ["GC", [], fields.GC.isInvalid],
        ["GD", [], fields.GD.isInvalid],
        ["GRUPO", [], fields.GRUPO.isInvalid],
        ["POSICION_EQUIPO_TORENO", [], fields.POSICION_EQUIPO_TORENO.isInvalid],
        ["crea_dato", [], fields.crea_dato.isInvalid],
        ["modifica_dato", [], fields.modifica_dato.isInvalid],
        ["usuario_dato", [], fields.usuario_dato.isInvalid]
    ]);

    // Validate form
    fequipotorneosrch.validate = function () {
        if (!this.validateRequired)
            return true; // Ignore validation
        var fobj = this.getForm();

        // Validate fields
        if (!this.validateFields())
            return false;

        // Call Form_CustomValidate event
        if (!this.customValidate(fobj)) {
            this.focus();
            return false;
        }
        return true;
    }

    // Form_CustomValidate
    fequipotorneosrch.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fequipotorneosrch.validateRequired = ew.CLIENT_VALIDATE;

    // Dynamic selection lists
    fequipotorneosrch.lists.ID_TORNEO = <?= $Page->ID_TORNEO->toClientList($Page) ?>;

    // Filters
    fequipotorneosrch.filterList = <?= $Page->getFilterList() ?>;
    loadjs.done("fequipotorneosrch");
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
<form name="fequipotorneosrch" id="fequipotorneosrch" class="ew-form ew-ext-search-form" action="<?= CurrentPageUrl(false) ?>">
<div id="fequipotorneosrch_search_panel" class="mb-2 mb-sm-0 <?= $Page->SearchPanelClass ?>"><!-- .ew-search-panel -->
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="equipotorneo">
<div class="ew-extended-search container-fluid">
<div class="row mb-0<?= ($Page->SearchFieldsPerRow > 0) ? " row-cols-sm-" . $Page->SearchFieldsPerRow : "" ?>">
<?php
// Render search row
$Page->RowType = ROWTYPE_SEARCH;
$Page->resetAttributes();
$Page->renderRow();
?>
<?php if ($Page->ID_TORNEO->Visible) { // ID_TORNEO ?>
<?php
if (!$Page->ID_TORNEO->UseFilter) {
    $Page->SearchColumnCount++;
}
?>
    <div id="xs_ID_TORNEO" class="col-sm-auto d-sm-flex mb-3 px-0 pe-sm-2<?= $Page->ID_TORNEO->UseFilter ? " ew-filter-field" : "" ?>">
        <select
            id="x_ID_TORNEO"
            name="x_ID_TORNEO[]"
            class="form-control ew-select<?= $Page->ID_TORNEO->isInvalidClass() ?>"
            data-select2-id="fequipotorneosrch_x_ID_TORNEO"
            data-table="equipotorneo"
            data-field="x_ID_TORNEO"
            data-caption="<?= HtmlEncode(RemoveHtml($Page->ID_TORNEO->caption())) ?>"
            data-filter="true"
            multiple
            size="1"
            data-value-separator="<?= $Page->ID_TORNEO->displayValueSeparatorAttribute() ?>"
            data-placeholder="<?= HtmlEncode($Page->ID_TORNEO->getPlaceHolder()) ?>"
            <?= $Page->ID_TORNEO->editAttributes() ?>>
            <?= $Page->ID_TORNEO->selectOptionListHtml("x_ID_TORNEO", true) ?>
        </select>
        <div class="invalid-feedback"><?= $Page->ID_TORNEO->getErrorMessage(false) ?></div>
        <?= $Page->ID_TORNEO->Lookup->getParamTag($Page, "p_x_ID_TORNEO") ?>
        <script>
        loadjs.ready("fequipotorneosrch", function() {
            var options = {
                name: "x_ID_TORNEO",
                selectId: "fequipotorneosrch_x_ID_TORNEO",
                ajax: { id: "x_ID_TORNEO", form: "fequipotorneosrch", limit: ew.FILTER_PAGE_SIZE, data: { ajax: "filter" } }
            };
            options = Object.assign({}, ew.filterOptions, options, ew.vars.tables.equipotorneo.fields.ID_TORNEO.filterOptions);
            ew.createFilter(options);
        });
        </script>
    </div><!-- /.col-sm-auto -->
<?php } ?>
</div><!-- /.row -->
<div class="row mb-0">
    <div class="col-sm-auto px-0 pe-sm-2">
        <div class="ew-basic-search input-group">
            <input type="search" name="<?= Config("TABLE_BASIC_SEARCH") ?>" id="<?= Config("TABLE_BASIC_SEARCH") ?>" class="form-control ew-basic-search-keyword" value="<?= HtmlEncode($Page->BasicSearch->getKeyword()) ?>" placeholder="<?= HtmlEncode($Language->phrase("Search")) ?>" aria-label="<?= HtmlEncode($Language->phrase("Search")) ?>">
            <input type="hidden" name="<?= Config("TABLE_BASIC_SEARCH_TYPE") ?>" id="<?= Config("TABLE_BASIC_SEARCH_TYPE") ?>" class="ew-basic-search-type" value="<?= HtmlEncode($Page->BasicSearch->getType()) ?>">
            <button type="button" data-bs-toggle="dropdown" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" aria-haspopup="true" aria-expanded="false">
                <span id="searchtype"><?= $Page->BasicSearch->getTypeNameShort() ?></span>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "" ? " active" : "" ?>" form="fequipotorneosrch" data-ew-action="search-type"><?= $Language->phrase("QuickSearchAuto") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "=" ? " active" : "" ?>" form="fequipotorneosrch" data-ew-action="search-type" data-search-type="="><?= $Language->phrase("QuickSearchExact") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "AND" ? " active" : "" ?>" form="fequipotorneosrch" data-ew-action="search-type" data-search-type="AND"><?= $Language->phrase("QuickSearchAll") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "OR" ? " active" : "" ?>" form="fequipotorneosrch" data-ew-action="search-type" data-search-type="OR"><?= $Language->phrase("QuickSearchAny") ?></button>
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
<div class="card ew-card ew-grid<?php if ($Page->isAddOrEdit()) { ?> ew-grid-add-edit<?php } ?> equipotorneo">
<form name="fequipotorneolist" id="fequipotorneolist" class="ew-form ew-list-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="equipotorneo">
<div id="gmp_equipotorneo" class="<?= ResponsiveTableClass() ?>card-body ew-grid-middle-panel">
<?php if ($Page->TotalRecords > 0 || $Page->isGridEdit()) { ?>
<table id="tbl_equipotorneolist" class="table table-bordered table-hover table-sm ew-table"><!-- .ew-table -->
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
<?php if ($Page->ID_EQUIPO_TORNEO->Visible) { // ID_EQUIPO_TORNEO ?>
        <th data-name="ID_EQUIPO_TORNEO" class="<?= $Page->ID_EQUIPO_TORNEO->headerCellClass() ?>"><div id="elh_equipotorneo_ID_EQUIPO_TORNEO" class="equipotorneo_ID_EQUIPO_TORNEO"><?= $Page->renderFieldHeader($Page->ID_EQUIPO_TORNEO) ?></div></th>
<?php } ?>
<?php if ($Page->ID_TORNEO->Visible) { // ID_TORNEO ?>
        <th data-name="ID_TORNEO" class="<?= $Page->ID_TORNEO->headerCellClass() ?>"><div id="elh_equipotorneo_ID_TORNEO" class="equipotorneo_ID_TORNEO"><?= $Page->renderFieldHeader($Page->ID_TORNEO) ?></div></th>
<?php } ?>
<?php if ($Page->ID_EQUIPO->Visible) { // ID_EQUIPO ?>
        <th data-name="ID_EQUIPO" class="<?= $Page->ID_EQUIPO->headerCellClass() ?>"><div id="elh_equipotorneo_ID_EQUIPO" class="equipotorneo_ID_EQUIPO"><?= $Page->renderFieldHeader($Page->ID_EQUIPO) ?></div></th>
<?php } ?>
<?php if ($Page->PARTIDOS_JUGADOS->Visible) { // PARTIDOS_JUGADOS ?>
        <th data-name="PARTIDOS_JUGADOS" class="<?= $Page->PARTIDOS_JUGADOS->headerCellClass() ?>"><div id="elh_equipotorneo_PARTIDOS_JUGADOS" class="equipotorneo_PARTIDOS_JUGADOS"><?= $Page->renderFieldHeader($Page->PARTIDOS_JUGADOS) ?></div></th>
<?php } ?>
<?php if ($Page->PARTIDOS_GANADOS->Visible) { // PARTIDOS_GANADOS ?>
        <th data-name="PARTIDOS_GANADOS" class="<?= $Page->PARTIDOS_GANADOS->headerCellClass() ?>"><div id="elh_equipotorneo_PARTIDOS_GANADOS" class="equipotorneo_PARTIDOS_GANADOS"><?= $Page->renderFieldHeader($Page->PARTIDOS_GANADOS) ?></div></th>
<?php } ?>
<?php if ($Page->PARTIDOS_EMPATADOS->Visible) { // PARTIDOS_EMPATADOS ?>
        <th data-name="PARTIDOS_EMPATADOS" class="<?= $Page->PARTIDOS_EMPATADOS->headerCellClass() ?>"><div id="elh_equipotorneo_PARTIDOS_EMPATADOS" class="equipotorneo_PARTIDOS_EMPATADOS"><?= $Page->renderFieldHeader($Page->PARTIDOS_EMPATADOS) ?></div></th>
<?php } ?>
<?php if ($Page->PARTIDOS_PERDIDOS->Visible) { // PARTIDOS_PERDIDOS ?>
        <th data-name="PARTIDOS_PERDIDOS" class="<?= $Page->PARTIDOS_PERDIDOS->headerCellClass() ?>"><div id="elh_equipotorneo_PARTIDOS_PERDIDOS" class="equipotorneo_PARTIDOS_PERDIDOS"><?= $Page->renderFieldHeader($Page->PARTIDOS_PERDIDOS) ?></div></th>
<?php } ?>
<?php if ($Page->GF->Visible) { // GF ?>
        <th data-name="GF" class="<?= $Page->GF->headerCellClass() ?>"><div id="elh_equipotorneo_GF" class="equipotorneo_GF"><?= $Page->renderFieldHeader($Page->GF) ?></div></th>
<?php } ?>
<?php if ($Page->GC->Visible) { // GC ?>
        <th data-name="GC" class="<?= $Page->GC->headerCellClass() ?>"><div id="elh_equipotorneo_GC" class="equipotorneo_GC"><?= $Page->renderFieldHeader($Page->GC) ?></div></th>
<?php } ?>
<?php if ($Page->GD->Visible) { // GD ?>
        <th data-name="GD" class="<?= $Page->GD->headerCellClass() ?>"><div id="elh_equipotorneo_GD" class="equipotorneo_GD"><?= $Page->renderFieldHeader($Page->GD) ?></div></th>
<?php } ?>
<?php if ($Page->GRUPO->Visible) { // GRUPO ?>
        <th data-name="GRUPO" class="<?= $Page->GRUPO->headerCellClass() ?>"><div id="elh_equipotorneo_GRUPO" class="equipotorneo_GRUPO"><?= $Page->renderFieldHeader($Page->GRUPO) ?></div></th>
<?php } ?>
<?php if ($Page->POSICION_EQUIPO_TORENO->Visible) { // POSICION_EQUIPO_TORENO ?>
        <th data-name="POSICION_EQUIPO_TORENO" class="<?= $Page->POSICION_EQUIPO_TORENO->headerCellClass() ?>"><div id="elh_equipotorneo_POSICION_EQUIPO_TORENO" class="equipotorneo_POSICION_EQUIPO_TORENO"><?= $Page->renderFieldHeader($Page->POSICION_EQUIPO_TORENO) ?></div></th>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <th data-name="crea_dato" class="<?= $Page->crea_dato->headerCellClass() ?>"><div id="elh_equipotorneo_crea_dato" class="equipotorneo_crea_dato"><?= $Page->renderFieldHeader($Page->crea_dato) ?></div></th>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <th data-name="modifica_dato" class="<?= $Page->modifica_dato->headerCellClass() ?>"><div id="elh_equipotorneo_modifica_dato" class="equipotorneo_modifica_dato"><?= $Page->renderFieldHeader($Page->modifica_dato) ?></div></th>
<?php } ?>
<?php if ($Page->usuario_dato->Visible) { // usuario_dato ?>
        <th data-name="usuario_dato" class="<?= $Page->usuario_dato->headerCellClass() ?>"><div id="elh_equipotorneo_usuario_dato" class="equipotorneo_usuario_dato"><?= $Page->renderFieldHeader($Page->usuario_dato) ?></div></th>
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
            "id" => "r" . $Page->RowCount . "_equipotorneo",
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
    <?php if ($Page->ID_EQUIPO_TORNEO->Visible) { // ID_EQUIPO_TORNEO ?>
        <td data-name="ID_EQUIPO_TORNEO"<?= $Page->ID_EQUIPO_TORNEO->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_ID_EQUIPO_TORNEO" class="el_equipotorneo_ID_EQUIPO_TORNEO">
<span<?= $Page->ID_EQUIPO_TORNEO->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->ID_EQUIPO_TORNEO->getDisplayValue($Page->ID_EQUIPO_TORNEO->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="equipotorneo" data-field="x_ID_EQUIPO_TORNEO" data-hidden="1" name="x<?= $Page->RowIndex ?>_ID_EQUIPO_TORNEO" id="x<?= $Page->RowIndex ?>_ID_EQUIPO_TORNEO" value="<?= HtmlEncode($Page->ID_EQUIPO_TORNEO->CurrentValue) ?>">
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_ID_EQUIPO_TORNEO" class="el_equipotorneo_ID_EQUIPO_TORNEO">
<span<?= $Page->ID_EQUIPO_TORNEO->viewAttributes() ?>>
<?= $Page->ID_EQUIPO_TORNEO->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } else { ?>
            <input type="hidden" data-table="equipotorneo" data-field="x_ID_EQUIPO_TORNEO" data-hidden="1" name="x<?= $Page->RowIndex ?>_ID_EQUIPO_TORNEO" id="x<?= $Page->RowIndex ?>_ID_EQUIPO_TORNEO" value="<?= HtmlEncode($Page->ID_EQUIPO_TORNEO->CurrentValue) ?>">
    <?php } ?>
    <?php if ($Page->ID_TORNEO->Visible) { // ID_TORNEO ?>
        <td data-name="ID_TORNEO"<?= $Page->ID_TORNEO->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_ID_TORNEO" class="el_equipotorneo_ID_TORNEO">
    <select
        id="x<?= $Page->RowIndex ?>_ID_TORNEO"
        name="x<?= $Page->RowIndex ?>_ID_TORNEO"
        class="form-select ew-select<?= $Page->ID_TORNEO->isInvalidClass() ?>"
        data-select2-id="fequipotorneolist_x<?= $Page->RowIndex ?>_ID_TORNEO"
        data-table="equipotorneo"
        data-field="x_ID_TORNEO"
        data-value-separator="<?= $Page->ID_TORNEO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->ID_TORNEO->getPlaceHolder()) ?>"
        <?= $Page->ID_TORNEO->editAttributes() ?>>
        <?= $Page->ID_TORNEO->selectOptionListHtml("x{$Page->RowIndex}_ID_TORNEO") ?>
    </select>
    <div class="invalid-feedback"><?= $Page->ID_TORNEO->getErrorMessage() ?></div>
<?= $Page->ID_TORNEO->Lookup->getParamTag($Page, "p_x" . $Page->RowIndex . "_ID_TORNEO") ?>
<script>
loadjs.ready("fequipotorneolist", function() {
    var options = { name: "x<?= $Page->RowIndex ?>_ID_TORNEO", selectId: "fequipotorneolist_x<?= $Page->RowIndex ?>_ID_TORNEO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fequipotorneolist.lists.ID_TORNEO.lookupOptions.length) {
        options.data = { id: "x<?= $Page->RowIndex ?>_ID_TORNEO", form: "fequipotorneolist" };
    } else {
        options.ajax = { id: "x<?= $Page->RowIndex ?>_ID_TORNEO", form: "fequipotorneolist", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.equipotorneo.fields.ID_TORNEO.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_ID_TORNEO" class="el_equipotorneo_ID_TORNEO">
<span<?= $Page->ID_TORNEO->viewAttributes() ?>>
<?= $Page->ID_TORNEO->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->ID_EQUIPO->Visible) { // ID_EQUIPO ?>
        <td data-name="ID_EQUIPO"<?= $Page->ID_EQUIPO->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_ID_EQUIPO" class="el_equipotorneo_ID_EQUIPO">
<div class="input-group flex-nowrap">
    <select
        id="x<?= $Page->RowIndex ?>_ID_EQUIPO"
        name="x<?= $Page->RowIndex ?>_ID_EQUIPO"
        class="form-select ew-select<?= $Page->ID_EQUIPO->isInvalidClass() ?>"
        data-select2-id="fequipotorneolist_x<?= $Page->RowIndex ?>_ID_EQUIPO"
        data-table="equipotorneo"
        data-field="x_ID_EQUIPO"
        data-value-separator="<?= $Page->ID_EQUIPO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->ID_EQUIPO->getPlaceHolder()) ?>"
        <?= $Page->ID_EQUIPO->editAttributes() ?>>
        <?= $Page->ID_EQUIPO->selectOptionListHtml("x{$Page->RowIndex}_ID_EQUIPO") ?>
    </select>
    <button type="button" class="btn btn-default ew-add-opt-btn" id="aol_x<?= $Page->RowIndex ?>_ID_EQUIPO" title="<?= HtmlTitle($Language->phrase("AddLink")) . "&nbsp;" . $Page->ID_EQUIPO->caption() ?>" data-title="<?= $Page->ID_EQUIPO->caption() ?>" data-ew-action="add-option" data-el="x<?= $Page->RowIndex ?>_ID_EQUIPO" data-url="<?= GetUrl("equipoaddopt") ?>"><i class="fas fa-plus ew-icon"></i></button>
</div>
<div class="invalid-feedback"><?= $Page->ID_EQUIPO->getErrorMessage() ?></div>
<?= $Page->ID_EQUIPO->Lookup->getParamTag($Page, "p_x" . $Page->RowIndex . "_ID_EQUIPO") ?>
<script>
loadjs.ready("fequipotorneolist", function() {
    var options = { name: "x<?= $Page->RowIndex ?>_ID_EQUIPO", selectId: "fequipotorneolist_x<?= $Page->RowIndex ?>_ID_EQUIPO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fequipotorneolist.lists.ID_EQUIPO.lookupOptions.length) {
        options.data = { id: "x<?= $Page->RowIndex ?>_ID_EQUIPO", form: "fequipotorneolist" };
    } else {
        options.ajax = { id: "x<?= $Page->RowIndex ?>_ID_EQUIPO", form: "fequipotorneolist", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.equipotorneo.fields.ID_EQUIPO.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_ID_EQUIPO" class="el_equipotorneo_ID_EQUIPO">
<span<?= $Page->ID_EQUIPO->viewAttributes() ?>>
<?= $Page->ID_EQUIPO->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->PARTIDOS_JUGADOS->Visible) { // PARTIDOS_JUGADOS ?>
        <td data-name="PARTIDOS_JUGADOS"<?= $Page->PARTIDOS_JUGADOS->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_PARTIDOS_JUGADOS" class="el_equipotorneo_PARTIDOS_JUGADOS">
<input type="<?= $Page->PARTIDOS_JUGADOS->getInputTextType() ?>" name="x<?= $Page->RowIndex ?>_PARTIDOS_JUGADOS" id="x<?= $Page->RowIndex ?>_PARTIDOS_JUGADOS" data-table="equipotorneo" data-field="x_PARTIDOS_JUGADOS" value="<?= $Page->PARTIDOS_JUGADOS->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->PARTIDOS_JUGADOS->getPlaceHolder()) ?>"<?= $Page->PARTIDOS_JUGADOS->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->PARTIDOS_JUGADOS->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_PARTIDOS_JUGADOS" class="el_equipotorneo_PARTIDOS_JUGADOS">
<span<?= $Page->PARTIDOS_JUGADOS->viewAttributes() ?>>
<?= $Page->PARTIDOS_JUGADOS->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->PARTIDOS_GANADOS->Visible) { // PARTIDOS_GANADOS ?>
        <td data-name="PARTIDOS_GANADOS"<?= $Page->PARTIDOS_GANADOS->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_PARTIDOS_GANADOS" class="el_equipotorneo_PARTIDOS_GANADOS">
<input type="<?= $Page->PARTIDOS_GANADOS->getInputTextType() ?>" name="x<?= $Page->RowIndex ?>_PARTIDOS_GANADOS" id="x<?= $Page->RowIndex ?>_PARTIDOS_GANADOS" data-table="equipotorneo" data-field="x_PARTIDOS_GANADOS" value="<?= $Page->PARTIDOS_GANADOS->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->PARTIDOS_GANADOS->getPlaceHolder()) ?>"<?= $Page->PARTIDOS_GANADOS->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->PARTIDOS_GANADOS->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_PARTIDOS_GANADOS" class="el_equipotorneo_PARTIDOS_GANADOS">
<span<?= $Page->PARTIDOS_GANADOS->viewAttributes() ?>>
<?= $Page->PARTIDOS_GANADOS->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->PARTIDOS_EMPATADOS->Visible) { // PARTIDOS_EMPATADOS ?>
        <td data-name="PARTIDOS_EMPATADOS"<?= $Page->PARTIDOS_EMPATADOS->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_PARTIDOS_EMPATADOS" class="el_equipotorneo_PARTIDOS_EMPATADOS">
<input type="<?= $Page->PARTIDOS_EMPATADOS->getInputTextType() ?>" name="x<?= $Page->RowIndex ?>_PARTIDOS_EMPATADOS" id="x<?= $Page->RowIndex ?>_PARTIDOS_EMPATADOS" data-table="equipotorneo" data-field="x_PARTIDOS_EMPATADOS" value="<?= $Page->PARTIDOS_EMPATADOS->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->PARTIDOS_EMPATADOS->getPlaceHolder()) ?>"<?= $Page->PARTIDOS_EMPATADOS->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->PARTIDOS_EMPATADOS->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_PARTIDOS_EMPATADOS" class="el_equipotorneo_PARTIDOS_EMPATADOS">
<span<?= $Page->PARTIDOS_EMPATADOS->viewAttributes() ?>>
<?= $Page->PARTIDOS_EMPATADOS->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->PARTIDOS_PERDIDOS->Visible) { // PARTIDOS_PERDIDOS ?>
        <td data-name="PARTIDOS_PERDIDOS"<?= $Page->PARTIDOS_PERDIDOS->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_PARTIDOS_PERDIDOS" class="el_equipotorneo_PARTIDOS_PERDIDOS">
<input type="<?= $Page->PARTIDOS_PERDIDOS->getInputTextType() ?>" name="x<?= $Page->RowIndex ?>_PARTIDOS_PERDIDOS" id="x<?= $Page->RowIndex ?>_PARTIDOS_PERDIDOS" data-table="equipotorneo" data-field="x_PARTIDOS_PERDIDOS" value="<?= $Page->PARTIDOS_PERDIDOS->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->PARTIDOS_PERDIDOS->getPlaceHolder()) ?>"<?= $Page->PARTIDOS_PERDIDOS->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->PARTIDOS_PERDIDOS->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_PARTIDOS_PERDIDOS" class="el_equipotorneo_PARTIDOS_PERDIDOS">
<span<?= $Page->PARTIDOS_PERDIDOS->viewAttributes() ?>>
<?= $Page->PARTIDOS_PERDIDOS->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->GF->Visible) { // GF ?>
        <td data-name="GF"<?= $Page->GF->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_GF" class="el_equipotorneo_GF">
<input type="<?= $Page->GF->getInputTextType() ?>" name="x<?= $Page->RowIndex ?>_GF" id="x<?= $Page->RowIndex ?>_GF" data-table="equipotorneo" data-field="x_GF" value="<?= $Page->GF->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->GF->getPlaceHolder()) ?>"<?= $Page->GF->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->GF->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_GF" class="el_equipotorneo_GF">
<span<?= $Page->GF->viewAttributes() ?>>
<?= $Page->GF->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->GC->Visible) { // GC ?>
        <td data-name="GC"<?= $Page->GC->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_GC" class="el_equipotorneo_GC">
<input type="<?= $Page->GC->getInputTextType() ?>" name="x<?= $Page->RowIndex ?>_GC" id="x<?= $Page->RowIndex ?>_GC" data-table="equipotorneo" data-field="x_GC" value="<?= $Page->GC->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->GC->getPlaceHolder()) ?>"<?= $Page->GC->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->GC->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_GC" class="el_equipotorneo_GC">
<span<?= $Page->GC->viewAttributes() ?>>
<?= $Page->GC->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->GD->Visible) { // GD ?>
        <td data-name="GD"<?= $Page->GD->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_GD" class="el_equipotorneo_GD">
<input type="<?= $Page->GD->getInputTextType() ?>" name="x<?= $Page->RowIndex ?>_GD" id="x<?= $Page->RowIndex ?>_GD" data-table="equipotorneo" data-field="x_GD" value="<?= $Page->GD->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->GD->getPlaceHolder()) ?>"<?= $Page->GD->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->GD->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_GD" class="el_equipotorneo_GD">
<span<?= $Page->GD->viewAttributes() ?>>
<?= $Page->GD->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->GRUPO->Visible) { // GRUPO ?>
        <td data-name="GRUPO"<?= $Page->GRUPO->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_GRUPO" class="el_equipotorneo_GRUPO">
    <select
        id="x<?= $Page->RowIndex ?>_GRUPO"
        name="x<?= $Page->RowIndex ?>_GRUPO"
        class="form-select ew-select<?= $Page->GRUPO->isInvalidClass() ?>"
        data-select2-id="fequipotorneolist_x<?= $Page->RowIndex ?>_GRUPO"
        data-table="equipotorneo"
        data-field="x_GRUPO"
        data-value-separator="<?= $Page->GRUPO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->GRUPO->getPlaceHolder()) ?>"
        <?= $Page->GRUPO->editAttributes() ?>>
        <?= $Page->GRUPO->selectOptionListHtml("x{$Page->RowIndex}_GRUPO") ?>
    </select>
    <div class="invalid-feedback"><?= $Page->GRUPO->getErrorMessage() ?></div>
<script>
loadjs.ready("fequipotorneolist", function() {
    var options = { name: "x<?= $Page->RowIndex ?>_GRUPO", selectId: "fequipotorneolist_x<?= $Page->RowIndex ?>_GRUPO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fequipotorneolist.lists.GRUPO.lookupOptions.length) {
        options.data = { id: "x<?= $Page->RowIndex ?>_GRUPO", form: "fequipotorneolist" };
    } else {
        options.ajax = { id: "x<?= $Page->RowIndex ?>_GRUPO", form: "fequipotorneolist", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.equipotorneo.fields.GRUPO.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_GRUPO" class="el_equipotorneo_GRUPO">
<span<?= $Page->GRUPO->viewAttributes() ?>>
<?= $Page->GRUPO->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->POSICION_EQUIPO_TORENO->Visible) { // POSICION_EQUIPO_TORENO ?>
        <td data-name="POSICION_EQUIPO_TORENO"<?= $Page->POSICION_EQUIPO_TORENO->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_POSICION_EQUIPO_TORENO" class="el_equipotorneo_POSICION_EQUIPO_TORENO">
    <select
        id="x<?= $Page->RowIndex ?>_POSICION_EQUIPO_TORENO"
        name="x<?= $Page->RowIndex ?>_POSICION_EQUIPO_TORENO"
        class="form-select ew-select<?= $Page->POSICION_EQUIPO_TORENO->isInvalidClass() ?>"
        data-select2-id="fequipotorneolist_x<?= $Page->RowIndex ?>_POSICION_EQUIPO_TORENO"
        data-table="equipotorneo"
        data-field="x_POSICION_EQUIPO_TORENO"
        data-value-separator="<?= $Page->POSICION_EQUIPO_TORENO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->POSICION_EQUIPO_TORENO->getPlaceHolder()) ?>"
        <?= $Page->POSICION_EQUIPO_TORENO->editAttributes() ?>>
        <?= $Page->POSICION_EQUIPO_TORENO->selectOptionListHtml("x{$Page->RowIndex}_POSICION_EQUIPO_TORENO") ?>
    </select>
    <div class="invalid-feedback"><?= $Page->POSICION_EQUIPO_TORENO->getErrorMessage() ?></div>
<script>
loadjs.ready("fequipotorneolist", function() {
    var options = { name: "x<?= $Page->RowIndex ?>_POSICION_EQUIPO_TORENO", selectId: "fequipotorneolist_x<?= $Page->RowIndex ?>_POSICION_EQUIPO_TORENO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fequipotorneolist.lists.POSICION_EQUIPO_TORENO.lookupOptions.length) {
        options.data = { id: "x<?= $Page->RowIndex ?>_POSICION_EQUIPO_TORENO", form: "fequipotorneolist" };
    } else {
        options.ajax = { id: "x<?= $Page->RowIndex ?>_POSICION_EQUIPO_TORENO", form: "fequipotorneolist", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.equipotorneo.fields.POSICION_EQUIPO_TORENO.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_POSICION_EQUIPO_TORENO" class="el_equipotorneo_POSICION_EQUIPO_TORENO">
<span<?= $Page->POSICION_EQUIPO_TORENO->viewAttributes() ?>>
<?= $Page->POSICION_EQUIPO_TORENO->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <td data-name="crea_dato"<?= $Page->crea_dato->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_crea_dato" class="el_equipotorneo_crea_dato">
<input type="hidden" data-table="equipotorneo" data-field="x_crea_dato" data-hidden="1" name="x<?= $Page->RowIndex ?>_crea_dato" id="x<?= $Page->RowIndex ?>_crea_dato" value="<?= HtmlEncode($Page->crea_dato->CurrentValue) ?>">
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_crea_dato" class="el_equipotorneo_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<?= $Page->crea_dato->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <td data-name="modifica_dato"<?= $Page->modifica_dato->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_modifica_dato" class="el_equipotorneo_modifica_dato">
<input type="hidden" data-table="equipotorneo" data-field="x_modifica_dato" data-hidden="1" name="x<?= $Page->RowIndex ?>_modifica_dato" id="x<?= $Page->RowIndex ?>_modifica_dato" value="<?= HtmlEncode($Page->modifica_dato->CurrentValue) ?>">
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_modifica_dato" class="el_equipotorneo_modifica_dato">
<span<?= $Page->modifica_dato->viewAttributes() ?>>
<?= $Page->modifica_dato->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->usuario_dato->Visible) { // usuario_dato ?>
        <td data-name="usuario_dato"<?= $Page->usuario_dato->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_equipotorneo_usuario_dato" class="el_equipotorneo_usuario_dato">
<span<?= $Page->usuario_dato->viewAttributes() ?>>
<?= $Page->usuario_dato->getViewValue() ?></span>
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
loadjs.ready(["fequipotorneolist","load"], () => fequipotorneolist.updateLists(<?= $Page->RowIndex ?>));
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
    ew.addEventHandlers("equipotorneo");
});
</script>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
<?php } ?>
