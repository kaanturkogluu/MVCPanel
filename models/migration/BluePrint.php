<?php
class Blueprint {
    protected $table;
    protected $columns = [];
    protected $primaryKey = [];
    protected $foreignKeys = [];
    protected $timestamps = true;

    public function __construct($table) {
        $this->table = $table;
    }

    public function id() {
        $this->columns[] = [
            'name' => 'id',
            'type' => 'INT',
            'auto_increment' => true,
            'primary_key' => true
        ];
        $this->primaryKey = ['id'];
        return $this;
    }

    public function string($name, $length = 255) {
        $this->columns[] = [
            'name' => $name,
            'type' => "VARCHAR($length)"
        ];
        return $this;
    }
    public function json($name) {
        $this->columns[] = [
            'name' => $name,
            'type' => "JSON"
        ];
        return $this;
    }
    public function text($name) {
        $this->columns[] = [
            'name' => $name,
            'type' => 'TEXT'
        ];
        return $this;
    }

    public function integer($name) {
        $this->columns[] = [
            'name' => $name,
            'type' => 'INT'
        ];
        return $this;
    }

    public function decimal($name, $precision = 10, $scale = 2) {
        $this->columns[] = [
            'name' => $name,
            'type' => "DECIMAL($precision, $scale)"
        ];
        return $this;
    }

    public function enum($name, $values) {
        $values = array_map(fn($value) => "'$value'", $values);
        $this->columns[] = [
            'name' => $name,
            'type' => 'ENUM(' . implode(', ', $values) . ')'
        ];
        return $this;
    }

    public function boolean($name) {
        $this->columns[] = [
            'name' => $name,
            'type' => 'BOOLEAN'
        ];
        return $this;
    }

    public function timestamp($name) {
        $this->columns[] = [
            'name' => $name,
            'type' => 'TIMESTAMP'
        ];
        return $this;
    }

    public function foreignKey($column, $referenceTable, $referenceColumn = 'id') {
        $this->foreignKeys[] = [
            'column' => $column,
            'reference_table' => $referenceTable,
            'reference_column' => $referenceColumn
        ];
        return $this;
    }

    public function nullable() {
        $this->columns[count($this->columns) - 1]['nullable'] = true;
        return $this;
    }

    public function default($value) {
        if (is_string($value)) {
            if (isset($this->columns[count($this->columns) - 1]['type']) && 
                strpos($this->columns[count($this->columns) - 1]['type'], 'TIMESTAMP') !== false && 
                $value === 'CURRENT_TIMESTAMP') {
                $value = 'CURRENT_TIMESTAMP';
            } else {
                $value = "'$value'";
            }
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'NULL';
        }
        $this->columns[count($this->columns) - 1]['default'] = $value;
        return $this;
    }

    public function unique() {
        $this->columns[count($this->columns) - 1]['unique'] = true;
        return $this;
    }

    public function timestamps($value = true) {
        $this->timestamps = $value;
        return $this;
    }

    public function primaryKey($columns) {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        $this->primaryKey = $columns;
        return $this;
    }

    public function create() {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (";
        
        $columnDefinitions = [];
        
        foreach ($this->columns as $column) {
            $definition = "`{$column['name']}` {$column['type']}";
            
            if (isset($column['auto_increment']) && $column['auto_increment']) {
                $definition .= " AUTO_INCREMENT";
            }
            
            if (!isset($column['nullable']) || !$column['nullable']) {
                $definition .= " NOT NULL";
            }
            
            if (isset($column['default'])) {
                $definition .= " DEFAULT {$column['default']}";
            }
            
            if (isset($column['unique'])) {
                $definition .= " UNIQUE";
            }
            
            $columnDefinitions[] = $definition;
        }

        // Timestamps ekle
        if ($this->timestamps) {
            $columnDefinitions[] = "`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
            $columnDefinitions[] = "`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        }

        $sql .= implode(", ", $columnDefinitions);

        // Primary Key ekle
        if (!empty($this->primaryKey)) {
            $sql .= ", PRIMARY KEY (" . implode(", ", $this->primaryKey) . ")";
        }

        // Foreign Key'leri ekle
        foreach ($this->foreignKeys as $fk) {
            $sql .= ", FOREIGN KEY (`{$fk['column']}`) REFERENCES `{$fk['reference_table']}`(`{$fk['reference_column']}`) ON DELETE CASCADE";
        }

        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        return $sql;
    }
}
