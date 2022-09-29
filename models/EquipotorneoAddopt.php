<?php

namespace PHPMaker2022\project11;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Page class
 */
class EquipotorneoAddopt extends Equipotorneo
{
    use MessagesTrait;

    // Page ID
    public $PageID = "addopt";

    // Project ID
    public $ProjectID = PROJECT_ID;

    // Table name
    public $TableName = 'equipotorneo';

    // Page object name
    public $PageObjName = "EquipotorneoAddopt";

    // View file path
    public $View = null;

    // Title
    public $Title = null; // Title for <title> tag

    // Rendering View
    public $RenderingView = false;

    // Page headings
    public $Heading = "";
    public $Subheading = "";
    public $PageHeader;
    public $PageFooter;

    // Page layout
    public $UseLayout = true;

    // Page terminated
    private $terminated = false;

    // Page heading
    public function pageHeading()
    {
        global $Language;
        if ($this->Heading != "") {
            return $this->Heading;
        }
        if (method_exists($this, "tableCaption")) {
            return $this->tableCaption();
        }
        return "";
    }

    // Page subheading
    public function pageSubheading()
    {
        global $Language;
        if ($this->Subheading != "") {
            return $this->Subheading;
        }
        if ($this->TableName) {
            return $Language->phrase($this->PageID);
        }
        return "";
    }

    // Page name
    public function pageName()
    {
        return CurrentPageName();
    }

    // Page URL
    public function pageUrl($withArgs = true)
    {
        $route = GetRoute();
        $args = RemoveXss($route->getArguments());
        if (!$withArgs) {
            foreach ($args as $key => &$val) {
                $val = "";
            }
            unset($val);
        }
        $url = rtrim(UrlFor($route->getName(), $args), "/") . "?";
        if ($this->UseTokenInUrl) {
            $url .= "t=" . $this->TableVar . "&"; // Add page token
        }
        return $url;
    }

    // Show Page Header
    public function showPageHeader()
    {
        $header = $this->PageHeader;
        $this->pageDataRendering($header);
        if ($header != "") { // Header exists, display
            echo '<p id="ew-page-header">' . $header . '</p>';
        }
    }

    // Show Page Footer
    public function showPageFooter()
    {
        $footer = $this->PageFooter;
        $this->pageDataRendered($footer);
        if ($footer != "") { // Footer exists, display
            echo '<p id="ew-page-footer">' . $footer . '</p>';
        }
    }

    // Validate page request
    protected function isPageRequest()
    {
        global $CurrentForm;
        if ($this->UseTokenInUrl) {
            if ($CurrentForm) {
                return $this->TableVar == $CurrentForm->getValue("t");
            }
            if (Get("t") !== null) {
                return $this->TableVar == Get("t");
            }
        }
        return true;
    }

    // Constructor
    public function __construct()
    {
        global $Language, $DashboardReport, $DebugTimer;
        global $UserTable;

        // Initialize
        $GLOBALS["Page"] = &$this;

        // Language object
        $Language = Container("language");

        // Parent constuctor
        parent::__construct();

        // Table object (equipotorneo)
        if (!isset($GLOBALS["equipotorneo"]) || get_class($GLOBALS["equipotorneo"]) == PROJECT_NAMESPACE . "equipotorneo") {
            $GLOBALS["equipotorneo"] = &$this;
        }

        // Table name (for backward compatibility only)
        if (!defined(PROJECT_NAMESPACE . "TABLE_NAME")) {
            define(PROJECT_NAMESPACE . "TABLE_NAME", 'equipotorneo');
        }

        // Start timer
        $DebugTimer = Container("timer");

        // Debug message
        LoadDebugMessage();

        // Open connection
        $GLOBALS["Conn"] = $GLOBALS["Conn"] ?? $this->getConnection();

        // User table object
        $UserTable = Container("usertable");
    }

    // Get content from stream
    public function getContents($stream = null): string
    {
        global $Response;
        return is_object($Response) ? $Response->getBody() : ob_get_clean();
    }

    // Is lookup
    public function isLookup()
    {
        return SameText(Route(0), Config("API_LOOKUP_ACTION"));
    }

    // Is AutoFill
    public function isAutoFill()
    {
        return $this->isLookup() && SameText(Post("ajax"), "autofill");
    }

    // Is AutoSuggest
    public function isAutoSuggest()
    {
        return $this->isLookup() && SameText(Post("ajax"), "autosuggest");
    }

    // Is modal lookup
    public function isModalLookup()
    {
        return $this->isLookup() && SameText(Post("ajax"), "modal");
    }

    // Is terminated
    public function isTerminated()
    {
        return $this->terminated;
    }

    /**
     * Terminate page
     *
     * @param string $url URL for direction
     * @return void
     */
    public function terminate($url = "")
    {
        if ($this->terminated) {
            return;
        }
        global $ExportFileName, $TempImages, $DashboardReport, $Response;

        // Page is terminated
        $this->terminated = true;

         // Page Unload event
        if (method_exists($this, "pageUnload")) {
            $this->pageUnload();
        }

        // Global Page Unloaded event (in userfn*.php)
        Page_Unloaded();

        // Export
        if ($this->CustomExport && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, Config("EXPORT_CLASSES"))) {
            $content = $this->getContents();
            if ($ExportFileName == "") {
                $ExportFileName = $this->TableVar;
            }
            $class = PROJECT_NAMESPACE . Config("EXPORT_CLASSES." . $this->CustomExport);
            if (class_exists($class)) {
                $tbl = Container("equipotorneo");
                $doc = new $class($tbl);
                $doc->Text = @$content;
                if ($this->isExport("email")) {
                    echo $this->exportEmail($doc->Text);
                } else {
                    $doc->export();
                }
                DeleteTempImages(); // Delete temp images
                return;
            }
        }
        if (!IsApi() && method_exists($this, "pageRedirecting")) {
            $this->pageRedirecting($url);
        }

        // Close connection
        CloseConnections();

        // Return for API
        if (IsApi()) {
            $res = $url === true;
            if (!$res) { // Show error
                WriteJson(array_merge(["success" => false], $this->getMessages()));
            }
            return;
        } else { // Check if response is JSON
            if (StartsString("application/json", $Response->getHeaderLine("Content-type")) && $Response->getBody()->getSize()) { // With JSON response
                $this->clearMessages();
                return;
            }
        }

