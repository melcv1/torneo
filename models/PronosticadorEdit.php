<?php

namespace PHPMaker2022\project11;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Page class
 */
class PronosticadorEdit extends Pronosticador
{
    use MessagesTrait;

    // Page ID
    public $PageID = "edit";

    // Project ID
    public $ProjectID = PROJECT_ID;

    // Table name
    public $TableName = 'pronosticador';

    // Page object name
    public $PageObjName = "PronosticadorEdit";

    // View file path
    public $View = null;

    // Title
    public $Title = null; // Title for <title> tag

    // Rendering View
    public $RenderingView = false;

    // Audit Trail
    public $AuditTrailOnAdd = true;
    public $AuditTrailOnEdit = true;
    public $AuditTrailOnDelete = true;
    public $AuditTrailOnView = false;
    public $AuditTrailOnViewData = false;
    public $AuditTrailOnSearch = false;

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

        // Table object (pronosticador)
        if (!isset($GLOBALS["pronosticador"]) || get_class($GLOBALS["pronosticador"]) == PROJECT_NAMESPACE . "pronosticador") {
            $GLOBALS["pronosticador"] = &$this;
        }

        // Table name (for backward compatibility only)
        if (!defined(PROJECT_NAMESPACE . "TABLE_NAME")) {
            define(PROJECT_NAMESPACE . "TABLE_NAME", 'pronosticador');
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
                $tbl = Container("pronosticador");
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

            // Handle modal response
            if ($this->IsModal) { // Show as modal
                $row = ["url" => GetUrl($url), "modal" => "1"];
                $pageName = GetPageName($url);
                if ($pageName != $this->getListUrl()) { // Not List page
                    $row["caption"] = $this->getModalCaption($pageName);
                    if ($pageName == "pronosticadorview") {
                        $row["view"] = "1";
                    }
                } else { // List page should not be shown as modal => error
                    $row["error"] = $this->getFailureMessage();
                    $this->clearFailureMessage();
                }
                WriteJson($row);
            } else {
                SaveDebugMessage();
                Redirect(GetUrl($url));
            }
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
            $key .= @$ar['ID_ENCUESTA'];
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
            $this->ID_ENCUESTA->Visible = false;
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

    // Properties
    public $FormClassName = "ew-form ew-edit-form";
    public $IsModal = false;
    public $IsMobileOrModal = false;
    public $DbMasterFilter;
    public $DbDetailFilter;
    public $HashValue; // Hash Value
    public $DisplayRecords = 1;
    public $StartRecord;
    public $StopRecord;
    public $TotalRecords = 0;
    public $RecordRange = 10;
    public $RecordCount;

    /**
     * Page run
     *
     * @return void
     */
    public function run()
    {
        global $ExportType, $CustomExportType, $ExportFileName, $UserProfile, $Language, $Security, $CurrentForm,
            $SkipHeaderFooter;

        // Is modal
        $this->IsModal = Param("modal") == "1";
        $this->UseLayout = $this->UseLayout && !$this->IsModal;

        // Use layout
        $this->UseLayout = $this->UseLayout && ConvertToBool(Param("layout", true));

        // Create form object
        $CurrentForm = new HttpForm();
        $this->CurrentAction = Param("action"); // Set up current action
        $this->ID_ENCUESTA->setVisibility();
        $this->ID_EQUIPOTORNEO->setVisibility();
        $this->EQUIPO->setVisibility();
        $this->GRUPO->setVisibility();
        $this->POSICION->setVisibility();
        $this->NUMERACION->setVisibility();
        $this->crea_dato->setVisibility();
        $this->modifica_dato->setVisibility();
        $this->usuario_dato->setVisibility();
        $this->ID_PARTICIPANTE->setVisibility();
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
        $this->setupLookupOptions($this->ID_EQUIPOTORNEO);
        $this->setupLookupOptions($this->EQUIPO);
        $this->setupLookupOptions($this->GRUPO);
        $this->setupLookupOptions($this->POSICION);
        $this->setupLookupOptions($this->NUMERACION);
        $this->setupLookupOptions($this->ID_PARTICIPANTE);

        // Check modal
        if ($this->IsModal) {
            $SkipHeaderFooter = true;
        }
        $this->IsMobileOrModal = IsMobile() || $this->IsModal;
        $this->FormClassName = "ew-form ew-edit-form";
        $loaded = false;
        $postBack = false;

        // Set up current action and primary key
        if (IsApi()) {
            // Load key values
            $loaded = true;
            if (($keyValue = Get("ID_ENCUESTA") ?? Key(0) ?? Route(2)) !== null) {
                $this->ID_ENCUESTA->setQueryStringValue($keyValue);
                $this->ID_ENCUESTA->setOldValue($this->ID_ENCUESTA->QueryStringValue);
            } elseif (Post("ID_ENCUESTA") !== null) {
                $this->ID_ENCUESTA->setFormValue(Post("ID_ENCUESTA"));
                $this->ID_ENCUESTA->setOldValue($this->ID_ENCUESTA->FormValue);
            } else {
                $loaded = false; // Unable to load key
            }

            // Load record
            if ($loaded) {
                $loaded = $this->loadRow();
            }
            if (!$loaded) {
                $this->setFailureMessage($Language->phrase("NoRecord")); // Set no record message
                $this->terminate();
                return;
            }
            $this->CurrentAction = "update"; // Update record directly
            $this->OldKey = $this->getKey(true); // Get from CurrentValue
            $postBack = true;
        } else {
            if (Post("action") !== null) {
                $this->CurrentAction = Post("action"); // Get action code
                if (!$this->isShow()) { // Not reload record, handle as postback
                    $postBack = true;
                }

                // Get key from Form
                $this->setKey(Post($this->OldKeyName), $this->isShow());
            } else {
                $this->CurrentAction = "show"; // Default action is display

                // Load key from QueryString
                $loadByQuery = false;
                if (($keyValue = Get("ID_ENCUESTA") ?? Route("ID_ENCUESTA")) !== null) {
                    $this->ID_ENCUESTA->setQueryStringValue($keyValue);
                    $loadByQuery = true;
                } else {
                    $this->ID_ENCUESTA->CurrentValue = null;
                }
            }

            // Load recordset
            if ($this->isShow()) {
                    // Load current record
                    $loaded = $this->loadRow();
                $this->OldKey = $loaded ? $this->getKey(true) : ""; // Get from CurrentValue
            }
        }

        // Process form if post back
        if ($postBack) {
            $this->loadFormValues(); // Get form values

            // Overwrite record, reload hash value
            if ($this->isOverwrite()) {
                $this->loadRowHash();
                $this->CurrentAction = "update";
            }
        }

        // Validate form if post back
        if ($postBack) {
            if (!$this->validateForm()) {
                $this->EventCancelled = true; // Event cancelled
                $this->restoreFormValues();
                if (IsApi()) {
                    $this->terminate();
                    return;
                } else {
                    $this->CurrentAction = ""; // Form error, reset action
                }
            }
        }

        // Perform current action
        switch ($this->CurrentAction) {
            case "show": // Get a record to display
                    if (!$loaded) { // Load record based on key
                        if ($this->getFailureMessage() == "") {
                            $this->setFailureMessage($Language->phrase("NoRecord")); // No record found
                        }
                        $this->terminate("pronosticadorlist"); // No matching record, return to list
                        return;
                    }
                break;
            case "update": // Update
                $returnUrl = $this->getReturnUrl();
                if (GetPageName($returnUrl) == "pronosticadorlist") {
                    $returnUrl = $this->addMasterUrl($returnUrl); // List page, return to List page with correct master key if necessary
                }
                $this->SendEmail = true; // Send email on update success
                if ($this->editRow()) { // Update record based on key
                    if ($this->getSuccessMessage() == "") {
                        $this->setSuccessMessage($Language->phrase("UpdateSuccess")); // Update success
                    }
                    if (IsApi()) {
                        $this->terminate(true);
                        return;
                    } else {
                        $this->terminate($returnUrl); // Return to caller
                        return;
                    }
                } elseif (IsApi()) { // API request, return
                    $this->terminate();
                    return;
                } elseif ($this->getFailureMessage() == $Language->phrase("NoRecord")) {
                    $this->terminate($returnUrl); // Return to caller
                    return;
                } else {
                    $this->EventCancelled = true; // Event cancelled
                    $this->restoreFormValues(); // Restore form values if update failed
                }
        }

        // Set up Breadcrumb
        $this->setupBreadcrumb();

        // Render the record
        $this->RowType = ROWTYPE_EDIT; // Render as Edit
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

    // Load form values
    protected function loadFormValues()
    {
        // Load from form
        global $CurrentForm;
        $validate = !Config("SERVER_VALIDATE");

        // Check field name 'ID_ENCUESTA' first before field var 'x_ID_ENCUESTA'
        $val = $CurrentForm->hasValue("ID_ENCUESTA") ? $CurrentForm->getValue("ID_ENCUESTA") : $CurrentForm->getValue("x_ID_ENCUESTA");
        if (!$this->ID_ENCUESTA->IsDetailKey) {
            $this->ID_ENCUESTA->setFormValue($val);
        }

        // Check field name 'ID_EQUIPOTORNEO' first before field var 'x_ID_EQUIPOTORNEO'
        $val = $CurrentForm->hasValue("ID_EQUIPOTORNEO") ? $CurrentForm->getValue("ID_EQUIPOTORNEO") : $CurrentForm->getValue("x_ID_EQUIPOTORNEO");
        if (!$this->ID_EQUIPOTORNEO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->ID_EQUIPOTORNEO->Visible = false; // Disable update for API request
            } else {
                $this->ID_EQUIPOTORNEO->setFormValue($val);
            }
        }

        // Check field name 'EQUIPO' first before field var 'x_EQUIPO'
        $val = $CurrentForm->hasValue("EQUIPO") ? $CurrentForm->getValue("EQUIPO") : $CurrentForm->getValue("x_EQUIPO");
        if (!$this->EQUIPO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->EQUIPO->Visible = false; // Disable update for API request
            } else {
                $this->EQUIPO->setFormValue($val);
            }
        }

        // Check field name 'GRUPO' first before field var 'x_GRUPO'
        $val = $CurrentForm->hasValue("GRUPO") ? $CurrentForm->getValue("GRUPO") : $CurrentForm->getValue("x_GRUPO");
        if (!$this->GRUPO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->GRUPO->Visible = false; // Disable update for API request
            } else {
                $this->GRUPO->setFormValue($val);
            }
        }

