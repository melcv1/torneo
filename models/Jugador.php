<?php

namespace PHPMaker2022\project11;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Table class for jugador
 */
class Jugador extends DbTable
{
    protected $SqlFrom = "";
    protected $SqlSelect = null;
    protected $SqlSelectList = null;
    protected $SqlWhere = "";
    protected $SqlGroupBy = "";
    protected $SqlHaving = "";
    protected $SqlOrderBy = "";
    public $UseSessionForListSql = true;

    // Column CSS classes
    public $LeftColumnClass = "col-sm-2 col-form-label ew-label";
    public $RightColumnClass = "col-sm-10";
    public $OffsetColumnClass = "col-sm-10 offset-sm-2";
    public $TableLeftColumnClass = "w-col-2";

    // Export
    public $ExportDoc;

    // Fields
    public $id_jugador;
    public $nombre_jugador;
    public $votos_jugador;
    public $imagen_jugador;
    public $crea_dato;
    public $modifica_dato;
    public $usuario_dato;
    public $posicion;
    public $nombre_equipo;

    // Page ID
    public $PageID = ""; // To be overridden by subclass

    // Constructor
    public function __construct()
    {
        global $Language, $CurrentLanguage, $CurrentLocale;
        parent::__construct();

        // Language object
        $Language = Container("language");
        $this->TableVar = 'jugador';
        $this->TableName = 'jugador';
        $this->TableType = 'TABLE';

        // Update Table
        $this->UpdateTable = "`jugador`";
        $this->Dbid = 'DB';
        $this->ExportAll = true;
        $this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
        $this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
        $this->ExportPageSize = "a4"; // Page size (PDF only)
        $this->ExportExcelPageOrientation = ""; // Page orientation (PhpSpreadsheet only)
        $this->ExportExcelPageSize = ""; // Page size (PhpSpreadsheet only)
        $this->ExportWordVersion = 12; // Word version (PHPWord only)
        $this->ExportWordPageOrientation = "portrait"; // Page orientation (PHPWord only)
        $this->ExportWordPageSize = "A4"; // Page orientation (PHPWord only)
        $this->ExportWordColumnWidth = null; // Cell width (PHPWord only)
        $this->DetailAdd = false; // Allow detail add
        $this->DetailEdit = false; // Allow detail edit
        $this->DetailView = false; // Allow detail view
        $this->ShowMultipleDetails = false; // Show multiple details
        $this->GridAddRowCount = 5;
        $this->AllowAddDeleteRow = true; // Allow add/delete row
        $this->UserIDAllowSecurity = Config("DEFAULT_USER_ID_ALLOW_SECURITY"); // Default User ID allowed permissions
        $this->BasicSearch = new BasicSearch($this->TableVar);

        // id_jugador
        $this->id_jugador = new DbField(
            'jugador',
            'jugador',
            'x_id_jugador',
            'id_jugador',
            '`id_jugador`',
            '`id_jugador`',
            3,
            11,
            -1,
            false,
            '`id_jugador`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'NO'
        );
        $this->id_jugador->InputTextType = "text";
        $this->id_jugador->IsAutoIncrement = true; // Autoincrement field
        $this->id_jugador->IsPrimaryKey = true; // Primary key field
        $this->id_jugador->DefaultErrorMessage = $Language->phrase("IncorrectInteger");
        $this->Fields['id_jugador'] = &$this->id_jugador;

        // nombre_jugador
        $this->nombre_jugador = new DbField(
            'jugador',
            'jugador',
            'x_nombre_jugador',
            'nombre_jugador',
            '`nombre_jugador`',
            '`nombre_jugador`',
            201,
            256,
            -1,
            false,
            '`nombre_jugador`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXTAREA'
        );
        $this->nombre_jugador->InputTextType = "text";
        $this->Fields['nombre_jugador'] = &$this->nombre_jugador;

        // votos_jugador
        $this->votos_jugador = new DbField(
            'jugador',
            'jugador',
            'x_votos_jugador',
            'votos_jugador',
            '`votos_jugador`',
            '`votos_jugador`',
            201,
            1024,
            -1,
            false,
            '`votos_jugador`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXTAREA'
        );
        $this->votos_jugador->InputTextType = "text";
        $this->Fields['votos_jugador'] = &$this->votos_jugador;

        // imagen_jugador
        $this->imagen_jugador = new DbField(
            'jugador',
            'jugador',
            'x_imagen_jugador',
            'imagen_jugador',
            '`imagen_jugador`',
            '`imagen_jugador`',
            201,
            1024,
            -1,
            true,
            '`imagen_jugador`',
            false,
            false,
            false,
            'IMAGE',
            'FILE'
        );
        $this->imagen_jugador->InputTextType = "text";
        $this->Fields['imagen_jugador'] = &$this->imagen_jugador;

        // crea_dato
        $this->crea_dato = new DbField(
            'jugador',
            'jugador',
            'x_crea_dato',
            'crea_dato',
            '`crea_dato`',
            CastDateFieldForLike("`crea_dato`", 0, "DB"),
            135,
            19,
            0,
            false,
            '`crea_dato`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXT'
        );
        $this->crea_dato->InputTextType = "text";
        $this->crea_dato->DefaultErrorMessage = str_replace("%s", $GLOBALS["DATE_FORMAT"], $Language->phrase("IncorrectDate"));
        $this->Fields['crea_dato'] = &$this->crea_dato;

        // modifica_dato
        $this->modifica_dato = new DbField(
            'jugador',
            'jugador',
            'x_modifica_dato',
            'modifica_dato',
            '`modifica_dato`',
            CastDateFieldForLike("`modifica_dato`", 0, "DB"),
            135,
            19,
            0,
            false,
            '`modifica_dato`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXT'
        );
        $this->modifica_dato->InputTextType = "text";
        $this->modifica_dato->DefaultErrorMessage = str_replace("%s", $GLOBALS["DATE_FORMAT"], $Language->phrase("IncorrectDate"));
        $this->Fields['modifica_dato'] = &$this->modifica_dato;

        // usuario_dato
        $this->usuario_dato = new DbField(
            'jugador',
            'jugador',
            'x_usuario_dato',
            'usuario_dato',
            '`usuario_dato`',
            '`usuario_dato`',
            201,
            256,
            -1,
            false,
            '`usuario_dato`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXTAREA'
        );
        $this->usuario_dato->InputTextType = "text";
        $this->Fields['usuario_dato'] = &$this->usuario_dato;

        // posicion
        $this->posicion = new DbField(
            'jugador',
            'jugador',
            'x_posicion',
            'posicion',
            '`posicion`',
            '`posicion`',
            200,
            56,
            -1,
            false,
            '`posicion`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXT'
        );
        $this->posicion->InputTextType = "text";
        $this->Fields['posicion'] = &$this->posicion;

        // nombre_equipo
        $this->nombre_equipo = new DbField(
            'jugador',
            'jugador',
            'x_nombre_equipo',
            'nombre_equipo',
            '`nombre_equipo`',
            '`nombre_equipo`',
            201,
            256,
            -1,
            false,
            '`nombre_equipo`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXTAREA'
        );
        $this->nombre_equipo->InputTextType = "text";
        $this->Fields['nombre_equipo'] = &$this->nombre_equipo;

        // Add Doctrine Cache
        $this->Cache = new ArrayCache();
        $this->CacheProfile = new \Doctrine\DBAL\Cache\QueryCacheProfile(0, $this->TableVar);
    }

