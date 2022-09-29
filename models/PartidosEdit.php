<?php

namespace PHPMaker2022\project11;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Page class
 */
class PartidosEdit extends Partidos
{
    use MessagesTrait;

    // Page ID
    public $PageID = "edit";

    // Project ID
    public $ProjectID = PROJECT_ID;

    // Table name
    public $TableName = 'partidos';

    // Page object name
    public $PageObjName = "PartidosEdit";

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

        // Table object (partidos)
        if (!isset($GLOBALS["partidos"]) || get_class($GLOBALS["partidos"]) == PROJECT_NAMESPACE . "partidos") {
            $GLOBALS["partidos"] = &$this;
        }

        // Table name (for backward compatibility only)
        if (!defined(PROJECT_NAMESPACE . "TABLE_NAME")) {
            define(PROJECT_NAMESPACE . "TABLE_NAME", 'partidos');
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
                $tbl = Container("partidos");
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
                    if ($pageName == "partidosview") {
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
            $key .= @$ar['ID_PARTIDO'];
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
            $this->ID_PARTIDO->Visible = false;
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
        $this->ID_TORNEO->setVisibility();
        $this->equipo_local->setVisibility();
        $this->equipo_visitante->setVisibility();
        $this->ID_PARTIDO->setVisibility();
        $this->FECHA_PARTIDO->setVisibility();
        $this->HORA_PARTIDO->setVisibility();
        $this->ESTADIO->setVisibility();
        $this->CIUDAD_PARTIDO->setVisibility();
        $this->PAIS_PARTIDO->setVisibility();
        $this->GOLES_LOCAL->setVisibility();
        $this->GOLES_VISITANTE->setVisibility();
        $this->GOLES_EXTRA_EQUIPO1->setVisibility();
        $this->GOLES_EXTRA_EQUIPO2->setVisibility();
        $this->NOTA_PARTIDO->setVisibility();
        $this->RESUMEN_PARTIDO->setVisibility();
        $this->ESTADO_PARTIDO->setVisibility();
        $this->crea_dato->setVisibility();
        $this->modifica_dato->setVisibility();
        $this->usuario_dato->setVisibility();
        $this->automatico->setVisibility();
        $this->actualizado->setVisibility();
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
        $this->setupLookupOptions($this->equipo_local);
        $this->setupLookupOptions($this->equipo_visitante);
        $this->setupLookupOptions($this->ESTADIO);
        $this->setupLookupOptions($this->PAIS_PARTIDO);
        $this->setupLookupOptions($this->ESTADO_PARTIDO);
        $this->setupLookupOptions($this->automatico);

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
            if (($keyValue = Get("ID_PARTIDO") ?? Key(0) ?? Route(2)) !== null) {
                $this->ID_PARTIDO->setQueryStringValue($keyValue);
                $this->ID_PARTIDO->setOldValue($this->ID_PARTIDO->QueryStringValue);
            } elseif (Post("ID_PARTIDO") !== null) {
                $this->ID_PARTIDO->setFormValue(Post("ID_PARTIDO"));
                $this->ID_PARTIDO->setOldValue($this->ID_PARTIDO->FormValue);
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
                if (($keyValue = Get("ID_PARTIDO") ?? Route("ID_PARTIDO")) !== null) {
                    $this->ID_PARTIDO->setQueryStringValue($keyValue);
                    $loadByQuery = true;
                } else {
                    $this->ID_PARTIDO->CurrentValue = null;
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
                        $this->terminate("partidoslist"); // No matching record, return to list
                        return;
                    }
                break;
            case "update": // Update
                $returnUrl = $this->getReturnUrl();
                if (GetPageName($returnUrl) == "partidoslist") {
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

        // Check field name 'ID_TORNEO' first before field var 'x_ID_TORNEO'
        $val = $CurrentForm->hasValue("ID_TORNEO") ? $CurrentForm->getValue("ID_TORNEO") : $CurrentForm->getValue("x_ID_TORNEO");
        if (!$this->ID_TORNEO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->ID_TORNEO->Visible = false; // Disable update for API request
            } else {
                $this->ID_TORNEO->setFormValue($val);
            }
        }

        // Check field name 'equipo_local' first before field var 'x_equipo_local'
        $val = $CurrentForm->hasValue("equipo_local") ? $CurrentForm->getValue("equipo_local") : $CurrentForm->getValue("x_equipo_local");
        if (!$this->equipo_local->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->equipo_local->Visible = false; // Disable update for API request
            } else {
                $this->equipo_local->setFormValue($val);
            }
        }

        // Check field name 'equipo_visitante' first before field var 'x_equipo_visitante'
        $val = $CurrentForm->hasValue("equipo_visitante") ? $CurrentForm->getValue("equipo_visitante") : $CurrentForm->getValue("x_equipo_visitante");
        if (!$this->equipo_visitante->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->equipo_visitante->Visible = false; // Disable update for API request
            } else {
                $this->equipo_visitante->setFormValue($val);
            }
        }

        // Check field name 'ID_PARTIDO' first before field var 'x_ID_PARTIDO'
        $val = $CurrentForm->hasValue("ID_PARTIDO") ? $CurrentForm->getValue("ID_PARTIDO") : $CurrentForm->getValue("x_ID_PARTIDO");
        if (!$this->ID_PARTIDO->IsDetailKey) {
            $this->ID_PARTIDO->setFormValue($val);
        }

        // Check field name 'FECHA_PARTIDO' first before field var 'x_FECHA_PARTIDO'
        $val = $CurrentForm->hasValue("FECHA_PARTIDO") ? $CurrentForm->getValue("FECHA_PARTIDO") : $CurrentForm->getValue("x_FECHA_PARTIDO");
        if (!$this->FECHA_PARTIDO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->FECHA_PARTIDO->Visible = false; // Disable update for API request
            } else {
                $this->FECHA_PARTIDO->setFormValue($val, true, $validate);
            }
            $this->FECHA_PARTIDO->CurrentValue = UnFormatDateTime($this->FECHA_PARTIDO->CurrentValue, $this->FECHA_PARTIDO->formatPattern());
        }

