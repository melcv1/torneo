<?php

namespace PHPMaker2022\project11;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Page class
 */
class PronosticadorList extends Pronosticador
{
    use MessagesTrait;

    // Page ID
    public $PageID = "list";

    // Project ID
    public $ProjectID = PROJECT_ID;

    // Table name
    public $TableName = 'pronosticador';

    // Page object name
    public $PageObjName = "PronosticadorList";

    // View file path
    public $View = null;

    // Title
    public $Title = null; // Title for <title> tag

    // Rendering View
    public $RenderingView = false;

    // Grid form hidden field names
    public $FormName = "fpronosticadorlist";
    public $FormActionName = "k_action";
    public $FormBlankRowName = "k_blankrow";
    public $FormKeyCountName = "key_count";

    // Page URLs
    public $AddUrl;
    public $EditUrl;
    public $CopyUrl;
    public $DeleteUrl;
    public $ViewUrl;
    public $ListUrl;

    // Update URLs
    public $InlineAddUrl;
    public $InlineCopyUrl;
    public $InlineEditUrl;
    public $GridAddUrl;
    public $GridEditUrl;
    public $MultiDeleteUrl;
    public $MultiUpdateUrl;

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

        // Page URL
        $pageUrl = $this->pageUrl(false);

        // Initialize URLs
        $this->AddUrl = "pronosticadoradd";
        $this->InlineAddUrl = $pageUrl . "action=add";
        $this->GridAddUrl = $pageUrl . "action=gridadd";
        $this->GridEditUrl = $pageUrl . "action=gridedit";
        $this->MultiDeleteUrl = "pronosticadordelete";
        $this->MultiUpdateUrl = "pronosticadorupdate";

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

        // List options
        $this->ListOptions = new ListOptions(["Tag" => "td", "TableVar" => $this->TableVar]);

        // Export options
        $this->ExportOptions = new ListOptions(["TagClassName" => "ew-export-option"]);

        // Import options
        $this->ImportOptions = new ListOptions(["TagClassName" => "ew-import-option"]);

        // Other options
        if (!$this->OtherOptions) {
            $this->OtherOptions = new ListOptionsArray();
        }

        // Grid-Add/Edit
        $this->OtherOptions["addedit"] = new ListOptions([
            "TagClassName" => "ew-add-edit-option",
            "UseDropDownButton" => false,
            "DropDownButtonPhrase" => $Language->phrase("ButtonAddEdit"),
            "UseButtonGroup" => true
        ]);

        // Detail tables
        $this->OtherOptions["detail"] = new ListOptions(["TagClassName" => "ew-detail-option"]);
        // Actions
        $this->OtherOptions["action"] = new ListOptions(["TagClassName" => "ew-action-option"]);

        // Column visibility
        $this->OtherOptions["column"] = new ListOptions([
            "TableVar" => $this->TableVar,
            "TagClassName" => "ew-column-option",
            "ButtonGroupClass" => "ew-column-dropdown",
            "UseDropDownButton" => true,
            "DropDownButtonPhrase" => $Language->phrase("Columns"),
            "DropDownAutoClose" => "outside",
            "UseButtonGroup" => false
        ]);

        // Filter options
        $this->FilterOptions = new ListOptions(["TagClassName" => "ew-filter-option"]);