    // Field Visibility
    public function getFieldVisibility($fldParm)
    {
        global $Security;
        return $this->$fldParm->Visible; // Returns original value
    }

    // Set left column class (must be predefined col-*-* classes of Bootstrap grid system)
    public function setLeftColumnClass($class)
    {
        if (preg_match('/^col\-(\w+)\-(\d+)$/', $class, $match)) {
            $this->LeftColumnClass = $class . " col-form-label ew-label";
            $this->RightColumnClass = "col-" . $match[1] . "-" . strval(12 - (int)$match[2]);
            $this->OffsetColumnClass = $this->RightColumnClass . " " . str_replace("col-", "offset-", $class);
            $this->TableLeftColumnClass = preg_replace('/^col-\w+-(\d+)$/', "w-col-$1", $class); // Change to w-col-*
        }
    }

    // Single column sort
    public function updateSort(&$fld)
    {
        if ($this->CurrentOrder == $fld->Name) {
            $sortField = $fld->Expression;
            $lastSort = $fld->getSort();
            if (in_array($this->CurrentOrderType, ["ASC", "DESC", "NO"])) {
                $curSort = $this->CurrentOrderType;
            } else {
                $curSort = $lastSort;
            }
            $orderBy = in_array($curSort, ["ASC", "DESC"]) ? $sortField . " " . $curSort : "";
            $this->setSessionOrderBy($orderBy); // Save to Session
        }
    }

    // Update field sort
    public function updateFieldSort()
    {
        $orderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
        $flds = GetSortFields($orderBy);
        foreach ($this->Fields as $field) {
            $fldSort = "";
            foreach ($flds as $fld) {
                if ($fld[0] == $field->Expression || $fld[0] == $field->VirtualExpression) {
                    $fldSort = $fld[1];
                }
            }
            $field->setSort($fldSort);
        }
    }

    // Table level SQL
    public function getSqlFrom() // From
    {
        return ($this->SqlFrom != "") ? $this->SqlFrom : "`jugador`";
    }

    public function sqlFrom() // For backward compatibility
    {
        return $this->getSqlFrom();
    }

    public function setSqlFrom($v)
    {
        $this->SqlFrom = $v;
    }

    public function getSqlSelect() // Select
    {
        return $this->SqlSelect ?? $this->getQueryBuilder()->select("*");
    }

    public function sqlSelect() // For backward compatibility
    {
        return $this->getSqlSelect();
    }

    public function setSqlSelect($v)
    {
        $this->SqlSelect = $v;
    }

    public function getSqlWhere() // Where
    {
        $where = ($this->SqlWhere != "") ? $this->SqlWhere : "";
        $this->DefaultFilter = "";
        AddFilter($where, $this->DefaultFilter);
        return $where;
    }

    public function sqlWhere() // For backward compatibility
    {
        return $this->getSqlWhere();
    }

    public function setSqlWhere($v)
    {
        $this->SqlWhere = $v;
    }

    public function getSqlGroupBy() // Group By
    {
        return ($this->SqlGroupBy != "") ? $this->SqlGroupBy : "";
    }

    public function sqlGroupBy() // For backward compatibility
    {
        return $this->getSqlGroupBy();
    }

    public function setSqlGroupBy($v)
    {
        $this->SqlGroupBy = $v;
    }

    public function getSqlHaving() // Having
    {
        return ($this->SqlHaving != "") ? $this->SqlHaving : "";
    }

    public function sqlHaving() // For backward compatibility
    {
        return $this->getSqlHaving();
    }

    public function setSqlHaving($v)
    {
        $this->SqlHaving = $v;
    }

    public function getSqlOrderBy() // Order By
    {
        return ($this->SqlOrderBy != "") ? $this->SqlOrderBy : "";
    }

    public function sqlOrderBy() // For backward compatibility
    {
        return $this->getSqlOrderBy();
    }

    public function setSqlOrderBy($v)
    {
        $this->SqlOrderBy = $v;
    }

    // Apply User ID filters
    public function applyUserIDFilters($filter, $id = "")
    {
        return $filter;
    }

