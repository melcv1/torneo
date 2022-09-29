<?php

namespace PHPMaker2022\project11;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Page class
 */
class PartidosList extends Partidos
{
    use MessagesTrait;

    // Page ID
    public $PageID = "list";

    // Project ID
    public $ProjectID = PROJECT_ID;

    // Table name
    public $TableName = 'partidos';

    // Page object name
    public $PageObjName = "PartidosList";

    // View file path
    public $View = null;

    // Title
    public $Title = null; // Title for <title> tag

    // Rendering View
    public $RenderingView = false;

    // Grid form hidden field names
    public $FormName = "fpartidoslist";
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

        // Table object (partidos)
        if (!isset($GLOBALS["partidos"]) || get_class($GLOBALS["partidos"]) == PROJECT_NAMESPACE . "partidos") {
            $GLOBALS["partidos"] = &$this;
        }

        // Page URL
        $pageUrl = $this->pageUrl(false);

        // Initialize URLs
        $this->AddUrl = "partidosadd";
        $this->InlineAddUrl = $pageUrl . "action=add";
        $this->GridAddUrl = $pageUrl . "action=gridadd";
        $this->GridEditUrl = $pageUrl . "action=gridedit";
        $this->MultiDeleteUrl = "partidosdelete";
        $this->MultiUpdateUrl = "partidosupdate";

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

        // Setup other options
        $this->setupOtherOptions();

        // Set up custom action (compatible with old version)
        foreach ($this->CustomActions as $name => $action) {
            $this->ListActions->add($name, $action);
        }