        // List actions
        $this->ListActions = new ListActions();
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
                        if ($fld->DataType == DATATYPE_MEMO && $fld->MemoMaxLength > 0) {
                            $val = TruncateMemo($val, $fld->MemoMaxLength, $fld->TruncateMemoRemoveHtml);
                        }
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
        if ($this->isAddOrEdit()) {
            $this->usuario_dato->Visible = false;
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

    // Class variables
    public $ListOptions; // List options
    public $ExportOptions; // Export options
    public $SearchOptions; // Search options
    public $OtherOptions; // Other options
    public $FilterOptions; // Filter options
    public $ImportOptions; // Import options
    public $ListActions; // List actions
    public $SelectedCount = 0;
    public $SelectedIndex = 0;
    public $DisplayRecords = 20;
    public $StartRecord;
    public $StopRecord;
    public $TotalRecords = 0;
    public $RecordRange = 10;
    public $PageSizes = "10,20,50,80,-1"; // Page sizes (comma separated)
    public $DefaultSearchWhere = ""; // Default search WHERE clause
    public $SearchWhere = ""; // Search WHERE clause
    public $SearchPanelClass = "ew-search-panel collapse show"; // Search Panel class
    public $SearchColumnCount = 0; // For extended search
    public $SearchFieldsPerRow = 1; // For extended search
    public $RecordCount = 0; // Record count
    public $EditRowCount;
    public $StartRowCount = 1;
    public $RowCount = 0;
    public $Attrs = []; // Row attributes and cell attributes
    public $RowIndex = 0; // Row index
    public $KeyCount = 0; // Key count
    public $MultiColumnGridClass = "row-cols-md";
    public $MultiColumnEditClass = "col-12 w-100";
    public $MultiColumnCardClass = "card h-100 ew-card";
    public $MultiColumnListOptionsPosition = "bottom-start";
    public $DbMasterFilter = ""; // Master filter
    public $DbDetailFilter = ""; // Detail filter
    public $MasterRecordExists;
    public $MultiSelectKey;
    public $Command;
    public $UserAction; // User action
    public $RestoreSearch = false;
    public $HashValue; // Hash value
    public $DetailPages;
    public $OldRecordset;

    /**
     * Page run
     *
     * @return void
     */
    public function run()
    {
        global $ExportType, $CustomExportType, $ExportFileName, $UserProfile, $Language, $Security, $CurrentForm;

        // Multi column button position
        $this->MultiColumnListOptionsPosition = Config("MULTI_COLUMN_LIST_OPTIONS_POSITION");

        // Use layout
        $this->UseLayout = $this->UseLayout && ConvertToBool(Param("layout", true));

        // Create form object
        $CurrentForm = new HttpForm();
        $this->CurrentAction = Param("action"); // Set up current action

        // Get grid add count
        $gridaddcnt = Get(Config("TABLE_GRID_ADD_ROW_COUNT"), "");
        if (is_numeric($gridaddcnt) && $gridaddcnt > 0) {
            $this->GridAddRowCount = $gridaddcnt;
        }

        // Set up list options
        $this->setupListOptions();
        $this->ID_ENCUESTA->setVisibility();
        $this->ID_EQUIPOTORNEO->setVisibility();
        $this->EQUIPO->setVisibility();
        $this->GRUPO->setVisibility();
        $this->POSICION->setVisibility();
        $this->NUMERACION->setVisibility();
        $this->crea_dato->setVisibility();
        $this->modifica_dato->setVisibility();
        $this->usuario_dato->Visible = false;
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

        // Setup other options
        $this->setupOtherOptions();

        // Set up custom action (compatible with old version)
        foreach ($this->CustomActions as $name => $action) {
            $this->ListActions->add($name, $action);
        }

        // Set up lookup cache
        $this->setupLookupOptions($this->ID_EQUIPOTORNEO);
        $this->setupLookupOptions($this->EQUIPO);
        $this->setupLookupOptions($this->GRUPO);
        $this->setupLookupOptions($this->POSICION);
        $this->setupLookupOptions($this->NUMERACION);
        $this->setupLookupOptions($this->ID_PARTICIPANTE);

        // Load default values for add
        $this->loadDefaultValues();

        // Search filters
        $srchAdvanced = ""; // Advanced search filter
        $srchBasic = ""; // Basic search filter
        $filter = "";

        // Get command
        $this->Command = strtolower(Get("cmd", ""));
        if ($this->isPageRequest()) {
            // Process list action first
            if ($this->processListAction()) { // Ajax request
                $this->terminate();
                return;
            }

            // Set up records per page
            $this->setupDisplayRecords();

            // Handle reset command
            $this->resetCmd();

            // Set up Breadcrumb
            if (!$this->isExport()) {
                $this->setupBreadcrumb();
            }

            // Check QueryString parameters
            if (Get("action") !== null) {
                $this->CurrentAction = Get("action");

                // Clear inline mode
                if ($this->isCancel()) {
                    $this->clearInlineMode();
                }

                // Switch to inline edit mode
                if ($this->isEdit()) {
                    $this->inlineEditMode();
                }
            } else {
                if (Post("action") !== null) {
                    $this->CurrentAction = Post("action"); // Get action

                    // Inline Update
                    if (($this->isUpdate() || $this->isOverwrite()) && Session(SESSION_INLINE_MODE) == "edit") {
                        $this->setKey(Post($this->OldKeyName));
                        $this->inlineUpdate();
                    }
                }
            }

            // Hide list options
            if ($this->isExport()) {
                $this->ListOptions->hideAllOptions(["sequence"]);
                $this->ListOptions->UseDropDownButton = false; // Disable drop down button
                $this->ListOptions->UseButtonGroup = false; // Disable button group
            } elseif ($this->isGridAdd() || $this->isGridEdit()) {
                $this->ListOptions->hideAllOptions();
                $this->ListOptions->UseDropDownButton = false; // Disable drop down button
                $this->ListOptions->UseButtonGroup = false; // Disable button group
            }

            // Hide options
            if ($this->isExport() || $this->CurrentAction) {
                $this->ExportOptions->hideAllOptions();
                $this->FilterOptions->hideAllOptions();
                $this->ImportOptions->hideAllOptions();
            }

            // Hide other options
            if ($this->isExport()) {
                $this->OtherOptions->hideAllOptions();
            }

            // Get default search criteria
            AddFilter($this->DefaultSearchWhere, $this->basicSearchWhere(true));

            // Get basic search values
            $this->loadBasicSearchValues();

            // Process filter list
            if ($this->processFilterList()) {
                $this->terminate();
                return;
            }

            // Restore search parms from Session if not searching / reset / export
            if (($this->isExport() || $this->Command != "search" && $this->Command != "reset" && $this->Command != "resetall") && $this->Command != "json" && $this->checkSearchParms()) {
                $this->restoreSearchParms();
            }

            // Call Recordset SearchValidated event
            $this->recordsetSearchValidated();

            // Set up sorting order
            $this->setupSortOrder();

            // Get basic search criteria
            if (!$this->hasInvalidFields()) {
                $srchBasic = $this->basicSearchWhere();
            }
        }

        // Restore display records
        if ($this->Command != "json" && $this->getRecordsPerPage() != "") {
            $this->DisplayRecords = $this->getRecordsPerPage(); // Restore from Session
        } else {
            $this->DisplayRecords = 20; // Load default
            $this->setRecordsPerPage($this->DisplayRecords); // Save default to Session
        }

        // Load search default if no existing search criteria
        if (!$this->checkSearchParms()) {
            // Load basic search from default
            $this->BasicSearch->loadDefault();
            if ($this->BasicSearch->Keyword != "") {
                $srchBasic = $this->basicSearchWhere();
            }
        }

        // Build search criteria
        AddFilter($this->SearchWhere, $srchAdvanced);
        AddFilter($this->SearchWhere, $srchBasic);

        // Call Recordset_Searching event
        $this->recordsetSearching($this->SearchWhere);

        // Save search criteria
        if ($this->Command == "search" && !$this->RestoreSearch) {
            $this->setSearchWhere($this->SearchWhere); // Save to Session
            $this->StartRecord = 1; // Reset start record counter
            $this->setStartRecordNumber($this->StartRecord);
        } elseif ($this->Command != "json") {
            $this->SearchWhere = $this->getSearchWhere();
        }

        // Build filter
        $filter = "";
        if (!$Security->canList()) {
            $filter = "(0=1)"; // Filter all records
        }
        AddFilter($filter, $this->DbDetailFilter);
        AddFilter($filter, $this->SearchWhere);

        // Set up filter
        if ($this->Command == "json") {
            $this->UseSessionForListSql = false; // Do not use session for ListSQL
            $this->CurrentFilter = $filter;
        } else {
            $this->setSessionWhere($filter);
            $this->CurrentFilter = "";
        }
        if ($this->isGridAdd()) {
            $this->CurrentFilter = "0=1";
            $this->StartRecord = 1;
            $this->DisplayRecords = $this->GridAddRowCount;
            $this->TotalRecords = $this->DisplayRecords;
            $this->StopRecord = $this->DisplayRecords;
        } else {
            $this->TotalRecords = $this->listRecordCount();
            $this->StartRecord = 1;
            if ($this->DisplayRecords <= 0 || ($this->isExport() && $this->ExportAll)) { // Display all records
                $this->DisplayRecords = $this->TotalRecords;
            }
            if (!($this->isExport() && $this->ExportAll)) { // Set up start record position
                $this->setupStartRecord();
            }
            $this->Recordset = $this->loadRecordset($this->StartRecord - 1, $this->DisplayRecords);

            // Set no record found message
            if (!$this->CurrentAction && $this->TotalRecords == 0) {
                if (!$Security->canList()) {
                    $this->setWarningMessage(DeniedMessage());
                }
                if ($this->SearchWhere == "0=101") {
                    $this->setWarningMessage($Language->phrase("EnterSearchCriteria"));
                } else {
                    $this->setWarningMessage($Language->phrase("NoRecord"));
                }
            }

            // Audit trail on search
            if ($this->AuditTrailOnSearch && $this->Command == "search" && !$this->RestoreSearch) {
                $searchParm = ServerVar("QUERY_STRING");
                $searchSql = $this->getSessionWhere();
                $this->writeAuditTrailOnSearch($searchParm, $searchSql);
            }
        }

        // Set up list action columns
        foreach ($this->ListActions->Items as $listaction) {
            if ($listaction->Allow) {
                if ($listaction->Select == ACTION_MULTIPLE) { // Show checkbox column if multiple action
                    $this->ListOptions["checkbox"]->Visible = true;
                } elseif ($listaction->Select == ACTION_SINGLE) { // Show list action column
                        $this->ListOptions["listactions"]->Visible = true; // Set visible if any list action is allowed
                }
            }
        }

        // Search options
        $this->setupSearchOptions();

        // Set up search panel class
        if ($this->SearchWhere != "") {
            AppendClass($this->SearchPanelClass, "show");
        }

        // Normal return
        if (IsApi()) {
            $rows = $this->getRecordsFromRecordset($this->Recordset);
            $this->Recordset->close();
            WriteJson(["success" => true, $this->TableVar => $rows, "totalRecordCount" => $this->TotalRecords]);
            $this->terminate(true);
            return;
        }

        // Set up pager
        $this->Pager = new PrevNextPager($this->TableVar, $this->StartRecord, $this->getRecordsPerPage(), $this->TotalRecords, $this->PageSizes, $this->RecordRange, $this->AutoHidePager, $this->AutoHidePageSizeSelector);

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

    // Set up number of records displayed per page
    protected function setupDisplayRecords()
    {
        $wrk = Get(Config("TABLE_REC_PER_PAGE"), "");
        if ($wrk != "") {
            if (is_numeric($wrk)) {
                $this->DisplayRecords = (int)$wrk;
            } else {
                if (SameText($wrk, "all")) { // Display all records
                    $this->DisplayRecords = -1;
                } else {
                    $this->DisplayRecords = 20; // Non-numeric, load default
                }
            }
            $this->setRecordsPerPage($this->DisplayRecords); // Save to Session
            // Reset start position
            $this->StartRecord = 1;
            $this->setStartRecordNumber($this->StartRecord);
        }
    }

    // Exit inline mode
    protected function clearInlineMode()
    {
        $this->LastAction = $this->CurrentAction; // Save last action
        $this->CurrentAction = ""; // Clear action
        $_SESSION[SESSION_INLINE_MODE] = ""; // Clear inline mode
    }

    // Switch to Inline Edit mode
    protected function inlineEditMode()
    {
        global $Security, $Language;
        if (!$Security->canEdit()) { // No edit permission
            $this->CurrentAction = "";
            $this->setFailureMessage($Language->phrase("NoEditPermission"));
            return false;
        }
        $inlineEdit = true;
        if (($keyValue = Get("ID_ENCUESTA") ?? Route("ID_ENCUESTA")) !== null) {
            $this->ID_ENCUESTA->setQueryStringValue($keyValue);
        } else {
            $inlineEdit = false;
        }
        if ($inlineEdit) {
            if ($this->loadRow()) {
                $this->OldKey = $this->getKey(true); // Get from CurrentValue
                $this->setKey($this->OldKey); // Set to OldValue
                $_SESSION[SESSION_INLINE_MODE] = "edit"; // Enable inline edit
            }
        }
        return true;
    }

    // Perform update to Inline Edit record
    protected function inlineUpdate()
    {
        global $Language, $CurrentForm;
        $CurrentForm->Index = 1;
        $this->loadFormValues(); // Get form values

        // Validate form
        $inlineUpdate = true;
        if (!$this->validateForm()) {
            $inlineUpdate = false; // Form error, reset action
        } else {
            $inlineUpdate = false;
            $this->SendEmail = true; // Send email on update success
            $inlineUpdate = $this->editRow(); // Update record
        }
        if ($inlineUpdate) { // Update success
            if ($this->getSuccessMessage() == "") {
                $this->setSuccessMessage($Language->phrase("UpdateSuccess")); // Set up success message
            }
            $this->clearInlineMode(); // Clear inline edit mode
        } else {
            if ($this->getFailureMessage() == "") {
                $this->setFailureMessage($Language->phrase("UpdateFailed")); // Set update failed message
            }
            $this->EventCancelled = true; // Cancel event
            $this->CurrentAction = "edit"; // Stay in edit mode
        }
    }

    // Check Inline Edit key
    public function checkInlineEditKey()
    {
        if (!SameString($this->ID_ENCUESTA->OldValue, $this->ID_ENCUESTA->CurrentValue)) {
            return false;
        }
        return true;
    }

    // Build filter for all keys
    protected function buildKeyFilter()
    {
        global $CurrentForm;
        $wrkFilter = "";

        // Update row index and get row key
        $rowindex = 1;
        $CurrentForm->Index = $rowindex;
        $thisKey = strval($CurrentForm->getValue($this->OldKeyName));
        while ($thisKey != "") {
            $this->setKey($thisKey);
            if ($this->OldKey != "") {
                $filter = $this->getRecordFilter();
                if ($wrkFilter != "") {
                    $wrkFilter .= " OR ";
                }
                $wrkFilter .= $filter;
            } else {
                $wrkFilter = "0=1";
                break;
            }

            // Update row index and get row key
            $rowindex++; // Next row
            $CurrentForm->Index = $rowindex;
            $thisKey = strval($CurrentForm->getValue($this->OldKeyName));
        }
        return $wrkFilter;
    }

    // Get list of filters
    public function getFilterList()
    {
        global $UserProfile;

        // Initialize
        $filterList = "";
        $savedFilterList = "";

        // Load server side filters
        if (Config("SEARCH_FILTER_OPTION") == "Server" && isset($UserProfile)) {
            $savedFilterList = $UserProfile->getSearchFilters(CurrentUserName(), "fpronosticadorsrch");
        }
        $filterList = Concat($filterList, $this->ID_ENCUESTA->AdvancedSearch->toJson(), ","); // Field ID_ENCUESTA
        $filterList = Concat($filterList, $this->ID_EQUIPOTORNEO->AdvancedSearch->toJson(), ","); // Field ID_EQUIPOTORNEO
        $filterList = Concat($filterList, $this->EQUIPO->AdvancedSearch->toJson(), ","); // Field EQUIPO
        $filterList = Concat($filterList, $this->GRUPO->AdvancedSearch->toJson(), ","); // Field GRUPO
        $filterList = Concat($filterList, $this->POSICION->AdvancedSearch->toJson(), ","); // Field POSICION
        $filterList = Concat($filterList, $this->NUMERACION->AdvancedSearch->toJson(), ","); // Field NUMERACION
        $filterList = Concat($filterList, $this->crea_dato->AdvancedSearch->toJson(), ","); // Field crea_dato
        $filterList = Concat($filterList, $this->modifica_dato->AdvancedSearch->toJson(), ","); // Field modifica_dato
        $filterList = Concat($filterList, $this->usuario_dato->AdvancedSearch->toJson(), ","); // Field usuario_dato
        $filterList = Concat($filterList, $this->ID_PARTICIPANTE->AdvancedSearch->toJson(), ","); // Field ID_PARTICIPANTE
        if ($this->BasicSearch->Keyword != "") {
            $wrk = "\"" . Config("TABLE_BASIC_SEARCH") . "\":\"" . JsEncode($this->BasicSearch->Keyword) . "\",\"" . Config("TABLE_BASIC_SEARCH_TYPE") . "\":\"" . JsEncode($this->BasicSearch->Type) . "\"";
            $filterList = Concat($filterList, $wrk, ",");
        }

        // Return filter list in JSON
        if ($filterList != "") {
            $filterList = "\"data\":{" . $filterList . "}";
        }
        if ($savedFilterList != "") {
            $filterList = Concat($filterList, "\"filters\":" . $savedFilterList, ",");
        }
        return ($filterList != "") ? "{" . $filterList . "}" : "null";
    }

    // Process filter list
    protected function processFilterList()
    {
        global $UserProfile;
        if (Post("ajax") == "savefilters") { // Save filter request (Ajax)
            $filters = Post("filters");
            $UserProfile->setSearchFilters(CurrentUserName(), "fpronosticadorsrch", $filters);
            WriteJson([["success" => true]]); // Success
            return true;
        } elseif (Post("cmd") == "resetfilter") {
            $this->restoreFilterList();
        }
        return false;
    }

    // Restore list of filters
    protected function restoreFilterList()
    {
        // Return if not reset filter
        if (Post("cmd") !== "resetfilter") {
            return false;
        }
        $filter = json_decode(Post("filter"), true);
        $this->Command = "search";

        // Field ID_ENCUESTA
        $this->ID_ENCUESTA->AdvancedSearch->SearchValue = @$filter["x_ID_ENCUESTA"];
        $this->ID_ENCUESTA->AdvancedSearch->SearchOperator = @$filter["z_ID_ENCUESTA"];
        $this->ID_ENCUESTA->AdvancedSearch->SearchCondition = @$filter["v_ID_ENCUESTA"];
        $this->ID_ENCUESTA->AdvancedSearch->SearchValue2 = @$filter["y_ID_ENCUESTA"];
        $this->ID_ENCUESTA->AdvancedSearch->SearchOperator2 = @$filter["w_ID_ENCUESTA"];
        $this->ID_ENCUESTA->AdvancedSearch->save();

        // Field ID_EQUIPOTORNEO
        $this->ID_EQUIPOTORNEO->AdvancedSearch->SearchValue = @$filter["x_ID_EQUIPOTORNEO"];
        $this->ID_EQUIPOTORNEO->AdvancedSearch->SearchOperator = @$filter["z_ID_EQUIPOTORNEO"];
        $this->ID_EQUIPOTORNEO->AdvancedSearch->SearchCondition = @$filter["v_ID_EQUIPOTORNEO"];
        $this->ID_EQUIPOTORNEO->AdvancedSearch->SearchValue2 = @$filter["y_ID_EQUIPOTORNEO"];
        $this->ID_EQUIPOTORNEO->AdvancedSearch->SearchOperator2 = @$filter["w_ID_EQUIPOTORNEO"];
        $this->ID_EQUIPOTORNEO->AdvancedSearch->save();

        // Field EQUIPO
        $this->EQUIPO->AdvancedSearch->SearchValue = @$filter["x_EQUIPO"];
        $this->EQUIPO->AdvancedSearch->SearchOperator = @$filter["z_EQUIPO"];
        $this->EQUIPO->AdvancedSearch->SearchCondition = @$filter["v_EQUIPO"];
        $this->EQUIPO->AdvancedSearch->SearchValue2 = @$filter["y_EQUIPO"];
        $this->EQUIPO->AdvancedSearch->SearchOperator2 = @$filter["w_EQUIPO"];
        $this->EQUIPO->AdvancedSearch->save();

        // Field GRUPO
        $this->GRUPO->AdvancedSearch->SearchValue = @$filter["x_GRUPO"];
        $this->GRUPO->AdvancedSearch->SearchOperator = @$filter["z_GRUPO"];
        $this->GRUPO->AdvancedSearch->SearchCondition = @$filter["v_GRUPO"];
        $this->GRUPO->AdvancedSearch->SearchValue2 = @$filter["y_GRUPO"];
        $this->GRUPO->AdvancedSearch->SearchOperator2 = @$filter["w_GRUPO"];
        $this->GRUPO->AdvancedSearch->save();

        // Field POSICION
        $this->POSICION->AdvancedSearch->SearchValue = @$filter["x_POSICION"];
        $this->POSICION->AdvancedSearch->SearchOperator = @$filter["z_POSICION"];
        $this->POSICION->AdvancedSearch->SearchCondition = @$filter["v_POSICION"];
        $this->POSICION->AdvancedSearch->SearchValue2 = @$filter["y_POSICION"];
        $this->POSICION->AdvancedSearch->SearchOperator2 = @$filter["w_POSICION"];
        $this->POSICION->AdvancedSearch->save();

        // Field NUMERACION
        $this->NUMERACION->AdvancedSearch->SearchValue = @$filter["x_NUMERACION"];
        $this->NUMERACION->AdvancedSearch->SearchOperator = @$filter["z_NUMERACION"];
        $this->NUMERACION->AdvancedSearch->SearchCondition = @$filter["v_NUMERACION"];
        $this->NUMERACION->AdvancedSearch->SearchValue2 = @$filter["y_NUMERACION"];
        $this->NUMERACION->AdvancedSearch->SearchOperator2 = @$filter["w_NUMERACION"];
        $this->NUMERACION->AdvancedSearch->save();

        // Field crea_dato
        $this->crea_dato->AdvancedSearch->SearchValue = @$filter["x_crea_dato"];
        $this->crea_dato->AdvancedSearch->SearchOperator = @$filter["z_crea_dato"];
        $this->crea_dato->AdvancedSearch->SearchCondition = @$filter["v_crea_dato"];
        $this->crea_dato->AdvancedSearch->SearchValue2 = @$filter["y_crea_dato"];
        $this->crea_dato->AdvancedSearch->SearchOperator2 = @$filter["w_crea_dato"];
        $this->crea_dato->AdvancedSearch->save();

        // Field modifica_dato
        $this->modifica_dato->AdvancedSearch->SearchValue = @$filter["x_modifica_dato"];
        $this->modifica_dato->AdvancedSearch->SearchOperator = @$filter["z_modifica_dato"];
        $this->modifica_dato->AdvancedSearch->SearchCondition = @$filter["v_modifica_dato"];
        $this->modifica_dato->AdvancedSearch->SearchValue2 = @$filter["y_modifica_dato"];
        $this->modifica_dato->AdvancedSearch->SearchOperator2 = @$filter["w_modifica_dato"];
        $this->modifica_dato->AdvancedSearch->save();

        // Field usuario_dato
        $this->usuario_dato->AdvancedSearch->SearchValue = @$filter["x_usuario_dato"];
        $this->usuario_dato->AdvancedSearch->SearchOperator = @$filter["z_usuario_dato"];
        $this->usuario_dato->AdvancedSearch->SearchCondition = @$filter["v_usuario_dato"];
        $this->usuario_dato->AdvancedSearch->SearchValue2 = @$filter["y_usuario_dato"];
        $this->usuario_dato->AdvancedSearch->SearchOperator2 = @$filter["w_usuario_dato"];
        $this->usuario_dato->AdvancedSearch->save();

        // Field ID_PARTICIPANTE
        $this->ID_PARTICIPANTE->AdvancedSearch->SearchValue = @$filter["x_ID_PARTICIPANTE"];
        $this->ID_PARTICIPANTE->AdvancedSearch->SearchOperator = @$filter["z_ID_PARTICIPANTE"];
        $this->ID_PARTICIPANTE->AdvancedSearch->SearchCondition = @$filter["v_ID_PARTICIPANTE"];
        $this->ID_PARTICIPANTE->AdvancedSearch->SearchValue2 = @$filter["y_ID_PARTICIPANTE"];
        $this->ID_PARTICIPANTE->AdvancedSearch->SearchOperator2 = @$filter["w_ID_PARTICIPANTE"];
        $this->ID_PARTICIPANTE->AdvancedSearch->save();
        $this->BasicSearch->setKeyword(@$filter[Config("TABLE_BASIC_SEARCH")]);
        $this->BasicSearch->setType(@$filter[Config("TABLE_BASIC_SEARCH_TYPE")]);
    }

    // Return basic search WHERE clause based on search keyword and type
    protected function basicSearchWhere($default = false)
    {
        global $Security;
        $searchStr = "";
        if (!$Security->canSearch()) {
            return "";
        }

        // Fields to search
        $searchFlds = [];
        $searchFlds[] = &$this->EQUIPO;
        $searchFlds[] = &$this->GRUPO;
        $searchFlds[] = &$this->POSICION;
        $searchFlds[] = &$this->NUMERACION;
        $searchFlds[] = &$this->usuario_dato;
        $searchKeyword = $default ? $this->BasicSearch->KeywordDefault : $this->BasicSearch->Keyword;
        $searchType = $default ? $this->BasicSearch->TypeDefault : $this->BasicSearch->Type;

        // Get search SQL
        if ($searchKeyword != "") {
            $ar = $this->BasicSearch->keywordList($default);
            $searchStr = GetQuickSearchFilter($searchFlds, $ar, $searchType, Config("BASIC_SEARCH_ANY_FIELDS"), $this->Dbid);
            if (!$default && in_array($this->Command, ["", "reset", "resetall"])) {
                $this->Command = "search";
            }
        }
        if (!$default && $this->Command == "search") {
            $this->BasicSearch->setKeyword($searchKeyword);
            $this->BasicSearch->setType($searchType);
        }
        return $searchStr;
    }

    // Check if search parm exists
    protected function checkSearchParms()
    {
        // Check basic search
        if ($this->BasicSearch->issetSession()) {
            return true;
        }
        return false;
    }

    // Clear all search parameters
    protected function resetSearchParms()
    {
        // Clear search WHERE clause
        $this->SearchWhere = "";
        $this->setSearchWhere($this->SearchWhere);

        // Clear basic search parameters
        $this->resetBasicSearchParms();
    }

    // Load advanced search default values
    protected function loadAdvancedSearchDefault()
    {
        return false;
    }

    // Clear all basic search parameters
    protected function resetBasicSearchParms()
    {
        $this->BasicSearch->unsetSession();
    }

    // Restore all search parameters
    protected function restoreSearchParms()
    {
        $this->RestoreSearch = true;

        // Restore basic search values
        $this->BasicSearch->load();
    }

    // Set up sort parameters
    protected function setupSortOrder()
    {
        // Load default Sorting Order
        if ($this->Command != "json") {
            $defaultSort = ""; // Set up default sort
            if ($this->getSessionOrderBy() == "" && $defaultSort != "") {
                $this->setSessionOrderBy($defaultSort);
            }
            $defaultSortList = ""; // Set up default sort
            if ($this->getSessionOrderByList() == "" && $defaultSortList != "") {
                $this->setSessionOrderByList($defaultSortList);
            }
        }

        // Check for "order" parameter
        if (Get("order") !== null) {
            $this->CurrentOrder = Get("order");
            $this->CurrentOrderType = Get("ordertype", "");
            $this->updateSort($this->ID_ENCUESTA); // ID_ENCUESTA
            $this->updateSort($this->ID_EQUIPOTORNEO); // ID_EQUIPOTORNEO
            $this->updateSort($this->EQUIPO); // EQUIPO
            $this->updateSort($this->GRUPO); // GRUPO
            $this->updateSort($this->POSICION); // POSICION
            $this->updateSort($this->NUMERACION); // NUMERACION
            $this->updateSort($this->crea_dato); // crea_dato
            $this->updateSort($this->modifica_dato); // modifica_dato
            $this->updateSort($this->ID_PARTICIPANTE); // ID_PARTICIPANTE
            $this->setStartRecordNumber(1); // Reset start position
        }

        // Update field sort
        $this->updateFieldSort();
    }

    // Reset command
    // - cmd=reset (Reset search parameters)
    // - cmd=resetall (Reset search and master/detail parameters)
    // - cmd=resetsort (Reset sort parameters)
    protected function resetCmd()
    {
        // Check if reset command
        if (StartsString("reset", $this->Command)) {
            // Reset search criteria
            if ($this->Command == "reset" || $this->Command == "resetall") {
                $this->resetSearchParms();
            }

            // Reset (clear) sorting order
            if ($this->Command == "resetsort") {
                $orderBy = "";
                $this->setSessionOrderBy($orderBy);
                $this->setSessionOrderByList($orderBy);
                $this->ID_ENCUESTA->setSort("");
                $this->ID_EQUIPOTORNEO->setSort("");
                $this->EQUIPO->setSort("");
                $this->GRUPO->setSort("");
                $this->POSICION->setSort("");
                $this->NUMERACION->setSort("");
                $this->crea_dato->setSort("");
                $this->modifica_dato->setSort("");
                $this->usuario_dato->setSort("");
                $this->ID_PARTICIPANTE->setSort("");
            }

            // Reset start position
            $this->StartRecord = 1;
            $this->setStartRecordNumber($this->StartRecord);
        }
    }

    // Set up list options
    protected function setupListOptions()
    {
        global $Security, $Language;

        // Add group option item ("button")
        $item = &$this->ListOptions->addGroupOption();
        $item->Body = "";
        $item->OnLeft = true;
        $item->Visible = false;

        // "view"
        $item = &$this->ListOptions->add("view");
        $item->CssClass = "text-nowrap";
        $item->Visible = $Security->canView();
        $item->OnLeft = true;

        // "edit"
        $item = &$this->ListOptions->add("edit");
        $item->CssClass = "text-nowrap";
        $item->Visible = $Security->canEdit();
        $item->OnLeft = true;

        // "copy"
        $item = &$this->ListOptions->add("copy");
        $item->CssClass = "text-nowrap";
        $item->Visible = $Security->canAdd();
        $item->OnLeft = true;

        // List actions
        $item = &$this->ListOptions->add("listactions");
        $item->CssClass = "text-nowrap";
        $item->OnLeft = true;
        $item->Visible = false;
        $item->ShowInButtonGroup = false;
        $item->ShowInDropDown = false;

        // "checkbox"
        $item = &$this->ListOptions->add("checkbox");
        $item->Visible = $Security->canDelete();
        $item->OnLeft = true;
        $item->Header = "<div class=\"form-check\"><input type=\"checkbox\" name=\"key\" id=\"key\" class=\"form-check-input\" data-ew-action=\"select-all-keys\"></div>";
        if ($item->OnLeft) {
            $item->moveTo(0);
        }
        $item->ShowInDropDown = false;
        $item->ShowInButtonGroup = false;

        // Drop down button for ListOptions
        $this->ListOptions->UseDropDownButton = false;
        $this->ListOptions->DropDownButtonPhrase = $Language->phrase("ButtonListOptions");
        $this->ListOptions->UseButtonGroup = false;
        if ($this->ListOptions->UseButtonGroup && IsMobile()) {
            $this->ListOptions->UseDropDownButton = true;
        }

        //$this->ListOptions->ButtonClass = ""; // Class for button group

        // Call ListOptions_Load event
        $this->listOptionsLoad();
        $this->setupListOptionsExt();
        $item = $this->ListOptions[$this->ListOptions->GroupOptionName];
        $item->Visible = $this->ListOptions->groupOptionVisible();
    }

    // Set up list options (extensions)
    protected function setupListOptionsExt()
    {
            // Set up list options (to be implemented by extensions)
    }

    // Render list options
    public function renderListOptions()
    {
        global $Security, $Language, $CurrentForm, $UserProfile;
        $this->ListOptions->loadDefault();

        // Call ListOptions_Rendering event
        $this->listOptionsRendering();

        // Set up row action and key
        if ($CurrentForm && is_numeric($this->RowIndex) && $this->RowType != "view") {
            $CurrentForm->Index = $this->RowIndex;
            $actionName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormActionName);
            $oldKeyName = str_replace("k_", "k" . $this->RowIndex . "_", $this->OldKeyName);
            $blankRowName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormBlankRowName);
            if ($this->RowAction != "") {
                $this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $actionName . "\" id=\"" . $actionName . "\" value=\"" . $this->RowAction . "\">";
            }
            $oldKey = $this->getKey(false); // Get from OldValue
            if ($oldKeyName != "" && $oldKey != "") {
                $this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $oldKeyName . "\" id=\"" . $oldKeyName . "\" value=\"" . HtmlEncode($oldKey) . "\">";
            }
            if ($this->RowAction == "insert" && $this->isConfirm() && $this->emptyRow()) {
                $this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $blankRowName . "\" id=\"" . $blankRowName . "\" value=\"1\">";
            }
        }
        $pageUrl = $this->pageUrl(false);

        // "edit"
        $opt = $this->ListOptions["edit"];
        if ($this->isInlineEditRow()) { // Inline-Edit
            $this->ListOptions->CustomItem = "edit"; // Show edit column only
            $cancelurl = $this->addMasterUrl($pageUrl . "action=cancel");
                $opt->Body = "<div" . (($opt->OnLeft) ? " class=\"text-end\"" : "") . ">" .
                "<button class=\"ew-grid-link ew-inline-update\" title=\"" . HtmlTitle($Language->phrase("UpdateLink")) . "\" data-caption=\"" . HtmlTitle($Language->phrase("UpdateLink")) . "\" form=\"fpronosticadorlist\" formaction=\"" . HtmlEncode(GetUrl(UrlAddHash($this->pageName(), "r" . $this->RowCount . "_" . $this->TableVar))) . "\">" . $Language->phrase("UpdateLink") . "</button>&nbsp;" .
                "<a class=\"ew-grid-link ew-inline-cancel\" title=\"" . HtmlTitle($Language->phrase("CancelLink")) . "\" data-caption=\"" . HtmlTitle($Language->phrase("CancelLink")) . "\" href=\"" . $cancelurl . "\">" . $Language->phrase("CancelLink") . "</a>" .
                "<input type=\"hidden\" name=\"action\" id=\"action\" value=\"update\"></div>";
            $opt->Body .= "<input type=\"hidden\" name=\"k" . $this->RowIndex . "_key\" id=\"k" . $this->RowIndex . "_key\" value=\"" . HtmlEncode($this->ID_ENCUESTA->CurrentValue) . "\">";
            return;
        }
        if ($this->CurrentMode == "view") {
            // "view"
            $opt = $this->ListOptions["view"];
            $viewcaption = HtmlTitle($Language->phrase("ViewLink"));
            if ($Security->canView()) {
                if (IsMobile()) {
                    $opt->Body = "<a class=\"ew-row-link ew-view\" title=\"" . $viewcaption . "\" data-caption=\"" . $viewcaption . "\" href=\"" . HtmlEncode(GetUrl($this->ViewUrl)) . "\">" . $Language->phrase("ViewLink") . "</a>";
                } else {
                    $opt->Body = "<a class=\"ew-row-link ew-view\" title=\"" . $viewcaption . "\" data-table=\"pronosticador\" data-caption=\"" . $viewcaption . "\" data-ew-action=\"modal\" data-url=\"" . HtmlEncode(GetUrl($this->ViewUrl)) . "\" data-btn=\"null\">" . $Language->phrase("ViewLink") . "</a>";
                }
            } else {
                $opt->Body = "";
            }

            // "edit"
            $opt = $this->ListOptions["edit"];
            $editcaption = HtmlTitle($Language->phrase("EditLink"));
            if ($Security->canEdit()) {
                $opt->Body .= "<a class=\"ew-row-link ew-inline-edit\" title=\"" . HtmlTitle($Language->phrase("InlineEditLink")) . "\" data-caption=\"" . HtmlTitle($Language->phrase("InlineEditLink")) . "\" href=\"" . HtmlEncode(UrlAddHash(GetUrl($this->InlineEditUrl), "r" . $this->RowCount . "_" . $this->TableVar)) . "\">" . $Language->phrase("InlineEditLink") . "</a>";
            } else {
                $opt->Body = "";
            }

            // "copy"
            $opt = $this->ListOptions["copy"];
            $copycaption = HtmlTitle($Language->phrase("CopyLink"));
            if ($Security->canAdd()) {
                $opt->Body = "<a class=\"ew-row-link ew-copy\" title=\"" . $copycaption . "\" data-caption=\"" . $copycaption . "\" href=\"" . HtmlEncode(GetUrl($this->CopyUrl)) . "\">" . $Language->phrase("CopyLink") . "</a>";
            } else {
                $opt->Body = "";
            }
        } // End View mode

        // Set up list action buttons
        $opt = $this->ListOptions["listactions"];
        if ($opt && !$this->isExport() && !$this->CurrentAction) {
            $body = "";
            $links = [];
            foreach ($this->ListActions->Items as $listaction) {
                $action = $listaction->Action;
                $allowed = $listaction->Allow;
                if ($listaction->Select == ACTION_SINGLE && $allowed) {
                    $caption = $listaction->Caption;
                    $icon = ($listaction->Icon != "") ? "<i class=\"" . HtmlEncode(str_replace(" ew-icon", "", $listaction->Icon)) . "\" data-caption=\"" . HtmlTitle($caption) . "\"></i> " : "";
                    $link = "<li><button type=\"button\" class=\"dropdown-item ew-action ew-list-action\" data-caption=\"" . HtmlTitle($caption) . "\" data-ew-action=\"submit\" form=\"fpronosticadorlist\" data-key=\"" . $this->keyToJson(true) . "\"" . $listaction->toDataAttrs() . ">" . $icon . $listaction->Caption . "</button></li>";
                    if ($link != "") {
                        $links[] = $link;
                        if ($body == "") { // Setup first button
                            $body = "<button type=\"button\" class=\"btn btn-default ew-action ew-list-action\" title=\"" . HtmlTitle($caption) . "\" data-caption=\"" . HtmlTitle($caption) . "\" data-ew-action=\"submit\" form=\"fpronosticadorlist\" data-key=\"" . $this->keyToJson(true) . "\"" . $listaction->toDataAttrs() . ">" . $icon . $listaction->Caption . "</button>";
                        }
                    }
                }
            }
            if (count($links) > 1) { // More than one buttons, use dropdown
                $body = "<button class=\"dropdown-toggle btn btn-default ew-actions\" title=\"" . HtmlTitle($Language->phrase("ListActionButton")) . "\" data-bs-toggle=\"dropdown\">" . $Language->phrase("ListActionButton") . "</button>";
                $content = "";
                foreach ($links as $link) {
                    $content .= "<li>" . $link . "</li>";
                }
                $body .= "<ul class=\"dropdown-menu" . ($opt->OnLeft ? "" : " dropdown-menu-right") . "\">" . $content . "</ul>";
                $body = "<div class=\"btn-group btn-group-sm\">" . $body . "</div>";
            }
            if (count($links) > 0) {
                $opt->Body = $body;
            }
        }

        // "checkbox"
        $opt = $this->ListOptions["checkbox"];
        $opt->Body = "<div class=\"form-check\"><input type=\"checkbox\" id=\"key_m_" . $this->RowCount . "\" name=\"key_m[]\" class=\"form-check-input ew-multi-select\" value=\"" . HtmlEncode($this->ID_ENCUESTA->CurrentValue) . "\" data-ew-action=\"select-key\"></div>";
        $this->renderListOptionsExt();

        // Call ListOptions_Rendered event
        $this->listOptionsRendered();
    }

