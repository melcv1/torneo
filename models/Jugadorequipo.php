<?php

namespace PHPMaker2022\project11;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Table class for jugadorequipo
 */
class Jugadorequipo extends DbTable
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
    public $ID_TORNEO;
    public $id_jugadorequipo;
    public $id_equipo;
    public $id_jugador;
    public $crea_dato;
    public $modifica_dato;
    public $GOLES;

    // Page ID
    public $PageID = ""; // To be overridden by subclass

    // Constructor
    public function __construct()
    {
        global $Language, $CurrentLanguage, $CurrentLocale;
        parent::__construct();

        // Language object
        $Language = Container("language");
        $this->TableVar = 'jugadorequipo';
        $this->TableName = 'jugadorequipo';
        $this->TableType = 'TABLE';

        // Update Table
        $this->UpdateTable = "`jugadorequipo`";
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

        // ID_TORNEO
        $this->ID_TORNEO = new DbField(
            'jugadorequipo',
            'jugadorequipo',
            'x_ID_TORNEO',
            'ID_TORNEO',
            '`ID_TORNEO`',
            '`ID_TORNEO`',
            3,
            11,
            -1,
            false,
            '`ID_TORNEO`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'SELECT'
        );
        $this->ID_TORNEO->InputTextType = "text";
        $this->ID_TORNEO->UsePleaseSelect = true; // Use PleaseSelect by default
        $this->ID_TORNEO->PleaseSelectText = $Language->phrase("PleaseSelect"); // "PleaseSelect" text
        switch ($CurrentLanguage) {
            case "en-US":
                $this->ID_TORNEO->Lookup = new Lookup('ID_TORNEO', 'torneo', false, 'ID_TORNEO', ["NOM_TORNEO_LARGO","","",""], [], ["x_id_equipo"], [], [], [], [], '', '', "`NOM_TORNEO_LARGO`");
                break;
            default:
                $this->ID_TORNEO->Lookup = new Lookup('ID_TORNEO', 'torneo', false, 'ID_TORNEO', ["NOM_TORNEO_LARGO","","",""], [], ["x_id_equipo"], [], [], [], [], '', '', "`NOM_TORNEO_LARGO`");
                break;
        }
        $this->ID_TORNEO->DefaultErrorMessage = $Language->phrase("IncorrectInteger");
        $this->Fields['ID_TORNEO'] = &$this->ID_TORNEO;

        // id_jugadorequipo
        $this->id_jugadorequipo = new DbField(
            'jugadorequipo',
            'jugadorequipo',
            'x_id_jugadorequipo',
            'id_jugadorequipo',
            '`id_jugadorequipo`',
            '`id_jugadorequipo`',
            3,
            11,
            -1,
            false,
            '`id_jugadorequipo`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'NO'
        );
        $this->id_jugadorequipo->InputTextType = "text";
        $this->id_jugadorequipo->IsAutoIncrement = true; // Autoincrement field
        $this->id_jugadorequipo->IsPrimaryKey = true; // Primary key field
        $this->id_jugadorequipo->DefaultErrorMessage = $Language->phrase("IncorrectInteger");
        $this->Fields['id_jugadorequipo'] = &$this->id_jugadorequipo;

        // id_equipo
        $this->id_equipo = new DbField(
            'jugadorequipo',
            'jugadorequipo',
            'x_id_equipo',
            'id_equipo',
            '`id_equipo`',
            '`id_equipo`',
            3,
            11,
            -1,
            false,
            '`id_equipo`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'SELECT'
        );
        $this->id_equipo->InputTextType = "text";
        $this->id_equipo->Nullable = false; // NOT NULL field
        $this->id_equipo->Required = true; // Required field
        $this->id_equipo->UsePleaseSelect = true; // Use PleaseSelect by default
        $this->id_equipo->PleaseSelectText = $Language->phrase("PleaseSelect"); // "PleaseSelect" text
        switch ($CurrentLanguage) {
            case "en-US":
                $this->id_equipo->Lookup = new Lookup('id_equipo', 'equipotorneo', false, 'ID_EQUIPO_TORNEO', ["ID_EQUIPO","","",""], ["x_ID_TORNEO"], [], ["ID_TORNEO"], ["x_ID_TORNEO"], [], [], '', '', "`ID_EQUIPO`");
                break;
            default:
                $this->id_equipo->Lookup = new Lookup('id_equipo', 'equipotorneo', false, 'ID_EQUIPO_TORNEO', ["ID_EQUIPO","","",""], ["x_ID_TORNEO"], [], ["ID_TORNEO"], ["x_ID_TORNEO"], [], [], '', '', "`ID_EQUIPO`");
                break;
        }
        $this->id_equipo->DefaultErrorMessage = $Language->phrase("IncorrectInteger");
        $this->Fields['id_equipo'] = &$this->id_equipo;

        // id_jugador
        $this->id_jugador = new DbField(
            'jugadorequipo',
            'jugadorequipo',
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
            'SELECT'
        );
        $this->id_jugador->InputTextType = "text";
        $this->id_jugador->Nullable = false; // NOT NULL field
        $this->id_jugador->Required = true; // Required field
        $this->id_jugador->UsePleaseSelect = true; // Use PleaseSelect by default
        $this->id_jugador->PleaseSelectText = $Language->phrase("PleaseSelect"); // "PleaseSelect" text
        switch ($CurrentLanguage) {
            case "en-US":
                $this->id_jugador->Lookup = new Lookup('id_jugador', 'jugador', false, 'id_jugador', ["nombre_jugador","","",""], [], [], [], [], [], [], '', '', "`nombre_jugador`");
                break;
            default:
                $this->id_jugador->Lookup = new Lookup('id_jugador', 'jugador', false, 'id_jugador', ["nombre_jugador","","",""], [], [], [], [], [], [], '', '', "`nombre_jugador`");
                break;
        }
        $this->id_jugador->DefaultErrorMessage = $Language->phrase("IncorrectInteger");
        $this->Fields['id_jugador'] = &$this->id_jugador;

        // crea_dato
        $this->crea_dato = new DbField(
            'jugadorequipo',
            'jugadorequipo',
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
        $this->crea_dato->Nullable = false; // NOT NULL field
        $this->crea_dato->Required = true; // Required field
        $this->crea_dato->DefaultErrorMessage = str_replace("%s", $GLOBALS["DATE_FORMAT"], $Language->phrase("IncorrectDate"));
        $this->Fields['crea_dato'] = &$this->crea_dato;

        // modifica_dato
        $this->modifica_dato = new DbField(
            'jugadorequipo',
            'jugadorequipo',
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
        $this->modifica_dato->Nullable = false; // NOT NULL field
        $this->modifica_dato->Required = true; // Required field
        $this->modifica_dato->DefaultErrorMessage = str_replace("%s", $GLOBALS["DATE_FORMAT"], $Language->phrase("IncorrectDate"));
        $this->Fields['modifica_dato'] = &$this->modifica_dato;

        // GOLES
        $this->GOLES = new DbField(
            'jugadorequipo',
            'jugadorequipo',
            'x_GOLES',
            'GOLES',
            '`GOLES`',
            '`GOLES`',
            200,
            64,
            -1,
            false,
            '`GOLES`',
            false,
            false,
            false,
            'FORMATTED TEXT',
            'TEXT'
        );
        $this->GOLES->InputTextType = "text";
        $this->GOLES->Nullable = false; // NOT NULL field
        $this->GOLES->Required = true; // Required field
        $this->Fields['GOLES'] = &$this->GOLES;

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
        return ($this->SqlFrom != "") ? $this->SqlFrom : "`jugadorequipo`";
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
            $this->id_jugadorequipo->setDbValue($conn->lastInsertId());
            $rs['id_jugadorequipo'] = $this->id_jugadorequipo->DbValue;
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
            if (array_key_exists('id_jugadorequipo', $rs)) {
                AddFilter($where, QuotedName('id_jugadorequipo', $this->Dbid) . '=' . QuotedValue($rs['id_jugadorequipo'], $this->id_jugadorequipo->DataType, $this->Dbid));
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
        $this->ID_TORNEO->DbValue = $row['ID_TORNEO'];
        $this->id_jugadorequipo->DbValue = $row['id_jugadorequipo'];
        $this->id_equipo->DbValue = $row['id_equipo'];
        $this->id_jugador->DbValue = $row['id_jugador'];
        $this->crea_dato->DbValue = $row['crea_dato'];
        $this->modifica_dato->DbValue = $row['modifica_dato'];
        $this->GOLES->DbValue = $row['GOLES'];
    }

    // Delete uploaded files
    public function deleteUploadedFiles($row)
    {
        $this->loadDbValues($row);
    }

    // Record filter WHERE clause
    protected function sqlKeyFilter()
    {
        return "`id_jugadorequipo` = @id_jugadorequipo@";
    }

    // Get Key
    public function getKey($current = false)
    {
        $keys = [];
        $val = $current ? $this->id_jugadorequipo->CurrentValue : $this->id_jugadorequipo->OldValue;
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
                $this->id_jugadorequipo->CurrentValue = $keys[0];
            } else {
                $this->id_jugadorequipo->OldValue = $keys[0];
            }
        }
    }

    // Get record filter
    public function getRecordFilter($row = null)
    {
        $keyFilter = $this->sqlKeyFilter();
        if (is_array($row)) {
            $val = array_key_exists('id_jugadorequipo', $row) ? $row['id_jugadorequipo'] : null;
        } else {
            $val = $this->id_jugadorequipo->OldValue !== null ? $this->id_jugadorequipo->OldValue : $this->id_jugadorequipo->CurrentValue;
        }
        if (!is_numeric($val)) {
            return "0=1"; // Invalid key
        }
        if ($val === null) {
            return "0=1"; // Invalid key
        } else {
            $keyFilter = str_replace("@id_jugadorequipo@", AdjustSql($val, $this->Dbid), $keyFilter); // Replace key value
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
        return $_SESSION[$name] ?? GetUrl("jugadorequipolist");
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
        if ($pageName == "jugadorequipoview") {
            return $Language->phrase("View");
        } elseif ($pageName == "jugadorequipoedit") {
            return $Language->phrase("Edit");
        } elseif ($pageName == "jugadorequipoadd") {
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
                return "JugadorequipoView";
            case Config("API_ADD_ACTION"):
                return "JugadorequipoAdd";
            case Config("API_EDIT_ACTION"):
                return "JugadorequipoEdit";
            case Config("API_DELETE_ACTION"):
                return "JugadorequipoDelete";
            case Config("API_LIST_ACTION"):
                return "JugadorequipoList";
            default:
                return "";
        }
    }

    // List URL
    public function getListUrl()
    {
        return "jugadorequipolist";
    }

    // View URL
    public function getViewUrl($parm = "")
    {
        if ($parm != "") {
            $url = $this->keyUrl("jugadorequipoview", $this->getUrlParm($parm));
        } else {
            $url = $this->keyUrl("jugadorequipoview", $this->getUrlParm(Config("TABLE_SHOW_DETAIL") . "="));
        }
        return $this->addMasterUrl($url);
    }

    // Add URL
    public function getAddUrl($parm = "")
    {
        if ($parm != "") {
            $url = "jugadorequipoadd?" . $this->getUrlParm($parm);
        } else {
            $url = "jugadorequipoadd";
        }
        return $this->addMasterUrl($url);
    }

    // Edit URL
    public function getEditUrl($parm = "")
    {
        $url = $this->keyUrl("jugadorequipoedit", $this->getUrlParm($parm));
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
        $url = $this->keyUrl("jugadorequipoadd", $this->getUrlParm($parm));
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
        return $this->keyUrl("jugadorequipodelete", $this->getUrlParm());
    }

    // Add master url
    public function addMasterUrl($url)
    {
        return $url;
    }

    public function keyToJson($htmlEncode = false)
    {
        $json = "";
        $json .= "\"id_jugadorequipo\":" . JsonEncode($this->id_jugadorequipo->CurrentValue, "number");
        $json = "{" . $json . "}";
        if ($htmlEncode) {
            $json = HtmlEncode($json);
        }
        return $json;
    }

    // Add key value to URL
    public function keyUrl($url, $parm = "")
    {
        if ($this->id_jugadorequipo->CurrentValue !== null) {
            $url .= "/" . $this->encodeKeyValue($this->id_jugadorequipo->CurrentValue);
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
            if (($keyValue = Param("id_jugadorequipo") ?? Route("id_jugadorequipo")) !== null) {
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
                $this->id_jugadorequipo->CurrentValue = $key;
            } else {
                $this->id_jugadorequipo->OldValue = $key;
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
        $this->ID_TORNEO->setDbValue($row['ID_TORNEO']);
        $this->id_jugadorequipo->setDbValue($row['id_jugadorequipo']);
        $this->id_equipo->setDbValue($row['id_equipo']);
        $this->id_jugador->setDbValue($row['id_jugador']);
        $this->crea_dato->setDbValue($row['crea_dato']);
        $this->modifica_dato->setDbValue($row['modifica_dato']);
        $this->GOLES->setDbValue($row['GOLES']);
    }

    // Render list row values
    public function renderListRow()
    {
        global $Security, $CurrentLanguage, $Language;

        // Call Row Rendering event
        $this->rowRendering();

        // Common render codes

        // ID_TORNEO

        // id_jugadorequipo

        // id_equipo

        // id_jugador

        // crea_dato

        // modifica_dato

        // GOLES

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

        // id_jugadorequipo
        $this->id_jugadorequipo->ViewValue = $this->id_jugadorequipo->CurrentValue;
        $this->id_jugadorequipo->ViewCustomAttributes = "";

        // id_equipo
        $curVal = strval($this->id_equipo->CurrentValue);
        if ($curVal != "") {
            $this->id_equipo->ViewValue = $this->id_equipo->lookupCacheOption($curVal);
            if ($this->id_equipo->ViewValue === null) { // Lookup from database
                $filterWrk = "`ID_EQUIPO_TORNEO`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                $sqlWrk = $this->id_equipo->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                $conn = Conn();
                $config = $conn->getConfiguration();
                $config->setResultCacheImpl($this->Cache);
                $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                $ari = count($rswrk);
                if ($ari > 0) { // Lookup values found
                    $arwrk = $this->id_equipo->Lookup->renderViewRow($rswrk[0]);
                    $this->id_equipo->ViewValue = $this->id_equipo->displayValue($arwrk);
                } else {
                    $this->id_equipo->ViewValue = FormatNumber($this->id_equipo->CurrentValue, $this->id_equipo->formatPattern());
                }
            }
        } else {
            $this->id_equipo->ViewValue = null;
        }
        $this->id_equipo->ViewCustomAttributes = "";

        // id_jugador
        $curVal = strval($this->id_jugador->CurrentValue);
        if ($curVal != "") {
            $this->id_jugador->ViewValue = $this->id_jugador->lookupCacheOption($curVal);
            if ($this->id_jugador->ViewValue === null) { // Lookup from database
                $filterWrk = "`id_jugador`" . SearchString("=", $curVal, DATATYPE_NUMBER, "");
                $sqlWrk = $this->id_jugador->Lookup->getSql(false, $filterWrk, '', $this, true, true);
                $conn = Conn();
                $config = $conn->getConfiguration();
                $config->setResultCacheImpl($this->Cache);
                $rswrk = $conn->executeCacheQuery($sqlWrk, [], [], $this->CacheProfile)->fetchAll();
                $ari = count($rswrk);
                if ($ari > 0) { // Lookup values found
                    $arwrk = $this->id_jugador->Lookup->renderViewRow($rswrk[0]);
                    $this->id_jugador->ViewValue = $this->id_jugador->displayValue($arwrk);
                } else {
                    $this->id_jugador->ViewValue = FormatNumber($this->id_jugador->CurrentValue, $this->id_jugador->formatPattern());
                }
            }
        } else {
            $this->id_jugador->ViewValue = null;
        }
        $this->id_jugador->ViewCustomAttributes = "";

        // crea_dato
        $this->crea_dato->ViewValue = $this->crea_dato->CurrentValue;
        $this->crea_dato->ViewValue = FormatDateTime($this->crea_dato->ViewValue, $this->crea_dato->formatPattern());
        $this->crea_dato->ViewCustomAttributes = "";

        // modifica_dato
        $this->modifica_dato->ViewValue = $this->modifica_dato->CurrentValue;
        $this->modifica_dato->ViewValue = FormatDateTime($this->modifica_dato->ViewValue, $this->modifica_dato->formatPattern());
        $this->modifica_dato->ViewCustomAttributes = "";

        // GOLES
        $this->GOLES->ViewValue = $this->GOLES->CurrentValue;
        $this->GOLES->ViewCustomAttributes = "";

        // ID_TORNEO
        $this->ID_TORNEO->LinkCustomAttributes = "";
        $this->ID_TORNEO->HrefValue = "";
        $this->ID_TORNEO->TooltipValue = "";

        // id_jugadorequipo
        $this->id_jugadorequipo->LinkCustomAttributes = "";
        $this->id_jugadorequipo->HrefValue = "";
        $this->id_jugadorequipo->TooltipValue = "";

        // id_equipo
        $this->id_equipo->LinkCustomAttributes = "";
        $this->id_equipo->HrefValue = "";
        $this->id_equipo->TooltipValue = "";

        // id_jugador
        $this->id_jugador->LinkCustomAttributes = "";
        $this->id_jugador->HrefValue = "";
        $this->id_jugador->TooltipValue = "";

        // crea_dato
        $this->crea_dato->LinkCustomAttributes = "";
        $this->crea_dato->HrefValue = "";
        $this->crea_dato->TooltipValue = "";

        // modifica_dato
        $this->modifica_dato->LinkCustomAttributes = "";
        $this->modifica_dato->HrefValue = "";
        $this->modifica_dato->TooltipValue = "";

        // GOLES
        $this->GOLES->LinkCustomAttributes = "";
        $this->GOLES->HrefValue = "";
        $this->GOLES->TooltipValue = "";

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

        // ID_TORNEO
        $this->ID_TORNEO->setupEditAttributes();
        $this->ID_TORNEO->EditCustomAttributes = "";
        $this->ID_TORNEO->PlaceHolder = RemoveHtml($this->ID_TORNEO->caption());

        // id_jugadorequipo
        $this->id_jugadorequipo->setupEditAttributes();
        $this->id_jugadorequipo->EditCustomAttributes = "";
        $this->id_jugadorequipo->EditValue = $this->id_jugadorequipo->CurrentValue;
        $this->id_jugadorequipo->ViewCustomAttributes = "";

        // id_equipo
        $this->id_equipo->setupEditAttributes();
        $this->id_equipo->EditCustomAttributes = "";
        $this->id_equipo->PlaceHolder = RemoveHtml($this->id_equipo->caption());

        // id_jugador
        $this->id_jugador->setupEditAttributes();
        $this->id_jugador->EditCustomAttributes = "";
        $this->id_jugador->PlaceHolder = RemoveHtml($this->id_jugador->caption());

        // crea_dato
        $this->crea_dato->setupEditAttributes();
        $this->crea_dato->EditCustomAttributes = "";
        $this->crea_dato->EditValue = FormatDateTime($this->crea_dato->CurrentValue, $this->crea_dato->formatPattern());
        $this->crea_dato->PlaceHolder = RemoveHtml($this->crea_dato->caption());

        // modifica_dato
        $this->modifica_dato->setupEditAttributes();
        $this->modifica_dato->EditCustomAttributes = "";
        $this->modifica_dato->EditValue = FormatDateTime($this->modifica_dato->CurrentValue, $this->modifica_dato->formatPattern());
        $this->modifica_dato->PlaceHolder = RemoveHtml($this->modifica_dato->caption());

        // GOLES
        $this->GOLES->setupEditAttributes();
        $this->GOLES->EditCustomAttributes = "";
        if (!$this->GOLES->Raw) {
            $this->GOLES->CurrentValue = HtmlDecode($this->GOLES->CurrentValue);
        }
        $this->GOLES->EditValue = $this->GOLES->CurrentValue;
        $this->GOLES->PlaceHolder = RemoveHtml($this->GOLES->caption());

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
                    $doc->exportCaption($this->ID_TORNEO);
                    $doc->exportCaption($this->id_jugadorequipo);
                    $doc->exportCaption($this->id_equipo);
                    $doc->exportCaption($this->id_jugador);
                    $doc->exportCaption($this->crea_dato);
                    $doc->exportCaption($this->modifica_dato);
                    $doc->exportCaption($this->GOLES);
                } else {
                    $doc->exportCaption($this->ID_TORNEO);
                    $doc->exportCaption($this->id_jugadorequipo);
                    $doc->exportCaption($this->id_equipo);
                    $doc->exportCaption($this->id_jugador);
                    $doc->exportCaption($this->crea_dato);
                    $doc->exportCaption($this->modifica_dato);
                    $doc->exportCaption($this->GOLES);
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
                        $doc->exportField($this->ID_TORNEO);
                        $doc->exportField($this->id_jugadorequipo);
                        $doc->exportField($this->id_equipo);
                        $doc->exportField($this->id_jugador);
                        $doc->exportField($this->crea_dato);
                        $doc->exportField($this->modifica_dato);
                        $doc->exportField($this->GOLES);
                    } else {
                        $doc->exportField($this->ID_TORNEO);
                        $doc->exportField($this->id_jugadorequipo);
                        $doc->exportField($this->id_equipo);
                        $doc->exportField($this->id_jugador);
                        $doc->exportField($this->crea_dato);
                        $doc->exportField($this->modifica_dato);
                        $doc->exportField($this->GOLES);
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

        // No binary fields
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
