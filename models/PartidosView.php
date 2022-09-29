<?php

namespace PHPMaker2022\project11;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Page class
 */
class PartidosView extends Partidos
{
    use MessagesTrait;

    // Page ID
    public $PageID = "view";

    // Project ID
    public $ProjectID = PROJECT_ID;

    // Table name
    public $TableName = 'partidos';

    // Page object name
    public $PageObjName = "PartidosView";

    // View file path
    public $View = null;

    // Title
    public $Title = null; // Title for <title> tag

    // Rendering View
    public $RenderingView = false;

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

        // Set up record key
        if (($keyValue = Get("ID_PARTIDO") ?? Route("ID_PARTIDO")) !== null) {
            $this->RecKey["ID_PARTIDO"] = $keyValue;
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

        // Export options
        $this->ExportOptions = new ListOptions(["TagClassName" => "ew-export-option"]);

        // Other options
        if (!$this->OtherOptions) {
            $this->OtherOptions = new ListOptionsArray();
        }

        // Detail tables
        $this->OtherOptions["detail"] = new ListOptions(["TagClassName" => "ew-detail-option"]);
        // Actions
        $this->OtherOptions["action"] = new ListOptions(["TagClassName" => "ew-action-option"]);
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
    public $ExportOptions; // Export options
    public $OtherOptions; // Other options
    public $DisplayRecords = 1;
    public $DbMasterFilter;
    public $DbDetailFilter;
    public $StartRecord;
    public $StopRecord;
    public $TotalRecords = 0;
    public $RecordRange = 10;
    public $RecKey = [];
    public $IsModal = false;

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

        // Load current record
        $loadCurrentRecord = false;
        $returnUrl = "";
        $matchRecord = false;
        if ($this->isPageRequest()) { // Validate request
            if (($keyValue = Get("ID_PARTIDO") ?? Route("ID_PARTIDO")) !== null) {
                $this->ID_PARTIDO->setQueryStringValue($keyValue);
                $this->RecKey["ID_PARTIDO"] = $this->ID_PARTIDO->QueryStringValue;
            } elseif (Post("ID_PARTIDO") !== null) {
                $this->ID_PARTIDO->setFormValue(Post("ID_PARTIDO"));
                $this->RecKey["ID_PARTIDO"] = $this->ID_PARTIDO->FormValue;
            } elseif (IsApi() && ($keyValue = Key(0) ?? Route(2)) !== null) {
                $this->ID_PARTIDO->setQueryStringValue($keyValue);
                $this->RecKey["ID_PARTIDO"] = $this->ID_PARTIDO->QueryStringValue;
            } elseif (!$loadCurrentRecord) {
                $returnUrl = "partidoslist"; // Return to list
            }

            // Get action
            $this->CurrentAction = "show"; // Display
            switch ($this->CurrentAction) {
                case "show": // Get a record to display

                        // Load record based on key
                        if (IsApi()) {
                            $filter = $this->getRecordFilter();
                            $this->CurrentFilter = $filter;
                            $sql = $this->getCurrentSql();
                            $conn = $this->getConnection();
                            $this->Recordset = LoadRecordset($sql, $conn);
                            $res = $this->Recordset && !$this->Recordset->EOF;
                        } else {
                            $res = $this->loadRow();
                        }
                        if (!$res) { // Load record based on key
                            if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "") {
                                $this->setFailureMessage($Language->phrase("NoRecord")); // Set no record message
                            }
                            $returnUrl = "partidoslist"; // No matching record, return to list
                        }
                    break;
            }
        } else {
            $returnUrl = "partidoslist"; // Not page request, return to list
        }
        if ($returnUrl != "") {
            $this->terminate($returnUrl);
            return;
        }

        // Set up Breadcrumb
        if (!$this->isExport()) {
            $this->setupBreadcrumb();
        }

        // Render row
        $this->RowType = ROWTYPE_VIEW;
        $this->resetAttributes();
        $this->renderRow();

        // Normal return
        if (IsApi()) {
            $rows = $this->getRecordsFromRecordset($this->Recordset, true); // Get current record only
            $this->Recordset->close();
            WriteJson(["success" => true, $this->TableVar => $rows]);
            $this->terminate(true);
            return;
        }

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