    // Render list options (extensions)
    protected function renderListOptionsExt()
    {
        // Render list options (to be implemented by extensions)
        global $Security, $Language;
    }

    // Set up other options
    protected function setupOtherOptions()
    {
        global $Language, $Security;
        $options = &$this->OtherOptions;
        $option = $options["addedit"];

        // Add
        $item = &$option->add("add");
        $addcaption = HtmlTitle($Language->phrase("AddLink"));
        $item->Body = "<a class=\"ew-add-edit ew-add\" title=\"" . $addcaption . "\" data-caption=\"" . $addcaption . "\" href=\"" . HtmlEncode(GetUrl($this->AddUrl)) . "\">" . $Language->phrase("AddLink") . "</a>";
        $item->Visible = $this->AddUrl != "" && $Security->canAdd();
        $option = $options["action"];

        // Add multi delete
        $item = &$option->add("multidelete");
        $item->Body = "<button type=\"button\" class=\"ew-action ew-multi-delete\" title=\"" . HtmlTitle($Language->phrase("DeleteSelectedLink")) . "\" data-caption=\"" . HtmlTitle($Language->phrase("DeleteSelectedLink")) . "\" form=\"fpronosticadorlist\" data-ew-action=\"submit\" data-url=\"" . GetUrl($this->MultiDeleteUrl) . "\"data-data='{\"action\":\"show\"}'>" . $Language->phrase("DeleteSelectedLink") . "</button>";
        $item->Visible = $Security->canDelete();

        // Show column list for column visibility
        if ($this->UseColumnVisibility) {
            $option = $this->OtherOptions["column"];
            $item = &$option->addGroupOption();
            $item->Body = "";
            $item->Visible = $this->UseColumnVisibility;
            $option->add("ID_ENCUESTA", $this->createColumnOption("ID_ENCUESTA"));
            $option->add("ID_EQUIPOTORNEO", $this->createColumnOption("ID_EQUIPOTORNEO"));
            $option->add("EQUIPO", $this->createColumnOption("EQUIPO"));
            $option->add("GRUPO", $this->createColumnOption("GRUPO"));
            $option->add("POSICION", $this->createColumnOption("POSICION"));
            $option->add("NUMERACION", $this->createColumnOption("NUMERACION"));
            $option->add("crea_dato", $this->createColumnOption("crea_dato"));
            $option->add("modifica_dato", $this->createColumnOption("modifica_dato"));
            $option->add("ID_PARTICIPANTE", $this->createColumnOption("ID_PARTICIPANTE"));
        }

        // Set up options default
        foreach ($options as $name => $option) {
            if ($name != "column") { // Always use dropdown for column
                $option->UseDropDownButton = false;
                $option->UseButtonGroup = true;
            }
            //$option->ButtonClass = ""; // Class for button group
            $item = &$option->addGroupOption();
            $item->Body = "";
            $item->Visible = false;
        }
        $options["addedit"]->DropDownButtonPhrase = $Language->phrase("ButtonAddEdit");
        $options["detail"]->DropDownButtonPhrase = $Language->phrase("ButtonDetails");
        $options["action"]->DropDownButtonPhrase = $Language->phrase("ButtonActions");

        // Filter button
        $item = &$this->FilterOptions->add("savecurrentfilter");
        $item->Body = "<a class=\"ew-save-filter\" data-form=\"fpronosticadorsrch\" data-ew-action=\"none\">" . $Language->phrase("SaveCurrentFilter") . "</a>";
        $item->Visible = true;
        $item = &$this->FilterOptions->add("deletefilter");
        $item->Body = "<a class=\"ew-delete-filter\" data-form=\"fpronosticadorsrch\" data-ew-action=\"none\">" . $Language->phrase("DeleteFilter") . "</a>";
        $item->Visible = true;
        $this->FilterOptions->UseDropDownButton = true;
        $this->FilterOptions->UseButtonGroup = !$this->FilterOptions->UseDropDownButton;
        $this->FilterOptions->DropDownButtonPhrase = $Language->phrase("Filters");

        // Add group option item
        $item = &$this->FilterOptions->addGroupOption();
        $item->Body = "";
        $item->Visible = false;
    }

