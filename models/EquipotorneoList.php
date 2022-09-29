<?php

namespace PHPMaker2022\project11;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Page class
 */
class EquipotorneoList extends Equipotorneo
{
    use MessagesTrait;

    // Page ID
    public $PageID = "list";

    // Project ID
    public $ProjectID = PROJECT_ID;

    // Table name
    public $TableName = 'equipotorneo';

    // Page object name
    public $PageObjName = "EquipotorneoList";

    // View file path
    public $View = null;

    // Title
    public $Title = null; // Title for <title> tag

    // Rendering View
    public $RenderingView = false;

    // Grid form hidden field names
    public $FormName = "fequipotorneolist";
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

        // Table object (equipotorneo)
        if (!isset($GLOBALS["equipotorneo"]) || get_class($GLOBALS["equipotorneo"]) == PROJECT_NAMESPACE . "equipotorneo") {
            $GLOBALS["equipotorneo"] = &$this;
        }

        // Page URL
        $pageUrl = $this->pageUrl(false);

        // Initialize URLs
        $this->AddUrl = "equipotorneoadd";
        $this->InlineAddUrl = $pageUrl . "action=add";
        $this->GridAddUrl = $pageUrl . "action=gridadd";
        $this->GridEditUrl = $pageUrl . "action=gridedit";
        $this->MultiDeleteUrl = "equipotorneodelete";
        $this->MultiUpdateUrl = "equipotorneoupdate";

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
        $this->ID_EQUIPO_TORNEO->setVisibility();
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

        // Setup other options
        $this->setupOtherOptions();

        // Set up custom action (compatible with old version)
        foreach ($this->CustomActions as $name => $action) {
            $this->ListActions->add($name, $action);
        }

        // Set up lookup cache
        $this->setupLookupOptions($this->ID_TORNEO);
        $this->setupLookupOptions($this->ID_EQUIPO);
        $this->setupLookupOptions($this->GRUPO);
        $this->setupLookupOptions($this->POSICION_EQUIPO_TORENO);

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
            AddFilter($this->DefaultSearchWhere, $this->advancedSearchWhere(true));

            // Get basic search values
            $this->loadBasicSearchValues();

            // Get and validate search values for advanced search
            if (EmptyValue($this->UserAction)) { // Skip if user action
                $this->loadSearchValues();
            }