        // Check field name 'HORA_PARTIDO' first before field var 'x_HORA_PARTIDO'
        $val = $CurrentForm->hasValue("HORA_PARTIDO") ? $CurrentForm->getValue("HORA_PARTIDO") : $CurrentForm->getValue("x_HORA_PARTIDO");
        if (!$this->HORA_PARTIDO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->HORA_PARTIDO->Visible = false; // Disable update for API request
            } else {
                $this->HORA_PARTIDO->setFormValue($val, true, $validate);
            }
            $this->HORA_PARTIDO->CurrentValue = UnFormatDateTime($this->HORA_PARTIDO->CurrentValue, $this->HORA_PARTIDO->formatPattern());
        }

        // Check field name 'ESTADIO' first before field var 'x_ESTADIO'
        $val = $CurrentForm->hasValue("ESTADIO") ? $CurrentForm->getValue("ESTADIO") : $CurrentForm->getValue("x_ESTADIO");
        if (!$this->ESTADIO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->ESTADIO->Visible = false; // Disable update for API request
            } else {
                $this->ESTADIO->setFormValue($val);
            }
        }

        // Check field name 'CIUDAD_PARTIDO' first before field var 'x_CIUDAD_PARTIDO'
        $val = $CurrentForm->hasValue("CIUDAD_PARTIDO") ? $CurrentForm->getValue("CIUDAD_PARTIDO") : $CurrentForm->getValue("x_CIUDAD_PARTIDO");
        if (!$this->CIUDAD_PARTIDO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->CIUDAD_PARTIDO->Visible = false; // Disable update for API request
            } else {
                $this->CIUDAD_PARTIDO->setFormValue($val);
            }
        }

        // Check field name 'PAIS_PARTIDO' first before field var 'x_PAIS_PARTIDO'
        $val = $CurrentForm->hasValue("PAIS_PARTIDO") ? $CurrentForm->getValue("PAIS_PARTIDO") : $CurrentForm->getValue("x_PAIS_PARTIDO");
        if (!$this->PAIS_PARTIDO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->PAIS_PARTIDO->Visible = false; // Disable update for API request
            } else {
                $this->PAIS_PARTIDO->setFormValue($val);
            }
        }

        // Check field name 'GOLES_LOCAL' first before field var 'x_GOLES_LOCAL'
        $val = $CurrentForm->hasValue("GOLES_LOCAL") ? $CurrentForm->getValue("GOLES_LOCAL") : $CurrentForm->getValue("x_GOLES_LOCAL");
        if (!$this->GOLES_LOCAL->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->GOLES_LOCAL->Visible = false; // Disable update for API request
            } else {
                $this->GOLES_LOCAL->setFormValue($val, true, $validate);
            }
        }

        // Check field name 'GOLES_VISITANTE' first before field var 'x_GOLES_VISITANTE'
        $val = $CurrentForm->hasValue("GOLES_VISITANTE") ? $CurrentForm->getValue("GOLES_VISITANTE") : $CurrentForm->getValue("x_GOLES_VISITANTE");
        if (!$this->GOLES_VISITANTE->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->GOLES_VISITANTE->Visible = false; // Disable update for API request
            } else {
                $this->GOLES_VISITANTE->setFormValue($val, true, $validate);
            }
        }

        // Check field name 'GOLES_EXTRA_EQUIPO1' first before field var 'x_GOLES_EXTRA_EQUIPO1'
        $val = $CurrentForm->hasValue("GOLES_EXTRA_EQUIPO1") ? $CurrentForm->getValue("GOLES_EXTRA_EQUIPO1") : $CurrentForm->getValue("x_GOLES_EXTRA_EQUIPO1");
        if (!$this->GOLES_EXTRA_EQUIPO1->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->GOLES_EXTRA_EQUIPO1->Visible = false; // Disable update for API request
            } else {
                $this->GOLES_EXTRA_EQUIPO1->setFormValue($val, true, $validate);
            }
        }

        // Check field name 'GOLES_EXTRA_EQUIPO2' first before field var 'x_GOLES_EXTRA_EQUIPO2'
        $val = $CurrentForm->hasValue("GOLES_EXTRA_EQUIPO2") ? $CurrentForm->getValue("GOLES_EXTRA_EQUIPO2") : $CurrentForm->getValue("x_GOLES_EXTRA_EQUIPO2");
        if (!$this->GOLES_EXTRA_EQUIPO2->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->GOLES_EXTRA_EQUIPO2->Visible = false; // Disable update for API request
            } else {
                $this->GOLES_EXTRA_EQUIPO2->setFormValue($val, true, $validate);
            }
        }

        // Check field name 'NOTA_PARTIDO' first before field var 'x_NOTA_PARTIDO'
        $val = $CurrentForm->hasValue("NOTA_PARTIDO") ? $CurrentForm->getValue("NOTA_PARTIDO") : $CurrentForm->getValue("x_NOTA_PARTIDO");
        if (!$this->NOTA_PARTIDO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->NOTA_PARTIDO->Visible = false; // Disable update for API request
            } else {
                $this->NOTA_PARTIDO->setFormValue($val);
            }
        }

        // Check field name 'RESUMEN_PARTIDO' first before field var 'x_RESUMEN_PARTIDO'
        $val = $CurrentForm->hasValue("RESUMEN_PARTIDO") ? $CurrentForm->getValue("RESUMEN_PARTIDO") : $CurrentForm->getValue("x_RESUMEN_PARTIDO");
        if (!$this->RESUMEN_PARTIDO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->RESUMEN_PARTIDO->Visible = false; // Disable update for API request
            } else {
                $this->RESUMEN_PARTIDO->setFormValue($val);
            }
        }

        // Check field name 'ESTADO_PARTIDO' first before field var 'x_ESTADO_PARTIDO'
        $val = $CurrentForm->hasValue("ESTADO_PARTIDO") ? $CurrentForm->getValue("ESTADO_PARTIDO") : $CurrentForm->getValue("x_ESTADO_PARTIDO");
        if (!$this->ESTADO_PARTIDO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->ESTADO_PARTIDO->Visible = false; // Disable update for API request
            } else {
                $this->ESTADO_PARTIDO->setFormValue($val);
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

        // Check field name 'automatico' first before field var 'x_automatico'
        $val = $CurrentForm->hasValue("automatico") ? $CurrentForm->getValue("automatico") : $CurrentForm->getValue("x_automatico");
        if (!$this->automatico->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->automatico->Visible = false; // Disable update for API request
            } else {
                $this->automatico->setFormValue($val);
            }
        }

        // Check field name 'actualizado' first before field var 'x_actualizado'
        $val = $CurrentForm->hasValue("actualizado") ? $CurrentForm->getValue("actualizado") : $CurrentForm->getValue("x_actualizado");
        if (!$this->actualizado->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->actualizado->Visible = false; // Disable update for API request
            } else {
                $this->actualizado->setFormValue($val);
            }
        }
    }

    // Restore form values
    public function restoreFormValues()
    {
        global $CurrentForm;
        $this->ID_TORNEO->CurrentValue = $this->ID_TORNEO->FormValue;
        $this->equipo_local->CurrentValue = $this->equipo_local->FormValue;
        $this->equipo_visitante->CurrentValue = $this->equipo_visitante->FormValue;
        $this->ID_PARTIDO->CurrentValue = $this->ID_PARTIDO->FormValue;
        $this->FECHA_PARTIDO->CurrentValue = $this->FECHA_PARTIDO->FormValue;
        $this->FECHA_PARTIDO->CurrentValue = UnFormatDateTime($this->FECHA_PARTIDO->CurrentValue, $this->FECHA_PARTIDO->formatPattern());
        $this->HORA_PARTIDO->CurrentValue = $this->HORA_PARTIDO->FormValue;
        $this->HORA_PARTIDO->CurrentValue = UnFormatDateTime($this->HORA_PARTIDO->CurrentValue, $this->HORA_PARTIDO->formatPattern());
        $this->ESTADIO->CurrentValue = $this->ESTADIO->FormValue;
        $this->CIUDAD_PARTIDO->CurrentValue = $this->CIUDAD_PARTIDO->FormValue;
        $this->PAIS_PARTIDO->CurrentValue = $this->PAIS_PARTIDO->FormValue;
        $this->GOLES_LOCAL->CurrentValue = $this->GOLES_LOCAL->FormValue;
        $this->GOLES_VISITANTE->CurrentValue = $this->GOLES_VISITANTE->FormValue;
        $this->GOLES_EXTRA_EQUIPO1->CurrentValue = $this->GOLES_EXTRA_EQUIPO1->FormValue;
        $this->GOLES_EXTRA_EQUIPO2->CurrentValue = $this->GOLES_EXTRA_EQUIPO2->FormValue;
        $this->NOTA_PARTIDO->CurrentValue = $this->NOTA_PARTIDO->FormValue;
        $this->RESUMEN_PARTIDO->CurrentValue = $this->RESUMEN_PARTIDO->FormValue;
        $this->ESTADO_PARTIDO->CurrentValue = $this->ESTADO_PARTIDO->FormValue;
        $this->crea_dato->CurrentValue = $this->crea_dato->FormValue;
        $this->crea_dato->CurrentValue = UnFormatDateTime($this->crea_dato->CurrentValue, $this->crea_dato->formatPattern());
        $this->modifica_dato->CurrentValue = $this->modifica_dato->FormValue;
        $this->modifica_dato->CurrentValue = UnFormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern());
        $this->usuario_dato->CurrentValue = $this->usuario_dato->FormValue;
        $this->automatico->CurrentValue = $this->automatico->FormValue;
        $this->actualizado->CurrentValue = $this->actualizado->FormValue;
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
        $this->ID_TORNEO->setDbValue($row['ID_TORNEO']);
        $this->equipo_local->setDbValue($row['equipo_local']);
        if (array_key_exists('EV__equipo_local', $row)) {
            $this->equipo_local->VirtualValue = $row['EV__equipo_local']; // Set up virtual field value
        } else {
            $this->equipo_local->VirtualValue = ""; // Clear value
        }
        $this->equipo_visitante->setDbValue($row['equipo_visitante']);
        if (array_key_exists('EV__equipo_visitante', $row)) {
            $this->equipo_visitante->VirtualValue = $row['EV__equipo_visitante']; // Set up virtual field value
        } else {
            $this->equipo_visitante->VirtualValue = ""; // Clear value
        }
        $this->ID_PARTIDO->setDbValue($row['ID_PARTIDO']);
        $this->FECHA_PARTIDO->setDbValue($row['FECHA_PARTIDO']);
        $this->HORA_PARTIDO->setDbValue($row['HORA_PARTIDO']);
        $this->ESTADIO->setDbValue($row['ESTADIO']);
        $this->CIUDAD_PARTIDO->setDbValue($row['CIUDAD_PARTIDO']);
        $this->PAIS_PARTIDO->setDbValue($row['PAIS_PARTIDO']);
        $this->GOLES_LOCAL->setDbValue($row['GOLES_LOCAL']);
        $this->GOLES_VISITANTE->setDbValue($row['GOLES_VISITANTE']);
        $this->GOLES_EXTRA_EQUIPO1->setDbValue($row['GOLES_EXTRA_EQUIPO1']);
        $this->GOLES_EXTRA_EQUIPO2->setDbValue($row['GOLES_EXTRA_EQUIPO2']);
        $this->NOTA_PARTIDO->setDbValue($row['NOTA_PARTIDO']);
        $this->RESUMEN_PARTIDO->setDbValue($row['RESUMEN_PARTIDO']);
        $this->ESTADO_PARTIDO->setDbValue($row['ESTADO_PARTIDO']);
        $this->crea_dato->setDbValue($row['crea_dato']);
        $this->modifica_dato->setDbValue($row['modifica_dato']);
        $this->usuario_dato->setDbValue($row['usuario_dato']);
        $this->automatico->setDbValue($row['automatico']);
        $this->actualizado->setDbValue($row['actualizado']);
    }

    // Return a row with default values
    protected function newRow()
    {
        $row = [];
        $row['ID_TORNEO'] = $this->ID_TORNEO->DefaultValue;
        $row['equipo_local'] = $this->equipo_local->DefaultValue;
        $row['equipo_visitante'] = $this->equipo_visitante->DefaultValue;
        $row['ID_PARTIDO'] = $this->ID_PARTIDO->DefaultValue;
        $row['FECHA_PARTIDO'] = $this->FECHA_PARTIDO->DefaultValue;
        $row['HORA_PARTIDO'] = $this->HORA_PARTIDO->DefaultValue;
        $row['ESTADIO'] = $this->ESTADIO->DefaultValue;
        $row['CIUDAD_PARTIDO'] = $this->CIUDAD_PARTIDO->DefaultValue;
        $row['PAIS_PARTIDO'] = $this->PAIS_PARTIDO->DefaultValue;
        $row['GOLES_LOCAL'] = $this->GOLES_LOCAL->DefaultValue;
        $row['GOLES_VISITANTE'] = $this->GOLES_VISITANTE->DefaultValue;
        $row['GOLES_EXTRA_EQUIPO1'] = $this->GOLES_EXTRA_EQUIPO1->DefaultValue;
        $row['GOLES_EXTRA_EQUIPO2'] = $this->GOLES_EXTRA_EQUIPO2->DefaultValue;
        $row['NOTA_PARTIDO'] = $this->NOTA_PARTIDO->DefaultValue;
        $row['RESUMEN_PARTIDO'] = $this->RESUMEN_PARTIDO->DefaultValue;
        $row['ESTADO_PARTIDO'] = $this->ESTADO_PARTIDO->DefaultValue;
        $row['crea_dato'] = $this->crea_dato->DefaultValue;
        $row['modifica_dato'] = $this->modifica_dato->DefaultValue;
        $row['usuario_dato'] = $this->usuario_dato->DefaultValue;
        $row['automatico'] = $this->automatico->DefaultValue;
        $row['actualizado'] = $this->actualizado->DefaultValue;
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

        // ID_TORNEO
        $this->ID_TORNEO->RowCssClass = "row";

        // equipo_local
        $this->equipo_local->RowCssClass = "row";

        // equipo_visitante
        $this->equipo_visitante->RowCssClass = "row";

        // ID_PARTIDO
        $this->ID_PARTIDO->RowCssClass = "row";

        // FECHA_PARTIDO
        $this->FECHA_PARTIDO->RowCssClass = "row";

        // HORA_PARTIDO
        $this->HORA_PARTIDO->RowCssClass = "row";

        // ESTADIO
        $this->ESTADIO->RowCssClass = "row";

        // CIUDAD_PARTIDO
        $this->CIUDAD_PARTIDO->RowCssClass = "row";

        // PAIS_PARTIDO
        $this->PAIS_PARTIDO->RowCssClass = "row";

        // GOLES_LOCAL
        $this->GOLES_LOCAL->RowCssClass = "row";

        // GOLES_VISITANTE
        $this->GOLES_VISITANTE->RowCssClass = "row";

        // GOLES_EXTRA_EQUIPO1
        $this->GOLES_EXTRA_EQUIPO1->RowCssClass = "row";

        // GOLES_EXTRA_EQUIPO2
        $this->GOLES_EXTRA_EQUIPO2->RowCssClass = "row";

        // NOTA_PARTIDO
        $this->NOTA_PARTIDO->RowCssClass = "row";

        // RESUMEN_PARTIDO
        $this->RESUMEN_PARTIDO->RowCssClass = "row";

        // ESTADO_PARTIDO
        $this->ESTADO_PARTIDO->RowCssClass = "row";

        // crea_dato
        $this->crea_dato->RowCssClass = "row";

        // modifica_dato
        $this->modifica_dato->RowCssClass = "row";

        // usuario_dato
        $this->usuario_dato->RowCssClass = "row";

        // automatico
        $this->automatico->RowCssClass = "row";

        // actualizado
        $this->actualizado->RowCssClass = "row";

        // View row
        if ($this->RowType == ROWTYPE_VIEW) {
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

            // equipo_local
            if ($this->equipo_local->VirtualValue != "") {
                $this->equipo_local->ViewValue = $this->equipo_local->VirtualValue;
            } else {
                $curVal = strval($this->equipo_local->CurrentValue);
                if ($curVal != "") {
                    $this->equipo_local->ViewValue = $this->equipo_local->lookupCacheOption($curVal);
                    if ($this->equipo_local->ViewValue === null) { // Lookup from database
                        $filterWrk = "`ID_EQUIPO`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                        $sqlWrk = $this->equipo_local->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                        $conn = Conn();
                        $config = $conn->getConfiguration();
                        $config->setResultCacheImpl($this->Cache);
                        $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                        $ari = count($rswrk);
                        if ($ari > 0) { // Lookup values found
                            $arwrk = $this->equipo_local->Lookup->renderViewRow($rswrk[0]);
                            $this->equipo_local->ViewValue = $this->equipo_local->displayValue($arwrk);
                        } else {
                            $this->equipo_local->ViewValue = FormatNumber($this->equipo_local->CurrentValue, $this->equipo_local->formatPattern());
                        }
                    }
                } else {
                    $this->equipo_local->ViewValue = null;
                }
            }
            $this->equipo_local->ViewCustomAttributes = "";

            // equipo_visitante
            if ($this->equipo_visitante->VirtualValue != "") {
                $this->equipo_visitante->ViewValue = $this->equipo_visitante->VirtualValue;
            } else {
                $curVal = strval($this->equipo_visitante->CurrentValue);
                if ($curVal != "") {
                    $this->equipo_visitante->ViewValue = $this->equipo_visitante->lookupCacheOption($curVal);
                    if ($this->equipo_visitante->ViewValue === null) { // Lookup from database
                        $filterWrk = "`ID_EQUIPO`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                        $sqlWrk = $this->equipo_visitante->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                        $conn = Conn();
                        $config = $conn->getConfiguration();
                        $config->setResultCacheImpl($this->Cache);
                        $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                        $ari = count($rswrk);
                        if ($ari > 0) { // Lookup values found
                            $arwrk = $this->equipo_visitante->Lookup->renderViewRow($rswrk[0]);
                            $this->equipo_visitante->ViewValue = $this->equipo_visitante->displayValue($arwrk);
                        } else {
                            $this->equipo_visitante->ViewValue = FormatNumber($this->equipo_visitante->CurrentValue, $this->equipo_visitante->formatPattern());
                        }
                    }
                } else {
                    $this->equipo_visitante->ViewValue = null;
                }
            }
            $this->equipo_visitante->ViewCustomAttributes = "";

            // ID_PARTIDO
            $this->ID_PARTIDO->ViewValue = $this->ID_PARTIDO->CurrentValue;
            $this->ID_PARTIDO->ViewCustomAttributes = "";

            // FECHA_PARTIDO
            $this->FECHA_PARTIDO->ViewValue = $this->FECHA_PARTIDO->CurrentValue;
            $this->FECHA_PARTIDO->ViewValue = FormatDateTime($this->FECHA_PARTIDO->ViewValue, $this->FECHA_PARTIDO->formatPattern());
            $this->FECHA_PARTIDO->ViewCustomAttributes = "";

            // HORA_PARTIDO
            $this->HORA_PARTIDO->ViewValue = $this->HORA_PARTIDO->CurrentValue;
            $this->HORA_PARTIDO->ViewValue = FormatDateTime($this->HORA_PARTIDO->ViewValue, $this->HORA_PARTIDO->formatPattern());
            $this->HORA_PARTIDO->ViewCustomAttributes = "";

            // ESTADIO
            $curVal = strval($this->ESTADIO->CurrentValue);
            if ($curVal != "") {
                $this->ESTADIO->ViewValue = $this->ESTADIO->lookupCacheOption($curVal);
                if ($this->ESTADIO->ViewValue === null) { // Lookup from database
                    $filterWrk = "`id_estadio`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                    $sqlWrk = $this->ESTADIO->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                    $conn = Conn();
                    $config = $conn->getConfiguration();
                    $config->setResultCacheImpl($this->Cache);
                    $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                    $ari = count($rswrk);
                    if ($ari > 0) { // Lookup values found
                        $arwrk = $this->ESTADIO->Lookup->renderViewRow($rswrk[0]);
                        $this->ESTADIO->ViewValue = $this->ESTADIO->displayValue($arwrk);
                    } else {
                        $this->ESTADIO->ViewValue = FormatNumber($this->ESTADIO->CurrentValue, $this->ESTADIO->formatPattern());
                    }
                }
            } else {
                $this->ESTADIO->ViewValue = null;
            }
            $this->ESTADIO->ViewCustomAttributes = "";

            // CIUDAD_PARTIDO
            $this->CIUDAD_PARTIDO->ViewValue = $this->CIUDAD_PARTIDO->CurrentValue;
            $this->CIUDAD_PARTIDO->ViewCustomAttributes = "";

            // PAIS_PARTIDO
            $curVal = strval($this->PAIS_PARTIDO->CurrentValue);
            if ($curVal != "") {
                $this->PAIS_PARTIDO->ViewValue = $this->PAIS_PARTIDO->lookupCacheOption($curVal);
                if ($this->PAIS_PARTIDO->ViewValue === null) { // Lookup from database
                    $filterWrk = "`PAIS_EQUIPO`" . SearchString("=", $curVal, DATATYPE_MEMO, "");
                    $sqlWrk = $this->PAIS_PARTIDO->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                    $conn = Conn();
                    $config = $conn->getConfiguration();
                    $config->setResultCacheImpl($this->Cache);
                    $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                    $ari = count($rswrk);
                    if ($ari > 0) { // Lookup values found
                        $arwrk = $this->PAIS_PARTIDO->Lookup->renderViewRow($rswrk[0]);
                        $this->PAIS_PARTIDO->ViewValue = $this->PAIS_PARTIDO->displayValue($arwrk);
                    } else {
                        $this->PAIS_PARTIDO->ViewValue = $this->PAIS_PARTIDO->CurrentValue;
                    }
                }
            } else {
                $this->PAIS_PARTIDO->ViewValue = null;
            }
            $this->PAIS_PARTIDO->ViewCustomAttributes = "";

            // GOLES_LOCAL
            $this->GOLES_LOCAL->ViewValue = $this->GOLES_LOCAL->CurrentValue;
            $this->GOLES_LOCAL->ViewValue = FormatNumber($this->GOLES_LOCAL->ViewValue, $this->GOLES_LOCAL->formatPattern());
            $this->GOLES_LOCAL->ViewCustomAttributes = "";

            // GOLES_VISITANTE
            $this->GOLES_VISITANTE->ViewValue = $this->GOLES_VISITANTE->CurrentValue;
            $this->GOLES_VISITANTE->ViewValue = FormatNumber($this->GOLES_VISITANTE->ViewValue, $this->GOLES_VISITANTE->formatPattern());
            $this->GOLES_VISITANTE->ViewCustomAttributes = "";

            // GOLES_EXTRA_EQUIPO1
            $this->GOLES_EXTRA_EQUIPO1->ViewValue = $this->GOLES_EXTRA_EQUIPO1->CurrentValue;
            $this->GOLES_EXTRA_EQUIPO1->ViewValue = FormatNumber($this->GOLES_EXTRA_EQUIPO1->ViewValue, $this->GOLES_EXTRA_EQUIPO1->formatPattern());
            $this->GOLES_EXTRA_EQUIPO1->ViewCustomAttributes = "";

            // GOLES_EXTRA_EQUIPO2
            $this->GOLES_EXTRA_EQUIPO2->ViewValue = $this->GOLES_EXTRA_EQUIPO2->CurrentValue;
            $this->GOLES_EXTRA_EQUIPO2->ViewValue = FormatNumber($this->GOLES_EXTRA_EQUIPO2->ViewValue, $this->GOLES_EXTRA_EQUIPO2->formatPattern());
            $this->GOLES_EXTRA_EQUIPO2->ViewCustomAttributes = "";

            // NOTA_PARTIDO
            $this->NOTA_PARTIDO->ViewValue = $this->NOTA_PARTIDO->CurrentValue;
            $this->NOTA_PARTIDO->ViewCustomAttributes = "";

            // RESUMEN_PARTIDO
            $this->RESUMEN_PARTIDO->ViewValue = $this->RESUMEN_PARTIDO->CurrentValue;
            $this->RESUMEN_PARTIDO->ViewCustomAttributes = "";

            // ESTADO_PARTIDO
            if (strval($this->ESTADO_PARTIDO->CurrentValue) != "") {
                $this->ESTADO_PARTIDO->ViewValue = $this->ESTADO_PARTIDO->optionCaption($this->ESTADO_PARTIDO->CurrentValue);
            } else {
                $this->ESTADO_PARTIDO->ViewValue = null;
            }
            $this->ESTADO_PARTIDO->ViewCustomAttributes = "";

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

            // automatico
            if (ConvertToBool($this->automatico->CurrentValue)) {
                $this->automatico->ViewValue = $this->automatico->tagCaption(1) != "" ? $this->automatico->tagCaption(1) : "Yes";
            } else {
                $this->automatico->ViewValue = $this->automatico->tagCaption(2) != "" ? $this->automatico->tagCaption(2) : "No";
            }
            $this->automatico->ViewCustomAttributes = "";

            // actualizado
            $this->actualizado->ViewValue = $this->actualizado->CurrentValue;
            $this->actualizado->ViewCustomAttributes = "";

            // ID_TORNEO
            $this->ID_TORNEO->LinkCustomAttributes = "";
            $this->ID_TORNEO->HrefValue = "";

            // equipo_local
            $this->equipo_local->LinkCustomAttributes = "";
            $this->equipo_local->HrefValue = "";

            // equipo_visitante
            $this->equipo_visitante->LinkCustomAttributes = "";
            $this->equipo_visitante->HrefValue = "";

            // ID_PARTIDO
            $this->ID_PARTIDO->LinkCustomAttributes = "";
            $this->ID_PARTIDO->HrefValue = "";

            // FECHA_PARTIDO
            $this->FECHA_PARTIDO->LinkCustomAttributes = "";
            $this->FECHA_PARTIDO->HrefValue = "";

            // HORA_PARTIDO
            $this->HORA_PARTIDO->LinkCustomAttributes = "";
            $this->HORA_PARTIDO->HrefValue = "";

            // ESTADIO
            $this->ESTADIO->LinkCustomAttributes = "";
            $this->ESTADIO->HrefValue = "";

            // CIUDAD_PARTIDO
            $this->CIUDAD_PARTIDO->LinkCustomAttributes = "";
            $this->CIUDAD_PARTIDO->HrefValue = "";

            // PAIS_PARTIDO
            $this->PAIS_PARTIDO->LinkCustomAttributes = "";
            $this->PAIS_PARTIDO->HrefValue = "";

            // GOLES_LOCAL
            $this->GOLES_LOCAL->LinkCustomAttributes = "";
            $this->GOLES_LOCAL->HrefValue = "";

            // GOLES_VISITANTE
            $this->GOLES_VISITANTE->LinkCustomAttributes = "";
            $this->GOLES_VISITANTE->HrefValue = "";

            // GOLES_EXTRA_EQUIPO1
            $this->GOLES_EXTRA_EQUIPO1->LinkCustomAttributes = "";
            $this->GOLES_EXTRA_EQUIPO1->HrefValue = "";

            // GOLES_EXTRA_EQUIPO2
            $this->GOLES_EXTRA_EQUIPO2->LinkCustomAttributes = "";
            $this->GOLES_EXTRA_EQUIPO2->HrefValue = "";

            // NOTA_PARTIDO
            $this->NOTA_PARTIDO->LinkCustomAttributes = "";
            $this->NOTA_PARTIDO->HrefValue = "";

            // RESUMEN_PARTIDO
            $this->RESUMEN_PARTIDO->LinkCustomAttributes = "";
            $this->RESUMEN_PARTIDO->HrefValue = "";

            // ESTADO_PARTIDO
            $this->ESTADO_PARTIDO->LinkCustomAttributes = "";
            $this->ESTADO_PARTIDO->HrefValue = "";

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

            // automatico
            $this->automatico->LinkCustomAttributes = "";
            $this->automatico->HrefValue = "";

            // actualizado
            $this->actualizado->LinkCustomAttributes = "";
            $this->actualizado->HrefValue = "";
            $this->actualizado->TooltipValue = "";
        } elseif ($this->RowType == ROWTYPE_EDIT) {
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

            // equipo_local
            $this->equipo_local->setupEditAttributes();
            $this->equipo_local->EditCustomAttributes = "";
            $curVal = trim(strval($this->equipo_local->CurrentValue));
            if ($curVal != "") {
                $this->equipo_local->ViewValue = $this->equipo_local->lookupCacheOption($curVal);
            } else {
                $this->equipo_local->ViewValue = $this->equipo_local->Lookup !== null && is_array($this->equipo_local->lookupOptions()) ? $curVal : null;
            }
            if ($this->equipo_local->ViewValue !== null) { // Load from cache
                $this->equipo_local->EditValue = array_values($this->equipo_local->lookupOptions());
            } else { // Lookup from database
                if ($curVal == "") {
                    $filterWrk = "0=1";
                } else {
                    $filterWrk = "`ID_EQUIPO`" . SearchString("=", $this->equipo_local->CurrentValue, DATATYPE_NUMBER, "");
                }
                $sqlWrk = $this->equipo_local->Lookup->getSql(true, $filterWrk, '', $this, false, true);
                $conn = Conn();
                $config = $conn->getConfiguration();
                $config->setResultCacheImpl($this->Cache);
                $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                $ari = count($rswrk);
                $arwrk = $rswrk;
                foreach ($arwrk as &$row) {
                    $row = $this->equipo_local->Lookup->renderViewRow($row);
                }
                $this->equipo_local->EditValue = $arwrk;
            }
            $this->equipo_local->PlaceHolder = RemoveHtml($this->equipo_local->caption());

            // equipo_visitante
            $this->equipo_visitante->setupEditAttributes();
            $this->equipo_visitante->EditCustomAttributes = "";
            $curVal = trim(strval($this->equipo_visitante->CurrentValue));
            if ($curVal != "") {
                $this->equipo_visitante->ViewValue = $this->equipo_visitante->lookupCacheOption($curVal);
            } else {
                $this->equipo_visitante->ViewValue = $this->equipo_visitante->Lookup !== null && is_array($this->equipo_visitante->lookupOptions()) ? $curVal : null;
            }
            if ($this->equipo_visitante->ViewValue !== null) { // Load from cache
                $this->equipo_visitante->EditValue = array_values($this->equipo_visitante->lookupOptions());
            } else { // Lookup from database
                if ($curVal == "") {
                    $filterWrk = "0=1";
                } else {
                    $filterWrk = "`ID_EQUIPO`" . SearchString("=", $this->equipo_visitante->CurrentValue, DATATYPE_NUMBER, "");
                }
                $sqlWrk = $this->equipo_visitante->Lookup->getSql(true, $filterWrk, '', $this, false, true);
                $conn = Conn();
                $config = $conn->getConfiguration();
                $config->setResultCacheImpl($this->Cache);
                $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                $ari = count($rswrk);
                $arwrk = $rswrk;
                foreach ($arwrk as &$row) {
                    $row = $this->equipo_visitante->Lookup->renderViewRow($row);
                }
                $this->equipo_visitante->EditValue = $arwrk;
            }
            $this->equipo_visitante->PlaceHolder = RemoveHtml($this->equipo_visitante->caption());

            // ID_PARTIDO
            $this->ID_PARTIDO->setupEditAttributes();
            $this->ID_PARTIDO->EditCustomAttributes = "";
            $this->ID_PARTIDO->EditValue = $this->ID_PARTIDO->CurrentValue;
            $this->ID_PARTIDO->ViewCustomAttributes = "";

            // FECHA_PARTIDO
            $this->FECHA_PARTIDO->setupEditAttributes();
            $this->FECHA_PARTIDO->EditCustomAttributes = "";
            $this->FECHA_PARTIDO->EditValue = HtmlEncode(FormatDateTime($this->FECHA_PARTIDO->CurrentValue, $this->FECHA_PARTIDO->formatPattern()));
            $this->FECHA_PARTIDO->PlaceHolder = RemoveHtml($this->FECHA_PARTIDO->caption());

            // HORA_PARTIDO
            $this->HORA_PARTIDO->setupEditAttributes();
            $this->HORA_PARTIDO->EditCustomAttributes = "";
            $this->HORA_PARTIDO->EditValue = HtmlEncode(FormatDateTime($this->HORA_PARTIDO->CurrentValue, $this->HORA_PARTIDO->formatPattern()));
            $this->HORA_PARTIDO->PlaceHolder = RemoveHtml($this->HORA_PARTIDO->caption());

            // ESTADIO
            $this->ESTADIO->setupEditAttributes();
            $this->ESTADIO->EditCustomAttributes = "";
            $curVal = trim(strval($this->ESTADIO->CurrentValue));
            if ($curVal != "") {
                $this->ESTADIO->ViewValue = $this->ESTADIO->lookupCacheOption($curVal);
            } else {
                $this->ESTADIO->ViewValue = $this->ESTADIO->Lookup !== null && is_array($this->ESTADIO->lookupOptions()) ? $curVal : null;
            }
            if ($this->ESTADIO->ViewValue !== null) { // Load from cache
                $this->ESTADIO->EditValue = array_values($this->ESTADIO->lookupOptions());
            } else { // Lookup from database
                if ($curVal == "") {
                    $filterWrk = "0=1";
                } else {
                    $filterWrk = "`id_estadio`" . SearchString("=", $this->ESTADIO->CurrentValue, DATATYPE_NUMBER, "");
                }
                $sqlWrk = $this->ESTADIO->Lookup->getSql(true, $filterWrk, '', $this, false, true);
                $conn = Conn();
                $config = $conn->getConfiguration();
                $config->setResultCacheImpl($this->Cache);
                $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                $ari = count($rswrk);
                $arwrk = $rswrk;
                $this->ESTADIO->EditValue = $arwrk;
            }
            $this->ESTADIO->PlaceHolder = RemoveHtml($this->ESTADIO->caption());

            // CIUDAD_PARTIDO
            $this->CIUDAD_PARTIDO->setupEditAttributes();
            $this->CIUDAD_PARTIDO->EditCustomAttributes = "";
            $this->CIUDAD_PARTIDO->EditValue = HtmlEncode($this->CIUDAD_PARTIDO->CurrentValue);
            $this->CIUDAD_PARTIDO->PlaceHolder = RemoveHtml($this->CIUDAD_PARTIDO->caption());

            // PAIS_PARTIDO
            $this->PAIS_PARTIDO->setupEditAttributes();
            $this->PAIS_PARTIDO->EditCustomAttributes = "";
            $curVal = trim(strval($this->PAIS_PARTIDO->CurrentValue));
            if ($curVal != "") {
                $this->PAIS_PARTIDO->ViewValue = $this->PAIS_PARTIDO->lookupCacheOption($curVal);
            } else {
                $this->PAIS_PARTIDO->ViewValue = $this->PAIS_PARTIDO->Lookup !== null && is_array($this->PAIS_PARTIDO->lookupOptions()) ? $curVal : null;
            }
            if ($this->PAIS_PARTIDO->ViewValue !== null) { // Load from cache
                $this->PAIS_PARTIDO->EditValue = array_values($this->PAIS_PARTIDO->lookupOptions());
            } else { // Lookup from database
                if ($curVal == "") {
                    $filterWrk = "0=1";
                } else {
                    $filterWrk = "`PAIS_EQUIPO`" . SearchString("=", $this->PAIS_PARTIDO->CurrentValue, DATATYPE_MEMO, "");
                }
                $sqlWrk = $this->PAIS_PARTIDO->Lookup->getSql(true, $filterWrk, '', $this, false, true);
                $conn = Conn();
                $config = $conn->getConfiguration();
                $config->setResultCacheImpl($this->Cache);
                $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                $ari = count($rswrk);
                $arwrk = $rswrk;
                $this->PAIS_PARTIDO->EditValue = $arwrk;
            }
            $this->PAIS_PARTIDO->PlaceHolder = RemoveHtml($this->PAIS_PARTIDO->caption());

            // GOLES_LOCAL
            $this->GOLES_LOCAL->setupEditAttributes();
            $this->GOLES_LOCAL->EditCustomAttributes = "";
            $this->GOLES_LOCAL->EditValue = HtmlEncode($this->GOLES_LOCAL->CurrentValue);
            $this->GOLES_LOCAL->PlaceHolder = RemoveHtml($this->GOLES_LOCAL->caption());
            if (strval($this->GOLES_LOCAL->EditValue) != "" && is_numeric($this->GOLES_LOCAL->EditValue)) {
                $this->GOLES_LOCAL->EditValue = FormatNumber($this->GOLES_LOCAL->EditValue, null);
            }

            // GOLES_VISITANTE
            $this->GOLES_VISITANTE->setupEditAttributes();
            $this->GOLES_VISITANTE->EditCustomAttributes = "";
            $this->GOLES_VISITANTE->EditValue = HtmlEncode($this->GOLES_VISITANTE->CurrentValue);
            $this->GOLES_VISITANTE->PlaceHolder = RemoveHtml($this->GOLES_VISITANTE->caption());
            if (strval($this->GOLES_VISITANTE->EditValue) != "" && is_numeric($this->GOLES_VISITANTE->EditValue)) {
                $this->GOLES_VISITANTE->EditValue = FormatNumber($this->GOLES_VISITANTE->EditValue, null);
            }

            // GOLES_EXTRA_EQUIPO1
            $this->GOLES_EXTRA_EQUIPO1->setupEditAttributes();
            $this->GOLES_EXTRA_EQUIPO1->EditCustomAttributes = "";
            $this->GOLES_EXTRA_EQUIPO1->EditValue = HtmlEncode($this->GOLES_EXTRA_EQUIPO1->CurrentValue);
            $this->GOLES_EXTRA_EQUIPO1->PlaceHolder = RemoveHtml($this->GOLES_EXTRA_EQUIPO1->caption());
            if (strval($this->GOLES_EXTRA_EQUIPO1->EditValue) != "" && is_numeric($this->GOLES_EXTRA_EQUIPO1->EditValue)) {
                $this->GOLES_EXTRA_EQUIPO1->EditValue = FormatNumber($this->GOLES_EXTRA_EQUIPO1->EditValue, null);
            }

            // GOLES_EXTRA_EQUIPO2
            $this->GOLES_EXTRA_EQUIPO2->setupEditAttributes();
            $this->GOLES_EXTRA_EQUIPO2->EditCustomAttributes = "";
            $this->GOLES_EXTRA_EQUIPO2->EditValue = HtmlEncode($this->GOLES_EXTRA_EQUIPO2->CurrentValue);
            $this->GOLES_EXTRA_EQUIPO2->PlaceHolder = RemoveHtml($this->GOLES_EXTRA_EQUIPO2->caption());
            if (strval($this->GOLES_EXTRA_EQUIPO2->EditValue) != "" && is_numeric($this->GOLES_EXTRA_EQUIPO2->EditValue)) {
                $this->GOLES_EXTRA_EQUIPO2->EditValue = FormatNumber($this->GOLES_EXTRA_EQUIPO2->EditValue, null);
            }

            // NOTA_PARTIDO
            $this->NOTA_PARTIDO->setupEditAttributes();
            $this->NOTA_PARTIDO->EditCustomAttributes = "";
            $this->NOTA_PARTIDO->EditValue = HtmlEncode($this->NOTA_PARTIDO->CurrentValue);
            $this->NOTA_PARTIDO->PlaceHolder = RemoveHtml($this->NOTA_PARTIDO->caption());

            // RESUMEN_PARTIDO
            $this->RESUMEN_PARTIDO->setupEditAttributes();
            $this->RESUMEN_PARTIDO->EditCustomAttributes = "";
            $this->RESUMEN_PARTIDO->EditValue = HtmlEncode($this->RESUMEN_PARTIDO->CurrentValue);
            $this->RESUMEN_PARTIDO->PlaceHolder = RemoveHtml($this->RESUMEN_PARTIDO->caption());

            // ESTADO_PARTIDO
            $this->ESTADO_PARTIDO->setupEditAttributes();
            $this->ESTADO_PARTIDO->EditCustomAttributes = "";
            $this->ESTADO_PARTIDO->EditValue = $this->ESTADO_PARTIDO->options(true);
            $this->ESTADO_PARTIDO->PlaceHolder = RemoveHtml($this->ESTADO_PARTIDO->caption());

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

            // automatico
            $this->automatico->EditCustomAttributes = "";
            $this->automatico->EditValue = $this->automatico->options(false);
            $this->automatico->PlaceHolder = RemoveHtml($this->automatico->caption());

            // actualizado
            $this->actualizado->setupEditAttributes();
            $this->actualizado->EditCustomAttributes = "";

            // Edit refer script

            // ID_TORNEO
            $this->ID_TORNEO->LinkCustomAttributes = "";
            $this->ID_TORNEO->HrefValue = "";

            // equipo_local
            $this->equipo_local->LinkCustomAttributes = "";
            $this->equipo_local->HrefValue = "";

            // equipo_visitante
            $this->equipo_visitante->LinkCustomAttributes = "";
            $this->equipo_visitante->HrefValue = "";

            // ID_PARTIDO
            $this->ID_PARTIDO->LinkCustomAttributes = "";
            $this->ID_PARTIDO->HrefValue = "";

            // FECHA_PARTIDO
            $this->FECHA_PARTIDO->LinkCustomAttributes = "";
            $this->FECHA_PARTIDO->HrefValue = "";

            // HORA_PARTIDO
            $this->HORA_PARTIDO->LinkCustomAttributes = "";
            $this->HORA_PARTIDO->HrefValue = "";

            // ESTADIO
            $this->ESTADIO->LinkCustomAttributes = "";
            $this->ESTADIO->HrefValue = "";

            // CIUDAD_PARTIDO
            $this->CIUDAD_PARTIDO->LinkCustomAttributes = "";
            $this->CIUDAD_PARTIDO->HrefValue = "";

            // PAIS_PARTIDO
            $this->PAIS_PARTIDO->LinkCustomAttributes = "";
            $this->PAIS_PARTIDO->HrefValue = "";

            // GOLES_LOCAL
            $this->GOLES_LOCAL->LinkCustomAttributes = "";
            $this->GOLES_LOCAL->HrefValue = "";

            // GOLES_VISITANTE
            $this->GOLES_VISITANTE->LinkCustomAttributes = "";
            $this->GOLES_VISITANTE->HrefValue = "";

            // GOLES_EXTRA_EQUIPO1
            $this->GOLES_EXTRA_EQUIPO1->LinkCustomAttributes = "";
            $this->GOLES_EXTRA_EQUIPO1->HrefValue = "";

            // GOLES_EXTRA_EQUIPO2
            $this->GOLES_EXTRA_EQUIPO2->LinkCustomAttributes = "";
            $this->GOLES_EXTRA_EQUIPO2->HrefValue = "";

            // NOTA_PARTIDO
            $this->NOTA_PARTIDO->LinkCustomAttributes = "";
            $this->NOTA_PARTIDO->HrefValue = "";

            // RESUMEN_PARTIDO
            $this->RESUMEN_PARTIDO->LinkCustomAttributes = "";
            $this->RESUMEN_PARTIDO->HrefValue = "";

            // ESTADO_PARTIDO
            $this->ESTADO_PARTIDO->LinkCustomAttributes = "";
            $this->ESTADO_PARTIDO->HrefValue = "";

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

            // automatico
            $this->automatico->LinkCustomAttributes = "";
            $this->automatico->HrefValue = "";

            // actualizado
            $this->actualizado->LinkCustomAttributes = "";
            $this->actualizado->HrefValue = "";
            $this->actualizado->TooltipValue = "";
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
        if ($this->equipo_local->Required) {
            if (!$this->equipo_local->IsDetailKey && EmptyValue($this->equipo_local->FormValue)) {
                $this->equipo_local->addErrorMessage(str_replace("%s", $this->equipo_local->caption(), $this->equipo_local->RequiredErrorMessage));
            }
        }
        if ($this->equipo_visitante->Required) {
            if (!$this->equipo_visitante->IsDetailKey && EmptyValue($this->equipo_visitante->FormValue)) {
                $this->equipo_visitante->addErrorMessage(str_replace("%s", $this->equipo_visitante->caption(), $this->equipo_visitante->RequiredErrorMessage));
            }
        }
        if ($this->ID_PARTIDO->Required) {
            if (!$this->ID_PARTIDO->IsDetailKey && EmptyValue($this->ID_PARTIDO->FormValue)) {
                $this->ID_PARTIDO->addErrorMessage(str_replace("%s", $this->ID_PARTIDO->caption(), $this->ID_PARTIDO->RequiredErrorMessage));
            }
        }
        if ($this->FECHA_PARTIDO->Required) {
            if (!$this->FECHA_PARTIDO->IsDetailKey && EmptyValue($this->FECHA_PARTIDO->FormValue)) {
                $this->FECHA_PARTIDO->addErrorMessage(str_replace("%s", $this->FECHA_PARTIDO->caption(), $this->FECHA_PARTIDO->RequiredErrorMessage));
            }
        }
        if (!CheckDate($this->FECHA_PARTIDO->FormValue, $this->FECHA_PARTIDO->formatPattern())) {
            $this->FECHA_PARTIDO->addErrorMessage($this->FECHA_PARTIDO->getErrorMessage(false));
        }
        if ($this->HORA_PARTIDO->Required) {
            if (!$this->HORA_PARTIDO->IsDetailKey && EmptyValue($this->HORA_PARTIDO->FormValue)) {
                $this->HORA_PARTIDO->addErrorMessage(str_replace("%s", $this->HORA_PARTIDO->caption(), $this->HORA_PARTIDO->RequiredErrorMessage));
            }
        }
        if (!CheckTime($this->HORA_PARTIDO->FormValue, $this->HORA_PARTIDO->formatPattern())) {
            $this->HORA_PARTIDO->addErrorMessage($this->HORA_PARTIDO->getErrorMessage(false));
        }
        if ($this->ESTADIO->Required) {
            if (!$this->ESTADIO->IsDetailKey && EmptyValue($this->ESTADIO->FormValue)) {
                $this->ESTADIO->addErrorMessage(str_replace("%s", $this->ESTADIO->caption(), $this->ESTADIO->RequiredErrorMessage));
            }
        }
        if ($this->CIUDAD_PARTIDO->Required) {
            if (!$this->CIUDAD_PARTIDO->IsDetailKey && EmptyValue($this->CIUDAD_PARTIDO->FormValue)) {
                $this->CIUDAD_PARTIDO->addErrorMessage(str_replace("%s", $this->CIUDAD_PARTIDO->caption(), $this->CIUDAD_PARTIDO->RequiredErrorMessage));
            }
        }
        if ($this->PAIS_PARTIDO->Required) {
            if (!$this->PAIS_PARTIDO->IsDetailKey && EmptyValue($this->PAIS_PARTIDO->FormValue)) {
                $this->PAIS_PARTIDO->addErrorMessage(str_replace("%s", $this->PAIS_PARTIDO->caption(), $this->PAIS_PARTIDO->RequiredErrorMessage));
            }
        }
        if ($this->GOLES_LOCAL->Required) {
            if (!$this->GOLES_LOCAL->IsDetailKey && EmptyValue($this->GOLES_LOCAL->FormValue)) {
                $this->GOLES_LOCAL->addErrorMessage(str_replace("%s", $this->GOLES_LOCAL->caption(), $this->GOLES_LOCAL->RequiredErrorMessage));
            }
        }
        if (!CheckInteger($this->GOLES_LOCAL->FormValue)) {
            $this->GOLES_LOCAL->addErrorMessage($this->GOLES_LOCAL->getErrorMessage(false));
        }
        if ($this->GOLES_VISITANTE->Required) {
            if (!$this->GOLES_VISITANTE->IsDetailKey && EmptyValue($this->GOLES_VISITANTE->FormValue)) {
                $this->GOLES_VISITANTE->addErrorMessage(str_replace("%s", $this->GOLES_VISITANTE->caption(), $this->GOLES_VISITANTE->RequiredErrorMessage));
            }
        }
        if (!CheckInteger($this->GOLES_VISITANTE->FormValue)) {
            $this->GOLES_VISITANTE->addErrorMessage($this->GOLES_VISITANTE->getErrorMessage(false));
        }
        if ($this->GOLES_EXTRA_EQUIPO1->Required) {
            if (!$this->GOLES_EXTRA_EQUIPO1->IsDetailKey && EmptyValue($this->GOLES_EXTRA_EQUIPO1->FormValue)) {
                $this->GOLES_EXTRA_EQUIPO1->addErrorMessage(str_replace("%s", $this->GOLES_EXTRA_EQUIPO1->caption(), $this->GOLES_EXTRA_EQUIPO1->RequiredErrorMessage));
            }
        }
        if (!CheckInteger($this->GOLES_EXTRA_EQUIPO1->FormValue)) {
            $this->GOLES_EXTRA_EQUIPO1->addErrorMessage($this->GOLES_EXTRA_EQUIPO1->getErrorMessage(false));
        }
        if ($this->GOLES_EXTRA_EQUIPO2->Required) {
            if (!$this->GOLES_EXTRA_EQUIPO2->IsDetailKey && EmptyValue($this->GOLES_EXTRA_EQUIPO2->FormValue)) {
                $this->GOLES_EXTRA_EQUIPO2->addErrorMessage(str_replace("%s", $this->GOLES_EXTRA_EQUIPO2->caption(), $this->GOLES_EXTRA_EQUIPO2->RequiredErrorMessage));
            }
        }
        if (!CheckInteger($this->GOLES_EXTRA_EQUIPO2->FormValue)) {
            $this->GOLES_EXTRA_EQUIPO2->addErrorMessage($this->GOLES_EXTRA_EQUIPO2->getErrorMessage(false));
        }
        if ($this->NOTA_PARTIDO->Required) {
            if (!$this->NOTA_PARTIDO->IsDetailKey && EmptyValue($this->NOTA_PARTIDO->FormValue)) {
                $this->NOTA_PARTIDO->addErrorMessage(str_replace("%s", $this->NOTA_PARTIDO->caption(), $this->NOTA_PARTIDO->RequiredErrorMessage));
            }
        }
        if ($this->RESUMEN_PARTIDO->Required) {
            if (!$this->RESUMEN_PARTIDO->IsDetailKey && EmptyValue($this->RESUMEN_PARTIDO->FormValue)) {
                $this->RESUMEN_PARTIDO->addErrorMessage(str_replace("%s", $this->RESUMEN_PARTIDO->caption(), $this->RESUMEN_PARTIDO->RequiredErrorMessage));
            }
        }
        if ($this->ESTADO_PARTIDO->Required) {
            if (!$this->ESTADO_PARTIDO->IsDetailKey && EmptyValue($this->ESTADO_PARTIDO->FormValue)) {
                $this->ESTADO_PARTIDO->addErrorMessage(str_replace("%s", $this->ESTADO_PARTIDO->caption(), $this->ESTADO_PARTIDO->RequiredErrorMessage));
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
        if ($this->automatico->Required) {
            if ($this->automatico->FormValue == "") {
                $this->automatico->addErrorMessage(str_replace("%s", $this->automatico->caption(), $this->automatico->RequiredErrorMessage));
            }
        }
        if ($this->actualizado->Required) {
            if (!$this->actualizado->IsDetailKey && EmptyValue($this->actualizado->FormValue)) {
                $this->actualizado->addErrorMessage(str_replace("%s", $this->actualizado->caption(), $this->actualizado->RequiredErrorMessage));
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

        // ID_TORNEO
        $this->ID_TORNEO->setDbValueDef($rsnew, $this->ID_TORNEO->CurrentValue, null, $this->ID_TORNEO->ReadOnly);

        // equipo_local
        $this->equipo_local->setDbValueDef($rsnew, $this->equipo_local->CurrentValue, null, $this->equipo_local->ReadOnly);

        // equipo_visitante
        $this->equipo_visitante->setDbValueDef($rsnew, $this->equipo_visitante->CurrentValue, null, $this->equipo_visitante->ReadOnly);

        // FECHA_PARTIDO
        $this->FECHA_PARTIDO->setDbValueDef($rsnew, UnFormatDateTime($this->FECHA_PARTIDO->CurrentValue, $this->FECHA_PARTIDO->formatPattern()), null, $this->FECHA_PARTIDO->ReadOnly);

        // HORA_PARTIDO
        $this->HORA_PARTIDO->setDbValueDef($rsnew, UnFormatDateTime($this->HORA_PARTIDO->CurrentValue, $this->HORA_PARTIDO->formatPattern()), null, $this->HORA_PARTIDO->ReadOnly);

        // ESTADIO
        $this->ESTADIO->setDbValueDef($rsnew, $this->ESTADIO->CurrentValue, null, $this->ESTADIO->ReadOnly);

        // CIUDAD_PARTIDO
        $this->CIUDAD_PARTIDO->setDbValueDef($rsnew, $this->CIUDAD_PARTIDO->CurrentValue, null, $this->CIUDAD_PARTIDO->ReadOnly);

        // PAIS_PARTIDO
        $this->PAIS_PARTIDO->setDbValueDef($rsnew, $this->PAIS_PARTIDO->CurrentValue, null, $this->PAIS_PARTIDO->ReadOnly);

        // GOLES_LOCAL
        $this->GOLES_LOCAL->setDbValueDef($rsnew, $this->GOLES_LOCAL->CurrentValue, null, $this->GOLES_LOCAL->ReadOnly);

        // GOLES_VISITANTE
        $this->GOLES_VISITANTE->setDbValueDef($rsnew, $this->GOLES_VISITANTE->CurrentValue, null, $this->GOLES_VISITANTE->ReadOnly);

        // GOLES_EXTRA_EQUIPO1
        $this->GOLES_EXTRA_EQUIPO1->setDbValueDef($rsnew, $this->GOLES_EXTRA_EQUIPO1->CurrentValue, null, $this->GOLES_EXTRA_EQUIPO1->ReadOnly);

        // GOLES_EXTRA_EQUIPO2
        $this->GOLES_EXTRA_EQUIPO2->setDbValueDef($rsnew, $this->GOLES_EXTRA_EQUIPO2->CurrentValue, null, $this->GOLES_EXTRA_EQUIPO2->ReadOnly);

        // NOTA_PARTIDO
        $this->NOTA_PARTIDO->setDbValueDef($rsnew, $this->NOTA_PARTIDO->CurrentValue, null, $this->NOTA_PARTIDO->ReadOnly);

        // RESUMEN_PARTIDO
        $this->RESUMEN_PARTIDO->setDbValueDef($rsnew, $this->RESUMEN_PARTIDO->CurrentValue, null, $this->RESUMEN_PARTIDO->ReadOnly);

        // ESTADO_PARTIDO
        $this->ESTADO_PARTIDO->setDbValueDef($rsnew, $this->ESTADO_PARTIDO->CurrentValue, null, $this->ESTADO_PARTIDO->ReadOnly);

        // automatico
        $tmpBool = $this->automatico->CurrentValue;
        if ($tmpBool != "1" && $tmpBool != "0") {
            $tmpBool = !empty($tmpBool) ? "1" : "0";
        }
        $this->automatico->setDbValueDef($rsnew, $tmpBool, null, $this->automatico->ReadOnly);

        // Update current values
        $this->setCurrentValues($rsnew);

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

    // Set up Breadcrumb
    protected function setupBreadcrumb()
    {
        global $Breadcrumb, $Language;
        $Breadcrumb = new Breadcrumb("index");
        $url = CurrentUrl();
        $Breadcrumb->add("list", $this->TableVar, $this->addMasterUrl("partidoslist"), "", $this->TableVar, true);
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
                case "x_ID_TORNEO":
                    break;
                case "x_equipo_local":
                    break;
                case "x_equipo_visitante":
                    break;
                case "x_ESTADIO":
                    break;
                case "x_PAIS_PARTIDO":
                    break;
                case "x_ESTADO_PARTIDO":
                    break;
                case "x_automatico":
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
