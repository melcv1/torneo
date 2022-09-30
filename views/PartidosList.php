<?php

namespace PHPMaker2022\project11;

// Page object
$PartidosList = &$Page;
?>
<?php if (!$Page->isExport()) { ?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { partidos: currentTable } });
var currentForm, currentPageID;
var fpartidoslist;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fpartidoslist = new ew.Form("fpartidoslist", "list");
    currentPageID = ew.PAGE_ID = "list";
    currentForm = fpartidoslist;
    fpartidoslist.formKeyCountName = "<?= $Page->FormKeyCountName ?>";

    // Add fields
    var fields = currentTable.fields;
    fpartidoslist.addFields([
        ["ID_TORNEO", [fields.ID_TORNEO.visible && fields.ID_TORNEO.required ? ew.Validators.required(fields.ID_TORNEO.caption) : null], fields.ID_TORNEO.isInvalid],
        ["equipo_local", [fields.equipo_local.visible && fields.equipo_local.required ? ew.Validators.required(fields.equipo_local.caption) : null], fields.equipo_local.isInvalid],
        ["equipo_visitante", [fields.equipo_visitante.visible && fields.equipo_visitante.required ? ew.Validators.required(fields.equipo_visitante.caption) : null], fields.equipo_visitante.isInvalid],
        ["ID_PARTIDO", [fields.ID_PARTIDO.visible && fields.ID_PARTIDO.required ? ew.Validators.required(fields.ID_PARTIDO.caption) : null], fields.ID_PARTIDO.isInvalid],
        ["FECHA_PARTIDO", [fields.FECHA_PARTIDO.visible && fields.FECHA_PARTIDO.required ? ew.Validators.required(fields.FECHA_PARTIDO.caption) : null, ew.Validators.datetime(fields.FECHA_PARTIDO.clientFormatPattern)], fields.FECHA_PARTIDO.isInvalid],
        ["HORA_PARTIDO", [fields.HORA_PARTIDO.visible && fields.HORA_PARTIDO.required ? ew.Validators.required(fields.HORA_PARTIDO.caption) : null, ew.Validators.time(fields.HORA_PARTIDO.clientFormatPattern)], fields.HORA_PARTIDO.isInvalid],
        ["ESTADIO", [fields.ESTADIO.visible && fields.ESTADIO.required ? ew.Validators.required(fields.ESTADIO.caption) : null], fields.ESTADIO.isInvalid],
        ["CIUDAD_PARTIDO", [fields.CIUDAD_PARTIDO.visible && fields.CIUDAD_PARTIDO.required ? ew.Validators.required(fields.CIUDAD_PARTIDO.caption) : null], fields.CIUDAD_PARTIDO.isInvalid],
        ["PAIS_PARTIDO", [fields.PAIS_PARTIDO.visible && fields.PAIS_PARTIDO.required ? ew.Validators.required(fields.PAIS_PARTIDO.caption) : null], fields.PAIS_PARTIDO.isInvalid],
        ["GOLES_LOCAL", [fields.GOLES_LOCAL.visible && fields.GOLES_LOCAL.required ? ew.Validators.required(fields.GOLES_LOCAL.caption) : null, ew.Validators.integer], fields.GOLES_LOCAL.isInvalid],
        ["GOLES_VISITANTE", [fields.GOLES_VISITANTE.visible && fields.GOLES_VISITANTE.required ? ew.Validators.required(fields.GOLES_VISITANTE.caption) : null, ew.Validators.integer], fields.GOLES_VISITANTE.isInvalid],
        ["GOLES_EXTRA_EQUIPO1", [fields.GOLES_EXTRA_EQUIPO1.visible && fields.GOLES_EXTRA_EQUIPO1.required ? ew.Validators.required(fields.GOLES_EXTRA_EQUIPO1.caption) : null, ew.Validators.integer], fields.GOLES_EXTRA_EQUIPO1.isInvalid],
        ["GOLES_EXTRA_EQUIPO2", [fields.GOLES_EXTRA_EQUIPO2.visible && fields.GOLES_EXTRA_EQUIPO2.required ? ew.Validators.required(fields.GOLES_EXTRA_EQUIPO2.caption) : null, ew.Validators.integer], fields.GOLES_EXTRA_EQUIPO2.isInvalid],
        ["NOTA_PARTIDO", [fields.NOTA_PARTIDO.visible && fields.NOTA_PARTIDO.required ? ew.Validators.required(fields.NOTA_PARTIDO.caption) : null], fields.NOTA_PARTIDO.isInvalid],
        ["RESUMEN_PARTIDO", [fields.RESUMEN_PARTIDO.visible && fields.RESUMEN_PARTIDO.required ? ew.Validators.required(fields.RESUMEN_PARTIDO.caption) : null], fields.RESUMEN_PARTIDO.isInvalid],
        ["ESTADO_PARTIDO", [fields.ESTADO_PARTIDO.visible && fields.ESTADO_PARTIDO.required ? ew.Validators.required(fields.ESTADO_PARTIDO.caption) : null], fields.ESTADO_PARTIDO.isInvalid],
        ["crea_dato", [fields.crea_dato.visible && fields.crea_dato.required ? ew.Validators.required(fields.crea_dato.caption) : null], fields.crea_dato.isInvalid],
        ["modifica_dato", [fields.modifica_dato.visible && fields.modifica_dato.required ? ew.Validators.required(fields.modifica_dato.caption) : null], fields.modifica_dato.isInvalid],
        ["usuario_dato", [fields.usuario_dato.visible && fields.usuario_dato.required ? ew.Validators.required(fields.usuario_dato.caption) : null], fields.usuario_dato.isInvalid],
        ["automatico", [fields.automatico.visible && fields.automatico.required ? ew.Validators.required(fields.automatico.caption) : null], fields.automatico.isInvalid],
        ["actualizado", [fields.actualizado.visible && fields.actualizado.required ? ew.Validators.required(fields.actualizado.caption) : null], fields.actualizado.isInvalid]
    ]);

    // Form_CustomValidate
    fpartidoslist.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fpartidoslist.validateRequired = ew.CLIENT_VALIDATE;

    // Dynamic selection lists
    fpartidoslist.lists.ID_TORNEO = <?= $Page->ID_TORNEO->toClientList($Page) ?>;
    fpartidoslist.lists.equipo_local = <?= $Page->equipo_local->toClientList($Page) ?>;
    fpartidoslist.lists.equipo_visitante = <?= $Page->equipo_visitante->toClientList($Page) ?>;
    fpartidoslist.lists.ESTADIO = <?= $Page->ESTADIO->toClientList($Page) ?>;
    fpartidoslist.lists.PAIS_PARTIDO = <?= $Page->PAIS_PARTIDO->toClientList($Page) ?>;
    fpartidoslist.lists.ESTADO_PARTIDO = <?= $Page->ESTADO_PARTIDO->toClientList($Page) ?>;
    fpartidoslist.lists.automatico = <?= $Page->automatico->toClientList($Page) ?>;
    loadjs.done("fpartidoslist");
});
var fpartidossrch, currentSearchForm, currentAdvancedSearchForm;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object for search
    fpartidossrch = new ew.Form("fpartidossrch", "list");
    currentSearchForm = fpartidossrch;

    // Add fields
    var fields = currentTable.fields;
    fpartidossrch.addFields([
        ["ID_TORNEO", [], fields.ID_TORNEO.isInvalid],
        ["equipo_local", [], fields.equipo_local.isInvalid],
        ["equipo_visitante", [], fields.equipo_visitante.isInvalid],
        ["ID_PARTIDO", [], fields.ID_PARTIDO.isInvalid],
        ["FECHA_PARTIDO", [], fields.FECHA_PARTIDO.isInvalid],
        ["HORA_PARTIDO", [], fields.HORA_PARTIDO.isInvalid],
        ["ESTADIO", [], fields.ESTADIO.isInvalid],
        ["CIUDAD_PARTIDO", [], fields.CIUDAD_PARTIDO.isInvalid],
        ["PAIS_PARTIDO", [], fields.PAIS_PARTIDO.isInvalid],
        ["GOLES_LOCAL", [], fields.GOLES_LOCAL.isInvalid],
        ["GOLES_VISITANTE", [], fields.GOLES_VISITANTE.isInvalid],
        ["GOLES_EXTRA_EQUIPO1", [], fields.GOLES_EXTRA_EQUIPO1.isInvalid],
        ["GOLES_EXTRA_EQUIPO2", [], fields.GOLES_EXTRA_EQUIPO2.isInvalid],
        ["NOTA_PARTIDO", [], fields.NOTA_PARTIDO.isInvalid],
        ["RESUMEN_PARTIDO", [], fields.RESUMEN_PARTIDO.isInvalid],
        ["ESTADO_PARTIDO", [], fields.ESTADO_PARTIDO.isInvalid],
        ["crea_dato", [], fields.crea_dato.isInvalid],
        ["modifica_dato", [], fields.modifica_dato.isInvalid],
        ["usuario_dato", [], fields.usuario_dato.isInvalid],
        ["automatico", [], fields.automatico.isInvalid],
        ["actualizado", [], fields.actualizado.isInvalid]
    ]);

    // Validate form
    fpartidossrch.validate = function () {
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
    fpartidossrch.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fpartidossrch.validateRequired = ew.CLIENT_VALIDATE;

    // Dynamic selection lists
    fpartidossrch.lists.ID_TORNEO = <?= $Page->ID_TORNEO->toClientList($Page) ?>;
    fpartidossrch.lists.ESTADIO = <?= $Page->ESTADIO->toClientList($Page) ?>;

    // Filters
    fpartidossrch.filterList = <?= $Page->getFilterList() ?>;
    loadjs.done("fpartidossrch");
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
<form name="fpartidossrch" id="fpartidossrch" class="ew-form ew-ext-search-form" action="<?= CurrentPageUrl(false) ?>">
<div id="fpartidossrch_search_panel" class="mb-2 mb-sm-0 <?= $Page->SearchPanelClass ?>"><!-- .ew-search-panel -->
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="partidos">
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
            data-select2-id="fpartidossrch_x_ID_TORNEO"
            data-table="partidos"
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
        <script>
        loadjs.ready("fpartidossrch", function() {
            var options = {
                name: "x_ID_TORNEO",
                selectId: "fpartidossrch_x_ID_TORNEO",
                ajax: { id: "x_ID_TORNEO", form: "fpartidossrch", limit: ew.FILTER_PAGE_SIZE, data: { ajax: "filter" } }
            };
            options = Object.assign({}, ew.filterOptions, options, ew.vars.tables.partidos.fields.ID_TORNEO.filterOptions);
            ew.createFilter(options);
        });
        </script>
    </div><!-- /.col-sm-auto -->
<?php } ?>
<?php if ($Page->ESTADIO->Visible) { // ESTADIO ?>
<?php
if (!$Page->ESTADIO->UseFilter) {
    $Page->SearchColumnCount++;
}
?>
    <div id="xs_ESTADIO" class="col-sm-auto d-sm-flex mb-3 px-0 pe-sm-2<?= $Page->ESTADIO->UseFilter ? " ew-filter-field" : "" ?>">
        <select
            id="x_ESTADIO"
            name="x_ESTADIO[]"
            class="form-control ew-select<?= $Page->ESTADIO->isInvalidClass() ?>"
            data-select2-id="fpartidossrch_x_ESTADIO"
            data-table="partidos"
            data-field="x_ESTADIO"
            data-caption="<?= HtmlEncode(RemoveHtml($Page->ESTADIO->caption())) ?>"
            data-filter="true"
            multiple
            size="1"
            data-value-separator="<?= $Page->ESTADIO->displayValueSeparatorAttribute() ?>"
            data-placeholder="<?= HtmlEncode($Page->ESTADIO->getPlaceHolder()) ?>"
            <?= $Page->ESTADIO->editAttributes() ?>>
            <?= $Page->ESTADIO->selectOptionListHtml("x_ESTADIO", true) ?>
        </select>
        <div class="invalid-feedback"><?= $Page->ESTADIO->getErrorMessage(false) ?></div>
        <script>
        loadjs.ready("fpartidossrch", function() {
            var options = {
                name: "x_ESTADIO",
                selectId: "fpartidossrch_x_ESTADIO",
                ajax: { id: "x_ESTADIO", form: "fpartidossrch", limit: ew.FILTER_PAGE_SIZE, data: { ajax: "filter" } }
            };
            options = Object.assign({}, ew.filterOptions, options, ew.vars.tables.partidos.fields.ESTADIO.filterOptions);
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
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "" ? " active" : "" ?>" form="fpartidossrch" data-ew-action="search-type"><?= $Language->phrase("QuickSearchAuto") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "=" ? " active" : "" ?>" form="fpartidossrch" data-ew-action="search-type" data-search-type="="><?= $Language->phrase("QuickSearchExact") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "AND" ? " active" : "" ?>" form="fpartidossrch" data-ew-action="search-type" data-search-type="AND"><?= $Language->phrase("QuickSearchAll") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "OR" ? " active" : "" ?>" form="fpartidossrch" data-ew-action="search-type" data-search-type="OR"><?= $Language->phrase("QuickSearchAny") ?></button>
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
<div class="card ew-card ew-grid<?php if ($Page->isAddOrEdit()) { ?> ew-grid-add-edit<?php } ?> partidos">
<form name="fpartidoslist" id="fpartidoslist" class="ew-form ew-list-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="partidos">
<div id="gmp_partidos" class="<?= ResponsiveTableClass() ?>card-body ew-grid-middle-panel">
<?php if ($Page->TotalRecords > 0 || $Page->isGridEdit()) { ?>
<table id="tbl_partidoslist" class="table table-bordered table-hover table-sm ew-table"><!-- .ew-table -->
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
<?php if ($Page->ID_TORNEO->Visible) { // ID_TORNEO ?>
        <th data-name="ID_TORNEO" class="<?= $Page->ID_TORNEO->headerCellClass() ?>"><div id="elh_partidos_ID_TORNEO" class="partidos_ID_TORNEO"><?= $Page->renderFieldHeader($Page->ID_TORNEO) ?></div></th>
<?php } ?>
<?php if ($Page->equipo_local->Visible) { // equipo_local ?>
        <th data-name="equipo_local" class="<?= $Page->equipo_local->headerCellClass() ?>"><div id="elh_partidos_equipo_local" class="partidos_equipo_local"><?= $Page->renderFieldHeader($Page->equipo_local) ?></div></th>
<?php } ?>
<?php if ($Page->equipo_visitante->Visible) { // equipo_visitante ?>
        <th data-name="equipo_visitante" class="<?= $Page->equipo_visitante->headerCellClass() ?>"><div id="elh_partidos_equipo_visitante" class="partidos_equipo_visitante"><?= $Page->renderFieldHeader($Page->equipo_visitante) ?></div></th>
<?php } ?>
<?php if ($Page->ID_PARTIDO->Visible) { // ID_PARTIDO ?>
        <th data-name="ID_PARTIDO" class="<?= $Page->ID_PARTIDO->headerCellClass() ?>"><div id="elh_partidos_ID_PARTIDO" class="partidos_ID_PARTIDO"><?= $Page->renderFieldHeader($Page->ID_PARTIDO) ?></div></th>
<?php } ?>
<?php if ($Page->FECHA_PARTIDO->Visible) { // FECHA_PARTIDO ?>
        <th data-name="FECHA_PARTIDO" class="<?= $Page->FECHA_PARTIDO->headerCellClass() ?>"><div id="elh_partidos_FECHA_PARTIDO" class="partidos_FECHA_PARTIDO"><?= $Page->renderFieldHeader($Page->FECHA_PARTIDO) ?></div></th>
<?php } ?>
<?php if ($Page->HORA_PARTIDO->Visible) { // HORA_PARTIDO ?>
        <th data-name="HORA_PARTIDO" class="<?= $Page->HORA_PARTIDO->headerCellClass() ?>"><div id="elh_partidos_HORA_PARTIDO" class="partidos_HORA_PARTIDO"><?= $Page->renderFieldHeader($Page->HORA_PARTIDO) ?></div></th>
<?php } ?>
<?php if ($Page->ESTADIO->Visible) { // ESTADIO ?>
        <th data-name="ESTADIO" class="<?= $Page->ESTADIO->headerCellClass() ?>"><div id="elh_partidos_ESTADIO" class="partidos_ESTADIO"><?= $Page->renderFieldHeader($Page->ESTADIO) ?></div></th>
<?php } ?>
<?php if ($Page->CIUDAD_PARTIDO->Visible) { // CIUDAD_PARTIDO ?>
        <th data-name="CIUDAD_PARTIDO" class="<?= $Page->CIUDAD_PARTIDO->headerCellClass() ?>"><div id="elh_partidos_CIUDAD_PARTIDO" class="partidos_CIUDAD_PARTIDO"><?= $Page->renderFieldHeader($Page->CIUDAD_PARTIDO) ?></div></th>
<?php } ?>
<?php if ($Page->PAIS_PARTIDO->Visible) { // PAIS_PARTIDO ?>
        <th data-name="PAIS_PARTIDO" class="<?= $Page->PAIS_PARTIDO->headerCellClass() ?>"><div id="elh_partidos_PAIS_PARTIDO" class="partidos_PAIS_PARTIDO"><?= $Page->renderFieldHeader($Page->PAIS_PARTIDO) ?></div></th>
<?php } ?>
<?php if ($Page->GOLES_LOCAL->Visible) { // GOLES_LOCAL ?>
        <th data-name="GOLES_LOCAL" class="<?= $Page->GOLES_LOCAL->headerCellClass() ?>"><div id="elh_partidos_GOLES_LOCAL" class="partidos_GOLES_LOCAL"><?= $Page->renderFieldHeader($Page->GOLES_LOCAL) ?></div></th>
<?php } ?>
<?php if ($Page->GOLES_VISITANTE->Visible) { // GOLES_VISITANTE ?>
        <th data-name="GOLES_VISITANTE" class="<?= $Page->GOLES_VISITANTE->headerCellClass() ?>"><div id="elh_partidos_GOLES_VISITANTE" class="partidos_GOLES_VISITANTE"><?= $Page->renderFieldHeader($Page->GOLES_VISITANTE) ?></div></th>
<?php } ?>
<?php if ($Page->GOLES_EXTRA_EQUIPO1->Visible) { // GOLES_EXTRA_EQUIPO1 ?>
        <th data-name="GOLES_EXTRA_EQUIPO1" class="<?= $Page->GOLES_EXTRA_EQUIPO1->headerCellClass() ?>"><div id="elh_partidos_GOLES_EXTRA_EQUIPO1" class="partidos_GOLES_EXTRA_EQUIPO1"><?= $Page->renderFieldHeader($Page->GOLES_EXTRA_EQUIPO1) ?></div></th>
<?php } ?>
<?php if ($Page->GOLES_EXTRA_EQUIPO2->Visible) { // GOLES_EXTRA_EQUIPO2 ?>
        <th data-name="GOLES_EXTRA_EQUIPO2" class="<?= $Page->GOLES_EXTRA_EQUIPO2->headerCellClass() ?>"><div id="elh_partidos_GOLES_EXTRA_EQUIPO2" class="partidos_GOLES_EXTRA_EQUIPO2"><?= $Page->renderFieldHeader($Page->GOLES_EXTRA_EQUIPO2) ?></div></th>
<?php } ?>
<?php if ($Page->NOTA_PARTIDO->Visible) { // NOTA_PARTIDO ?>
        <th data-name="NOTA_PARTIDO" class="<?= $Page->NOTA_PARTIDO->headerCellClass() ?>"><div id="elh_partidos_NOTA_PARTIDO" class="partidos_NOTA_PARTIDO"><?= $Page->renderFieldHeader($Page->NOTA_PARTIDO) ?></div></th>
<?php } ?>
<?php if ($Page->RESUMEN_PARTIDO->Visible) { // RESUMEN_PARTIDO ?>
        <th data-name="RESUMEN_PARTIDO" class="<?= $Page->RESUMEN_PARTIDO->headerCellClass() ?>"><div id="elh_partidos_RESUMEN_PARTIDO" class="partidos_RESUMEN_PARTIDO"><?= $Page->renderFieldHeader($Page->RESUMEN_PARTIDO) ?></div></th>
<?php } ?>
<?php if ($Page->ESTADO_PARTIDO->Visible) { // ESTADO_PARTIDO ?>
        <th data-name="ESTADO_PARTIDO" class="<?= $Page->ESTADO_PARTIDO->headerCellClass() ?>"><div id="elh_partidos_ESTADO_PARTIDO" class="partidos_ESTADO_PARTIDO"><?= $Page->renderFieldHeader($Page->ESTADO_PARTIDO) ?></div></th>
<?php } ?>
<?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <th data-name="crea_dato" class="<?= $Page->crea_dato->headerCellClass() ?>"><div id="elh_partidos_crea_dato" class="partidos_crea_dato"><?= $Page->renderFieldHeader($Page->crea_dato) ?></div></th>
<?php } ?>
<?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <th data-name="modifica_dato" class="<?= $Page->modifica_dato->headerCellClass() ?>"><div id="elh_partidos_modifica_dato" class="partidos_modifica_dato"><?= $Page->renderFieldHeader($Page->modifica_dato) ?></div></th>
<?php } ?>
<?php if ($Page->usuario_dato->Visible) { // usuario_dato ?>
        <th data-name="usuario_dato" class="<?= $Page->usuario_dato->headerCellClass() ?>"><div id="elh_partidos_usuario_dato" class="partidos_usuario_dato"><?= $Page->renderFieldHeader($Page->usuario_dato) ?></div></th>
<?php } ?>
<?php if ($Page->automatico->Visible) { // automatico ?>
        <th data-name="automatico" class="<?= $Page->automatico->headerCellClass() ?>"><div id="elh_partidos_automatico" class="partidos_automatico"><?= $Page->renderFieldHeader($Page->automatico) ?></div></th>
<?php } ?>
<?php if ($Page->actualizado->Visible) { // actualizado ?>
        <th data-name="actualizado" class="<?= $Page->actualizado->headerCellClass() ?>"><div id="elh_partidos_actualizado" class="partidos_actualizado"><?= $Page->renderFieldHeader($Page->actualizado) ?></div></th>
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
            "id" => "r" . $Page->RowCount . "_partidos",
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
    <?php if ($Page->ID_TORNEO->Visible) { // ID_TORNEO ?>
        <td data-name="ID_TORNEO"<?= $Page->ID_TORNEO->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_ID_TORNEO" class="el_partidos_ID_TORNEO">
<?php $Page->ID_TORNEO->EditAttrs->prepend("onchange", "ew.updateOptions.call(this);"); ?>
    <select
        id="x<?= $Page->RowIndex ?>_ID_TORNEO"
        name="x<?= $Page->RowIndex ?>_ID_TORNEO"
        class="form-select ew-select<?= $Page->ID_TORNEO->isInvalidClass() ?>"
        data-select2-id="fpartidoslist_x<?= $Page->RowIndex ?>_ID_TORNEO"
        data-table="partidos"
        data-field="x_ID_TORNEO"
        data-value-separator="<?= $Page->ID_TORNEO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->ID_TORNEO->getPlaceHolder()) ?>"
        <?= $Page->ID_TORNEO->editAttributes() ?>>
        <?= $Page->ID_TORNEO->selectOptionListHtml("x{$Page->RowIndex}_ID_TORNEO") ?>
    </select>
    <div class="invalid-feedback"><?= $Page->ID_TORNEO->getErrorMessage() ?></div>
<?= $Page->ID_TORNEO->Lookup->getParamTag($Page, "p_x" . $Page->RowIndex . "_ID_TORNEO") ?>
<script>
loadjs.ready("fpartidoslist", function() {
    var options = { name: "x<?= $Page->RowIndex ?>_ID_TORNEO", selectId: "fpartidoslist_x<?= $Page->RowIndex ?>_ID_TORNEO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpartidoslist.lists.ID_TORNEO.lookupOptions.length) {
        options.data = { id: "x<?= $Page->RowIndex ?>_ID_TORNEO", form: "fpartidoslist" };
    } else {
        options.ajax = { id: "x<?= $Page->RowIndex ?>_ID_TORNEO", form: "fpartidoslist", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.partidos.fields.ID_TORNEO.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_ID_TORNEO" class="el_partidos_ID_TORNEO">
<span<?= $Page->ID_TORNEO->viewAttributes() ?>>
<?= $Page->ID_TORNEO->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->equipo_local->Visible) { // equipo_local ?>
        <td data-name="equipo_local"<?= $Page->equipo_local->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_equipo_local" class="el_partidos_equipo_local">
<?php $Page->equipo_local->EditAttrs->prepend("onchange", "ew.updateOptions.call(this);"); ?>
<div class="input-group flex-nowrap">
    <select
        id="x<?= $Page->RowIndex ?>_equipo_local"
        name="x<?= $Page->RowIndex ?>_equipo_local"
        class="form-select ew-select<?= $Page->equipo_local->isInvalidClass() ?>"
        data-select2-id="fpartidoslist_x<?= $Page->RowIndex ?>_equipo_local"
        data-table="partidos"
        data-field="x_equipo_local"
        data-value-separator="<?= $Page->equipo_local->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->equipo_local->getPlaceHolder()) ?>"
        <?= $Page->equipo_local->editAttributes() ?>>
        <?= $Page->equipo_local->selectOptionListHtml("x{$Page->RowIndex}_equipo_local") ?>
    </select>
    <button type="button" class="btn btn-default ew-add-opt-btn" id="aol_x<?= $Page->RowIndex ?>_equipo_local" title="<?= HtmlTitle($Language->phrase("AddLink")) . "&nbsp;" . $Page->equipo_local->caption() ?>" data-title="<?= $Page->equipo_local->caption() ?>" data-ew-action="add-option" data-el="x<?= $Page->RowIndex ?>_equipo_local" data-url="<?= GetUrl("equipotorneoaddopt") ?>"><i class="fas fa-plus ew-icon"></i></button>
</div>
<div class="invalid-feedback"><?= $Page->equipo_local->getErrorMessage() ?></div>
<?= $Page->equipo_local->Lookup->getParamTag($Page, "p_x" . $Page->RowIndex . "_equipo_local") ?>
<script>
loadjs.ready("fpartidoslist", function() {
    var options = { name: "x<?= $Page->RowIndex ?>_equipo_local", selectId: "fpartidoslist_x<?= $Page->RowIndex ?>_equipo_local" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpartidoslist.lists.equipo_local.lookupOptions.length) {
        options.data = { id: "x<?= $Page->RowIndex ?>_equipo_local", form: "fpartidoslist" };
    } else {
        options.ajax = { id: "x<?= $Page->RowIndex ?>_equipo_local", form: "fpartidoslist", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.partidos.fields.equipo_local.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_equipo_local" class="el_partidos_equipo_local">
<span<?= $Page->equipo_local->viewAttributes() ?>>
<?= $Page->equipo_local->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->equipo_visitante->Visible) { // equipo_visitante ?>
        <td data-name="equipo_visitante"<?= $Page->equipo_visitante->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_equipo_visitante" class="el_partidos_equipo_visitante">
<div class="input-group flex-nowrap">
    <select
        id="x<?= $Page->RowIndex ?>_equipo_visitante"
        name="x<?= $Page->RowIndex ?>_equipo_visitante"
        class="form-select ew-select<?= $Page->equipo_visitante->isInvalidClass() ?>"
        data-select2-id="fpartidoslist_x<?= $Page->RowIndex ?>_equipo_visitante"
        data-table="partidos"
        data-field="x_equipo_visitante"
        data-value-separator="<?= $Page->equipo_visitante->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->equipo_visitante->getPlaceHolder()) ?>"
        <?= $Page->equipo_visitante->editAttributes() ?>>
        <?= $Page->equipo_visitante->selectOptionListHtml("x{$Page->RowIndex}_equipo_visitante") ?>
    </select>
    <button type="button" class="btn btn-default ew-add-opt-btn" id="aol_x<?= $Page->RowIndex ?>_equipo_visitante" title="<?= HtmlTitle($Language->phrase("AddLink")) . "&nbsp;" . $Page->equipo_visitante->caption() ?>" data-title="<?= $Page->equipo_visitante->caption() ?>" data-ew-action="add-option" data-el="x<?= $Page->RowIndex ?>_equipo_visitante" data-url="<?= GetUrl("equipotorneoaddopt") ?>"><i class="fas fa-plus ew-icon"></i></button>
</div>
<div class="invalid-feedback"><?= $Page->equipo_visitante->getErrorMessage() ?></div>
<?= $Page->equipo_visitante->Lookup->getParamTag($Page, "p_x" . $Page->RowIndex . "_equipo_visitante") ?>
<script>
loadjs.ready("fpartidoslist", function() {
    var options = { name: "x<?= $Page->RowIndex ?>_equipo_visitante", selectId: "fpartidoslist_x<?= $Page->RowIndex ?>_equipo_visitante" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpartidoslist.lists.equipo_visitante.lookupOptions.length) {
        options.data = { id: "x<?= $Page->RowIndex ?>_equipo_visitante", form: "fpartidoslist" };
    } else {
        options.ajax = { id: "x<?= $Page->RowIndex ?>_equipo_visitante", form: "fpartidoslist", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.partidos.fields.equipo_visitante.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_equipo_visitante" class="el_partidos_equipo_visitante">
<span<?= $Page->equipo_visitante->viewAttributes() ?>>
<?= $Page->equipo_visitante->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->ID_PARTIDO->Visible) { // ID_PARTIDO ?>
        <td data-name="ID_PARTIDO"<?= $Page->ID_PARTIDO->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_ID_PARTIDO" class="el_partidos_ID_PARTIDO">
<span<?= $Page->ID_PARTIDO->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->ID_PARTIDO->getDisplayValue($Page->ID_PARTIDO->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="partidos" data-field="x_ID_PARTIDO" data-hidden="1" name="x<?= $Page->RowIndex ?>_ID_PARTIDO" id="x<?= $Page->RowIndex ?>_ID_PARTIDO" value="<?= HtmlEncode($Page->ID_PARTIDO->CurrentValue) ?>">
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_ID_PARTIDO" class="el_partidos_ID_PARTIDO">
<span<?= $Page->ID_PARTIDO->viewAttributes() ?>>
<?= $Page->ID_PARTIDO->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } else { ?>
            <input type="hidden" data-table="partidos" data-field="x_ID_PARTIDO" data-hidden="1" name="x<?= $Page->RowIndex ?>_ID_PARTIDO" id="x<?= $Page->RowIndex ?>_ID_PARTIDO" value="<?= HtmlEncode($Page->ID_PARTIDO->CurrentValue) ?>">
    <?php } ?>
    <?php if ($Page->FECHA_PARTIDO->Visible) { // FECHA_PARTIDO ?>
        <td data-name="FECHA_PARTIDO"<?= $Page->FECHA_PARTIDO->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_FECHA_PARTIDO" class="el_partidos_FECHA_PARTIDO">
<input type="<?= $Page->FECHA_PARTIDO->getInputTextType() ?>" name="x<?= $Page->RowIndex ?>_FECHA_PARTIDO" id="x<?= $Page->RowIndex ?>_FECHA_PARTIDO" data-table="partidos" data-field="x_FECHA_PARTIDO" value="<?= $Page->FECHA_PARTIDO->EditValue ?>" placeholder="<?= HtmlEncode($Page->FECHA_PARTIDO->getPlaceHolder()) ?>"<?= $Page->FECHA_PARTIDO->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->FECHA_PARTIDO->getErrorMessage() ?></div>
<?php if (!$Page->FECHA_PARTIDO->ReadOnly && !$Page->FECHA_PARTIDO->Disabled && !isset($Page->FECHA_PARTIDO->EditAttrs["readonly"]) && !isset($Page->FECHA_PARTIDO->EditAttrs["disabled"])) { ?>
<script>
loadjs.ready(["fpartidoslist", "datetimepicker"], function () {
    let format = "<?= DateFormat(14) ?>",
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
    ew.createDateTimePicker("fpartidoslist", "x<?= $Page->RowIndex ?>_FECHA_PARTIDO", ew.deepAssign({"useCurrent":false}, options));
});
</script>
<?php } ?>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_FECHA_PARTIDO" class="el_partidos_FECHA_PARTIDO">
<span<?= $Page->FECHA_PARTIDO->viewAttributes() ?>>
<?= $Page->FECHA_PARTIDO->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->HORA_PARTIDO->Visible) { // HORA_PARTIDO ?>
        <td data-name="HORA_PARTIDO"<?= $Page->HORA_PARTIDO->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_HORA_PARTIDO" class="el_partidos_HORA_PARTIDO">
<input type="<?= $Page->HORA_PARTIDO->getInputTextType() ?>" name="x<?= $Page->RowIndex ?>_HORA_PARTIDO" id="x<?= $Page->RowIndex ?>_HORA_PARTIDO" data-table="partidos" data-field="x_HORA_PARTIDO" value="<?= $Page->HORA_PARTIDO->EditValue ?>" placeholder="<?= HtmlEncode($Page->HORA_PARTIDO->getPlaceHolder()) ?>"<?= $Page->HORA_PARTIDO->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->HORA_PARTIDO->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_HORA_PARTIDO" class="el_partidos_HORA_PARTIDO">
<span<?= $Page->HORA_PARTIDO->viewAttributes() ?>>
<?= $Page->HORA_PARTIDO->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->ESTADIO->Visible) { // ESTADIO ?>
        <td data-name="ESTADIO"<?= $Page->ESTADIO->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_ESTADIO" class="el_partidos_ESTADIO">
<div class="input-group flex-nowrap">
    <select
        id="x<?= $Page->RowIndex ?>_ESTADIO"
        name="x<?= $Page->RowIndex ?>_ESTADIO"
        class="form-select ew-select<?= $Page->ESTADIO->isInvalidClass() ?>"
        data-select2-id="fpartidoslist_x<?= $Page->RowIndex ?>_ESTADIO"
        data-table="partidos"
        data-field="x_ESTADIO"
        data-value-separator="<?= $Page->ESTADIO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->ESTADIO->getPlaceHolder()) ?>"
        <?= $Page->ESTADIO->editAttributes() ?>>
        <?= $Page->ESTADIO->selectOptionListHtml("x{$Page->RowIndex}_ESTADIO") ?>
    </select>
    <button type="button" class="btn btn-default ew-add-opt-btn" id="aol_x<?= $Page->RowIndex ?>_ESTADIO" title="<?= HtmlTitle($Language->phrase("AddLink")) . "&nbsp;" . $Page->ESTADIO->caption() ?>" data-title="<?= $Page->ESTADIO->caption() ?>" data-ew-action="add-option" data-el="x<?= $Page->RowIndex ?>_ESTADIO" data-url="<?= GetUrl("estadioaddopt") ?>"><i class="fas fa-plus ew-icon"></i></button>
</div>
<div class="invalid-feedback"><?= $Page->ESTADIO->getErrorMessage() ?></div>
<?= $Page->ESTADIO->Lookup->getParamTag($Page, "p_x" . $Page->RowIndex . "_ESTADIO") ?>
<script>
loadjs.ready("fpartidoslist", function() {
    var options = { name: "x<?= $Page->RowIndex ?>_ESTADIO", selectId: "fpartidoslist_x<?= $Page->RowIndex ?>_ESTADIO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpartidoslist.lists.ESTADIO.lookupOptions.length) {
        options.data = { id: "x<?= $Page->RowIndex ?>_ESTADIO", form: "fpartidoslist" };
    } else {
        options.ajax = { id: "x<?= $Page->RowIndex ?>_ESTADIO", form: "fpartidoslist", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.partidos.fields.ESTADIO.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_ESTADIO" class="el_partidos_ESTADIO">
<span<?= $Page->ESTADIO->viewAttributes() ?>>
<?= $Page->ESTADIO->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->CIUDAD_PARTIDO->Visible) { // CIUDAD_PARTIDO ?>
        <td data-name="CIUDAD_PARTIDO"<?= $Page->CIUDAD_PARTIDO->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_CIUDAD_PARTIDO" class="el_partidos_CIUDAD_PARTIDO">
<textarea data-table="partidos" data-field="x_CIUDAD_PARTIDO" name="x<?= $Page->RowIndex ?>_CIUDAD_PARTIDO" id="x<?= $Page->RowIndex ?>_CIUDAD_PARTIDO" cols="35" rows="1" placeholder="<?= HtmlEncode($Page->CIUDAD_PARTIDO->getPlaceHolder()) ?>"<?= $Page->CIUDAD_PARTIDO->editAttributes() ?>><?= $Page->CIUDAD_PARTIDO->EditValue ?></textarea>
<div class="invalid-feedback"><?= $Page->CIUDAD_PARTIDO->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_CIUDAD_PARTIDO" class="el_partidos_CIUDAD_PARTIDO">
<span<?= $Page->CIUDAD_PARTIDO->viewAttributes() ?>>
<?= $Page->CIUDAD_PARTIDO->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->PAIS_PARTIDO->Visible) { // PAIS_PARTIDO ?>
        <td data-name="PAIS_PARTIDO"<?= $Page->PAIS_PARTIDO->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_PAIS_PARTIDO" class="el_partidos_PAIS_PARTIDO">
    <select
        id="x<?= $Page->RowIndex ?>_PAIS_PARTIDO"
        name="x<?= $Page->RowIndex ?>_PAIS_PARTIDO"
        class="form-select ew-select<?= $Page->PAIS_PARTIDO->isInvalidClass() ?>"
        data-select2-id="fpartidoslist_x<?= $Page->RowIndex ?>_PAIS_PARTIDO"
        data-table="partidos"
        data-field="x_PAIS_PARTIDO"
        data-value-separator="<?= $Page->PAIS_PARTIDO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->PAIS_PARTIDO->getPlaceHolder()) ?>"
        <?= $Page->PAIS_PARTIDO->editAttributes() ?>>
        <?= $Page->PAIS_PARTIDO->selectOptionListHtml("x{$Page->RowIndex}_PAIS_PARTIDO") ?>
    </select>
    <div class="invalid-feedback"><?= $Page->PAIS_PARTIDO->getErrorMessage() ?></div>
<?= $Page->PAIS_PARTIDO->Lookup->getParamTag($Page, "p_x" . $Page->RowIndex . "_PAIS_PARTIDO") ?>
<script>
loadjs.ready("fpartidoslist", function() {
    var options = { name: "x<?= $Page->RowIndex ?>_PAIS_PARTIDO", selectId: "fpartidoslist_x<?= $Page->RowIndex ?>_PAIS_PARTIDO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpartidoslist.lists.PAIS_PARTIDO.lookupOptions.length) {
        options.data = { id: "x<?= $Page->RowIndex ?>_PAIS_PARTIDO", form: "fpartidoslist" };
    } else {
        options.ajax = { id: "x<?= $Page->RowIndex ?>_PAIS_PARTIDO", form: "fpartidoslist", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.partidos.fields.PAIS_PARTIDO.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_PAIS_PARTIDO" class="el_partidos_PAIS_PARTIDO">
<span<?= $Page->PAIS_PARTIDO->viewAttributes() ?>>
<?= $Page->PAIS_PARTIDO->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->GOLES_LOCAL->Visible) { // GOLES_LOCAL ?>
        <td data-name="GOLES_LOCAL"<?= $Page->GOLES_LOCAL->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_GOLES_LOCAL" class="el_partidos_GOLES_LOCAL">
<input type="<?= $Page->GOLES_LOCAL->getInputTextType() ?>" name="x<?= $Page->RowIndex ?>_GOLES_LOCAL" id="x<?= $Page->RowIndex ?>_GOLES_LOCAL" data-table="partidos" data-field="x_GOLES_LOCAL" value="<?= $Page->GOLES_LOCAL->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->GOLES_LOCAL->getPlaceHolder()) ?>"<?= $Page->GOLES_LOCAL->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->GOLES_LOCAL->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_GOLES_LOCAL" class="el_partidos_GOLES_LOCAL">
<span<?= $Page->GOLES_LOCAL->viewAttributes() ?>>
<?= $Page->GOLES_LOCAL->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->GOLES_VISITANTE->Visible) { // GOLES_VISITANTE ?>
        <td data-name="GOLES_VISITANTE"<?= $Page->GOLES_VISITANTE->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_GOLES_VISITANTE" class="el_partidos_GOLES_VISITANTE">
<input type="<?= $Page->GOLES_VISITANTE->getInputTextType() ?>" name="x<?= $Page->RowIndex ?>_GOLES_VISITANTE" id="x<?= $Page->RowIndex ?>_GOLES_VISITANTE" data-table="partidos" data-field="x_GOLES_VISITANTE" value="<?= $Page->GOLES_VISITANTE->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->GOLES_VISITANTE->getPlaceHolder()) ?>"<?= $Page->GOLES_VISITANTE->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->GOLES_VISITANTE->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_GOLES_VISITANTE" class="el_partidos_GOLES_VISITANTE">
<span<?= $Page->GOLES_VISITANTE->viewAttributes() ?>>
<?= $Page->GOLES_VISITANTE->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->GOLES_EXTRA_EQUIPO1->Visible) { // GOLES_EXTRA_EQUIPO1 ?>
        <td data-name="GOLES_EXTRA_EQUIPO1"<?= $Page->GOLES_EXTRA_EQUIPO1->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_GOLES_EXTRA_EQUIPO1" class="el_partidos_GOLES_EXTRA_EQUIPO1">
<input type="<?= $Page->GOLES_EXTRA_EQUIPO1->getInputTextType() ?>" name="x<?= $Page->RowIndex ?>_GOLES_EXTRA_EQUIPO1" id="x<?= $Page->RowIndex ?>_GOLES_EXTRA_EQUIPO1" data-table="partidos" data-field="x_GOLES_EXTRA_EQUIPO1" value="<?= $Page->GOLES_EXTRA_EQUIPO1->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->GOLES_EXTRA_EQUIPO1->getPlaceHolder()) ?>"<?= $Page->GOLES_EXTRA_EQUIPO1->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->GOLES_EXTRA_EQUIPO1->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_GOLES_EXTRA_EQUIPO1" class="el_partidos_GOLES_EXTRA_EQUIPO1">
<span<?= $Page->GOLES_EXTRA_EQUIPO1->viewAttributes() ?>>
<?= $Page->GOLES_EXTRA_EQUIPO1->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->GOLES_EXTRA_EQUIPO2->Visible) { // GOLES_EXTRA_EQUIPO2 ?>
        <td data-name="GOLES_EXTRA_EQUIPO2"<?= $Page->GOLES_EXTRA_EQUIPO2->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_GOLES_EXTRA_EQUIPO2" class="el_partidos_GOLES_EXTRA_EQUIPO2">
<input type="<?= $Page->GOLES_EXTRA_EQUIPO2->getInputTextType() ?>" name="x<?= $Page->RowIndex ?>_GOLES_EXTRA_EQUIPO2" id="x<?= $Page->RowIndex ?>_GOLES_EXTRA_EQUIPO2" data-table="partidos" data-field="x_GOLES_EXTRA_EQUIPO2" value="<?= $Page->GOLES_EXTRA_EQUIPO2->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->GOLES_EXTRA_EQUIPO2->getPlaceHolder()) ?>"<?= $Page->GOLES_EXTRA_EQUIPO2->editAttributes() ?>>
<div class="invalid-feedback"><?= $Page->GOLES_EXTRA_EQUIPO2->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_GOLES_EXTRA_EQUIPO2" class="el_partidos_GOLES_EXTRA_EQUIPO2">
<span<?= $Page->GOLES_EXTRA_EQUIPO2->viewAttributes() ?>>
<?= $Page->GOLES_EXTRA_EQUIPO2->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->NOTA_PARTIDO->Visible) { // NOTA_PARTIDO ?>
        <td data-name="NOTA_PARTIDO"<?= $Page->NOTA_PARTIDO->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_NOTA_PARTIDO" class="el_partidos_NOTA_PARTIDO">
<textarea data-table="partidos" data-field="x_NOTA_PARTIDO" name="x<?= $Page->RowIndex ?>_NOTA_PARTIDO" id="x<?= $Page->RowIndex ?>_NOTA_PARTIDO" cols="35" rows="1" placeholder="<?= HtmlEncode($Page->NOTA_PARTIDO->getPlaceHolder()) ?>"<?= $Page->NOTA_PARTIDO->editAttributes() ?>><?= $Page->NOTA_PARTIDO->EditValue ?></textarea>
<div class="invalid-feedback"><?= $Page->NOTA_PARTIDO->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_NOTA_PARTIDO" class="el_partidos_NOTA_PARTIDO">
<span<?= $Page->NOTA_PARTIDO->viewAttributes() ?>>
<?= $Page->NOTA_PARTIDO->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->RESUMEN_PARTIDO->Visible) { // RESUMEN_PARTIDO ?>
        <td data-name="RESUMEN_PARTIDO"<?= $Page->RESUMEN_PARTIDO->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_RESUMEN_PARTIDO" class="el_partidos_RESUMEN_PARTIDO">
<textarea data-table="partidos" data-field="x_RESUMEN_PARTIDO" name="x<?= $Page->RowIndex ?>_RESUMEN_PARTIDO" id="x<?= $Page->RowIndex ?>_RESUMEN_PARTIDO" cols="35" rows="1" placeholder="<?= HtmlEncode($Page->RESUMEN_PARTIDO->getPlaceHolder()) ?>"<?= $Page->RESUMEN_PARTIDO->editAttributes() ?>><?= $Page->RESUMEN_PARTIDO->EditValue ?></textarea>
<div class="invalid-feedback"><?= $Page->RESUMEN_PARTIDO->getErrorMessage() ?></div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_RESUMEN_PARTIDO" class="el_partidos_RESUMEN_PARTIDO">
<span<?= $Page->RESUMEN_PARTIDO->viewAttributes() ?>>
<?= $Page->RESUMEN_PARTIDO->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->ESTADO_PARTIDO->Visible) { // ESTADO_PARTIDO ?>
        <td data-name="ESTADO_PARTIDO"<?= $Page->ESTADO_PARTIDO->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_ESTADO_PARTIDO" class="el_partidos_ESTADO_PARTIDO">
    <select
        id="x<?= $Page->RowIndex ?>_ESTADO_PARTIDO"
        name="x<?= $Page->RowIndex ?>_ESTADO_PARTIDO"
        class="form-select ew-select<?= $Page->ESTADO_PARTIDO->isInvalidClass() ?>"
        data-select2-id="fpartidoslist_x<?= $Page->RowIndex ?>_ESTADO_PARTIDO"
        data-table="partidos"
        data-field="x_ESTADO_PARTIDO"
        data-value-separator="<?= $Page->ESTADO_PARTIDO->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->ESTADO_PARTIDO->getPlaceHolder()) ?>"
        <?= $Page->ESTADO_PARTIDO->editAttributes() ?>>
        <?= $Page->ESTADO_PARTIDO->selectOptionListHtml("x{$Page->RowIndex}_ESTADO_PARTIDO") ?>
    </select>
    <div class="invalid-feedback"><?= $Page->ESTADO_PARTIDO->getErrorMessage() ?></div>
<script>
loadjs.ready("fpartidoslist", function() {
    var options = { name: "x<?= $Page->RowIndex ?>_ESTADO_PARTIDO", selectId: "fpartidoslist_x<?= $Page->RowIndex ?>_ESTADO_PARTIDO" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fpartidoslist.lists.ESTADO_PARTIDO.lookupOptions.length) {
        options.data = { id: "x<?= $Page->RowIndex ?>_ESTADO_PARTIDO", form: "fpartidoslist" };
    } else {
        options.ajax = { id: "x<?= $Page->RowIndex ?>_ESTADO_PARTIDO", form: "fpartidoslist", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.partidos.fields.ESTADO_PARTIDO.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_ESTADO_PARTIDO" class="el_partidos_ESTADO_PARTIDO">
<span<?= $Page->ESTADO_PARTIDO->viewAttributes() ?>>
<?= $Page->ESTADO_PARTIDO->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->crea_dato->Visible) { // crea_dato ?>
        <td data-name="crea_dato"<?= $Page->crea_dato->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_crea_dato" class="el_partidos_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->crea_dato->getDisplayValue($Page->crea_dato->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="partidos" data-field="x_crea_dato" data-hidden="1" name="x<?= $Page->RowIndex ?>_crea_dato" id="x<?= $Page->RowIndex ?>_crea_dato" value="<?= HtmlEncode($Page->crea_dato->CurrentValue) ?>">
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_crea_dato" class="el_partidos_crea_dato">
<span<?= $Page->crea_dato->viewAttributes() ?>>
<?= $Page->crea_dato->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->modifica_dato->Visible) { // modifica_dato ?>
        <td data-name="modifica_dato"<?= $Page->modifica_dato->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_modifica_dato" class="el_partidos_modifica_dato">
<span<?= $Page->modifica_dato->viewAttributes() ?>>
<input type="text" readonly class="form-control-plaintext" value="<?= HtmlEncode(RemoveHtml($Page->modifica_dato->getDisplayValue($Page->modifica_dato->EditValue))) ?>"></span>
</span>
<input type="hidden" data-table="partidos" data-field="x_modifica_dato" data-hidden="1" name="x<?= $Page->RowIndex ?>_modifica_dato" id="x<?= $Page->RowIndex ?>_modifica_dato" value="<?= HtmlEncode($Page->modifica_dato->CurrentValue) ?>">
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_modifica_dato" class="el_partidos_modifica_dato">
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
<span id="el<?= $Page->RowCount ?>_partidos_usuario_dato" class="el_partidos_usuario_dato">
<span<?= $Page->usuario_dato->viewAttributes() ?>>
<?= $Page->usuario_dato->getViewValue() ?></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->automatico->Visible) { // automatico ?>
        <td data-name="automatico"<?= $Page->automatico->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_automatico" class="el_partidos_automatico">
<div class="form-check d-inline-block">
    <input type="checkbox" class="form-check-input<?= $Page->automatico->isInvalidClass() ?>" data-table="partidos" data-field="x_automatico" name="x<?= $Page->RowIndex ?>_automatico[]" id="x<?= $Page->RowIndex ?>_automatico_345490" value="1"<?= ConvertToBool($Page->automatico->CurrentValue) ? " checked" : "" ?><?= $Page->automatico->editAttributes() ?>>
    <div class="invalid-feedback"><?= $Page->automatico->getErrorMessage() ?></div>
</div>
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_automatico" class="el_partidos_automatico">
<span<?= $Page->automatico->viewAttributes() ?>>
<div class="form-check d-inline-block">
    <input type="checkbox" id="x_automatico_<?= $Page->RowCount ?>" class="form-check-input" value="<?= $Page->automatico->getViewValue() ?>" disabled<?php if (ConvertToBool($Page->automatico->CurrentValue)) { ?> checked<?php } ?>>
    <label class="form-check-label" for="x_automatico_<?= $Page->RowCount ?>"></label>
</div></span>
</span>
<?php } ?>
</td>
    <?php } ?>
    <?php if ($Page->actualizado->Visible) { // actualizado ?>
        <td data-name="actualizado"<?= $Page->actualizado->cellAttributes() ?>>
<?php if ($Page->RowType == ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?= $Page->RowCount ?>_partidos_actualizado" class="el_partidos_actualizado">
<input type="hidden" data-table="partidos" data-field="x_actualizado" data-hidden="1" name="x<?= $Page->RowIndex ?>_actualizado" id="x<?= $Page->RowIndex ?>_actualizado" value="<?= HtmlEncode($Page->actualizado->CurrentValue) ?>">
</span>
<?php } ?>
<?php if ($Page->RowType == ROWTYPE_VIEW) { // View record ?>
<span id="el<?= $Page->RowCount ?>_partidos_actualizado" class="el_partidos_actualizado">
<span<?= $Page->actualizado->viewAttributes() ?>>
<?= $Page->actualizado->getViewValue() ?></span>
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
loadjs.ready(["fpartidoslist","load"], () => fpartidoslist.updateLists(<?= $Page->RowIndex ?>));
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
    ew.addEventHandlers("partidos");
});
</script>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
<?php } ?>