    // Create new column option
    public function createColumnOption($name)
    {
        $field = $this->Fields[$name] ?? false;
        if ($field && $field->Visible) {
            $item = new ListOption($field->Name);
            $item->Body = '<button class="dropdown-item">' .
                '<div class="form-check ew-dropdown-checkbox">' .
                '<div class="form-check-input ew-dropdown-check-input" data-field="' . $field->Param . '"></div>' .
                '<label class="form-check-label ew-dropdown-check-label">' . $field->caption() . '</label></div></button>';
            return $item;
        }
        return null;
    }

    // Render other options
    public function renderOtherOptions()
    {
        global $Language, $Security;
        $options = &$this->OtherOptions;
        $option = $options["action"];
        // Set up list action buttons
        foreach ($this->ListActions->Items as $listaction) {
            if ($listaction->Select == ACTION_MULTIPLE) {
                $item = &$option->add("custom_" . $listaction->Action);
                $caption = $listaction->Caption;
                $icon = ($listaction->Icon != "") ? '<i class="' . HtmlEncode($listaction->Icon) . '" data-caption="' . HtmlEncode($caption) . '"></i>' . $caption : $caption;
                $item->Body = '<button type="button" class="btn btn-default ew-action ew-list-action" title="' . HtmlEncode($caption) . '" data-caption="' . HtmlEncode($caption) . '" data-ew-action="submit" form="fpronosticadorlist"' . $listaction->toDataAttrs() . '>' . $icon . '</button>';
                $item->Visible = $listaction->Allow;
            }
        }

        // Hide grid edit and other options
        if ($this->TotalRecords <= 0) {
            $option = $options["addedit"];
            $item = $option["gridedit"];
            if ($item) {
                $item->Visible = false;
            }
            $option = $options["action"];
            $option->hideAllOptions();
        }
    }