    // Check if User ID security allows view all
    public function userIDAllow($id = "")
    {
        $allow = $this->UserIDAllowSecurity;
        switch ($id) {
            case "add":
            case "copy":
            case "gridadd":
            case "register":
            case "addopt":
                return (($allow & 1) == 1);
            case "edit":
            case "gridedit":
            case "update":
            case "changepassword":
            case "resetpassword":
                return (($allow & 4) == 4);
            case "delete":
                return (($allow & 2) == 2);
            case "view":
                return (($allow & 32) == 32);
            case "search":
                return (($allow & 64) == 64);
            case "lookup":
                return (($allow & 256) == 256);
            default:
                return (($allow & 8) == 8);
        }
    }

    /**
     * Get record count
     *
     * @param string|QueryBuilder $sql SQL or QueryBuilder
     * @param mixed $c Connection
     * @return int
     */
    public function getRecordCount($sql, $c = null)
    {
        $cnt = -1;
        $rs = null;
        if ($sql instanceof QueryBuilder) { // Query builder
            $sqlwrk = clone $sql;
            $sqlwrk = $sqlwrk->resetQueryPart("orderBy")->getSQL();
        } else {
            $sqlwrk = $sql;
        }
        $pattern = '/^SELECT\s([\s\S]+)\sFROM\s/i';
        // Skip Custom View / SubQuery / SELECT DISTINCT / ORDER BY
        if (
            ($this->TableType == 'TABLE' || $this->TableType == 'VIEW' || $this->TableType == 'LINKTABLE') &&
            preg_match($pattern, $sqlwrk) && !preg_match('/\(\s*(SELECT[^)]+)\)/i', $sqlwrk) &&
            !preg_match('/^\s*select\s+distinct\s+/i', $sqlwrk) && !preg_match('/\s+order\s+by\s+/i', $sqlwrk)
        ) {
            $sqlwrk = "SELECT COUNT(*) FROM " . preg_replace($pattern, "", $sqlwrk);
        } else {
            $sqlwrk = "SELECT COUNT(*) FROM (" . $sqlwrk . ") COUNT_TABLE";
        }
        $conn = $c ?? $this->getConnection();
        $cnt = $conn->fetchOne($sqlwrk);
        if ($cnt !== false) {
            return (int)$cnt;
        }

        // Unable to get count by SELECT COUNT(*), execute the SQL to get record count directly
        return ExecuteRecordCount($sql, $conn);
    }

    // Get SQL
    public function getSql($where, $orderBy = "")
    {
        return $this->buildSelectSql(
            $this->getSqlSelect(),
            $this->getSqlFrom(),
            $this->getSqlWhere(),
            $this->getSqlGroupBy(),
            $this->getSqlHaving(),
            $this->getSqlOrderBy(),
            $where,
            $orderBy
        )->getSQL();
    }

    // Table SQL
    public function getCurrentSql()
    {
        $filter = $this->CurrentFilter;
        $filter = $this->applyUserIDFilters($filter);
        $sort = $this->getSessionOrderBy();
        return $this->getSql($filter, $sort);
    }

    /**
     * Table SQL with List page filter
     *
     * @return QueryBuilder
     */
    public function getListSql()
    {
        $filter = $this->UseSessionForListSql ? $this->getSessionWhere() : "";
        AddFilter($filter, $this->CurrentFilter);
        $filter = $this->applyUserIDFilters($filter);
        $this->recordsetSelecting($filter);
        $select = $this->getSqlSelect();
        $from = $this->getSqlFrom();
        $sort = $this->UseSessionForListSql ? $this->getSessionOrderBy() : "";
        $this->Sort = $sort;
        return $this->buildSelectSql(
            $select,
            $from,
            $this->getSqlWhere(),
            $this->getSqlGroupBy(),
            $this->getSqlHaving(),
            $this->getSqlOrderBy(),
            $filter,
            $sort
        );
    }

    // Get ORDER BY clause
    public function getOrderBy()
    {
        $orderBy = $this->getSqlOrderBy();
        $sort = $this->getSessionOrderBy();
        if ($orderBy != "" && $sort != "") {
            $orderBy .= ", " . $sort;
        } elseif ($sort != "") {
            $orderBy = $sort;
        }
        return $orderBy;
    }

    // Get record count based on filter (for detail record count in master table pages)
    public function loadRecordCount($filter)
    {
        $origFilter = $this->CurrentFilter;
        $this->CurrentFilter = $filter;
        $this->recordsetSelecting($this->CurrentFilter);
        $select = $this->TableType == 'CUSTOMVIEW' ? $this->getSqlSelect() : $this->getQueryBuilder()->select("*");
        $groupBy = $this->TableType == 'CUSTOMVIEW' ? $this->getSqlGroupBy() : "";
        $having = $this->TableType == 'CUSTOMVIEW' ? $this->getSqlHaving() : "";
        $sql = $this->buildSelectSql($select, $this->getSqlFrom(), $this->getSqlWhere(), $groupBy, $having, "", $this->CurrentFilter, "");
        $cnt = $this->getRecordCount($sql);
        $this->CurrentFilter = $origFilter;
        return $cnt;
    }

    // Get record count (for current List page)
    public function listRecordCount()
    {
        $filter = $this->getSessionWhere();
        AddFilter($filter, $this->CurrentFilter);
        $filter = $this->applyUserIDFilters($filter);
        $this->recordsetSelecting($filter);
        $select = $this->TableType == 'CUSTOMVIEW' ? $this->getSqlSelect() : $this->getQueryBuilder()->select("*");
        $groupBy = $this->TableType == 'CUSTOMVIEW' ? $this->getSqlGroupBy() : "";
        $having = $this->TableType == 'CUSTOMVIEW' ? $this->getSqlHaving() : "";
        $sql = $this->buildSelectSql($select, $this->getSqlFrom(), $this->getSqlWhere(), $groupBy, $having, "", $filter, "");
        $cnt = $this->getRecordCount($sql);
        return $cnt;
    }