        // Go to URL if specified
        if ($url != "") {
            if (!Config("DEBUG") && ob_get_length()) {
                ob_end_clean();
            }
            SaveDebugMessage();
            Redirect(GetUrl($url));
        }
        return; // Return to controller
    }

    // Get records from recordset
    protected function getRecordsFromRecordset($rs, $current = false)
    {
        $rows = [];
        if (is_object($rs)) { // Recordset
            while ($rs && !$rs->EOF) {
                $this->loadRowValues($rs); // Set up DbValue/CurrentValue
                $row = $this->getRecordFromArray($rs->fields);
                if ($current) {
                    return $row;
                } else {
                    $rows[] = $row;
                }
                $rs->moveNext();
            }
        } elseif (is_array($rs)) {
            foreach ($rs as $ar) {
                $row = $this->getRecordFromArray($ar);
                if ($current) {
                    return $row;
                } else {
                    $rows[] = $row;
                }
            }
        }
        return $rows;
    }

    // Get record from array
    protected function getRecordFromArray($ar)
    {
        $row = [];
        if (is_array($ar)) {
            foreach ($ar as $fldname => $val) {
                if (array_key_exists($fldname, $this->Fields) && ($this->Fields[$fldname]->Visible || $this->Fields[$fldname]->IsPrimaryKey)) { // Primary key or Visible
                    $fld = &$this->Fields[$fldname];
                    if ($fld->HtmlTag == "FILE") { // Upload field
                        if (EmptyValue($val)) {
                            $row[$fldname] = null;
                        } else {
                            if ($fld->DataType == DATATYPE_BLOB) {
                                $url = FullUrl(GetApiUrl(Config("API_FILE_ACTION") .
                                    "/" . $fld->TableVar . "/" . $fld->Param . "/" . rawurlencode($this->getRecordKeyValue($ar))));
                                $row[$fldname] = ["type" => ContentType($val), "url" => $url, "name" => $fld->Param . ContentExtension($val)];
                            } elseif (!$fld->UploadMultiple || !ContainsString($val, Config("MULTIPLE_UPLOAD_SEPARATOR"))) { // Single file
                                $url = FullUrl(GetApiUrl(Config("API_FILE_ACTION") .
                                    "/" . $fld->TableVar . "/" . Encrypt($fld->physicalUploadPath() . $val)));
                                $row[$fldname] = ["type" => MimeContentType($val), "url" => $url, "name" => $val];
                            } else { // Multiple files
                                $files = explode(Config("MULTIPLE_UPLOAD_SEPARATOR"), $val);
                                $ar = [];
                                foreach ($files as $file) {
                                    $url = FullUrl(GetApiUrl(Config("API_FILE_ACTION") .
                                        "/" . $fld->TableVar . "/" . Encrypt($fld->physicalUploadPath() . $file)));
                                    if (!EmptyValue($file)) {
                                        $ar[] = ["type" => MimeContentType($file), "url" => $url, "name" => $file];
                                    }
                                }
                                $row[$fldname] = $ar;
                            }
                        }
                    } else {
                        $row[$fldname] = $val;
                    }
                }
            }
        }
        return $row;
    }

    // Get record key value from array
    protected function getRecordKeyValue($ar)
    {
        $key = "";
        if (is_array($ar)) {
            $key .= @$ar['ID_EQUIPO_TORNEO'];
        }
        return $key;
    }

    /**
     * Hide fields for add/edit
     *
     * @return void
     */
    protected function hideFieldsForAddEdit()
    {
        if ($this->isAdd() || $this->isCopy() || $this->isGridAdd()) {
            $this->ID_EQUIPO_TORNEO->Visible = false;
        }
    }

    // Lookup data
    public function lookup($ar = null)
    {
        global $Language, $Security;

        // Get lookup object
        $fieldName = $ar["field"] ?? Post("field");
        $lookup = $this->Fields[$fieldName]->Lookup;

        // Get lookup parameters
        $lookupType = $ar["ajax"] ?? Post("ajax", "unknown");
        $pageSize = -1;
        $offset = -1;
        $searchValue = "";
        if (SameText($lookupType, "modal") || SameText($lookupType, "filter")) {
            $searchValue = $ar["q"] ?? Param("q") ?? $ar["sv"] ?? Post("sv", "");
            $pageSize = $ar["n"] ?? Param("n") ?? $ar["recperpage"] ?? Post("recperpage", 10);
        } elseif (SameText($lookupType, "autosuggest")) {
            $searchValue = $ar["q"] ?? Param("q", "");
            $pageSize = $ar["n"] ?? Param("n", -1);
            $pageSize = is_numeric($pageSize) ? (int)$pageSize : -1;
            if ($pageSize <= 0) {
                $pageSize = Config("AUTO_SUGGEST_MAX_ENTRIES");
            }
        }
        $start = $ar["start"] ?? Param("start", -1);
        $start = is_numeric($start) ? (int)$start : -1;
        $page = $ar["page"] ?? Param("page", -1);
        $page = is_numeric($page) ? (int)$page : -1;
        $offset = $start >= 0 ? $start : ($page > 0 && $pageSize > 0 ? ($page - 1) * $pageSize : 0);
        $userSelect = Decrypt($ar["s"] ?? Post("s", ""));
        $userFilter = Decrypt($ar["f"] ?? Post("f", ""));
        $userOrderBy = Decrypt($ar["o"] ?? Post("o", ""));
        $keys = $ar["keys"] ?? Post("keys");
        $lookup->LookupType = $lookupType; // Lookup type
        $lookup->FilterValues = []; // Clear filter values first
        if ($keys !== null) { // Selected records from modal
            if (is_array($keys)) {
                $keys = implode(Config("MULTIPLE_OPTION_SEPARATOR"), $keys);
            }
            $lookup->FilterFields = []; // Skip parent fields if any
            $lookup->FilterValues[] = $keys; // Lookup values
            $pageSize = -1; // Show all records
        } else { // Lookup values
            $lookup->FilterValues[] = $ar["v0"] ?? $ar["lookupValue"] ?? Post("v0", Post("lookupValue", ""));
        }
        $cnt = is_array($lookup->FilterFields) ? count($lookup->FilterFields) : 0;
        for ($i = 1; $i <= $cnt; $i++) {
            $lookup->FilterValues[] = $ar["v" . $i] ?? Post("v" . $i, "");
        }
        $lookup->SearchValue = $searchValue;
        $lookup->PageSize = $pageSize;
        $lookup->Offset = $offset;
        if ($userSelect != "") {
            $lookup->UserSelect = $userSelect;
        }
        if ($userFilter != "") {
            $lookup->UserFilter = $userFilter;
        }
        if ($userOrderBy != "") {
            $lookup->UserOrderBy = $userOrderBy;
        }
        return $lookup->toJson($this, !is_array($ar)); // Use settings from current page
    }
    public $IsModal = false;
    public $IsMobileOrModal = true; // Add option page is always modal

    /**
     * Page run
     *
     * @return void
     */
    public function run()
    {
        global $ExportType, $CustomExportType, $ExportFileName, $UserProfile, $Language, $Security, $CurrentForm;

        // Use layout
        $this->UseLayout = $this->UseLayout && ConvertToBool(Param("layout", true));

        // Create form object
        $CurrentForm = new HttpForm();
        $this->CurrentAction = Param("action"); // Set up current action
        $this->ID_EQUIPO_TORNEO->Visible = false;
        $this->ID_TORNEO->setVisibility();
        $this->ID_EQUIPO->setVisibility();
        $this->PARTIDOS_JUGADOS->setVisibility();
        $this->PARTIDOS_GANADOS->setVisibility();
        $this->PARTIDOS_EMPATADOS->setVisibility();
        $this->PARTIDOS_PERDIDOS->setVisibility();
        $this->GF->setVisibility();
        $this->GC->setVisibility();
        $this->GD->setVisibility();
        $this->GRUPO->setVisibility();
        $this->POSICION_EQUIPO_TORENO->setVisibility();
        $this->crea_dato->setVisibility();
        $this->modifica_dato->setVisibility();
        $this->usuario_dato->setVisibility();
        $this->hideFieldsForAddEdit();

        // Set lookup cache
        if (!in_array($this->PageID, Config("LOOKUP_CACHE_PAGE_IDS"))) {
            $this->setUseLookupCache(false);
        }

        // Global Page Loading event (in userfn*.php)
        Page_Loading();

        // Page Load event
        if (method_exists($this, "pageLoad")) {
            $this->pageLoad();
        }

        // Set up lookup cache
        $this->setupLookupOptions($this->ID_TORNEO);
        $this->setupLookupOptions($this->ID_EQUIPO);
        $this->setupLookupOptions($this->GRUPO);
        $this->setupLookupOptions($this->POSICION_EQUIPO_TORENO);

        // Load default values for add
        $this->loadDefaultValues();

        // Set up Breadcrumb
        //$this->setupBreadcrumb(); // Not used
        $this->loadRowValues(); // Load default values

        // Render row
        $this->RowType = ROWTYPE_ADD; // Render add type
        $this->resetAttributes();
        $this->renderRow();

        // Set LoginStatus / Page_Rendering / Page_Render
        if (!IsApi() && !$this->isTerminated()) {
            // Setup login status
            SetupLoginStatus();

            // Pass login status to client side
            SetClientVar("login", LoginStatus());

            // Global Page Rendering event (in userfn*.php)
            Page_Rendering();

            // Page Render event
            if (method_exists($this, "pageRender")) {
                $this->pageRender();
            }

            // Render search option
            if (method_exists($this, "renderSearchOptions")) {
                $this->renderSearchOptions();
            }
        }
    }

    // Get upload files
    protected function getUploadFiles()
    {
        global $CurrentForm, $Language;
    }

    // Load default values
    protected function loadDefaultValues()
    {
        $this->PARTIDOS_JUGADOS->DefaultValue = 0;
        $this->PARTIDOS_JUGADOS->OldValue = $this->PARTIDOS_JUGADOS->DefaultValue;
        $this->PARTIDOS_GANADOS->DefaultValue = 0;
        $this->PARTIDOS_GANADOS->OldValue = $this->PARTIDOS_GANADOS->DefaultValue;
        $this->PARTIDOS_EMPATADOS->DefaultValue = 0;
        $this->PARTIDOS_EMPATADOS->OldValue = $this->PARTIDOS_EMPATADOS->DefaultValue;
        $this->PARTIDOS_PERDIDOS->DefaultValue = 0;
        $this->PARTIDOS_PERDIDOS->OldValue = $this->PARTIDOS_PERDIDOS->DefaultValue;
        $this->GF->DefaultValue = 0;
        $this->GF->OldValue = $this->GF->DefaultValue;
        $this->GC->DefaultValue = 0;
        $this->GC->OldValue = $this->GC->DefaultValue;
        $this->GD->DefaultValue = 0;
        $this->GD->OldValue = $this->GD->DefaultValue;
        $this->usuario_dato->DefaultValue = "admin";
        $this->usuario_dato->OldValue = $this->usuario_dato->DefaultValue;
    }

    // Load form values
    protected function loadFormValues()
    {
        // Load from form
        global $CurrentForm;
        $validate = !Config("SERVER_VALIDATE");

        // Check field name 'ID_TORNEO' first before field var 'x_ID_TORNEO'
        $val = $CurrentForm->hasValue("ID_TORNEO") ? $CurrentForm->getValue("ID_TORNEO") : $CurrentForm->getValue("x_ID_TORNEO");
        if (!$this->ID_TORNEO->IsDetailKey) {
            $this->ID_TORNEO->setFormValue(ConvertFromUtf8($val));
        }

        // Check field name 'ID_EQUIPO' first before field var 'x_ID_EQUIPO'
        $val = $CurrentForm->hasValue("ID_EQUIPO") ? $CurrentForm->getValue("ID_EQUIPO") : $CurrentForm->getValue("x_ID_EQUIPO");
        if (!$this->ID_EQUIPO->IsDetailKey) {
            $this->ID_EQUIPO->setFormValue(ConvertFromUtf8($val));
        }

        // Check field name 'PARTIDOS_JUGADOS' first before field var 'x_PARTIDOS_JUGADOS'
        $val = $CurrentForm->hasValue("PARTIDOS_JUGADOS") ? $CurrentForm->getValue("PARTIDOS_JUGADOS") : $CurrentForm->getValue("x_PARTIDOS_JUGADOS");
        if (!$this->PARTIDOS_JUGADOS->IsDetailKey) {
            $this->PARTIDOS_JUGADOS->setFormValue(ConvertFromUtf8($val), true, $validate);
        }

        // Check field name 'PARTIDOS_GANADOS' first before field var 'x_PARTIDOS_GANADOS'
        $val = $CurrentForm->hasValue("PARTIDOS_GANADOS") ? $CurrentForm->getValue("PARTIDOS_GANADOS") : $CurrentForm->getValue("x_PARTIDOS_GANADOS");
        if (!$this->PARTIDOS_GANADOS->IsDetailKey) {
            $this->PARTIDOS_GANADOS->setFormValue(ConvertFromUtf8($val), true, $validate);
        }

        // Check field name 'PARTIDOS_EMPATADOS' first before field var 'x_PARTIDOS_EMPATADOS'
        $val = $CurrentForm->hasValue("PARTIDOS_EMPATADOS") ? $CurrentForm->getValue("PARTIDOS_EMPATADOS") : $CurrentForm->getValue("x_PARTIDOS_EMPATADOS");
        if (!$this->PARTIDOS_EMPATADOS->IsDetailKey) {
            $this->PARTIDOS_EMPATADOS->setFormValue(ConvertFromUtf8($val), true, $validate);
        }

        // Check field name 'PARTIDOS_PERDIDOS' first before field var 'x_PARTIDOS_PERDIDOS'
        $val = $CurrentForm->hasValue("PARTIDOS_PERDIDOS") ? $CurrentForm->getValue("PARTIDOS_PERDIDOS") : $CurrentForm->getValue("x_PARTIDOS_PERDIDOS");
        if (!$this->PARTIDOS_PERDIDOS->IsDetailKey) {
            $this->PARTIDOS_PERDIDOS->setFormValue(ConvertFromUtf8($val), true, $validate);
        }

        // Check field name 'GF' first before field var 'x_GF'
        $val = $CurrentForm->hasValue("GF") ? $CurrentForm->getValue("GF") : $CurrentForm->getValue("x_GF");
        if (!$this->GF->IsDetailKey) {
            $this->GF->setFormValue(ConvertFromUtf8($val), true, $validate);
        }

        // Check field name 'GC' first before field var 'x_GC'
        $val = $CurrentForm->hasValue("GC") ? $CurrentForm->getValue("GC") : $CurrentForm->getValue("x_GC");
        if (!$this->GC->IsDetailKey) {
            $this->GC->setFormValue(ConvertFromUtf8($val), true, $validate);
        }

        // Check field name 'GD' first before field var 'x_GD'
        $val = $CurrentForm->hasValue("GD") ? $CurrentForm->getValue("GD") : $CurrentForm->getValue("x_GD");
        if (!$this->GD->IsDetailKey) {
            $this->GD->setFormValue(ConvertFromUtf8($val), true, $validate);
        }

        // Check field name 'GRUPO' first before field var 'x_GRUPO'
        $val = $CurrentForm->hasValue("GRUPO") ? $CurrentForm->getValue("GRUPO") : $CurrentForm->getValue("x_GRUPO");
        if (!$this->GRUPO->IsDetailKey) {
            $this->GRUPO->setFormValue(ConvertFromUtf8($val));
        }

        // Check field name 'POSICION_EQUIPO_TORENO' first before field var 'x_POSICION_EQUIPO_TORENO'
        $val = $CurrentForm->hasValue("POSICION_EQUIPO_TORENO") ? $CurrentForm->getValue("POSICION_EQUIPO_TORENO") : $CurrentForm->getValue("x_POSICION_EQUIPO_TORENO");
        if (!$this->POSICION_EQUIPO_TORENO->IsDetailKey) {
            $this->POSICION_EQUIPO_TORENO->setFormValue(ConvertFromUtf8($val));
        }

        // Check field name 'crea_dato' first before field var 'x_crea_dato'
        $val = $CurrentForm->hasValue("crea_dato") ? $CurrentForm->getValue("crea_dato") : $CurrentForm->getValue("x_crea_dato");
        if (!$this->crea_dato->IsDetailKey) {
            $this->crea_dato->setFormValue(ConvertFromUtf8($val));
            $this->crea_dato->CurrentValue = UnFormatDateTime($this->crea_dato->CurrentValue, $this->crea_dato->formatPattern());
        }

        // Check field name 'modifica_dato' first before field var 'x_modifica_dato'
        $val = $CurrentForm->hasValue("modifica_dato") ? $CurrentForm->getValue("modifica_dato") : $CurrentForm->getValue("x_modifica_dato");
        if (!$this->modifica_dato->IsDetailKey) {
            $this->modifica_dato->setFormValue(ConvertFromUtf8($val));
            $this->modifica_dato->CurrentValue = UnFormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern());
        }

        // Check field name 'usuario_dato' first before field var 'x_usuario_dato'
        $val = $CurrentForm->hasValue("usuario_dato") ? $CurrentForm->getValue("usuario_dato") : $CurrentForm->getValue("x_usuario_dato");
        if (!$this->usuario_dato->IsDetailKey) {
            $this->usuario_dato->setFormValue(ConvertFromUtf8($val));
        }

        // Check field name 'ID_EQUIPO_TORNEO' first before field var 'x_ID_EQUIPO_TORNEO'
        $val = $CurrentForm->hasValue("ID_EQUIPO_TORNEO") ? $CurrentForm->getValue("ID_EQUIPO_TORNEO") : $CurrentForm->getValue("x_ID_EQUIPO_TORNEO");
    }

    // Restore form values
    public function restoreFormValues()
    {
        global $CurrentForm;
        $this->ID_TORNEO->CurrentValue = ConvertToUtf8($this->ID_TORNEO->FormValue);
        $this->ID_EQUIPO->CurrentValue = ConvertToUtf8($this->ID_EQUIPO->FormValue);
        $this->PARTIDOS_JUGADOS->CurrentValue = ConvertToUtf8($this->PARTIDOS_JUGADOS->FormValue);
        $this->PARTIDOS_GANADOS->CurrentValue = ConvertToUtf8($this->PARTIDOS_GANADOS->FormValue);
        $this->PARTIDOS_EMPATADOS->CurrentValue = ConvertToUtf8($this->PARTIDOS_EMPATADOS->FormValue);
        $this->PARTIDOS_PERDIDOS->CurrentValue = ConvertToUtf8($this->PARTIDOS_PERDIDOS->FormValue);
        $this->GF->CurrentValue = ConvertToUtf8($this->GF->FormValue);
        $this->GC->CurrentValue = ConvertToUtf8($this->GC->FormValue);
        $this->GD->CurrentValue = ConvertToUtf8($this->GD->FormValue);
        $this->GRUPO->CurrentValue = ConvertToUtf8($this->GRUPO->FormValue);
        $this->POSICION_EQUIPO_TORENO->CurrentValue = ConvertToUtf8($this->POSICION_EQUIPO_TORENO->FormValue);
        $this->crea_dato->CurrentValue = ConvertToUtf8($this->crea_dato->FormValue);
        $this->crea_dato->CurrentValue = UnFormatDateTime($this->crea_dato->CurrentValue, $this->crea_dato->formatPattern());
        $this->modifica_dato->CurrentValue = ConvertToUtf8($this->modifica_dato->FormValue);
        $this->modifica_dato->CurrentValue = UnFormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern());
        $this->usuario_dato->CurrentValue = ConvertToUtf8($this->usuario_dato->FormValue);
    }

    /**
     * Load row based on key values
     *
     * @return void
     */
    public function loadRow()
    {
        global $Security, $Language;
        $filter = $this->getRecordFilter();

        // Call Row Selecting event
        $this->rowSelecting($filter);

        // Load SQL based on filter
        $this->CurrentFilter = $filter;
        $sql = $this->getCurrentSql();
        $conn = $this->getConnection();
        $res = false;
        $row = $conn->fetchAssociative($sql);
        if ($row) {
            $res = true;
            $this->loadRowValues($row); // Load row values
        }
        return $res;
    }

    /**
     * Load row values from recordset or record
     *
     * @param Recordset|array $rs Record
     * @return void
     */
    public function loadRowValues($rs = null)
    {
        if (is_array($rs)) {
            $row = $rs;
        } elseif ($rs && property_exists($rs, "fields")) { // Recordset
            $row = $rs->fields;
        } else {
            $row = $this->newRow();
        }
        if (!$row) {
            return;
        }

        // Call Row Selected event
        $this->rowSelected($row);
        $this->ID_EQUIPO_TORNEO->setDbValue($row['ID_EQUIPO_TORNEO']);
        $this->ID_TORNEO->setDbValue($row['ID_TORNEO']);
        $this->ID_EQUIPO->setDbValue($row['ID_EQUIPO']);
        if (array_key_exists('EV__ID_EQUIPO', $row)) {
            $this->ID_EQUIPO->VirtualValue = $row['EV__ID_EQUIPO']; // Set up virtual field value
        } else {
            $this->ID_EQUIPO->VirtualValue = ""; // Clear value
        }
        $this->PARTIDOS_JUGADOS->setDbValue($row['PARTIDOS_JUGADOS']);
        $this->PARTIDOS_GANADOS->setDbValue($row['PARTIDOS_GANADOS']);
        $this->PARTIDOS_EMPATADOS->setDbValue($row['PARTIDOS_EMPATADOS']);
        $this->PARTIDOS_PERDIDOS->setDbValue($row['PARTIDOS_PERDIDOS']);
        $this->GF->setDbValue($row['GF']);
        $this->GC->setDbValue($row['GC']);
        $this->GD->setDbValue($row['GD']);
        $this->GRUPO->setDbValue($row['GRUPO']);
        $this->POSICION_EQUIPO_TORENO->setDbValue($row['POSICION_EQUIPO_TORENO']);
        $this->crea_dato->setDbValue($row['crea_dato']);
        $this->modifica_dato->setDbValue($row['modifica_dato']);
        $this->usuario_dato->setDbValue($row['usuario_dato']);
    }

    // Return a row with default values
    protected function newRow()
    {
        $row = [];
        $row['ID_EQUIPO_TORNEO'] = $this->ID_EQUIPO_TORNEO->DefaultValue;
        $row['ID_TORNEO'] = $this->ID_TORNEO->DefaultValue;
        $row['ID_EQUIPO'] = $this->ID_EQUIPO->DefaultValue;
        $row['PARTIDOS_JUGADOS'] = $this->PARTIDOS_JUGADOS->DefaultValue;
        $row['PARTIDOS_GANADOS'] = $this->PARTIDOS_GANADOS->DefaultValue;
        $row['PARTIDOS_EMPATADOS'] = $this->PARTIDOS_EMPATADOS->DefaultValue;
        $row['PARTIDOS_PERDIDOS'] = $this->PARTIDOS_PERDIDOS->DefaultValue;
        $row['GF'] = $this->GF->DefaultValue;
        $row['GC'] = $this->GC->DefaultValue;
        $row['GD'] = $this->GD->DefaultValue;
        $row['GRUPO'] = $this->GRUPO->DefaultValue;
        $row['POSICION_EQUIPO_TORENO'] = $this->POSICION_EQUIPO_TORENO->DefaultValue;
        $row['crea_dato'] = $this->crea_dato->DefaultValue;
        $row['modifica_dato'] = $this->modifica_dato->DefaultValue;
        $row['usuario_dato'] = $this->usuario_dato->DefaultValue;
        return $row;
    }

    // Render row values based on field settings
    public function renderRow()
    {
        global $Security, $Language, $CurrentLanguage;

        // Initialize URLs

        // Call Row_Rendering event
        $this->rowRendering();

        // Common render codes for all row types

        // ID_EQUIPO_TORNEO
        $this->ID_EQUIPO_TORNEO->RowCssClass = "row";

        // ID_TORNEO
        $this->ID_TORNEO->RowCssClass = "row";

        // ID_EQUIPO
        $this->ID_EQUIPO->RowCssClass = "row";

        // PARTIDOS_JUGADOS
        $this->PARTIDOS_JUGADOS->RowCssClass = "row";

        // PARTIDOS_GANADOS
        $this->PARTIDOS_GANADOS->RowCssClass = "row";

        // PARTIDOS_EMPATADOS
        $this->PARTIDOS_EMPATADOS->RowCssClass = "row";

        // PARTIDOS_PERDIDOS
        $this->PARTIDOS_PERDIDOS->RowCssClass = "row";

        // GF
        $this->GF->RowCssClass = "row";

        // GC
        $this->GC->RowCssClass = "row";

        // GD
        $this->GD->RowCssClass = "row";

        // GRUPO
        $this->GRUPO->RowCssClass = "row";

        // POSICION_EQUIPO_TORENO
        $this->POSICION_EQUIPO_TORENO->RowCssClass = "row";

        // crea_dato
        $this->crea_dato->RowCssClass = "row";

        // modifica_dato
        $this->modifica_dato->RowCssClass = "row";

        // usuario_dato
        $this->usuario_dato->RowCssClass = "row";

        // View row
        if ($this->RowType == ROWTYPE_VIEW) {
            // ID_EQUIPO_TORNEO
            $this->ID_EQUIPO_TORNEO->ViewValue = $this->ID_EQUIPO_TORNEO->CurrentValue;
            $this->ID_EQUIPO_TORNEO->ViewCustomAttributes = "";

            // ID_TORNEO
            $curVal = strval($this->ID_TORNEO->CurrentValue);
            if ($curVal != "") {
                $this->ID_TORNEO->ViewValue = $this->ID_TORNEO->lookupCacheOption($curVal);
                if ($this->ID_TORNEO->ViewValue === null) { // Lookup from database
                    $filterWrk = "`ID_TORNEO`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                    $sqlWrk = $this->ID_TORNEO->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                    $conn = Conn();
                    $config = $conn->getConfiguration();
                    $config->setResultCacheImpl($this->Cache);
                    $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                    $ari = count($rswrk);
                    if ($ari > 0) { // Lookup values found
                        $arwrk = $this->ID_TORNEO->Lookup->renderViewRow($rswrk[0]);
                        $this->ID_TORNEO->ViewValue = $this->ID_TORNEO->displayValue($arwrk);
                    } else {
                        $this->ID_TORNEO->ViewValue = FormatNumber($this->ID_TORNEO->CurrentValue, $this->ID_TORNEO->formatPattern());
                    }
                }
            } else {
                $this->ID_TORNEO->ViewValue = null;
            }
            $this->ID_TORNEO->ViewCustomAttributes = "";

            // ID_EQUIPO
            if ($this->ID_EQUIPO->VirtualValue != "") {
                $this->ID_EQUIPO->ViewValue = $this->ID_EQUIPO->VirtualValue;
            } else {
                $curVal = strval($this->ID_EQUIPO->CurrentValue);
                if ($curVal != "") {
                    $this->ID_EQUIPO->ViewValue = $this->ID_EQUIPO->lookupCacheOption($curVal);
                    if ($this->ID_EQUIPO->ViewValue === null) { // Lookup from database
                        $filterWrk = "`ID_EQUIPO`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                        $sqlWrk = $this->ID_EQUIPO->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                        $conn = Conn();
                        $config = $conn->getConfiguration();
                        $config->setResultCacheImpl($this->Cache);
                        $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                        $ari = count($rswrk);
                        if ($ari > 0) { // Lookup values found
                            $arwrk = $this->ID_EQUIPO->Lookup->renderViewRow($rswrk[0]);
                            $this->ID_EQUIPO->ViewValue = $this->ID_EQUIPO->displayValue($arwrk);
                        } else {
                            $this->ID_EQUIPO->ViewValue = FormatNumber($this->ID_EQUIPO->CurrentValue, $this->ID_EQUIPO->formatPattern());
                        }
                    }
                } else {
                    $this->ID_EQUIPO->ViewValue = null;
                }
            }
            $this->ID_EQUIPO->ViewCustomAttributes = "";

            // PARTIDOS_JUGADOS
            $this->PARTIDOS_JUGADOS->ViewValue = $this->PARTIDOS_JUGADOS->CurrentValue;
            $this->PARTIDOS_JUGADOS->ViewValue = FormatNumber($this->PARTIDOS_JUGADOS->ViewValue, $this->PARTIDOS_JUGADOS->formatPattern());
            $this->PARTIDOS_JUGADOS->ViewCustomAttributes = "";

            // PARTIDOS_GANADOS
            $this->PARTIDOS_GANADOS->ViewValue = $this->PARTIDOS_GANADOS->CurrentValue;
            $this->PARTIDOS_GANADOS->ViewValue = FormatNumber($this->PARTIDOS_GANADOS->ViewValue, $this->PARTIDOS_GANADOS->formatPattern());
            $this->PARTIDOS_GANADOS->ViewCustomAttributes = "";

            // PARTIDOS_EMPATADOS
            $this->PARTIDOS_EMPATADOS->ViewValue = $this->PARTIDOS_EMPATADOS->CurrentValue;
            $this->PARTIDOS_EMPATADOS->ViewValue = FormatNumber($this->PARTIDOS_EMPATADOS->ViewValue, $this->PARTIDOS_EMPATADOS->formatPattern());
            $this->PARTIDOS_EMPATADOS->ViewCustomAttributes = "";

            // PARTIDOS_PERDIDOS
            $this->PARTIDOS_PERDIDOS->ViewValue = $this->PARTIDOS_PERDIDOS->CurrentValue;
            $this->PARTIDOS_PERDIDOS->ViewValue = FormatNumber($this->PARTIDOS_PERDIDOS->ViewValue, $this->PARTIDOS_PERDIDOS->formatPattern());
            $this->PARTIDOS_PERDIDOS->ViewCustomAttributes = "";

            // GF
            $this->GF->ViewValue = $this->GF->CurrentValue;
            $this->GF->ViewValue = FormatNumber($this->GF->ViewValue, $this->GF->formatPattern());
            $this->GF->ViewCustomAttributes = "";

            // GC
            $this->GC->ViewValue = $this->GC->CurrentValue;
            $this->GC->ViewValue = FormatNumber($this->GC->ViewValue, $this->GC->formatPattern());
            $this->GC->ViewCustomAttributes = "";

            // GD
            $this->GD->ViewValue = $this->GD->CurrentValue;
            $this->GD->ViewValue = FormatNumber($this->GD->ViewValue, $this->GD->formatPattern());
            $this->GD->ViewCustomAttributes = "";

            // GRUPO
            if (strval($this->GRUPO->CurrentValue) != "") {
                $this->GRUPO->ViewValue = $this->GRUPO->optionCaption($this->GRUPO->CurrentValue);
            } else {
                $this->GRUPO->ViewValue = null;
            }
            $this->GRUPO->ViewCustomAttributes = "";

            // POSICION_EQUIPO_TORENO
            if (strval($this->POSICION_EQUIPO_TORENO->CurrentValue) != "") {
                $this->POSICION_EQUIPO_TORENO->ViewValue = $this->POSICION_EQUIPO_TORENO->optionCaption($this->POSICION_EQUIPO_TORENO->CurrentValue);
            } else {
                $this->POSICION_EQUIPO_TORENO->ViewValue = null;
            }
            $this->POSICION_EQUIPO_TORENO->ViewCustomAttributes = "";

            // crea_dato
            $this->crea_dato->ViewValue = $this->crea_dato->CurrentValue;
            $this->crea_dato->ViewValue = FormatDateTime($this->crea_dato->ViewValue, $this->crea_dato->formatPattern());
            $this->crea_dato->CssClass = "fst-italic";
            $this->crea_dato->CellCssStyle .= "text-align: right;";
            $this->crea_dato->ViewCustomAttributes = "";

            // modifica_dato
            $this->modifica_dato->ViewValue = $this->modifica_dato->CurrentValue;
            $this->modifica_dato->ViewValue = FormatDateTime($this->modifica_dato->ViewValue, $this->modifica_dato->formatPattern());
            $this->modifica_dato->CssClass = "fst-italic";
            $this->modifica_dato->CellCssStyle .= "text-align: right;";
            $this->modifica_dato->ViewCustomAttributes = "";

            // usuario_dato
            $this->usuario_dato->ViewValue = $this->usuario_dato->CurrentValue;
            $this->usuario_dato->ViewCustomAttributes = "";

            // ID_TORNEO
            $this->ID_TORNEO->LinkCustomAttributes = "";
            $this->ID_TORNEO->HrefValue = "";
            $this->ID_TORNEO->TooltipValue = "";

            // ID_EQUIPO
            $this->ID_EQUIPO->LinkCustomAttributes = "";
            $this->ID_EQUIPO->HrefValue = "";
            $this->ID_EQUIPO->TooltipValue = "";

            // PARTIDOS_JUGADOS
            $this->PARTIDOS_JUGADOS->LinkCustomAttributes = "";
            $this->PARTIDOS_JUGADOS->HrefValue = "";
            $this->PARTIDOS_JUGADOS->TooltipValue = "";

            // PARTIDOS_GANADOS
            $this->PARTIDOS_GANADOS->LinkCustomAttributes = "";
            $this->PARTIDOS_GANADOS->HrefValue = "";
            $this->PARTIDOS_GANADOS->TooltipValue = "";

            // PARTIDOS_EMPATADOS
            $this->PARTIDOS_EMPATADOS->LinkCustomAttributes = "";
            $this->PARTIDOS_EMPATADOS->HrefValue = "";
            $this->PARTIDOS_EMPATADOS->TooltipValue = "";

            // PARTIDOS_PERDIDOS
            $this->PARTIDOS_PERDIDOS->LinkCustomAttributes = "";
            $this->PARTIDOS_PERDIDOS->HrefValue = "";
            $this->PARTIDOS_PERDIDOS->TooltipValue = "";

            // GF
            $this->GF->LinkCustomAttributes = "";
            $this->GF->HrefValue = "";
            $this->GF->TooltipValue = "";

            // GC
            $this->GC->LinkCustomAttributes = "";
            $this->GC->HrefValue = "";
            $this->GC->TooltipValue = "";

            // GD
            $this->GD->LinkCustomAttributes = "";
            $this->GD->HrefValue = "";
            $this->GD->TooltipValue = "";

            // GRUPO
            $this->GRUPO->LinkCustomAttributes = "";
            $this->GRUPO->HrefValue = "";
            $this->GRUPO->TooltipValue = "";

            // POSICION_EQUIPO_TORENO
            $this->POSICION_EQUIPO_TORENO->LinkCustomAttributes = "";
            $this->POSICION_EQUIPO_TORENO->HrefValue = "";
            $this->POSICION_EQUIPO_TORENO->TooltipValue = "";

            // crea_dato
            $this->crea_dato->LinkCustomAttributes = "";
            $this->crea_dato->HrefValue = "";
            $this->crea_dato->TooltipValue = "";

            // modifica_dato
            $this->modifica_dato->LinkCustomAttributes = "";
            $this->modifica_dato->HrefValue = "";
            $this->modifica_dato->TooltipValue = "";

            // usuario_dato
            $this->usuario_dato->LinkCustomAttributes = "";
            $this->usuario_dato->HrefValue = "";
            $this->usuario_dato->TooltipValue = "";
        } elseif ($this->RowType == ROWTYPE_ADD) {
            // ID_TORNEO
            $this->ID_TORNEO->setupEditAttributes();
            $this->ID_TORNEO->EditCustomAttributes = "";
            $curVal = trim(strval($this->ID_TORNEO->CurrentValue));
            if ($curVal != "") {
                $this->ID_TORNEO->ViewValue = $this->ID_TORNEO->lookupCacheOption($curVal);
            } else {
                $this->ID_TORNEO->ViewValue = $this->ID_TORNEO->Lookup !== null && is_array($this->ID_TORNEO->lookupOptions()) ? $curVal : null;
            }
            if ($this->ID_TORNEO->ViewValue !== null) { // Load from cache
                $this->ID_TORNEO->EditValue = array_values($this->ID_TORNEO->lookupOptions());
            } else { // Lookup from database
                if ($curVal == "") {
                    $filterWrk = "0=1";
                } else {
                    $filterWrk = "`ID_TORNEO`" . SearchString("=", $this->ID_TORNEO->CurrentValue, DATATYPE_NUMBER, "");
                }
                $sqlWrk = $this->ID_TORNEO->Lookup->getSql(true, $filterWrk, '', $this, false, true);
                $conn = Conn();
                $config = $conn->getConfiguration();
                $config->setResultCacheImpl($this->Cache);
                $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                $ari = count($rswrk);
                $arwrk = $rswrk;
                $this->ID_TORNEO->EditValue = $arwrk;
            }
            $this->ID_TORNEO->PlaceHolder = RemoveHtml($this->ID_TORNEO->caption());

            // ID_EQUIPO
            $this->ID_EQUIPO->setupEditAttributes();
            $this->ID_EQUIPO->EditCustomAttributes = "";
            $curVal = trim(strval($this->ID_EQUIPO->CurrentValue));
            if ($curVal != "") {
                $this->ID_EQUIPO->ViewValue = $this->ID_EQUIPO->lookupCacheOption($curVal);
            } else {
                $this->ID_EQUIPO->ViewValue = $this->ID_EQUIPO->Lookup !== null && is_array($this->ID_EQUIPO->lookupOptions()) ? $curVal : null;
            }
            if ($this->ID_EQUIPO->ViewValue !== null) { // Load from cache
                $this->ID_EQUIPO->EditValue = array_values($this->ID_EQUIPO->lookupOptions());
            } else { // Lookup from database
                if ($curVal == "") {
                    $filterWrk = "0=1";
                } else {
                    $filterWrk = "`ID_EQUIPO`" . SearchString("=", $this->ID_EQUIPO->CurrentValue, DATATYPE_NUMBER, "");
                }
                $sqlWrk = $this->ID_EQUIPO->Lookup->getSql(true, $filterWrk, '', $this, false, true);
                $conn = Conn();
                $config = $conn->getConfiguration();
                $config->setResultCacheImpl($this->Cache);
                $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                $ari = count($rswrk);
                $arwrk = $rswrk;
                $this->ID_EQUIPO->EditValue = $arwrk;
            }
            $this->ID_EQUIPO->PlaceHolder = RemoveHtml($this->ID_EQUIPO->caption());

            // PARTIDOS_JUGADOS
            $this->PARTIDOS_JUGADOS->setupEditAttributes();
            $this->PARTIDOS_JUGADOS->EditCustomAttributes = "";
            $this->PARTIDOS_JUGADOS->EditValue = HtmlEncode($this->PARTIDOS_JUGADOS->CurrentValue);
            $this->PARTIDOS_JUGADOS->PlaceHolder = RemoveHtml($this->PARTIDOS_JUGADOS->caption());
            if (strval($this->PARTIDOS_JUGADOS->EditValue) != "" && is_numeric($this->PARTIDOS_JUGADOS->EditValue)) {
                $this->PARTIDOS_JUGADOS->EditValue = FormatNumber($this->PARTIDOS_JUGADOS->EditValue, null);
            }

            // PARTIDOS_GANADOS
            $this->PARTIDOS_GANADOS->setupEditAttributes();
            $this->PARTIDOS_GANADOS->EditCustomAttributes = "";
            $this->PARTIDOS_GANADOS->EditValue = HtmlEncode($this->PARTIDOS_GANADOS->CurrentValue);
            $this->PARTIDOS_GANADOS->PlaceHolder = RemoveHtml($this->PARTIDOS_GANADOS->caption());
            if (strval($this->PARTIDOS_GANADOS->EditValue) != "" && is_numeric($this->PARTIDOS_GANADOS->EditValue)) {
                $this->PARTIDOS_GANADOS->EditValue = FormatNumber($this->PARTIDOS_GANADOS->EditValue, null);
            }

            // PARTIDOS_EMPATADOS
            $this->PARTIDOS_EMPATADOS->setupEditAttributes();
            $this->PARTIDOS_EMPATADOS->EditCustomAttributes = "";
            $this->PARTIDOS_EMPATADOS->EditValue = HtmlEncode($this->PARTIDOS_EMPATADOS->CurrentValue);
            $this->PARTIDOS_EMPATADOS->PlaceHolder = RemoveHtml($this->PARTIDOS_EMPATADOS->caption());
            if (strval($this->PARTIDOS_EMPATADOS->EditValue) != "" && is_numeric($this->PARTIDOS_EMPATADOS->EditValue)) {
                $this->PARTIDOS_EMPATADOS->EditValue = FormatNumber($this->PARTIDOS_EMPATADOS->EditValue, null);
            }

            // PARTIDOS_PERDIDOS
            $this->PARTIDOS_PERDIDOS->setupEditAttributes();
            $this->PARTIDOS_PERDIDOS->EditCustomAttributes = "";
            $this->PARTIDOS_PERDIDOS->EditValue = HtmlEncode($this->PARTIDOS_PERDIDOS->CurrentValue);
            $this->PARTIDOS_PERDIDOS->PlaceHolder = RemoveHtml($this->PARTIDOS_PERDIDOS->caption());
            if (strval($this->PARTIDOS_PERDIDOS->EditValue) != "" && is_numeric($this->PARTIDOS_PERDIDOS->EditValue)) {
                $this->PARTIDOS_PERDIDOS->EditValue = FormatNumber($this->PARTIDOS_PERDIDOS->EditValue, null);
            }

            // GF
            $this->GF->setupEditAttributes();
            $this->GF->EditCustomAttributes = "";
            $this->GF->EditValue = HtmlEncode($this->GF->CurrentValue);
            $this->GF->PlaceHolder = RemoveHtml($this->GF->caption());
            if (strval($this->GF->EditValue) != "" && is_numeric($this->GF->EditValue)) {
                $this->GF->EditValue = FormatNumber($this->GF->EditValue, null);
            }

            // GC
            $this->GC->setupEditAttributes();
            $this->GC->EditCustomAttributes = "";
            $this->GC->EditValue = HtmlEncode($this->GC->CurrentValue);
            $this->GC->PlaceHolder = RemoveHtml($this->GC->caption());
            if (strval($this->GC->EditValue) != "" && is_numeric($this->GC->EditValue)) {
                $this->GC->EditValue = FormatNumber($this->GC->EditValue, null);
            }

            // GD
            $this->GD->setupEditAttributes();
            $this->GD->EditCustomAttributes = "";
            $this->GD->EditValue = HtmlEncode($this->GD->CurrentValue);
            $this->GD->PlaceHolder = RemoveHtml($this->GD->caption());
            if (strval($this->GD->EditValue) != "" && is_numeric($this->GD->EditValue)) {
                $this->GD->EditValue = FormatNumber($this->GD->EditValue, null);
            }

            // GRUPO
            $this->GRUPO->setupEditAttributes();
            $this->GRUPO->EditCustomAttributes = "";
            $this->GRUPO->EditValue = $this->GRUPO->options(true);
            $this->GRUPO->PlaceHolder = RemoveHtml($this->GRUPO->caption());

            // POSICION_EQUIPO_TORENO
            $this->POSICION_EQUIPO_TORENO->setupEditAttributes();
            $this->POSICION_EQUIPO_TORENO->EditCustomAttributes = "";
            $this->POSICION_EQUIPO_TORENO->EditValue = $this->POSICION_EQUIPO_TORENO->options(true);
            $this->POSICION_EQUIPO_TORENO->PlaceHolder = RemoveHtml($this->POSICION_EQUIPO_TORENO->caption());

            // crea_dato
            $this->crea_dato->setupEditAttributes();
            $this->crea_dato->EditCustomAttributes = "";
            $this->crea_dato->CurrentValue = FormatDateTime($this->crea_dato->CurrentValue, $this->crea_dato->formatPattern());

            // modifica_dato
            $this->modifica_dato->setupEditAttributes();
            $this->modifica_dato->EditCustomAttributes = "";
            $this->modifica_dato->CurrentValue = FormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern());

            // usuario_dato
            $this->usuario_dato->setupEditAttributes();
            $this->usuario_dato->EditCustomAttributes = "";
            $this->usuario_dato->CurrentValue = CurrentUserName();

            // Add refer script

            // ID_TORNEO
            $this->ID_TORNEO->LinkCustomAttributes = "";
            $this->ID_TORNEO->HrefValue = "";

            // ID_EQUIPO
            $this->ID_EQUIPO->LinkCustomAttributes = "";
            $this->ID_EQUIPO->HrefValue = "";

            // PARTIDOS_JUGADOS
            $this->PARTIDOS_JUGADOS->LinkCustomAttributes = "";
            $this->PARTIDOS_JUGADOS->HrefValue = "";

            // PARTIDOS_GANADOS
            $this->PARTIDOS_GANADOS->LinkCustomAttributes = "";
            $this->PARTIDOS_GANADOS->HrefValue = "";

            // PARTIDOS_EMPATADOS
            $this->PARTIDOS_EMPATADOS->LinkCustomAttributes = "";
            $this->PARTIDOS_EMPATADOS->HrefValue = "";

            // PARTIDOS_PERDIDOS
            $this->PARTIDOS_PERDIDOS->LinkCustomAttributes = "";
            $this->PARTIDOS_PERDIDOS->HrefValue = "";

            // GF
            $this->GF->LinkCustomAttributes = "";
            $this->GF->HrefValue = "";

            // GC
            $this->GC->LinkCustomAttributes = "";
            $this->GC->HrefValue = "";

            // GD
            $this->GD->LinkCustomAttributes = "";
            $this->GD->HrefValue = "";

            // GRUPO
            $this->GRUPO->LinkCustomAttributes = "";
            $this->GRUPO->HrefValue = "";

            // POSICION_EQUIPO_TORENO
            $this->POSICION_EQUIPO_TORENO->LinkCustomAttributes = "";
            $this->POSICION_EQUIPO_TORENO->HrefValue = "";

            // crea_dato
            $this->crea_dato->LinkCustomAttributes = "";
            $this->crea_dato->HrefValue = "";

            // modifica_dato
            $this->modifica_dato->LinkCustomAttributes = "";
            $this->modifica_dato->HrefValue = "";

            // usuario_dato
            $this->usuario_dato->LinkCustomAttributes = "";
            $this->usuario_dato->HrefValue = "";
        }
        if ($this->RowType == ROWTYPE_ADD || $this->RowType == ROWTYPE_EDIT || $this->RowType == ROWTYPE_SEARCH) { // Add/Edit/Search row
            $this->setupFieldTitles();
        }

        // Call Row Rendered event
        if ($this->RowType != ROWTYPE_AGGREGATEINIT) {
            $this->rowRendered();
        }
    }

    // Validate form
    protected function validateForm()
    {
        global $Language;

        // Check if validation required
        if (!Config("SERVER_VALIDATE")) {
            return true;
        }
        $validateForm = true;
        if ($this->ID_TORNEO->Required) {
            if (!$this->ID_TORNEO->IsDetailKey && EmptyValue($this->ID_TORNEO->FormValue)) {
                $this->ID_TORNEO->addErrorMessage(str_replace("%s", $this->ID_TORNEO->caption(), $this->ID_TORNEO->RequiredErrorMessage));
            }
        }
        if ($this->ID_EQUIPO->Required) {
            if (!$this->ID_EQUIPO->IsDetailKey && EmptyValue($this->ID_EQUIPO->FormValue)) {
                $this->ID_EQUIPO->addErrorMessage(str_replace("%s", $this->ID_EQUIPO->caption(), $this->ID_EQUIPO->RequiredErrorMessage));
            }
        }
        if ($this->PARTIDOS_JUGADOS->Required) {
            if (!$this->PARTIDOS_JUGADOS->IsDetailKey && EmptyValue($this->PARTIDOS_JUGADOS->FormValue)) {
                $this->PARTIDOS_JUGADOS->addErrorMessage(str_replace("%s", $this->PARTIDOS_JUGADOS->caption(), $this->PARTIDOS_JUGADOS->RequiredErrorMessage));
            }
        }
        if (!CheckInteger($this->PARTIDOS_JUGADOS->FormValue)) {
            $this->PARTIDOS_JUGADOS->addErrorMessage($this->PARTIDOS_JUGADOS->getErrorMessage(false));
        }
        if ($this->PARTIDOS_GANADOS->Required) {
            if (!$this->PARTIDOS_GANADOS->IsDetailKey && EmptyValue($this->PARTIDOS_GANADOS->FormValue)) {
                $this->PARTIDOS_GANADOS->addErrorMessage(str_replace("%s", $this->PARTIDOS_GANADOS->caption(), $this->PARTIDOS_GANADOS->RequiredErrorMessage));
            }
        }
        if (!CheckInteger($this->PARTIDOS_GANADOS->FormValue)) {
            $this->PARTIDOS_GANADOS->addErrorMessage($this->PARTIDOS_GANADOS->getErrorMessage(false));
        }
        if ($this->PARTIDOS_EMPATADOS->Required) {
            if (!$this->PARTIDOS_EMPATADOS->IsDetailKey && EmptyValue($this->PARTIDOS_EMPATADOS->FormValue)) {
                $this->PARTIDOS_EMPATADOS->addErrorMessage(str_replace("%s", $this->PARTIDOS_EMPATADOS->caption(), $this->PARTIDOS_EMPATADOS->RequiredErrorMessage));
            }
        }
        if (!CheckInteger($this->PARTIDOS_EMPATADOS->FormValue)) {
            $this->PARTIDOS_EMPATADOS->addErrorMessage($this->PARTIDOS_EMPATADOS->getErrorMessage(false));
        }
        if ($this->PARTIDOS_PERDIDOS->Required) {
            if (!$this->PARTIDOS_PERDIDOS->IsDetailKey && EmptyValue($this->PARTIDOS_PERDIDOS->FormValue)) {
                $this->PARTIDOS_PERDIDOS->addErrorMessage(str_replace("%s", $this->PARTIDOS_PERDIDOS->caption(), $this->PARTIDOS_PERDIDOS->RequiredErrorMessage));
            }
        }
        if (!CheckInteger($this->PARTIDOS_PERDIDOS->FormValue)) {
            $this->PARTIDOS_PERDIDOS->addErrorMessage($this->PARTIDOS_PERDIDOS->getErrorMessage(false));
        }
        if ($this->GF->Required) {
            if (!$this->GF->IsDetailKey && EmptyValue($this->GF->FormValue)) {
                $this->GF->addErrorMessage(str_replace("%s", $this->GF->caption(), $this->GF->RequiredErrorMessage));
            }
        }
        if (!CheckInteger($this->GF->FormValue)) {
            $this->GF->addErrorMessage($this->GF->getErrorMessage(false));
        }
        if ($this->GC->Required) {
            if (!$this->GC->IsDetailKey && EmptyValue($this->GC->FormValue)) {
                $this->GC->addErrorMessage(str_replace("%s", $this->GC->caption(), $this->GC->RequiredErrorMessage));
            }
        }
        if (!CheckInteger($this->GC->FormValue)) {
            $this->GC->addErrorMessage($this->GC->getErrorMessage(false));
        }
        if ($this->GD->Required) {
            if (!$this->GD->IsDetailKey && EmptyValue($this->GD->FormValue)) {
                $this->GD->addErrorMessage(str_replace("%s", $this->GD->caption(), $this->GD->RequiredErrorMessage));
            }
        }
        if (!CheckInteger($this->GD->FormValue)) {
            $this->GD->addErrorMessage($this->GD->getErrorMessage(false));
        }
        if ($this->GRUPO->Required) {
            if (!$this->GRUPO->IsDetailKey && EmptyValue($this->GRUPO->FormValue)) {
                $this->GRUPO->addErrorMessage(str_replace("%s", $this->GRUPO->caption(), $this->GRUPO->RequiredErrorMessage));
            }
        }
        if ($this->POSICION_EQUIPO_TORENO->Required) {
            if (!$this->POSICION_EQUIPO_TORENO->IsDetailKey && EmptyValue($this->POSICION_EQUIPO_TORENO->FormValue)) {
                $this->POSICION_EQUIPO_TORENO->addErrorMessage(str_replace("%s", $this->POSICION_EQUIPO_TORENO->caption(), $this->POSICION_EQUIPO_TORENO->RequiredErrorMessage));
            }
        }
        if ($this->crea_dato->Required) {
            if (!$this->crea_dato->IsDetailKey && EmptyValue($this->crea_dato->FormValue)) {
                $this->crea_dato->addErrorMessage(str_replace("%s", $this->crea_dato->caption(), $this->crea_dato->RequiredErrorMessage));
            }
        }
        if ($this->modifica_dato->Required) {
            if (!$this->modifica_dato->IsDetailKey && EmptyValue($this->modifica_dato->FormValue)) {
                $this->modifica_dato->addErrorMessage(str_replace("%s", $this->modifica_dato->caption(), $this->modifica_dato->RequiredErrorMessage));
            }
        }
        if ($this->usuario_dato->Required) {
            if (!$this->usuario_dato->IsDetailKey && EmptyValue($this->usuario_dato->FormValue)) {
                $this->usuario_dato->addErrorMessage(str_replace("%s", $this->usuario_dato->caption(), $this->usuario_dato->RequiredErrorMessage));
            }
        }

        // Return validate result
        $validateForm = $validateForm && !$this->hasInvalidFields();

        // Call Form_CustomValidate event
        $formCustomError = "";
        $validateForm = $validateForm && $this->formCustomValidate($formCustomError);
        if ($formCustomError != "") {
            $this->setFailureMessage($formCustomError);
        }
        return $validateForm;
    }

    // Set up Breadcrumb
    protected function setupBreadcrumb()
    {
        global $Breadcrumb, $Language;
        $Breadcrumb = new Breadcrumb("index");
        $url = CurrentUrl();
        $Breadcrumb->add("list", $this->TableVar, $this->addMasterUrl("equipotorneolist"), "", $this->TableVar, true);
        $pageId = "addopt";
        $Breadcrumb->add("addopt", $pageId, $url);
    }

    // Setup lookup options
    public function setupLookupOptions($fld)
    {
        if ($fld->Lookup !== null && $fld->Lookup->Options === null) {
            // Get default connection and filter
            $conn = $this->getConnection();
            $lookupFilter = "";

            // No need to check any more
            $fld->Lookup->Options = [];

            // Set up lookup SQL and connection
            switch ($fld->FieldVar) {
                case "x_ID_TORNEO":
                    break;
                case "x_ID_EQUIPO":
                    break;
                case "x_GRUPO":
                    break;
                case "x_POSICION_EQUIPO_TORENO":
                    break;
                default:
                    $lookupFilter = "";
                    break;
            }

            // Always call to Lookup->getSql so that user can setup Lookup->Options in Lookup_Selecting server event
            $sql = $fld->Lookup->getSql(false, "", $lookupFilter, $this);

            // Set up lookup cache
            if (!$fld->hasLookupOptions() && $fld->UseLookupCache && $sql != "" && count($fld->Lookup->Options) == 0) {
                $totalCnt = $this->getRecordCount($sql, $conn);
                if ($totalCnt > $fld->LookupCacheCount) { // Total count > cache count, do not cache
                    return;
                }
                $rows = $conn->executeQuery($sql)->fetchAll();
                $ar = [];
                foreach ($rows as $row) {
                    $row = $fld->Lookup->renderViewRow($row, Container($fld->Lookup->LinkTable));
                    $ar[strval($row["lf"])] = $row;
                }
                $fld->Lookup->Options = $ar;
            }
        }
    }

    // Page Load event
    public function pageLoad()
    {
        //Log("Page Load");
    }

    // Page Unload event
    public function pageUnload()
    {
        //Log("Page Unload");
    }

    // Page Redirecting event
    public function pageRedirecting(&$url)
    {
        // Example:
        //$url = "your URL";
    }

    // Message Showing event
    // $type = ''|'success'|'failure'|'warning'
    public function messageShowing(&$msg, $type)
    {
        if ($type == 'success') {
            //$msg = "your success message";
        } elseif ($type == 'failure') {
            //$msg = "your failure message";
        } elseif ($type == 'warning') {
            //$msg = "your warning message";
        } else {
            //$msg = "your message";
        }
    }

    // Page Render event
    public function pageRender()
    {
        //Log("Page Render");
    }

    // Page Data Rendering event
    public function pageDataRendering(&$header)
    {
        // Example:
        //$header = "your header";
    }

    // Page Data Rendered event
    public function pageDataRendered(&$footer)
    {
        // Example:
        //$footer = "your footer";
    }
}
