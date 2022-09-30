<?php

namespace PHPMaker2022\project11;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Page class
 */
class JugadorAddopt extends Jugador
{
    use MessagesTrait;

    // Page ID
    public $PageID = "addopt";

    // Project ID
    public $ProjectID = PROJECT_ID;

    // Table name
    public $TableName = 'jugador';

    // Page object name
    public $PageObjName = "JugadorAddopt";

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

        // Table object (jugador)
        if (!isset($GLOBALS["jugador"]) || get_class($GLOBALS["jugador"]) == PROJECT_NAMESPACE . "jugador") {
            $GLOBALS["jugador"] = &$this;
        }

        // Table name (for backward compatibility only)
        if (!defined(PROJECT_NAMESPACE . "TABLE_NAME")) {
            define(PROJECT_NAMESPACE . "TABLE_NAME", 'jugador');
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
                $tbl = Container("jugador");
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
            $key .= @$ar['id_jugador'];
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
            $this->id_jugador->Visible = false;
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
        $this->id_jugador->Visible = false;
        $this->nombre_jugador->setVisibility();
        $this->votos_jugador->setVisibility();
        $this->imagen_jugador->setVisibility();
        $this->crea_dato->setVisibility();
        $this->modifica_dato->setVisibility();
        $this->usuario_dato->setVisibility();
        $this->posicion->setVisibility();
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
        $this->imagen_jugador->Upload->Index = $CurrentForm->Index;
        $this->imagen_jugador->Upload->uploadFile();
        $this->imagen_jugador->CurrentValue = $this->imagen_jugador->Upload->FileName;
    }

    // Load default values
    protected function loadDefaultValues()
    {
        $this->votos_jugador->DefaultValue = "0";
        $this->votos_jugador->OldValue = $this->votos_jugador->DefaultValue;
        $this->usuario_dato->DefaultValue = "admin";
        $this->usuario_dato->OldValue = $this->usuario_dato->DefaultValue;
    }

    // Load form values
    protected function loadFormValues()
    {
        // Load from form
        global $CurrentForm;
        $validate = !Config("SERVER_VALIDATE");

        // Check field name 'nombre_jugador' first before field var 'x_nombre_jugador'
        $val = $CurrentForm->hasValue("nombre_jugador") ? $CurrentForm->getValue("nombre_jugador") : $CurrentForm->getValue("x_nombre_jugador");
        if (!$this->nombre_jugador->IsDetailKey) {
            $this->nombre_jugador->setFormValue(ConvertFromUtf8($val));
        }

        // Check field name 'votos_jugador' first before field var 'x_votos_jugador'
        $val = $CurrentForm->hasValue("votos_jugador") ? $CurrentForm->getValue("votos_jugador") : $CurrentForm->getValue("x_votos_jugador");
        if (!$this->votos_jugador->IsDetailKey) {
            $this->votos_jugador->setFormValue(ConvertFromUtf8($val));
        }

        // Check field name 'crea_dato' first before field var 'x_crea_dato'
        $val = $CurrentForm->hasValue("crea_dato") ? $CurrentForm->getValue("crea_dato") : $CurrentForm->getValue("x_crea_dato");
        if (!$this->crea_dato->IsDetailKey) {
            $this->crea_dato->setFormValue(ConvertFromUtf8($val), true, $validate);
            $this->crea_dato->CurrentValue = UnFormatDateTime($this->crea_dato->CurrentValue, $this->crea_dato->formatPattern());
        }

        // Check field name 'modifica_dato' first before field var 'x_modifica_dato'
        $val = $CurrentForm->hasValue("modifica_dato") ? $CurrentForm->getValue("modifica_dato") : $CurrentForm->getValue("x_modifica_dato");
        if (!$this->modifica_dato->IsDetailKey) {
            $this->modifica_dato->setFormValue(ConvertFromUtf8($val), true, $validate);
            $this->modifica_dato->CurrentValue = UnFormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern());
        }

        // Check field name 'usuario_dato' first before field var 'x_usuario_dato'
        $val = $CurrentForm->hasValue("usuario_dato") ? $CurrentForm->getValue("usuario_dato") : $CurrentForm->getValue("x_usuario_dato");
        if (!$this->usuario_dato->IsDetailKey) {
            $this->usuario_dato->setFormValue(ConvertFromUtf8($val));
        }