    /**
     * INSERT statement
     *
     * @param mixed $rs
     * @return QueryBuilder
     */
    public function insertSql(&$rs)
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->insert($this->UpdateTable);
        foreach ($rs as $name => $value) {
            if (!isset($this->Fields[$name]) || $this->Fields[$name]->IsCustom) {
                continue;
            }
            $type = GetParameterType($this->Fields[$name], $value, $this->Dbid);
            $queryBuilder->setValue($this->Fields[$name]->Expression, $queryBuilder->createPositionalParameter($value, $type));
        }
        return $queryBuilder;
    }

    // Insert
    public function insert(&$rs)
    {
        $conn = $this->getConnection();
        $success = $this->insertSql($rs)->execute();
        if ($success) {
            // Get insert id if necessary
            $this->id_jugador->setDbValue($conn->lastInsertId());
            $rs['id_jugador'] = $this->id_jugador->DbValue;
        }
        return $success;
    }

    /**
     * UPDATE statement
     *
     * @param array $rs Data to be updated
     * @param string|array $where WHERE clause
     * @param string $curfilter Filter
     * @return QueryBuilder
     */
    public function updateSql(&$rs, $where = "", $curfilter = true)
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->update($this->UpdateTable);
        foreach ($rs as $name => $value) {
            if (!isset($this->Fields[$name]) || $this->Fields[$name]->IsCustom || $this->Fields[$name]->IsAutoIncrement) {
                continue;
            }
            $type = GetParameterType($this->Fields[$name], $value, $this->Dbid);
            $queryBuilder->set($this->Fields[$name]->Expression, $queryBuilder->createPositionalParameter($value, $type));
        }
        $filter = ($curfilter) ? $this->CurrentFilter : "";
        if (is_array($where)) {
            $where = $this->arrayToFilter($where);
        }
        AddFilter($filter, $where);
        if ($filter != "") {
            $queryBuilder->where($filter);
        }
        return $queryBuilder;
    }

    // Update
    public function update(&$rs, $where = "", $rsold = null, $curfilter = true)
    {
        // If no field is updated, execute may return 0. Treat as success
        $success = $this->updateSql($rs, $where, $curfilter)->execute();
        $success = ($success > 0) ? $success : true;
        return $success;
    }

    /**
     * DELETE statement
     *
     * @param array $rs Key values
     * @param string|array $where WHERE clause
     * @param string $curfilter Filter
     * @return QueryBuilder
     */
    public function deleteSql(&$rs, $where = "", $curfilter = true)
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->delete($this->UpdateTable);
        if (is_array($where)) {
            $where = $this->arrayToFilter($where);
        }
        if ($rs) {
            if (array_key_exists('id_jugador', $rs)) {
                AddFilter($where, QuotedName('id_jugador', $this->Dbid) . '=' . QuotedValue($rs['id_jugador'], $this->id_jugador->DataType, $this->Dbid));
            }
        }
        $filter = ($curfilter) ? $this->CurrentFilter : "";
        AddFilter($filter, $where);
        return $queryBuilder->where($filter != "" ? $filter : "0=1");
    }

    // Delete
    public function delete(&$rs, $where = "", $curfilter = false)
    {
        $success = true;
        if ($success) {
            $success = $this->deleteSql($rs, $where, $curfilter)->execute();
        }
        return $success;
    }

    // Load DbValue from recordset or array
    protected function loadDbValues($row)
    {
        if (!is_array($row)) {
            return;
        }
        $this->id_jugador->DbValue = $row['id_jugador'];
        $this->nombre_jugador->DbValue = $row['nombre_jugador'];
        $this->votos_jugador->DbValue = $row['votos_jugador'];
        $this->imagen_jugador->Upload->DbValue = $row['imagen_jugador'];
        $this->crea_dato->DbValue = $row['crea_dato'];
        $this->modifica_dato->DbValue = $row['modifica_dato'];
        $this->usuario_dato->DbValue = $row['usuario_dato'];
        $this->posicion->DbValue = $row['posicion'];
        $this->nombre_equipo->DbValue = $row['nombre_equipo'];
    }

    // Delete uploaded files
    public function deleteUploadedFiles($row)
    {
        $this->loadDbValues($row);
        $oldFiles = EmptyValue($row['imagen_jugador']) ? [] : [$row['imagen_jugador']];
        foreach ($oldFiles as $oldFile) {
            if (file_exists($this->imagen_jugador->oldPhysicalUploadPath() . $oldFile)) {
                @unlink($this->imagen_jugador->oldPhysicalUploadPath() . $oldFile);
            }
        }
    }

    // Record filter WHERE clause
    protected function sqlKeyFilter()
    {
        return "`id_jugador` = @id_jugador@";
    }

    // Get Key
    public function getKey($current = false)
    {
        $keys = [];
        $val = $current ? $this->id_jugador->CurrentValue : $this->id_jugador->OldValue;
        if (EmptyValue($val)) {
            return "";
        } else {
            $keys[] = $val;
        }
        return implode(Config("COMPOSITE_KEY_SEPARATOR"), $keys);
    }

    // Set Key
    public function setKey($key, $current = false)
    {
        $this->OldKey = strval($key);
        $keys = explode(Config("COMPOSITE_KEY_SEPARATOR"), $this->OldKey);
        if (count($keys) == 1) {
            if ($current) {
                $this->id_jugador->CurrentValue = $keys[0];
            } else {
                $this->id_jugador->OldValue = $keys[0];
            }
        }
    }

    // Get record filter
    public function getRecordFilter($row = null)
    {
        $keyFilter = $this->sqlKeyFilter();
        if (is_array($row)) {
            $val = array_key_exists('id_jugador', $row) ? $row['id_jugador'] : null;
        } else {
            $val = $this->id_jugador->OldValue !== null ? $this->id_jugador->OldValue : $this->id_jugador->CurrentValue;
        }
        if (!is_numeric($val)) {
            return "0=1"; // Invalid key
        }
        if ($val === null) {
            return "0=1"; // Invalid key
        } else {
            $keyFilter = str_replace("@id_jugador@", AdjustSql($val, $this->Dbid), $keyFilter); // Replace key value
        }
        return $keyFilter;
    }

    // Return page URL
    public function getReturnUrl()
    {
        $referUrl = ReferUrl();
        $referPageName = ReferPageName();
        $name = PROJECT_NAME . "_" . $this->TableVar . "_" . Config("TABLE_RETURN_URL");
        // Get referer URL automatically
        if ($referUrl != "" && $referPageName != CurrentPageName() && $referPageName != "login") { // Referer not same page or login page
            $_SESSION[$name] = $referUrl; // Save to Session
        }
        return $_SESSION[$name] ?? GetUrl("jugadorlist");
    }

    // Set return page URL
    public function setReturnUrl($v)
    {
        $_SESSION[PROJECT_NAME . "_" . $this->TableVar . "_" . Config("TABLE_RETURN_URL")] = $v;
    }

    // Get modal caption
    public function getModalCaption($pageName)
    {
        global $Language;
        if ($pageName == "jugadorview") {
            return $Language->phrase("View");
        } elseif ($pageName == "jugadoredit") {
            return $Language->phrase("Edit");
        } elseif ($pageName == "jugadoradd") {
            return $Language->phrase("Add");
        } else {
            return "";
        }
    }

    // API page name
    public function getApiPageName($action)
    {
        switch (strtolower($action)) {
            case Config("API_VIEW_ACTION"):
                return "JugadorView";
            case Config("API_ADD_ACTION"):
                return "JugadorAdd";
            case Config("API_EDIT_ACTION"):
                return "JugadorEdit";
            case Config("API_DELETE_ACTION"):
                return "JugadorDelete";
            case Config("API_LIST_ACTION"):
                return "JugadorList";
            default:
                return "";
        }
    }

    // List URL
    public function getListUrl()
    {
        return "jugadorlist";
    }

    // View URL
    public function getViewUrl($parm = "")
    {
        if ($parm != "") {
            $url = $this->keyUrl("jugadorview", $this->getUrlParm($parm));
        } else {
            $url = $this->keyUrl("jugadorview", $this->getUrlParm(Config("TABLE_SHOW_DETAIL") . "="));
        }
        return $this->addMasterUrl($url);
    }

    // Add URL
    public function getAddUrl($parm = "")
    {
        if ($parm != "") {
            $url = "jugadoradd?" . $this->getUrlParm($parm);
        } else {
            $url = "jugadoradd";
        }
        return $this->addMasterUrl($url);
    }

    // Edit URL
    public function getEditUrl($parm = "")
    {
        $url = $this->keyUrl("jugadoredit", $this->getUrlParm($parm));
        return $this->addMasterUrl($url);
    }

    // Inline edit URL
    public function getInlineEditUrl()
    {
        $url = $this->keyUrl(CurrentPageName(), $this->getUrlParm("action=edit"));
        return $this->addMasterUrl($url);
    }

    // Copy URL
    public function getCopyUrl($parm = "")
    {
        $url = $this->keyUrl("jugadoradd", $this->getUrlParm($parm));
        return $this->addMasterUrl($url);
    }

    // Inline copy URL
    public function getInlineCopyUrl()
    {
        $url = $this->keyUrl(CurrentPageName(), $this->getUrlParm("action=copy"));
        return $this->addMasterUrl($url);
    }

    // Delete URL
    public function getDeleteUrl()
    {
        return $this->keyUrl("jugadordelete", $this->getUrlParm());
    }

    // Add master url
    public function addMasterUrl($url)
    {
        return $url;
    }

    public function keyToJson($htmlEncode = false)
    {
        $json = "";
        $json .= "\"id_jugador\":" . JsonEncode($this->id_jugador->CurrentValue, "number");
        $json = "{" . $json . "}";
        if ($htmlEncode) {
            $json = HtmlEncode($json);
        }
        return $json;
    }

    // Add key value to URL
    public function keyUrl($url, $parm = "")
    {
        if ($this->id_jugador->CurrentValue !== null) {
            $url .= "/" . $this->encodeKeyValue($this->id_jugador->CurrentValue);
        } else {
            return "javascript:ew.alert(ew.language.phrase('InvalidRecord'));";
        }
        if ($parm != "") {
            $url .= "?" . $parm;
        }
        return $url;
    }

    // Render sort
    public function renderFieldHeader($fld)
    {
        global $Security, $Language;
        $sortUrl = "";
        $attrs = "";
        if ($fld->Sortable) {
            $sortUrl = $this->sortUrl($fld);
            $attrs = ' role="button" data-sort-url="' . $sortUrl . '" data-sort-type="1"';
        }
        $html = '<div class="ew-table-header-caption"' . $attrs . '>' . $fld->caption() . '</div>';
        if ($sortUrl) {
            $html .= '<div class="ew-table-header-sort">' . $fld->getSortIcon() . '</div>';
        }
        if ($fld->UseFilter && $Security->canSearch()) {
            $html .= '<div class="ew-filter-dropdown-btn" data-ew-action="filter" data-table="' . $fld->TableVar . '" data-field="' . $fld->FieldVar .
                '"><div class="ew-table-header-filter" role="button" aria-haspopup="true">' . $Language->phrase("Filter") . '</div></div>';
        }
        $html = '<div class="ew-table-header-btn">' . $html . '</div>';
        if ($this->UseCustomTemplate) {
            $scriptId = str_replace("{id}", $fld->TableVar . "_" . $fld->Param, "tpc_{id}");
            $html = '<template id="' . $scriptId . '">' . $html . '</template>';
        }
        return $html;
    }

    // Sort URL
    public function sortUrl($fld)
    {
        if (
            $this->CurrentAction || $this->isExport() ||
            in_array($fld->Type, [128, 204, 205])
        ) { // Unsortable data type
                return "";
        } elseif ($fld->Sortable) {
            $urlParm = $this->getUrlParm("order=" . urlencode($fld->Name) . "&amp;ordertype=" . $fld->getNextSort());
            return $this->addMasterUrl(CurrentPageName() . "?" . $urlParm);
        } else {
            return "";
        }
    }

    // Get record keys from Post/Get/Session
    public function getRecordKeys()
    {
        $arKeys = [];
        $arKey = [];
        if (Param("key_m") !== null) {
            $arKeys = Param("key_m");
            $cnt = count($arKeys);
        } else {
            if (($keyValue = Param("id_jugador") ?? Route("id_jugador")) !== null) {
                $arKeys[] = $keyValue;
            } elseif (IsApi() && (($keyValue = Key(0) ?? Route(2)) !== null)) {
                $arKeys[] = $keyValue;
            } else {
                $arKeys = null; // Do not setup
            }

            //return $arKeys; // Do not return yet, so the values will also be checked by the following code
        }
        // Check keys
        $ar = [];
        if (is_array($arKeys)) {
            foreach ($arKeys as $key) {
                if (!is_numeric($key)) {
                    continue;
                }
                $ar[] = $key;
            }
        }
        return $ar;
    }

    // Get filter from record keys
    public function getFilterFromRecordKeys($setCurrent = true)
    {
        $arKeys = $this->getRecordKeys();
        $keyFilter = "";
        foreach ($arKeys as $key) {
            if ($keyFilter != "") {
                $keyFilter .= " OR ";
            }
            if ($setCurrent) {
                $this->id_jugador->CurrentValue = $key;
            } else {
                $this->id_jugador->OldValue = $key;
            }
            $keyFilter .= "(" . $this->getRecordFilter() . ")";
        }
        return $keyFilter;
    }

    // Load recordset based on filter
    public function loadRs($filter)
    {
        $sql = $this->getSql($filter); // Set up filter (WHERE Clause)
        $conn = $this->getConnection();
        return $conn->executeQuery($sql);
    }

    // Load row values from record
    public function loadListRowValues(&$rs)
    {
        if (is_array($rs)) {
            $row = $rs;
        } elseif ($rs && property_exists($rs, "fields")) { // Recordset
            $row = $rs->fields;
        } else {
            return;
        }
        $this->id_jugador->setDbValue($row['id_jugador']);
        $this->nombre_jugador->setDbValue($row['nombre_jugador']);
        $this->votos_jugador->setDbValue($row['votos_jugador']);
        $this->imagen_jugador->Upload->DbValue = $row['imagen_jugador'];
        $this->crea_dato->setDbValue($row['crea_dato']);
        $this->modifica_dato->setDbValue($row['modifica_dato']);
        $this->usuario_dato->setDbValue($row['usuario_dato']);
        $this->posicion->setDbValue($row['posicion']);
        $this->nombre_equipo->setDbValue($row['nombre_equipo']);
    }

    // Render list row values
    public function renderListRow()
    {
        global $Security, $CurrentLanguage, $Language;

        // Call Row Rendering event
        $this->rowRendering();

        // Common render codes

        // id_jugador

        // nombre_jugador

        // votos_jugador

        // imagen_jugador

        // crea_dato

        // modifica_dato

        // usuario_dato

        // posicion

        // nombre_equipo

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

        // nombre_equipo
        $this->nombre_equipo->ViewValue = $this->nombre_equipo->CurrentValue;
        $this->nombre_equipo->ViewCustomAttributes = "";

        // id_jugador
        $this->id_jugador->LinkCustomAttributes = "";
        $this->id_jugador->HrefValue = "";
        $this->id_jugador->TooltipValue = "";

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

        // nombre_equipo
        $this->nombre_equipo->LinkCustomAttributes = "";
        $this->nombre_equipo->HrefValue = "";
        $this->nombre_equipo->TooltipValue = "";

        // Call Row Rendered event
        $this->rowRendered();

        // Save data for Custom Template
        $this->Rows[] = $this->customTemplateFieldValues();
    }

    // Render edit row values
    public function renderEditRow()
    {
        global $Security, $CurrentLanguage, $Language;

        // Call Row Rendering event
        $this->rowRendering();

        // id_jugador
        $this->id_jugador->setupEditAttributes();
        $this->id_jugador->EditCustomAttributes = "";
        $this->id_jugador->EditValue = $this->id_jugador->CurrentValue;
        $this->id_jugador->ViewCustomAttributes = "";

        // nombre_jugador
        $this->nombre_jugador->setupEditAttributes();
        $this->nombre_jugador->EditCustomAttributes = "";
        $this->nombre_jugador->EditValue = $this->nombre_jugador->CurrentValue;
        $this->nombre_jugador->PlaceHolder = RemoveHtml($this->nombre_jugador->caption());

        // votos_jugador
        $this->votos_jugador->setupEditAttributes();
        $this->votos_jugador->EditCustomAttributes = "";
        $this->votos_jugador->EditValue = $this->votos_jugador->CurrentValue;
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

        // posicion
        $this->posicion->setupEditAttributes();
        $this->posicion->EditCustomAttributes = "";
        if (!$this->posicion->Raw) {
            $this->posicion->CurrentValue = HtmlDecode($this->posicion->CurrentValue);
        }
        $this->posicion->EditValue = $this->posicion->CurrentValue;
        $this->posicion->PlaceHolder = RemoveHtml($this->posicion->caption());

        // nombre_equipo
        $this->nombre_equipo->setupEditAttributes();
        $this->nombre_equipo->EditCustomAttributes = "";
        $this->nombre_equipo->EditValue = $this->nombre_equipo->CurrentValue;
        $this->nombre_equipo->PlaceHolder = RemoveHtml($this->nombre_equipo->caption());

        // Call Row Rendered event
        $this->rowRendered();
    }

    // Aggregate list row values
    public function aggregateListRowValues()
    {
    }

    // Aggregate list row (for rendering)
    public function aggregateListRow()
    {
        // Call Row Rendered event
        $this->rowRendered();
    }

    // Export data in HTML/CSV/Word/Excel/Email/PDF format
    public function exportDocument($doc, $recordset, $startRec = 1, $stopRec = 1, $exportPageType = "")
    {
        if (!$recordset || !$doc) {
            return;
        }
        if (!$doc->ExportCustom) {
            // Write header
            $doc->exportTableHeader();
            if ($doc->Horizontal) { // Horizontal format, write header
                $doc->beginExportRow();
                if ($exportPageType == "view") {
                    $doc->exportCaption($this->id_jugador);
                    $doc->exportCaption($this->nombre_jugador);
                    $doc->exportCaption($this->votos_jugador);
                    $doc->exportCaption($this->imagen_jugador);
                    $doc->exportCaption($this->crea_dato);
                    $doc->exportCaption($this->modifica_dato);
                    $doc->exportCaption($this->usuario_dato);
                    $doc->exportCaption($this->posicion);
                    $doc->exportCaption($this->nombre_equipo);
                } else {
                    $doc->exportCaption($this->id_jugador);
                    $doc->exportCaption($this->crea_dato);
                    $doc->exportCaption($this->modifica_dato);
                    $doc->exportCaption($this->posicion);
                }
                $doc->endExportRow();
            }
        }

        // Move to first record
        $recCnt = $startRec - 1;
        $stopRec = ($stopRec > 0) ? $stopRec : PHP_INT_MAX;
        while (!$recordset->EOF && $recCnt < $stopRec) {
            $row = $recordset->fields;
            $recCnt++;
            if ($recCnt >= $startRec) {
                $rowCnt = $recCnt - $startRec + 1;

                // Page break
                if ($this->ExportPageBreakCount > 0) {
                    if ($rowCnt > 1 && ($rowCnt - 1) % $this->ExportPageBreakCount == 0) {
                        $doc->exportPageBreak();
                    }
                }
                $this->loadListRowValues($row);

                // Render row
                $this->RowType = ROWTYPE_VIEW; // Render view
                $this->resetAttributes();
                $this->renderListRow();
                if (!$doc->ExportCustom) {
                    $doc->beginExportRow($rowCnt); // Allow CSS styles if enabled
                    if ($exportPageType == "view") {
                        $doc->exportField($this->id_jugador);
                        $doc->exportField($this->nombre_jugador);
                        $doc->exportField($this->votos_jugador);
                        $doc->exportField($this->imagen_jugador);
                        $doc->exportField($this->crea_dato);
                        $doc->exportField($this->modifica_dato);
                        $doc->exportField($this->usuario_dato);
                        $doc->exportField($this->posicion);
                        $doc->exportField($this->nombre_equipo);
                    } else {
                        $doc->exportField($this->id_jugador);
                        $doc->exportField($this->crea_dato);
                        $doc->exportField($this->modifica_dato);
                        $doc->exportField($this->posicion);
                    }
                    $doc->endExportRow($rowCnt);
                }
            }

            // Call Row Export server event
            if ($doc->ExportCustom) {
                $this->ExportDoc = &$doc;
                $this->rowExport($row);
            }
            $recordset->moveNext();
        }
        if (!$doc->ExportCustom) {
            $doc->exportTableFooter();
        }
    }

    // Get file data
    public function getFileData($fldparm, $key, $resize, $width = 0, $height = 0, $plugins = [])
    {
        global $DownloadFileName;
        $width = ($width > 0) ? $width : Config("THUMBNAIL_DEFAULT_WIDTH");
        $height = ($height > 0) ? $height : Config("THUMBNAIL_DEFAULT_HEIGHT");

        // Set up field name / file name field / file type field
        $fldName = "";
        $fileNameFld = "";
        $fileTypeFld = "";
        if ($fldparm == 'imagen_jugador') {
            $fldName = "imagen_jugador";
            $fileNameFld = "imagen_jugador";
        } else {
            return false; // Incorrect field
        }

        // Set up key values
        $ar = explode(Config("COMPOSITE_KEY_SEPARATOR"), $key);
        if (count($ar) == 1) {
            $this->id_jugador->CurrentValue = $ar[0];
        } else {
            return false; // Incorrect key
        }

        // Set up filter (WHERE Clause)
        $filter = $this->getRecordFilter();
        $this->CurrentFilter = $filter;
        $sql = $this->getCurrentSql();
        $conn = $this->getConnection();
        $dbtype = GetConnectionType($this->Dbid);
        if ($row = $conn->fetchAssociative($sql)) {
            $val = $row[$fldName];
            if (!EmptyValue($val)) {
                $fld = $this->Fields[$fldName];

                // Binary data
                if ($fld->DataType == DATATYPE_BLOB) {
                    if ($dbtype != "MYSQL") {
                        if (is_resource($val) && get_resource_type($val) == "stream") { // Byte array
                            $val = stream_get_contents($val);
                        }
                    }
                    if ($resize) {
                        ResizeBinary($val, $width, $height, $plugins);
                    }

                    // Write file type
                    if ($fileTypeFld != "" && !EmptyValue($row[$fileTypeFld])) {
                        AddHeader("Content-type", $row[$fileTypeFld]);
                    } else {
                        AddHeader("Content-type", ContentType($val));
                    }

                    // Write file name
                    $downloadPdf = !Config("EMBED_PDF") && Config("DOWNLOAD_PDF_FILE");
                    if ($fileNameFld != "" && !EmptyValue($row[$fileNameFld])) {
                        $fileName = $row[$fileNameFld];
                        $pathinfo = pathinfo($fileName);
                        $ext = strtolower(@$pathinfo["extension"]);
                        $isPdf = SameText($ext, "pdf");
                        if ($downloadPdf || !$isPdf) { // Skip header if not download PDF
                            AddHeader("Content-Disposition", "attachment; filename=\"" . $fileName . "\"");
                        }
                    } else {
                        $ext = ContentExtension($val);
                        $isPdf = SameText($ext, ".pdf");
                        if ($isPdf && $downloadPdf) { // Add header if download PDF
                            AddHeader("Content-Disposition", "attachment" . ($DownloadFileName ? "; filename=\"" . $DownloadFileName . "\"" : ""));
                        }
                    }

                    // Write file data
                    if (
                        StartsString("PK", $val) &&
                        ContainsString($val, "[Content_Types].xml") &&
                        ContainsString($val, "_rels") &&
                        ContainsString($val, "docProps")
                    ) { // Fix Office 2007 documents
                        if (!EndsString("\0\0\0", $val)) { // Not ends with 3 or 4 \0
                            $val .= "\0\0\0\0";
                        }
                    }

                    // Clear any debug message
                    if (ob_get_length()) {
                        ob_end_clean();
                    }

                    // Write binary data
                    Write($val);

                // Upload to folder
                } else {
                    if ($fld->UploadMultiple) {
                        $files = explode(Config("MULTIPLE_UPLOAD_SEPARATOR"), $val);
                    } else {
                        $files = [$val];
                    }
                    $data = [];
                    $ar = [];
                    foreach ($files as $file) {
                        if (!EmptyValue($file)) {
                            if (Config("ENCRYPT_FILE_PATH")) {
                                $ar[$file] = FullUrl(GetApiUrl(Config("API_FILE_ACTION") .
                                    "/" . $this->TableVar . "/" . Encrypt($fld->physicalUploadPath() . $file)));
                            } else {
                                $ar[$file] = FullUrl($fld->hrefPath() . $file);
                            }
                        }
                    }
                    $data[$fld->Param] = $ar;
                    WriteJson($data);
                }
            }
            return true;
        }
        return false;
    }

    // Table level events

    // Recordset Selecting event
    public function recordsetSelecting(&$filter)
    {
        // Enter your code here
    }

    // Recordset Selected event
    public function recordsetSelected(&$rs)
    {
        //Log("Recordset Selected");
    }

    // Recordset Search Validated event
    public function recordsetSearchValidated()
    {
        // Example:
        //$this->MyField1->AdvancedSearch->SearchValue = "your search criteria"; // Search value
    }

    // Recordset Searching event
    public function recordsetSearching(&$filter)
    {
        // Enter your code here
    }

    // Row_Selecting event
    public function rowSelecting(&$filter)
    {
        // Enter your code here
    }

    // Row Selected event
    public function rowSelected(&$rs)
    {
        //Log("Row Selected");
    }

    // Row Inserting event
    public function rowInserting($rsold, &$rsnew)
    {
        // Enter your code here
        // To cancel, set return value to false
        return true;
    }

    // Row Inserted event
    public function rowInserted($rsold, &$rsnew)
    {
        //Log("Row Inserted");
    }

    // Row Updating event
    public function rowUpdating($rsold, &$rsnew)
    {
        // Enter your code here
        // To cancel, set return value to false
        return true;
    }

    // Row Updated event
    public function rowUpdated($rsold, &$rsnew)
    {
        //Log("Row Updated");
    }

    // Row Update Conflict event
    public function rowUpdateConflict($rsold, &$rsnew)
    {
        // Enter your code here
        // To ignore conflict, set return value to false
        return true;
    }

    // Grid Inserting event
    public function gridInserting()
    {
        // Enter your code here
        // To reject grid insert, set return value to false
        return true;
    }

    // Grid Inserted event
    public function gridInserted($rsnew)
    {
        //Log("Grid Inserted");
    }

    // Grid Updating event
    public function gridUpdating($rsold)
    {
        // Enter your code here
        // To reject grid update, set return value to false
        return true;
    }

    // Grid Updated event
    public function gridUpdated($rsold, $rsnew)
    {
        //Log("Grid Updated");
    }

    // Row Deleting event
    public function rowDeleting(&$rs)
    {
        // Enter your code here
        // To cancel, set return value to False
        return true;
    }

    // Row Deleted event
    public function rowDeleted(&$rs)
    {
        //Log("Row Deleted");
    }

    // Email Sending event
    public function emailSending($email, &$args)
    {
        //var_dump($email, $args); exit();
        return true;
    }

    // Lookup Selecting event
    public function lookupSelecting($fld, &$filter)
    {
        //var_dump($fld->Name, $fld->Lookup, $filter); // Uncomment to view the filter
        // Enter your code here
    }

    // Row Rendering event
    public function rowRendering()
    {
        // Enter your code here
    }

    // Row Rendered event
    public function rowRendered()
    {
        // To view properties of field class, use:
        //var_dump($this-><FieldName>);
    }

    // User ID Filtering event
    public function userIdFiltering(&$filter)
    {
        // Enter your code here
    }
}