        // Check field name 'POSICION' first before field var 'x_POSICION'
        $val = $CurrentForm->hasValue("POSICION") ? $CurrentForm->getValue("POSICION") : $CurrentForm->getValue("x_POSICION");
        if (!$this->POSICION->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->POSICION->Visible = false; // Disable update for API request
            } else {
                $this->POSICION->setFormValue($val);
            }
        }

        // Check field name 'NUMERACION' first before field var 'x_NUMERACION'
        $val = $CurrentForm->hasValue("NUMERACION") ? $CurrentForm->getValue("NUMERACION") : $CurrentForm->getValue("x_NUMERACION");
        if (!$this->NUMERACION->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->NUMERACION->Visible = false; // Disable update for API request
            } else {
                $this->NUMERACION->setFormValue($val);
            }
        }

        // Check field name 'crea_dato' first before field var 'x_crea_dato'
        $val = $CurrentForm->hasValue("crea_dato") ? $CurrentForm->getValue("crea_dato") : $CurrentForm->getValue("x_crea_dato");
        if (!$this->crea_dato->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->crea_dato->Visible = false; // Disable update for API request
            } else {
                $this->crea_dato->setFormValue($val);
            }
            $this->crea_dato->CurrentValue = UnFormatDateTime($this->crea_dato->CurrentValue, $this->crea_dato->formatPattern());
        }

        // Check field name 'modifica_dato' first before field var 'x_modifica_dato'
        $val = $CurrentForm->hasValue("modifica_dato") ? $CurrentForm->getValue("modifica_dato") : $CurrentForm->getValue("x_modifica_dato");
        if (!$this->modifica_dato->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->modifica_dato->Visible = false; // Disable update for API request
            } else {
                $this->modifica_dato->setFormValue($val);
            }
            $this->modifica_dato->CurrentValue = UnFormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern());
        }

        // Check field name 'usuario_dato' first before field var 'x_usuario_dato'
        $val = $CurrentForm->hasValue("usuario_dato") ? $CurrentForm->getValue("usuario_dato") : $CurrentForm->getValue("x_usuario_dato");
        if (!$this->usuario_dato->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->usuario_dato->Visible = false; // Disable update for API request
            } else {
                $this->usuario_dato->setFormValue($val);
            }
        }

        // Check field name 'ID_PARTICIPANTE' first before field var 'x_ID_PARTICIPANTE'
        $val = $CurrentForm->hasValue("ID_PARTICIPANTE") ? $CurrentForm->getValue("ID_PARTICIPANTE") : $CurrentForm->getValue("x_ID_PARTICIPANTE");
        if (!$this->ID_PARTICIPANTE->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->ID_PARTICIPANTE->Visible = false; // Disable update for API request
            } else {
                $this->ID_PARTICIPANTE->setFormValue($val);
            }
        }
        if (!$this->isOverwrite()) {
            $this->HashValue = $CurrentForm->getValue("k_hash");
        }
    }

    // Restore form values
    public function restoreFormValues()
    {
        global $CurrentForm;
        $this->ID_ENCUESTA->CurrentValue = $this->ID_ENCUESTA->FormValue;
        $this->ID_EQUIPOTORNEO->CurrentValue = $this->ID_EQUIPOTORNEO->FormValue;
        $this->EQUIPO->CurrentValue = $this->EQUIPO->FormValue;
        $this->GRUPO->CurrentValue = $this->GRUPO->FormValue;
        $this->POSICION->CurrentValue = $this->POSICION->FormValue;
        $this->NUMERACION->CurrentValue = $this->NUMERACION->FormValue;
        $this->crea_dato->CurrentValue = $this->crea_dato->FormValue;
        $this->crea_dato->CurrentValue = UnFormatDateTime($this->crea_dato->CurrentValue, $this->crea_dato->formatPattern());
        $this->modifica_dato->CurrentValue = $this->modifica_dato->FormValue;
        $this->modifica_dato->CurrentValue = UnFormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern());
        $this->usuario_dato->CurrentValue = $this->usuario_dato->FormValue;
        $this->ID_PARTICIPANTE->CurrentValue = $this->ID_PARTICIPANTE->FormValue;
        if (!$this->isOverwrite()) {
            $this->HashValue = $CurrentForm->getValue("k_hash");
        }
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
            if (!$this->EventCancelled) {
                $this->HashValue = $this->getRowHash($row); // Get hash value for record
            }
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
        $this->ID_ENCUESTA->setDbValue($row['ID_ENCUESTA']);
        $this->ID_EQUIPOTORNEO->setDbValue($row['ID_EQUIPOTORNEO']);
        $this->EQUIPO->setDbValue($row['EQUIPO']);
        if (array_key_exists('EV__EQUIPO', $row)) {
            $this->EQUIPO->VirtualValue = $row['EV__EQUIPO']; // Set up virtual field value
        } else {
            $this->EQUIPO->VirtualValue = ""; // Clear value
        }
        $this->GRUPO->setDbValue($row['GRUPO']);
        $this->POSICION->setDbValue($row['POSICION']);
        $this->NUMERACION->setDbValue($row['NUMERACION']);
        $this->crea_dato->setDbValue($row['crea_dato']);
        $this->modifica_dato->setDbValue($row['modifica_dato']);
        $this->usuario_dato->setDbValue($row['usuario_dato']);
        $this->ID_PARTICIPANTE->setDbValue($row['ID_PARTICIPANTE']);
    }

    // Return a row with default values
    protected function newRow()
    {
        $row = [];
        $row['ID_ENCUESTA'] = $this->ID_ENCUESTA->DefaultValue;
        $row['ID_EQUIPOTORNEO'] = $this->ID_EQUIPOTORNEO->DefaultValue;
        $row['EQUIPO'] = $this->EQUIPO->DefaultValue;
        $row['GRUPO'] = $this->GRUPO->DefaultValue;
        $row['POSICION'] = $this->POSICION->DefaultValue;
        $row['NUMERACION'] = $this->NUMERACION->DefaultValue;
        $row['crea_dato'] = $this->crea_dato->DefaultValue;
        $row['modifica_dato'] = $this->modifica_dato->DefaultValue;
        $row['usuario_dato'] = $this->usuario_dato->DefaultValue;
        $row['ID_PARTICIPANTE'] = $this->ID_PARTICIPANTE->DefaultValue;
        return $row;
    }

    // Load old record
    protected function loadOldRecord()
    {
        // Load old record
        $this->OldRecordset = null;
        $validKey = $this->OldKey != "";
        if ($validKey) {
            $this->CurrentFilter = $this->getRecordFilter();
            $sql = $this->getCurrentSql();
            $conn = $this->getConnection();
            $this->OldRecordset = LoadRecordset($sql, $conn);
        }
        $this->loadRowValues($this->OldRecordset); // Load row values
        return $validKey;
    }

    // Render row values based on field settings
    public function renderRow()
    {
        global $Security, $Language, $CurrentLanguage;

        // Initialize URLs

        // Call Row_Rendering event
        $this->rowRendering();

        // Common render codes for all row types

        // ID_ENCUESTA
        $this->ID_ENCUESTA->RowCssClass = "row";

        // ID_EQUIPOTORNEO
        $this->ID_EQUIPOTORNEO->RowCssClass = "row";

        // EQUIPO
        $this->EQUIPO->RowCssClass = "row";

        // GRUPO
        $this->GRUPO->RowCssClass = "row";

        // POSICION
        $this->POSICION->RowCssClass = "row";

        // NUMERACION
        $this->NUMERACION->RowCssClass = "row";

        // crea_dato
        $this->crea_dato->RowCssClass = "row";

        // modifica_dato
        $this->modifica_dato->RowCssClass = "row";

        // usuario_dato
        $this->usuario_dato->RowCssClass = "row";

        // ID_PARTICIPANTE
        $this->ID_PARTICIPANTE->RowCssClass = "row";

        // View row
        if ($this->RowType == ROWTYPE_VIEW) {
            // ID_ENCUESTA
            $this->ID_ENCUESTA->ViewValue = $this->ID_ENCUESTA->CurrentValue;
            $this->ID_ENCUESTA->ViewCustomAttributes = "";

            // ID_EQUIPOTORNEO
            $curVal = strval($this->ID_EQUIPOTORNEO->CurrentValue);
            if ($curVal != "") {
                $this->ID_EQUIPOTORNEO->ViewValue = $this->ID_EQUIPOTORNEO->lookupCacheOption($curVal);
                if ($this->ID_EQUIPOTORNEO->ViewValue === null) { // Lookup from database
                    $filterWrk = "`ID_EQUIPO_TORNEO`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                    $sqlWrk = $this->ID_EQUIPOTORNEO->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                    $conn = Conn();
                    $config = $conn->getConfiguration();
                    $config->setResultCacheImpl($this->Cache);
                    $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                    $ari = count($rswrk);
                    if ($ari > 0) { // Lookup values found
                        $arwrk = $this->ID_EQUIPOTORNEO->Lookup->renderViewRow($rswrk[0]);
                        $this->ID_EQUIPOTORNEO->ViewValue = $this->ID_EQUIPOTORNEO->displayValue($arwrk);
                    } else {
                        $this->ID_EQUIPOTORNEO->ViewValue = FormatNumber($this->ID_EQUIPOTORNEO->CurrentValue, $this->ID_EQUIPOTORNEO->formatPattern());
                    }
                }
            } else {
                $this->ID_EQUIPOTORNEO->ViewValue = null;
            }
            $this->ID_EQUIPOTORNEO->ViewCustomAttributes = "";

            // EQUIPO
            if ($this->EQUIPO->VirtualValue != "") {
                $this->EQUIPO->ViewValue = $this->EQUIPO->VirtualValue;
            } else {
                $curVal = strval($this->EQUIPO->CurrentValue);
                if ($curVal != "") {
                    $this->EQUIPO->ViewValue = $this->EQUIPO->lookupCacheOption($curVal);
                    if ($this->EQUIPO->ViewValue === null) { // Lookup from database
                        $filterWrk = "`NOM_EQUIPO_CORTO`" . SearchString("=", $curVal, DATATYPE_MEMO, "");
                        $sqlWrk = $this->EQUIPO->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                        $conn = Conn();
                        $config = $conn->getConfiguration();
                        $config->setResultCacheImpl($this->Cache);
                        $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                        $ari = count($rswrk);
                        if ($ari > 0) { // Lookup values found
                            $arwrk = $this->EQUIPO->Lookup->renderViewRow($rswrk[0]);
                            $this->EQUIPO->ViewValue = $this->EQUIPO->displayValue($arwrk);
                        } else {
                            $this->EQUIPO->ViewValue = $this->EQUIPO->CurrentValue;
                        }
                    }
                } else {
                    $this->EQUIPO->ViewValue = null;
                }
            }
            $this->EQUIPO->ViewCustomAttributes = "";

            // GRUPO
            $curVal = strval($this->GRUPO->CurrentValue);
            if ($curVal != "") {
                $this->GRUPO->ViewValue = $this->GRUPO->lookupCacheOption($curVal);
                if ($this->GRUPO->ViewValue === null) { // Lookup from database
                    $filterWrk = "`GRUPO`" . SearchString("=", $curVal, DATATYPE_MEMO, "");
                    $sqlWrk = $this->GRUPO->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                    $conn = Conn();
                    $config = $conn->getConfiguration();
                    $config->setResultCacheImpl($this->Cache);
                    $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                    $ari = count($rswrk);
                    if ($ari > 0) { // Lookup values found
                        $arwrk = $this->GRUPO->Lookup->renderViewRow($rswrk[0]);
                        $this->GRUPO->ViewValue = $this->GRUPO->displayValue($arwrk);
                    } else {
                        $this->GRUPO->ViewValue = $this->GRUPO->CurrentValue;
                    }
                }
            } else {
                $this->GRUPO->ViewValue = null;
            }
            $this->GRUPO->ViewCustomAttributes = "";

            // POSICION
            if (strval($this->POSICION->CurrentValue) != "") {
                $this->POSICION->ViewValue = $this->POSICION->optionCaption($this->POSICION->CurrentValue);
            } else {
                $this->POSICION->ViewValue = null;
            }
            $this->POSICION->ViewCustomAttributes = "";

            // NUMERACION
            if (strval($this->NUMERACION->CurrentValue) != "") {
                $this->NUMERACION->ViewValue = $this->NUMERACION->optionCaption($this->NUMERACION->CurrentValue);
            } else {
                $this->NUMERACION->ViewValue = null;
            }
            $this->NUMERACION->ViewCustomAttributes = "";

            // crea_dato
            $this->crea_dato->ViewValue = $this->crea_dato->CurrentValue;
            $this->crea_dato->ViewValue = FormatDateTime($this->crea_dato->ViewValue, $this->crea_dato->formatPattern());
            $this->crea_dato->ViewCustomAttributes = "";

            // modifica_dato
            $this->modifica_dato->ViewValue = $this->modifica_dato->CurrentValue;
            $this->modifica_dato->ViewValue = FormatDateTime($this->modifica_dato->ViewValue, $this->modifica_dato->formatPattern());
            $this->modifica_dato->ViewCustomAttributes = "";

            // usuario_dato
            $this->usuario_dato->ViewValue = $this->usuario_dato->CurrentValue;
            $this->usuario_dato->ViewCustomAttributes = "";

            // ID_PARTICIPANTE
            $curVal = strval($this->ID_PARTICIPANTE->CurrentValue);
            if ($curVal != "") {
                $this->ID_PARTICIPANTE->ViewValue = $this->ID_PARTICIPANTE->lookupCacheOption($curVal);
                if ($this->ID_PARTICIPANTE->ViewValue === null) { // Lookup from database
                    $filterWrk = "`ID_PARTICIPANTE`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                    $sqlWrk = $this->ID_PARTICIPANTE->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                    $conn = Conn();
                    $config = $conn->getConfiguration();
                    $config->setResultCacheImpl($this->Cache);
                    $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                    $ari = count($rswrk);
                    if ($ari > 0) { // Lookup values found
                        $arwrk = $this->ID_PARTICIPANTE->Lookup->renderViewRow($rswrk[0]);
                        $this->ID_PARTICIPANTE->ViewValue = $this->ID_PARTICIPANTE->displayValue($arwrk);
                    } else {
                        $this->ID_PARTICIPANTE->ViewValue = FormatNumber($this->ID_PARTICIPANTE->CurrentValue, $this->ID_PARTICIPANTE->formatPattern());
                    }
                }
            } else {
                $this->ID_PARTICIPANTE->ViewValue = null;
            }
            $this->ID_PARTICIPANTE->ViewCustomAttributes = "";

            // ID_ENCUESTA
            $this->ID_ENCUESTA->LinkCustomAttributes = "";
            $this->ID_ENCUESTA->HrefValue = "";

            // ID_EQUIPOTORNEO
            $this->ID_EQUIPOTORNEO->LinkCustomAttributes = "";
            $this->ID_EQUIPOTORNEO->HrefValue = "";

            // EQUIPO
            $this->EQUIPO->LinkCustomAttributes = "";
            $this->EQUIPO->HrefValue = "";

            // GRUPO
            $this->GRUPO->LinkCustomAttributes = "";
            $this->GRUPO->HrefValue = "";

            // POSICION
            $this->POSICION->LinkCustomAttributes = "";
            $this->POSICION->HrefValue = "";

            // NUMERACION
            $this->NUMERACION->LinkCustomAttributes = "";
            $this->NUMERACION->HrefValue = "";

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

            // ID_PARTICIPANTE
            $this->ID_PARTICIPANTE->LinkCustomAttributes = "";
            $this->ID_PARTICIPANTE->HrefValue = "";
        } elseif ($this->RowType == ROWTYPE_EDIT) {
            // ID_ENCUESTA
            $this->ID_ENCUESTA->setupEditAttributes();
            $this->ID_ENCUESTA->EditCustomAttributes = "";
            $this->ID_ENCUESTA->EditValue = $this->ID_ENCUESTA->CurrentValue;
            $this->ID_ENCUESTA->ViewCustomAttributes = "";

            // ID_EQUIPOTORNEO
            $this->ID_EQUIPOTORNEO->setupEditAttributes();
            $this->ID_EQUIPOTORNEO->EditCustomAttributes = "";
            $curVal = trim(strval($this->ID_EQUIPOTORNEO->CurrentValue));
            if ($curVal != "") {
                $this->ID_EQUIPOTORNEO->ViewValue = $this->ID_EQUIPOTORNEO->lookupCacheOption($curVal);
            } else {
                $this->ID_EQUIPOTORNEO->ViewValue = $this->ID_EQUIPOTORNEO->Lookup !== null && is_array($this->ID_EQUIPOTORNEO->lookupOptions()) ? $curVal : null;
            }
            if ($this->ID_EQUIPOTORNEO->ViewValue !== null) { // Load from cache
                $this->ID_EQUIPOTORNEO->EditValue = array_values($this->ID_EQUIPOTORNEO->lookupOptions());
            } else { // Lookup from database
                if ($curVal == "") {
                    $filterWrk = "0=1";
                } else {
                    $filterWrk = "`ID_EQUIPO_TORNEO`" . SearchString("=", $this->ID_EQUIPOTORNEO->CurrentValue, DATATYPE_NUMBER, "");
                }
                $sqlWrk = $this->ID_EQUIPOTORNEO->Lookup->getSql(true, $filterWrk, '', $this, false, true);
                $conn = Conn();
                $config = $conn->getConfiguration();
                $config->setResultCacheImpl($this->Cache);
                $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                $ari = count($rswrk);
                $arwrk = $rswrk;
                foreach ($arwrk as &$row) {
                    $row = $this->ID_EQUIPOTORNEO->Lookup->renderViewRow($row);
                }
                $this->ID_EQUIPOTORNEO->EditValue = $arwrk;
            }
            $this->ID_EQUIPOTORNEO->PlaceHolder = RemoveHtml($this->ID_EQUIPOTORNEO->caption());

            // EQUIPO
            $this->EQUIPO->setupEditAttributes();
            $this->EQUIPO->EditCustomAttributes = "";
            $curVal = trim(strval($this->EQUIPO->CurrentValue));
            if ($curVal != "") {
                $this->EQUIPO->ViewValue = $this->EQUIPO->lookupCacheOption($curVal);
            } else {
                $this->EQUIPO->ViewValue = $this->EQUIPO->Lookup !== null && is_array($this->EQUIPO->lookupOptions()) ? $curVal : null;
            }
            if ($this->EQUIPO->ViewValue !== null) { // Load from cache
                $this->EQUIPO->EditValue = array_values($this->EQUIPO->lookupOptions());
            } else { // Lookup from database
                if ($curVal == "") {
                    $filterWrk = "0=1";
                } else {
                    $filterWrk = "`NOM_EQUIPO_CORTO`" . SearchString("=", $this->EQUIPO->CurrentValue, DATATYPE_MEMO, "");
                }
                $sqlWrk = $this->EQUIPO->Lookup->getSql(true, $filterWrk, '', $this, false, true);
                $conn = Conn();
                $config = $conn->getConfiguration();
                $config->setResultCacheImpl($this->Cache);
                $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                $ari = count($rswrk);
                $arwrk = $rswrk;
                $this->EQUIPO->EditValue = $arwrk;
            }
            $this->EQUIPO->PlaceHolder = RemoveHtml($this->EQUIPO->caption());

            // GRUPO
            $this->GRUPO->setupEditAttributes();
            $this->GRUPO->EditCustomAttributes = "";
            $curVal = trim(strval($this->GRUPO->CurrentValue));
            if ($curVal != "") {
                $this->GRUPO->ViewValue = $this->GRUPO->lookupCacheOption($curVal);
            } else {
                $this->GRUPO->ViewValue = $this->GRUPO->Lookup !== null && is_array($this->GRUPO->lookupOptions()) ? $curVal : null;
            }
            if ($this->GRUPO->ViewValue !== null) { // Load from cache
                $this->GRUPO->EditValue = array_values($this->GRUPO->lookupOptions());
            } else { // Lookup from database
                if ($curVal == "") {
                    $filterWrk = "0=1";
                } else {
                    $filterWrk = "`GRUPO`" . SearchString("=", $this->GRUPO->CurrentValue, DATATYPE_MEMO, "");
                }
                $sqlWrk = $this->GRUPO->Lookup->getSql(true, $filterWrk, '', $this, false, true);
                $conn = Conn();
                $config = $conn->getConfiguration();
                $config->setResultCacheImpl($this->Cache);
                $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                $ari = count($rswrk);
                $arwrk = $rswrk;
                $this->GRUPO->EditValue = $arwrk;
            }
            $this->GRUPO->PlaceHolder = RemoveHtml($this->GRUPO->caption());

            // POSICION
            $this->POSICION->setupEditAttributes();
            $this->POSICION->EditCustomAttributes = "";
            $this->POSICION->EditValue = $this->POSICION->options(true);
            $this->POSICION->PlaceHolder = RemoveHtml($this->POSICION->caption());

            // NUMERACION
            $this->NUMERACION->setupEditAttributes();
            $this->NUMERACION->EditCustomAttributes = "";
            $this->NUMERACION->EditValue = $this->NUMERACION->options(true);
            $this->NUMERACION->PlaceHolder = RemoveHtml($this->NUMERACION->caption());

            // crea_dato
            $this->crea_dato->setupEditAttributes();
            $this->crea_dato->EditCustomAttributes = "";
            $this->crea_dato->EditValue = $this->crea_dato->CurrentValue;
            $this->crea_dato->EditValue = FormatDateTime($this->crea_dato->EditValue, $this->crea_dato->formatPattern());
            $this->crea_dato->ViewCustomAttributes = "";

            // modifica_dato
            $this->modifica_dato->setupEditAttributes();
            $this->modifica_dato->EditCustomAttributes = "";
            $this->modifica_dato->EditValue = $this->modifica_dato->CurrentValue;
            $this->modifica_dato->EditValue = FormatDateTime($this->modifica_dato->EditValue, $this->modifica_dato->formatPattern());
            $this->modifica_dato->ViewCustomAttributes = "";

            // usuario_dato

            // ID_PARTICIPANTE
            $this->ID_PARTICIPANTE->setupEditAttributes();
            $this->ID_PARTICIPANTE->EditCustomAttributes = "";
            $curVal = trim(strval($this->ID_PARTICIPANTE->CurrentValue));
            if ($curVal != "") {
                $this->ID_PARTICIPANTE->ViewValue = $this->ID_PARTICIPANTE->lookupCacheOption($curVal);
            } else {
                $this->ID_PARTICIPANTE->ViewValue = $this->ID_PARTICIPANTE->Lookup !== null && is_array($this->ID_PARTICIPANTE->lookupOptions()) ? $curVal : null;
            }
            if ($this->ID_PARTICIPANTE->ViewValue !== null) { // Load from cache
                $this->ID_PARTICIPANTE->EditValue = array_values($this->ID_PARTICIPANTE->lookupOptions());
            } else { // Lookup from database
                if ($curVal == "") {
                    $filterWrk = "0=1";
                } else {
                    $filterWrk = "`ID_PARTICIPANTE`" . SearchString("=", $this->ID_PARTICIPANTE->CurrentValue, DATATYPE_NUMBER, "");
                }
                $sqlWrk = $this->ID_PARTICIPANTE->Lookup->getSql(true, $filterWrk, '', $this, false, true);
                $conn = Conn();
                $config = $conn->getConfiguration();
                $config->setResultCacheImpl($this->Cache);
                $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                $ari = count($rswrk);
                $arwrk = $rswrk;
                $this->ID_PARTICIPANTE->EditValue = $arwrk;
            }
            $this->ID_PARTICIPANTE->PlaceHolder = RemoveHtml($this->ID_PARTICIPANTE->caption());

            // Edit refer script

            // ID_ENCUESTA
            $this->ID_ENCUESTA->LinkCustomAttributes = "";
            $this->ID_ENCUESTA->HrefValue = "";

            // ID_EQUIPOTORNEO
            $this->ID_EQUIPOTORNEO->LinkCustomAttributes = "";
            $this->ID_EQUIPOTORNEO->HrefValue = "";

            // EQUIPO
            $this->EQUIPO->LinkCustomAttributes = "";
            $this->EQUIPO->HrefValue = "";

            // GRUPO
            $this->GRUPO->LinkCustomAttributes = "";
            $this->GRUPO->HrefValue = "";

            // POSICION
            $this->POSICION->LinkCustomAttributes = "";
            $this->POSICION->HrefValue = "";

            // NUMERACION
            $this->NUMERACION->LinkCustomAttributes = "";
            $this->NUMERACION->HrefValue = "";

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

            // ID_PARTICIPANTE
            $this->ID_PARTICIPANTE->LinkCustomAttributes = "";
            $this->ID_PARTICIPANTE->HrefValue = "";
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
        if ($this->ID_ENCUESTA->Required) {
            if (!$this->ID_ENCUESTA->IsDetailKey && EmptyValue($this->ID_ENCUESTA->FormValue)) {
                $this->ID_ENCUESTA->addErrorMessage(str_replace("%s", $this->ID_ENCUESTA->caption(), $this->ID_ENCUESTA->RequiredErrorMessage));
            }
        }
        if ($this->ID_EQUIPOTORNEO->Required) {
            if (!$this->ID_EQUIPOTORNEO->IsDetailKey && EmptyValue($this->ID_EQUIPOTORNEO->FormValue)) {
                $this->ID_EQUIPOTORNEO->addErrorMessage(str_replace("%s", $this->ID_EQUIPOTORNEO->caption(), $this->ID_EQUIPOTORNEO->RequiredErrorMessage));
            }
        }
        if ($this->EQUIPO->Required) {
            if (!$this->EQUIPO->IsDetailKey && EmptyValue($this->EQUIPO->FormValue)) {
                $this->EQUIPO->addErrorMessage(str_replace("%s", $this->EQUIPO->caption(), $this->EQUIPO->RequiredErrorMessage));
            }
        }
        if ($this->GRUPO->Required) {
            if (!$this->GRUPO->IsDetailKey && EmptyValue($this->GRUPO->FormValue)) {
                $this->GRUPO->addErrorMessage(str_replace("%s", $this->GRUPO->caption(), $this->GRUPO->RequiredErrorMessage));
            }
        }
        if ($this->POSICION->Required) {
            if (!$this->POSICION->IsDetailKey && EmptyValue($this->POSICION->FormValue)) {
                $this->POSICION->addErrorMessage(str_replace("%s", $this->POSICION->caption(), $this->POSICION->RequiredErrorMessage));
            }
        }
        if ($this->NUMERACION->Required) {
            if (!$this->NUMERACION->IsDetailKey && EmptyValue($this->NUMERACION->FormValue)) {
                $this->NUMERACION->addErrorMessage(str_replace("%s", $this->NUMERACION->caption(), $this->NUMERACION->RequiredErrorMessage));
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
        if ($this->ID_PARTICIPANTE->Required) {
            if (!$this->ID_PARTICIPANTE->IsDetailKey && EmptyValue($this->ID_PARTICIPANTE->FormValue)) {
                $this->ID_PARTICIPANTE->addErrorMessage(str_replace("%s", $this->ID_PARTICIPANTE->caption(), $this->ID_PARTICIPANTE->RequiredErrorMessage));
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

    // Update record based on key values
    protected function editRow()
    {
        global $Security, $Language;
        $oldKeyFilter = $this->getRecordFilter();
        $filter = $this->applyUserIDFilters($oldKeyFilter);
        $conn = $this->getConnection();

        // Load old row
        $this->CurrentFilter = $filter;
        $sql = $this->getCurrentSql();
        $rsold = $conn->fetchAssociative($sql);
        if (!$rsold) {
            $this->setFailureMessage($Language->phrase("NoRecord")); // Set no record message
            return false; // Update Failed
        } else {
            // Save old values
            $this->loadDbValues($rsold);
        }

        // Set new row
        $rsnew = [];

        // ID_EQUIPOTORNEO
        $this->ID_EQUIPOTORNEO->setDbValueDef($rsnew, $this->ID_EQUIPOTORNEO->CurrentValue, 0, $this->ID_EQUIPOTORNEO->ReadOnly);

        // EQUIPO
        $this->EQUIPO->setDbValueDef($rsnew, $this->EQUIPO->CurrentValue, null, $this->EQUIPO->ReadOnly);

        // GRUPO
        $this->GRUPO->setDbValueDef($rsnew, $this->GRUPO->CurrentValue, null, $this->GRUPO->ReadOnly);

        // POSICION
        $this->POSICION->setDbValueDef($rsnew, $this->POSICION->CurrentValue, null, $this->POSICION->ReadOnly);

        // NUMERACION
        $this->NUMERACION->setDbValueDef($rsnew, $this->NUMERACION->CurrentValue, null, $this->NUMERACION->ReadOnly);

        // ID_PARTICIPANTE
        $this->ID_PARTICIPANTE->setDbValueDef($rsnew, $this->ID_PARTICIPANTE->CurrentValue, null, $this->ID_PARTICIPANTE->ReadOnly);

        // Update current values
        $this->setCurrentValues($rsnew);

            // Check hash value
            $rowHasConflict = (!IsApi() && $this->getRowHash($rsold) != $this->HashValue);

            // Call Row Update Conflict event
            if ($rowHasConflict) {
                $rowHasConflict = $this->rowUpdateConflict($rsold, $rsnew);
            }
            if ($rowHasConflict) {
                $this->setFailureMessage($Language->phrase("RecordChangedByOtherUser"));
                $this->UpdateConflict = "U";
                return false; // Update Failed
            }

        // Call Row Updating event
        $updateRow = $this->rowUpdating($rsold, $rsnew);
        if ($updateRow) {
            if (count($rsnew) > 0) {
                $this->CurrentFilter = $filter; // Set up current filter
                $editRow = $this->update($rsnew, "", $rsold);
            } else {
                $editRow = true; // No field to update
            }
            if ($editRow) {
            }
        } else {
            if ($this->getSuccessMessage() != "" || $this->getFailureMessage() != "") {
                // Use the message, do nothing
            } elseif ($this->CancelMessage != "") {
                $this->setFailureMessage($this->CancelMessage);
                $this->CancelMessage = "";
            } else {
                $this->setFailureMessage($Language->phrase("UpdateCancelled"));
            }
            $editRow = false;
        }

        // Call Row_Updated event
        if ($editRow) {
            $this->rowUpdated($rsold, $rsnew);
        }

        // Clean upload path if any
        if ($editRow) {
        }

        // Write JSON for API request
        if (IsApi() && $editRow) {
            $row = $this->getRecordsFromRecordset([$rsnew], true);
            WriteJson(["success" => true, $this->TableVar => $row]);
        }
        return $editRow;
    }

    // Load row hash
    protected function loadRowHash()
    {
        $filter = $this->getRecordFilter();

        // Load SQL based on filter
        $this->CurrentFilter = $filter;
        $sql = $this->getCurrentSql();
        $conn = $this->getConnection();
        $row = $conn->fetchAssociative($sql);
        $this->HashValue = $row ? $this->getRowHash($row) : ""; // Get hash value for record
    }

    // Get Row Hash
    public function getRowHash(&$rs)
    {
        if (!$rs) {
            return "";
        }
        $row = ($rs instanceof Recordset) ? $rs->fields : $rs;
        $hash = "";
        $hash .= GetFieldHash($row['ID_EQUIPOTORNEO']); // ID_EQUIPOTORNEO
        $hash .= GetFieldHash($row['EQUIPO']); // EQUIPO
        $hash .= GetFieldHash($row['GRUPO']); // GRUPO
        $hash .= GetFieldHash($row['POSICION']); // POSICION
        $hash .= GetFieldHash($row['NUMERACION']); // NUMERACION
        $hash .= GetFieldHash($row['ID_PARTICIPANTE']); // ID_PARTICIPANTE
        return md5($hash);
    }

    // Set up Breadcrumb
    protected function setupBreadcrumb()
    {
        global $Breadcrumb, $Language;
        $Breadcrumb = new Breadcrumb("index");
        $url = CurrentUrl();
        $Breadcrumb->add("list", $this->TableVar, $this->addMasterUrl("pronosticadorlist"), "", $this->TableVar, true);
        $pageId = "edit";
        $Breadcrumb->add("edit", $pageId, $url);
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
                case "x_ID_EQUIPOTORNEO":
                    break;
                case "x_EQUIPO":
                    break;
                case "x_GRUPO":
                    break;
                case "x_POSICION":
                    break;
                case "x_NUMERACION":
                    break;
                case "x_ID_PARTICIPANTE":
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

    // Set up starting record parameters
    public function setupStartRecord()
    {
        if ($this->DisplayRecords == 0) {
            return;
        }
        if ($this->isPageRequest()) { // Validate request
            $startRec = Get(Config("TABLE_START_REC"));
            if ($startRec !== null && is_numeric($startRec)) { // Check for "start" parameter
                $this->StartRecord = $startRec;
                $this->setStartRecordNumber($this->StartRecord);
            }
        }
        $this->StartRecord = $this->getStartRecordNumber();

        // Check if correct start record counter
        if (!is_numeric($this->StartRecord) || $this->StartRecord == "") { // Avoid invalid start record counter
            $this->StartRecord = 1; // Reset start record counter
            $this->setStartRecordNumber($this->StartRecord);
        } elseif ($this->StartRecord > $this->TotalRecords) { // Avoid starting record > total records
            $this->StartRecord = (int)(($this->TotalRecords - 1) / $this->DisplayRecords) * $this->DisplayRecords + 1; // Point to last page first record
            $this->setStartRecordNumber($this->StartRecord);
        } elseif (($this->StartRecord - 1) % $this->DisplayRecords != 0) {
            $this->StartRecord = (int)(($this->StartRecord - 1) / $this->DisplayRecords) * $this->DisplayRecords + 1; // Point to page boundary
            $this->setStartRecordNumber($this->StartRecord);
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

    // Form Custom Validate event
    public function formCustomValidate(&$customError)
    {
        // Return error message in $customError
        return true;
    }
}