        // Set up lookup cache
        $this->setupLookupOptions($this->ID_TORNEO);
        $this->setupLookupOptions($this->equipo_local);
        $this->setupLookupOptions($this->equipo_visitante);
        $this->setupLookupOptions($this->ESTADIO);
        $this->setupLookupOptions($this->PAIS_PARTIDO);
        $this->setupLookupOptions($this->ESTADO_PARTIDO);
        $this->setupLookupOptions($this->automatico);

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
        if (($keyValue = Get("ID_PARTIDO") ?? Route("ID_PARTIDO")) !== null) {
            $this->ID_PARTIDO->setQueryStringValue($keyValue);
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
        if (!SameString($this->ID_PARTIDO->OldValue, $this->ID_PARTIDO->CurrentValue)) {
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
            $savedFilterList = $UserProfile->getSearchFilters(CurrentUserName(), "fpartidossrch");
        }
        $filterList = Concat($filterList, $this->ID_TORNEO->AdvancedSearch->toJson(), ","); // Field ID_TORNEO
        $filterList = Concat($filterList, $this->equipo_local->AdvancedSearch->toJson(), ","); // Field equipo_local
        $filterList = Concat($filterList, $this->equipo_visitante->AdvancedSearch->toJson(), ","); // Field equipo_visitante
        $filterList = Concat($filterList, $this->ID_PARTIDO->AdvancedSearch->toJson(), ","); // Field ID_PARTIDO
        $filterList = Concat($filterList, $this->FECHA_PARTIDO->AdvancedSearch->toJson(), ","); // Field FECHA_PARTIDO
        $filterList = Concat($filterList, $this->HORA_PARTIDO->AdvancedSearch->toJson(), ","); // Field HORA_PARTIDO
        $filterList = Concat($filterList, $this->ESTADIO->AdvancedSearch->toJson(), ","); // Field ESTADIO
        $filterList = Concat($filterList, $this->CIUDAD_PARTIDO->AdvancedSearch->toJson(), ","); // Field CIUDAD_PARTIDO
        $filterList = Concat($filterList, $this->PAIS_PARTIDO->AdvancedSearch->toJson(), ","); // Field PAIS_PARTIDO
        $filterList = Concat($filterList, $this->GOLES_LOCAL->AdvancedSearch->toJson(), ","); // Field GOLES_LOCAL
        $filterList = Concat($filterList, $this->GOLES_VISITANTE->AdvancedSearch->toJson(), ","); // Field GOLES_VISITANTE
        $filterList = Concat($filterList, $this->GOLES_EXTRA_EQUIPO1->AdvancedSearch->toJson(), ","); // Field GOLES_EXTRA_EQUIPO1
        $filterList = Concat($filterList, $this->GOLES_EXTRA_EQUIPO2->AdvancedSearch->toJson(), ","); // Field GOLES_EXTRA_EQUIPO2
        $filterList = Concat($filterList, $this->NOTA_PARTIDO->AdvancedSearch->toJson(), ","); // Field NOTA_PARTIDO
        $filterList = Concat($filterList, $this->RESUMEN_PARTIDO->AdvancedSearch->toJson(), ","); // Field RESUMEN_PARTIDO
        $filterList = Concat($filterList, $this->ESTADO_PARTIDO->AdvancedSearch->toJson(), ","); // Field ESTADO_PARTIDO
        $filterList = Concat($filterList, $this->crea_dato->AdvancedSearch->toJson(), ","); // Field crea_dato
        $filterList = Concat($filterList, $this->modifica_dato->AdvancedSearch->toJson(), ","); // Field modifica_dato
        $filterList = Concat($filterList, $this->usuario_dato->AdvancedSearch->toJson(), ","); // Field usuario_dato
        $filterList = Concat($filterList, $this->automatico->AdvancedSearch->toJson(), ","); // Field automatico
        $filterList = Concat($filterList, $this->actualizado->AdvancedSearch->toJson(), ","); // Field actualizado
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
            $UserProfile->setSearchFilters(CurrentUserName(), "fpartidossrch", $filters);
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

        // Field equipo_local
        $this->equipo_local->AdvancedSearch->SearchValue = @$filter["x_equipo_local"];
        $this->equipo_local->AdvancedSearch->SearchOperator = @$filter["z_equipo_local"];
        $this->equipo_local->AdvancedSearch->SearchCondition = @$filter["v_equipo_local"];
        $this->equipo_local->AdvancedSearch->SearchValue2 = @$filter["y_equipo_local"];
        $this->equipo_local->AdvancedSearch->SearchOperator2 = @$filter["w_equipo_local"];
        $this->equipo_local->AdvancedSearch->save();

        // Field equipo_visitante
        $this->equipo_visitante->AdvancedSearch->SearchValue = @$filter["x_equipo_visitante"];
        $this->equipo_visitante->AdvancedSearch->SearchOperator = @$filter["z_equipo_visitante"];
        $this->equipo_visitante->AdvancedSearch->SearchCondition = @$filter["v_equipo_visitante"];
        $this->equipo_visitante->AdvancedSearch->SearchValue2 = @$filter["y_equipo_visitante"];
        $this->equipo_visitante->AdvancedSearch->SearchOperator2 = @$filter["w_equipo_visitante"];
        $this->equipo_visitante->AdvancedSearch->save();

        // Field ID_PARTIDO
        $this->ID_PARTIDO->AdvancedSearch->SearchValue = @$filter["x_ID_PARTIDO"];
        $this->ID_PARTIDO->AdvancedSearch->SearchOperator = @$filter["z_ID_PARTIDO"];
        $this->ID_PARTIDO->AdvancedSearch->SearchCondition = @$filter["v_ID_PARTIDO"];
        $this->ID_PARTIDO->AdvancedSearch->SearchValue2 = @$filter["y_ID_PARTIDO"];
        $this->ID_PARTIDO->AdvancedSearch->SearchOperator2 = @$filter["w_ID_PARTIDO"];
        $this->ID_PARTIDO->AdvancedSearch->save();

        // Field FECHA_PARTIDO
        $this->FECHA_PARTIDO->AdvancedSearch->SearchValue = @$filter["x_FECHA_PARTIDO"];
        $this->FECHA_PARTIDO->AdvancedSearch->SearchOperator = @$filter["z_FECHA_PARTIDO"];
        $this->FECHA_PARTIDO->AdvancedSearch->SearchCondition = @$filter["v_FECHA_PARTIDO"];
        $this->FECHA_PARTIDO->AdvancedSearch->SearchValue2 = @$filter["y_FECHA_PARTIDO"];
        $this->FECHA_PARTIDO->AdvancedSearch->SearchOperator2 = @$filter["w_FECHA_PARTIDO"];
        $this->FECHA_PARTIDO->AdvancedSearch->save();

        // Field HORA_PARTIDO
        $this->HORA_PARTIDO->AdvancedSearch->SearchValue = @$filter["x_HORA_PARTIDO"];
        $this->HORA_PARTIDO->AdvancedSearch->SearchOperator = @$filter["z_HORA_PARTIDO"];
        $this->HORA_PARTIDO->AdvancedSearch->SearchCondition = @$filter["v_HORA_PARTIDO"];
        $this->HORA_PARTIDO->AdvancedSearch->SearchValue2 = @$filter["y_HORA_PARTIDO"];
        $this->HORA_PARTIDO->AdvancedSearch->SearchOperator2 = @$filter["w_HORA_PARTIDO"];
        $this->HORA_PARTIDO->AdvancedSearch->save();

        // Field ESTADIO
        $this->ESTADIO->AdvancedSearch->SearchValue = @$filter["x_ESTADIO"];
        $this->ESTADIO->AdvancedSearch->SearchOperator = @$filter["z_ESTADIO"];
        $this->ESTADIO->AdvancedSearch->SearchCondition = @$filter["v_ESTADIO"];
        $this->ESTADIO->AdvancedSearch->SearchValue2 = @$filter["y_ESTADIO"];
        $this->ESTADIO->AdvancedSearch->SearchOperator2 = @$filter["w_ESTADIO"];
        $this->ESTADIO->AdvancedSearch->save();

        // Field CIUDAD_PARTIDO
        $this->CIUDAD_PARTIDO->AdvancedSearch->SearchValue = @$filter["x_CIUDAD_PARTIDO"];
        $this->CIUDAD_PARTIDO->AdvancedSearch->SearchOperator = @$filter["z_CIUDAD_PARTIDO"];
        $this->CIUDAD_PARTIDO->AdvancedSearch->SearchCondition = @$filter["v_CIUDAD_PARTIDO"];
        $this->CIUDAD_PARTIDO->AdvancedSearch->SearchValue2 = @$filter["y_CIUDAD_PARTIDO"];
        $this->CIUDAD_PARTIDO->AdvancedSearch->SearchOperator2 = @$filter["w_CIUDAD_PARTIDO"];
        $this->CIUDAD_PARTIDO->AdvancedSearch->save();

        // Field PAIS_PARTIDO
        $this->PAIS_PARTIDO->AdvancedSearch->SearchValue = @$filter["x_PAIS_PARTIDO"];
        $this->PAIS_PARTIDO->AdvancedSearch->SearchOperator = @$filter["z_PAIS_PARTIDO"];
        $this->PAIS_PARTIDO->AdvancedSearch->SearchCondition = @$filter["v_PAIS_PARTIDO"];
        $this->PAIS_PARTIDO->AdvancedSearch->SearchValue2 = @$filter["y_PAIS_PARTIDO"];
        $this->PAIS_PARTIDO->AdvancedSearch->SearchOperator2 = @$filter["w_PAIS_PARTIDO"];
        $this->PAIS_PARTIDO->AdvancedSearch->save();

        // Field GOLES_LOCAL
        $this->GOLES_LOCAL->AdvancedSearch->SearchValue = @$filter["x_GOLES_LOCAL"];
        $this->GOLES_LOCAL->AdvancedSearch->SearchOperator = @$filter["z_GOLES_LOCAL"];
        $this->GOLES_LOCAL->AdvancedSearch->SearchCondition = @$filter["v_GOLES_LOCAL"];
        $this->GOLES_LOCAL->AdvancedSearch->SearchValue2 = @$filter["y_GOLES_LOCAL"];
        $this->GOLES_LOCAL->AdvancedSearch->SearchOperator2 = @$filter["w_GOLES_LOCAL"];
        $this->GOLES_LOCAL->AdvancedSearch->save();

        // Field GOLES_VISITANTE
        $this->GOLES_VISITANTE->AdvancedSearch->SearchValue = @$filter["x_GOLES_VISITANTE"];
        $this->GOLES_VISITANTE->AdvancedSearch->SearchOperator = @$filter["z_GOLES_VISITANTE"];
        $this->GOLES_VISITANTE->AdvancedSearch->SearchCondition = @$filter["v_GOLES_VISITANTE"];
        $this->GOLES_VISITANTE->AdvancedSearch->SearchValue2 = @$filter["y_GOLES_VISITANTE"];
        $this->GOLES_VISITANTE->AdvancedSearch->SearchOperator2 = @$filter["w_GOLES_VISITANTE"];
        $this->GOLES_VISITANTE->AdvancedSearch->save();

        // Field GOLES_EXTRA_EQUIPO1
        $this->GOLES_EXTRA_EQUIPO1->AdvancedSearch->SearchValue = @$filter["x_GOLES_EXTRA_EQUIPO1"];
        $this->GOLES_EXTRA_EQUIPO1->AdvancedSearch->SearchOperator = @$filter["z_GOLES_EXTRA_EQUIPO1"];
        $this->GOLES_EXTRA_EQUIPO1->AdvancedSearch->SearchCondition = @$filter["v_GOLES_EXTRA_EQUIPO1"];
        $this->GOLES_EXTRA_EQUIPO1->AdvancedSearch->SearchValue2 = @$filter["y_GOLES_EXTRA_EQUIPO1"];
        $this->GOLES_EXTRA_EQUIPO1->AdvancedSearch->SearchOperator2 = @$filter["w_GOLES_EXTRA_EQUIPO1"];
        $this->GOLES_EXTRA_EQUIPO1->AdvancedSearch->save();

        // Field GOLES_EXTRA_EQUIPO2
        $this->GOLES_EXTRA_EQUIPO2->AdvancedSearch->SearchValue = @$filter["x_GOLES_EXTRA_EQUIPO2"];
        $this->GOLES_EXTRA_EQUIPO2->AdvancedSearch->SearchOperator = @$filter["z_GOLES_EXTRA_EQUIPO2"];
        $this->GOLES_EXTRA_EQUIPO2->AdvancedSearch->SearchCondition = @$filter["v_GOLES_EXTRA_EQUIPO2"];
        $this->GOLES_EXTRA_EQUIPO2->AdvancedSearch->SearchValue2 = @$filter["y_GOLES_EXTRA_EQUIPO2"];
        $this->GOLES_EXTRA_EQUIPO2->AdvancedSearch->SearchOperator2 = @$filter["w_GOLES_EXTRA_EQUIPO2"];
        $this->GOLES_EXTRA_EQUIPO2->AdvancedSearch->save();

        // Field NOTA_PARTIDO
        $this->NOTA_PARTIDO->AdvancedSearch->SearchValue = @$filter["x_NOTA_PARTIDO"];
        $this->NOTA_PARTIDO->AdvancedSearch->SearchOperator = @$filter["z_NOTA_PARTIDO"];
        $this->NOTA_PARTIDO->AdvancedSearch->SearchCondition = @$filter["v_NOTA_PARTIDO"];
        $this->NOTA_PARTIDO->AdvancedSearch->SearchValue2 = @$filter["y_NOTA_PARTIDO"];
        $this->NOTA_PARTIDO->AdvancedSearch->SearchOperator2 = @$filter["w_NOTA_PARTIDO"];
        $this->NOTA_PARTIDO->AdvancedSearch->save();

        // Field RESUMEN_PARTIDO
        $this->RESUMEN_PARTIDO->AdvancedSearch->SearchValue = @$filter["x_RESUMEN_PARTIDO"];
        $this->RESUMEN_PARTIDO->AdvancedSearch->SearchOperator = @$filter["z_RESUMEN_PARTIDO"];
        $this->RESUMEN_PARTIDO->AdvancedSearch->SearchCondition = @$filter["v_RESUMEN_PARTIDO"];
        $this->RESUMEN_PARTIDO->AdvancedSearch->SearchValue2 = @$filter["y_RESUMEN_PARTIDO"];
        $this->RESUMEN_PARTIDO->AdvancedSearch->SearchOperator2 = @$filter["w_RESUMEN_PARTIDO"];
        $this->RESUMEN_PARTIDO->AdvancedSearch->save();

        // Field ESTADO_PARTIDO
        $this->ESTADO_PARTIDO->AdvancedSearch->SearchValue = @$filter["x_ESTADO_PARTIDO"];
        $this->ESTADO_PARTIDO->AdvancedSearch->SearchOperator = @$filter["z_ESTADO_PARTIDO"];
        $this->ESTADO_PARTIDO->AdvancedSearch->SearchCondition = @$filter["v_ESTADO_PARTIDO"];
        $this->ESTADO_PARTIDO->AdvancedSearch->SearchValue2 = @$filter["y_ESTADO_PARTIDO"];
        $this->ESTADO_PARTIDO->AdvancedSearch->SearchOperator2 = @$filter["w_ESTADO_PARTIDO"];
        $this->ESTADO_PARTIDO->AdvancedSearch->save();

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

        // Field automatico
        $this->automatico->AdvancedSearch->SearchValue = @$filter["x_automatico"];
        $this->automatico->AdvancedSearch->SearchOperator = @$filter["z_automatico"];
        $this->automatico->AdvancedSearch->SearchCondition = @$filter["v_automatico"];
        $this->automatico->AdvancedSearch->SearchValue2 = @$filter["y_automatico"];
        $this->automatico->AdvancedSearch->SearchOperator2 = @$filter["w_automatico"];
        $this->automatico->AdvancedSearch->save();

        // Field actualizado
        $this->actualizado->AdvancedSearch->SearchValue = @$filter["x_actualizado"];
        $this->actualizado->AdvancedSearch->SearchOperator = @$filter["z_actualizado"];
        $this->actualizado->AdvancedSearch->SearchCondition = @$filter["v_actualizado"];
        $this->actualizado->AdvancedSearch->SearchValue2 = @$filter["y_actualizado"];
        $this->actualizado->AdvancedSearch->SearchOperator2 = @$filter["w_actualizado"];
        $this->actualizado->AdvancedSearch->save();
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
        $this->buildSearchSql($where, $this->ID_TORNEO, $default, true); // ID_TORNEO
        $this->buildSearchSql($where, $this->equipo_local, $default, false); // equipo_local
        $this->buildSearchSql($where, $this->equipo_visitante, $default, false); // equipo_visitante
        $this->buildSearchSql($where, $this->ID_PARTIDO, $default, false); // ID_PARTIDO
        $this->buildSearchSql($where, $this->FECHA_PARTIDO, $default, false); // FECHA_PARTIDO
        $this->buildSearchSql($where, $this->HORA_PARTIDO, $default, false); // HORA_PARTIDO
        $this->buildSearchSql($where, $this->ESTADIO, $default, true); // ESTADIO
        $this->buildSearchSql($where, $this->CIUDAD_PARTIDO, $default, false); // CIUDAD_PARTIDO
        $this->buildSearchSql($where, $this->PAIS_PARTIDO, $default, false); // PAIS_PARTIDO
        $this->buildSearchSql($where, $this->GOLES_LOCAL, $default, false); // GOLES_LOCAL
        $this->buildSearchSql($where, $this->GOLES_VISITANTE, $default, false); // GOLES_VISITANTE
        $this->buildSearchSql($where, $this->GOLES_EXTRA_EQUIPO1, $default, false); // GOLES_EXTRA_EQUIPO1
        $this->buildSearchSql($where, $this->GOLES_EXTRA_EQUIPO2, $default, false); // GOLES_EXTRA_EQUIPO2
        $this->buildSearchSql($where, $this->NOTA_PARTIDO, $default, false); // NOTA_PARTIDO
        $this->buildSearchSql($where, $this->RESUMEN_PARTIDO, $default, false); // RESUMEN_PARTIDO
        $this->buildSearchSql($where, $this->ESTADO_PARTIDO, $default, false); // ESTADO_PARTIDO
        $this->buildSearchSql($where, $this->crea_dato, $default, false); // crea_dato
        $this->buildSearchSql($where, $this->modifica_dato, $default, false); // modifica_dato
        $this->buildSearchSql($where, $this->usuario_dato, $default, false); // usuario_dato
        $this->buildSearchSql($where, $this->automatico, $default, false); // automatico
        $this->buildSearchSql($where, $this->actualizado, $default, false); // actualizado

        // Set up search parm
        if (!$default && $where != "" && in_array($this->Command, ["", "reset", "resetall"])) {
            $this->Command = "search";
        }
        if (!$default && $this->Command == "search") {
            $this->ID_TORNEO->AdvancedSearch->save(); // ID_TORNEO
            $this->equipo_local->AdvancedSearch->save(); // equipo_local
            $this->equipo_visitante->AdvancedSearch->save(); // equipo_visitante
            $this->ID_PARTIDO->AdvancedSearch->save(); // ID_PARTIDO
            $this->FECHA_PARTIDO->AdvancedSearch->save(); // FECHA_PARTIDO
            $this->HORA_PARTIDO->AdvancedSearch->save(); // HORA_PARTIDO
            $this->ESTADIO->AdvancedSearch->save(); // ESTADIO
            $this->CIUDAD_PARTIDO->AdvancedSearch->save(); // CIUDAD_PARTIDO
            $this->PAIS_PARTIDO->AdvancedSearch->save(); // PAIS_PARTIDO
            $this->GOLES_LOCAL->AdvancedSearch->save(); // GOLES_LOCAL
            $this->GOLES_VISITANTE->AdvancedSearch->save(); // GOLES_VISITANTE
            $this->GOLES_EXTRA_EQUIPO1->AdvancedSearch->save(); // GOLES_EXTRA_EQUIPO1
            $this->GOLES_EXTRA_EQUIPO2->AdvancedSearch->save(); // GOLES_EXTRA_EQUIPO2
            $this->NOTA_PARTIDO->AdvancedSearch->save(); // NOTA_PARTIDO
            $this->RESUMEN_PARTIDO->AdvancedSearch->save(); // RESUMEN_PARTIDO
            $this->ESTADO_PARTIDO->AdvancedSearch->save(); // ESTADO_PARTIDO
            $this->crea_dato->AdvancedSearch->save(); // crea_dato
            $this->modifica_dato->AdvancedSearch->save(); // modifica_dato
            $this->usuario_dato->AdvancedSearch->save(); // usuario_dato
            $this->automatico->AdvancedSearch->save(); // automatico
            $this->actualizado->AdvancedSearch->save(); // actualizado
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
        $searchFlds[] = &$this->ID_TORNEO;
        $searchFlds[] = &$this->FECHA_PARTIDO;
        $searchFlds[] = &$this->HORA_PARTIDO;
        $searchFlds[] = &$this->ESTADIO;
        $searchFlds[] = &$this->CIUDAD_PARTIDO;
        $searchFlds[] = &$this->PAIS_PARTIDO;
        $searchFlds[] = &$this->NOTA_PARTIDO;
        $searchFlds[] = &$this->RESUMEN_PARTIDO;
        $searchFlds[] = &$this->ESTADO_PARTIDO;
        $searchFlds[] = &$this->usuario_dato;
        $searchFlds[] = &$this->actualizado;
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
        if ($this->ID_TORNEO->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->equipo_local->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->equipo_visitante->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->ID_PARTIDO->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->FECHA_PARTIDO->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->HORA_PARTIDO->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->ESTADIO->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->CIUDAD_PARTIDO->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->PAIS_PARTIDO->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->GOLES_LOCAL->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->GOLES_VISITANTE->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->GOLES_EXTRA_EQUIPO1->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->GOLES_EXTRA_EQUIPO2->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->NOTA_PARTIDO->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->RESUMEN_PARTIDO->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->ESTADO_PARTIDO->AdvancedSearch->issetSession()) {
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
        if ($this->automatico->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->actualizado->AdvancedSearch->issetSession()) {
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
        $this->ID_TORNEO->AdvancedSearch->unsetSession();
        $this->equipo_local->AdvancedSearch->unsetSession();
        $this->equipo_visitante->AdvancedSearch->unsetSession();
        $this->ID_PARTIDO->AdvancedSearch->unsetSession();
        $this->FECHA_PARTIDO->AdvancedSearch->unsetSession();
        $this->HORA_PARTIDO->AdvancedSearch->unsetSession();
        $this->ESTADIO->AdvancedSearch->unsetSession();
        $this->CIUDAD_PARTIDO->AdvancedSearch->unsetSession();
        $this->PAIS_PARTIDO->AdvancedSearch->unsetSession();
        $this->GOLES_LOCAL->AdvancedSearch->unsetSession();
        $this->GOLES_VISITANTE->AdvancedSearch->unsetSession();
        $this->GOLES_EXTRA_EQUIPO1->AdvancedSearch->unsetSession();
        $this->GOLES_EXTRA_EQUIPO2->AdvancedSearch->unsetSession();
        $this->NOTA_PARTIDO->AdvancedSearch->unsetSession();
        $this->RESUMEN_PARTIDO->AdvancedSearch->unsetSession();
        $this->ESTADO_PARTIDO->AdvancedSearch->unsetSession();
        $this->crea_dato->AdvancedSearch->unsetSession();
        $this->modifica_dato->AdvancedSearch->unsetSession();
        $this->usuario_dato->AdvancedSearch->unsetSession();
        $this->automatico->AdvancedSearch->unsetSession();
        $this->actualizado->AdvancedSearch->unsetSession();
    }

    // Restore all search parameters
    protected function restoreSearchParms()
    {
        $this->RestoreSearch = true;

        // Restore basic search values
        $this->BasicSearch->load();

        // Restore advanced search values
        $this->ID_TORNEO->AdvancedSearch->load();
        $this->equipo_local->AdvancedSearch->load();
        $this->equipo_visitante->AdvancedSearch->load();
        $this->ID_PARTIDO->AdvancedSearch->load();
        $this->FECHA_PARTIDO->AdvancedSearch->load();
        $this->HORA_PARTIDO->AdvancedSearch->load();
        $this->ESTADIO->AdvancedSearch->load();
        $this->CIUDAD_PARTIDO->AdvancedSearch->load();
        $this->PAIS_PARTIDO->AdvancedSearch->load();
        $this->GOLES_LOCAL->AdvancedSearch->load();
        $this->GOLES_VISITANTE->AdvancedSearch->load();
        $this->GOLES_EXTRA_EQUIPO1->AdvancedSearch->load();
        $this->GOLES_EXTRA_EQUIPO2->AdvancedSearch->load();
        $this->NOTA_PARTIDO->AdvancedSearch->load();
        $this->RESUMEN_PARTIDO->AdvancedSearch->load();
        $this->ESTADO_PARTIDO->AdvancedSearch->load();
        $this->crea_dato->AdvancedSearch->load();
        $this->modifica_dato->AdvancedSearch->load();
        $this->usuario_dato->AdvancedSearch->load();
        $this->automatico->AdvancedSearch->load();
        $this->actualizado->AdvancedSearch->load();
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
            $this->updateSort($this->ID_TORNEO); // ID_TORNEO
            $this->updateSort($this->equipo_local); // equipo_local
            $this->updateSort($this->equipo_visitante); // equipo_visitante
            $this->updateSort($this->ID_PARTIDO); // ID_PARTIDO
            $this->updateSort($this->FECHA_PARTIDO); // FECHA_PARTIDO
            $this->updateSort($this->HORA_PARTIDO); // HORA_PARTIDO
            $this->updateSort($this->ESTADIO); // ESTADIO
            $this->updateSort($this->CIUDAD_PARTIDO); // CIUDAD_PARTIDO
            $this->updateSort($this->PAIS_PARTIDO); // PAIS_PARTIDO
            $this->updateSort($this->GOLES_LOCAL); // GOLES_LOCAL
            $this->updateSort($this->GOLES_VISITANTE); // GOLES_VISITANTE
            $this->updateSort($this->GOLES_EXTRA_EQUIPO1); // GOLES_EXTRA_EQUIPO1
            $this->updateSort($this->GOLES_EXTRA_EQUIPO2); // GOLES_EXTRA_EQUIPO2
            $this->updateSort($this->NOTA_PARTIDO); // NOTA_PARTIDO
            $this->updateSort($this->RESUMEN_PARTIDO); // RESUMEN_PARTIDO
            $this->updateSort($this->ESTADO_PARTIDO); // ESTADO_PARTIDO
            $this->updateSort($this->crea_dato); // crea_dato
            $this->updateSort($this->modifica_dato); // modifica_dato
            $this->updateSort($this->usuario_dato); // usuario_dato
            $this->updateSort($this->automatico); // automatico
            $this->updateSort($this->actualizado); // actualizado
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
                $this->ID_TORNEO->setSort("");
                $this->equipo_local->setSort("");
                $this->equipo_visitante->setSort("");
                $this->ID_PARTIDO->setSort("");
                $this->FECHA_PARTIDO->setSort("");
                $this->HORA_PARTIDO->setSort("");
                $this->ESTADIO->setSort("");
                $this->CIUDAD_PARTIDO->setSort("");
                $this->PAIS_PARTIDO->setSort("");
                $this->GOLES_LOCAL->setSort("");
                $this->GOLES_VISITANTE->setSort("");
                $this->GOLES_EXTRA_EQUIPO1->setSort("");
                $this->GOLES_EXTRA_EQUIPO2->setSort("");
                $this->NOTA_PARTIDO->setSort("");
                $this->RESUMEN_PARTIDO->setSort("");
                $this->ESTADO_PARTIDO->setSort("");
                $this->crea_dato->setSort("");
                $this->modifica_dato->setSort("");
                $this->usuario_dato->setSort("");
                $this->automatico->setSort("");
                $this->actualizado->setSort("");
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
                "<button class=\"ew-grid-link ew-inline-update\" title=\"" . HtmlTitle($Language->phrase("UpdateLink")) . "\" data-caption=\"" . HtmlTitle($Language->phrase("UpdateLink")) . "\" form=\"fpartidoslist\" formaction=\"" . HtmlEncode(GetUrl(UrlAddHash($this->pageName(), "r" . $this->RowCount . "_" . $this->TableVar))) . "\">" . $Language->phrase("UpdateLink") . "</button>&nbsp;" .
                "<a class=\"ew-grid-link ew-inline-cancel\" title=\"" . HtmlTitle($Language->phrase("CancelLink")) . "\" data-caption=\"" . HtmlTitle($Language->phrase("CancelLink")) . "\" href=\"" . $cancelurl . "\">" . $Language->phrase("CancelLink") . "</a>" .
                "<input type=\"hidden\" name=\"action\" id=\"action\" value=\"update\"></div>";
            $opt->Body .= "<input type=\"hidden\" name=\"k" . $this->RowIndex . "_key\" id=\"k" . $this->RowIndex . "_key\" value=\"" . HtmlEncode($this->ID_PARTIDO->CurrentValue) . "\">";
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
                    $link = "<li><button type=\"button\" class=\"dropdown-item ew-action ew-list-action\" data-caption=\"" . HtmlTitle($caption) . "\" data-ew-action=\"submit\" form=\"fpartidoslist\" data-key=\"" . $this->keyToJson(true) . "\"" . $listaction->toDataAttrs() . ">" . $icon . $listaction->Caption . "</button></li>";
                    if ($link != "") {
                        $links[] = $link;
                        if ($body == "") { // Setup first button
                            $body = "<button type=\"button\" class=\"btn btn-default ew-action ew-list-action\" title=\"" . HtmlTitle($caption) . "\" data-caption=\"" . HtmlTitle($caption) . "\" data-ew-action=\"submit\" form=\"fpartidoslist\" data-key=\"" . $this->keyToJson(true) . "\"" . $listaction->toDataAttrs() . ">" . $icon . $listaction->Caption . "</button>";
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
        $opt->Body = "<div class=\"form-check\"><input type=\"checkbox\" id=\"key_m_" . $this->RowCount . "\" name=\"key_m[]\" class=\"form-check-input ew-multi-select\" value=\"" . HtmlEncode($this->ID_PARTIDO->CurrentValue) . "\" data-ew-action=\"select-key\"></div>";
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
        $item->Body = "<button type=\"button\" class=\"ew-action ew-multi-delete\" title=\"" . HtmlTitle($Language->phrase("DeleteSelectedLink")) . "\" data-caption=\"" . HtmlTitle($Language->phrase("DeleteSelectedLink")) . "\" form=\"fpartidoslist\" data-ew-action=\"submit\" data-url=\"" . GetUrl($this->MultiDeleteUrl) . "\"data-data='{\"action\":\"show\"}'>" . $Language->phrase("DeleteSelectedLink") . "</button>";
        $item->Visible = $Security->canDelete();

        // Show column list for column visibility
        if ($this->UseColumnVisibility) {
            $option = $this->OtherOptions["column"];
            $item = &$option->addGroupOption();
            $item->Body = "";
            $item->Visible = $this->UseColumnVisibility;
            $option->add("ID_TORNEO", $this->createColumnOption("ID_TORNEO"));
            $option->add("equipo_local", $this->createColumnOption("equipo_local"));
            $option->add("equipo_visitante", $this->createColumnOption("equipo_visitante"));
            $option->add("ID_PARTIDO", $this->createColumnOption("ID_PARTIDO"));
            $option->add("FECHA_PARTIDO", $this->createColumnOption("FECHA_PARTIDO"));
            $option->add("HORA_PARTIDO", $this->createColumnOption("HORA_PARTIDO"));
            $option->add("ESTADIO", $this->createColumnOption("ESTADIO"));
            $option->add("CIUDAD_PARTIDO", $this->createColumnOption("CIUDAD_PARTIDO"));
            $option->add("PAIS_PARTIDO", $this->createColumnOption("PAIS_PARTIDO"));
            $option->add("GOLES_LOCAL", $this->createColumnOption("GOLES_LOCAL"));
            $option->add("GOLES_VISITANTE", $this->createColumnOption("GOLES_VISITANTE"));
            $option->add("GOLES_EXTRA_EQUIPO1", $this->createColumnOption("GOLES_EXTRA_EQUIPO1"));
            $option->add("GOLES_EXTRA_EQUIPO2", $this->createColumnOption("GOLES_EXTRA_EQUIPO2"));
            $option->add("NOTA_PARTIDO", $this->createColumnOption("NOTA_PARTIDO"));
            $option->add("RESUMEN_PARTIDO", $this->createColumnOption("RESUMEN_PARTIDO"));
            $option->add("ESTADO_PARTIDO", $this->createColumnOption("ESTADO_PARTIDO"));
            $option->add("crea_dato", $this->createColumnOption("crea_dato"));
            $option->add("modifica_dato", $this->createColumnOption("modifica_dato"));
            $option->add("usuario_dato", $this->createColumnOption("usuario_dato"));
            $option->add("automatico", $this->createColumnOption("automatico"));
            $option->add("actualizado", $this->createColumnOption("actualizado"));
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
        $item->Body = "<a class=\"ew-save-filter\" data-form=\"fpartidossrch\" data-ew-action=\"none\">" . $Language->phrase("SaveCurrentFilter") . "</a>";
        $item->Visible = true;
        $item = &$this->FilterOptions->add("deletefilter");
        $item->Body = "<a class=\"ew-delete-filter\" data-form=\"fpartidossrch\" data-ew-action=\"none\">" . $Language->phrase("DeleteFilter") . "</a>";
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
                $item->Body = '<button type="button" class="btn btn-default ew-action ew-list-action" title="' . HtmlEncode($caption) . '" data-caption="' . HtmlEncode($caption) . '" data-ew-action="submit" form="fpartidoslist"' . $listaction->toDataAttrs() . '>' . $icon . '</button>';
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
        $this->FECHA_PARTIDO->DefaultValue = "07/01/2022";
        $this->FECHA_PARTIDO->OldValue = $this->FECHA_PARTIDO->DefaultValue;
        $this->HORA_PARTIDO->DefaultValue = "00:00";
        $this->HORA_PARTIDO->OldValue = $this->HORA_PARTIDO->DefaultValue;
        $this->GOLES_LOCAL->DefaultValue = 0;
        $this->GOLES_LOCAL->OldValue = $this->GOLES_LOCAL->DefaultValue;
        $this->GOLES_VISITANTE->DefaultValue = 0;
        $this->GOLES_VISITANTE->OldValue = $this->GOLES_VISITANTE->DefaultValue;
        $this->GOLES_EXTRA_EQUIPO1->DefaultValue = 0;
        $this->GOLES_EXTRA_EQUIPO1->OldValue = $this->GOLES_EXTRA_EQUIPO1->DefaultValue;
        $this->GOLES_EXTRA_EQUIPO2->DefaultValue = 0;
        $this->GOLES_EXTRA_EQUIPO2->OldValue = $this->GOLES_EXTRA_EQUIPO2->DefaultValue;
        $this->ESTADO_PARTIDO->DefaultValue = "Por jugar";
        $this->ESTADO_PARTIDO->OldValue = $this->ESTADO_PARTIDO->DefaultValue;
        $this->usuario_dato->DefaultValue = "admin";
        $this->usuario_dato->OldValue = $this->usuario_dato->DefaultValue;
        $this->actualizado->DefaultValue = "0";
        $this->actualizado->OldValue = $this->actualizado->DefaultValue;
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

        // ID_TORNEO
        if ($this->ID_TORNEO->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->ID_TORNEO->AdvancedSearch->SearchValue != "" || $this->ID_TORNEO->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // equipo_local
        if ($this->equipo_local->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->equipo_local->AdvancedSearch->SearchValue != "" || $this->equipo_local->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // equipo_visitante
        if ($this->equipo_visitante->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->equipo_visitante->AdvancedSearch->SearchValue != "" || $this->equipo_visitante->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // ID_PARTIDO
        if ($this->ID_PARTIDO->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->ID_PARTIDO->AdvancedSearch->SearchValue != "" || $this->ID_PARTIDO->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // FECHA_PARTIDO
        if ($this->FECHA_PARTIDO->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->FECHA_PARTIDO->AdvancedSearch->SearchValue != "" || $this->FECHA_PARTIDO->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // HORA_PARTIDO
        if ($this->HORA_PARTIDO->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->HORA_PARTIDO->AdvancedSearch->SearchValue != "" || $this->HORA_PARTIDO->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // ESTADIO
        if ($this->ESTADIO->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->ESTADIO->AdvancedSearch->SearchValue != "" || $this->ESTADIO->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // CIUDAD_PARTIDO
        if ($this->CIUDAD_PARTIDO->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->CIUDAD_PARTIDO->AdvancedSearch->SearchValue != "" || $this->CIUDAD_PARTIDO->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // PAIS_PARTIDO
        if ($this->PAIS_PARTIDO->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->PAIS_PARTIDO->AdvancedSearch->SearchValue != "" || $this->PAIS_PARTIDO->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // GOLES_LOCAL
        if ($this->GOLES_LOCAL->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->GOLES_LOCAL->AdvancedSearch->SearchValue != "" || $this->GOLES_LOCAL->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // GOLES_VISITANTE
        if ($this->GOLES_VISITANTE->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->GOLES_VISITANTE->AdvancedSearch->SearchValue != "" || $this->GOLES_VISITANTE->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // GOLES_EXTRA_EQUIPO1
        if ($this->GOLES_EXTRA_EQUIPO1->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->GOLES_EXTRA_EQUIPO1->AdvancedSearch->SearchValue != "" || $this->GOLES_EXTRA_EQUIPO1->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // GOLES_EXTRA_EQUIPO2
        if ($this->GOLES_EXTRA_EQUIPO2->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->GOLES_EXTRA_EQUIPO2->AdvancedSearch->SearchValue != "" || $this->GOLES_EXTRA_EQUIPO2->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // NOTA_PARTIDO
        if ($this->NOTA_PARTIDO->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->NOTA_PARTIDO->AdvancedSearch->SearchValue != "" || $this->NOTA_PARTIDO->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // RESUMEN_PARTIDO
        if ($this->RESUMEN_PARTIDO->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->RESUMEN_PARTIDO->AdvancedSearch->SearchValue != "" || $this->RESUMEN_PARTIDO->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // ESTADO_PARTIDO
        if ($this->ESTADO_PARTIDO->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->ESTADO_PARTIDO->AdvancedSearch->SearchValue != "" || $this->ESTADO_PARTIDO->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
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

        // automatico
        if ($this->automatico->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->automatico->AdvancedSearch->SearchValue != "" || $this->automatico->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }
        if (is_array($this->automatico->AdvancedSearch->SearchValue)) {
            $this->automatico->AdvancedSearch->SearchValue = implode(Config("MULTIPLE_OPTION_SEPARATOR"), $this->automatico->AdvancedSearch->SearchValue);
        }
        if (is_array($this->automatico->AdvancedSearch->SearchValue2)) {
            $this->automatico->AdvancedSearch->SearchValue2 = implode(Config("MULTIPLE_OPTION_SEPARATOR"), $this->automatico->AdvancedSearch->SearchValue2);
        }

        // actualizado
        if ($this->actualizado->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->actualizado->AdvancedSearch->SearchValue != "" || $this->actualizado->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
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
        if (!$this->ID_PARTIDO->IsDetailKey && !$this->isGridAdd() && !$this->isAdd()) {
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
        if (!$this->isGridAdd() && !$this->isAdd()) {
            $this->ID_PARTIDO->CurrentValue = $this->ID_PARTIDO->FormValue;
        }
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

        // equipo_local

        // equipo_visitante

        // ID_PARTIDO

        // FECHA_PARTIDO

        // HORA_PARTIDO

        // ESTADIO

        // CIUDAD_PARTIDO

        // PAIS_PARTIDO

        // GOLES_LOCAL

        // GOLES_VISITANTE

        // GOLES_EXTRA_EQUIPO1

        // GOLES_EXTRA_EQUIPO2

        // NOTA_PARTIDO

        // RESUMEN_PARTIDO

        // ESTADO_PARTIDO

        // crea_dato

        // modifica_dato

        // usuario_dato

        // automatico

        // actualizado

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
            $this->ID_TORNEO->TooltipValue = "";

            // equipo_local
            $this->equipo_local->LinkCustomAttributes = "";
            $this->equipo_local->HrefValue = "";
            $this->equipo_local->TooltipValue = "";

            // equipo_visitante
            $this->equipo_visitante->LinkCustomAttributes = "";
            $this->equipo_visitante->HrefValue = "";
            $this->equipo_visitante->TooltipValue = "";

            // ID_PARTIDO
            $this->ID_PARTIDO->LinkCustomAttributes = "";
            $this->ID_PARTIDO->HrefValue = "";
            $this->ID_PARTIDO->TooltipValue = "";

            // FECHA_PARTIDO
            $this->FECHA_PARTIDO->LinkCustomAttributes = "";
            $this->FECHA_PARTIDO->HrefValue = "";
            $this->FECHA_PARTIDO->TooltipValue = "";

            // HORA_PARTIDO
            $this->HORA_PARTIDO->LinkCustomAttributes = "";
            $this->HORA_PARTIDO->HrefValue = "";
            $this->HORA_PARTIDO->TooltipValue = "";

            // ESTADIO
            $this->ESTADIO->LinkCustomAttributes = "";
            $this->ESTADIO->HrefValue = "";
            $this->ESTADIO->TooltipValue = "";

            // CIUDAD_PARTIDO
            $this->CIUDAD_PARTIDO->LinkCustomAttributes = "";
            $this->CIUDAD_PARTIDO->HrefValue = "";
            $this->CIUDAD_PARTIDO->TooltipValue = "";

            // PAIS_PARTIDO
            $this->PAIS_PARTIDO->LinkCustomAttributes = "";
            $this->PAIS_PARTIDO->HrefValue = "";
            $this->PAIS_PARTIDO->TooltipValue = "";

            // GOLES_LOCAL
            $this->GOLES_LOCAL->LinkCustomAttributes = "";
            $this->GOLES_LOCAL->HrefValue = "";
            $this->GOLES_LOCAL->TooltipValue = "";

            // GOLES_VISITANTE
            $this->GOLES_VISITANTE->LinkCustomAttributes = "";
            $this->GOLES_VISITANTE->HrefValue = "";
            $this->GOLES_VISITANTE->TooltipValue = "";

            // GOLES_EXTRA_EQUIPO1
            $this->GOLES_EXTRA_EQUIPO1->LinkCustomAttributes = "";
            $this->GOLES_EXTRA_EQUIPO1->HrefValue = "";
            $this->GOLES_EXTRA_EQUIPO1->TooltipValue = "";

            // GOLES_EXTRA_EQUIPO2
            $this->GOLES_EXTRA_EQUIPO2->LinkCustomAttributes = "";
            $this->GOLES_EXTRA_EQUIPO2->HrefValue = "";
            $this->GOLES_EXTRA_EQUIPO2->TooltipValue = "";

            // NOTA_PARTIDO
            $this->NOTA_PARTIDO->LinkCustomAttributes = "";
            $this->NOTA_PARTIDO->HrefValue = "";
            $this->NOTA_PARTIDO->TooltipValue = "";

            // RESUMEN_PARTIDO
            $this->RESUMEN_PARTIDO->LinkCustomAttributes = "";
            $this->RESUMEN_PARTIDO->HrefValue = "";
            $this->RESUMEN_PARTIDO->TooltipValue = "";

            // ESTADO_PARTIDO
            $this->ESTADO_PARTIDO->LinkCustomAttributes = "";
            $this->ESTADO_PARTIDO->HrefValue = "";
            $this->ESTADO_PARTIDO->TooltipValue = "";

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
            $this->automatico->TooltipValue = "";

            // actualizado
            $this->actualizado->LinkCustomAttributes = "";
            $this->actualizado->HrefValue = "";
            $this->actualizado->TooltipValue = "";
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

            // equipo_local
            $this->equipo_local->setupEditAttributes();
            $this->equipo_local->EditCustomAttributes = "";
            $this->equipo_local->PlaceHolder = RemoveHtml($this->equipo_local->caption());

            // equipo_visitante
            $this->equipo_visitante->setupEditAttributes();
            $this->equipo_visitante->EditCustomAttributes = "";
            $this->equipo_visitante->PlaceHolder = RemoveHtml($this->equipo_visitante->caption());

            // ID_PARTIDO

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
                $filterWrk = "";
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
            $this->crea_dato->EditValue = HtmlEncode(FormatDateTime($this->crea_dato->CurrentValue, $this->crea_dato->formatPattern()));
            $this->crea_dato->PlaceHolder = RemoveHtml($this->crea_dato->caption());

            // modifica_dato
            $this->modifica_dato->setupEditAttributes();
            $this->modifica_dato->EditCustomAttributes = "";
            $this->modifica_dato->EditValue = HtmlEncode(FormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern()));
            $this->modifica_dato->PlaceHolder = RemoveHtml($this->modifica_dato->caption());

            // usuario_dato

            // automatico
            $this->automatico->EditCustomAttributes = "";
            $this->automatico->EditValue = $this->automatico->options(false);
            $this->automatico->PlaceHolder = RemoveHtml($this->automatico->caption());

            // actualizado
            $this->actualizado->setupEditAttributes();
            $this->actualizado->EditCustomAttributes = "";
            $this->actualizado->CurrentValue = "0";

            // Add refer script

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

            // modifica_dato
            $this->modifica_dato->LinkCustomAttributes = "";
            $this->modifica_dato->HrefValue = "";

            // usuario_dato
            $this->usuario_dato->LinkCustomAttributes = "";
            $this->usuario_dato->HrefValue = "";

            // automatico
            $this->automatico->LinkCustomAttributes = "";
            $this->automatico->HrefValue = "";

            // actualizado
            $this->actualizado->LinkCustomAttributes = "";
            $this->actualizado->HrefValue = "";
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
        } elseif ($this->RowType == ROWTYPE_SEARCH) {
            // ID_TORNEO
            if ($this->ID_TORNEO->UseFilter && !EmptyValue($this->ID_TORNEO->AdvancedSearch->SearchValue)) {
                if (is_array($this->ID_TORNEO->AdvancedSearch->SearchValue)) {
                    $this->ID_TORNEO->AdvancedSearch->SearchValue = implode(Config("MULTIPLE_OPTION_SEPARATOR"), $this->ID_TORNEO->AdvancedSearch->SearchValue);
                }
                $this->ID_TORNEO->EditValue = explode(Config("MULTIPLE_OPTION_SEPARATOR"), $this->ID_TORNEO->AdvancedSearch->SearchValue);
            }

            // equipo_local
            $this->equipo_local->setupEditAttributes();
            $this->equipo_local->EditCustomAttributes = "";
            $this->equipo_local->EditValue = HtmlEncode($this->equipo_local->AdvancedSearch->SearchValue);
            $this->equipo_local->PlaceHolder = RemoveHtml($this->equipo_local->caption());

            // equipo_visitante
            $this->equipo_visitante->setupEditAttributes();
            $this->equipo_visitante->EditCustomAttributes = "";
            $this->equipo_visitante->EditValue = HtmlEncode($this->equipo_visitante->AdvancedSearch->SearchValue);
            $this->equipo_visitante->PlaceHolder = RemoveHtml($this->equipo_visitante->caption());

            // ID_PARTIDO
            $this->ID_PARTIDO->setupEditAttributes();
            $this->ID_PARTIDO->EditCustomAttributes = "";
            $this->ID_PARTIDO->EditValue = HtmlEncode($this->ID_PARTIDO->AdvancedSearch->SearchValue);
            $this->ID_PARTIDO->PlaceHolder = RemoveHtml($this->ID_PARTIDO->caption());

            // FECHA_PARTIDO
            $this->FECHA_PARTIDO->setupEditAttributes();
            $this->FECHA_PARTIDO->EditCustomAttributes = "";
            $this->FECHA_PARTIDO->EditValue = HtmlEncode(FormatDateTime(UnFormatDateTime($this->FECHA_PARTIDO->AdvancedSearch->SearchValue, $this->FECHA_PARTIDO->formatPattern()), $this->FECHA_PARTIDO->formatPattern()));
            $this->FECHA_PARTIDO->PlaceHolder = RemoveHtml($this->FECHA_PARTIDO->caption());

            // HORA_PARTIDO
            $this->HORA_PARTIDO->setupEditAttributes();
            $this->HORA_PARTIDO->EditCustomAttributes = "";
            $this->HORA_PARTIDO->EditValue = HtmlEncode(FormatDateTime(UnFormatDateTime($this->HORA_PARTIDO->AdvancedSearch->SearchValue, $this->HORA_PARTIDO->formatPattern()), $this->HORA_PARTIDO->formatPattern()));
            $this->HORA_PARTIDO->PlaceHolder = RemoveHtml($this->HORA_PARTIDO->caption());

            // ESTADIO
            if ($this->ESTADIO->UseFilter && !EmptyValue($this->ESTADIO->AdvancedSearch->SearchValue)) {
                if (is_array($this->ESTADIO->AdvancedSearch->SearchValue)) {
                    $this->ESTADIO->AdvancedSearch->SearchValue = implode(Config("MULTIPLE_OPTION_SEPARATOR"), $this->ESTADIO->AdvancedSearch->SearchValue);
                }
                $this->ESTADIO->EditValue = explode(Config("MULTIPLE_OPTION_SEPARATOR"), $this->ESTADIO->AdvancedSearch->SearchValue);
            }

            // CIUDAD_PARTIDO
            $this->CIUDAD_PARTIDO->setupEditAttributes();
            $this->CIUDAD_PARTIDO->EditCustomAttributes = "";
            $this->CIUDAD_PARTIDO->EditValue = HtmlEncode($this->CIUDAD_PARTIDO->AdvancedSearch->SearchValue);
            $this->CIUDAD_PARTIDO->PlaceHolder = RemoveHtml($this->CIUDAD_PARTIDO->caption());

            // PAIS_PARTIDO
            $this->PAIS_PARTIDO->setupEditAttributes();
            $this->PAIS_PARTIDO->EditCustomAttributes = "";
            $this->PAIS_PARTIDO->PlaceHolder = RemoveHtml($this->PAIS_PARTIDO->caption());

            // GOLES_LOCAL
            $this->GOLES_LOCAL->setupEditAttributes();
            $this->GOLES_LOCAL->EditCustomAttributes = "";
            $this->GOLES_LOCAL->EditValue = HtmlEncode($this->GOLES_LOCAL->AdvancedSearch->SearchValue);
            $this->GOLES_LOCAL->PlaceHolder = RemoveHtml($this->GOLES_LOCAL->caption());

            // GOLES_VISITANTE
            $this->GOLES_VISITANTE->setupEditAttributes();
            $this->GOLES_VISITANTE->EditCustomAttributes = "";
            $this->GOLES_VISITANTE->EditValue = HtmlEncode($this->GOLES_VISITANTE->AdvancedSearch->SearchValue);
            $this->GOLES_VISITANTE->PlaceHolder = RemoveHtml($this->GOLES_VISITANTE->caption());

            // GOLES_EXTRA_EQUIPO1
            $this->GOLES_EXTRA_EQUIPO1->setupEditAttributes();
            $this->GOLES_EXTRA_EQUIPO1->EditCustomAttributes = "";
            $this->GOLES_EXTRA_EQUIPO1->EditValue = HtmlEncode($this->GOLES_EXTRA_EQUIPO1->AdvancedSearch->SearchValue);
            $this->GOLES_EXTRA_EQUIPO1->PlaceHolder = RemoveHtml($this->GOLES_EXTRA_EQUIPO1->caption());

            // GOLES_EXTRA_EQUIPO2
            $this->GOLES_EXTRA_EQUIPO2->setupEditAttributes();
            $this->GOLES_EXTRA_EQUIPO2->EditCustomAttributes = "";
            $this->GOLES_EXTRA_EQUIPO2->EditValue = HtmlEncode($this->GOLES_EXTRA_EQUIPO2->AdvancedSearch->SearchValue);
            $this->GOLES_EXTRA_EQUIPO2->PlaceHolder = RemoveHtml($this->GOLES_EXTRA_EQUIPO2->caption());

            // NOTA_PARTIDO
            $this->NOTA_PARTIDO->setupEditAttributes();
            $this->NOTA_PARTIDO->EditCustomAttributes = "";
            $this->NOTA_PARTIDO->EditValue = HtmlEncode($this->NOTA_PARTIDO->AdvancedSearch->SearchValue);
            $this->NOTA_PARTIDO->PlaceHolder = RemoveHtml($this->NOTA_PARTIDO->caption());

            // RESUMEN_PARTIDO
            $this->RESUMEN_PARTIDO->setupEditAttributes();
            $this->RESUMEN_PARTIDO->EditCustomAttributes = "";
            $this->RESUMEN_PARTIDO->EditValue = HtmlEncode($this->RESUMEN_PARTIDO->AdvancedSearch->SearchValue);
            $this->RESUMEN_PARTIDO->PlaceHolder = RemoveHtml($this->RESUMEN_PARTIDO->caption());

            // ESTADO_PARTIDO
            $this->ESTADO_PARTIDO->setupEditAttributes();
            $this->ESTADO_PARTIDO->EditCustomAttributes = "";
            $this->ESTADO_PARTIDO->EditValue = $this->ESTADO_PARTIDO->options(true);
            $this->ESTADO_PARTIDO->PlaceHolder = RemoveHtml($this->ESTADO_PARTIDO->caption());

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

            // automatico
            $this->automatico->EditCustomAttributes = "";
            $this->automatico->EditValue = $this->automatico->options(false);
            $this->automatico->PlaceHolder = RemoveHtml($this->automatico->caption());

            // actualizado
            $this->actualizado->setupEditAttributes();
            $this->actualizado->EditCustomAttributes = "";
            if (!$this->actualizado->Raw) {
                $this->actualizado->AdvancedSearch->SearchValue = HtmlDecode($this->actualizado->AdvancedSearch->SearchValue);
            }
            $this->actualizado->EditValue = HtmlEncode($this->actualizado->AdvancedSearch->SearchValue);
            $this->actualizado->PlaceHolder = RemoveHtml($this->actualizado->caption());
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
        $hash .= GetFieldHash($row['equipo_local']); // equipo_local
        $hash .= GetFieldHash($row['equipo_visitante']); // equipo_visitante
        $hash .= GetFieldHash($row['FECHA_PARTIDO']); // FECHA_PARTIDO
        $hash .= GetFieldHash($row['HORA_PARTIDO']); // HORA_PARTIDO
        $hash .= GetFieldHash($row['ESTADIO']); // ESTADIO
        $hash .= GetFieldHash($row['CIUDAD_PARTIDO']); // CIUDAD_PARTIDO
        $hash .= GetFieldHash($row['PAIS_PARTIDO']); // PAIS_PARTIDO
        $hash .= GetFieldHash($row['GOLES_LOCAL']); // GOLES_LOCAL
        $hash .= GetFieldHash($row['GOLES_VISITANTE']); // GOLES_VISITANTE
        $hash .= GetFieldHash($row['GOLES_EXTRA_EQUIPO1']); // GOLES_EXTRA_EQUIPO1
        $hash .= GetFieldHash($row['GOLES_EXTRA_EQUIPO2']); // GOLES_EXTRA_EQUIPO2
        $hash .= GetFieldHash($row['NOTA_PARTIDO']); // NOTA_PARTIDO
        $hash .= GetFieldHash($row['RESUMEN_PARTIDO']); // RESUMEN_PARTIDO
        $hash .= GetFieldHash($row['ESTADO_PARTIDO']); // ESTADO_PARTIDO
        $hash .= GetFieldHash($row['automatico']); // automatico
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

        // equipo_local
        $this->equipo_local->setDbValueDef($rsnew, $this->equipo_local->CurrentValue, null, false);

        // equipo_visitante
        $this->equipo_visitante->setDbValueDef($rsnew, $this->equipo_visitante->CurrentValue, null, false);

        // FECHA_PARTIDO
        $this->FECHA_PARTIDO->setDbValueDef($rsnew, UnFormatDateTime($this->FECHA_PARTIDO->CurrentValue, $this->FECHA_PARTIDO->formatPattern()), null, false);

        // HORA_PARTIDO
        $this->HORA_PARTIDO->setDbValueDef($rsnew, UnFormatDateTime($this->HORA_PARTIDO->CurrentValue, $this->HORA_PARTIDO->formatPattern()), null, false);

        // ESTADIO
        $this->ESTADIO->setDbValueDef($rsnew, $this->ESTADIO->CurrentValue, null, false);

        // CIUDAD_PARTIDO
        $this->CIUDAD_PARTIDO->setDbValueDef($rsnew, $this->CIUDAD_PARTIDO->CurrentValue, null, false);

        // PAIS_PARTIDO
        $this->PAIS_PARTIDO->setDbValueDef($rsnew, $this->PAIS_PARTIDO->CurrentValue, null, false);

        // GOLES_LOCAL
        $this->GOLES_LOCAL->setDbValueDef($rsnew, $this->GOLES_LOCAL->CurrentValue, null, strval($this->GOLES_LOCAL->CurrentValue ?? "") == "");

        // GOLES_VISITANTE
        $this->GOLES_VISITANTE->setDbValueDef($rsnew, $this->GOLES_VISITANTE->CurrentValue, null, strval($this->GOLES_VISITANTE->CurrentValue ?? "") == "");

        // GOLES_EXTRA_EQUIPO1
        $this->GOLES_EXTRA_EQUIPO1->setDbValueDef($rsnew, $this->GOLES_EXTRA_EQUIPO1->CurrentValue, null, strval($this->GOLES_EXTRA_EQUIPO1->CurrentValue ?? "") == "");

        // GOLES_EXTRA_EQUIPO2
        $this->GOLES_EXTRA_EQUIPO2->setDbValueDef($rsnew, $this->GOLES_EXTRA_EQUIPO2->CurrentValue, null, strval($this->GOLES_EXTRA_EQUIPO2->CurrentValue ?? "") == "");

        // NOTA_PARTIDO
        $this->NOTA_PARTIDO->setDbValueDef($rsnew, $this->NOTA_PARTIDO->CurrentValue, null, false);

        // RESUMEN_PARTIDO
        $this->RESUMEN_PARTIDO->setDbValueDef($rsnew, $this->RESUMEN_PARTIDO->CurrentValue, null, false);

        // ESTADO_PARTIDO
        $this->ESTADO_PARTIDO->setDbValueDef($rsnew, $this->ESTADO_PARTIDO->CurrentValue, null, false);

        // crea_dato
        $this->crea_dato->setDbValueDef($rsnew, UnFormatDateTime($this->crea_dato->CurrentValue, $this->crea_dato->formatPattern()), null, false);

        // modifica_dato
        $this->modifica_dato->setDbValueDef($rsnew, UnFormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern()), null, false);

        // usuario_dato
        $this->usuario_dato->CurrentValue = CurrentUserName();
        $this->usuario_dato->setDbValueDef($rsnew, $this->usuario_dato->CurrentValue, null);

        // automatico
        $tmpBool = $this->automatico->CurrentValue;
        if ($tmpBool != "1" && $tmpBool != "0") {
            $tmpBool = !empty($tmpBool) ? "1" : "0";
        }
        $this->automatico->setDbValueDef($rsnew, $tmpBool, null, false);

        // actualizado
        $this->actualizado->setDbValueDef($rsnew, $this->actualizado->CurrentValue, null, false);

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
        $this->ID_TORNEO->AdvancedSearch->load();
        $this->equipo_local->AdvancedSearch->load();
        $this->equipo_visitante->AdvancedSearch->load();
        $this->ID_PARTIDO->AdvancedSearch->load();
        $this->FECHA_PARTIDO->AdvancedSearch->load();
        $this->HORA_PARTIDO->AdvancedSearch->load();
        $this->ESTADIO->AdvancedSearch->load();
        $this->CIUDAD_PARTIDO->AdvancedSearch->load();
        $this->PAIS_PARTIDO->AdvancedSearch->load();
        $this->GOLES_LOCAL->AdvancedSearch->load();
        $this->GOLES_VISITANTE->AdvancedSearch->load();
        $this->GOLES_EXTRA_EQUIPO1->AdvancedSearch->load();
        $this->GOLES_EXTRA_EQUIPO2->AdvancedSearch->load();
        $this->NOTA_PARTIDO->AdvancedSearch->load();
        $this->RESUMEN_PARTIDO->AdvancedSearch->load();
        $this->ESTADO_PARTIDO->AdvancedSearch->load();
        $this->crea_dato->AdvancedSearch->load();
        $this->modifica_dato->AdvancedSearch->load();
        $this->usuario_dato->AdvancedSearch->load();
        $this->automatico->AdvancedSearch->load();
        $this->actualizado->AdvancedSearch->load();
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
        $item->Body = "<a class=\"btn btn-default ew-search-toggle" . $searchToggleClass . "\" role=\"button\" title=\"" . $Language->phrase("SearchPanel") . "\" data-caption=\"" . $Language->phrase("SearchPanel") . "\" data-ew-action=\"search-toggle\" data-form=\"fpartidossrch\" aria-pressed=\"" . ($searchToggleClass == " active" ? "true" : "false") . "\">" . $Language->phrase("SearchLink") . "</a>";
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