        // Check field name 'posicion' first before field var 'x_posicion'
        $val = $CurrentForm->hasValue("posicion") ? $CurrentForm->getValue("posicion") : $CurrentForm->getValue("x_posicion");
        if (!$this->posicion->IsDetailKey) {
            $this->posicion->setFormValue(ConvertFromUtf8($val));
        }

        // Check field name 'id_jugador' first before field var 'x_id_jugador'
        $val = $CurrentForm->hasValue("id_jugador") ? $CurrentForm->getValue("id_jugador") : $CurrentForm->getValue("x_id_jugador");
        $this->getUploadFiles(); // Get upload files
    }

    // Restore form values
    public function restoreFormValues()
    {
        global $CurrentForm;
        $this->nombre_jugador->CurrentValue = ConvertToUtf8($this->nombre_jugador->FormValue);
        $this->votos_jugador->CurrentValue = ConvertToUtf8($this->votos_jugador->FormValue);
        $this->crea_dato->CurrentValue = ConvertToUtf8($this->crea_dato->FormValue);
        $this->crea_dato->CurrentValue = UnFormatDateTime($this->crea_dato->CurrentValue, $this->crea_dato->formatPattern());
        $this->modifica_dato->CurrentValue = ConvertToUtf8($this->modifica_dato->FormValue);
        $this->modifica_dato->CurrentValue = UnFormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern());
        $this->usuario_dato->CurrentValue = ConvertToUtf8($this->usuario_dato->FormValue);
        $this->posicion->CurrentValue = ConvertToUtf8($this->posicion->FormValue);
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
        $this->id_jugador->setDbValue($row['id_jugador']);
        $this->nombre_jugador->setDbValue($row['nombre_jugador']);
        $this->votos_jugador->setDbValue($row['votos_jugador']);
        $this->imagen_jugador->Upload->DbValue = $row['imagen_jugador'];
        $this->imagen_jugador->setDbValue($this->imagen_jugador->Upload->DbValue);
        $this->crea_dato->setDbValue($row['crea_dato']);
        $this->modifica_dato->setDbValue($row['modifica_dato']);
        $this->usuario_dato->setDbValue($row['usuario_dato']);
        $this->posicion->setDbValue($row['posicion']);
    }

    // Return a row with default values
    protected function newRow()
    {
        $row = [];
        $row['id_jugador'] = $this->id_jugador->DefaultValue;
        $row['nombre_jugador'] = $this->nombre_jugador->DefaultValue;
        $row['votos_jugador'] = $this->votos_jugador->DefaultValue;
        $row['imagen_jugador'] = $this->imagen_jugador->DefaultValue;
        $row['crea_dato'] = $this->crea_dato->DefaultValue;
        $row['modifica_dato'] = $this->modifica_dato->DefaultValue;
        $row['usuario_dato'] = $this->usuario_dato->DefaultValue;
        $row['posicion'] = $this->posicion->DefaultValue;
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

        // id_jugador
        $this->id_jugador->RowCssClass = "row";

        // nombre_jugador
        $this->nombre_jugador->RowCssClass = "row";

        // votos_jugador
        $this->votos_jugador->RowCssClass = "row";

        // imagen_jugador
        $this->imagen_jugador->RowCssClass = "row";

        // crea_dato
        $this->crea_dato->RowCssClass = "row";

        // modifica_dato
        $this->modifica_dato->RowCssClass = "row";

        // usuario_dato
        $this->usuario_dato->RowCssClass = "row";

        // posicion
        $this->posicion->RowCssClass = "row";

        // View row
        if ($this->RowType == ROWTYPE_VIEW) {
            // id_jugador
            $this->id_jugador->ViewValue = $this->id_jugador->CurrentValue;
            $this->id_jugador->ViewCustomAttributes = "";

            // nombre_jugador
            $this->nombre_jugador->ViewValue = $this->nombre_jugador->CurrentValue;
            $this->nombre_jugador->ViewCustomAttributes = "";

            // votos_jugador
            $this->votos_jugador->ViewValue = $this->votos_jugador->CurrentValue;
            $this->votos_jugador->ViewCustomAttributes = "";

            // imagen_jugador
            if (!EmptyValue($this->imagen_jugador->Upload->DbValue)) {
                $this->imagen_jugador->ImageWidth = 50;
                $this->imagen_jugador->ImageHeight = 0;
                $this->imagen_jugador->ImageAlt = $this->imagen_jugador->alt();
                $this->imagen_jugador->ImageCssClass = "ew-image";
                $this->imagen_jugador->ViewValue = $this->imagen_jugador->Upload->DbValue;
            } else {
                $this->imagen_jugador->ViewValue = "";
            }
            $this->imagen_jugador->ViewCustomAttributes = "";

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

            // posicion
            $this->posicion->ViewValue = $this->posicion->CurrentValue;
            $this->posicion->ViewCustomAttributes = "";

            // nombre_jugador
            $this->nombre_jugador->LinkCustomAttributes = "";
            $this->nombre_jugador->HrefValue = "";
            $this->nombre_jugador->TooltipValue = "";

            // votos_jugador
            $this->votos_jugador->LinkCustomAttributes = "";
            $this->votos_jugador->HrefValue = "";
            $this->votos_jugador->TooltipValue = "";

            // imagen_jugador
            $this->imagen_jugador->LinkCustomAttributes = "";
            if (!EmptyValue($this->imagen_jugador->Upload->DbValue)) {
                $this->imagen_jugador->HrefValue = GetFileUploadUrl($this->imagen_jugador, $this->imagen_jugador->htmlDecode($this->imagen_jugador->Upload->DbValue)); // Add prefix/suffix
                $this->imagen_jugador->LinkAttrs["target"] = ""; // Add target
                if ($this->isExport()) {
                    $this->imagen_jugador->HrefValue = FullUrl($this->imagen_jugador->HrefValue, "href");
                }
            } else {
                $this->imagen_jugador->HrefValue = "";
            }
            $this->imagen_jugador->ExportHrefValue = $this->imagen_jugador->UploadPath . $this->imagen_jugador->Upload->DbValue;
            $this->imagen_jugador->TooltipValue = "";
            if ($this->imagen_jugador->UseColorbox) {
                if (EmptyValue($this->imagen_jugador->TooltipValue)) {
                    $this->imagen_jugador->LinkAttrs["title"] = $Language->phrase("ViewImageGallery");
                }
                $this->imagen_jugador->LinkAttrs["data-rel"] = "jugador_x_imagen_jugador";
                $this->imagen_jugador->LinkAttrs->appendClass("ew-lightbox");
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

            // posicion
            $this->posicion->LinkCustomAttributes = "";
            $this->posicion->HrefValue = "";
            $this->posicion->TooltipValue = "";
        } elseif ($this->RowType == ROWTYPE_ADD) {
            // nombre_jugador
            $this->nombre_jugador->setupEditAttributes();
            $this->nombre_jugador->EditCustomAttributes = "";
            $this->nombre_jugador->EditValue = HtmlEncode($this->nombre_jugador->CurrentValue);
            $this->nombre_jugador->PlaceHolder = RemoveHtml($this->nombre_jugador->caption());

            // votos_jugador
            $this->votos_jugador->setupEditAttributes();
            $this->votos_jugador->EditCustomAttributes = "";
            $this->votos_jugador->EditValue = HtmlEncode($this->votos_jugador->CurrentValue);
            $this->votos_jugador->PlaceHolder = RemoveHtml($this->votos_jugador->caption());

            // imagen_jugador
            $this->imagen_jugador->setupEditAttributes();
            $this->imagen_jugador->EditCustomAttributes = "";
            if (!EmptyValue($this->imagen_jugador->Upload->DbValue)) {
                $this->imagen_jugador->ImageWidth = 50;
                $this->imagen_jugador->ImageHeight = 0;
                $this->imagen_jugador->ImageAlt = $this->imagen_jugador->alt();
                $this->imagen_jugador->ImageCssClass = "ew-image";
                $this->imagen_jugador->EditValue = $this->imagen_jugador->Upload->DbValue;
            } else {
                $this->imagen_jugador->EditValue = "";
            }
            if (!EmptyValue($this->imagen_jugador->CurrentValue)) {
                $this->imagen_jugador->Upload->FileName = $this->imagen_jugador->CurrentValue;
            }
            if ($this->isShow()) {
                RenderUploadField($this->imagen_jugador);
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
            $this->usuario_dato->setupEditAttributes();
            $this->usuario_dato->EditCustomAttributes = "";
            $this->usuario_dato->CurrentValue = CurrentUserID();

            // posicion
            $this->posicion->setupEditAttributes();
            $this->posicion->EditCustomAttributes = "";
            if (!$this->posicion->Raw) {
                $this->posicion->CurrentValue = HtmlDecode($this->posicion->CurrentValue);
            }
            $this->posicion->EditValue = HtmlEncode($this->posicion->CurrentValue);
            $this->posicion->PlaceHolder = RemoveHtml($this->posicion->caption());

            // Add refer script

            // nombre_jugador
            $this->nombre_jugador->LinkCustomAttributes = "";
            $this->nombre_jugador->HrefValue = "";

            // votos_jugador
            $this->votos_jugador->LinkCustomAttributes = "";
            $this->votos_jugador->HrefValue = "";

            // imagen_jugador
            $this->imagen_jugador->LinkCustomAttributes = "";
            if (!EmptyValue($this->imagen_jugador->Upload->DbValue)) {
                $this->imagen_jugador->HrefValue = GetFileUploadUrl($this->imagen_jugador, $this->imagen_jugador->htmlDecode($this->imagen_jugador->Upload->DbValue)); // Add prefix/suffix
                $this->imagen_jugador->LinkAttrs["target"] = ""; // Add target
                if ($this->isExport()) {
                    $this->imagen_jugador->HrefValue = FullUrl($this->imagen_jugador->HrefValue, "href");
                }
            } else {
                $this->imagen_jugador->HrefValue = "";
            }
            $this->imagen_jugador->ExportHrefValue = $this->imagen_jugador->UploadPath . $this->imagen_jugador->Upload->DbValue;

            // crea_dato
            $this->crea_dato->LinkCustomAttributes = "";
            $this->crea_dato->HrefValue = "";

            // modifica_dato
            $this->modifica_dato->LinkCustomAttributes = "";
            $this->modifica_dato->HrefValue = "";

            // usuario_dato
            $this->usuario_dato->LinkCustomAttributes = "";
            $this->usuario_dato->HrefValue = "";

            // posicion
            $this->posicion->LinkCustomAttributes = "";
            $this->posicion->HrefValue = "";
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
        if ($this->nombre_jugador->Required) {
            if (!$this->nombre_jugador->IsDetailKey && EmptyValue($this->nombre_jugador->FormValue)) {
                $this->nombre_jugador->addErrorMessage(str_replace("%s", $this->nombre_jugador->caption(), $this->nombre_jugador->RequiredErrorMessage));
            }
        }
        if ($this->votos_jugador->Required) {
            if (!$this->votos_jugador->IsDetailKey && EmptyValue($this->votos_jugador->FormValue)) {
                $this->votos_jugador->addErrorMessage(str_replace("%s", $this->votos_jugador->caption(), $this->votos_jugador->RequiredErrorMessage));
            }
        }
        if ($this->imagen_jugador->Required) {
            if ($this->imagen_jugador->Upload->FileName == "" && !$this->imagen_jugador->Upload->KeepFile) {
                $this->imagen_jugador->addErrorMessage(str_replace("%s", $this->imagen_jugador->caption(), $this->imagen_jugador->RequiredErrorMessage));
            }
        }
        if ($this->crea_dato->Required) {
            if (!$this->crea_dato->IsDetailKey && EmptyValue($this->crea_dato->FormValue)) {
                $this->crea_dato->addErrorMessage(str_replace("%s", $this->crea_dato->caption(), $this->crea_dato->RequiredErrorMessage));
            }
        }
        if (!CheckDate($this->crea_dato->FormValue, $this->crea_dato->formatPattern())) {
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
        if ($this->usuario_dato->Required) {
            if (!$this->usuario_dato->IsDetailKey && EmptyValue($this->usuario_dato->FormValue)) {
                $this->usuario_dato->addErrorMessage(str_replace("%s", $this->usuario_dato->caption(), $this->usuario_dato->RequiredErrorMessage));
            }
        }
        if ($this->posicion->Required) {
            if (!$this->posicion->IsDetailKey && EmptyValue($this->posicion->FormValue)) {
                $this->posicion->addErrorMessage(str_replace("%s", $this->posicion->caption(), $this->posicion->RequiredErrorMessage));
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
        $Breadcrumb->add("list", $this->TableVar, $this->addMasterUrl("jugadorlist"), "", $this->TableVar, true);
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
