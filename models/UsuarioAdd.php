<?php

namespace PHPMaker2022\project1;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Page class
 */
class UsuarioAdd extends Usuario
{
    use MessagesTrait;

    // Page ID
    public $PageID = "add";

    // Project ID
    public $ProjectID = PROJECT_ID;

    // Table name
    public $TableName = 'usuario';

    // Page object name
    public $PageObjName = "UsuarioAdd";

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
        $args = $route->getArguments();
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

        // Table object (usuario)
        if (!isset($GLOBALS["usuario"]) || get_class($GLOBALS["usuario"]) == PROJECT_NAMESPACE . "usuario") {
            $GLOBALS["usuario"] = &$this;
        }

        // Table name (for backward compatibility only)
        if (!defined(PROJECT_NAMESPACE . "TABLE_NAME")) {
            define(PROJECT_NAMESPACE . "TABLE_NAME", 'usuario');
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
                $tbl = Container("usuario");
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
                    if ($pageName == "UsuarioView") {
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
            $key .= @$ar['ID_USUARIO'];
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
            $this->ID_USUARIO->Visible = false;
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
    public $FormClassName = "ew-form ew-add-form";
    public $IsModal = false;
    public $IsMobileOrModal = false;
    public $DbMasterFilter = "";
    public $DbDetailFilter = "";
    public $StartRecord;
    public $Priv = 0;
    public $OldRecordset;
    public $CopyRecord;

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
        $this->ID_USUARIO->Visible = false;
        $this->USER->setVisibility();
        $this->CONTRASENA->setVisibility();
        $this->nombre->setVisibility();
        $this->crea_dato->setVisibility();
        $this->modifica_dato->setVisibility();
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

        // Load default values for add
        $this->loadDefaultValues();

        // Check modal
        if ($this->IsModal) {
            $SkipHeaderFooter = true;
        }
        $this->IsMobileOrModal = IsMobile() || $this->IsModal;
        $this->FormClassName = "ew-form ew-add-form";
        $postBack = false;

        // Set up current action
        if (IsApi()) {
            $this->CurrentAction = "insert"; // Add record directly
            $postBack = true;
        } elseif (Post("action") !== null) {
            $this->CurrentAction = Post("action"); // Get form action
            $this->setKey(Post($this->OldKeyName));
            $postBack = true;
        } else {
            // Load key values from QueryString
            if (($keyValue = Get("ID_USUARIO") ?? Route("ID_USUARIO")) !== null) {
                $this->ID_USUARIO->setQueryStringValue($keyValue);
            }
            $this->OldKey = $this->getKey(true); // Get from CurrentValue
            $this->CopyRecord = !EmptyValue($this->OldKey);
            if ($this->CopyRecord) {
                $this->CurrentAction = "copy"; // Copy record
            } else {
                $this->CurrentAction = "show"; // Display blank record
            }
        }

        // Load old record / default values
        $loaded = $this->loadOldRecord();

        // Load form values
        if ($postBack) {
            $this->loadFormValues(); // Load form values
        }

        // Validate form if post back
        if ($postBack) {
            if (!$this->validateForm()) {
                $this->EventCancelled = true; // Event cancelled
                $this->restoreFormValues(); // Restore form values
                if (IsApi()) {
                    $this->terminate();
                    return;
                } else {
                    $this->CurrentAction = "show"; // Form error, reset action
                }
            }
        }

        // Perform current action
        switch ($this->CurrentAction) {
            case "copy": // Copy an existing record
                if (!$loaded) { // Record not loaded
                    if ($this->getFailureMessage() == "") {
                        $this->setFailureMessage($Language->phrase("NoRecord")); // No record found
                    }
                    $this->terminate("UsuarioList"); // No matching record, return to list
                    return;
                }
                break;
            case "insert": // Add new record
                $this->SendEmail = true; // Send email on add success
                if ($this->addRow($this->OldRecordset)) { // Add successful
                    if ($this->getSuccessMessage() == "" && Post("addopt") != "1") { // Skip success message for addopt (done in JavaScript)
                        $this->setSuccessMessage($Language->phrase("AddSuccess")); // Set up success message
                    }
                    $returnUrl = $this->getReturnUrl();
                    if (GetPageName($returnUrl) == "UsuarioList") {
                        $returnUrl = $this->addMasterUrl($returnUrl); // List page, return to List page with correct master key if necessary
                    } elseif (GetPageName($returnUrl) == "UsuarioView") {
                        $returnUrl = $this->getViewUrl(); // View page, return to View page with keyurl directly
                    }
                    if (IsApi()) { // Return to caller
                        $this->terminate(true);
                        return;
                    } else {
                        $this->terminate($returnUrl);
                        return;
                    }
                } elseif (IsApi()) { // API request, return
                    $this->terminate();
                    return;
                } else {
                    $this->EventCancelled = true; // Event cancelled
                    $this->restoreFormValues(); // Add failed, restore form values
                }
        }

        // Set up Breadcrumb
        $this->setupBreadcrumb();

        // Render row based on row type
        $this->RowType = ROWTYPE_ADD; // Render add type

        // Render row
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
        $this->crea_dato->DefaultValue = current_timestamp();
    }

    // Load form values
    protected function loadFormValues()
    {
        // Load from form
        global $CurrentForm;
        $validate = !Config("SERVER_VALIDATE");

        // Check field name 'USER' first before field var 'x_USER'
        $val = $CurrentForm->hasValue("USER") ? $CurrentForm->getValue("USER") : $CurrentForm->getValue("x_USER");
        if (!$this->USER->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->USER->Visible = false; // Disable update for API request
            } else {
                $this->USER->setFormValue($val);
            }
        }

        // Check field name 'CONTRASENA' first before field var 'x_CONTRASENA'
        $val = $CurrentForm->hasValue("CONTRASENA") ? $CurrentForm->getValue("CONTRASENA") : $CurrentForm->getValue("x_CONTRASENA");
        if (!$this->CONTRASENA->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->CONTRASENA->Visible = false; // Disable update for API request
            } else {
                $this->CONTRASENA->setFormValue($val);
            }
        }