    // Process list action
    protected function processListAction()
    {
        global $Language, $Security, $Response;
        $userlist = "";
        $user = "";
        $filter = $this->getFilterFromRecordKeys();
        $userAction = Post("useraction", "");
        if ($filter != "" && $userAction != "") {
            // Check permission first
            $actionCaption = $userAction;
            if (array_key_exists($userAction, $this->ListActions->Items)) {
                $actionCaption = $this->ListActions[$userAction]->Caption;
                if (!$this->ListActions[$userAction]->Allow) {
                    $errmsg = str_replace('%s', $actionCaption, $Language->phrase("CustomActionNotAllowed"));
                    if (Post("ajax") == $userAction) { // Ajax
                        echo "<p class=\"text-danger\">" . $errmsg . "</p>";
                        return true;
                    } else {
                        $this->setFailureMessage($errmsg);
                        return false;
                    }
                }
            }
            $this->CurrentFilter = $filter;
            $sql = $this->getCurrentSql();
            $conn = $this->getConnection();
            $rs = LoadRecordset($sql, $conn);
            $this->UserAction = $userAction;
            $this->ActionValue = Post("actionvalue");

            // Call row action event
            if ($rs) {
                if ($this->UseTransaction) {
                    $conn->beginTransaction();
                }
                $this->SelectedCount = $rs->recordCount();
                $this->SelectedIndex = 0;
                while (!$rs->EOF) {
                    $this->SelectedIndex++;
                    $row = $rs->fields;
                    $processed = $this->rowCustomAction($userAction, $row);
                    if (!$processed) {
                        break;
                    }
                    $rs->moveNext();
                }
                if ($processed) {
                    if ($this->UseTransaction) { // Commit transaction
                        $conn->commit();
                    }
                    if ($this->getSuccessMessage() == "" && !ob_get_length() && !$Response->getBody()->getSize()) { // No output
                        $this->setSuccessMessage(str_replace('%s', $actionCaption, $Language->phrase("CustomActionCompleted"))); // Set up success message
                    }
                } else {
                    if ($this->UseTransaction) { // Rollback transaction
                        $conn->rollback();
                    }

                    // Set up error message
                    if ($this->getSuccessMessage() != "" || $this->getFailureMessage() != "") {
                        // Use the message, do nothing
                    } elseif ($this->CancelMessage != "") {
                        $this->setFailureMessage($this->CancelMessage);
                        $this->CancelMessage = "";
                    } else {
                        $this->setFailureMessage(str_replace('%s', $actionCaption, $Language->phrase("CustomActionFailed")));
                    }
                }
            }
            if ($rs) {
                $rs->close();
            }
            if (Post("ajax") == $userAction) { // Ajax
                if ($this->getSuccessMessage() != "") {
                    echo "<p class=\"text-success\">" . $this->getSuccessMessage() . "</p>";
                    $this->clearSuccessMessage(); // Clear message
                }
                if ($this->getFailureMessage() != "") {
                    echo "<p class=\"text-danger\">" . $this->getFailureMessage() . "</p>";
                    $this->clearFailureMessage(); // Clear message
                }
                return true;
            }
        }
        return false; // Not ajax request
    }