            // Process filter list
            if ($this->processFilterList()) {
                $this->terminate();
                return;
            }
            if (!$this->validateSearch()) {
                // Nothing to do
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

            // Get search criteria for advanced search
            if (!$this->hasInvalidFields()) {
                $srchAdvanced = $this->advancedSearchWhere();
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

            // Load advanced search from default
            if ($this->loadAdvancedSearchDefault()) {
                $srchAdvanced = $this->advancedSearchWhere();
            }
        }

        // Restore search settings from Session
        if (!$this->hasInvalidFields()) {
            $this->loadAdvancedSearch();
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
        if (($keyValue = Get("ID_EQUIPO_TORNEO") ?? Route("ID_EQUIPO_TORNEO")) !== null) {
            $this->ID_EQUIPO_TORNEO->setQueryStringValue($keyValue);
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
        if (!SameString($this->ID_EQUIPO_TORNEO->OldValue, $this->ID_EQUIPO_TORNEO->CurrentValue)) {
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
            $savedFilterList = $UserProfile->getSearchFilters(CurrentUserName(), "fequipotorneosrch");
        }
        $filterList = Concat($filterList, $this->ID_EQUIPO_TORNEO->AdvancedSearch->toJson(), ","); // Field ID_EQUIPO_TORNEO
        $filterList = Concat($filterList, $this->ID_TORNEO->AdvancedSearch->toJson(), ","); // Field ID_TORNEO
        $filterList = Concat($filterList, $this->ID_EQUIPO->AdvancedSearch->toJson(), ","); // Field ID_EQUIPO
        $filterList = Concat($filterList, $this->PARTIDOS_JUGADOS->AdvancedSearch->toJson(), ","); // Field PARTIDOS_JUGADOS
        $filterList = Concat($filterList, $this->PARTIDOS_GANADOS->AdvancedSearch->toJson(), ","); // Field PARTIDOS_GANADOS
        $filterList = Concat($filterList, $this->PARTIDOS_EMPATADOS->AdvancedSearch->toJson(), ","); // Field PARTIDOS_EMPATADOS
        $filterList = Concat($filterList, $this->PARTIDOS_PERDIDOS->AdvancedSearch->toJson(), ","); // Field PARTIDOS_PERDIDOS
        $filterList = Concat($filterList, $this->GF->AdvancedSearch->toJson(), ","); // Field GF
        $filterList = Concat($filterList, $this->GC->AdvancedSearch->toJson(), ","); // Field GC
        $filterList = Concat($filterList, $this->GD->AdvancedSearch->toJson(), ","); // Field GD
        $filterList = Concat($filterList, $this->GRUPO->AdvancedSearch->toJson(), ","); // Field GRUPO
        $filterList = Concat($filterList, $this->POSICION_EQUIPO_TORENO->AdvancedSearch->toJson(), ","); // Field POSICION_EQUIPO_TORENO
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
            $UserProfile->setSearchFilters(CurrentUserName(), "fequipotorneosrch", $filters);
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

        // Field ID_EQUIPO_TORNEO
        $this->ID_EQUIPO_TORNEO->AdvancedSearch->SearchValue = @$filter["x_ID_EQUIPO_TORNEO"];
        $this->ID_EQUIPO_TORNEO->AdvancedSearch->SearchOperator = @$filter["z_ID_EQUIPO_TORNEO"];
        $this->ID_EQUIPO_TORNEO->AdvancedSearch->SearchCondition = @$filter["v_ID_EQUIPO_TORNEO"];
        $this->ID_EQUIPO_TORNEO->AdvancedSearch->SearchValue2 = @$filter["y_ID_EQUIPO_TORNEO"];
        $this->ID_EQUIPO_TORNEO->AdvancedSearch->SearchOperator2 = @$filter["w_ID_EQUIPO_TORNEO"];
        $this->ID_EQUIPO_TORNEO->AdvancedSearch->save();

        // Field ID_TORNEO
        $this->ID_TORNEO->AdvancedSearch->SearchValue = @$filter["x_ID_TORNEO"];
        $this->ID_TORNEO->AdvancedSearch->SearchOperator = @$filter["z_ID_TORNEO"];
        $this->ID_TORNEO->AdvancedSearch->SearchCondition = @$filter["v_ID_TORNEO"];
        $this->ID_TORNEO->AdvancedSearch->SearchValue2 = @$filter["y_ID_TORNEO"];
        $this->ID_TORNEO->AdvancedSearch->SearchOperator2 = @$filter["w_ID_TORNEO"];
        $this->ID_TORNEO->AdvancedSearch->save();

        // Field ID_EQUIPO
        $this->ID_EQUIPO->AdvancedSearch->SearchValue = @$filter["x_ID_EQUIPO"];
        $this->ID_EQUIPO->AdvancedSearch->SearchOperator = @$filter["z_ID_EQUIPO"];
        $this->ID_EQUIPO->AdvancedSearch->SearchCondition = @$filter["v_ID_EQUIPO"];
        $this->ID_EQUIPO->AdvancedSearch->SearchValue2 = @$filter["y_ID_EQUIPO"];
        $this->ID_EQUIPO->AdvancedSearch->SearchOperator2 = @$filter["w_ID_EQUIPO"];
        $this->ID_EQUIPO->AdvancedSearch->save();

        // Field PARTIDOS_JUGADOS
        $this->PARTIDOS_JUGADOS->AdvancedSearch->SearchValue = @$filter["x_PARTIDOS_JUGADOS"];
        $this->PARTIDOS_JUGADOS->AdvancedSearch->SearchOperator = @$filter["z_PARTIDOS_JUGADOS"];
        $this->PARTIDOS_JUGADOS->AdvancedSearch->SearchCondition = @$filter["v_PARTIDOS_JUGADOS"];
        $this->PARTIDOS_JUGADOS->AdvancedSearch->SearchValue2 = @$filter["y_PARTIDOS_JUGADOS"];
        $this->PARTIDOS_JUGADOS->AdvancedSearch->SearchOperator2 = @$filter["w_PARTIDOS_JUGADOS"];
        $this->PARTIDOS_JUGADOS->AdvancedSearch->save();

        // Field PARTIDOS_GANADOS
        $this->PARTIDOS_GANADOS->AdvancedSearch->SearchValue = @$filter["x_PARTIDOS_GANADOS"];
        $this->PARTIDOS_GANADOS->AdvancedSearch->SearchOperator = @$filter["z_PARTIDOS_GANADOS"];
        $this->PARTIDOS_GANADOS->AdvancedSearch->SearchCondition = @$filter["v_PARTIDOS_GANADOS"];
        $this->PARTIDOS_GANADOS->AdvancedSearch->SearchValue2 = @$filter["y_PARTIDOS_GANADOS"];
        $this->PARTIDOS_GANADOS->AdvancedSearch->SearchOperator2 = @$filter["w_PARTIDOS_GANADOS"];
        $this->PARTIDOS_GANADOS->AdvancedSearch->save();

        // Field PARTIDOS_EMPATADOS
        $this->PARTIDOS_EMPATADOS->AdvancedSearch->SearchValue = @$filter["x_PARTIDOS_EMPATADOS"];
        $this->PARTIDOS_EMPATADOS->AdvancedSearch->SearchOperator = @$filter["z_PARTIDOS_EMPATADOS"];
        $this->PARTIDOS_EMPATADOS->AdvancedSearch->SearchCondition = @$filter["v_PARTIDOS_EMPATADOS"];
        $this->PARTIDOS_EMPATADOS->AdvancedSearch->SearchValue2 = @$filter["y_PARTIDOS_EMPATADOS"];
        $this->PARTIDOS_EMPATADOS->AdvancedSearch->SearchOperator2 = @$filter["w_PARTIDOS_EMPATADOS"];
        $this->PARTIDOS_EMPATADOS->AdvancedSearch->save();

        // Field PARTIDOS_PERDIDOS
        $this->PARTIDOS_PERDIDOS->AdvancedSearch->SearchValue = @$filter["x_PARTIDOS_PERDIDOS"];
        $this->PARTIDOS_PERDIDOS->AdvancedSearch->SearchOperator = @$filter["z_PARTIDOS_PERDIDOS"];
        $this->PARTIDOS_PERDIDOS->AdvancedSearch->SearchCondition = @$filter["v_PARTIDOS_PERDIDOS"];
        $this->PARTIDOS_PERDIDOS->AdvancedSearch->SearchValue2 = @$filter["y_PARTIDOS_PERDIDOS"];
        $this->PARTIDOS_PERDIDOS->AdvancedSearch->SearchOperator2 = @$filter["w_PARTIDOS_PERDIDOS"];
        $this->PARTIDOS_PERDIDOS->AdvancedSearch->save();

        // Field GF
        $this->GF->AdvancedSearch->SearchValue = @$filter["x_GF"];
        $this->GF->AdvancedSearch->SearchOperator = @$filter["z_GF"];
        $this->GF->AdvancedSearch->SearchCondition = @$filter["v_GF"];
        $this->GF->AdvancedSearch->SearchValue2 = @$filter["y_GF"];
        $this->GF->AdvancedSearch->SearchOperator2 = @$filter["w_GF"];
        $this->GF->AdvancedSearch->save();

        // Field GC
        $this->GC->AdvancedSearch->SearchValue = @$filter["x_GC"];
        $this->GC->AdvancedSearch->SearchOperator = @$filter["z_GC"];
        $this->GC->AdvancedSearch->SearchCondition = @$filter["v_GC"];
        $this->GC->AdvancedSearch->SearchValue2 = @$filter["y_GC"];
        $this->GC->AdvancedSearch->SearchOperator2 = @$filter["w_GC"];
        $this->GC->AdvancedSearch->save();

        // Field GD
        $this->GD->AdvancedSearch->SearchValue = @$filter["x_GD"];
        $this->GD->AdvancedSearch->SearchOperator = @$filter["z_GD"];
        $this->GD->AdvancedSearch->SearchCondition = @$filter["v_GD"];
        $this->GD->AdvancedSearch->SearchValue2 = @$filter["y_GD"];
        $this->GD->AdvancedSearch->SearchOperator2 = @$filter["w_GD"];
        $this->GD->AdvancedSearch->save();

        // Field GRUPO
        $this->GRUPO->AdvancedSearch->SearchValue = @$filter["x_GRUPO"];
        $this->GRUPO->AdvancedSearch->SearchOperator = @$filter["z_GRUPO"];
        $this->GRUPO->AdvancedSearch->SearchCondition = @$filter["v_GRUPO"];
        $this->GRUPO->AdvancedSearch->SearchValue2 = @$filter["y_GRUPO"];
        $this->GRUPO->AdvancedSearch->SearchOperator2 = @$filter["w_GRUPO"];
        $this->GRUPO->AdvancedSearch->save();

        // Field POSICION_EQUIPO_TORENO
        $this->POSICION_EQUIPO_TORENO->AdvancedSearch->SearchValue = @$filter["x_POSICION_EQUIPO_TORENO"];
        $this->POSICION_EQUIPO_TORENO->AdvancedSearch->SearchOperator = @$filter["z_POSICION_EQUIPO_TORENO"];
        $this->POSICION_EQUIPO_TORENO->AdvancedSearch->SearchCondition = @$filter["v_POSICION_EQUIPO_TORENO"];
        $this->POSICION_EQUIPO_TORENO->AdvancedSearch->SearchValue2 = @$filter["y_POSICION_EQUIPO_TORENO"];
        $this->POSICION_EQUIPO_TORENO->AdvancedSearch->SearchOperator2 = @$filter["w_POSICION_EQUIPO_TORENO"];
        $this->POSICION_EQUIPO_TORENO->AdvancedSearch->save();

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

    // Advanced search WHERE clause based on QueryString
    protected function advancedSearchWhere($default = false)
    {
        global $Security;
        $where = "";
        if (!$Security->canSearch()) {
            return "";
        }
        $this->buildSearchSql($where, $this->ID_EQUIPO_TORNEO, $default, false); // ID_EQUIPO_TORNEO
        $this->buildSearchSql($where, $this->ID_TORNEO, $default, true); // ID_TORNEO
        $this->buildSearchSql($where, $this->ID_EQUIPO, $default, false); // ID_EQUIPO
        $this->buildSearchSql($where, $this->PARTIDOS_JUGADOS, $default, false); // PARTIDOS_JUGADOS
        $this->buildSearchSql($where, $this->PARTIDOS_GANADOS, $default, false); // PARTIDOS_GANADOS
        $this->buildSearchSql($where, $this->PARTIDOS_EMPATADOS, $default, false); // PARTIDOS_EMPATADOS
        $this->buildSearchSql($where, $this->PARTIDOS_PERDIDOS, $default, false); // PARTIDOS_PERDIDOS
        $this->buildSearchSql($where, $this->GF, $default, false); // GF
        $this->buildSearchSql($where, $this->GC, $default, false); // GC
        $this->buildSearchSql($where, $this->GD, $default, false); // GD
        $this->buildSearchSql($where, $this->GRUPO, $default, false); // GRUPO
        $this->buildSearchSql($where, $this->POSICION_EQUIPO_TORENO, $default, false); // POSICION_EQUIPO_TORENO
        $this->buildSearchSql($where, $this->crea_dato, $default, false); // crea_dato
        $this->buildSearchSql($where, $this->modifica_dato, $default, false); // modifica_dato
        $this->buildSearchSql($where, $this->usuario_dato, $default, false); // usuario_dato

        // Set up search parm
        if (!$default && $where != "" && in_array($this->Command, ["", "reset", "resetall"])) {
            $this->Command = "search";
        }
        if (!$default && $this->Command == "search") {
            $this->ID_EQUIPO_TORNEO->AdvancedSearch->save(); // ID_EQUIPO_TORNEO
            $this->ID_TORNEO->AdvancedSearch->save(); // ID_TORNEO
            $this->ID_EQUIPO->AdvancedSearch->save(); // ID_EQUIPO
            $this->PARTIDOS_JUGADOS->AdvancedSearch->save(); // PARTIDOS_JUGADOS
            $this->PARTIDOS_GANADOS->AdvancedSearch->save(); // PARTIDOS_GANADOS
            $this->PARTIDOS_EMPATADOS->AdvancedSearch->save(); // PARTIDOS_EMPATADOS
            $this->PARTIDOS_PERDIDOS->AdvancedSearch->save(); // PARTIDOS_PERDIDOS
            $this->GF->AdvancedSearch->save(); // GF
            $this->GC->AdvancedSearch->save(); // GC
            $this->GD->AdvancedSearch->save(); // GD
            $this->GRUPO->AdvancedSearch->save(); // GRUPO
            $this->POSICION_EQUIPO_TORENO->AdvancedSearch->save(); // POSICION_EQUIPO_TORENO
            $this->crea_dato->AdvancedSearch->save(); // crea_dato
            $this->modifica_dato->AdvancedSearch->save(); // modifica_dato
            $this->usuario_dato->AdvancedSearch->save(); // usuario_dato
        }
        return $where;
    }

    // Build search SQL
    protected function buildSearchSql(&$where, &$fld, $default, $multiValue)
    {
        $fldParm = $fld->Param;
        $fldVal = $default ? $fld->AdvancedSearch->SearchValueDefault : $fld->AdvancedSearch->SearchValue;
        $fldOpr = $default ? $fld->AdvancedSearch->SearchOperatorDefault : $fld->AdvancedSearch->SearchOperator;
        $fldCond = $default ? $fld->AdvancedSearch->SearchConditionDefault : $fld->AdvancedSearch->SearchCondition;
        $fldVal2 = $default ? $fld->AdvancedSearch->SearchValue2Default : $fld->AdvancedSearch->SearchValue2;
        $fldOpr2 = $default ? $fld->AdvancedSearch->SearchOperator2Default : $fld->AdvancedSearch->SearchOperator2;
        $wrk = "";
        if (is_array($fldVal)) {
            $fldVal = implode(Config("MULTIPLE_OPTION_SEPARATOR"), $fldVal);
        }
        if (is_array($fldVal2)) {
            $fldVal2 = implode(Config("MULTIPLE_OPTION_SEPARATOR"), $fldVal2);
        }
        $fldOpr = strtoupper(trim($fldOpr ?? ""));
        if ($fldOpr == "") {
            $fldOpr = "=";
        }
        $fldOpr2 = strtoupper(trim($fldOpr2 ?? ""));
        if ($fldOpr2 == "") {
            $fldOpr2 = "=";
        }
        if (Config("SEARCH_MULTI_VALUE_OPTION") == 1 && !$fld->UseFilter || !IsMultiSearchOperator($fldOpr)) {
            $multiValue = false;
        }
        if ($multiValue) {
            $wrk = $fldVal != "" ? GetMultiSearchSql($fld, $fldOpr, $fldVal, $this->Dbid) : ""; // Field value 1
            $wrk2 = $fldVal2 != "" ? GetMultiSearchSql($fld, $fldOpr2, $fldVal2, $this->Dbid) : ""; // Field value 2
            AddFilter($wrk, $wrk2, $fldCond);
        } else {
            $fldVal = $this->convertSearchValue($fld, $fldVal);
            $fldVal2 = $this->convertSearchValue($fld, $fldVal2);
            $wrk = GetSearchSql($fld, $fldVal, $fldOpr, $fldCond, $fldVal2, $fldOpr2, $this->Dbid);
        }
        if ($this->SearchOption == "AUTO" && in_array($this->BasicSearch->getType(), ["AND", "OR"])) {
            $cond = $this->BasicSearch->getType();
        } else {
            $cond = SameText($this->SearchOption, "OR") ? "OR" : "AND";
        }
        AddFilter($where, $wrk, $cond);
    }

    // Convert search value
    protected function convertSearchValue(&$fld, $fldVal)
    {
        if ($fldVal == Config("NULL_VALUE") || $fldVal == Config("NOT_NULL_VALUE")) {
            return $fldVal;
        }
        $value = $fldVal;
        if ($fld->isBoolean()) {
            if ($fldVal != "") {
                $value = (SameText($fldVal, "1") || SameText($fldVal, "y") || SameText($fldVal, "t")) ? $fld->TrueValue : $fld->FalseValue;
            }
        } elseif ($fld->DataType == DATATYPE_DATE || $fld->DataType == DATATYPE_TIME) {
            if ($fldVal != "") {
                $value = UnFormatDateTime($fldVal, $fld->formatPattern());
            }
        }
        return $value;
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
        $searchFlds[] = &$this->ID_EQUIPO_TORNEO;
        $searchFlds[] = &$this->ID_TORNEO;
        $searchFlds[] = &$this->ID_EQUIPO;
        $searchFlds[] = &$this->PARTIDOS_JUGADOS;
        $searchFlds[] = &$this->PARTIDOS_GANADOS;
        $searchFlds[] = &$this->PARTIDOS_EMPATADOS;
        $searchFlds[] = &$this->PARTIDOS_PERDIDOS;
        $searchFlds[] = &$this->GF;
        $searchFlds[] = &$this->GC;
        $searchFlds[] = &$this->GD;
        $searchFlds[] = &$this->GRUPO;
        $searchFlds[] = &$this->POSICION_EQUIPO_TORENO;
        $searchFlds[] = &$this->crea_dato;
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
        if ($this->ID_EQUIPO_TORNEO->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->ID_TORNEO->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->ID_EQUIPO->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->PARTIDOS_JUGADOS->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->PARTIDOS_GANADOS->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->PARTIDOS_EMPATADOS->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->PARTIDOS_PERDIDOS->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->GF->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->GC->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->GD->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->GRUPO->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->POSICION_EQUIPO_TORENO->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->crea_dato->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->modifica_dato->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->usuario_dato->AdvancedSearch->issetSession()) {
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

        // Clear advanced search parameters
        $this->resetAdvancedSearchParms();
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

    // Clear all advanced search parameters
    protected function resetAdvancedSearchParms()
    {
        $this->ID_EQUIPO_TORNEO->AdvancedSearch->unsetSession();
        $this->ID_TORNEO->AdvancedSearch->unsetSession();
        $this->ID_EQUIPO->AdvancedSearch->unsetSession();
        $this->PARTIDOS_JUGADOS->AdvancedSearch->unsetSession();
        $this->PARTIDOS_GANADOS->AdvancedSearch->unsetSession();
        $this->PARTIDOS_EMPATADOS->AdvancedSearch->unsetSession();
        $this->PARTIDOS_PERDIDOS->AdvancedSearch->unsetSession();
        $this->GF->AdvancedSearch->unsetSession();
        $this->GC->AdvancedSearch->unsetSession();
        $this->GD->AdvancedSearch->unsetSession();
        $this->GRUPO->AdvancedSearch->unsetSession();
        $this->POSICION_EQUIPO_TORENO->AdvancedSearch->unsetSession();
        $this->crea_dato->AdvancedSearch->unsetSession();
        $this->modifica_dato->AdvancedSearch->unsetSession();
        $this->usuario_dato->AdvancedSearch->unsetSession();
    }

    // Restore all search parameters
    protected function restoreSearchParms()
    {
        $this->RestoreSearch = true;

        // Restore basic search values
        $this->BasicSearch->load();

        // Restore advanced search values
        $this->ID_EQUIPO_TORNEO->AdvancedSearch->load();
        $this->ID_TORNEO->AdvancedSearch->load();
        $this->ID_EQUIPO->AdvancedSearch->load();
        $this->PARTIDOS_JUGADOS->AdvancedSearch->load();
        $this->PARTIDOS_GANADOS->AdvancedSearch->load();
        $this->PARTIDOS_EMPATADOS->AdvancedSearch->load();
        $this->PARTIDOS_PERDIDOS->AdvancedSearch->load();
        $this->GF->AdvancedSearch->load();
        $this->GC->AdvancedSearch->load();
        $this->GD->AdvancedSearch->load();
        $this->GRUPO->AdvancedSearch->load();
        $this->POSICION_EQUIPO_TORENO->AdvancedSearch->load();
        $this->crea_dato->AdvancedSearch->load();
        $this->modifica_dato->AdvancedSearch->load();
        $this->usuario_dato->AdvancedSearch->load();
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
            $this->updateSort($this->ID_EQUIPO_TORNEO); // ID_EQUIPO_TORNEO
            $this->updateSort($this->ID_TORNEO); // ID_TORNEO
            $this->updateSort($this->ID_EQUIPO); // ID_EQUIPO
            $this->updateSort($this->PARTIDOS_JUGADOS); // PARTIDOS_JUGADOS
            $this->updateSort($this->PARTIDOS_GANADOS); // PARTIDOS_GANADOS
            $this->updateSort($this->PARTIDOS_EMPATADOS); // PARTIDOS_EMPATADOS
            $this->updateSort($this->PARTIDOS_PERDIDOS); // PARTIDOS_PERDIDOS
            $this->updateSort($this->GF); // GF
            $this->updateSort($this->GC); // GC
            $this->updateSort($this->GD); // GD
            $this->updateSort($this->GRUPO); // GRUPO
            $this->updateSort($this->POSICION_EQUIPO_TORENO); // POSICION_EQUIPO_TORENO
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
                $this->setSessionOrderByList($orderBy);
                $this->ID_EQUIPO_TORNEO->setSort("");
                $this->ID_TORNEO->setSort("");
                $this->ID_EQUIPO->setSort("");
                $this->PARTIDOS_JUGADOS->setSort("");
                $this->PARTIDOS_GANADOS->setSort("");
                $this->PARTIDOS_EMPATADOS->setSort("");
                $this->PARTIDOS_PERDIDOS->setSort("");
                $this->GF->setSort("");
                $this->GC->setSort("");
                $this->GD->setSort("");
                $this->GRUPO->setSort("");
                $this->POSICION_EQUIPO_TORENO->setSort("");
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
                "<button class=\"ew-grid-link ew-inline-update\" title=\"" . HtmlTitle($Language->phrase("UpdateLink")) . "\" data-caption=\"" . HtmlTitle($Language->phrase("UpdateLink")) . "\" form=\"fequipotorneolist\" formaction=\"" . HtmlEncode(GetUrl(UrlAddHash($this->pageName(), "r" . $this->RowCount . "_" . $this->TableVar))) . "\">" . $Language->phrase("UpdateLink") . "</button>&nbsp;" .
                "<a class=\"ew-grid-link ew-inline-cancel\" title=\"" . HtmlTitle($Language->phrase("CancelLink")) . "\" data-caption=\"" . HtmlTitle($Language->phrase("CancelLink")) . "\" href=\"" . $cancelurl . "\">" . $Language->phrase("CancelLink") . "</a>" .
                "<input type=\"hidden\" name=\"action\" id=\"action\" value=\"update\"></div>";
            $opt->Body .= "<input type=\"hidden\" name=\"k" . $this->RowIndex . "_key\" id=\"k" . $this->RowIndex . "_key\" value=\"" . HtmlEncode($this->ID_EQUIPO_TORNEO->CurrentValue) . "\">";
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
                    $link = "<li><button type=\"button\" class=\"dropdown-item ew-action ew-list-action\" data-caption=\"" . HtmlTitle($caption) . "\" data-ew-action=\"submit\" form=\"fequipotorneolist\" data-key=\"" . $this->keyToJson(true) . "\"" . $listaction->toDataAttrs() . ">" . $icon . $listaction->Caption . "</button></li>";
                    if ($link != "") {
                        $links[] = $link;
                        if ($body == "") { // Setup first button
                            $body = "<button type=\"button\" class=\"btn btn-default ew-action ew-list-action\" title=\"" . HtmlTitle($caption) . "\" data-caption=\"" . HtmlTitle($caption) . "\" data-ew-action=\"submit\" form=\"fequipotorneolist\" data-key=\"" . $this->keyToJson(true) . "\"" . $listaction->toDataAttrs() . ">" . $icon . $listaction->Caption . "</button>";
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
        $opt->Body = "<div class=\"form-check\"><input type=\"checkbox\" id=\"key_m_" . $this->RowCount . "\" name=\"key_m[]\" class=\"form-check-input ew-multi-select\" value=\"" . HtmlEncode($this->ID_EQUIPO_TORNEO->CurrentValue) . "\" data-ew-action=\"select-key\"></div>";
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
        $item->Body = "<button type=\"button\" class=\"ew-action ew-multi-delete\" title=\"" . HtmlTitle($Language->phrase("DeleteSelectedLink")) . "\" data-caption=\"" . HtmlTitle($Language->phrase("DeleteSelectedLink")) . "\" form=\"fequipotorneolist\" data-ew-action=\"submit\" data-url=\"" . GetUrl($this->MultiDeleteUrl) . "\"data-data='{\"action\":\"show\"}'>" . $Language->phrase("DeleteSelectedLink") . "</button>";
        $item->Visible = $Security->canDelete();

        // Show column list for column visibility
        if ($this->UseColumnVisibility) {
            $option = $this->OtherOptions["column"];
            $item = &$option->addGroupOption();
            $item->Body = "";
            $item->Visible = $this->UseColumnVisibility;
            $option->add("ID_EQUIPO_TORNEO", $this->createColumnOption("ID_EQUIPO_TORNEO"));
            $option->add("ID_TORNEO", $this->createColumnOption("ID_TORNEO"));
            $option->add("ID_EQUIPO", $this->createColumnOption("ID_EQUIPO"));
            $option->add("PARTIDOS_JUGADOS", $this->createColumnOption("PARTIDOS_JUGADOS"));
            $option->add("PARTIDOS_GANADOS", $this->createColumnOption("PARTIDOS_GANADOS"));
            $option->add("PARTIDOS_EMPATADOS", $this->createColumnOption("PARTIDOS_EMPATADOS"));
            $option->add("PARTIDOS_PERDIDOS", $this->createColumnOption("PARTIDOS_PERDIDOS"));
            $option->add("GF", $this->createColumnOption("GF"));
            $option->add("GC", $this->createColumnOption("GC"));
            $option->add("GD", $this->createColumnOption("GD"));
            $option->add("GRUPO", $this->createColumnOption("GRUPO"));
            $option->add("POSICION_EQUIPO_TORENO", $this->createColumnOption("POSICION_EQUIPO_TORENO"));
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
        $item->Body = "<a class=\"ew-save-filter\" data-form=\"fequipotorneosrch\" data-ew-action=\"none\">" . $Language->phrase("SaveCurrentFilter") . "</a>";
        $item->Visible = true;
        $item = &$this->FilterOptions->add("deletefilter");
        $item->Body = "<a class=\"ew-delete-filter\" data-form=\"fequipotorneosrch\" data-ew-action=\"none\">" . $Language->phrase("DeleteFilter") . "</a>";
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
                $item->Body = '<button type="button" class="btn btn-default ew-action ew-list-action" title="' . HtmlEncode($caption) . '" data-caption="' . HtmlEncode($caption) . '" data-ew-action="submit" form="fequipotorneolist"' . $listaction->toDataAttrs() . '>' . $icon . '</button>';
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

    // Load basic search values
    protected function loadBasicSearchValues()
    {
        $this->BasicSearch->setKeyword(Get(Config("TABLE_BASIC_SEARCH"), ""), false);
        if ($this->BasicSearch->Keyword != "" && $this->Command == "") {
            $this->Command = "search";
        }
        $this->BasicSearch->setType(Get(Config("TABLE_BASIC_SEARCH_TYPE"), ""), false);
    }

    // Load search values for validation
    protected function loadSearchValues()
    {
        // Load search values
        $hasValue = false;

        // ID_EQUIPO_TORNEO
        if ($this->ID_EQUIPO_TORNEO->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->ID_EQUIPO_TORNEO->AdvancedSearch->SearchValue != "" || $this->ID_EQUIPO_TORNEO->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // ID_TORNEO
        if ($this->ID_TORNEO->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->ID_TORNEO->AdvancedSearch->SearchValue != "" || $this->ID_TORNEO->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // ID_EQUIPO
        if ($this->ID_EQUIPO->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->ID_EQUIPO->AdvancedSearch->SearchValue != "" || $this->ID_EQUIPO->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // PARTIDOS_JUGADOS
        if ($this->PARTIDOS_JUGADOS->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->PARTIDOS_JUGADOS->AdvancedSearch->SearchValue != "" || $this->PARTIDOS_JUGADOS->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // PARTIDOS_GANADOS
        if ($this->PARTIDOS_GANADOS->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->PARTIDOS_GANADOS->AdvancedSearch->SearchValue != "" || $this->PARTIDOS_GANADOS->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // PARTIDOS_EMPATADOS
        if ($this->PARTIDOS_EMPATADOS->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->PARTIDOS_EMPATADOS->AdvancedSearch->SearchValue != "" || $this->PARTIDOS_EMPATADOS->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // PARTIDOS_PERDIDOS
        if ($this->PARTIDOS_PERDIDOS->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->PARTIDOS_PERDIDOS->AdvancedSearch->SearchValue != "" || $this->PARTIDOS_PERDIDOS->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // GF
        if ($this->GF->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->GF->AdvancedSearch->SearchValue != "" || $this->GF->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // GC
        if ($this->GC->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->GC->AdvancedSearch->SearchValue != "" || $this->GC->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // GD
        if ($this->GD->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->GD->AdvancedSearch->SearchValue != "" || $this->GD->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // GRUPO
        if ($this->GRUPO->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->GRUPO->AdvancedSearch->SearchValue != "" || $this->GRUPO->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // POSICION_EQUIPO_TORENO
        if ($this->POSICION_EQUIPO_TORENO->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->POSICION_EQUIPO_TORENO->AdvancedSearch->SearchValue != "" || $this->POSICION_EQUIPO_TORENO->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // crea_dato
        if ($this->crea_dato->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->crea_dato->AdvancedSearch->SearchValue != "" || $this->crea_dato->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // modifica_dato
        if ($this->modifica_dato->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->modifica_dato->AdvancedSearch->SearchValue != "" || $this->modifica_dato->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // usuario_dato
        if ($this->usuario_dato->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->usuario_dato->AdvancedSearch->SearchValue != "" || $this->usuario_dato->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }
        return $hasValue;
    }

    // Load form values
    protected function loadFormValues()
    {
        // Load from form
        global $CurrentForm;
        $validate = !Config("SERVER_VALIDATE");

        // Check field name 'ID_EQUIPO_TORNEO' first before field var 'x_ID_EQUIPO_TORNEO'
        $val = $CurrentForm->hasValue("ID_EQUIPO_TORNEO") ? $CurrentForm->getValue("ID_EQUIPO_TORNEO") : $CurrentForm->getValue("x_ID_EQUIPO_TORNEO");
        if (!$this->ID_EQUIPO_TORNEO->IsDetailKey && !$this->isGridAdd() && !$this->isAdd()) {
            $this->ID_EQUIPO_TORNEO->setFormValue($val);
        }

        // Check field name 'ID_TORNEO' first before field var 'x_ID_TORNEO'
        $val = $CurrentForm->hasValue("ID_TORNEO") ? $CurrentForm->getValue("ID_TORNEO") : $CurrentForm->getValue("x_ID_TORNEO");
        if (!$this->ID_TORNEO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->ID_TORNEO->Visible = false; // Disable update for API request
            } else {
                $this->ID_TORNEO->setFormValue($val);
            }
        }

        // Check field name 'ID_EQUIPO' first before field var 'x_ID_EQUIPO'
        $val = $CurrentForm->hasValue("ID_EQUIPO") ? $CurrentForm->getValue("ID_EQUIPO") : $CurrentForm->getValue("x_ID_EQUIPO");
        if (!$this->ID_EQUIPO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->ID_EQUIPO->Visible = false; // Disable update for API request
            } else {
                $this->ID_EQUIPO->setFormValue($val);
            }
        }

        // Check field name 'PARTIDOS_JUGADOS' first before field var 'x_PARTIDOS_JUGADOS'
        $val = $CurrentForm->hasValue("PARTIDOS_JUGADOS") ? $CurrentForm->getValue("PARTIDOS_JUGADOS") : $CurrentForm->getValue("x_PARTIDOS_JUGADOS");
        if (!$this->PARTIDOS_JUGADOS->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->PARTIDOS_JUGADOS->Visible = false; // Disable update for API request
            } else {
                $this->PARTIDOS_JUGADOS->setFormValue($val, true, $validate);
            }
        }

        // Check field name 'PARTIDOS_GANADOS' first before field var 'x_PARTIDOS_GANADOS'
        $val = $CurrentForm->hasValue("PARTIDOS_GANADOS") ? $CurrentForm->getValue("PARTIDOS_GANADOS") : $CurrentForm->getValue("x_PARTIDOS_GANADOS");
        if (!$this->PARTIDOS_GANADOS->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->PARTIDOS_GANADOS->Visible = false; // Disable update for API request
            } else {
                $this->PARTIDOS_GANADOS->setFormValue($val, true, $validate);
            }
        }

        // Check field name 'PARTIDOS_EMPATADOS' first before field var 'x_PARTIDOS_EMPATADOS'
        $val = $CurrentForm->hasValue("PARTIDOS_EMPATADOS") ? $CurrentForm->getValue("PARTIDOS_EMPATADOS") : $CurrentForm->getValue("x_PARTIDOS_EMPATADOS");
        if (!$this->PARTIDOS_EMPATADOS->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->PARTIDOS_EMPATADOS->Visible = false; // Disable update for API request
            } else {
                $this->PARTIDOS_EMPATADOS->setFormValue($val, true, $validate);
            }
        }

        // Check field name 'PARTIDOS_PERDIDOS' first before field var 'x_PARTIDOS_PERDIDOS'
        $val = $CurrentForm->hasValue("PARTIDOS_PERDIDOS") ? $CurrentForm->getValue("PARTIDOS_PERDIDOS") : $CurrentForm->getValue("x_PARTIDOS_PERDIDOS");
        if (!$this->PARTIDOS_PERDIDOS->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->PARTIDOS_PERDIDOS->Visible = false; // Disable update for API request
            } else {
                $this->PARTIDOS_PERDIDOS->setFormValue($val, true, $validate);
            }
        }

        // Check field name 'GF' first before field var 'x_GF'
        $val = $CurrentForm->hasValue("GF") ? $CurrentForm->getValue("GF") : $CurrentForm->getValue("x_GF");
        if (!$this->GF->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->GF->Visible = false; // Disable update for API request
            } else {
                $this->GF->setFormValue($val, true, $validate);
            }
        }

        // Check field name 'GC' first before field var 'x_GC'
        $val = $CurrentForm->hasValue("GC") ? $CurrentForm->getValue("GC") : $CurrentForm->getValue("x_GC");
        if (!$this->GC->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->GC->Visible = false; // Disable update for API request
            } else {
                $this->GC->setFormValue($val, true, $validate);
            }
        }

        // Check field name 'GD' first before field var 'x_GD'
        $val = $CurrentForm->hasValue("GD") ? $CurrentForm->getValue("GD") : $CurrentForm->getValue("x_GD");
        if (!$this->GD->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->GD->Visible = false; // Disable update for API request
            } else {
                $this->GD->setFormValue($val, true, $validate);
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

        // Check field name 'POSICION_EQUIPO_TORENO' first before field var 'x_POSICION_EQUIPO_TORENO'
        $val = $CurrentForm->hasValue("POSICION_EQUIPO_TORENO") ? $CurrentForm->getValue("POSICION_EQUIPO_TORENO") : $CurrentForm->getValue("x_POSICION_EQUIPO_TORENO");
        if (!$this->POSICION_EQUIPO_TORENO->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->POSICION_EQUIPO_TORENO->Visible = false; // Disable update for API request
            } else {
                $this->POSICION_EQUIPO_TORENO->setFormValue($val);
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
    }

    // Restore form values
    public function restoreFormValues()
    {
        global $CurrentForm;
        if (!$this->isGridAdd() && !$this->isAdd()) {
            $this->ID_EQUIPO_TORNEO->CurrentValue = $this->ID_EQUIPO_TORNEO->FormValue;
        }
        $this->ID_TORNEO->CurrentValue = $this->ID_TORNEO->FormValue;
        $this->ID_EQUIPO->CurrentValue = $this->ID_EQUIPO->FormValue;
        $this->PARTIDOS_JUGADOS->CurrentValue = $this->PARTIDOS_JUGADOS->FormValue;
        $this->PARTIDOS_GANADOS->CurrentValue = $this->PARTIDOS_GANADOS->FormValue;
        $this->PARTIDOS_EMPATADOS->CurrentValue = $this->PARTIDOS_EMPATADOS->FormValue;
        $this->PARTIDOS_PERDIDOS->CurrentValue = $this->PARTIDOS_PERDIDOS->FormValue;
        $this->GF->CurrentValue = $this->GF->FormValue;
        $this->GC->CurrentValue = $this->GC->FormValue;
        $this->GD->CurrentValue = $this->GD->FormValue;
        $this->GRUPO->CurrentValue = $this->GRUPO->FormValue;
        $this->POSICION_EQUIPO_TORENO->CurrentValue = $this->POSICION_EQUIPO_TORENO->FormValue;
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

        // ID_EQUIPO_TORNEO

        // ID_TORNEO

        // ID_EQUIPO

        // PARTIDOS_JUGADOS

        // PARTIDOS_GANADOS

        // PARTIDOS_EMPATADOS

        // PARTIDOS_PERDIDOS

        // GF

        // GC

        // GD

        // GRUPO

        // POSICION_EQUIPO_TORENO

        // crea_dato

        // modifica_dato

        // usuario_dato

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

            // ID_EQUIPO_TORNEO
            $this->ID_EQUIPO_TORNEO->LinkCustomAttributes = "";
            $this->ID_EQUIPO_TORNEO->HrefValue = "";
            $this->ID_EQUIPO_TORNEO->TooltipValue = "";

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
            // ID_EQUIPO_TORNEO

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
                $filterWrk = "";
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
            $this->crea_dato->EditValue = HtmlEncode(FormatDateTime($this->crea_dato->CurrentValue, $this->crea_dato->formatPattern()));
            $this->crea_dato->PlaceHolder = RemoveHtml($this->crea_dato->caption());

            // modifica_dato
            $this->modifica_dato->setupEditAttributes();
            $this->modifica_dato->EditCustomAttributes = "";
            $this->modifica_dato->EditValue = HtmlEncode(FormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern()));
            $this->modifica_dato->PlaceHolder = RemoveHtml($this->modifica_dato->caption());

            // usuario_dato

            // Add refer script

            // ID_EQUIPO_TORNEO
            $this->ID_EQUIPO_TORNEO->LinkCustomAttributes = "";
            $this->ID_EQUIPO_TORNEO->HrefValue = "";

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
        } elseif ($this->RowType == ROWTYPE_EDIT) {
            // ID_EQUIPO_TORNEO
            $this->ID_EQUIPO_TORNEO->setupEditAttributes();
            $this->ID_EQUIPO_TORNEO->EditCustomAttributes = "";
            $this->ID_EQUIPO_TORNEO->EditValue = $this->ID_EQUIPO_TORNEO->CurrentValue;
            $this->ID_EQUIPO_TORNEO->ViewCustomAttributes = "";

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

            // Edit refer script

            // ID_EQUIPO_TORNEO
            $this->ID_EQUIPO_TORNEO->LinkCustomAttributes = "";
            $this->ID_EQUIPO_TORNEO->HrefValue = "";

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
            $this->crea_dato->TooltipValue = "";

            // modifica_dato
            $this->modifica_dato->LinkCustomAttributes = "";
            $this->modifica_dato->HrefValue = "";
            $this->modifica_dato->TooltipValue = "";

            // usuario_dato
            $this->usuario_dato->LinkCustomAttributes = "";
            $this->usuario_dato->HrefValue = "";
            $this->usuario_dato->TooltipValue = "";
        } elseif ($this->RowType == ROWTYPE_SEARCH) {
            // ID_EQUIPO_TORNEO
            $this->ID_EQUIPO_TORNEO->setupEditAttributes();
            $this->ID_EQUIPO_TORNEO->EditCustomAttributes = "";
            $this->ID_EQUIPO_TORNEO->EditValue = HtmlEncode($this->ID_EQUIPO_TORNEO->AdvancedSearch->SearchValue);
            $this->ID_EQUIPO_TORNEO->PlaceHolder = RemoveHtml($this->ID_EQUIPO_TORNEO->caption());

            // ID_TORNEO
            if ($this->ID_TORNEO->UseFilter && !EmptyValue($this->ID_TORNEO->AdvancedSearch->SearchValue)) {
                if (is_array($this->ID_TORNEO->AdvancedSearch->SearchValue)) {
                    $this->ID_TORNEO->AdvancedSearch->SearchValue = implode(Config("MULTIPLE_OPTION_SEPARATOR"), $this->ID_TORNEO->AdvancedSearch->SearchValue);
                }
                $this->ID_TORNEO->EditValue = explode(Config("MULTIPLE_OPTION_SEPARATOR"), $this->ID_TORNEO->AdvancedSearch->SearchValue);
            }

            // ID_EQUIPO
            $this->ID_EQUIPO->setupEditAttributes();
            $this->ID_EQUIPO->EditCustomAttributes = "";
            $this->ID_EQUIPO->EditValue = HtmlEncode($this->ID_EQUIPO->AdvancedSearch->SearchValue);
            $this->ID_EQUIPO->PlaceHolder = RemoveHtml($this->ID_EQUIPO->caption());

            // PARTIDOS_JUGADOS
            $this->PARTIDOS_JUGADOS->setupEditAttributes();
            $this->PARTIDOS_JUGADOS->EditCustomAttributes = "";
            $this->PARTIDOS_JUGADOS->EditValue = HtmlEncode($this->PARTIDOS_JUGADOS->AdvancedSearch->SearchValue);
            $this->PARTIDOS_JUGADOS->PlaceHolder = RemoveHtml($this->PARTIDOS_JUGADOS->caption());

            // PARTIDOS_GANADOS
            $this->PARTIDOS_GANADOS->setupEditAttributes();
            $this->PARTIDOS_GANADOS->EditCustomAttributes = "";
            $this->PARTIDOS_GANADOS->EditValue = HtmlEncode($this->PARTIDOS_GANADOS->AdvancedSearch->SearchValue);
            $this->PARTIDOS_GANADOS->PlaceHolder = RemoveHtml($this->PARTIDOS_GANADOS->caption());

            // PARTIDOS_EMPATADOS
            $this->PARTIDOS_EMPATADOS->setupEditAttributes();
            $this->PARTIDOS_EMPATADOS->EditCustomAttributes = "";
            $this->PARTIDOS_EMPATADOS->EditValue = HtmlEncode($this->PARTIDOS_EMPATADOS->AdvancedSearch->SearchValue);
            $this->PARTIDOS_EMPATADOS->PlaceHolder = RemoveHtml($this->PARTIDOS_EMPATADOS->caption());

            // PARTIDOS_PERDIDOS
            $this->PARTIDOS_PERDIDOS->setupEditAttributes();
            $this->PARTIDOS_PERDIDOS->EditCustomAttributes = "";
            $this->PARTIDOS_PERDIDOS->EditValue = HtmlEncode($this->PARTIDOS_PERDIDOS->AdvancedSearch->SearchValue);
            $this->PARTIDOS_PERDIDOS->PlaceHolder = RemoveHtml($this->PARTIDOS_PERDIDOS->caption());

            // GF
            $this->GF->setupEditAttributes();
            $this->GF->EditCustomAttributes = "";
            $this->GF->EditValue = HtmlEncode($this->GF->AdvancedSearch->SearchValue);
            $this->GF->PlaceHolder = RemoveHtml($this->GF->caption());

            // GC
            $this->GC->setupEditAttributes();
            $this->GC->EditCustomAttributes = "";
            $this->GC->EditValue = HtmlEncode($this->GC->AdvancedSearch->SearchValue);
            $this->GC->PlaceHolder = RemoveHtml($this->GC->caption());

            // GD
            $this->GD->setupEditAttributes();
            $this->GD->EditCustomAttributes = "";
            $this->GD->EditValue = HtmlEncode($this->GD->AdvancedSearch->SearchValue);
            $this->GD->PlaceHolder = RemoveHtml($this->GD->caption());

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
            $this->crea_dato->EditValue = HtmlEncode(FormatDateTime(UnFormatDateTime($this->crea_dato->AdvancedSearch->SearchValue, $this->crea_dato->formatPattern()), $this->crea_dato->formatPattern()));
            $this->crea_dato->PlaceHolder = RemoveHtml($this->crea_dato->caption());

            // modifica_dato
            $this->modifica_dato->setupEditAttributes();
            $this->modifica_dato->EditCustomAttributes = "";
            $this->modifica_dato->EditValue = HtmlEncode(FormatDateTime(UnFormatDateTime($this->modifica_dato->AdvancedSearch->SearchValue, $this->modifica_dato->formatPattern()), $this->modifica_dato->formatPattern()));
            $this->modifica_dato->PlaceHolder = RemoveHtml($this->modifica_dato->caption());

            // usuario_dato
            $this->usuario_dato->setupEditAttributes();
            $this->usuario_dato->EditCustomAttributes = "";
            if (!$this->usuario_dato->Raw) {
                $this->usuario_dato->AdvancedSearch->SearchValue = HtmlDecode($this->usuario_dato->AdvancedSearch->SearchValue);
            }
            $this->usuario_dato->EditValue = HtmlEncode($this->usuario_dato->AdvancedSearch->SearchValue);
            $this->usuario_dato->PlaceHolder = RemoveHtml($this->usuario_dato->caption());
        }
        if ($this->RowType == ROWTYPE_ADD || $this->RowType == ROWTYPE_EDIT || $this->RowType == ROWTYPE_SEARCH) { // Add/Edit/Search row
            $this->setupFieldTitles();
        }

        // Call Row Rendered event
        if ($this->RowType != ROWTYPE_AGGREGATEINIT) {
            $this->rowRendered();
        }
    }

    // Validate search
    protected function validateSearch()
    {
        // Check if validation required
        if (!Config("SERVER_VALIDATE")) {
            return true;
        }

        // Return validate result
        $validateSearch = !$this->hasInvalidFields();

        // Call Form_CustomValidate event
        $formCustomError = "";
        $validateSearch = $validateSearch && $this->formCustomValidate($formCustomError);
        if ($formCustomError != "") {
            $this->setFailureMessage($formCustomError);
        }
        return $validateSearch;
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
        if ($this->ID_EQUIPO_TORNEO->Required) {
            if (!$this->ID_EQUIPO_TORNEO->IsDetailKey && EmptyValue($this->ID_EQUIPO_TORNEO->FormValue)) {
                $this->ID_EQUIPO_TORNEO->addErrorMessage(str_replace("%s", $this->ID_EQUIPO_TORNEO->caption(), $this->ID_EQUIPO_TORNEO->RequiredErrorMessage));
            }
        }
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

        // ID_EQUIPO
        $this->ID_EQUIPO->setDbValueDef($rsnew, $this->ID_EQUIPO->CurrentValue, null, $this->ID_EQUIPO->ReadOnly);

        // PARTIDOS_JUGADOS
        $this->PARTIDOS_JUGADOS->setDbValueDef($rsnew, $this->PARTIDOS_JUGADOS->CurrentValue, null, $this->PARTIDOS_JUGADOS->ReadOnly);

        // PARTIDOS_GANADOS
        $this->PARTIDOS_GANADOS->setDbValueDef($rsnew, $this->PARTIDOS_GANADOS->CurrentValue, null, $this->PARTIDOS_GANADOS->ReadOnly);

        // PARTIDOS_EMPATADOS
        $this->PARTIDOS_EMPATADOS->setDbValueDef($rsnew, $this->PARTIDOS_EMPATADOS->CurrentValue, null, $this->PARTIDOS_EMPATADOS->ReadOnly);

        // PARTIDOS_PERDIDOS
        $this->PARTIDOS_PERDIDOS->setDbValueDef($rsnew, $this->PARTIDOS_PERDIDOS->CurrentValue, null, $this->PARTIDOS_PERDIDOS->ReadOnly);

        // GF
        $this->GF->setDbValueDef($rsnew, $this->GF->CurrentValue, null, $this->GF->ReadOnly);

        // GC
        $this->GC->setDbValueDef($rsnew, $this->GC->CurrentValue, null, $this->GC->ReadOnly);

        // GD
        $this->GD->setDbValueDef($rsnew, $this->GD->CurrentValue, null, $this->GD->ReadOnly);

        // GRUPO
        $this->GRUPO->setDbValueDef($rsnew, $this->GRUPO->CurrentValue, null, $this->GRUPO->ReadOnly);

        // POSICION_EQUIPO_TORENO
        $this->POSICION_EQUIPO_TORENO->setDbValueDef($rsnew, $this->POSICION_EQUIPO_TORENO->CurrentValue, null, $this->POSICION_EQUIPO_TORENO->ReadOnly);

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
        $hash .= GetFieldHash($row['ID_TORNEO']); // ID_TORNEO
        $hash .= GetFieldHash($row['ID_EQUIPO']); // ID_EQUIPO
        $hash .= GetFieldHash($row['PARTIDOS_JUGADOS']); // PARTIDOS_JUGADOS
        $hash .= GetFieldHash($row['PARTIDOS_GANADOS']); // PARTIDOS_GANADOS
        $hash .= GetFieldHash($row['PARTIDOS_EMPATADOS']); // PARTIDOS_EMPATADOS
        $hash .= GetFieldHash($row['PARTIDOS_PERDIDOS']); // PARTIDOS_PERDIDOS
        $hash .= GetFieldHash($row['GF']); // GF
        $hash .= GetFieldHash($row['GC']); // GC
        $hash .= GetFieldHash($row['GD']); // GD
        $hash .= GetFieldHash($row['GRUPO']); // GRUPO
        $hash .= GetFieldHash($row['POSICION_EQUIPO_TORENO']); // POSICION_EQUIPO_TORENO
        return md5($hash);
    }

    // Add record
    protected function addRow($rsold = null)
    {
        global $Language, $Security;

        // Set new row
        $rsnew = [];

        // ID_TORNEO
        $this->ID_TORNEO->setDbValueDef($rsnew, $this->ID_TORNEO->CurrentValue, null, false);

        // ID_EQUIPO
        $this->ID_EQUIPO->setDbValueDef($rsnew, $this->ID_EQUIPO->CurrentValue, null, false);

        // PARTIDOS_JUGADOS
        $this->PARTIDOS_JUGADOS->setDbValueDef($rsnew, $this->PARTIDOS_JUGADOS->CurrentValue, null, false);

        // PARTIDOS_GANADOS
        $this->PARTIDOS_GANADOS->setDbValueDef($rsnew, $this->PARTIDOS_GANADOS->CurrentValue, null, false);

        // PARTIDOS_EMPATADOS
        $this->PARTIDOS_EMPATADOS->setDbValueDef($rsnew, $this->PARTIDOS_EMPATADOS->CurrentValue, null, false);

        // PARTIDOS_PERDIDOS
        $this->PARTIDOS_PERDIDOS->setDbValueDef($rsnew, $this->PARTIDOS_PERDIDOS->CurrentValue, null, false);

        // GF
        $this->GF->setDbValueDef($rsnew, $this->GF->CurrentValue, null, false);

        // GC
        $this->GC->setDbValueDef($rsnew, $this->GC->CurrentValue, null, false);

        // GD
        $this->GD->setDbValueDef($rsnew, $this->GD->CurrentValue, null, false);

        // GRUPO
        $this->GRUPO->setDbValueDef($rsnew, $this->GRUPO->CurrentValue, null, false);

        // POSICION_EQUIPO_TORENO
        $this->POSICION_EQUIPO_TORENO->setDbValueDef($rsnew, $this->POSICION_EQUIPO_TORENO->CurrentValue, null, false);

        // crea_dato
        $this->crea_dato->setDbValueDef($rsnew, UnFormatDateTime($this->crea_dato->CurrentValue, $this->crea_dato->formatPattern()), null, false);

        // modifica_dato
        $this->modifica_dato->setDbValueDef($rsnew, UnFormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern()), null, false);

        // usuario_dato
        $this->usuario_dato->CurrentValue = CurrentUserName();
        $this->usuario_dato->setDbValueDef($rsnew, $this->usuario_dato->CurrentValue, null);

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

    // Load advanced search
    public function loadAdvancedSearch()
    {
        $this->ID_EQUIPO_TORNEO->AdvancedSearch->load();
        $this->ID_TORNEO->AdvancedSearch->load();
        $this->ID_EQUIPO->AdvancedSearch->load();
        $this->PARTIDOS_JUGADOS->AdvancedSearch->load();
        $this->PARTIDOS_GANADOS->AdvancedSearch->load();
        $this->PARTIDOS_EMPATADOS->AdvancedSearch->load();
        $this->PARTIDOS_PERDIDOS->AdvancedSearch->load();
        $this->GF->AdvancedSearch->load();
        $this->GC->AdvancedSearch->load();
        $this->GD->AdvancedSearch->load();
        $this->GRUPO->AdvancedSearch->load();
        $this->POSICION_EQUIPO_TORENO->AdvancedSearch->load();
        $this->crea_dato->AdvancedSearch->load();
        $this->modifica_dato->AdvancedSearch->load();
        $this->usuario_dato->AdvancedSearch->load();
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
        $item->Body = "<a class=\"btn btn-default ew-search-toggle" . $searchToggleClass . "\" role=\"button\" title=\"" . $Language->phrase("SearchPanel") . "\" data-caption=\"" . $Language->phrase("SearchPanel") . "\" data-ew-action=\"search-toggle\" data-form=\"fequipotorneosrch\" aria-pressed=\"" . ($searchToggleClass == " active" ? "true" : "false") . "\">" . $Language->phrase("SearchLink") . "</a>";
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