        // Check field name 'nombre' first before field var 'x_nombre'
        $val = $CurrentForm->hasValue("nombre") ? $CurrentForm->getValue("nombre") : $CurrentForm->getValue("x_nombre");
        if (!$this->nombre->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->nombre->Visible = false; // Disable update for API request
            } else {
                $this->nombre->setFormValue($val);
            }
        }

        // Check field name 'crea_dato' first before field var 'x_crea_dato'
        $val = $CurrentForm->hasValue("crea_dato") ? $CurrentForm->getValue("crea_dato") : $CurrentForm->getValue("x_crea_dato");
        if (!$this->crea_dato->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->crea_dato->Visible = false; // Disable update for API request
            } else {
                $this->crea_dato->setFormValue($val, true, $validate);
            }
        }

        // Check field name 'modifica_dato' first before field var 'x_modifica_dato'
        $val = $CurrentForm->hasValue("modifica_dato") ? $CurrentForm->getValue("modifica_dato") : $CurrentForm->getValue("x_modifica_dato");
        if (!$this->modifica_dato->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->modifica_dato->Visible = false; // Disable update for API request
            } else {
                $this->modifica_dato->setFormValue($val, true, $validate);
            }
            $this->modifica_dato->CurrentValue = UnFormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern());
        }

        // Check field name 'ID_USUARIO' first before field var 'x_ID_USUARIO'
        $val = $CurrentForm->hasValue("ID_USUARIO") ? $CurrentForm->getValue("ID_USUARIO") : $CurrentForm->getValue("x_ID_USUARIO");
    }

    // Restore form values
    public function restoreFormValues()
    {
        global $CurrentForm;
        $this->USER->CurrentValue = $this->USER->FormValue;
        $this->CONTRASENA->CurrentValue = $this->CONTRASENA->FormValue;
        $this->nombre->CurrentValue = $this->nombre->FormValue;
        $this->crea_dato->CurrentValue = $this->crea_dato->FormValue;
        $this->modifica_dato->CurrentValue = $this->modifica_dato->FormValue;
        $this->modifica_dato->CurrentValue = UnFormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern());
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
        $this->ID_USUARIO->setDbValue($row['ID_USUARIO']);
        $this->USER->setDbValue($row['USER']);
        $this->CONTRASENA->setDbValue($row['CONTRASENA']);
        $this->nombre->setDbValue($row['nombre']);
        $this->crea_dato->setDbValue($row['crea_dato']);
        $this->modifica_dato->setDbValue($row['modifica_dato']);
    }

    // Return a row with default values
    protected function newRow()
    {
        $row = [];
        $row['ID_USUARIO'] = $this->ID_USUARIO->DefaultValue;
        $row['USER'] = $this->USER->DefaultValue;
        $row['CONTRASENA'] = $this->CONTRASENA->DefaultValue;
        $row['nombre'] = $this->nombre->DefaultValue;
        $row['crea_dato'] = $this->crea_dato->DefaultValue;
        $row['modifica_dato'] = $this->modifica_dato->DefaultValue;
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

        // ID_USUARIO
        $this->ID_USUARIO->RowCssClass = "row";

        // USER
        $this->USER->RowCssClass = "row";

        // CONTRASENA
        $this->CONTRASENA->RowCssClass = "row";

        // nombre
        $this->nombre->RowCssClass = "row";

        // crea_dato
        $this->crea_dato->RowCssClass = "row";

        // modifica_dato
        $this->modifica_dato->RowCssClass = "row";

        // View row
        if ($this->RowType == ROWTYPE_VIEW) {
            // ID_USUARIO
            $this->ID_USUARIO->ViewValue = $this->ID_USUARIO->CurrentValue;
            $this->ID_USUARIO->ViewCustomAttributes = "";

            // USER
            $this->USER->ViewValue = $this->USER->CurrentValue;
            $this->USER->ViewCustomAttributes = "";

            // CONTRASENA
            $this->CONTRASENA->ViewValue = $this->CONTRASENA->CurrentValue;
            $this->CONTRASENA->ViewCustomAttributes = "";

            // nombre
            $this->nombre->ViewValue = $this->nombre->CurrentValue;
            $this->nombre->ViewCustomAttributes = "";

            // crea_dato
            $this->crea_dato->ViewValue = $this->crea_dato->CurrentValue;
            $this->crea_dato->ViewValue = FormatNumber($this->crea_dato->ViewValue, $this->crea_dato->formatPattern());
            $this->crea_dato->ViewCustomAttributes = "";

            // modifica_dato
            $this->modifica_dato->ViewValue = $this->modifica_dato->CurrentValue;
            $this->modifica_dato->ViewValue = FormatDateTime($this->modifica_dato->ViewValue, $this->modifica_dato->formatPattern());
            $this->modifica_dato->ViewCustomAttributes = "";

            // USER
            $this->USER->LinkCustomAttributes = "";
            $this->USER->HrefValue = "";

            // CONTRASENA
            $this->CONTRASENA->LinkCustomAttributes = "";
            $this->CONTRASENA->HrefValue = "";

            // nombre
            $this->nombre->LinkCustomAttributes = "";
            $this->nombre->HrefValue = "";

            // crea_dato
            $this->crea_dato->LinkCustomAttributes = "";
            $this->crea_dato->HrefValue = "";

            // modifica_dato
            $this->modifica_dato->LinkCustomAttributes = "";
            $this->modifica_dato->HrefValue = "";
        } elseif ($this->RowType == ROWTYPE_ADD) {
            // USER
            $this->USER->setupEditAttributes();
            $this->USER->EditCustomAttributes = "";
            $this->USER->EditValue = HtmlEncode($this->USER->CurrentValue);
            $this->USER->PlaceHolder = RemoveHtml($this->USER->caption());

            // CONTRASENA
            $this->CONTRASENA->setupEditAttributes();
            $this->CONTRASENA->EditCustomAttributes = "";
            $this->CONTRASENA->EditValue = HtmlEncode($this->CONTRASENA->CurrentValue);
            $this->CONTRASENA->PlaceHolder = RemoveHtml($this->CONTRASENA->caption());

            // nombre
            $this->nombre->setupEditAttributes();
            $this->nombre->EditCustomAttributes = "";
            $this->nombre->EditValue = HtmlEncode($this->nombre->CurrentValue);
            $this->nombre->PlaceHolder = RemoveHtml($this->nombre->caption());

            // crea_dato
            $this->crea_dato->setupEditAttributes();
            $this->crea_dato->EditCustomAttributes = "";
            $this->crea_dato->EditValue = HtmlEncode($this->crea_dato->CurrentValue);
            $this->crea_dato->PlaceHolder = RemoveHtml($this->crea_dato->caption());
            if (strval($this->crea_dato->EditValue) != "" && is_numeric($this->crea_dato->EditValue)) {
                $this->crea_dato->EditValue = FormatNumber($this->crea_dato->EditValue, null);
            }

            // modifica_dato
            $this->modifica_dato->setupEditAttributes();
            $this->modifica_dato->EditCustomAttributes = "";
            $this->modifica_dato->EditValue = HtmlEncode(FormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern()));
            $this->modifica_dato->PlaceHolder = RemoveHtml($this->modifica_dato->caption());

            // Add refer script

            // USER
            $this->USER->LinkCustomAttributes = "";
            $this->USER->HrefValue = "";

            // CONTRASENA
            $this->CONTRASENA->LinkCustomAttributes = "";
            $this->CONTRASENA->HrefValue = "";

            // nombre
            $this->nombre->LinkCustomAttributes = "";
            $this->nombre->HrefValue = "";

            // crea_dato
            $this->crea_dato->LinkCustomAttributes = "";
            $this->crea_dato->HrefValue = "";

            // modifica_dato
            $this->modifica_dato->LinkCustomAttributes = "";
            $this->modifica_dato->HrefValue = "";
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
        if ($this->USER->Required) {
            if (!$this->USER->IsDetailKey && EmptyValue($this->USER->FormValue)) {
                $this->USER->addErrorMessage(str_replace("%s", $this->USER->caption(), $this->USER->RequiredErrorMessage));
            }
        }
        if ($this->CONTRASENA->Required) {
            if (!$this->CONTRASENA->IsDetailKey && EmptyValue($this->CONTRASENA->FormValue)) {
                $this->CONTRASENA->addErrorMessage(str_replace("%s", $this->CONTRASENA->caption(), $this->CONTRASENA->RequiredErrorMessage));
            }
        }
        if ($this->nombre->Required) {
            if (!$this->nombre->IsDetailKey && EmptyValue($this->nombre->FormValue)) {
                $this->nombre->addErrorMessage(str_replace("%s", $this->nombre->caption(), $this->nombre->RequiredErrorMessage));
            }
        }
        if ($this->crea_dato->Required) {
            if (!$this->crea_dato->IsDetailKey && EmptyValue($this->crea_dato->FormValue)) {
                $this->crea_dato->addErrorMessage(str_replace("%s", $this->crea_dato->caption(), $this->crea_dato->RequiredErrorMessage));
            }
        }
        if (!CheckInteger($this->crea_dato->FormValue)) {
            $this->crea_dato->addErrorMessage($this->crea_dato->getErrorMessage(false));
        }
        if ($this->modifica_dato->Required) {
            if (!$this->modifica_dato->IsDetailKey && EmptyValue($this->modifica_dato->FormValue)) {
                $this->modifica_dato->addErrorMessage(str_replace("%s", $this->modifica_dato->caption(), $this->modifica_dato->RequiredErrorMessage));
            }
        }
        if (!CheckDate($this->modifica_dato->FormValue, $this->modifica_dato->formatPattern())) {
            $this->modifica_dato->addErrorMessage($this->modifica_dato->getErrorMessage(false));
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

    // Add record
    protected function addRow($rsold = null)
    {
        global $Language, $Security;

        // Set new row
        $rsnew = [];

        // USER
        $this->USER->setDbValueDef($rsnew, $this->USER->CurrentValue, null, false);

        // CONTRASENA
        $this->CONTRASENA->setDbValueDef($rsnew, $this->CONTRASENA->CurrentValue, null, false);

        // nombre
        $this->nombre->setDbValueDef($rsnew, $this->nombre->CurrentValue, "", false);

        // crea_dato
        $this->crea_dato->setDbValueDef($rsnew, $this->crea_dato->CurrentValue, 0, strval($this->crea_dato->CurrentValue ?? "") == "");

        // modifica_dato
        $this->modifica_dato->setDbValueDef($rsnew, UnFormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern()), CurrentDate(), false);

        // Update current values
        $this->setCurrentValues($rsnew);
        $conn = $this->getConnection();

        // Load db values from old row
        $this->loadDbValues($rsold);
        if ($rsold) {
        }

        // Call Row Inserting event
        $insertRow = $this->rowInserting($rsold, $rsnew);
        if ($insertRow) {
            $addRow = $this->insert($rsnew);
            if ($addRow) {
            }
        } else {
            if ($this->getSuccessMessage() != "" || $this->getFailureMessage() != "") {
                // Use the message, do nothing
            } elseif ($this->CancelMessage != "") {
                $this->setFailureMessage($this->CancelMessage);
                $this->CancelMessage = "";
            } else {
                $this->setFailureMessage($Language->phrase("InsertCancelled"));
            }
            $addRow = false;
        }
        if ($addRow) {
            // Call Row Inserted event
            $this->rowInserted($rsold, $rsnew);
        }

        // Clean upload path if any
        if ($addRow) {
        }

        // Write JSON for API request
        if (IsApi() && $addRow) {
            $row = $this->getRecordsFromRecordset([$rsnew], true);
            WriteJson(["success" => true, $this->TableVar => $row]);
        }
        return $addRow;
    }

    // Set up Breadcrumb
    protected function setupBreadcrumb()
    {
        global $Breadcrumb, $Language;
        $Breadcrumb = new Breadcrumb("index");
        $url = CurrentUrl();
        $Breadcrumb->add("list", $this->TableVar, $this->addMasterUrl("UsuarioList"), "", $this->TableVar, true);
        $pageId = ($this->isCopy()) ? "Copy" : "Add";
        $Breadcrumb->add("add", $pageId, $url);
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

    // Form Custom Validate event
    public function formCustomValidate(&$customError)
    {
        // Return error message in $customError
        return true;
    }
}