    // Load default values
    protected function loadDefaultValues()
    {
        $this->usuario_dato->DefaultValue = "admin";
        $this->usuario_dato->OldValue = $this->usuario_dato->DefaultValue;
    }

    // Load basic search values
    protected function loadBasicSearchValues()
    {
        $this->BasicSearch->setKeyword(Get(Config("TABLE_BASIC_SEARCH"), ""), false);
        if ($this->BasicSearch->Keyword != "" && $this->Command == "") {
            $this->Command = "search";
        }
        $this->BasicSearch->setType(Get(Config("TABLE_BASIC_SEARCH_TYPE"), ""), false);
    }

    // Load form values
    protected function loadFormValues()
    {
        // Load from form
        global $CurrentForm;
        $validate = !Config("SERVER_VALIDATE");

        // Check field name 'ID_ENCUESTA' first before field var 'x_ID_ENCUESTA'
        $val = $CurrentForm->hasValue("ID_ENCUESTA") ? $CurrentForm->getValue("ID_ENCUESTA") : $CurrentForm->getValue("x_ID_ENCUESTA");
        if (!$this->ID_ENCUESTA->IsDetailKey && !$this->isGridAdd() && !$this->isAdd()) {
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

        // Check field name 'ID_PARTICIPANTE' first before field var 'x_ID_PARTICIPANTE'
        $val = $CurrentForm->hasValue("ID_PARTICIPANTE") ? $CurrentForm->getValue("ID_PARTICIPANTE") : $CurrentForm->getValue("x_ID_PARTICIPANTE");
        if (!$this->ID_PARTICIPANTE->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->ID_PARTICIPANTE->Visible = false; // Disable update for API request
            } else {
                $this->ID_PARTICIPANTE->setFormValue($val);
            }
        }
    }