    // Set up other options
    protected function setupOtherOptions()
    {
        global $Language, $Security;
        $options = &$this->OtherOptions;
        $option = $options["action"];

        // Add
        $item = &$option->add("add");
        $addcaption = HtmlTitle($Language->phrase("ViewPageAddLink"));
        if ($this->IsModal) {
            $item->Body = "<a class=\"ew-action ew-add\" title=\"" . $addcaption . "\" data-caption=\"" . $addcaption . "\" data-ew-action=\"modal\" data-url=\"" . HtmlEncode(GetUrl($this->AddUrl)) . "\">" . $Language->phrase("ViewPageAddLink") . "</a>";
        } else {
            $item->Body = "<a class=\"ew-action ew-add\" title=\"" . $addcaption . "\" data-caption=\"" . $addcaption . "\" href=\"" . HtmlEncode(GetUrl($this->AddUrl)) . "\">" . $Language->phrase("ViewPageAddLink") . "</a>";
        }
        $item->Visible = ($this->AddUrl != "" && $Security->canAdd());

        // Edit
        $item = &$option->add("edit");
        $editcaption = HtmlTitle($Language->phrase("ViewPageEditLink"));
        if ($this->IsModal) {
            $item->Body = "<a class=\"ew-action ew-edit\" title=\"" . $editcaption . "\" data-caption=\"" . $editcaption . "\" data-ew-action=\"modal\" data-url=\"" . HtmlEncode(GetUrl($this->EditUrl)) . "\">" . $Language->phrase("ViewPageEditLink") . "</a>";
        } else {
            $item->Body = "<a class=\"ew-action ew-edit\" title=\"" . $editcaption . "\" data-caption=\"" . $editcaption . "\" href=\"" . HtmlEncode(GetUrl($this->EditUrl)) . "\">" . $Language->phrase("ViewPageEditLink") . "</a>";
        }
        $item->Visible = ($this->EditUrl != "" && $Security->canEdit());

        // Copy
        $item = &$option->add("copy");
        $copycaption = HtmlTitle($Language->phrase("ViewPageCopyLink"));
        if ($this->IsModal) {
            $item->Body = "<a class=\"ew-action ew-copy\" title=\"" . $copycaption . "\" data-caption=\"" . $copycaption . "\" data-ew-action=\"modal\" data-url=\"" . HtmlEncode(GetUrl($this->CopyUrl)) . "\" data-btn=\"AddBtn\">" . $Language->phrase("ViewPageCopyLink") . "</a>";
        } else {
            $item->Body = "<a class=\"ew-action ew-copy\" title=\"" . $copycaption . "\" data-caption=\"" . $copycaption . "\" href=\"" . HtmlEncode(GetUrl($this->CopyUrl)) . "\">" . $Language->phrase("ViewPageCopyLink") . "</a>";
        }
        $item->Visible = ($this->CopyUrl != "" && $Security->canAdd());

        // Delete
        $item = &$option->add("delete");
        if ($this->IsModal) { // Handle as inline delete
            $item->Body = "<a data-ew-action=\"inline-delete\" class=\"ew-action ew-delete\" title=\"" . HtmlTitle($Language->phrase("ViewPageDeleteLink")) . "\" data-caption=\"" . HtmlTitle($Language->phrase("ViewPageDeleteLink")) . "\" href=\"" . HtmlEncode(UrlAddQuery(GetUrl($this->DeleteUrl), "action=1")) . "\">" . $Language->phrase("ViewPageDeleteLink") . "</a>";
        } else {
            $item->Body = "<a class=\"ew-action ew-delete\" title=\"" . HtmlTitle($Language->phrase("ViewPageDeleteLink")) . "\" data-caption=\"" . HtmlTitle($Language->phrase("ViewPageDeleteLink")) . "\" href=\"" . HtmlEncode(GetUrl($this->DeleteUrl)) . "\">" . $Language->phrase("ViewPageDeleteLink") . "</a>";
        }
        $item->Visible = ($this->DeleteUrl != "" && $Security->canDelete());

        // Set up action default
        $option = $options["action"];
        $option->DropDownButtonPhrase = $Language->phrase("ButtonActions");
        $option->UseDropDownButton = false;
        $option->UseButtonGroup = true;
        $item = &$option->addGroupOption();
        $item->Body = "";
        $item->Visible = false;
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

    // Render row values based on field settings
    public function renderRow()
    {
        global $Security, $Language, $CurrentLanguage;

        // Initialize URLs
        $this->AddUrl = $this->getAddUrl();
        $this->EditUrl = $this->getEditUrl();
        $this->CopyUrl = $this->getCopyUrl();
        $this->DeleteUrl = $this->getDeleteUrl();
        $this->ListUrl = $this->getListUrl();
        $this->setupOtherOptions();

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
        }

        // Call Row Rendered event
        if ($this->RowType != ROWTYPE_AGGREGATEINIT) {
            $this->rowRendered();
        }
    }

    // Set up Breadcrumb
    protected function setupBreadcrumb()
    {
        global $Breadcrumb, $Language;
        $Breadcrumb = new Breadcrumb("index");
        $url = CurrentUrl();
        $Breadcrumb->add("list", $this->TableVar, $this->addMasterUrl("partidoslist"), "", $this->TableVar, true);
        $pageId = "view";
        $Breadcrumb->add("view", $pageId, $url);
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
}
