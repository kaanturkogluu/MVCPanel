<?php

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/Database.php";


class BaseModel
{

    protected $table;
    protected $primaryKey = 'id';
    protected $tablePrefix;

    protected PDO $db;

    public function __construct(string $dbKey = 'default')
    {
        $this->db = Database::getInstance($dbKey)->getConnection();
    }

    public function get($columns = ['*'], $where = null, $orderBy = null, $limit = null)
    {
        if (is_array($columns)) {
            $columns = implode(', ', $columns);
        }

        $sql = "SELECT $columns FROM {$this->table}";
        $values = [];

        if ($where) {
            if (is_array($where)) {
                $conditions = [];
                foreach ($where as $key => $value) {
                    $conditions[] = "$key = :$key";
                    $values[$key] = $value;
                }
                $sql .= " WHERE " . implode(' AND ', $conditions);
            } else {
                $sql .= " WHERE $where";
            }
        }

        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);
        return $stmt->fetchAll();
    }

    public function findAll($conditions = [], $orderBy = null, $limit = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $sql .= " WHERE ";
            $whereClauses = [];
            foreach ($conditions as $key => $value) {
                $whereClauses[] = "$key = :$key";
                $params[":$key"] = $value;
            }
            $sql .= implode(' AND ', $whereClauses);
        }

        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        if ($limit) {
            $sql .= " LIMIT $limit";
        }


        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findWithIn($conditions = [], $orderBy = null, $limit = null, $page = null, $perPage = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $key => $value) {
                // IN destekle
                if (str_ends_with($key, ' IN') && is_array($value)) {
                    $field = str_replace(' IN', '', $key);
                    $placeholders = [];
                    foreach ($value as $i => $val) {
                        $ph = ":{$field}_{$i}";
                        $placeholders[] = $ph;
                        $params[$ph] = $val;
                    }
                    $whereClauses[] = "$field IN (" . implode(', ', $placeholders) . ")";
                }
                // LIKE destekle
                elseif (str_ends_with($key, ' LIKE')) {
                    $field = str_replace(' LIKE', '', $key);
                    $paramKey = ":{$field}_like";
                    $whereClauses[] = "$field LIKE $paramKey";
                    $params[$paramKey] = $value;
                }
                // Eşitlik kontrolü
                else {
                    $paramKey = ":$key";
                    $whereClauses[] = "$key = $paramKey";
                    $params[$paramKey] = $value;
                }
            }

            $whereClause = implode(' AND ', $whereClauses);
            $sql .= " WHERE " . $whereClause;
            $countSql .= " WHERE " . $whereClause;
        }

        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        // Toplam kayıt sayısını al
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = $stmt->fetch()['total'];

        // Sayfalama uygulanacaksa
        if ($page !== null && $perPage !== null) {
            $offset = ($page - 1) * $perPage;
            $sql .= " LIMIT :limit OFFSET :offset";
            $params[':limit'] = (int) $perPage;
            $params[':offset'] = (int) $offset;
        } elseif ($limit) {
            $sql .= " LIMIT $limit";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();

        // Sayfalama bilgilerini hesapla
        $totalPages = $perPage ? ceil($total / $perPage) : 1;

        return [
            'data' => $data,
            'pagination' => [
                'total' => (int) $total,
                'per_page' => (int) $perPage,
                'current_page' => (int) $page,
                'total_pages' => $totalPages,
                'has_more' => $page < $totalPages
            ]
        ];
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }


    public function create($data)
    {
        $fields = array_keys($data);
        $placeholders = array_map(function ($field) {
            return ":$field";
        }, $fields);

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $fields = array_keys($data);
        $setClauses = array_map(function ($field) {
            return "$field = :$field";
        }, $fields);

        $sql = "UPDATE {$this->table} 
                SET " . implode(', ', $setClauses) . " 
                WHERE {$this->primaryKey} = :id";

        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function count($conditions = [])
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $sql .= " WHERE ";
            $whereClauses = [];
            foreach ($conditions as $key => $value) {
                $whereClauses[] = "$key = :$key";
                $params[$key] = $value;
            }
            $sql .= implode(' AND ', $whereClauses);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return isset($result['count']) ? (int) $result['count'] : 0;
    }

    public function getDataWithPagination($page = 1, $perPage = 10, $filter = null)
    {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if ($filter) {
            $whereClauses = [];
            foreach ($filter as $key => $value) {
                $whereClauses[] = "$key = :$key";
                $params[$key] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        $sql .= " ORDER BY {$this->primaryKey} DESC LIMIT :offset, :perPage";
        $params['offset'] = (int) $offset;
        $params['perPage'] = (int) $perPage;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();

        $data = $stmt->fetchAll();
        $totalPages = ceil($this->count($filter) / $perPage);

        return ['data' => $data, 'total_page' => $totalPages];
    }

    public function getDataWithJoinPagination($page = 1, $perPage = 10, $filter = null, $customFrom = null, $select = '*')
    {
        $offset = ($page - 1) * $perPage;

        $fromClause = $customFrom ? $customFrom : $this->table;

        $sql = "SELECT {$select} FROM  {$fromClause}";

        if ($filter) {
            $whereClauses = [];
            foreach ($filter as $key => $value) {
                if (isset($this->tablePrefix)) {

                    $key = $this->tablePrefix . "." . $key;
                }
                $whereClauses[] = "$key = :" . ltrim($key, $this->tablePrefix . '.');
            }
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }


        $sql .= " ORDER BY {$this->tablePrefix}.{$this->primaryKey} DESC";


        $sql .= " LIMIT :offset, :perPage";





        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);

        if ($filter) {
            foreach ($filter as $key => $value) {
                $stmt->bindParam(":$key", $value, PDO::PARAM_STR);
            }
        }

        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $dataCount = $this->count($filter) ?? 0;

        $totalPages = ceil($dataCount / $perPage);

        return ['data' => $data, 'total_page' => $totalPages, 'data_count' => $dataCount];
    }


    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    public function commit()
    {
        return $this->db->commit();
    }

    public function rollBack()
    {
        return $this->db->rollBack();
    }
}