    // Restore form values
    public function restoreFormValues()
    {
        global $CurrentForm;
        if (!$this->isGridAdd() && !$this->isAdd()) {
            $this->ID_ENCUESTA->CurrentValue = $this->ID_ENCUESTA->FormValue;
        }
        $this->ID_EQUIPOTORNEO->CurrentValue = $this->ID_EQUIPOTORNEO->FormValue;
        $this->EQUIPO->CurrentValue = $this->EQUIPO->FormValue;
        $this->GRUPO->CurrentValue = $this->GRUPO->FormValue;
        $this->POSICION->CurrentValue = $this->POSICION->FormValue;
        $this->NUMERACION->CurrentValue = $this->NUMERACION->FormValue;
        $this->crea_dato->CurrentValue = $this->crea_dato->FormValue;
        $this->crea_dato->CurrentValue = UnFormatDateTime($this->crea_dato->CurrentValue, $this->crea_dato->formatPattern());
        $this->modifica_dato->CurrentValue = $this->modifica_dato->FormValue;
        $this->modifica_dato->CurrentValue = UnFormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern());
        $this->ID_PARTICIPANTE->CurrentValue = $this->ID_PARTICIPANTE->FormValue;
    }

    // Load recordset
    public function loadRecordset($offset = -1, $rowcnt = -1)
    {
        // Load List page SQL (QueryBuilder)
        $sql = $this->getListSql();

        // Load recordset
        if ($offset > -1) {
            $sql->setFirstResult($offset);
        }
        if ($rowcnt > 0) {
            $sql->setMaxResults($rowcnt);
        }
        $result = $sql->execute();
        $rs = new Recordset($result, $sql);

        // Call Recordset Selected event
        $this->recordsetSelected($rs);
        return $rs;
    }

    // Load records as associative array
    public function loadRows($offset = -1, $rowcnt = -1)
    {
        // Load List page SQL (QueryBuilder)
        $sql = $this->getListSql();

        // Load recordset
        if ($offset > -1) {
            $sql->setFirstResult($offset);
        }
        if ($rowcnt > 0) {
            $sql->setMaxResults($rowcnt);
        }
        $result = $sql->execute();
        return $result->fetchAll(FetchMode::ASSOCIATIVE);
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
        $this->ViewUrl = $this->getViewUrl();
        $this->EditUrl = $this->getEditUrl();
        $this->InlineEditUrl = $this->getInlineEditUrl();
        $this->CopyUrl = $this->getCopyUrl();
        $this->InlineCopyUrl = $this->getInlineCopyUrl();
        $this->DeleteUrl = $this->getDeleteUrl();

        // Call Row_Rendering event
        $this->rowRendering();

        // Common render codes for all row types

        // ID_ENCUESTA

        // ID_EQUIPOTORNEO

        // EQUIPO

        // GRUPO

        // POSICION

        // NUMERACION

        // crea_dato

        // modifica_dato

        // usuario_dato

        // ID_PARTICIPANTE

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
            $this->ID_ENCUESTA->TooltipValue = "";

            // ID_EQUIPOTORNEO
            $this->ID_EQUIPOTORNEO->LinkCustomAttributes = "";
            $this->ID_EQUIPOTORNEO->HrefValue = "";
            $this->ID_EQUIPOTORNEO->TooltipValue = "";

            // EQUIPO
            $this->EQUIPO->LinkCustomAttributes = "";
            $this->EQUIPO->HrefValue = "";
            $this->EQUIPO->TooltipValue = "";

            // GRUPO
            $this->GRUPO->LinkCustomAttributes = "";
            $this->GRUPO->HrefValue = "";
            $this->GRUPO->TooltipValue = "";

            // POSICION
            $this->POSICION->LinkCustomAttributes = "";
            $this->POSICION->HrefValue = "";
            $this->POSICION->TooltipValue = "";

            // NUMERACION
            $this->NUMERACION->LinkCustomAttributes = "";
            $this->NUMERACION->HrefValue = "";
            $this->NUMERACION->TooltipValue = "";

            // crea_dato
            $this->crea_dato->LinkCustomAttributes = "";
            $this->crea_dato->HrefValue = "";
            $this->crea_dato->TooltipValue = "";

            // modifica_dato
            $this->modifica_dato->LinkCustomAttributes = "";
            $this->modifica_dato->HrefValue = "";
            $this->modifica_dato->TooltipValue = "";

            // ID_PARTICIPANTE
            $this->ID_PARTICIPANTE->LinkCustomAttributes = "";
            $this->ID_PARTICIPANTE->HrefValue = "";
            $this->ID_PARTICIPANTE->TooltipValue = "";
        } elseif ($this->RowType == ROWTYPE_ADD) {
            // ID_ENCUESTA

            // ID_EQUIPOTORNEO
            $this->ID_EQUIPOTORNEO->setupEditAttributes();
            $this->ID_EQUIPOTORNEO->EditCustomAttributes = "";
            $this->ID_EQUIPOTORNEO->PlaceHolder = RemoveHtml($this->ID_EQUIPOTORNEO->caption());

            // EQUIPO
            $this->EQUIPO->setupEditAttributes();
            $this->EQUIPO->EditCustomAttributes = "";
            $this->EQUIPO->PlaceHolder = RemoveHtml($this->EQUIPO->caption());

            // GRUPO
            $this->GRUPO->setupEditAttributes();
            $this->GRUPO->EditCustomAttributes = "";
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
            $this->crea_dato->EditValue = HtmlEncode(FormatDateTime($this->crea_dato->CurrentValue, $this->crea_dato->formatPattern()));
            $this->crea_dato->PlaceHolder = RemoveHtml($this->crea_dato->caption());

            // modifica_dato
            $this->modifica_dato->setupEditAttributes();
            $this->modifica_dato->EditCustomAttributes = "";
            $this->modifica_dato->EditValue = HtmlEncode(FormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern()));
            $this->modifica_dato->PlaceHolder = RemoveHtml($this->modifica_dato->caption());

            // ID_PARTICIPANTE
            $this->ID_PARTICIPANTE->setupEditAttributes();
            $this->ID_PARTICIPANTE->EditCustomAttributes = "";
            $this->ID_PARTICIPANTE->PlaceHolder = RemoveHtml($this->ID_PARTICIPANTE->caption());

            // Add refer script

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

            // modifica_dato
            $this->modifica_dato->LinkCustomAttributes = "";
            $this->modifica_dato->HrefValue = "";

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

    // Add record
    protected function addRow($rsold = null)
    {
        global $Language, $Security;

        // Set new row
        $rsnew = [];

        // ID_EQUIPOTORNEO
        $this->ID_EQUIPOTORNEO->setDbValueDef($rsnew, $this->ID_EQUIPOTORNEO->CurrentValue, 0, false);

        // EQUIPO
        $this->EQUIPO->setDbValueDef($rsnew, $this->EQUIPO->CurrentValue, null, false);

        // GRUPO
        $this->GRUPO->setDbValueDef($rsnew, $this->GRUPO->CurrentValue, null, false);

        // POSICION
        $this->POSICION->setDbValueDef($rsnew, $this->POSICION->CurrentValue, null, false);

        // NUMERACION
        $this->NUMERACION->setDbValueDef($rsnew, $this->NUMERACION->CurrentValue, null, false);

        // crea_dato
        $this->crea_dato->setDbValueDef($rsnew, UnFormatDateTime($this->crea_dato->CurrentValue, $this->crea_dato->formatPattern()), null, false);

        // modifica_dato
        $this->modifica_dato->setDbValueDef($rsnew, UnFormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern()), null, false);

        // ID_PARTICIPANTE
        $this->ID_PARTICIPANTE->setDbValueDef($rsnew, $this->ID_PARTICIPANTE->CurrentValue, null, false);

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

    // Set up search options
    protected function setupSearchOptions()
    {
        global $Language, $Security;
        $pageUrl = $this->pageUrl(false);
        $this->SearchOptions = new ListOptions(["TagClassName" => "ew-search-option"]);

        // Search button
        $item = &$this->SearchOptions->add("searchtoggle");
        $searchToggleClass = ($this->SearchWhere != "") ? " active" : " active";
        $item->Body = "<a class=\"btn btn-default ew-search-toggle" . $searchToggleClass . "\" role=\"button\" title=\"" . $Language->phrase("SearchPanel") . "\" data-caption=\"" . $Language->phrase("SearchPanel") . "\" data-ew-action=\"search-toggle\" data-form=\"fpronosticadorsrch\" aria-pressed=\"" . ($searchToggleClass == " active" ? "true" : "false") . "\">" . $Language->phrase("SearchLink") . "</a>";
        $item->Visible = true;

        // Show all button
        $item = &$this->SearchOptions->add("showall");
        $item->Body = "<a class=\"btn btn-default ew-show-all\" title=\"" . $Language->phrase("ShowAll") . "\" data-caption=\"" . $Language->phrase("ShowAll") . "\" href=\"" . $pageUrl . "cmd=reset\">" . $Language->phrase("ShowAllBtn") . "</a>";
        $item->Visible = ($this->SearchWhere != $this->DefaultSearchWhere && $this->SearchWhere != "0=101");

        // Button group for search
        $this->SearchOptions->UseDropDownButton = false;
        $this->SearchOptions->UseButtonGroup = true;
        $this->SearchOptions->DropDownButtonPhrase = $Language->phrase("ButtonSearch");

        // Add group option item
        $item = &$this->SearchOptions->addGroupOption();
        $item->Body = "";
        $item->Visible = false;

        // Hide search options
        if ($this->isExport() || $this->CurrentAction) {
            $this->SearchOptions->hideAllOptions();
        }
        if (!$Security->canSearch()) {
            $this->SearchOptions->hideAllOptions();
            $this->FilterOptions->hideAllOptions();
        }
    }

    // Check if any search fields
    public function hasSearchFields()
    {
        return true;
    }

    // Render search options
    protected function renderSearchOptions()
    {
        if (!$this->hasSearchFields() && $this->SearchOptions["searchtoggle"]) {
            $this->SearchOptions["searchtoggle"]->Visible = false;
        }
    }

    // Set up Breadcrumb
    protected function setupBreadcrumb()
    {
        global $Breadcrumb, $Language;
        $Breadcrumb = new Breadcrumb("index");
        $url = CurrentUrl();
        $url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
        $Breadcrumb->add("list", $this->TableVar, $url, "", $this->TableVar, true);
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
            $pageNo = Get(Config("TABLE_PAGE_NO"));
            if ($pageNo !== null) { // Check for "pageno" parameter first
                $pageNo = ParseInteger($pageNo);
                if (is_numeric($pageNo)) {
                    $this->StartRecord = ($pageNo - 1) * $this->DisplayRecords + 1;
                    if ($this->StartRecord <= 0) {
                        $this->StartRecord = 1;
                    } elseif ($this->StartRecord >= (int)(($this->TotalRecords - 1) / $this->DisplayRecords) * $this->DisplayRecords + 1) {
                        $this->StartRecord = (int)(($this->TotalRecords - 1) / $this->DisplayRecords) * $this->DisplayRecords + 1;
                    }
                    $this->setStartRecordNumber($this->StartRecord);
                }
            } elseif ($startRec !== null && is_numeric($startRec)) { // Check for "start" parameter
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

    // ListOptions Load event
    public function listOptionsLoad()
    {
        // Example:
        //$opt = &$this->ListOptions->Add("new");
        //$opt->Header = "xxx";
        //$opt->OnLeft = true; // Link on left
        //$opt->MoveTo(0); // Move to first column
    }

    // ListOptions Rendering event
    public function listOptionsRendering()
    {
        //Container("DetailTableGrid")->DetailAdd = (...condition...); // Set to true or false conditionally
        //Container("DetailTableGrid")->DetailEdit = (...condition...); // Set to true or false conditionally
        //Container("DetailTableGrid")->DetailView = (...condition...); // Set to true or false conditionally
    }

    // ListOptions Rendered event
    public function listOptionsRendered()
    {
        // Example:
        //$this->ListOptions["new"]->Body = "xxx";
    }

    // Row Custom Action event
    public function rowCustomAction($action, $row)
    {
        // Return false to abort
        return true;
    }

    // Page Exporting event
    // $this->ExportDoc = export document object
    public function pageExporting()
    {
        //$this->ExportDoc->Text = "my header"; // Export header
        //return false; // Return false to skip default export and use Row_Export event
        return true; // Return true to use default export and skip Row_Export event
    }

    // Row Export event
    // $this->ExportDoc = export document object
    public function rowExport($rs)
    {
        //$this->ExportDoc->Text .= "my content"; // Build HTML with field value: $rs["MyField"] or $this->MyField->ViewValue
    }

    // Page Exported event
    // $this->ExportDoc = export document object
    public function pageExported()
    {
        //$this->ExportDoc->Text .= "my footer"; // Export footer
        //Log($this->ExportDoc->Text);
    }

    // Page Importing event
    public function pageImporting($reader, &$options)
    {
        //var_dump($reader); // Import data reader
        //var_dump($options); // Show all options for importing
        //return false; // Return false to skip import
        return true;
    }

    // Row Import event
    public function rowImport(&$row, $cnt)
    {
        //Log($cnt); // Import record count
        //var_dump($row); // Import row
        //return false; // Return false to skip import
        return true;
    }

    // Page Imported event
    public function pageImported($reader, $results)
    {
        //var_dump($reader); // Import data reader
        //var_dump($results); // Import results
    }
}
