<?php

namespace PHPMaker2022\project11;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Page class
 */
class TorneoList extends Torneo
{
    use MessagesTrait;

    // Page ID
    public $PageID = "list";

    // Project ID
    public $ProjectID = PROJECT_ID;

    // Table name
    public $TableName = 'torneo';

    // Page object name
    public $PageObjName = "TorneoList";

    // View file path
    public $View = null;

    // Title
    public $Title = null; // Title for <title> tag

    // Rendering View
    public $RenderingView = false;

    // Grid form hidden field names
    public $FormName = "ftorneolist";
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

        // Table object (torneo)
        if (!isset($GLOBALS["torneo"]) || get_class($GLOBALS["torneo"]) == PROJECT_NAMESPACE . "torneo") {
            $GLOBALS["torneo"] = &$this;
        }

        // Page URL
        $pageUrl = $this->pageUrl(false);

        // Initialize URLs
        $this->AddUrl = "torneoadd";
        $this->InlineAddUrl = $pageUrl . "action=add";
        $this->GridAddUrl = $pageUrl . "action=gridadd";
        $this->GridEditUrl = $pageUrl . "action=gridedit";
        $this->MultiDeleteUrl = "torneodelete";
        $this->MultiUpdateUrl = "torneoupdate";

        // Table name (for backward compatibility only)
        if (!defined(PROJECT_NAMESPACE . "TABLE_NAME")) {
            define(PROJECT_NAMESPACE . "TABLE_NAME", 'torneo');
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
                $tbl = Container("torneo");
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
            $key .= @$ar['ID_TORNEO'];
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
            $this->ID_TORNEO->Visible = false;
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
        $this->ID_TORNEO->setVisibility();
        $this->NOM_TORNEO_CORTO->setVisibility();
        $this->NOM_TORNEO_LARGO->setVisibility();
        $this->PAIS_TORNEO->setVisibility();
        $this->REGION_TORNEO->setVisibility();
        $this->DETALLE_TORNEO->setVisibility();
        $this->LOGO_TORNEO->setVisibility();
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

        // Setup other options
        $this->setupOtherOptions();

        // Set up custom action (compatible with old version)
        foreach ($this->CustomActions as $name => $action) {
            $this->ListActions->add($name, $action);
        }

        // Set up lookup cache

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
        if (($keyValue = Get("ID_TORNEO") ?? Route("ID_TORNEO")) !== null) {
            $this->ID_TORNEO->setQueryStringValue($keyValue);
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
        if (!SameString($this->ID_TORNEO->OldValue, $this->ID_TORNEO->CurrentValue)) {
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
            $savedFilterList = $UserProfile->getSearchFilters(CurrentUserName(), "ftorneosrch");
        }
        $filterList = Concat($filterList, $this->ID_TORNEO->AdvancedSearch->toJson(), ","); // Field ID_TORNEO
        $filterList = Concat($filterList, $this->NOM_TORNEO_CORTO->AdvancedSearch->toJson(), ","); // Field NOM_TORNEO_CORTO
        $filterList = Concat($filterList, $this->NOM_TORNEO_LARGO->AdvancedSearch->toJson(), ","); // Field NOM_TORNEO_LARGO
        $filterList = Concat($filterList, $this->PAIS_TORNEO->AdvancedSearch->toJson(), ","); // Field PAIS_TORNEO
        $filterList = Concat($filterList, $this->REGION_TORNEO->AdvancedSearch->toJson(), ","); // Field REGION_TORNEO
        $filterList = Concat($filterList, $this->DETALLE_TORNEO->AdvancedSearch->toJson(), ","); // Field DETALLE_TORNEO
        $filterList = Concat($filterList, $this->crea_dato->AdvancedSearch->toJson(), ","); // Field crea_dato
        $filterList = Concat($filterList, $this->modifica_dato->AdvancedSearch->toJson(), ","); // Field modifica_dato
        $filterList = Concat($filterList, $this->usuario_dato->AdvancedSearch->toJson(), ","); // Field usuario_dato
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
            $UserProfile->setSearchFilters(CurrentUserName(), "ftorneosrch", $filters);
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

        // Field ID_TORNEO
        $this->ID_TORNEO->AdvancedSearch->SearchValue = @$filter["x_ID_TORNEO"];
        $this->ID_TORNEO->AdvancedSearch->SearchOperator = @$filter["z_ID_TORNEO"];
        $this->ID_TORNEO->AdvancedSearch->SearchCondition = @$filter["v_ID_TORNEO"];
        $this->ID_TORNEO->AdvancedSearch->SearchValue2 = @$filter["y_ID_TORNEO"];
        $this->ID_TORNEO->AdvancedSearch->SearchOperator2 = @$filter["w_ID_TORNEO"];
        $this->ID_TORNEO->AdvancedSearch->save();

        // Field NOM_TORNEO_CORTO
        $this->NOM_TORNEO_CORTO->AdvancedSearch->SearchValue = @$filter["x_NOM_TORNEO_CORTO"];
        $this->NOM_TORNEO_CORTO->AdvancedSearch->SearchOperator = @$filter["z_NOM_TORNEO_CORTO"];
        $this->NOM_TORNEO_CORTO->AdvancedSearch->SearchCondition = @$filter["v_NOM_TORNEO_CORTO"];
        $this->NOM_TORNEO_CORTO->AdvancedSearch->SearchValue2 = @$filter["y_NOM_TORNEO_CORTO"];
        $this->NOM_TORNEO_CORTO->AdvancedSearch->SearchOperator2 = @$filter["w_NOM_TORNEO_CORTO"];
        $this->NOM_TORNEO_CORTO->AdvancedSearch->save();

        // Field NOM_TORNEO_LARGO
        $this->NOM_TORNEO_LARGO->AdvancedSearch->SearchValue = @$filter["x_NOM_TORNEO_LARGO"];
        $this->NOM_TORNEO_LARGO->AdvancedSearch->SearchOperator = @$filter["z_NOM_TORNEO_LARGO"];
        $this->NOM_TORNEO_LARGO->AdvancedSearch->SearchCondition = @$filter["v_NOM_TORNEO_LARGO"];
        $this->NOM_TORNEO_LARGO->AdvancedSearch->SearchValue2 = @$filter["y_NOM_TORNEO_LARGO"];
        $this->NOM_TORNEO_LARGO->AdvancedSearch->SearchOperator2 = @$filter["w_NOM_TORNEO_LARGO"];
        $this->NOM_TORNEO_LARGO->AdvancedSearch->save();

        // Field PAIS_TORNEO
        $this->PAIS_TORNEO->AdvancedSearch->SearchValue = @$filter["x_PAIS_TORNEO"];
        $this->PAIS_TORNEO->AdvancedSearch->SearchOperator = @$filter["z_PAIS_TORNEO"];
        $this->PAIS_TORNEO->AdvancedSearch->SearchCondition = @$filter["v_PAIS_TORNEO"];
        $this->PAIS_TORNEO->AdvancedSearch->SearchValue2 = @$filter["y_PAIS_TORNEO"];
        $this->PAIS_TORNEO->AdvancedSearch->SearchOperator2 = @$filter["w_PAIS_TORNEO"];
        $this->PAIS_TORNEO->AdvancedSearch->save();

        // Field REGION_TORNEO
        $this->REGION_TORNEO->AdvancedSearch->SearchValue = @$filter["x_REGION_TORNEO"];
        $this->REGION_TORNEO->AdvancedSearch->SearchOperator = @$filter["z_REGION_TORNEO"];
        $this->REGION_TORNEO->AdvancedSearch->SearchCondition = @$filter["v_REGION_TORNEO"];
        $this->REGION_TORNEO->AdvancedSearch->SearchValue2 = @$filter["y_REGION_TORNEO"];
        $this->REGION_TORNEO->AdvancedSearch->SearchOperator2 = @$filter["w_REGION_TORNEO"];
        $this->REGION_TORNEO->AdvancedSearch->save();

        // Field DETALLE_TORNEO
        $this->DETALLE_TORNEO->AdvancedSearch->SearchValue = @$filter["x_DETALLE_TORNEO"];
        $this->DETALLE_TORNEO->AdvancedSearch->SearchOperator = @$filter["z_DETALLE_TORNEO"];
        $this->DETALLE_TORNEO->AdvancedSearch->SearchCondition = @$filter["v_DETALLE_TORNEO"];
        $this->DETALLE_TORNEO->AdvancedSearch->SearchValue2 = @$filter["y_DETALLE_TORNEO"];
        $this->DETALLE_TORNEO->AdvancedSearch->SearchOperator2 = @$filter["w_DETALLE_TORNEO"];
        $this->DETALLE_TORNEO->AdvancedSearch->save();

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
        $searchFlds[] = &$this->NOM_TORNEO_CORTO;
        $searchFlds[] = &$this->NOM_TORNEO_LARGO;
        $searchFlds[] = &$this->PAIS_TORNEO;
        $searchFlds[] = &$this->REGION_TORNEO;
        $searchFlds[] = &$this->DETALLE_TORNEO;
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
        }

        // Check for "order" parameter
        if (Get("order") !== null) {
            $this->CurrentOrder = Get("order");
            $this->CurrentOrderType = Get("ordertype", "");
            $this->updateSort($this->ID_TORNEO); // ID_TORNEO
            $this->updateSort($this->NOM_TORNEO_CORTO); // NOM_TORNEO_CORTO
            $this->updateSort($this->NOM_TORNEO_LARGO); // NOM_TORNEO_LARGO
            $this->updateSort($this->PAIS_TORNEO); // PAIS_TORNEO
            $this->updateSort($this->REGION_TORNEO); // REGION_TORNEO
            $this->updateSort($this->DETALLE_TORNEO); // DETALLE_TORNEO
            $this->updateSort($this->LOGO_TORNEO); // LOGO_TORNEO
            $this->updateSort($this->crea_dato); // crea_dato
            $this->updateSort($this->modifica_dato); // modifica_dato
            $this->updateSort($this->usuario_dato); // usuario_dato
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
                $this->ID_TORNEO->setSort("");
                $this->NOM_TORNEO_CORTO->setSort("");
                $this->NOM_TORNEO_LARGO->setSort("");
                $this->PAIS_TORNEO->setSort("");
                $this->REGION_TORNEO->setSort("");
                $this->DETALLE_TORNEO->setSort("");
                $this->LOGO_TORNEO->setSort("");
                $this->crea_dato->setSort("");
                $this->modifica_dato->setSort("");
                $this->usuario_dato->setSort("");
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
                "<button class=\"ew-grid-link ew-inline-update\" title=\"" . HtmlTitle($Language->phrase("UpdateLink")) . "\" data-caption=\"" . HtmlTitle($Language->phrase("UpdateLink")) . "\" form=\"ftorneolist\" formaction=\"" . HtmlEncode(GetUrl(UrlAddHash($this->pageName(), "r" . $this->RowCount . "_" . $this->TableVar))) . "\">" . $Language->phrase("UpdateLink") . "</button>&nbsp;" .
                "<a class=\"ew-grid-link ew-inline-cancel\" title=\"" . HtmlTitle($Language->phrase("CancelLink")) . "\" data-caption=\"" . HtmlTitle($Language->phrase("CancelLink")) . "\" href=\"" . $cancelurl . "\">" . $Language->phrase("CancelLink") . "</a>" .
                "<input type=\"hidden\" name=\"action\" id=\"action\" value=\"update\"></div>";
            $opt->Body .= "<input type=\"hidden\" name=\"k" . $this->RowIndex . "_key\" id=\"k" . $this->RowIndex . "_key\" value=\"" . HtmlEncode($this->ID_TORNEO->CurrentValue) . "\">";
            return;
        }
        if ($this->CurrentMode == "view") {
            // "view"
            $opt = $this->ListOptions["view"];
            $viewcaption = HtmlTitle($Language->phrase("ViewLink"));
            if ($Security->canView()) {
                $opt->Body = "<a class=\"ew-row-link ew-view\" title=\"" . $viewcaption . "\" data-caption=\"" . $viewcaption . "\" href=\"" . HtmlEncode(GetUrl($this->ViewUrl)) . "\">" . $Language->phrase("ViewLink") . "</a>";
            } else {
                $opt->Body = "";
            }

            // "edit"
            $opt = $this->ListOptions["edit"];
            $editcaption = HtmlTitle($Language->phrase("EditLink"));
            if ($Security->canEdit()) {
                $opt->Body = "<a class=\"ew-row-link ew-edit\" title=\"" . HtmlTitle($Language->phrase("EditLink")) . "\" data-caption=\"" . HtmlTitle($Language->phrase("EditLink")) . "\" href=\"" . HtmlEncode(GetUrl($this->EditUrl)) . "\">" . $Language->phrase("EditLink") . "</a>";
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
                    $link = "<li><button type=\"button\" class=\"dropdown-item ew-action ew-list-action\" data-caption=\"" . HtmlTitle($caption) . "\" data-ew-action=\"submit\" form=\"ftorneolist\" data-key=\"" . $this->keyToJson(true) . "\"" . $listaction->toDataAttrs() . ">" . $icon . $listaction->Caption . "</button></li>";
                    if ($link != "") {
                        $links[] = $link;
                        if ($body == "") { // Setup first button
                            $body = "<button type=\"button\" class=\"btn btn-default ew-action ew-list-action\" title=\"" . HtmlTitle($caption) . "\" data-caption=\"" . HtmlTitle($caption) . "\" data-ew-action=\"submit\" form=\"ftorneolist\" data-key=\"" . $this->keyToJson(true) . "\"" . $listaction->toDataAttrs() . ">" . $icon . $listaction->Caption . "</button>";
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
        $opt->Body = "<div class=\"form-check\"><input type=\"checkbox\" id=\"key_m_" . $this->RowCount . "\" name=\"key_m[]\" class=\"form-check-input ew-multi-select\" value=\"" . HtmlEncode($this->ID_TORNEO->CurrentValue) . "\" data-ew-action=\"select-key\"></div>";
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
        $item->Body = "<button type=\"button\" class=\"ew-action ew-multi-delete\" title=\"" . HtmlTitle($Language->phrase("DeleteSelectedLink")) . "\" data-caption=\"" . HtmlTitle($Language->phrase("DeleteSelectedLink")) . "\" form=\"ftorneolist\" data-ew-action=\"submit\" data-url=\"" . GetUrl($this->MultiDeleteUrl) . "\"data-data='{\"action\":\"show\"}'>" . $Language->phrase("DeleteSelectedLink") . "</button>";
        $item->Visible = $Security->canDelete();

        // Show column list for column visibility
        if ($this->UseColumnVisibility) {
            $option = $this->OtherOptions["column"];
            $item = &$option->addGroupOption();
            $item->Body = "";
            $item->Visible = $this->UseColumnVisibility;
            $option->add("ID_TORNEO", $this->createColumnOption("ID_TORNEO"));
            $option->add("NOM_TORNEO_CORTO", $this->createColumnOption("NOM_TORNEO_CORTO"));
            $option->add("NOM_TORNEO_LARGO", $this->createColumnOption("NOM_TORNEO_LARGO"));
            $option->add("PAIS_TORNEO", $this->createColumnOption("PAIS_TORNEO"));
            $option->add("REGION_TORNEO", $this->createColumnOption("REGION_TORNEO"));
            $option->add("DETALLE_TORNEO", $this->createColumnOption("DETALLE_TORNEO"));
            $option->add("LOGO_TORNEO", $this->createColumnOption("LOGO_TORNEO"));
            $option->add("crea_dato", $this->createColumnOption("crea_dato"));
            $option->add("modifica_dato", $this->createColumnOption("modifica_dato"));
            $option->add("usuario_dato", $this->createColumnOption("usuario_dato"));
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
        $item->Body = "<a class=\"ew-save-filter\" data-form=\"ftorneosrch\" data-ew-action=\"none\">" . $Language->phrase("SaveCurrentFilter") . "</a>";
        $item->Visible = true;
        $item = &$this->FilterOptions->add("deletefilter");
        $item->Body = "<a class=\"ew-delete-filter\" data-form=\"ftorneosrch\" data-ew-action=\"none\">" . $Language->phrase("DeleteFilter") . "</a>";
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
                $item->Body = '<button type="button" class="btn btn-default ew-action ew-list-action" title="' . HtmlEncode($caption) . '" data-caption="' . HtmlEncode($caption) . '" data-ew-action="submit" form="ftorneolist"' . $listaction->toDataAttrs() . '>' . $icon . '</button>';
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

    // Get upload files
    protected function getUploadFiles()
    {
        global $CurrentForm, $Language;
        $this->LOGO_TORNEO->Upload->Index = $CurrentForm->Index;
        $this->LOGO_TORNEO->Upload->uploadFile();
        $this->LOGO_TORNEO->CurrentValue = $this->LOGO_TORNEO->Upload->FileName;
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

        // Check field name 'ID_TORNEO' first before field var 'x_ID_TORNEO'
        $val = $CurrentForm->hasValue("ID_TORNEO") ? $CurrentForm->getValue("ID_TORNEO") : $CurrentForm->getValue("x_ID_TORNEO");
        if (!$this->ID_TORNEO->IsDetailKey && !$this->isGridAdd() && !$this->isAdd()) {
            $this->ID_TORNEO->setFormValue($val);
        }

        // Check field name 'NOM_TORNEO_CORTO' first before field var 'x_NOM_TORNEO_CORTO'
        $val = $CurrentForm->hasValue("NOM_TORNEO_CORTO") ? $CurrentForm->getValue("NOM_TORNEO_CORTO") : $CurrentForm->getValue("x_NOM_TORNEO_CORTO");
        if (!$this->NOM_TORNEO_CORTO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->NOM_TORNEO_CORTO->Visible = false; // Disable update for API request
            } else {
                $this->NOM_TORNEO_CORTO->setFormValue($val);
            }
        }

        // Check field name 'NOM_TORNEO_LARGO' first before field var 'x_NOM_TORNEO_LARGO'
        $val = $CurrentForm->hasValue("NOM_TORNEO_LARGO") ? $CurrentForm->getValue("NOM_TORNEO_LARGO") : $CurrentForm->getValue("x_NOM_TORNEO_LARGO");
        if (!$this->NOM_TORNEO_LARGO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->NOM_TORNEO_LARGO->Visible = false; // Disable update for API request
            } else {
                $this->NOM_TORNEO_LARGO->setFormValue($val);
            }
        }

        // Check field name 'PAIS_TORNEO' first before field var 'x_PAIS_TORNEO'
        $val = $CurrentForm->hasValue("PAIS_TORNEO") ? $CurrentForm->getValue("PAIS_TORNEO") : $CurrentForm->getValue("x_PAIS_TORNEO");
        if (!$this->PAIS_TORNEO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->PAIS_TORNEO->Visible = false; // Disable update for API request
            } else {
                $this->PAIS_TORNEO->setFormValue($val);
            }
        }

        // Check field name 'REGION_TORNEO' first before field var 'x_REGION_TORNEO'
        $val = $CurrentForm->hasValue("REGION_TORNEO") ? $CurrentForm->getValue("REGION_TORNEO") : $CurrentForm->getValue("x_REGION_TORNEO");
        if (!$this->REGION_TORNEO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->REGION_TORNEO->Visible = false; // Disable update for API request
            } else {
                $this->REGION_TORNEO->setFormValue($val);
            }
        }

        // Check field name 'DETALLE_TORNEO' first before field var 'x_DETALLE_TORNEO'
        $val = $CurrentForm->hasValue("DETALLE_TORNEO") ? $CurrentForm->getValue("DETALLE_TORNEO") : $CurrentForm->getValue("x_DETALLE_TORNEO");
        if (!$this->DETALLE_TORNEO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->DETALLE_TORNEO->Visible = false; // Disable update for API request
            } else {
                $this->DETALLE_TORNEO->setFormValue($val);
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
        $this->getUploadFiles(); // Get upload files
    }

    // Restore form values
    public function restoreFormValues()
    {
        global $CurrentForm;
        if (!$this->isGridAdd() && !$this->isAdd()) {
            $this->ID_TORNEO->CurrentValue = $this->ID_TORNEO->FormValue;
        }
        $this->NOM_TORNEO_CORTO->CurrentValue = $this->NOM_TORNEO_CORTO->FormValue;
        $this->NOM_TORNEO_LARGO->CurrentValue = $this->NOM_TORNEO_LARGO->FormValue;
        $this->PAIS_TORNEO->CurrentValue = $this->PAIS_TORNEO->FormValue;
        $this->REGION_TORNEO->CurrentValue = $this->REGION_TORNEO->FormValue;
        $this->DETALLE_TORNEO->CurrentValue = $this->DETALLE_TORNEO->FormValue;
        $this->crea_dato->CurrentValue = $this->crea_dato->FormValue;
        $this->crea_dato->CurrentValue = UnFormatDateTime($this->crea_dato->CurrentValue, $this->crea_dato->formatPattern());
        $this->modifica_dato->CurrentValue = $this->modifica_dato->FormValue;
        $this->modifica_dato->CurrentValue = UnFormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern());
        $this->usuario_dato->CurrentValue = $this->usuario_dato->FormValue;
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
        $this->ID_TORNEO->setDbValue($row['ID_TORNEO']);
        $this->NOM_TORNEO_CORTO->setDbValue($row['NOM_TORNEO_CORTO']);
        $this->NOM_TORNEO_LARGO->setDbValue($row['NOM_TORNEO_LARGO']);
        $this->PAIS_TORNEO->setDbValue($row['PAIS_TORNEO']);
        $this->REGION_TORNEO->setDbValue($row['REGION_TORNEO']);
        $this->DETALLE_TORNEO->setDbValue($row['DETALLE_TORNEO']);
        $this->LOGO_TORNEO->Upload->DbValue = $row['LOGO_TORNEO'];
        $this->LOGO_TORNEO->setDbValue($this->LOGO_TORNEO->Upload->DbValue);
        $this->crea_dato->setDbValue($row['crea_dato']);
        $this->modifica_dato->setDbValue($row['modifica_dato']);
        $this->usuario_dato->setDbValue($row['usuario_dato']);
    }

    // Return a row with default values
    protected function newRow()
    {
        $row = [];
        $row['ID_TORNEO'] = $this->ID_TORNEO->DefaultValue;
        $row['NOM_TORNEO_CORTO'] = $this->NOM_TORNEO_CORTO->DefaultValue;
        $row['NOM_TORNEO_LARGO'] = $this->NOM_TORNEO_LARGO->DefaultValue;
        $row['PAIS_TORNEO'] = $this->PAIS_TORNEO->DefaultValue;
        $row['REGION_TORNEO'] = $this->REGION_TORNEO->DefaultValue;
        $row['DETALLE_TORNEO'] = $this->DETALLE_TORNEO->DefaultValue;
        $row['LOGO_TORNEO'] = $this->LOGO_TORNEO->DefaultValue;
        $row['crea_dato'] = $this->crea_dato->DefaultValue;
        $row['modifica_dato'] = $this->modifica_dato->DefaultValue;
        $row['usuario_dato'] = $this->usuario_dato->DefaultValue;
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

        // ID_TORNEO

        // NOM_TORNEO_CORTO

        // NOM_TORNEO_LARGO

        // PAIS_TORNEO

        // REGION_TORNEO

        // DETALLE_TORNEO

        // LOGO_TORNEO

        // crea_dato

        // modifica_dato

        // usuario_dato

        // View row
        if ($this->RowType == ROWTYPE_VIEW) {
            // ID_TORNEO
            $this->ID_TORNEO->ViewValue = $this->ID_TORNEO->CurrentValue;
            $this->ID_TORNEO->ViewCustomAttributes = "";

            // NOM_TORNEO_CORTO
            $this->NOM_TORNEO_CORTO->ViewValue = $this->NOM_TORNEO_CORTO->CurrentValue;
            $this->NOM_TORNEO_CORTO->ViewCustomAttributes = "";

            // NOM_TORNEO_LARGO
            $this->NOM_TORNEO_LARGO->ViewValue = $this->NOM_TORNEO_LARGO->CurrentValue;
            $this->NOM_TORNEO_LARGO->ViewCustomAttributes = "";

            // PAIS_TORNEO
            $this->PAIS_TORNEO->ViewValue = $this->PAIS_TORNEO->CurrentValue;
            $this->PAIS_TORNEO->ViewCustomAttributes = "";

            // REGION_TORNEO
            $this->REGION_TORNEO->ViewValue = $this->REGION_TORNEO->CurrentValue;
            $this->REGION_TORNEO->ViewCustomAttributes = "";

            // DETALLE_TORNEO
            $this->DETALLE_TORNEO->ViewValue = $this->DETALLE_TORNEO->CurrentValue;
            $this->DETALLE_TORNEO->ViewCustomAttributes = "";

            // LOGO_TORNEO
            if (!EmptyValue($this->LOGO_TORNEO->Upload->DbValue)) {
                $this->LOGO_TORNEO->ImageWidth = 50;
                $this->LOGO_TORNEO->ImageHeight = 0;
                $this->LOGO_TORNEO->ImageAlt = $this->LOGO_TORNEO->alt();
                $this->LOGO_TORNEO->ImageCssClass = "ew-image";
                $this->LOGO_TORNEO->ViewValue = $this->LOGO_TORNEO->Upload->DbValue;
            } else {
                $this->LOGO_TORNEO->ViewValue = "";
            }
            $this->LOGO_TORNEO->ViewCustomAttributes = "";

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

            // NOM_TORNEO_CORTO
            $this->NOM_TORNEO_CORTO->LinkCustomAttributes = "";
            $this->NOM_TORNEO_CORTO->HrefValue = "";
            $this->NOM_TORNEO_CORTO->TooltipValue = "";

            // NOM_TORNEO_LARGO
            $this->NOM_TORNEO_LARGO->LinkCustomAttributes = "";
            $this->NOM_TORNEO_LARGO->HrefValue = "";
            $this->NOM_TORNEO_LARGO->TooltipValue = "";

            // PAIS_TORNEO
            $this->PAIS_TORNEO->LinkCustomAttributes = "";
            $this->PAIS_TORNEO->HrefValue = "";
            $this->PAIS_TORNEO->TooltipValue = "";

            // REGION_TORNEO
            $this->REGION_TORNEO->LinkCustomAttributes = "";
            $this->REGION_TORNEO->HrefValue = "";
            $this->REGION_TORNEO->TooltipValue = "";

            // DETALLE_TORNEO
            $this->DETALLE_TORNEO->LinkCustomAttributes = "";
            $this->DETALLE_TORNEO->HrefValue = "";
            $this->DETALLE_TORNEO->TooltipValue = "";

            // LOGO_TORNEO
            $this->LOGO_TORNEO->LinkCustomAttributes = "";
            if (!EmptyValue($this->LOGO_TORNEO->Upload->DbValue)) {
                $this->LOGO_TORNEO->HrefValue = GetFileUploadUrl($this->LOGO_TORNEO, $this->LOGO_TORNEO->htmlDecode($this->LOGO_TORNEO->Upload->DbValue)); // Add prefix/suffix
                $this->LOGO_TORNEO->LinkAttrs["target"] = ""; // Add target
                if ($this->isExport()) {
                    $this->LOGO_TORNEO->HrefValue = FullUrl($this->LOGO_TORNEO->HrefValue, "href");
                }
            } else {
                $this->LOGO_TORNEO->HrefValue = "";
            }
            $this->LOGO_TORNEO->ExportHrefValue = $this->LOGO_TORNEO->UploadPath . $this->LOGO_TORNEO->Upload->DbValue;
            $this->LOGO_TORNEO->TooltipValue = "";
            if ($this->LOGO_TORNEO->UseColorbox) {
                if (EmptyValue($this->LOGO_TORNEO->TooltipValue)) {
                    $this->LOGO_TORNEO->LinkAttrs["title"] = $Language->phrase("ViewImageGallery");
                }
                $this->LOGO_TORNEO->LinkAttrs["data-rel"] = "torneo_x" . $this->RowCount . "_LOGO_TORNEO";
                $this->LOGO_TORNEO->LinkAttrs->appendClass("ew-lightbox");
            }

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

            // NOM_TORNEO_CORTO
            $this->NOM_TORNEO_CORTO->setupEditAttributes();
            $this->NOM_TORNEO_CORTO->EditCustomAttributes = "";
            $this->NOM_TORNEO_CORTO->EditValue = HtmlEncode($this->NOM_TORNEO_CORTO->CurrentValue);
            $this->NOM_TORNEO_CORTO->PlaceHolder = RemoveHtml($this->NOM_TORNEO_CORTO->caption());

            // NOM_TORNEO_LARGO
            $this->NOM_TORNEO_LARGO->setupEditAttributes();
            $this->NOM_TORNEO_LARGO->EditCustomAttributes = "";
            $this->NOM_TORNEO_LARGO->EditValue = HtmlEncode($this->NOM_TORNEO_LARGO->CurrentValue);
            $this->NOM_TORNEO_LARGO->PlaceHolder = RemoveHtml($this->NOM_TORNEO_LARGO->caption());

            // PAIS_TORNEO
            $this->PAIS_TORNEO->setupEditAttributes();
            $this->PAIS_TORNEO->EditCustomAttributes = "";
            $this->PAIS_TORNEO->EditValue = HtmlEncode($this->PAIS_TORNEO->CurrentValue);
            $this->PAIS_TORNEO->PlaceHolder = RemoveHtml($this->PAIS_TORNEO->caption());

            // REGION_TORNEO
            $this->REGION_TORNEO->setupEditAttributes();
            $this->REGION_TORNEO->EditCustomAttributes = "";
            $this->REGION_TORNEO->EditValue = HtmlEncode($this->REGION_TORNEO->CurrentValue);
            $this->REGION_TORNEO->PlaceHolder = RemoveHtml($this->REGION_TORNEO->caption());

            // DETALLE_TORNEO
            $this->DETALLE_TORNEO->setupEditAttributes();
            $this->DETALLE_TORNEO->EditCustomAttributes = "";
            $this->DETALLE_TORNEO->EditValue = HtmlEncode($this->DETALLE_TORNEO->CurrentValue);
            $this->DETALLE_TORNEO->PlaceHolder = RemoveHtml($this->DETALLE_TORNEO->caption());

            // LOGO_TORNEO
            $this->LOGO_TORNEO->setupEditAttributes();
            $this->LOGO_TORNEO->EditCustomAttributes = "";
            if (!EmptyValue($this->LOGO_TORNEO->Upload->DbValue)) {
                $this->LOGO_TORNEO->ImageWidth = 50;
                $this->LOGO_TORNEO->ImageHeight = 0;
                $this->LOGO_TORNEO->ImageAlt = $this->LOGO_TORNEO->alt();
                $this->LOGO_TORNEO->ImageCssClass = "ew-image";
                $this->LOGO_TORNEO->EditValue = $this->LOGO_TORNEO->Upload->DbValue;
            } else {
                $this->LOGO_TORNEO->EditValue = "";
            }
            if (!EmptyValue($this->LOGO_TORNEO->CurrentValue)) {
                if ($this->RowIndex == '$rowindex$') {
                    $this->LOGO_TORNEO->Upload->FileName = "";
                } else {
                    $this->LOGO_TORNEO->Upload->FileName = $this->LOGO_TORNEO->CurrentValue;
                }
            }
            if (is_numeric($this->RowIndex)) {
                RenderUploadField($this->LOGO_TORNEO, $this->RowIndex);
            }

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

            // usuario_dato

            // Add refer script

            // ID_TORNEO
            $this->ID_TORNEO->LinkCustomAttributes = "";
            $this->ID_TORNEO->HrefValue = "";

            // NOM_TORNEO_CORTO
            $this->NOM_TORNEO_CORTO->LinkCustomAttributes = "";
            $this->NOM_TORNEO_CORTO->HrefValue = "";

            // NOM_TORNEO_LARGO
            $this->NOM_TORNEO_LARGO->LinkCustomAttributes = "";
            $this->NOM_TORNEO_LARGO->HrefValue = "";

            // PAIS_TORNEO
            $this->PAIS_TORNEO->LinkCustomAttributes = "";
            $this->PAIS_TORNEO->HrefValue = "";

            // REGION_TORNEO
            $this->REGION_TORNEO->LinkCustomAttributes = "";
            $this->REGION_TORNEO->HrefValue = "";

            // DETALLE_TORNEO
            $this->DETALLE_TORNEO->LinkCustomAttributes = "";
            $this->DETALLE_TORNEO->HrefValue = "";

            // LOGO_TORNEO
            $this->LOGO_TORNEO->LinkCustomAttributes = "";
            if (!EmptyValue($this->LOGO_TORNEO->Upload->DbValue)) {
                $this->LOGO_TORNEO->HrefValue = GetFileUploadUrl($this->LOGO_TORNEO, $this->LOGO_TORNEO->htmlDecode($this->LOGO_TORNEO->Upload->DbValue)); // Add prefix/suffix
                $this->LOGO_TORNEO->LinkAttrs["target"] = ""; // Add target
                if ($this->isExport()) {
                    $this->LOGO_TORNEO->HrefValue = FullUrl($this->LOGO_TORNEO->HrefValue, "href");
                }
            } else {
                $this->LOGO_TORNEO->HrefValue = "";
            }
            $this->LOGO_TORNEO->ExportHrefValue = $this->LOGO_TORNEO->UploadPath . $this->LOGO_TORNEO->Upload->DbValue;

            // crea_dato
            $this->crea_dato->LinkCustomAttributes = "";
            $this->crea_dato->HrefValue = "";

            // modifica_dato
            $this->modifica_dato->LinkCustomAttributes = "";
            $this->modifica_dato->HrefValue = "";

            // usuario_dato
            $this->usuario_dato->LinkCustomAttributes = "";
            $this->usuario_dato->HrefValue = "";
        } elseif ($this->RowType == ROWTYPE_EDIT) {
            // ID_TORNEO
            $this->ID_TORNEO->setupEditAttributes();
            $this->ID_TORNEO->EditCustomAttributes = "";
            $this->ID_TORNEO->EditValue = $this->ID_TORNEO->CurrentValue;
            $this->ID_TORNEO->ViewCustomAttributes = "";

            // NOM_TORNEO_CORTO
            $this->NOM_TORNEO_CORTO->setupEditAttributes();
            $this->NOM_TORNEO_CORTO->EditCustomAttributes = "";
            $this->NOM_TORNEO_CORTO->EditValue = HtmlEncode($this->NOM_TORNEO_CORTO->CurrentValue);
            $this->NOM_TORNEO_CORTO->PlaceHolder = RemoveHtml($this->NOM_TORNEO_CORTO->caption());

            // NOM_TORNEO_LARGO
            $this->NOM_TORNEO_LARGO->setupEditAttributes();
            $this->NOM_TORNEO_LARGO->EditCustomAttributes = "";
            $this->NOM_TORNEO_LARGO->EditValue = HtmlEncode($this->NOM_TORNEO_LARGO->CurrentValue);
            $this->NOM_TORNEO_LARGO->PlaceHolder = RemoveHtml($this->NOM_TORNEO_LARGO->caption());

            // PAIS_TORNEO
            $this->PAIS_TORNEO->setupEditAttributes();
            $this->PAIS_TORNEO->EditCustomAttributes = "";
            $this->PAIS_TORNEO->EditValue = HtmlEncode($this->PAIS_TORNEO->CurrentValue);
            $this->PAIS_TORNEO->PlaceHolder = RemoveHtml($this->PAIS_TORNEO->caption());

            // REGION_TORNEO
            $this->REGION_TORNEO->setupEditAttributes();
            $this->REGION_TORNEO->EditCustomAttributes = "";
            $this->REGION_TORNEO->EditValue = HtmlEncode($this->REGION_TORNEO->CurrentValue);
            $this->REGION_TORNEO->PlaceHolder = RemoveHtml($this->REGION_TORNEO->caption());

            // DETALLE_TORNEO
            $this->DETALLE_TORNEO->setupEditAttributes();
            $this->DETALLE_TORNEO->EditCustomAttributes = "";
            $this->DETALLE_TORNEO->EditValue = HtmlEncode($this->DETALLE_TORNEO->CurrentValue);
            $this->DETALLE_TORNEO->PlaceHolder = RemoveHtml($this->DETALLE_TORNEO->caption());

            // LOGO_TORNEO
            $this->LOGO_TORNEO->setupEditAttributes();
            $this->LOGO_TORNEO->EditCustomAttributes = "";
            if (!EmptyValue($this->LOGO_TORNEO->Upload->DbValue)) {
                $this->LOGO_TORNEO->ImageWidth = 50;
                $this->LOGO_TORNEO->ImageHeight = 0;
                $this->LOGO_TORNEO->ImageAlt = $this->LOGO_TORNEO->alt();
                $this->LOGO_TORNEO->ImageCssClass = "ew-image";
                $this->LOGO_TORNEO->EditValue = $this->LOGO_TORNEO->Upload->DbValue;
            } else {
                $this->LOGO_TORNEO->EditValue = "";
            }
            if (!EmptyValue($this->LOGO_TORNEO->CurrentValue)) {
                if ($this->RowIndex == '$rowindex$') {
                    $this->LOGO_TORNEO->Upload->FileName = "";
                } else {
                    $this->LOGO_TORNEO->Upload->FileName = $this->LOGO_TORNEO->CurrentValue;
                }
            }
            if (is_numeric($this->RowIndex)) {
                RenderUploadField($this->LOGO_TORNEO, $this->RowIndex);
            }

            // crea_dato
            $this->crea_dato->setupEditAttributes();
            $this->crea_dato->EditCustomAttributes = "";
            $this->crea_dato->EditValue = $this->crea_dato->CurrentValue;
            $this->crea_dato->EditValue = FormatDateTime($this->crea_dato->EditValue, $this->crea_dato->formatPattern());
            $this->crea_dato->CssClass = "fst-italic";
            $this->crea_dato->CellCssStyle .= "text-align: right;";
            $this->crea_dato->ViewCustomAttributes = "";

            // modifica_dato
            $this->modifica_dato->setupEditAttributes();
            $this->modifica_dato->EditCustomAttributes = "";
            $this->modifica_dato->EditValue = $this->modifica_dato->CurrentValue;
            $this->modifica_dato->EditValue = FormatDateTime($this->modifica_dato->EditValue, $this->modifica_dato->formatPattern());
            $this->modifica_dato->CssClass = "fst-italic";
            $this->modifica_dato->CellCssStyle .= "text-align: right;";
            $this->modifica_dato->ViewCustomAttributes = "";

            // usuario_dato

            // Edit refer script

            // ID_TORNEO
            $this->ID_TORNEO->LinkCustomAttributes = "";
            $this->ID_TORNEO->HrefValue = "";

            // NOM_TORNEO_CORTO
            $this->NOM_TORNEO_CORTO->LinkCustomAttributes = "";
            $this->NOM_TORNEO_CORTO->HrefValue = "";

            // NOM_TORNEO_LARGO
            $this->NOM_TORNEO_LARGO->LinkCustomAttributes = "";
            $this->NOM_TORNEO_LARGO->HrefValue = "";

            // PAIS_TORNEO
            $this->PAIS_TORNEO->LinkCustomAttributes = "";
            $this->PAIS_TORNEO->HrefValue = "";

            // REGION_TORNEO
            $this->REGION_TORNEO->LinkCustomAttributes = "";
            $this->REGION_TORNEO->HrefValue = "";

            // DETALLE_TORNEO
            $this->DETALLE_TORNEO->LinkCustomAttributes = "";
            $this->DETALLE_TORNEO->HrefValue = "";

            // LOGO_TORNEO
            $this->LOGO_TORNEO->LinkCustomAttributes = "";
            if (!EmptyValue($this->LOGO_TORNEO->Upload->DbValue)) {
                $this->LOGO_TORNEO->HrefValue = GetFileUploadUrl($this->LOGO_TORNEO, $this->LOGO_TORNEO->htmlDecode($this->LOGO_TORNEO->Upload->DbValue)); // Add prefix/suffix
                $this->LOGO_TORNEO->LinkAttrs["target"] = ""; // Add target
                if ($this->isExport()) {
                    $this->LOGO_TORNEO->HrefValue = FullUrl($this->LOGO_TORNEO->HrefValue, "href");
                }
            } else {
                $this->LOGO_TORNEO->HrefValue = "";
            }
            $this->LOGO_TORNEO->ExportHrefValue = $this->LOGO_TORNEO->UploadPath . $this->LOGO_TORNEO->Upload->DbValue;

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
        if ($this->NOM_TORNEO_CORTO->Required) {
            if (!$this->NOM_TORNEO_CORTO->IsDetailKey && EmptyValue($this->NOM_TORNEO_CORTO->FormValue)) {
                $this->NOM_TORNEO_CORTO->addErrorMessage(str_replace("%s", $this->NOM_TORNEO_CORTO->caption(), $this->NOM_TORNEO_CORTO->RequiredErrorMessage));
            }
        }
        if ($this->NOM_TORNEO_LARGO->Required) {
            if (!$this->NOM_TORNEO_LARGO->IsDetailKey && EmptyValue($this->NOM_TORNEO_LARGO->FormValue)) {
                $this->NOM_TORNEO_LARGO->addErrorMessage(str_replace("%s", $this->NOM_TORNEO_LARGO->caption(), $this->NOM_TORNEO_LARGO->RequiredErrorMessage));
            }
        }
        if ($this->PAIS_TORNEO->Required) {
            if (!$this->PAIS_TORNEO->IsDetailKey && EmptyValue($this->PAIS_TORNEO->FormValue)) {
                $this->PAIS_TORNEO->addErrorMessage(str_replace("%s", $this->PAIS_TORNEO->caption(), $this->PAIS_TORNEO->RequiredErrorMessage));
            }
        }
        if ($this->REGION_TORNEO->Required) {
            if (!$this->REGION_TORNEO->IsDetailKey && EmptyValue($this->REGION_TORNEO->FormValue)) {
                $this->REGION_TORNEO->addErrorMessage(str_replace("%s", $this->REGION_TORNEO->caption(), $this->REGION_TORNEO->RequiredErrorMessage));
            }
        }
        if ($this->DETALLE_TORNEO->Required) {
            if (!$this->DETALLE_TORNEO->IsDetailKey && EmptyValue($this->DETALLE_TORNEO->FormValue)) {
                $this->DETALLE_TORNEO->addErrorMessage(str_replace("%s", $this->DETALLE_TORNEO->caption(), $this->DETALLE_TORNEO->RequiredErrorMessage));
            }
        }
        if ($this->LOGO_TORNEO->Required) {
            if ($this->LOGO_TORNEO->Upload->FileName == "" && !$this->LOGO_TORNEO->Upload->KeepFile) {
                $this->LOGO_TORNEO->addErrorMessage(str_replace("%s", $this->LOGO_TORNEO->caption(), $this->LOGO_TORNEO->RequiredErrorMessage));
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

        // NOM_TORNEO_CORTO
        $this->NOM_TORNEO_CORTO->setDbValueDef($rsnew, $this->NOM_TORNEO_CORTO->CurrentValue, null, $this->NOM_TORNEO_CORTO->ReadOnly);

        // NOM_TORNEO_LARGO
        $this->NOM_TORNEO_LARGO->setDbValueDef($rsnew, $this->NOM_TORNEO_LARGO->CurrentValue, null, $this->NOM_TORNEO_LARGO->ReadOnly);

        // PAIS_TORNEO
        $this->PAIS_TORNEO->setDbValueDef($rsnew, $this->PAIS_TORNEO->CurrentValue, null, $this->PAIS_TORNEO->ReadOnly);

        // REGION_TORNEO
        $this->REGION_TORNEO->setDbValueDef($rsnew, $this->REGION_TORNEO->CurrentValue, null, $this->REGION_TORNEO->ReadOnly);

        // DETALLE_TORNEO
        $this->DETALLE_TORNEO->setDbValueDef($rsnew, $this->DETALLE_TORNEO->CurrentValue, null, $this->DETALLE_TORNEO->ReadOnly);

        // LOGO_TORNEO
        if ($this->LOGO_TORNEO->Visible && !$this->LOGO_TORNEO->ReadOnly && !$this->LOGO_TORNEO->Upload->KeepFile) {
            $this->LOGO_TORNEO->Upload->DbValue = $rsold['LOGO_TORNEO']; // Get original value
            if ($this->LOGO_TORNEO->Upload->FileName == "") {
                $rsnew['LOGO_TORNEO'] = null;
            } else {
                $rsnew['LOGO_TORNEO'] = $this->LOGO_TORNEO->Upload->FileName;
            }
        }

        // Update current values
        $this->setCurrentValues($rsnew);
        if ($this->LOGO_TORNEO->Visible && !$this->LOGO_TORNEO->Upload->KeepFile) {
            $oldFiles = EmptyValue($this->LOGO_TORNEO->Upload->DbValue) ? [] : [$this->LOGO_TORNEO->htmlDecode($this->LOGO_TORNEO->Upload->DbValue)];
            if (!EmptyValue($this->LOGO_TORNEO->Upload->FileName)) {
                $newFiles = [$this->LOGO_TORNEO->Upload->FileName];
                $NewFileCount = count($newFiles);
                for ($i = 0; $i < $NewFileCount; $i++) {
                    if ($newFiles[$i] != "") {
                        $file = $newFiles[$i];
                        $tempPath = UploadTempPath($this->LOGO_TORNEO, $this->LOGO_TORNEO->Upload->Index);
                        if (file_exists($tempPath . $file)) {
                            if (Config("DELETE_UPLOADED_FILES")) {
                                $oldFileFound = false;
                                $oldFileCount = count($oldFiles);
                                for ($j = 0; $j < $oldFileCount; $j++) {
                                    $oldFile = $oldFiles[$j];
                                    if ($oldFile == $file) { // Old file found, no need to delete anymore
                                        array_splice($oldFiles, $j, 1);
                                        $oldFileFound = true;
                                        break;
                                    }
                                }
                                if ($oldFileFound) { // No need to check if file exists further
                                    continue;
                                }
                            }
                            $file1 = UniqueFilename($this->LOGO_TORNEO->physicalUploadPath(), $file); // Get new file name
                            if ($file1 != $file) { // Rename temp file
                                while (file_exists($tempPath . $file1) || file_exists($this->LOGO_TORNEO->physicalUploadPath() . $file1)) { // Make sure no file name clash
                                    $file1 = UniqueFilename([$this->LOGO_TORNEO->physicalUploadPath(), $tempPath], $file1, true); // Use indexed name
                                }
                                rename($tempPath . $file, $tempPath . $file1);
                                $newFiles[$i] = $file1;
                            }
                        }
                    }
                }
                $this->LOGO_TORNEO->Upload->DbValue = empty($oldFiles) ? "" : implode(Config("MULTIPLE_UPLOAD_SEPARATOR"), $oldFiles);
                $this->LOGO_TORNEO->Upload->FileName = implode(Config("MULTIPLE_UPLOAD_SEPARATOR"), $newFiles);
                $this->LOGO_TORNEO->setDbValueDef($rsnew, $this->LOGO_TORNEO->Upload->FileName, null, $this->LOGO_TORNEO->ReadOnly);
            }
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
                if ($this->LOGO_TORNEO->Visible && !$this->LOGO_TORNEO->Upload->KeepFile) {
                    $oldFiles = EmptyValue($this->LOGO_TORNEO->Upload->DbValue) ? [] : [$this->LOGO_TORNEO->htmlDecode($this->LOGO_TORNEO->Upload->DbValue)];
                    if (!EmptyValue($this->LOGO_TORNEO->Upload->FileName)) {
                        $newFiles = [$this->LOGO_TORNEO->Upload->FileName];
                        $newFiles2 = [$this->LOGO_TORNEO->htmlDecode($rsnew['LOGO_TORNEO'])];
                        $newFileCount = count($newFiles);
                        for ($i = 0; $i < $newFileCount; $i++) {
                            if ($newFiles[$i] != "") {
                                $file = UploadTempPath($this->LOGO_TORNEO, $this->LOGO_TORNEO->Upload->Index) . $newFiles[$i];
                                if (file_exists($file)) {
                                    if (@$newFiles2[$i] != "") { // Use correct file name
                                        $newFiles[$i] = $newFiles2[$i];
                                    }
                                    if (!$this->LOGO_TORNEO->Upload->SaveToFile($newFiles[$i], true, $i)) { // Just replace
                                        $this->setFailureMessage($Language->phrase("UploadErrMsg7"));
                                        return false;
                                    }
                                }
                            }
                        }
                    } else {
                        $newFiles = [];
                    }
                    if (Config("DELETE_UPLOADED_FILES")) {
                        foreach ($oldFiles as $oldFile) {
                            if ($oldFile != "" && !in_array($oldFile, $newFiles)) {
                                @unlink($this->LOGO_TORNEO->oldPhysicalUploadPath() . $oldFile);
                            }
                        }
                    }
                }
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
            // LOGO_TORNEO
            CleanUploadTempPath($this->LOGO_TORNEO, $this->LOGO_TORNEO->Upload->Index);
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
        $hash .= GetFieldHash($row['NOM_TORNEO_CORTO']); // NOM_TORNEO_CORTO
        $hash .= GetFieldHash($row['NOM_TORNEO_LARGO']); // NOM_TORNEO_LARGO
        $hash .= GetFieldHash($row['PAIS_TORNEO']); // PAIS_TORNEO
        $hash .= GetFieldHash($row['REGION_TORNEO']); // REGION_TORNEO
        $hash .= GetFieldHash($row['DETALLE_TORNEO']); // DETALLE_TORNEO
        $hash .= GetFieldHash($row['LOGO_TORNEO']); // LOGO_TORNEO
        return md5($hash);
    }

    // Add record
    protected function addRow($rsold = null)
    {
        global $Language, $Security;

        // Set new row
        $rsnew = [];

        // NOM_TORNEO_CORTO
        $this->NOM_TORNEO_CORTO->setDbValueDef($rsnew, $this->NOM_TORNEO_CORTO->CurrentValue, null, false);

        // NOM_TORNEO_LARGO
        $this->NOM_TORNEO_LARGO->setDbValueDef($rsnew, $this->NOM_TORNEO_LARGO->CurrentValue, null, false);

        // PAIS_TORNEO
        $this->PAIS_TORNEO->setDbValueDef($rsnew, $this->PAIS_TORNEO->CurrentValue, null, false);

        // REGION_TORNEO
        $this->REGION_TORNEO->setDbValueDef($rsnew, $this->REGION_TORNEO->CurrentValue, null, false);

        // DETALLE_TORNEO
        $this->DETALLE_TORNEO->setDbValueDef($rsnew, $this->DETALLE_TORNEO->CurrentValue, null, false);

        // LOGO_TORNEO
        if ($this->LOGO_TORNEO->Visible && !$this->LOGO_TORNEO->Upload->KeepFile) {
            $this->LOGO_TORNEO->Upload->DbValue = ""; // No need to delete old file
            if ($this->LOGO_TORNEO->Upload->FileName == "") {
                $rsnew['LOGO_TORNEO'] = null;
            } else {
                $rsnew['LOGO_TORNEO'] = $this->LOGO_TORNEO->Upload->FileName;
            }
        }

        // crea_dato
        $this->crea_dato->setDbValueDef($rsnew, UnFormatDateTime($this->crea_dato->CurrentValue, $this->crea_dato->formatPattern()), null, false);

        // modifica_dato
        $this->modifica_dato->setDbValueDef($rsnew, UnFormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern()), null, false);

        // usuario_dato
        $this->usuario_dato->CurrentValue = CurrentUserName();
        $this->usuario_dato->setDbValueDef($rsnew, $this->usuario_dato->CurrentValue, null);
        if ($this->LOGO_TORNEO->Visible && !$this->LOGO_TORNEO->Upload->KeepFile) {
            $oldFiles = EmptyValue($this->LOGO_TORNEO->Upload->DbValue) ? [] : [$this->LOGO_TORNEO->htmlDecode($this->LOGO_TORNEO->Upload->DbValue)];
            if (!EmptyValue($this->LOGO_TORNEO->Upload->FileName)) {
                $newFiles = [$this->LOGO_TORNEO->Upload->FileName];
                $NewFileCount = count($newFiles);
                for ($i = 0; $i < $NewFileCount; $i++) {
                    if ($newFiles[$i] != "") {
                        $file = $newFiles[$i];
                        $tempPath = UploadTempPath($this->LOGO_TORNEO, $this->LOGO_TORNEO->Upload->Index);
                        if (file_exists($tempPath . $file)) {
                            if (Config("DELETE_UPLOADED_FILES")) {
                                $oldFileFound = false;
                                $oldFileCount = count($oldFiles);
                                for ($j = 0; $j < $oldFileCount; $j++) {
                                    $oldFile = $oldFiles[$j];
                                    if ($oldFile == $file) { // Old file found, no need to delete anymore
                                        array_splice($oldFiles, $j, 1);
                                        $oldFileFound = true;
                                        break;
                                    }
                                }
                                if ($oldFileFound) { // No need to check if file exists further
                                    continue;
                                }
                            }
                            $file1 = UniqueFilename($this->LOGO_TORNEO->physicalUploadPath(), $file); // Get new file name
                            if ($file1 != $file) { // Rename temp file
                                while (file_exists($tempPath . $file1) || file_exists($this->LOGO_TORNEO->physicalUploadPath() . $file1)) { // Make sure no file name clash
                                    $file1 = UniqueFilename([$this->LOGO_TORNEO->physicalUploadPath(), $tempPath], $file1, true); // Use indexed name
                                }
                                rename($tempPath . $file, $tempPath . $file1);
                                $newFiles[$i] = $file1;
                            }
                        }
                    }
                }
                $this->LOGO_TORNEO->Upload->DbValue = empty($oldFiles) ? "" : implode(Config("MULTIPLE_UPLOAD_SEPARATOR"), $oldFiles);
                $this->LOGO_TORNEO->Upload->FileName = implode(Config("MULTIPLE_UPLOAD_SEPARATOR"), $newFiles);
                $this->LOGO_TORNEO->setDbValueDef($rsnew, $this->LOGO_TORNEO->Upload->FileName, null, false);
            }
        }

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
                if ($this->LOGO_TORNEO->Visible && !$this->LOGO_TORNEO->Upload->KeepFile) {
                    $oldFiles = EmptyValue($this->LOGO_TORNEO->Upload->DbValue) ? [] : [$this->LOGO_TORNEO->htmlDecode($this->LOGO_TORNEO->Upload->DbValue)];
                    if (!EmptyValue($this->LOGO_TORNEO->Upload->FileName)) {
                        $newFiles = [$this->LOGO_TORNEO->Upload->FileName];
                        $newFiles2 = [$this->LOGO_TORNEO->htmlDecode($rsnew['LOGO_TORNEO'])];
                        $newFileCount = count($newFiles);
                        for ($i = 0; $i < $newFileCount; $i++) {
                            if ($newFiles[$i] != "") {
                                $file = UploadTempPath($this->LOGO_TORNEO, $this->LOGO_TORNEO->Upload->Index) . $newFiles[$i];
                                if (file_exists($file)) {
                                    if (@$newFiles2[$i] != "") { // Use correct file name
                                        $newFiles[$i] = $newFiles2[$i];
                                    }
                                    if (!$this->LOGO_TORNEO->Upload->SaveToFile($newFiles[$i], true, $i)) { // Just replace
                                        $this->setFailureMessage($Language->phrase("UploadErrMsg7"));
                                        return false;
                                    }
                                }
                            }
                        }
                    } else {
                        $newFiles = [];
                    }
                    if (Config("DELETE_UPLOADED_FILES")) {
                        foreach ($oldFiles as $oldFile) {
                            if ($oldFile != "" && !in_array($oldFile, $newFiles)) {
                                @unlink($this->LOGO_TORNEO->oldPhysicalUploadPath() . $oldFile);
                            }
                        }
                    }
                }
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
            // LOGO_TORNEO
            CleanUploadTempPath($this->LOGO_TORNEO, $this->LOGO_TORNEO->Upload->Index);
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
        $item->Body = "<a class=\"btn btn-default ew-search-toggle" . $searchToggleClass . "\" role=\"button\" title=\"" . $Language->phrase("SearchPanel") . "\" data-caption=\"" . $Language->phrase("SearchPanel") . "\" data-ew-action=\"search-toggle\" data-form=\"ftorneosrch\" aria-pressed=\"" . ($searchToggleClass == " active" ? "true" : "false") . "\">" . $Language->phrase("SearchLink") . "</a>";
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
